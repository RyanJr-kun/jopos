<?php

use App\Http\Controllers\authentications\AuthController;
use App\Http\Controllers\authentications\ProfilTokoController;
use App\Http\Controllers\authentications\UserController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\event\BannerController;
use App\Http\Controllers\event\PromoController;
use App\Http\Controllers\inventaris\StokOpnameController;
use App\Http\Controllers\inventaris\StokPenyesuaianController;
use App\Http\Controllers\master\BrandController;
use App\Http\Controllers\master\GaransiController;
use App\Http\Controllers\master\KategoriProdukController;
use App\Http\Controllers\master\KategoriTransaksiController;
use App\Http\Controllers\master\PelangganController;
use App\Http\Controllers\master\PemasokController;
use App\Http\Controllers\master\SerialNumberController;
use App\Http\Controllers\master\UnitController;
use App\Http\Controllers\laporan\KeuanganController;
use App\Http\Controllers\laporan\LaporanController;
use App\Http\Controllers\laporan\PemasukanController;
use App\Http\Controllers\laporan\PengeluaranController;
use App\Http\Controllers\mainhero\PembelianController;
use App\Http\Controllers\mainhero\PenjualanController;
use App\Http\Controllers\produk\ProdukController;
use App\Http\Controllers\publik\MarketController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/auht/login', [AuthController::class, 'login'])->name('login.post');
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
    Route::get('stok-opname', [StokOpnameController::class, 'index'])->name('stok-opname.index');
    Route::post('stok-opname', [StokOpnameController::class, 'store'])->name('stok-opname.store');
    Route::get('stok-opname/history', [StokOpnameController::class, 'history'])->name('stok-opname.history');
    Route::get('stok-opname/history/{stok_opname}', [StokOpnameController::class, 'show'])->name('stok-opname.show');

    Route::resource('stok-penyesuaian', StokPenyesuaianController::class)->except(['edit', 'update']);

    Route::prefix('get-data')->as('get-data.')->group(function () {
        Route::get('produk', [ProdukController::class, 'getData'])->name('produk');
        Route::get('cek-stok-produk', [ProdukController::class, 'cekStok'])->name('cek-stok');
        Route::get('produk-by-barcode/{barcode}', [ProdukController::class, 'getByBarcode'])->name('produk.by-barcode');
        Route::get('low-stock-notifications', [ProdukController::class, 'getLowStockNotifications'])->name('notifications.low-stock');
        Route::get('notifications/unregistered-serials', [ProdukController::class, 'getUnregisteredSerialNotifications'])->name('notifications.unregistered-serials');
        // Tambahkan ini di dalam grup route yang memerlukan autentikasi
        Route::get('serial-product-info/{produk}', [SerialNumberController::class, 'getProductInfoForSerial'])->name('serial-product-info');
    });

    // Rute untuk halaman "Semua Notifikasi"
    Route::get('/notifications/all', [ProdukController::class, 'allNotifications'])->name('notifications.all');

    // Grup Rute Produk
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::post('upload', [ProdukController::class, 'upload'])->name('upload');
        Route::delete('revert', [ProdukController::class, 'revert'])->name('revert');
        Route::get('checkSlug', [ProdukController::class, 'checkSlug'])->name('checkSlug');
    });
    Route::resource('produk', ProdukController::class)->parameter('produk', 'produk:slug');

    //serial-number
    Route::get('serialNumber/{produk_slug?}', [SerialNumberController::class, 'index'])->name('serialNumber.index');
    Route::resource('serialNumber', SerialNumberController::class)->except(['show', 'index']);
    Route::get('serialNumber/get-by-product/{produk_id}', [SerialNumberController::class, 'getByProduct'])->name('serialNumber.getByProduct');

    //kategori produk
    Route::get('/kategoriproduk/{kategoriproduk}/json', [KategoriProdukController::class, 'getKategoriJson'])->name('kategoriproduk.getjson');
    Route::resource('kategoriproduk', KategoriProdukController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/kategoriproduk/chekSlug', [KategoriProdukController::class, 'chekSlug']);
    Route::post('/dashboard/kategoriproduk/upload', [KategoriProdukController::class, 'upload'])->name('kategoriproduk.upload');
    Route::delete('/dashboard/kategoriproduk/revert', [KategoriProdukController::class, 'revert'])->name('kategoriproduk.revert');

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
    Route::get('/garansi/{garansi}/json', [GaransiController::class, 'getGaransiJson'])->name('garansi.getjson');
    Route::resource('garansi', GaransiController::class)->except('show', 'create', 'edit');
    Route::get('/garansi/{garansi:slug}/json', [GaransiController::class, 'getGaransiJson'])->name('garansi.getjson');
    Route::resource('garansi', GaransiController::class)->except('show', 'create', 'edit')->parameters(['garansi' => 'garansi:slug']);
    Route::get('/dashboard/garansi/chekSlug', [GaransiController::class, 'chekSlug']);

    //transaksi penjualan
    Route::get('/penjualan/history/today', [PenjualanController::class, 'getTodayHistory'])->name('penjualan.history.today');
    Route::get('/penjualan/get-products', [PenjualanController::class, 'getProductsForCashier'])->name('penjualan.get-products');
    Route::resource('/penjualan', PenjualanController::class);
    Route::get('/penjualan/{penjualan}/json', [PenjualanController::class, 'getjson'])->name('penjualan.getjson');
    Route::get('/pelanggan/{pelanggan}/json', [PelangganController::class, 'getjson'])->name('pelanggan.getjson');
    Route::get('/penjualan/{penjualan:referensi}/thermal', [PenjualanController::class, 'printThermal'])->name('penjualan.thermal');
    Route::get('/penjualan/{penjualan:referensi}/pdf', [PenjualanController::class, 'generatePdf'])->name('penjualan.pdf');
    Route::resource('pelanggan', PelangganController::class)->except('show', 'create', 'edit');

    //pengeluaran.
    Route::get('/pengeluaran/{pengeluaran:referensi}/json', [PengeluaranController::class, 'getjson'])->name('pengeluaran.getjson');
    Route::resource('pengeluaran', PengeluaranController::class)->except('show', 'create', 'edit')->parameter('pengeluaran', 'pengeluaran:referensi');

    //pemasukan
    Route::get('/pemasukan/{pemasukan:referensi}/json', [PemasukanController::class, 'getjson'])->name('pemasukan.getjson');
    Route::resource('pemasukan', PemasukanController::class)->except('show', 'create', 'edit')->parameter('pemasukan', 'pemasukan:referensi');

    // kategori transaksi
    Route::get('/kategoritransaksi/{kategoritransaksi}/json', [KategoriTransaksiController::class, 'getKategoriJson'])->name('kategoritransaksi.getjson');
    Route::resource('kategoritransaksi', KategoriTransaksiController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/kategoritransaksi/chekSlug', [KategoriTransaksiController::class, 'chekSlug']);

    //Stok
    Route::get('/stok/rendah', [ProdukController::class, 'laporanStokRendah'])->name('stok.rendah');

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('inventaris', [LaporanController::class, 'inventaris'])->name('inventaris');
        Route::get('inventaris/export', [LaporanController::class, 'exportInventaris'])->name('inventaris.export');
        Route::get('pembelian', [LaporanController::class, 'pembelian'])->name('pembelian');
        Route::get('pembelian/export', [LaporanController::class, 'exportPembelian'])->name('pembelian.export');
        Route::get('penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('penjualan/export', [LaporanController::class, 'exportPenjualan'])->name('penjualan.export');
        Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('laba-rugi/export', [LaporanController::class, 'exportLabaRugi'])->name('laba-rugi.export');
    });

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('profil-toko', [ProfilTokoController::class, 'edit'])->name('profil-toko.edit');
        Route::put('profil-toko', [ProfilTokoController::class, 'update'])->name('profil-toko.update');
        Route::post('profil-toko/upload', [ProfilTokoController::class, 'upload'])->name('profil-toko.upload');
        Route::delete('profil-toko/revert', [ProfilTokoController::class, 'revert'])->name('profil-toko.revert');
    });
    Route::resource('promo', PromoController::class);
    Route::post('promo/validate-code', [PromoController::class, 'validateCode'])->name('promo.validateCode');
    Route::patch('/promo/{promo}/update-status/', [PromoController::class, 'updateStatus'])->name('promo.updateStatus');

    // Banner
    Route::get('/banner/{banner}/json', [BannerController::class, 'getJson'])->name('banner.getjson');
    Route::post('/banner/upload', [BannerController::class, 'upload'])->name('banner.upload');
    Route::delete('/banner/revert', [BannerController::class, 'revert'])->name('banner.revert');
    Route::resource('banner', BannerController::class)->except(['show', 'create', 'edit']);
});

Route::middleware(['admin', 'auth'])->group(function () {
    // Pembelian & Pemasok
    Route::resource('/pembelian', PembelianController::class)->parameter('pembelian', 'pembelian:referensi');
    Route::get('/pembelian/{pembelian:referensi}/pdf', [PembelianController::class, 'generatePdf'])->name('pembelian.pdf');
    Route::get('/pembelian/{pembelian:referensi}/thermal', [PembelianController::class, 'printThermal'])->name('pembelian.thermal');

    Route::get('/pemasok/{pemasok}/json', [PemasokController::class, 'getjson'])->name('pemasok.getjson');
    Route::resource('pemasok', PemasokController::class)->except('show', 'create', 'edit');

    //users
    Route::resource('users', UserController::class)->except('show')->parameter('user', 'user:username');
    Route::post('/dashboard/users/upload', [UserController::class, 'upload'])->name('users.upload');
    Route::delete('/dashboard/users/revert', [UserController::class, 'revert'])->name('users.revert');
});
