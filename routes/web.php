<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\RoleController;
use App\Http\Controllers\dashboard\DashboardController;
use App\Http\Controllers\dashboard\LaporanController;
use App\Http\Controllers\dashboard\StoreController;
use App\Http\Controllers\finance\CashFlowController;
use App\Http\Controllers\finance\KeuanganController;
use App\Http\Controllers\finance\TransactionCategoryController;
use App\Http\Controllers\hrd\CustomerController;
use App\Http\Controllers\hrd\UserController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// =========================================================
// 1. ROUTING SUBDOMAIN (JOPOS - Khusus Karyawan/Admin)
// =========================================================
Route::domain(env('POS_DOMAIN'))->group(function () {
  // Akses Tamu (Belum Login)
  Route::middleware('guest')->group(function () {
    // Path URL diubah sesuai target Anda
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('employee.login');
    Route::post('/auth/employees/login', [AuthController::class, 'login'])->name('employee.login.post');

    // Tambahkan ini untuk fitur Lupa Password
    Route::get('/auth/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/auth/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/auth/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/auth/reset-password', [AuthController::class, 'reset'])->name('password.update');
  });

  // Akses Karyawan (Sudah Login)
  Route::middleware(['auth', 'verified', 'employee'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('employee.logout');

    // Dashboard & Fitur Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])
      ->name('dashboard')
      ->middleware('permission:view-dashboard');

    Route::get('keuangan', [KeuanganController::class, 'index'])
      ->name('keuangan')
      ->middleware('permission:view-keuangan');

    Route::prefix('keuangan')
      ->name('keuangan.')
      ->group(function () {
        Route::post('/mutasi', [KeuanganController::class, 'storeMutasi'])->name('mutasi.store');

        // Account CRUD
        Route::post('/account', [KeuanganController::class, 'storeBank'])->name('account.store');
        Route::put('/account/{account}', [KeuanganController::class, 'updateBank'])->name('account.update');
        Route::delete('/account/{account}', [KeuanganController::class, 'destroyBank'])->name('account.destroy');
      });

    Route::prefix('financial/cash-flows/{type}')
      ->name('financial.cash-flows.')
      ->group(function () {
        Route::get('/', [CashFlowController::class, 'index'])->name('index');
        Route::get('/create', [CashFlowController::class, 'create'])->name('create');
        Route::post('/', [CashFlowController::class, 'store'])->name('store');
        Route::get('/{cash_flow:referensi}/json', [CashFlowController::class, 'getjson'])->name('getjson');
        Route::get('/{cash_flow:referensi}/edit', [CashFlowController::class, 'edit'])->name('edit');
        Route::put('/{cash_flow:referensi}', [CashFlowController::class, 'update'])->name('update');
        Route::delete('/{cash_flow:referensi}', [CashFlowController::class, 'destroy'])->name('destroy');
      });

    // Kategori transaksi
    Route::get('/kategoritransaksi/{kategoritransaksi}/json', [TransactionCategoryController::class, 'getKategoriJson'])->name('kategoritransaksi.getjson');
    Route::resource('kategoritransaksi', TransactionCategoryController::class)->except('show', 'create', 'edit');
    Route::get('/dashboard/kategoritransaksi/chekSlug', [TransactionCategoryController::class, 'chekSlug']);

    // Laporan
    Route::prefix('laporan')
      ->name('laporan.')
      ->group(function () {
        Route::get('inventaris', [LaporanController::class, 'inventaris'])->name('inventaris');
        Route::get('inventaris/export', [LaporanController::class, 'exportInventaris'])->name('inventaris.export');
        Route::get('pembelian', [LaporanController::class, 'pembelian'])->name('pembelian');
        Route::get('pembelian/export', [LaporanController::class, 'exportPurchase'])->name('pembelian.export');
        Route::get('penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('penjualan/export', [LaporanController::class, 'exportSale'])->name('penjualan.export');
        Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('laba-rugi/export', [LaporanController::class, 'exportLabaRugi'])->name('laba-rugi.export');
      });

    // Toko
    Route::resource('toko', StoreController::class)->except('show', 'create', 'edit');
    Route::prefix('toko')
      ->name('toko.')
      ->group(function () {
        Route::post('upload', [StoreController::class, 'upload'])->name('upload');
        Route::delete('revert', [StoreController::class, 'revert'])->name('revert');
        Route::get('{toko}/members', [StoreController::class, 'getMembers'])->name('members');
        Route::post('{toko}/members/update', [StoreController::class, 'updateMembers'])->name('members-update');
      });

    // Pelanggan
    Route::get('/pelanggan/{pelanggan}/json', [CustomerController::class, 'getjson'])->name('pelanggan.getjson');
    Route::resource('pelanggan', CustomerController::class)->except('show', 'create', 'edit');

    // Users
    Route::resource('users', UserController::class)->except('show')->parameter('user', 'user:username');
    Route::post('/dashboard/users/upload', [UserController::class, 'upload'])->name('users.upload');
    Route::delete('/dashboard/users/revert', [UserController::class, 'revert'])->name('users.revert');

    // Roles
    Route::resource('roles', RoleController::class)->except('show');

    Route::get('/setting', function () {
      return redirect()->route('setting.index', Auth::user()->username);
    })->name('setting.redirect');

    Route::prefix('setting')
      ->name('setting.')
      ->group(function () {
        Route::get('/{username}', [SettingController::class, 'index'])->name('index');
        Route::put('/{username}/profile', [SettingController::class, 'updateProfile'])->name('profile.update');
        Route::put('/{username}/password', [SettingController::class, 'updatePassword'])->name('password.update');
        Route::put('/{username}/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
      });

    Route::get('/notifications/all', [SettingController::class, 'allNotifications'])->name('notifications.all');
  });
});
