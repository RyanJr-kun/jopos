<?php

use Modules\Ecommerce\Http\Controllers\auth\CustomerAuthController;
use Modules\Ecommerce\Http\Controllers\auth\GoogleController;
use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\EcommerceController;
use Modules\Ecommerce\Http\Controllers\event\BannerController;
use Modules\Ecommerce\Http\Controllers\event\PromotionController;
use Modules\Ecommerce\Http\Controllers\MarketController;
use Modules\Ecommerce\Http\Controllers\artikel\ArtikelController;

// Ambil domain utama dari file .env
$domain = env('APP_DOMAIN', 'jocomputer.com');

// =========================================================
// 1. ROUTING DOMAIN UTAMA (Khusus Market / Publik / Customer)
// =========================================================
Route::domain($domain)->group(function () {

    Route::middleware('guest')->group(function () {
        // Autentikasi Standar
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/auth/customers/login', [CustomerAuthController::class, 'login'])->name('customer.login.post');

        Route::get('/auth/customers/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
        Route::post('/auth/customers/register', [CustomerAuthController::class, 'register'])->name('customer.register.post');

        // Rute Lupa Password Customer
        Route::get('/auth/customers/forgot-password', [CustomerAuthController::class, 'showForgotForm'])->name('customer.password.request');
        Route::post('/auth/customers/forgot-password', [CustomerAuthController::class, 'sendResetLink'])->name('customer.password.email');

        // Rute Reset Password Customer (dari link email)
        Route::get('/auth/customers/reset-password/{token}', [CustomerAuthController::class, 'showResetForm'])->name('customer.password.reset');
        Route::post('/auth/customers/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer.password.update');

        // Autentikasi via Google OAuth
        Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('customer.google.login');
        Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('/auth/customers/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    });

    // Rute untuk Web Market (Publik)[cite: 6]
    Route::get('/', [MarketController::class, 'index'])->name('market.home');
    Route::get('/market/produk', [MarketController::class, 'produk'])->name('market.produk');
    Route::get('/market/produk/{slug}', [MarketController::class, 'produkDetail'])->name('market.produk.detail');
    Route::get('/market/layanan', [MarketController::class, 'layanan'])->name('market.layanan');
    Route::get('/market/tentang', [MarketController::class, 'tentang'])->name('market.tentang');
    Route::get('/market/live-search', [MarketController::class, 'liveSearch'])->name('market.liveSearch');
});


// =========================================================
// 2. ROUTING SUBDOMAIN (JOPOS - Khusus Manajemen oleh Admin)
// =========================================================
Route::domain(env('POS_DOMAIN'))->group(function () {

    // Rute ini hanya bisa diakses oleh Karyawan/Admin yang sudah login[cite: 6]
    Route::middleware(['auth', 'verified', 'employee'])->group(function () {

        Route::resource('ecommerces', EcommerceController::class)->names('ecommerce');

        // Manajemen Artikel / Blog
        Route::resource('artikel', ArtikelController::class);

        // Manajemen Promo[cite: 6]
        Route::resource('promo', PromotionController::class);
        Route::post('promo/validate-code', [PromotionController::class, 'validateCode'])->name('promo.validateCode');
        Route::patch('/promo/{promo}/update-status/', [PromotionController::class, 'updateStatus'])->name('promo.updateStatus');

        // Manajemen Banner[cite: 6]
        Route::get('/banner/{banner}/json', [BannerController::class, 'getJson'])->name('banner.getjson');
        Route::post('/banner/upload', [BannerController::class, 'upload'])->name('banner.upload');
        Route::delete('/banner/revert', [BannerController::class, 'revert'])->name('banner.revert');
        Route::resource('banner', BannerController::class)->except(['show', 'create', 'edit']);
    });
});
