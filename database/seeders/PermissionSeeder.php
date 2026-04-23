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

        // 1. Buat daftar permission yang dibutuhkan aplikasi
        $permissions = [
            // Contoh kelompok Kelola User
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Contoh kelompok Kelola Role
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Tambahkan permission lain sesuai fitur aplikasimu di sini...
        ];

        // 2. Insert permission ke database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Buat Role menggunakan firstOrCreate
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        // Berikan semua permission ke super admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Buat Role lain
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(['view-users', 'view-roles']);
    }
}