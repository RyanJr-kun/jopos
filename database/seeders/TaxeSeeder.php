<?php

namespace Database\Seeders;

use App\Models\Taxe;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaxeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Taxe::firstOrCreate(['name_taxe' => 'Bebas Taxe', 'rate' => '0']);
        Taxe::firstOrCreate(['name_taxe' => 'PPN', 'rate' => '12']);
    }
}
