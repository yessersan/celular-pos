<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de venta</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            margin: 30px auto;
            max-width: 850px;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
        }

        .info {
            margin: 20px 0;
            line-height: 1.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-print {
            background: #2563eb;
        }

        .btn-back {
            background: #4b5563;
        }

        .thanks {
            text-align: center;
            margin-top: 35px;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="actions">
        <button class="btn btn-print" onclick="window.print()">
            Imprimir comprobante
        </button>

        <a href="{{ url('/reports') }}" class="btn btn-back">
            Volver al reporte
        </a>
    </div>

    <div class="header">
        <h1>COMPROBANTE DE VENTA</h1>
        <p>Venta de productos y servicios tecnológicos</p>
    </div>

    <div class="info">
        <strong>Número de venta:</strong>
        {{ $sale->receipt_number ?? 'Venta #' . $sale->id }}
        <br>

        <strong>Fecha:</strong>
        {{ $sale->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}
        <br>

        <strong>Cliente:</strong>
        {{ $sale->customer_name ?: 'Cliente general' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($sale->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name
                            ?? $item->name
                            ?? 'Artículo #' . $item->id }}
                    </td>

                    <td>
                        {{ $item->quantity ?? 1 }}
                    </td>

                    <td>
                        S/
                        {{ number_format(
                            (float) ($item->price ?? $item->unit_price ?? 0),
                            2
                        ) }}
                    </td>

                    <td>
                        S/
                        {{ number_format(
                            (float) (
                                $item->subtotal
                                ?? (($item->quantity ?? 1) *
                                    ($item->price ?? $item->unit_price ?? 0))
                            ),
                            2
                        ) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">
                        No hay artículos registrados en esta venta.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        Total: S/ {{ number_format((float) $sale->total, 2) }}
    </div>

    <div class="thanks">
        <p>Gracias por su compra.</p>
    </div>
</body>
</html>