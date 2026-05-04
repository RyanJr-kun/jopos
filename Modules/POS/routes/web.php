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
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::resource('pos', POSController::class)->names('pos');
    
        //transaksi penjualan
        Route::get('/penjualan/history/today', [SaleController::class, 'getTodayHistory'])->name('penjualan.history.today');
        Route::get('/penjualan/get-products', [SaleController::class, 'getProductsForCashier'])->name('penjualan.get-products');
        Route::resource('/penjualan', SaleController::class)->except('destroy');
        Route::get('/penjualan/{penjualan}/json', [SaleController::class, 'getjson'])->name('penjualan.getjson');
        Route::get('/penjualan/{penjualan:referensi}/thermal', [SaleController::class, 'printThermal'])->name('penjualan.thermal');
        Route::get('/penjualan/{penjualan:referensi}/pdf', [SaleController::class, 'generatePdf'])->name('penjualan.pdf');
    });
});

