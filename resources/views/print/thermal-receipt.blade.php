<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $sale->receipt_number }}</title>
<style>@page{size:80mm auto;margin:0}*{box-sizing:border-box}body{font-family:monospace;font-size:11px;width:76mm;margin:0 auto;padding:3mm;color:#000}.center{text-align:center}.right{text-align:right}.line{border-top:1px dashed #000;margin:2mm 0}table{width:100%;border-collapse:collapse;font-size:10px}td,th{padding:1.5mm 0;border-bottom:1px dotted #aaa}.total{font-weight:bold;font-size:14px;border-top:1px solid #000;margin-top:2mm;padding:2mm 0}.toolbar{padding:10px;background:#eee;margin-bottom:12px;text-align:center}@media print{.toolbar{display:none}}</style></head><body>
<div class="toolbar"><button onclick="window.print()">Imprimir</button></div>
<div class="center"><strong>CÉSPEDES STORE</strong><p>Servicio técnico, repuestos y accesorios</p><div class="line"></div><strong>{{ $sale->voucher_type ?? 'BOLETA' }} DE VENTA</strong><br>{{ $sale->receipt_number }}</div>
<div class="line"></div>
<p>Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</p><p>Cliente: {{ $sale->customer_name }}</p><p>{{ $sale->customer_document_type ?? '' }}: {{ $sale->customer_document_number ?? '—' }}</p>
<div class="line"></div>
<table><thead><tr><th>Cant.</th><th>Descripción</th><th class="right">Total</th></tr></thead><tbody>
@foreach ($sale->items as $item)<tr><td>{{ $item->quantity }}</td><td>{{ $item->name }}<br>S/ {{ number_format($item->unit_price,2) }} c/u</td><td class="right">S/ {{ number_format($item->subtotal,2) }}</td></tr>@endforeach
</tbody></table>
<div class="line"></div>
<p class="right">Subtotal: S/ {{ number_format($sale->subtotal ?? $sale->total,2) }}</p><p class="right">Descuento: S/ {{ number_format($sale->discount_amount ?? 0,2) }}</p>
<div class="total">TOTAL: <span style="float:right">S/ {{ number_format($sale->total,2) }}</span></div>
<div class="line"></div><p class="center">¡Gracias por su compra!</p><p class="center" style="font-size:9px">Comprobante interno; no representa emisión electrónica SUNAT.</p>
<script>window.addEventListener('load',()=>window.print());</script></body></html>
