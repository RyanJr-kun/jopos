<?php

use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\EcommerceController;
use Modules\Ecommerce\Http\Controllers\event\BannerController;
use Modules\Ecommerce\Http\Controllers\event\PromotionController;
use Modules\Ecommerce\Http\Controllers\MarketController;


// Rute untuk Web Market (Publik)
Route::get('/', [MarketController::class, 'index']);
Route::get('/market/produk', [MarketController::class, 'produk'])->name('market.produk');
Route::get('/market/produk/{slug}', [MarketController::class, 'produkDetail'])->name('market.produk.detail');
Route::get('/market/layanan', [MarketController::class, 'layanan'])->name('market.layanan');
Route::get('/market/tentang', [MarketController::class, 'tentang'])->name('market.tentang');
Route::get('/market/live-search', [MarketController::class, 'liveSearch'])->name('market.liveSearch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('ecommerces', EcommerceController::class)->names('ecommerce');

    Route::resource('promo', PromotionController::class);
    Route::post('promo/validate-code', [PromotionController::class, 'validateCode'])->name('promo.validateCode');
    Route::patch('/promo/{promo}/update-status/', [PromotionController::class, 'updateStatus'])->name('promo.updateStatus');

    // Banner
    Route::get('/banner/{banner}/json', [BannerController::class, 'getJson'])->name('banner.getjson');
    Route::post('/banner/upload', [BannerController::class, 'upload'])->name('banner.upload');
    Route::delete('/banner/revert', [BannerController::class, 'revert'])->name('banner.revert');
    Route::resource('banner', BannerController::class)->except(['show', 'create', 'edit']);
});
