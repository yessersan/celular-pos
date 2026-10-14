<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>{{ $sale->voucher_type ?? 'BOLETA' }} {{ $sale->receipt_number }}</title>
<style>
body{font-family:'DejaVu Sans',sans-serif;font-size:12px;color:#333}.header{border-bottom:2px solid #1e40af;padding-bottom:12px;margin-bottom:18px}.business{font-size:20px;font-weight:bold;color:#1e40af}.muted{color:#666;font-size:11px}.info{background:#f3f4f6;padding:10px;margin-bottom:14px}table{width:100%;border-collapse:collapse}th{background:#1e40af;color:white;text-align:left}th,td{padding:8px;border-bottom:1px solid #ddd}.right{text-align:right}.total{font-weight:bold;font-size:15px}.footer{text-align:center;margin-top:25px;color:#666;font-size:10px}
</style></head><body>
<div class="header"><div class="business">CÉSPEDES STORE</div><div class="muted">Reparación de celulares, repuestos y accesorios</div><h2>{{ $sale->voucher_type ?? 'BOLETA' }} DE VENTA</h2><strong>{{ $sale->receipt_number }}</strong></div>
<div class="info">
<p><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
<p><strong>Cliente:</strong> {{ $sale->customer_name }}</p>
<p><strong>Documento:</strong> {{ $sale->customer_document_type ?? '' }} {{ $sale->customer_document_number ?? '' }}</p>
<p><strong>Dirección:</strong> {{ $sale->customer_address ?: '—' }}</p>
<p><strong>Correo:</strong> {{ $sale->customer_email ?: '—' }}</p>
</div>
<table><thead><tr><th>Cant.</th><th>Descripción</th><th class="right">P. unitario</th><th class="right">Subtotal</th></tr></thead>
<tbody>@foreach ($sale->items as $item)<tr><td>{{ $item->quantity }}</td><td>{{ $item->name }}</td><td class="right">S/ {{ number_format($item->unit_price, 2) }}</td><td class="right">S/ {{ number_format($item->subtotal, 2) }}</td></tr>@endforeach</tbody>
<tfoot><tr><td colspan="3" class="right">Subtotal</td><td class="right">S/ {{ number_format($sale->subtotal ?? $sale->total, 2) }}</td></tr>
<tr><td colspan="3" class="right">Descuento</td><td class="right">S/ {{ number_format($sale->discount_amount ?? 0, 2) }}</td></tr>
<tr class="total"><td colspan="3" class="right">TOTAL A PAGAR</td><td class="right">S/ {{ number_format($sale->total, 2) }}</td></tr></tfoot></table>
<div class="footer">Comprobante interno generado por el sistema POS. No equivale a una factura electrónica validada por SUNAT.</div>
</body></html>
