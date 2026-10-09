<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta {{ $sale->receipt_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 90px; height: auto; margin-right: 15px; }
        .business-info { flex: 1; }
        .business-name { font-size: 22px; font-weight: bold; color: #1e40af; }
        .business-sub { font-size: 12px; color: #555; margin-top: 3px; }
        .receipt-title { text-align: right; font-size: 16px; font-weight: bold; color: #1e40af; }
        .receipt-number { font-size: 14px; color: #333; }
        .info-box { background: #f3f4f6; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 3px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.items th { background: #1e40af; color: white; padding: 8px; text-align: left; }
        table.items td { border-bottom: 1px solid #e5e7eb; padding: 8px; }
        table.items .right { text-align: right; }
        table.items .center { text-align: center; }
        .total-row { font-weight: bold; font-size: 15px; background: #f3f4f6; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #777; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" class="logo">
        @endif
        <div class="business-info">
            <div class="business-name">CÉSPEDES STORE</div>
            <div class="business-sub">
                Reparación de Celulares - Repuestos y Accesorios<br>
                RUC: 12345678901 | Tel: 999-999-999<br>
                Av. Principal 123
            </div>
        </div>
        <div>
            <div class="receipt-title">BOLETA DE VENTA</div>
            <div class="receipt-number">{{ $sale->receipt_number }}</div>
        </div>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</td>
                <td><strong>Cliente:</strong> {{ $sale->customer_name }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 10%">Cant.</th>
                <th>Descripción</th>
                <th class="right" style="width: 20%">P. Unitario</th>
                <th class="right" style="width: 20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
            <tr>
                <td class="center">{{ $item->quantity }}</td>
                <td>{{ $item->name }}</td>
                <td class="right">S/ {{ number_format($item->unit_price, 2) }}</td>
                <td class="right">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="right">TOTAL:</td>
                <td class="right">S/ {{ number_format($sale->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>¡Gracias por su preferencia!</p>
        <p>Conserve esta boleta como comprobante de su compra.</p>
    </div>
</body>
</html>