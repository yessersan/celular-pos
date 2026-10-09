<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class SalePdfController extends Controller
{
    /**
     * Vista imprimible (térmica 80mm) con auto-impresión.
     */
    public function print(Sale $sale): View
    {
        $sale->load('items');

        return view('print.thermal-receipt', compact('sale'));
    }

    /**
     * Descarga en PDF (formato A4).
     */
    public function pdf(Sale $sale): Response
    {
        $sale->load('items');

        $pdf = Pdf::loadView('pdf.receipt', compact('sale'));

        return $pdf->download('boleta-' . $sale->receipt_number . '.pdf');
    }
}