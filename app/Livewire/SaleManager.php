<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Service;
use App\Services\ApisPeruService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use RuntimeException;

class SaleManager extends Component
{
    public string $customer_name = '';
    public string $customer_document = '';
    public string $customer_address = '';
    public string $document_type = 'boleta';
    public string $discount_percent = '0';
    public string $productSearch = '';
    public string $serviceSearch = '';

    /** @var Collection<int, Product> */
    public Collection $products;
    /** @var Collection<int, Service> */
    public Collection $services;
    /** @var array<int, array{type: string, id: int, name: string, unit_price: float, quantity: int, subtotal: float}> */
    public array $items = [];

    public ?int $selectedProduct = null;
    public ?int $selectedService = null;
    public int $quantity = 1;
    public ?int $saleCompleted = null;

    public function mount(): void
    {
        $this->products = collect();
        $this->services = collect();
        $this->loadCatalogs();

        $productId = request()->query('product');
        if ($productId && ctype_digit((string) $productId)) {
            $product = Product::find((int) $productId);
            if ($product && $product->stock > 0) {
                $this->selectedProduct = $product->id;
                $this->quantity = 1;
                $this->addProduct();
                session()->flash('message', 'Producto agregado a la venta. Revisa la cantidad y confirma cuando estés listo.');
            }
        }
    }

    public function updatedProductSearch(): void { $this->loadCatalogs(); }
    public function updatedServiceSearch(): void { $this->loadCatalogs(); }

    public function loadCatalogs(): void
    {
        $this->products = Product::query()->where('stock', '>', 0)
            ->when(trim($this->productSearch) !== '', fn ($q) => $q->where('name', 'like', '%'.trim($this->productSearch).'%'))
            ->orderBy('name')->limit(100)->get();
        $this->services = Service::query()
            ->when(trim($this->serviceSearch) !== '', fn ($q) => $q->where('name', 'like', '%'.trim($this->serviceSearch).'%'))
            ->orderBy('name')->limit(100)->get();
    }

    public function lookupDni(ApisPeruService $apisPeru): void
    {
        $this->validate(['customer_document' => 'required|regex:/^\d{8}$/']);
        try {
            $person = $apisPeru->lookupDni($this->customer_document);
            $this->customer_name = $person['name'];
            $this->customer_address = $person['address'];
            session()->flash('message', 'Datos del cliente consultados correctamente.');
        } catch (\Throwable $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function addProduct(): void
    {
        $this->validate([
            'selectedProduct' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10000',
        ]);
        $product = Product::findOrFail($this->selectedProduct);
        if ($product->stock < $this->quantity) {
            session()->flash('error', 'Stock insuficiente. Disponible: '.$product->stock);
            return;
        }
        $this->saleCompleted = null;
        $this->items[] = [
            'type' => 'product', 'id' => $product->id, 'name' => $product->name,
            'unit_price' => (float) $product->sale_price, 'quantity' => $this->quantity,
            'subtotal' => round((float) $product->sale_price * $this->quantity, 2),
        ];
        $this->reset(['selectedProduct', 'quantity']);
        $this->quantity = 1;
    }

    public function addService(): void
    {
        $this->validate(['selectedService' => 'required|exists:services,id']);
        $service = Service::findOrFail($this->selectedService);
        $this->saleCompleted = null;
        $this->items[] = [
            'type' => 'service', 'id' => $service->id, 'name' => $service->name,
            'unit_price' => (float) $service->price, 'quantity' => 1,
            'subtotal' => (float) $service->price,
        ];
        $this->reset(['selectedService']);
    }

    public function removeItem(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function getSubtotalProperty(): float
    {
        return round((float) collect($this->items)->sum('subtotal'), 2);
    }

    public function getDiscountAmountProperty(): float
    {
        $percent = is_numeric($this->discount_percent) ? (float) $this->discount_percent : 0;
        return round($this->getSubtotalProperty() * min(100, max(0, $percent)) / 100, 2);
    }

    public function getTotalProperty(): float
    {
        return round(max(0, $this->getSubtotalProperty() - $this->getDiscountAmountProperty()), 2);
    }

    public function saveSale(): void
    {
        if (empty($this->items)) {
            session()->flash('error', 'Debe agregar al menos un producto o servicio.');
            return;
        }

        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_document' => ['nullable', 'regex:/^\d{8}$|^\d{11}$/'],
            'customer_address' => 'nullable|string|max:255',
            'document_type' => 'required|in:boleta,factura',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ]);

        if ($this->document_type === 'factura' &&
            (!preg_match('/^\d{11}$/', $this->customer_document) || trim($this->customer_address) === '')) {
            $this->addError('customer_document', 'Para emitir una factura se requiere RUC de 11 dígitos y dirección fiscal.');
            return;
        }

        try {
            DB::transaction(function () {
                $validatedItems = [];
                $subtotal = 0.0;

                foreach ($this->items as $item) {
                    if (!isset($item['type'], $item['id'], $item['quantity']) ||
                        !in_array($item['type'], ['product', 'service'], true) ||
                        !is_numeric($item['quantity']) || (int) $item['quantity'] < 1) {
                        throw new RuntimeException('El carrito contiene artículos no válidos. Vuelve a agregarlos.');
                    }

                    if ($item['type'] === 'product') {
                        $record = Product::whereKey((int) $item['id'])->lockForUpdate()->firstOrFail();
                        if ($record->stock < (int) $item['quantity']) {
                            throw new RuntimeException('Stock insuficiente para '.$record->name.'.');
                        }
                        $price = (float) $record->sale_price;
                    } else {
                        $record = Service::findOrFail((int) $item['id']);
                        $price = (float) $record->price;
                    }

                    $qty = (int) $item['quantity'];
                    $line = round($price * $qty, 2);
                    $subtotal += $line;
                    $validatedItems[] = ['type' => $item['type'], 'record' => $record, 'price' => $price, 'quantity' => $qty, 'subtotal' => $line];
                }

                $subtotal = round($subtotal, 2);
                $percent = (float) $this->discount_percent;
                $discount = round($subtotal * $percent / 100, 2);
                $sale = Sale::create([
                    'receipt_number' => 'TEMP-'.uniqid(),
                    'document_type' => $this->document_type,
                    'customer_name' => $this->customer_name,
                    'customer_document' => $this->customer_document ?: null,
                    'customer_address' => $this->customer_address ?: null,
                    'subtotal' => $subtotal,
                    'discount_percent' => $percent,
                    'discount_amount' => $discount,
                    'total' => round($subtotal - $discount, 2),
                ]);
                $sale->update(['receipt_number' => ($this->document_type === 'factura' ? 'F001-' : 'B001-').str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT)]);

                foreach ($validatedItems as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'saleable_id' => $item['record']->id,
                        'saleable_type' => $item['type'] === 'product' ? Product::class : Service::class,
                        'name' => $item['record']->name,
                        'unit_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);
                    if ($item['type'] === 'product') {
                        $item['record']->decrement('stock', $item['quantity']);
                    }
                }
                $this->saleCompleted = $sale->id;
            });
        } catch (\Throwable $exception) {
            session()->flash('error', $exception->getMessage());
            return;
        }

        $this->reset(['items', 'customer_name', 'customer_document', 'customer_address', 'discount_percent']);
        $this->document_type = 'boleta';
        $this->discount_percent = '0';
        $this->loadCatalogs();
        session()->flash('message', 'Venta registrada correctamente. Comprobante generado.');
    }

    public function printReceipt(int $saleId)
    {
        return redirect()->route('sale.print', $saleId);
    }

    public function downloadPdf(int $saleId)
    {
        return redirect()->route('sale.pdf', $saleId);
    }

    public function render()
    {
        return view('livewire.sale-manager');
    }
}
