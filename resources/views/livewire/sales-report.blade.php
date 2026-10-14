<div class="space-y-6">
    <header><h1 class="text-2xl font-bold text-gray-800">Reporte de ventas</h1><p class="mt-1 text-gray-500">Busca por comprobante, cliente o documento.</p></header>
    <section class="rounded-lg bg-white p-4 shadow">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label for="sale-search" class="mb-1 block text-sm font-medium">Buscar venta</label>
                <input id="sale-search" type="search" wire:model.live.debounce.300ms="search" placeholder="N.º de comprobante, cliente o DNI/RUC" class="w-full rounded border px-3 py-2">
            </div>
            <div>
                <label for="sale-period" class="mb-1 block text-sm font-medium">Periodo</label>
                <select id="sale-period" wire:model.live="period" class="w-full rounded border px-3 py-2">
                    <option value="today">Hoy</option><option value="week">Esta semana</option><option value="month">Este mes</option><option value="all">Todos</option>
                </select>
            </div>
        </div>
        <p wire:loading class="mt-3 text-sm text-blue-600">Actualizando reporte...</p>
    </section>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow"><p class="text-sm text-gray-500">Número de ventas</p><p class="mt-2 text-3xl font-bold">{{ $sales->count() }}</p></div>
        <div class="rounded-lg bg-white p-5 shadow"><p class="text-sm text-gray-500">Total vendido (resultados filtrados)</p><p class="mt-2 text-3xl font-bold text-green-700">S/ {{ number_format($total, 2) }}</p></div>
    </section>
    <section class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full">
            <thead class="bg-gray-100"><tr>
                <th class="px-4 py-3 text-left">Comprobante</th><th class="px-4 py-3 text-left">Fecha</th>
                <th class="px-4 py-3 text-left">Cliente</th><th class="px-4 py-3 text-left">Documento</th>
                <th class="px-4 py-3 text-left">Tipo</th><th class="px-4 py-3 text-right">Descuento</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Acciones</th>
            </tr></thead>
            <tbody>
            @forelse ($sales as $sale)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $sale->receipt_number }}</td>
                    <td class="px-4 py-3">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $sale->customer_name }}</td>
                    <td class="px-4 py-3">{{ $sale->customer_document_number ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $sale->voucher_type ?: 'BOLETA' }}</td>
                    <td class="px-4 py-3 text-right">S/ {{ number_format($sale->discount_amount ?? 0, 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold">S/ {{ number_format($sale->total, 2) }}</td>
                    <td class="px-4 py-3 text-center"><a class="text-blue-700 underline" href="{{ route('sale.pdf', $sale) }}">PDF</a> · <a class="text-blue-700 underline" href="{{ route('sale.print', $sale) }}">Imprimir</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay ventas para los filtros seleccionados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</div>
