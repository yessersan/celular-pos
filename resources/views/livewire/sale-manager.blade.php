<div>
    <h2 class="mb-4 text-xl font-bold">Nueva venta</h2>

    @if (session()->has('message'))
        <div role="status" class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div role="alert" class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}</div>
    @endif

    <section class="mb-6 rounded border bg-white p-4">
        <h3 class="mb-3 font-semibold">Datos del cliente y comprobante</h3>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm" for="voucher_type">Comprobante</label>
                <select id="voucher_type" wire:model="voucher_type" class="w-full rounded border px-3 py-2">
                    <option value="BOLETA">Boleta</option>
                    <option value="FACTURA">Factura</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm" for="customer_document_type">Documento</label>
                <select id="customer_document_type" wire:model.live="customer_document_type" class="w-full rounded border px-3 py-2">
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                </select>
                @error('customer_document_type') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm" for="customer_document_number">N.º documento</label>
                <div class="flex gap-2">
                    <input id="customer_document_number" wire:model="customer_document_number" inputmode="numeric" class="min-w-0 flex-1 rounded border px-3 py-2" maxlength="{{ $customer_document_type === 'RUC' ? 11 : 8 }}">
                    <button type="button" wire:click="lookupCustomer" wire:loading.attr="disabled" class="rounded bg-blue-700 px-3 py-2 text-white">Consultar</button>
                </div>
                @error('customer_document_number') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm" for="customer_name">Nombre / razón social</label>
                <input id="customer_name" wire:model="customer_name" class="w-full rounded border px-3 py-2" required>
                @error('customer_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm" for="customer_address">Dirección fiscal (opcional)</label>
                <input id="customer_address" wire:model="customer_address" class="w-full rounded border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm" for="customer_email">Correo (opcional)</label>
                <input id="customer_email" type="email" wire:model="customer_email" class="w-full rounded border px-3 py-2">
                @error('customer_email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">La consulta recupera los datos disponibles en ApisPeru. Para una factura se requiere RUC.</p>
    </section>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        <section class="rounded border bg-gray-50 p-4">
            <h3 class="mb-2 font-semibold">Agregar producto</h3>
            <select wire:model="selectedProduct" class="mb-2 w-full rounded border px-3 py-2">
                <option value="">Seleccionar producto...</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }}) — S/ {{ number_format($product->sale_price, 2) }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <input type="number" wire:model="quantity" min="1" class="w-24 rounded border px-3 py-2">
                <button type="button" wire:click="addProduct" class="rounded bg-blue-700 px-4 py-2 text-white">Agregar</button>
            </div>
            @error('selectedProduct') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('quantity') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </section>
        <section class="rounded border bg-gray-50 p-4">
            <h3 class="mb-2 font-semibold">Agregar servicio</h3>
            <select wire:model="selectedService" class="mb-2 w-full rounded border px-3 py-2">
                <option value="">Seleccionar servicio...</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }} — S/ {{ number_format($service->price, 2) }}</option>
                @endforeach
            </select>
            <button type="button" wire:click="addService" class="w-full rounded bg-blue-700 px-4 py-2 text-white">Agregar servicio</button>
            @error('selectedService') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </section>
    </div>

    @if (!empty($items))
        <div class="mb-4 overflow-x-auto rounded border bg-white">
            <table class="min-w-full">
                <thead class="bg-gray-100"><tr>
                    <th class="border px-3 py-2 text-left">Descripción</th><th class="border px-3 py-2">Tipo</th>
                    <th class="border px-3 py-2 text-right">P. unitario</th><th class="border px-3 py-2">Cantidad</th>
                    <th class="border px-3 py-2 text-right">Subtotal</th><th class="border px-3 py-2">Acción</th>
                </tr></thead>
                <tbody>
                @foreach ($items as $index => $item)
                    <tr wire:key="sale-item-{{ $index }}">
                        <td class="border px-3 py-2">{{ $item['name'] }}</td>
                        <td class="border px-3 py-2 text-center">{{ $item['type'] === 'product' ? 'Producto' : 'Servicio' }}</td>
                        <td class="border px-3 py-2 text-right">S/ {{ number_format($item['unit_price'], 2) }}</td>
                        <td class="border px-3 py-2 text-center">{{ $item['quantity'] }}</td>
                        <td class="border px-3 py-2 text-right">S/ {{ number_format($item['subtotal'], 2) }}</td>
                        <td class="border px-3 py-2 text-center"><button type="button" wire:click="removeItem({{ $index }})" class="text-red-600">Quitar</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <section class="mb-4 rounded border bg-white p-4">
            <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
                <div>
                    <label for="discount_amount" class="mb-1 block text-sm font-medium">Descuento para esta venta (S/)</label>
                    <input id="discount_amount" type="number" min="0" max="{{ $this->getSubtotalProperty() }}" step="0.01" wire:model.live="discount_amount" class="w-full rounded border px-3 py-2">
                    @error('discount_amount') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-500">No cambia los precios guardados en productos o servicios.</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Subtotal</p>
                    <p class="text-lg font-semibold">S/ {{ number_format($this->getSubtotalProperty(), 2) }}</p>
                    <p class="text-sm text-gray-500">Descuento</p>
                    <p class="font-semibold text-red-600">− S/ {{ number_format(min(max(0, $discount_amount), $this->getSubtotalProperty()), 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total a pagar</p>
                    <p class="text-2xl font-bold text-green-700">S/ {{ number_format($this->getTotalProperty(), 2) }}</p>
                </div>
            </div>
            <button type="button" wire:click="saveSale" wire:loading.attr="disabled" class="mt-4 rounded bg-green-700 px-6 py-3 font-bold text-white">
                <span wire:loading.remove>Registrar venta y generar comprobante interno</span>
                <span wire:loading>Registrando...</span>
            </button>
        </section>
    @else
        <p class="rounded bg-white py-8 text-center text-gray-500">Agrega productos o servicios para comenzar.</p>
    @endif

    @if ($saleCompleted)
        <div class="mt-4 flex flex-wrap items-center gap-3 rounded border-l-4 border-green-600 bg-green-50 p-4">
            <div class="flex-1"><p class="font-semibold">Venta #{{ $saleCompleted }} registrada</p><p class="text-sm">Puedes imprimir o descargar el comprobante.</p></div>
            <button type="button" wire:click="printReceipt({{ $saleCompleted }})" class="rounded bg-blue-700 px-4 py-2 text-white">Imprimir</button>
            <button type="button" wire:click="downloadPdf({{ $saleCompleted }})" class="rounded bg-gray-700 px-4 py-2 text-white">Descargar PDF</button>
        </div>
    @endif
</div>
