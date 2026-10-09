<div>
    {{-- Título --}}
    <h2 class="text-xl font-bold mb-4">Nueva Venta</h2>

    {{-- Mensajes --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Todo el contenido de tu formulario de ventas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Agregar producto --}}
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="font-semibold mb-2">Agregar Producto</h3>

            <select wire:model="selectedProduct"
                    class="w-full border rounded px-3 py-2 mb-2">
                <option value="">Seleccionar producto...</option>

                @foreach ($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                        (Stock: {{ $product->stock }})
                        - S/ {{ number_format($product->sale_price, 2) }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="number"
                       wire:model="quantity"
                       min="1"
                       class="w-24 border rounded px-3 py-2">

                <button type="button"
                        wire:click="addProduct"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                    Agregar
                </button>
            </div>

            @error('selectedProduct')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            @error('quantity')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Agregar servicio --}}
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="font-semibold mb-2">Agregar Servicio</h3>

            <select wire:model="selectedService"
                    class="w-full border rounded px-3 py-2 mb-2">
                <option value="">Seleccionar servicio...</option>

                @foreach ($services as $service)
                    <option value="{{ $service->id }}">
                        {{ $service->name }}
                        - S/ {{ number_format($service->price, 2) }}
                    </option>
                @endforeach
            </select>

            <button type="button"
                    wire:click="addService"
                    class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                Agregar Servicio
            </button>

            @error('selectedService')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Carrito --}}
    @if (!empty($items))
        <div class="overflow-x-auto mb-4">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 border">Descripción</th>
                        <th class="py-2 px-4 border">Tipo</th>
                        <th class="py-2 px-4 border">P. Unitario</th>
                        <th class="py-2 px-4 border">Cantidad</th>
                        <th class="py-2 px-4 border">Subtotal</th>
                        <th class="py-2 px-4 border">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr wire:key="sale-item-{{ $index }}">
                            <td class="py-2 px-4 border">{{ $item['name'] }}</td>

                            <td class="py-2 px-4 border text-center">
                                {{ $item['type'] === 'product' ? 'Producto' : 'Servicio' }}
                            </td>

                            <td class="py-2 px-4 border text-right">
                                S/ {{ number_format($item['unit_price'], 2) }}
                            </td>

                            <td class="py-2 px-4 border text-center">
                                {{ $item['quantity'] }}
                            </td>

                            <td class="py-2 px-4 border text-right">
                                S/ {{ number_format($item['subtotal'], 2) }}
                            </td>

                            <td class="py-2 px-4 border text-center">
                                <button type="button"
                                        wire:click="removeItem({{ $index }})"
                                        class="text-red-600 hover:underline">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="4" class="py-2 px-4 border text-right">
                            TOTAL:
                        </td>

                        <td class="py-2 px-4 border text-right">
                            S/ {{ number_format($this->getTotalProperty(), 2) }}
                        </td>

                        <td class="border"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Registrar venta --}}
        <div class="flex flex-col md:flex-row gap-4 items-end mb-4">
            <div class="flex-1 w-full">
                <label for="customer_name" class="block text-sm font-medium mb-1">
                    Nombre del cliente (opcional)
                </label>

                <input id="customer_name"
                       type="text"
                       wire:model="customer_name"
                       class="w-full border rounded px-3 py-2"
                       placeholder="Cliente general">
            </div>

            <button type="button"
                    wire:click="saveSale"
                    wire:loading.attr="disabled"
                    wire:target="saveSale"
                    class="bg-green-600 text-white px-6 py-2 rounded font-bold">
                <span wire:loading.remove wire:target="saveSale">
                    Registrar Venta y Generar Boleta
                </span>

                <span wire:loading wire:target="saveSale">
                    Registrando...
                </span>
            </button>
        </div>

        {{-- Boleta --}}
        @if ($saleCompleted)
            <div class="mt-4 flex flex-wrap gap-3 items-center bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <div class="flex-1">
                    <p class="font-semibold text-green-800">
                        Venta #{{ $saleCompleted }} registrada
                    </p>

                    <p class="text-sm text-green-700">
                        ¿Deseas imprimir la boleta?
                    </p>
                </div>

                <button type="button"
                        wire:click="printReceipt({{ $saleCompleted }})"
                        class="bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Imprimir boleta
                </button>

                <button type="button"
                        wire:click="downloadPdf({{ $saleCompleted }})"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Descargar PDF
                </button>
            </div>
        @endif

    @else
        <p class="text-gray-500 text-center py-8">
            Agregue productos o servicios para comenzar.
        </p>
    @endif
</div>