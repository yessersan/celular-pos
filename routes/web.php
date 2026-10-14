<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SalePdfController;
use App\Livewire\ProductManager;
use App\Livewire\SaleManager;
use App\Livewire\SalesReport;
use App\Livewire\ServiceManager;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:6,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('products'));
    Route::get('/products', ProductManager::class)->name('products');
    Route::get('/services', ServiceManager::class)->name('services');
    Route::get('/sales', SaleManager::class)->name('sales');
    Route::get('/reports', SalesReport::class)->name('reports');

    Route::get('/sale/{sale}/print', [SalePdfController::class, 'print'])->name('sale.print');
    Route::get('/sale/{sale}/pdf', [SalePdfController::class, 'pdf'])->name('sale.pdf');
});
