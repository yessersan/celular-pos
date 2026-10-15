<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class ProductManager extends Component
{
    use WithFileUploads;

    /** @var Collection<int, Product> */
    public Collection $products;

    public string $name = '';
    public TemporaryUploadedFile|null $image = null;        // archivo nuevo
    public ?string $currentImage = null; // ruta actual (edición)
    public float $purchase_price = 0;
    public float $sale_price = 0;
    public int $stock = 0;
    public ?int $editId = null;
    public bool $showForm = false;
    public string $search = '';
    public string $stockFilter = '';
    public string $minPrice = '';
    public string $maxPrice = '';

    protected array $rules = [
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|max:2048',
        'purchase_price' => 'required|numeric|min:0',
        'sale_price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ];

    public function mount(): void
    {
        $this->products = collect();
        $this->loadProducts();
    }

    public function updatedSearch(): void { $this->loadProducts(); }
    public function updatedStockFilter(): void { $this->loadProducts(); }
    public function updatedMinPrice(): void { $this->loadProducts(); }
    public function updatedMaxPrice(): void { $this->loadProducts(); }

    public function loadProducts(): void
    {
        $this->products = Product::query()
            ->when(trim($this->search) !== '', fn ($query) => $query->where('name', 'like', '%'.trim($this->search).'%'))
            ->when($this->stockFilter === 'available', fn ($query) => $query->where('stock', '>', 0))
            ->when($this->stockFilter === 'low', fn ($query) => $query->whereBetween('stock', [1, 3]))
            ->when($this->stockFilter === 'out', fn ($query) => $query->where('stock', 0))
            ->when($this->minPrice !== '' && is_numeric($this->minPrice), fn ($query) => $query->where('sale_price', '>=', (float) $this->minPrice))
            ->when($this->maxPrice !== '' && is_numeric($this->maxPrice), fn ($query) => $query->where('sale_price', '<=', (float) $this->maxPrice))
            ->orderBy('name')->get();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'stockFilter', 'minPrice', 'maxPrice']);
        $this->loadProducts();
    }

    public function sell(int $id): mixed
    {
        $product = Product::findOrFail($id);
        if ($product->stock < 1) {
            session()->flash('error', 'Este producto no tiene stock disponible.');
            return null;
        }

        return redirect()->route('sales', ['product' => $product->id]);
    }

    public function save(): void
    {
        $this->validate();

        $imagePath = $this->currentImage;

        if ($this->image) {
            // Eliminar imagen anterior si existe
            if ($this->currentImage && Storage::disk('public')->exists($this->currentImage)) {
                Storage::disk('public')->delete($this->currentImage);
            }
            $imagePath = $this->image->store('products', 'public');
            if (!$imagePath) {
                $this->addError('image', 'No se pudo guardar la imagen. Verifica los permisos de storage.');
                return;
            }
        }

        $data = [
            'name' => $this->name,
            'image' => $imagePath,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
        ];

        if ($this->editId) {
            Product::findOrFail($this->editId)->update($data);
        } else {
            Product::create($data);
        }

        $this->resetForm();
        $this->loadProducts();
        session()->flash('message', 'Producto guardado correctamente.');
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editId = $product->id;
        $this->name = $product->name;
        $this->currentImage = $product->image;
        $this->purchase_price = (float) $product->purchase_price;
        $this->sale_price = (float) $product->sale_price;
        $this->stock = (int) $product->stock;
        $this->image = null;
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        $this->loadProducts();
        session()->flash('message', 'Producto eliminado.');
    }

    public function removeImage(): void
    {
        if ($this->currentImage && Storage::disk('public')->exists($this->currentImage)) {
            Storage::disk('public')->delete($this->currentImage);
        }
        if ($this->editId) {
            Product::find($this->editId)?->update(['image' => null]);
        }
        $this->currentImage = null;
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'purchase_price', 'sale_price', 'stock', 'editId', 'image', 'currentImage']);
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.product-manager');
    }
}