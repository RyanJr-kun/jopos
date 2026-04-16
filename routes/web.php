<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\StoreSettingController;
use App\Http\Controllers\auth\UserController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\event\BannerController;
use App\Http\Controllers\event\PromotionController;
use App\Http\Controllers\inventaris\StockTakeController;
use App\Http\Controllers\inventaris\StockAdjustmentController;
use App\Http\Controllers\master\BrandController;
use App\Http\Controllers\master\WarrantieController;
use App\Http\Controllers\master\CategoryController;
use App\Http\Controllers\master\TransactionCategoryController;
use App\Http\Controllers\master\CustomerController;
use App\Http\Controllers\master\SupplierController;
use App\Http\Controllers\master\SerialNumberController;
use App\Http\Controllers\master\UnitController;
use App\Http\Controllers\laporan\KeuanganController;
use App\Http\Controllers\laporan\LaporanController;
use App\Http\Controllers\laporan\IncomeController;
use App\Http\Controllers\laporan\ExpenseController;
use App\Http\Controllers\mainhero\PurchaseController;
use App\Http\Controllers\mainhero\SaleController;
use App\Http\Controllers\produk\ProductController;
use App\Http\Controllers\publik\MarketController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
});

// Rute untuk Web Market (Publik)
Route::get('/', [MarketController::class, 'index']);
Route::get('/market/produk', [MarketController::class, 'produk'])->name('market.produk');
Route::get('/market/produk/{slug}', [MarketController::class, 'produkDetail'])->name('market.produk.detail');
Route::get('/market/layanan', [MarketController::class, 'layanan'])->name('market.layanan');
Route::get('/market/tentang', [MarketController::class, 'tentang'])->name('market.tentang');
Route::get('/market/live-search', [MarketController::class, 'liveSearch'])->name('market.liveSearch');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    Route::get('keuangan', [KeuanganController::class, 'index'])->name('keuangan');
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

    //transaksi penjualan
    Route::get('/penjualan/history/today', [SaleController::class, 'getTodayHistory'])->name('penjualan.history.today');
    Route::get('/penjualan/get-products', [SaleController::class, 'getProductsForCashier'])->name('penjualan.get-products');
    Route::resource('/penjualan', SaleController::class)->except('destroy');
    Route::get('/penjualan/{penjualan}/json', [SaleController::class, 'getjson'])->name('penjualan.getjson');
    Route::get('/pelanggan/{pelanggan}/json', [CustomerController::class, 'getjson'])->name('pelanggan.getjson');
    Route::get('/penjualan/{penjualan:referensi}/thermal', [SaleController::class, 'printThermal'])->name('penjualan.thermal');
    Route::get('/penjualan/{penjualan:referensi}/pdf', [SaleController::class, 'generatePdf'])->name('penjualan.pdf');
    Route::resource('pelanggan', CustomerController::class)->except('show', 'create', 'edit');

    //expense.
    Route::get('/expense/{expense:referensi}/json', [ExpenseController::class, 'getjson'])->name('expense.getjson');
    Route::resource('expense', ExpenseController::class)->except('show', 'create', 'edit')->parameter('expense', 'expense:referensi');

    //income
    Route::get('/income/{income:referensi}/json', [IncomeController::class, 'getjson'])->name('income.getjson');
    Route::resource('income', IncomeController::class)->except('show', 'create', 'edit')->parameter('income', 'income:referensi');

    // kategori transaksi
    Route::get('/kategoritransaksi/{kategoritransaksi}/json', [TransactionCategoryController::class, 'getKategoriJson'])->name('kategoritransaksi.getjson');
    Route::resource('kategoritransaksi', TransactionCategoryController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/kategoritransaksi/chekSlug', [TransactionCategoryController::class, 'chekSlug']);

    //Stock
    Route::get('/stok/rendah', [ProductController::class, 'laporanStockRendah'])->name('stok.rendah');

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('inventaris', [LaporanController::class, 'inventaris'])->name('inventaris');
        Route::get('inventaris/export', [LaporanController::class, 'exportInventaris'])->name('inventaris.export');
        Route::get('pembelian', [LaporanController::class, 'pembelian'])->name('pembelian');
        Route::get('pembelian/export', [LaporanController::class, 'exportPurchase'])->name('pembelian.export');
        Route::get('penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('penjualan/export', [LaporanController::class, 'exportSale'])->name('penjualan.export');
        Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('laba-rugi/export', [LaporanController::class, 'exportLabaRugi'])->name('laba-rugi.export');
    });

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('profil-toko', [StoreSettingController::class, 'edit'])->name('profil-toko.edit');
        Route::put('profil-toko', [StoreSettingController::class, 'update'])->name('profil-toko.update');
        Route::post('profil-toko/upload', [StoreSettingController::class, 'upload'])->name('profil-toko.upload');
        Route::delete('profil-toko/revert', [StoreSettingController::class, 'revert'])->name('profil-toko.revert');
    });
    Route::resource('promo', PromotionController::class);
    Route::post('promo/validate-code', [PromotionController::class, 'validateCode'])->name('promo.validateCode');
    Route::patch('/promo/{promo}/update-status/', [PromotionController::class, 'updateStatus'])->name('promo.updateStatus');

    // Banner
    Route::get('/banner/{banner}/json', [BannerController::class, 'getJson'])->name('banner.getjson');
    Route::post('/banner/upload', [BannerController::class, 'upload'])->name('banner.upload');
    Route::delete('/banner/revert', [BannerController::class, 'revert'])->name('banner.revert');
    Route::resource('banner', BannerController::class)->except(['show', 'create', 'edit']);
});

Route::middleware(['role:admin', 'auth'])->group(function () {
    // Purchase & Supplier
    Route::resource('/pembelian', PurchaseController::class)->parameter('pembelian', 'pembelian:referensi');
    Route::get('/pembelian/{pembelian:referensi}/pdf', [PurchaseController::class, 'generatePdf'])->name('pembelian.pdf');
    Route::get('/pembelian/{pembelian:referensi}/thermal', [PurchaseController::class, 'printThermal'])->name('pembelian.thermal');

    Route::get('/pemasok/{pemasok}/json', [SupplierController::class, 'getjson'])->name('pemasok.getjson');
    Route::resource('pemasok', SupplierController::class)->except('show', 'create', 'edit');

    //users
    Route::resource('users', UserController::class)->except('show')->parameter('user', 'user:username');
    Route::post('/dashboard/users/upload', [UserController::class, 'upload'])->name('users.upload');
    Route::delete('/dashboard/users/revert', [UserController::class, 'revert'])->name('users.revert');
});
