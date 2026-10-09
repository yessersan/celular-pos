
<div>
    <div class="container mx-auto px-4 py-6">

        {{-- Encabezado --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Reporte de ventas
                </h1>

                <p class="text-gray-500 mt-1">
                    Consulta las ventas registradas en el sistema.
                </p>
            </div>

            <a
                href="{{ url('/sales') }}"
                class="inline-block rounded bg-gray-700 px-4 py-2 text-center text-white hover:bg-gray-800"
            >
                Volver a ventas
            </a>
        </div>

        {{-- Filtros por periodo --}}
        <div class="rounded-lg bg-white p-4 shadow mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">
                Filtrar por periodo
            </h2>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="$set('period', 'today')"
                    wire:loading.attr="disabled"
                    class="rounded px-4 py-2
                    {{ $period === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    Hoy
                </button>

                <button
                    type="button"
                    wire:click="$set('period', 'week')"
                    wire:loading.attr="disabled"
                    class="rounded px-4 py-2
                    {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    Esta semana
                </button>

                <button
                    type="button"
                    wire:click="$set('period', 'month')"
                    wire:loading.attr="disabled"
                    class="rounded px-4 py-2
                    {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                >
                    Este mes
                </button>
            </div>

            <p wire:loading class="mt-3 text-sm text-blue-600">
                Actualizando reporte...
            </p>
        </div>

        {{-- Resumen --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="rounded-lg bg-white p-5 shadow">
                <p class="text-sm text-gray-500">
                    Número de ventas
                </p>

                <p class="mt-2 text-3xl font-bold text-gray-800">
                    {{ $sales->count() }}
                </p>
            </div>

            <div class="rounded-lg bg-white p-5 shadow">
                <p class="text-sm text-gray-500">
                    Total vendido
                </p>

                <p class="mt-2 text-3xl font-bold text-green-600">
                    S/ {{ number_format($total, 2) }}
                </p>
            </div>
        </div>

        {{-- Tabla de ventas --}}
        <div class="rounded-lg bg-white shadow">
            <div class="border-b p-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Detalle de ventas
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-700">
                            <th class="border-b p-3">N.º de venta</th>
                            <th class="border-b p-3">Fecha</th>
                            <th class="border-b p-3">Cliente</th>
                            <th class="border-b p-3">Artículos</th>
                            <th class="border-b p-3">Total</th>
                            <th class="border-b p-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sales as $sale)
                            <tr
                                wire:key="sale-{{ $sale->id }}"
                                class="hover:bg-gray-50"
                            >
                                <td class="border-b p-3">
                                    {{ $sale->receipt_number ?? 'Venta #' . $sale->id }}
                                </td>

                                <td class="border-b p-3">
                                    {{ $sale->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="border-b p-3">
                                    {{ $sale->customer_name ?: 'Cliente general' }}
                                </td>

                                <td class="border-b p-3">
                                    {{ $sale->items->count() }}
                                </td>

                                <td class="border-b p-3 font-semibold">
                                    S/ {{ number_format((float) $sale->total, 2) }}
                                </td>

                                <td class="border-b p-3">
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <a
                                            href="{{ route('sale.print', $sale->id) }}"
                                            target="_blank"
                                            class="rounded bg-blue-600 px-3 py-2 text-white hover:bg-blue-700"
                                        >
                                            Imprimir
                                        </a>

                                        <a
                                            href="{{ route('sale.pdf', $sale->id) }}"
                                            target="_blank"
                                            class="rounded bg-red-600 px-3 py-2 text-white hover:bg-red-700"
                                        >
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="p-6 text-center text-gray-500"
                                >
                                    No hay ventas registradas para este periodo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Total general --}}
            @if ($sales->isNotEmpty())
                <div class="border-t bg-gray-50 p-4 text-right">
                    <span class="text-gray-600">
                        Total del periodo:
                    </span>

                    <span class="ml-2 text-lg font-bold text-green-700">
                        S/ {{ number_format($total, 2) }}
                    </span>
                </div>
            @endif
        </div>

    </div>
</div>
