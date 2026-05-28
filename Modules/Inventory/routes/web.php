<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\master\BrandController;
use Modules\Inventory\Http\Controllers\master\CategoryController;
use Modules\Inventory\Http\Controllers\master\SerialNumberController;
use Modules\Inventory\Http\Controllers\master\SupplierController;
use Modules\Inventory\Http\Controllers\master\UnitController;
use Modules\Inventory\Http\Controllers\master\WarrantieController;
use Modules\Inventory\Http\Controllers\stok\StockAdjustmentController;
use Modules\Inventory\Http\Controllers\stok\StockTakeController;
use Modules\Inventory\Http\Controllers\produk\PurchaseController;
use Modules\Inventory\Http\Controllers\produk\ProductController;

// Mengambil variabel domain dari .env
$domain = env('APP_DOMAIN', 'jocomputer.com');

// =========================================================
// ROUTING SUBDOMAIN (JOPOS - Khusus Manajemen Inventory)
// =========================================================
Route::domain('jopos.' . $domain)->group(function () {
    
    // Semua rute inventory memerlukan login dan verifikasi
    Route::middleware(['auth', 'verified', 'employee'])->group(function () {

        // ---------------------------------------------------------
        // 1. MANAJEMEN STOK (Stock Management)
        // ---------------------------------------------------------
        Route::get('stok-opname', [StockTakeController::class, 'index'])->name('stok-opname.index')->middleware('permission:create-stok-opname');  
        Route::post('stok-opname', [StockTakeController::class, 'store'])->name('stok-opname.store')->middleware('permission:create-stok-opname');  
        Route::get('stok-opname/history', [StockTakeController::class, 'history'])->name('stok-opname.history')->middleware('permission:view-stok-opname');  
        Route::get('stok-opname/history/{stok_opname}', [StockTakeController::class, 'show'])->name('stok-opname.show')->middleware('permission:view-stok-opname');  
        
        Route::resource('stok-penyesuaian', StockAdjustmentController::class)->except(['edit', 'update']);  
        Route::get('/stok/rendah', [ProductController::class, 'lowStock'])->name('stok.rendah')->middleware('permission:view-stok-rendah');  


        // ---------------------------------------------------------
        // 2. DATA UTAMA PRODUK (Master Data Products)
        // ---------------------------------------------------------
        Route::prefix('produk')->name('produk.')->group(function () {
            Route::post('upload', [ProductController::class, 'upload'])->name('upload');  
            Route::delete('revert', [ProductController::class, 'revert'])->name('revert');  
            Route::get('checkSlug', [ProductController::class, 'checkSlug'])->name('checkSlug');  
        });
        Route::resource('produk', ProductController::class)->parameter('produk', 'produk:slug');  

        // Manajemen Serial Number
        // PENTING: Rute spesifik harus didefinisikan SEBELUM wildcard {produk_slug?}
        Route::get('serialNumber/get-by-product/{product_id}', [SerialNumberController::class, 'getByProduct'])->name('serialNumber.getByProduct');  
        Route::resource('serialNumber', SerialNumberController::class)->except(['show', 'index']);  
        Route::get('serialNumber/{produk_slug?}', [SerialNumberController::class, 'index'])->name('serialNumber.index');  


        // ---------------------------------------------------------
        // 3. MASTER DATA PENDUKUNG (Categories, Brands, Units, etc.)
        // ---------------------------------------------------------
        
        // Kategori Produk
        Route::get('/kategoriproduk/{kategoriproduk}/json', [CategoryController::class, 'getKategoriJson'])->name('kategoriproduk.getjson');  
        Route::resource('kategoriproduk', CategoryController::class)->except('show', 'create', 'edit');  
        Route::get('/dashboard/kategoriproduk/chekSlug', [CategoryController::class, 'chekSlug']);  
        Route::post('/dashboard/kategoriproduk/upload', [CategoryController::class, 'upload'])->name('kategoriproduk.upload');  
        Route::delete('/dashboard/kategoriproduk/revert', [CategoryController::class, 'revert'])->name('kategoriproduk.revert');  

        // Brand (Merek)
        Route::get('/brand/{brand}/json', [BrandController::class, 'getBrandJson'])->name('brand.getjson');  
        Route::resource('brand', BrandController::class)->except('show', 'create', 'edit');  
        Route::get('/dashboard/brand/chekSlug', [BrandController::class, 'chekSlug']);  
        Route::post('/dashboard/brand/upload', [BrandController::class, 'upload'])->name('brand.upload');  
        Route::delete('/dashboard/brand/revert', [BrandController::class, 'revert'])->name('brand.revert');  

        // Unit (Satuan)
        Route::get('/unit/{unit}/json', [UnitController::class, 'getUnitJson'])->name('unit.getjson');  
        Route::resource('unit', UnitController::class)->except('show', 'create', 'edit');  
        Route::get('/dashboard/unit/chekSlug', [UnitController::class, 'chekSlug']);  

        // Garansi
        Route::get('/garansi/{garansi:slug}/json', [WarrantieController::class, 'getWarrantieJson'])->name('garansi.getjson');  
        Route::resource('garansi', WarrantieController::class)->except('show', 'create', 'edit')->parameters(['garansi' => 'garansi:slug']);  
        Route::get('/dashboard/garansi/chekSlug', [WarrantieController::class, 'chekSlug']);  


        // ---------------------------------------------------------
        // 4. PEMBELIAN & PEMASOK (Purchases & Suppliers)
        // ---------------------------------------------------------
        
        // Transaksi Pembelian
        Route::resource('/pembelian', PurchaseController::class)->parameter('pembelian', 'pembelian:referensi');  
        Route::get('/pembelian/{pembelian:referensi}/pdf', [PurchaseController::class, 'generatePdf'])->name('pembelian.pdf');  
        Route::get('/pembelian/{pembelian:referensi}/thermal', [PurchaseController::class, 'printThermal'])->name('pembelian.thermal');  

        // Data Pemasok (Supplier)
        Route::get('/pemasok/{pemasok}/json', [SupplierController::class, 'getjson'])->name('pemasok.getjson');  
        Route::resource('pemasok', SupplierController::class)->except('show', 'create', 'edit');  


        // ---------------------------------------------------------
        // 5. DATA PENGAMBILAN (AJAX / API Endpoints)
        // ---------------------------------------------------------
        Route::prefix('get-data')->as('get-data.')->group(function () {
            Route::get('produk', [ProductController::class, 'getData'])->name('produk');  
            Route::get('cek-stok-produk', [ProductController::class, 'cekStock'])->name('cek-stok');  
            Route::get('produk-by-barcode/{barcode}', [ProductController::class, 'getByBarcode'])->name('produk.by-barcode');  
            Route::get('serial-product-info/{produk}', [SerialNumberController::class, 'getProductInfoForSerial'])->name('serial-product-info');  
            
            // Pengambilan Data Notifikasi
            Route::get('low-stock-notifications', [ProductController::class, 'getLowStockNotifications'])->name('notifications.low-stock');  
            Route::get('notifications/unregistered-serials', [ProductController::class, 'getUnregisteredSerialNotifications'])->name('notifications.unregistered-serials');  
        });

        

    });
});