<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\Http\Controllers\POSController;
use Modules\POS\Http\Controllers\SaleController;

// Mengambil variabel domain dari .env
$domain = env('APP_DOMAIN', 'jocomputer.com');

// =========================================================
// ROUTING SUBDOMAIN (JOPOS - Khusus Manajemen Inventory)
// =========================================================
Route::domain('jopos.' . $domain)->group(function () {
    Route::middleware(['auth', 'verified', 'employee'])->group(function () {
        Route::resource('pos', POSController::class)->names('pos');
        
        // 1. Letakkan rute kustom / spesifik di atas
        Route::get('/penjualan/history/today', [SaleController::class, 'getTodayHistory'])->name('penjualan.history.today');
        Route::get('/penjualan/get-products', [SaleController::class, 'getProductsForCashier'])->name('penjualan.get-products');
        Route::get('/penjualan/produk', [SaleController::class, 'getProduct'])->name('getDataProduct');
        
        Route::get('/penjualan/{penjualan}/json', [SaleController::class, 'getjson'])->name('penjualan.getjson');
        Route::get('/penjualan/{penjualan:referensi}/matrix', [SaleController::class, 'printThermal'])->name('penjualan.matrix');
        Route::get('/penjualan/{penjualan:referensi}/pdf', [SaleController::class, 'generatePdf'])->name('penjualan.pdf');
        Route::post('penjualan/{penjualan}/payment', [SaleController::class, 'storePayment'])->name('penjualan.payment.store');

        // 2. Letakkan Route::resource di paling bawah
        Route::resource('/penjualan', SaleController::class)->except('destroy');
    });
});

