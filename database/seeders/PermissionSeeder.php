<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions bawaan spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =============================================
        // DAFTAR PERMISSION BERDASARKAN FITUR APLIKASI
        // =============================================
        $permissions = [

            // ------------------------------------------
            // Dashboard
            // ------------------------------------------
            'view-dashboard',

            // ------------------------------------------
            // Penjualan (Sales)
            // ------------------------------------------
            'view-penjualan',       // Faktur Sale - list
            'create-penjualan',     // Kasir - buat transaksi baru
            'edit-penjualan',       // Update faktur
            'print-penjualan',      // Cetak thermal / PDF

            // ------------------------------------------
            // Pelanggan (Customer)
            // ------------------------------------------
            'view-pelanggan',
            'create-pelanggan',
            'edit-pelanggan',
            'delete-pelanggan',

            // ------------------------------------------
            // Pembelian (Purchase)
            // ------------------------------------------
            'view-pembelian',       // Faktur Purchase - list
            'create-pembelian',     // Beli Product
            'edit-pembelian',
            'delete-pembelian',
            'print-pembelian',      // Cetak thermal / PDF

            // ------------------------------------------
            // Pemasok (Supplier)
            // ------------------------------------------
            'view-pemasok',
            'create-pemasok',
            'edit-pemasok',
            'delete-pemasok',

            // ------------------------------------------
            // Produk (Product)
            // ------------------------------------------
            'view-produk',
            'create-produk',
            'edit-produk',
            'delete-produk',

            // ------------------------------------------
            // Kategori Produk
            // ------------------------------------------
            'view-kategoriproduk',
            'create-kategoriproduk',
            'edit-kategoriproduk',
            'delete-kategoriproduk',

            // ------------------------------------------
            // Brand
            // ------------------------------------------
            'view-brand',
            'create-brand',
            'edit-brand',
            'delete-brand',

            // ------------------------------------------
            // Satuan (Unit)
            // ------------------------------------------
            'view-unit',
            'create-unit',
            'edit-unit',
            'delete-unit',

            // ------------------------------------------
            // Garansi (Warranties)
            // ------------------------------------------
            'view-garansi',
            'create-garansi',
            'edit-garansi',
            'delete-garansi',

            // ------------------------------------------
            // Serial Number
            // ------------------------------------------
            'view-serialnumber',
            'create-serialnumber',
            'edit-serialnumber',
            'delete-serialnumber',

            // ------------------------------------------
            // Inventaris - Penyesuaian Stock
            // ------------------------------------------
            'view-stok-penyesuaian',
            'create-stok-penyesuaian',
            'delete-stok-penyesuaian',

            // ------------------------------------------
            // Inventaris - Stock Opname
            // ------------------------------------------
            'view-stok-opname',        // Riwayat & detail
            'create-stok-opname',      // Buat Data Baru

            // ------------------------------------------
            // Inventaris - Stock Rendah
            // ------------------------------------------
            'view-stok-rendah',

            // ------------------------------------------
            // Promo & Diskon
            // ------------------------------------------
            'view-promo',
            'create-promo',
            'edit-promo',
            'delete-promo',

            // ------------------------------------------
            // Banner
            // ------------------------------------------
            'view-banner',
            'create-banner',
            'edit-banner',
            'delete-banner',

            // ------------------------------------------
            // Keuangan - Rekap Keuangan
            // ------------------------------------------
            'view-keuangan',

            // ------------------------------------------
            // Keuangan - Pemasukan (Income)
            // ------------------------------------------
            'view-income',
            'create-income',
            'edit-income',
            'delete-income',

            // ------------------------------------------
            // Keuangan - Pengeluaran (Expense)
            // ------------------------------------------
            'view-expense',
            'create-expense',
            'edit-expense',
            'delete-expense',

            // ------------------------------------------
            // Kategori Transaksi
            // ------------------------------------------
            'view-kategoritransaksi',
            'create-kategoritransaksi',
            'edit-kategoritransaksi',
            'delete-kategoritransaksi',

            // ------------------------------------------
            // Laporan
            // ------------------------------------------
            'view-laporan-inventaris',      // Pergerakan Stock
            'export-laporan-inventaris',
            'view-laporan-penjualan',       // Laporan Sale
            'export-laporan-penjualan',
            'view-laporan-pembelian',       // Laporan Purchase
            'export-laporan-pembelian',
            'view-laporan-laba-rugi',       // Laporan Laba & Rugi
            'export-laporan-laba-rugi',

            // ------------------------------------------
            // Pengaturan Toko (Store Settings)
            // ------------------------------------------
            'view-pengaturan',
            'edit-pengaturan',

            // ------------------------------------------
            // Pengguna (Users)
            // ------------------------------------------
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // ------------------------------------------
            // Role & Permission
            // ------------------------------------------
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
        ];

        // Insert semua permission ke database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // =============================================
        // SETUP ROLES
        // =============================================

        // --- Super Admin: akses penuh ke semua fitur ---
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // --- Admin: akses penuh kecuali manajemen user & role ---
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view-dashboard',

            // Penjualan
            'view-penjualan', 'create-penjualan', 'edit-penjualan', 'print-penjualan',

            // Pelanggan
            'view-pelanggan', 'create-pelanggan', 'edit-pelanggan', 'delete-pelanggan',

            // Pembelian
            'view-pembelian', 'create-pembelian', 'edit-pembelian', 'delete-pembelian', 'print-pembelian',

            // Pemasok
            'view-pemasok', 'create-pemasok', 'edit-pemasok', 'delete-pemasok',

            // Produk
            'view-produk', 'create-produk', 'edit-produk', 'delete-produk',

            // Kategori Produk
            'view-kategoriproduk', 'create-kategoriproduk', 'edit-kategoriproduk', 'delete-kategoriproduk',

            // Brand
            'view-brand', 'create-brand', 'edit-brand', 'delete-brand',

            // Unit
            'view-unit', 'create-unit', 'edit-unit', 'delete-unit',

            // Garansi
            'view-garansi', 'create-garansi', 'edit-garansi', 'delete-garansi',

            // Serial Number
            'view-serialnumber', 'create-serialnumber', 'edit-serialnumber', 'delete-serialnumber',

            // Inventaris
            'view-stok-penyesuaian', 'create-stok-penyesuaian', 'delete-stok-penyesuaian',
            'view-stok-opname', 'create-stok-opname',
            'view-stok-rendah',

            // Promo & Banner
            'view-promo', 'create-promo', 'edit-promo', 'delete-promo',
            'view-banner', 'create-banner', 'edit-banner', 'delete-banner',

            // Keuangan
            'view-keuangan',
            'view-income', 'create-income', 'edit-income', 'delete-income',
            'view-expense', 'create-expense', 'edit-expense', 'delete-expense',
            'view-kategoritransaksi', 'create-kategoritransaksi', 'edit-kategoritransaksi', 'delete-kategoritransaksi',

            // Laporan
            'view-laporan-inventaris', 'export-laporan-inventaris',
            'view-laporan-penjualan', 'export-laporan-penjualan',
            'view-laporan-pembelian', 'export-laporan-pembelian',
            'view-laporan-laba-rugi', 'export-laporan-laba-rugi',

            // Pengaturan
            'view-pengaturan', 'edit-pengaturan',

            // User & Role management
            'view-users', 'create-users', 'edit-users', 'delete-users',
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
        ]);

        // --- Kasir: hanya bisa akses kasir, penjualan, pelanggan ---
        $kasir = Role::firstOrCreate(['name' => 'Kasir', 'guard_name' => 'web']);
        $kasir->syncPermissions([
            'view-dashboard',

            // Penjualan & Kasir
            'view-penjualan', 'create-penjualan', 'print-penjualan',

            // Pelanggan (view & create saja)
            'view-pelanggan', 'create-pelanggan',

            // Produk (view saja untuk keperluan kasir)
            'view-produk',

            // Stock rendah (notifikasi)
            'view-stok-rendah',
        ]);

        // --- Gudang: akses ke inventaris, produk, pembelian ---
        $gudang = Role::firstOrCreate(['name' => 'Gudang', 'guard_name' => 'web']);
        $gudang->syncPermissions([
            'view-dashboard',

            // Produk
            'view-produk', 'create-produk', 'edit-produk',

            // Kategori Produk
            'view-kategoriproduk', 'create-kategoriproduk', 'edit-kategoriproduk',

            // Brand, Unit, Garansi
            'view-brand', 'view-unit', 'view-garansi',

            // Serial Number
            'view-serialnumber', 'create-serialnumber', 'edit-serialnumber',

            // Pembelian (input & lihat)
            'view-pembelian', 'create-pembelian',

            // Inventaris
            'view-stok-penyesuaian', 'create-stok-penyesuaian',
            'view-stok-opname', 'create-stok-opname',
            'view-stok-rendah',
        ]);
    }
}