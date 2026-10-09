<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta {{ $sale->receipt_number }}</title>
    <style>
        /* ====== CONFIGURACIÓN IMPRESORA TÉRMICA ======
           Para 80mm → size: 80mm auto
           Para 58mm → size: 58mm auto  (y ajusta body a 54mm)
        */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', 'Lucida Console', 'Arial', monospace;
            font-size: 11px;
            color: #000;
            width: 76mm;
            margin: 0 auto;
            padding: 3mm 2mm;
            background: #fff;
            line-height: 1.35;
        }

        /* ====== UTILIDADES ====== */
        .center { text-align: center; }
        .right  { text-align: right; }
        .left   { text-align: left; }
        .bold   { font-weight: bold; }
        .small  { font-size: 9px; }
        .xsmall { font-size: 8px; color: #333; }

        .divider {
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }
        .divider-solid {
            border-top: 1px solid #000;
            margin: 2mm 0;
        }
        .divider-double {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 2mm 0;
        }

        /* ====== ENCABEZADO ====== */
        .logo {
            width: 42mm;
            height: auto;
            margin: 0 auto 2mm;
            display: block;
        }
        .business-name {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .business-info {
            font-size: 10px;
            margin-top: 1mm;
            line-height: 1.4;
        }
        .receipt-type {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2mm;
            letter-spacing: 0.5px;
        }
        .receipt-number {
            font-size: 13px;
            font-weight: bold;
            margin-top: 0.5mm;
        }

        /* ====== SECCIÓN INFO ====== */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            background: #e5e7eb;
            padding: 1mm 2mm;
            margin: 2mm 0 1mm;
            letter-spacing: 0.5px;
        }
        .info-line {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            padding: 0.4mm 0;
        }
        .info-line .label {
            font-weight: bold;
        }

        /* ====== TABLA ITEMS ====== */
        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 1mm;
        }
        table.items thead th {
            text-align: left;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.items th.right,
        table.items td.right { text-align: right; }
        table.items th.center,
        table.items td.center { text-align: center; }

        table.items td {
            padding: 1.3mm 0;
            vertical-align: top;
            border-bottom: 1px dotted #999;
        }
        table.items tr:last-child td { border-bottom: none; }

        .item-name {
            font-weight: bold;
            font-size: 10.5px;
            line-height: 1.25;
        }
        .item-detail {
            font-size: 9px;
            color: #444;
            margin-top: 0.3mm;
        }

        /* ====== TOTALES ====== */
        .totals {
            margin-top: 2mm;
            font-size: 10.5px;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 0.5mm 0;
        }
        .totals .row.grand {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 1.5mm 0;
            margin-top: 1mm;
        }

        /* ====== PIE ====== */
        .thanks {
            text-align: center;
            font-size: 10.5px;
            margin-top: 3mm;
            line-height: 1.5;
        }
        .thanks .big {
            font-size: 12px;
            font-weight: bold;
        }
        .hash {
            font-size: 8px;
            word-break: break-all;
            text-align: center;
            margin-top: 2mm;
            color: #333;
        }
        .barcode-text {
            text-align: center;
            font-size: 10px;
            margin-top: 2mm;
            letter-spacing: 1.5px;
            font-family: 'Courier New', monospace;
        }

        /* ====== TOOLBAR SOLO EN PANTALLA ====== */
        .no-print { display: none; }

        @media screen {
            body {
                background: #f3f4f6;
                padding: 20px;
                box-shadow: 0 0 15px rgba(0,0,0,0.08);
                border-radius: 6px;
                margin-top: 20px;
            }
            .print-toolbar {
                background: linear-gradient(135deg, #1e40af, #2563eb);
                color: white;
                padding: 12px;
                text-align: center;
                margin-bottom: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(30,64,175,0.3);
            }
            .print-toolbar strong {
                display: block;
                margin-bottom: 8px;
                font-size: 14px;
            }
            .print-toolbar button {
                background: #16a34a;
                color: white;
                border: none;
                padding: 9px 22px;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                margin: 0 4px;
                transition: background 0.15s;
            }
            .print-toolbar button:hover { background: #15803d; }
            .print-toolbar button.close { background: #dc2626; }
            .print-toolbar button.close:hover { background: #b91c1c; }
        }

        @media print {
            .no-print { display: none !important; }
            body {
                width: 76mm;
                padding: 2mm;
                margin: 0;
                box-shadow: none;
                background: #fff;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar (no se imprime) --}}
    <div class="print-toolbar no-print">
        <strong>🖨️ Vista previa de impresión</strong>
        <button onclick="window.print()">Imprimir ahora</button>
        <button class="close" onclick="window.close()">Cerrar</button>
    </div>

    {{-- ============ ENCABEZADO ============ --}}
    <div class="center">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" class="logo" alt="Céspedes Store">
        @endif

        <div class="business-name">CÉSPEDES STORE</div>
        <div class="business-info">
            Venta de Repuestos y Accesorios<br>
            Servicio Técnico de Celulares<br>
            RUC: 12345678901<br>
            Tel: 999-999-999<br>
            Av. Principal 123 - Lima
        </div>

        <div class="divider-solid"></div>

        <div class="receipt-type">BOLETA DE VENTA</div>
        <div class="receipt-number">{{ $sale->receipt_number }}</div>
    </div>

    <div class="divider"></div>

    {{-- ============ DATOS DE LA VENTA ============ --}}
    <div class="section-title">DATOS DE LA VENTA</div>

    <div class="info-line">
        <span class="label">FECHA:</span>
        <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-line">
        <span class="label">CLIENTE:</span>
        <span>{{ $sale->customer_name ?: 'Cliente general' }}</span>
    </div>
    <div class="info-line">
        <span class="label">MONEDA:</span>
        <span>SOLES (S/)</span>
    </div>

    <div class="divider"></div>

    {{-- ============ ITEMS ============ --}}
    <table class="items">
        <thead>
            <tr>
                <th class="center" style="width: 12%;">Cant.</th>
                <th>Descripción</th>
                <th class="right" style="width: 20%;">P/U</th>
                <th class="right" style="width: 22%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sale->items as $item)
            <tr>
                <td class="center bold">{{ $item->quantity }}</td>
                <td>
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-detail">
                        S/ {{ number_format($item->unit_price, 2) }} c/u
                    </div>
                </td>
                <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="right bold">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="center" style="padding: 3mm 0;">
                    Sin items registrados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ============ TOTALES ============ --}}
    <div class="totals">
        <div class="row">
            <span>Subtotal:</span>
            <span>S/ {{ number_format($sale->total, 2) }}</span>
        </div>
        <div class="row">
            <span>IGV (18%):</span>
            <span>S/ {{ number_format($sale->total * 0.18 / 1.18, 2) }}</span>
        </div>
        <div class="row">
            <span>Op. Gravada:</span>
            <span>S/ {{ number_format($sale->total / 1.18, 2) }}</span>
        </div>
        <div class="row grand">
            <span>TOTAL A PAGAR:</span>
            <span>S/ {{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    <div class="divider"></div>

    {{-- ============ PIE ============ --}}
    <div class="thanks">
        <div class="big">¡GRACIAS POR SU COMPRA!</div>
        <div class="small" style="margin-top: 1mm;">
            Conserve esta boleta como comprobante<br>
            de su compra.
        </div>
    </div>

    <div class="divider"></div>

    <div class="hash">
        Código: {{ strtoupper(md5($sale->id . $sale->receipt_number . $sale->total)) }}
    </div>

    <div class="barcode-text">
        *{{ $sale->receipt_number }}*
    </div>

    <div class="center xsmall" style="margin-top: 2mm;">
        Documento generado electrónicamente
    </div>

    <script>
        // Auto-lanzar el diálogo de impresión al cargar
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 400);
        });

        // (Opcional) Cerrar la ventana tras imprimir
        // window.addEventListener('afterprint', function () { window.close(); });
    </script>
</body>
</html>