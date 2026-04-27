<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\RoleController;
use App\Http\Controllers\hrd\UserController;
use App\Http\Controllers\hrd\CustomerController;
use App\Http\Controllers\dashboard\StoreController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\dashboard\LaporanController;
use App\Http\Controllers\finance\ExpenseController;
use App\Http\Controllers\finance\IncomeController;
use App\Http\Controllers\finance\KeuanganController;
use App\Http\Controllers\finance\TransactionCategoryController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    Route::get('keuangan', [KeuanganController::class, 'index'])->name('keuangan');



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
    Route::resource('toko', StoreController::class)->except('show', 'create', 'edit');
    Route::prefix('toko')->name('toko.')->group(function () {
        Route::post('toko/upload', [StoreController::class, 'upload'])->name('upload');
        Route::delete('toko/revert', [StoreController::class, 'revert'])->name('revert');
    });

    //pelanggan
    Route::get('/pelanggan/{pelanggan}/json', [CustomerController::class, 'getjson'])->name('pelanggan.getjson');
    Route::resource('pelanggan', CustomerController::class)->except('show', 'create', 'edit');

    //users
    Route::resource('users', UserController::class)->except('show')->parameter('user', 'user:username');
    Route::post('/dashboard/users/upload', [UserController::class, 'upload'])->name('users.upload');
    Route::delete('/dashboard/users/revert', [UserController::class, 'revert'])->name('users.revert');

    //roles
    Route::resource('roles', RoleController::class)->except('show');
});
