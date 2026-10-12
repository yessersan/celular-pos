<div>
    <h2 class="text-xl font-bold mb-4">Nueva venta</h2>
    @if (session()->has('message')) <div class="bg-green-100 text-green-800 p-3 rounded mb-3">{{ session('message') }}</div> @endif
    @if (session()->has('error')) <div class="bg-red-100 text-red-800 p-3 rounded mb-3">{{ session('error') }}</div> @endif

    <section class="bg-white border rounded p-4 mb-5">
        <h3 class="font-semibold mb-3">Datos del cliente y comprobante</h3>
        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm">Tipo de comprobante</label>
                <select wire:model.live="document_type" class="w-full border rounded p-2">
                    <option value="boleta">Boleta</option><option value="factura">Factura</option>
                </select>
            </div>
            <div>
                <label class="block text-sm">{{ $document_type === 'factura' ? 'RUC (11 dígitos)' : 'DNI (8 dígitos)' }}</label>
                <div class="flex gap-2">
                    <input wire:model="customer_document" inputmode="numeric" maxlength="11" class="min-w-0 flex-1 border rounded p-2" placeholder="Documento del cliente">
                    @if ($document_type === 'boleta')
                        <button type="button" wire:click="lookupDni" wire:loading.attr="disabled" class="bg-blue-700 text-white rounded px-3">Consultar DNI</button>
                    @endif
                </div>
                @error('customer_document') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div><label class="block text-sm">Nombre / Razón social</label><input wire:model="customer_name" class="w-full border rounded p-2" placeholder="Nombre del cliente o empresa">@error('customer_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror</div>
            <div><label class="block text-sm">Dirección fiscal</label><input wire:model="customer_address" class="w-full border rounded p-2" placeholder="Dirección">@error('customer_address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror</div>
        </div>
        <p class="text-xs text-gray-500 mt-2">La consulta DNI requiere configurar APISPERU_TOKEN en el archivo .env.</p>
    </section>

    <div class="grid md:grid-cols-2 gap-4 mb-5">
        <section class="bg-white border rounded p-4">
            <h3 class="font-semibold mb-2">Agregar producto</h3>
            <input wire:model.live.debounce.300ms="productSearch" class="w-full border rounded p-2 mb-2" placeholder="Buscar producto por nombre">
            <select wire:model="selectedProduct" class="w-full border rounded p-2 mb-2"><option value="">Seleccionar producto...</option>
                @foreach ($products as $product) <option value="{{ $product->id }}">{{ $product->name }} (stock: {{ $product->stock }}) — S/ {{ number_format($product->sale_price, 2) }}</option> @endforeach
            </select>
            <div class="flex gap-2"><input type="number" wire:model="quantity" min="1" class="w-24 border rounded p-2"><button type="button" wire:click="addProduct" class="bg-blue-700 text-white rounded px-4">Agregar</button></div>
            @error('selectedProduct') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            @error('quantity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </section>
        <section class="bg-white border rounded p-4">
            <h3 class="font-semibold mb-2">Agregar servicio</h3>
            <input wire:model.live.debounce.300ms="serviceSearch" class="w-full border rounded p-2 mb-2" placeholder="Buscar servicio por nombre">
            <select wire:model="selectedService" class="w-full border rounded p-2 mb-2"><option value="">Seleccionar servicio...</option>
                @foreach ($services as $service) <option value="{{ $service->id }}">{{ $service->name }} — S/ {{ number_format($service->price, 2) }}</option> @endforeach
            </select>
            <button type="button" wire:click="addService" class="bg-blue-700 text-white rounded px-4 py-2">Agregar servicio</button>
            @error('selectedService') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </section>
    </div>

    <section class="bg-white border rounded p-4">
        <h3 class="font-semibold mb-3">Detalle de venta</h3>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="bg-gray-100"><th class="p-2 text-left">Artículo</th><th class="p-2">Tipo</th><th class="p-2 text-right">Precio unit.</th><th class="p-2">Cant.</th><th class="p-2 text-right">Subtotal</th><th></th></tr></thead>
            <tbody>
            @forelse ($items as $index => $item)
                <tr wire:key="sale-item-{{ $index }}" class="border-t"><td class="p-2">{{ $item['name'] }}</td><td class="p-2 text-center">{{ $item['type'] === 'product' ? 'Producto' : 'Servicio' }}</td><td class="p-2 text-right">S/ {{ number_format($item['unit_price'], 2) }}</td><td class="p-2 text-center">{{ $item['quantity'] }}</td><td class="p-2 text-right">S/ {{ number_format($item['subtotal'], 2) }}</td><td class="p-2"><button type="button" wire:click="removeItem({{ $index }})" class="text-red-600">Quitar</button></td></tr>
            @empty <tr><td colspan="6" class="p-4 text-center text-gray-500">Agrega productos o servicios para comenzar.</td></tr> @endforelse
            </tbody>
        </table></div>
        <div class="max-w-sm ml-auto mt-4 space-y-2">
            <div class="flex justify-between"><span>Subtotal</span><strong>S/ {{ number_format($this->subtotal, 2) }}</strong></div>
            <div class="flex justify-between items-center gap-3"><label for="discount">Descuento (%)</label><input id="discount" type="number" min="0" max="100" step="0.01" wire:model.live="discount_percent" class="w-28 border rounded p-2"></div>
            @error('discount_percent') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            <div class="flex justify-between"><span>Descuento</span><strong>− S/ {{ number_format($this->discountAmount, 2) }}</strong></div>
            <div class="flex justify-between text-lg border-t pt-2"><span>Total</span><strong>S/ {{ number_format($this->total, 2) }}</strong></div>
            <button type="button" wire:click="saveSale" wire:loading.attr="disabled" class="w-full bg-green-700 text-white rounded px-4 py-3 font-semibold">Confirmar venta</button>
        </div>
    </section>
</div>
