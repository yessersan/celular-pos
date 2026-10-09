<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleManager extends Component
{
    public string $customer_name = '';

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
    }

    public function loadCatalogs(): void
    {
        $this->products = Product::where('stock', '>', 0)->orderBy('name')->get();
        $this->services = Service::orderBy('name')->get();
    }

    public function addProduct(): void
    {
        $this->validate(['selectedProduct' => 'required|exists:products,id']);

        $product = Product::findOrFail($this->selectedProduct);

        if ($product->stock < $this->quantity) {
            session()->flash('error', 'Stock insuficiente. Disponible: ' . $product->stock);
            return;
        }

        $this->items[] = [
            'type' => 'product',
            'id' => $product->id,
            'name' => $product->name,
            'unit_price' => (float) $product->sale_price,
            'quantity' => $this->quantity,
            'subtotal' => (float) $product->sale_price * $this->quantity,
        ];

        $this->reset(['selectedProduct', 'quantity']);
        $this->quantity = 1;
    }

    public function addService(): void
    {
        $this->validate(['selectedService' => 'required|exists:services,id']);

        $service = Service::findOrFail($this->selectedService);

        $this->items[] = [
            'type' => 'service',
            'id' => $service->id,
            'name' => $service->name,
            'unit_price' => (float) $service->price,
            'quantity' => 1,
            'subtotal' => (float) $service->price,
        ];

        $this->reset(['selectedService']);
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotalProperty(): float
    {
        return (float) collect($this->items)->sum('subtotal');
    }

    public function saveSale(): void
    {
        if (empty($this->items)) {
            session()->flash('error', 'Debe agregar al menos un producto o servicio.');
            return;
        }

        $this->validate(['customer_name' => 'nullable|string|max:255']);

        DB::transaction(function () {
            $lastSale = Sale::latest()->first();
            $nextNumber = $lastSale ? $lastSale->id + 1 : 1;
            $receiptNumber = 'B001-' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'receipt_number' => $receiptNumber,
                'customer_name' => $this->customer_name ?: 'Cliente general',
                'total' => $this->getTotalProperty(),
            ]);

            foreach ($this->items as $item) {
                $saleableType = $item['type'] === 'product' ? Product::class : Service::class;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'saleable_id' => $item['id'],
                    'saleable_type' => $saleableType,
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if ($product) {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }

            $this->saleCompleted = $sale->id;
        });

        $this->reset(['items', 'customer_name']);
        $this->loadCatalogs();
        session()->flash('message', 'Venta registrada correctamente. Boleta generada.');
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