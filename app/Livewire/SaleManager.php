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
use Throwable;

class SaleManager extends Component
{
    public string $customer_name = '';
    public string $customer_document_type = 'DNI';
    public string $customer_document_number = '';
    public string $customer_address = '';
    public string $customer_email = '';
    public string $voucher_type = 'BOLETA';
    public float $discount_amount = 0;
    public Collection $products;
    public Collection $services;
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
    }

    public function loadCatalogs(): void
    {
        $this->products = Product::where('stock', '>', 0)->orderBy('name')->get();
        $this->services = Service::orderBy('name')->get();
    }

    public function updatedCustomerDocumentType(): void
    {
        $this->customer_document_number = '';
        $this->customer_name = '';
        $this->customer_address = '';
        $this->resetValidation();
    }

    public function lookupCustomer(ApisPeruService $apisPeru): void
    {
        $length = $this->customer_document_type === 'RUC' ? 11 : 8;
        $this->validate(['customer_document_number' => ['required', 'regex:/^[0-9]{' . $length . '}$/']]);

        try {
            $customer = $this->customer_document_type === 'RUC'
                ? $apisPeru->lookupRuc($this->customer_document_number)
                : $apisPeru->lookupDni($this->customer_document_number);
            $this->customer_name = $customer['name'];
            $this->customer_address = $customer['address'];
            session()->flash('message', 'Datos del cliente consultados correctamente.');
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('error', $exception->getMessage());
        }
    }

    public function addProduct(): void
    {
        $this->validate([
            'selectedProduct' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $product = Product::findOrFail($this->selectedProduct);
        $added = collect($this->items)->where('type', 'product')->where('id', $product->id)->sum('quantity');
        if ($product->stock < $added + $this->quantity) {
            session()->flash('error', 'Stock insuficiente. Disponible: ' . max(0, $product->stock - $added));
            return;
        }
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
        $this->items[] = [
            'type' => 'service', 'id' => $service->id, 'name' => $service->name,
            'unit_price' => (float) $service->price, 'quantity' => 1, 'subtotal' => (float) $service->price,
        ];
        $this->reset(['selectedService']);
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getSubtotalProperty(): float
    {
        return round((float) collect($this->items)->sum('subtotal'), 2);
    }

    public function getTotalProperty(): float
    {
        return max(0, round($this->getSubtotalProperty() - min(max(0, $this->discount_amount), $this->getSubtotalProperty()), 2));
    }

    public function saveSale(): void
    {
        if (empty($this->items)) {
            session()->flash('error', 'Debe agregar al menos un producto o servicio.');
            return;
        }

        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_document_type' => 'required|in:DNI,RUC',
            'customer_document_number' => ['required', $this->customer_document_type === 'RUC' ? 'regex:/^[0-9]{11}$/' : 'regex:/^[0-9]{8}$/'],
            'customer_address' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'voucher_type' => 'required|in:BOLETA,FACTURA',
            'discount_amount' => 'required|numeric|min:0',
        ]);

        if ($this->voucher_type === 'FACTURA' && $this->customer_document_type !== 'RUC') {
            $this->addError('customer_document_type', 'Para emitir una factura se requiere RUC de 11 dígitos.');
            return;
        }

        try {
            DB::transaction(function () {
                $verifiedItems = [];
                foreach ($this->items as $item) {
                    if (!isset($item['type'], $item['id'], $item['quantity']) ||
                        !in_array($item['type'], ['product', 'service'], true) ||
                        !is_numeric($item['quantity']) || (int) $item['quantity'] < 1) {
                        throw new \RuntimeException('El carrito contiene un artículo no válido.');
                    }
                    if ($item['type'] === 'product') {
                        $model = Product::query()->lockForUpdate()->findOrFail((int) $item['id']);
                        $quantity = (int) $item['quantity'];
                        if ($model->stock < $quantity) {
                            throw new \RuntimeException('Stock insuficiente para ' . $model->name . '.');
                        }
                        $unitPrice = (float) $model->sale_price;
                    } else {
                        $model = Service::findOrFail((int) $item['id']);
                        $quantity = 1;
                        $unitPrice = (float) $model->price;
                    }
                    $verifiedItems[] = [
                        'type' => $item['type'], 'model' => $model, 'name' => $model->name,
                        'unit_price' => $unitPrice, 'quantity' => $quantity,
                        'subtotal' => round($unitPrice * $quantity, 2),
                    ];
                }

                $subtotal = round((float) collect($verifiedItems)->sum('subtotal'), 2);
                $discount = round((float) $this->discount_amount, 2);
                if ($discount > $subtotal) {
                    throw new \RuntimeException('El descuento no puede superar el subtotal.');
                }

                $prefix = $this->voucher_type === 'FACTURA' ? 'F001-' : 'B001-';
                $sale = Sale::create([
                    'receipt_number' => $prefix . strtoupper(bin2hex(random_bytes(3))),
                    'customer_name' => $this->customer_name,
                    'customer_document_type' => $this->customer_document_type,
                    'customer_document_number' => $this->customer_document_number,
                    'customer_address' => $this->customer_address ?: null,
                    'customer_email' => $this->customer_email ?: null,
                    'voucher_type' => $this->voucher_type,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'total' => round($subtotal - $discount, 2),
                ]);

                foreach ($verifiedItems as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id, 'saleable_id' => $item['model']->id,
                        'saleable_type' => $item['model']::class, 'name' => $item['name'],
                        'unit_price' => $item['unit_price'], 'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);
                    if ($item['type'] === 'product') {
                        $item['model']->decrement('stock', $item['quantity']);
                    }
                }
                $this->saleCompleted = $sale->id;
            });
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('error', $exception->getMessage());
            return;
        }

        $this->reset(['items', 'customer_name', 'customer_document_number', 'customer_address', 'customer_email', 'discount_amount']);
        $this->customer_document_type = 'DNI';
        $this->voucher_type = 'BOLETA';
        $this->discount_amount = 0;
        $this->loadCatalogs();
        session()->flash('message', 'Venta registrada correctamente. Comprobante interno generado.');
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
