<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">📦 Inventario de Productos</h2>
        <button wire:click="$set('showForm', true)"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg shadow transition">
            + Nuevo Producto
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if ($showForm)
    <div class="bg-white p-6 rounded-lg shadow-lg border-t-4 border-blue-700 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">
            {{ $editId ? 'Editar Producto' : 'Nuevo Producto' }}
        </h3>
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" wire:model="name"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen del Producto</label>
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($currentImage)
                            <img src="{{ asset('storage/'.$currentImage) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-400 text-xs text-center">Sin imagen</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" wire:model="image" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if ($currentImage)
                            <button type="button" wire:click="removeImage"
                                    class="text-red-600 text-sm mt-1 hover:underline">
                                Eliminar imagen actual
                            </button>
                        @endif
                        @error('image') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Compra</label>
                <input type="number" step="0.01" wire:model="purchase_price"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('purchase_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Venta</label>
                <input type="number" step="0.01" wire:model="sale_price"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('sale_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <input type="number" wire:model="stock"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 flex gap-2 justify-end">
                <button type="button" wire:click="resetForm"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                    Cancelar
                </button>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                    Guardar
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Imagen</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Nombre</th>
                    <th class="py-3 px-4 text-right text-sm font-semibold">P. Compra</th>
                    <th class="py-3 px-4 text-right text-sm font-semibold">P. Venta</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Stock</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">
                        @if ($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 class="w-12 h-12 rounded object-cover border">
                        @else
                            <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-xs">—</div>
                        @endif
                    </td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $product->name }}</td>
                    <td class="py-3 px-4 text-right text-gray-600">S/ {{ number_format($product->purchase_price, 2) }}</td>
                    <td class="py-3 px-4 text-right font-semibold text-gray-800">S/ {{ number_format($product->sale_price, 2) }}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-bold
                            {{ $product->stock <= 3 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center space-x-3">
                        <button wire:click="edit({{ $product->id }})"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</button>
                        <button wire:click="delete({{ $product->id }})"
                                wire:confirm="¿Eliminar este producto?"
                                class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">No hay productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>