<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer; // Pastikan Anda mengimpor model Customer

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'name' => 'Customer Umum',
                'kontak' => null,
                'email' => null,
                'alamat' => null,
                'status' => true,
            ]
        );
    }
}
