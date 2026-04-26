<?php

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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('stok-opname', [StockTakeController::class, 'index'])->name('stok-opname.index');
    Route::post('stok-opname', [StockTakeController::class, 'store'])->name('stok-opname.store');
    Route::get('stok-opname/history', [StockTakeController::class, 'history'])->name('stok-opname.history');
    Route::get('stok-opname/history/{stok_opname}', [StockTakeController::class, 'show'])->name('stok-opname.show');
    Route::resource('stok-penyesuaian', StockAdjustmentController::class)->except(['edit', 'update']);

    Route::prefix('get-data')->as('get-data.')->group(function () {
        Route::get('produk', [ProductController::class, 'getData'])->name('produk');
        Route::get('cek-stok-produk', [ProductController::class, 'cekStock'])->name('cek-stok');
        Route::get('produk-by-barcode/{barcode}', [ProductController::class, 'getByBarcode'])->name('produk.by-barcode');
        Route::get('low-stock-notifications', [ProductController::class, 'getLowStockNotifications'])->name('notifications.low-stock');
        Route::get('notifications/unregistered-serials', [ProductController::class, 'getUnregisteredSerialNotifications'])->name('notifications.unregistered-serials');
        // Tambahkan ini di dalam grup route yang memerlukan autentikasi
        Route::get('serial-product-info/{produk}', [SerialNumberController::class, 'getProductInfoForSerial'])->name('serial-product-info');
    });

    // Rute untuk halaman "Semua Notifikasi"
    Route::get('/notifications/all', [ProductController::class, 'allNotifications'])->name('notifications.all');

    // Grup Rute Product
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::post('upload', [ProductController::class, 'upload'])->name('upload');
        Route::delete('revert', [ProductController::class, 'revert'])->name('revert');
        Route::get('checkSlug', [ProductController::class, 'checkSlug'])->name('checkSlug');
    });
    Route::resource('produk', ProductController::class)->parameter('produk', 'produk:slug');

    //serial-number
    Route::get('serialNumber/{produk_slug?}', [SerialNumberController::class, 'index'])->name('serialNumber.index');
    Route::resource('serialNumber', SerialNumberController::class)->except(['show', 'index']);
    Route::get('serialNumber/get-by-product/{product_id}', [SerialNumberController::class, 'getByProduct'])->name('serialNumber.getByProduct');

    //kategori produk
    Route::get('/kategoriproduk/{kategoriproduk}/json', [CategoryController::class, 'getKategoriJson'])->name('kategoriproduk.getjson');
    Route::resource('kategoriproduk', CategoryController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/kategoriproduk/chekSlug', [CategoryController::class, 'chekSlug']);
    Route::post('/dashboard/kategoriproduk/upload', [CategoryController::class, 'upload'])->name('kategoriproduk.upload');
    Route::delete('/dashboard/kategoriproduk/revert', [CategoryController::class, 'revert'])->name('kategoriproduk.revert');

    //brand
    Route::get('/brand/{brand}/json', [BrandController::class, 'getBrandJson'])->name('brand.getjson');
    Route::resource('brand', BrandController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/brand/chekSlug', [BrandController::class, 'chekSlug']);
    Route::post('/dashboard/brand/upload', [BrandController::class, 'upload'])->name('brand.upload');
    Route::delete('/dashboard/brand/revert', [BrandController::class, 'revert'])->name('brand.revert');

    //unit
    Route::get('/unit/{unit}/json', [UnitController::class, 'getUnitJson'])->name('unit.getjson');
    Route::resource('unit', UnitController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/unit/chekSlug', [UnitController::class, 'chekSlug']);

    //garansi
    Route::get('/garansi/{garansi}/json', [WarrantieController::class, 'getWarrantieJson'])->name('garansi.getjson');
    Route::resource('garansi', WarrantieController::class)->except('show', 'create', 'edit');
    Route::get('/garansi/{garansi:slug}/json', [WarrantieController::class, 'getWarrantieJson'])->name('garansi.getjson');
    Route::resource('garansi', WarrantieController::class)->except('show', 'create', 'edit')->parameters(['garansi' => 'garansi:slug']);
    Route::get('/dashboard/garansi/chekSlug', [WarrantieController::class, 'chekSlug']);

    //Stock
    Route::get('/stok/rendah', [ProductController::class, 'laporanStockRendah'])->name('stok.rendah');

    // Purchase & Supplier
    Route::resource('/pembelian', PurchaseController::class)->parameter('pembelian', 'pembelian:referensi');
    Route::get('/pembelian/{pembelian:referensi}/pdf', [PurchaseController::class, 'generatePdf'])->name('pembelian.pdf');
    Route::get('/pembelian/{pembelian:referensi}/thermal', [PurchaseController::class, 'printThermal'])->name('pembelian.thermal');

    Route::get('/pemasok/{pemasok}/json', [SupplierController::class, 'getjson'])->name('pemasok.getjson');
    Route::resource('pemasok', SupplierController::class)->except('show', 'create', 'edit');
});
