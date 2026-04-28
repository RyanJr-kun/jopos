<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
   

    // 1. Buat Data Toko (Sebagai Pondasi)
        $store = Store::create([
            'name_toko' => 'JO Computer Kartasura',
            'type' => 'toko',
            'alamat' => 'Ngadirejo, Kartasura',
            'is_active' => 1,
        ]);

        // 2. Buat User Admin
        $admin = User::create([
            'name' => 'Ryan Junior',
            'username' => 'RyanJr-kun',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'), 
            'status' => 1,
        ]);

        // 3. Hubungkan ke Employee Profile
        // Di sini kita menyatukan user_id dan store_id
        EmployeeProfile::create([
            'user_id'           => $admin->id,
            'store_id'          => $store->id,
            'jabatan'           => 'staff_it', // Atau 'Full Stack Developer'
            'nik'               => '1234567890',
            'tanggal_bergabung' => now(),
        ]);
    }
}
