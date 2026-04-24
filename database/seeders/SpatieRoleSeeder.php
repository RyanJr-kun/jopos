<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions bawaan spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =========================================================
        // 2. DEFINISI MODUL DAN AKSI (MATRIX PERMISSIONS)
        // =========================================================
        // Format array: 'nama-modul' => ['daftar', 'aksi']
        // Dengan cara ini, menambah modul baru sangatlah mudah dan rapi.
        
        $modules = [
            'dashboard' => ['view'],

            // Modul Master & Produk
            'produk'         => ['view', 'create', 'edit', 'delete'],
            'kategoriproduk' => ['view', 'create', 'edit', 'delete'],
            'brand'          => ['view', 'create', 'edit', 'delete'],
            'unit'           => ['view', 'create', 'edit', 'delete'],
            'garansi'        => ['view', 'create', 'edit', 'delete'],
            'serialnumber'   => ['view', 'create', 'edit', 'delete'],
            'pemasok'        => ['view', 'create', 'edit', 'delete'],
            'pelanggan'      => ['view', 'create', 'edit', 'delete'],

            // Modul Transaksi
            'penjualan'      => ['view', 'create', 'edit', 'print'], // Sesuai desain awal Anda, tanpa delete
            'pembelian'      => ['view', 'create', 'edit', 'delete', 'print'],

            // Modul Inventaris
            'stok-penyesuaian' => ['view', 'create', 'delete'],
            'stok-opname'      => ['view', 'create'],
            'stok-rendah'      => ['view'],

            // Modul Marketing / Event
            'promo'          => ['view', 'create', 'edit', 'delete'],
            'banner'         => ['view', 'create', 'edit', 'delete'],

            // Modul Keuangan
            'keuangan'          => ['view'],
            'income'            => ['view', 'create', 'edit', 'delete'],
            'expense'           => ['view', 'create', 'edit', 'delete'],
            'kategoritransaksi' => ['view', 'create', 'edit', 'delete'],

            // Laporan
            'laporan-inventaris' => ['view', 'export'],
            'laporan-penjualan'  => ['view', 'export'],
            'laporan-pembelian'  => ['view', 'export'],
            'laporan-laba-rugi'  => ['view', 'export'],

            // Sistem & Pengaturan
            'pengaturan'     => ['view', 'edit'],
            'users'          => ['view', 'create', 'edit', 'delete'],
            'roles'          => ['view', 'create', 'edit', 'delete'],

            // =====================================================
            // 3 MODUL BARU ANDA
            // =====================================================
            'projek'      => ['view', 'create', 'edit', 'delete'],
            'kalender'    => ['view', 'create', 'edit', 'delete'],
            'toko-gudang' => ['view', 'create', 'edit', 'delete'],
        ];

        // 3. Insert Permissions secara Dinamis ke Database
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                // Menggabungkan aksi dan modul dengan strip (-), misal: "create-projek"
                $permissionName = "{$action}-{$module}"; 

                Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => 'web']
                );
            }
        }

        // =========================================================
        // 4. SETUP ROLES (HURUF KECIL KONSISTEN)
        // =========================================================
        // Menggunakan huruf kecil agar aman saat dipanggil di Middleware route Anda (role:admin)
        $admin   = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $kasir   = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $gudang  = Role::firstOrCreate(['name' => 'gudang', 'guard_name' => 'web']);
        $teknisi = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web']);
        $staff   = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        // 5. Assign Permissions ke Roles
        
        // Admin mendapat semua akses
        $admin->syncPermissions(Permission::all());

        // Kasir (Contoh)
        $kasir->syncPermissions([
            'view-dashboard',
            'view-penjualan', 'create-penjualan', 'print-penjualan',
            'view-pelanggan', 'create-pelanggan',
            'view-produk',
            'view-stok-rendah',
        ]);

        // Gudang (Contoh)
        $gudang->syncPermissions([
            'view-dashboard',
            'view-produk', 'create-produk', 'edit-produk',
            'view-kategoriproduk', 'create-kategoriproduk', 'edit-kategoriproduk',
            'view-brand', 'view-unit', 'view-garansi',
            'view-serialnumber', 'create-serialnumber', 'edit-serialnumber',
            'view-pembelian', 'create-pembelian',
            'view-stok-penyesuaian', 'create-stok-penyesuaian',
            'view-stok-opname', 'create-stok-opname',
            'view-stok-rendah',
            'view-toko-gudang', 'create-toko-gudang', 'edit-toko-gudang' // Modul baru
        ]);

        // =========================================================
        // 6. SETUP USER UNTUK LIVE PRODUCTION / HOSTING
        // =========================================================
        
        // Migrasi data role lama (jika ada) ke Spatie
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role_id == 1) {
                $user->assignRole('admin');
            } elseif ($user->role_id == 2) {
                $user->assignRole('kasir');
            }
        }

        // Memastikan User ID 1 selalu menjadi Super Admin / Admin
        $firstUser = User::find(1);
        if ($firstUser) {
            $firstUser->assignRole('admin');
        }

        $this->command->info('✅ Roles, Permissions, dan Assign User berhasil di-seeding! Modul Projek, Kalender, dan Toko & Gudang sudah masuk.');
    }
}