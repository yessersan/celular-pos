<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Catálogo de Servicios</h2>
        <button wire:click="$set('showForm', true)"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Nuevo Servicio
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if ($showForm)
    <div class="bg-gray-50 p-4 rounded mb-6 border">
        <form wire:submit="save" class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Nombre del Servicio</label>
                <input type="text" wire:model="name"
                       class="w-full border rounded px-3 py-2">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Precio</label>
                <input type="number" step="0.01" wire:model="price"
                       class="w-full border rounded px-3 py-2">
                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2 flex gap-2">
                <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Guardar
                </button>
                <button type="button" wire:click="resetForm"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
    @endif

    <table class="min-w-full bg-white border">
        <thead>
            <tr class="bg-gray-100">
                <th class="py-2 px-4 border">Servicio</th>
                <th class="py-2 px-4 border">Precio</th>
                <th class="py-2 px-4 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
            <tr>
                <td class="py-2 px-4 border">{{ $service->name }}</td>
                <td class="py-2 px-4 border text-right">S/ {{ number_format($service->price, 2) }}</td>
                <td class="py-2 px-4 border text-center space-x-2">
                    <button wire:click="edit({{ $service->id }})"
                            class="text-blue-600 hover:underline">Editar</button>
                    <button wire:click="delete({{ $service->id }})"
                            wire:confirm="¿Eliminar este servicio?"
                            class="text-red-600 hover:underline">Eliminar</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center py-4 text-gray-500">No hay servicios registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>