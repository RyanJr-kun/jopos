<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SpatieRoleSeeder extends Seeder
{
  public function run(): void
  {
    // =========================================================
    // 1. RESET CACHE
    // =========================================================
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // =========================================================
    // 2. DEFINISI MODUL DAN AKSI
    // =========================================================

    $modules = [
      // Umum
      'dashboard' => ['view'],

      // Master & Produk
      'produk' => ['view', 'create', 'edit', 'delete'],
      'kategoriproduk' => ['view', 'create', 'edit', 'delete'],
      'brand' => ['view', 'create', 'edit', 'delete'],
      'unit' => ['view', 'create', 'edit', 'delete'],
      'garansi' => ['view', 'create', 'edit', 'delete'],
      'serialnumber' => ['view', 'create', 'edit', 'delete'],
      'pemasok' => ['view', 'create', 'edit', 'delete'],
      'pelanggan' => ['view', 'create', 'edit', 'delete'],

      // Transaksi
      'penjualan' => ['view', 'create', 'edit'],
      'pembelian' => ['view', 'create', 'edit', 'delete'],

      // Inventaris
      'stok-penyesuaian' => ['view', 'create', 'delete'],
      'stok-opname' => ['view', 'create', 'delete'],
      'stok-rendah' => ['view'],
      'stok-transfer' => ['view', 'create', 'delete'],

      // Marketing
      'promo' => ['view', 'create', 'edit', 'delete'],
      'banner' => ['view', 'create', 'edit', 'delete'],

      // Keuangan
      'keuangan' => ['view'],
      'cash-flow' => ['view', 'create', 'edit', 'delete'],

      // Laporan
      'laporan-inventaris' => ['view'],
      'laporan-penjualan' => ['view'],
      'laporan-pembelian' => ['view'],
      'laporan-laba-rugi' => ['view'],

      // Konten
      'artikel' => ['view', 'create', 'edit', 'delete'],
      'services' => ['view', 'create', 'edit', 'delete'],
      'ecommerce' => ['view', 'create', 'edit', 'delete'],

      // Operasional
      'projek' => ['view', 'create', 'edit', 'delete'],
      'kalender' => ['view', 'create', 'edit', 'delete'],
      'toko-gudang' => ['view', 'create', 'edit', 'delete'],

      // Sistem
      'pengaturan' => ['view', 'edit'],
      'users' => ['view', 'create', 'edit', 'delete'],
      'roles' => ['view', 'create', 'edit', 'delete'],
    ];

    // =========================================================
    // 3. UPSERT MODULES DAN PERMISSIONS
    // =========================================================
    $permTable = config('permission.table_names.permissions', 'permissions');

    foreach ($modules as $moduleName => $actions) {
      // 3a. firstOrCreate module
      $module = DB::table('modules')->where('name', $moduleName)->first();

      if ($module) {
        $moduleId = $module->id;
      } else {
        $moduleId = DB::table('modules')->insertGetId([
          'name' => $moduleName,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }

      // 3b. Upsert setiap permission pada modul ini
      foreach ($actions as $action) {
        $permName = "{$action}-{$moduleName}";

        $existing = DB::table($permTable)->where('name', $permName)->where('guard_name', 'web')->first();

        if ($existing) {
          // Re-run safe: pastikan modules_id & action terisi meski seeder lama
          DB::table($permTable)
            ->where('id', $existing->id)
            ->update([
              'modules_id' => $moduleId,
              'action' => $action,
              'updated_at' => now(),
            ]);
        } else {
          DB::table($permTable)->insert([
            'modules_id' => $moduleId,
            'action' => $action,
            'name' => $permName,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }
      }
    }

    // =========================================================
    // 4. SETUP ROLES
    // =========================================================
    $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
    $gudang = Role::firstOrCreate(['name' => 'gudang', 'guard_name' => 'web']);
    $teknisi = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web']);
    $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

    // =========================================================
    // 5. ASSIGN PERMISSIONS KE ROLES
    // =========================================================

    // Admin → semua permission
    $admin->syncPermissions(Permission::all());

    // Manager → semua kecuali manajemen user/role dan pengaturan sensitif
    $manager->syncPermissions(Permission::whereNotIn('name', ['create-users', 'edit-users', 'delete-users', 'create-roles', 'edit-roles', 'delete-roles', 'edit-pengaturan'])->get());

    // Kasir → operasi penjualan dan pelanggan
    $kasir->syncPermissions(['view-dashboard', 'view-penjualan', 'create-penjualan', 'view-pelanggan', 'create-pelanggan', 'view-produk', 'view-stok-rendah', 'view-keuangan']);

    // Gudang → inventaris dan pembelian
    $gudang->syncPermissions([
      'view-dashboard',
      'view-produk',
      'create-produk',
      'edit-produk',
      'view-kategoriproduk',
      'create-kategoriproduk',
      'edit-kategoriproduk',
      'view-brand',
      'view-unit',
      'view-garansi',
      'view-serialnumber',
      'create-serialnumber',
      'edit-serialnumber',
      'view-pembelian',
      'create-pembelian',
      'view-stok-penyesuaian',
      'create-stok-penyesuaian',
      'view-stok-opname',
      'create-stok-opname',
      'view-stok-rendah',
      'view-toko-gudang',
      'create-toko-gudang',
      'edit-toko-gudang',
      'view-laporan-inventaris',
    ]);

    // Teknisi → produk dan serialnumber
    $teknisi->syncPermissions(['view-dashboard', 'view-produk', 'view-serialnumber', 'create-serialnumber', 'edit-serialnumber', 'view-stok-rendah']);

    // Staff → read-only
    $staff->syncPermissions(['view-dashboard', 'view-produk', 'view-pelanggan', 'view-penjualan', 'view-stok-rendah']);

    // =========================================================
    // 6. MIGRASI ROLE LAMA (role_id) & ASSIGN SUPER ADMIN
    // =========================================================

    // Migrasi dari kolom role_id lama jika masih ada di tabel users
    if (Schema::hasColumn('users', 'role_id')) {
      User::all()->each(function (User $user) {
        // Skip jika sudah punya role Spatie
        if ($user->roles()->exists()) {
          return;
        }

        match ((int) $user->role_id) {
          1 => $user->assignRole('admin'),
          2 => $user->assignRole('kasir'),
          3 => $user->assignRole('gudang'),
          default => $user->assignRole('staff'),
        };
      });
    }

    // Pastikan User ID 1 selalu admin (safety net untuk production)
    $superAdmin = User::find(1);
    if ($superAdmin && !$superAdmin->hasRole('admin')) {
      $superAdmin->assignRole('admin');
    }

    // Reset cache setelah semua selesai
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // =========================================================
    // 7. SUMMARY OUTPUT
    // =========================================================
    $this->command->info('');
    $this->command->info('✅ Seeding selesai!');
    $this->command->info('   Modules  : ' . DB::table('modules')->count() . ' entri');
    $this->command->info('   Permissions: ' . DB::table($permTable)->count() . ' entri');
    $this->command->info('');
    $this->command->table(['Role', 'Jumlah Permission'], Role::with('permissions')->get()->map(fn($r) => [$r->name, $r->permissions->count()])->toArray());
  }
}
