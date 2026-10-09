<?php

use App\Http\Controllers\SalePdfController;
use App\Livewire\ProductManager;
use App\Livewire\SaleManager;
use App\Livewire\SalesReport;
use App\Livewire\ServiceManager;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('products'));

Route::get('/products', ProductManager::class)->name('products');
Route::get('/services', ServiceManager::class)->name('services');
Route::get('/sales', SaleManager::class)->name('sales');
Route::get('/reports', SalesReport::class)->name('reports');

// Imprimir en térmica (abre ventana con auto-print)
Route::get('/sale/{sale}/print', [SalePdfController::class, 'print'])->name('sale.print');

// Descargar PDF A4
Route::get('/sale/{sale}/pdf', [SalePdfController::class, 'pdf'])->name('sale.pdf');