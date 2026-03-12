<?php

namespace Database\Seeders;

use App\Models\Warrantie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarrantieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warrantie::updateOrCreate(
            ['slug' => 'tanpa-garansi'], // Kunci untuk mencari record yang ada
            [
                'name' => 'Tanpa Warrantie',
                'duration' => 0,
                'description' => 'Product ini tidak memiliki garansi dari toko.',
                'status' => true,
            ]
        );
    }
}
