<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::query()->delete();

        $units = [
            // Satuan Umum & Kemasan
            ['name' => 'Pieces', 'singkat' => 'Pcs'],
            ['name' => 'Unit', 'singkat' => 'Unit'],
            ['name' => 'Set', 'singkat' => 'Set'],
            ['name' => 'Box', 'singkat' => 'Box'],
            ['name' => 'Dus', 'singkat' => 'Dus'],
            ['name' => 'Pack', 'singkat' => 'Pack'],
            ['name' => 'Roll', 'singkat' => 'Roll'],
            ['name' => 'Lembar', 'singkat' => 'Lembar'],

            // Satuan Ukuran Fisik
            ['name' => 'Milimeter', 'singkat' => 'mm'],
            ['name' => 'Sentimeter', 'singkat' => 'cm'],
            ['name' => 'Meter', 'singkat' => 'm'],
            ['name' => 'Inci', 'singkat' => 'Inch'],
            ['name' => 'Gram', 'singkat' => 'g'],
            ['name' => 'Kilogram', 'singkat' => 'kg'],
        ];

        // Looping untuk memasukkan data ke database
        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
                'slug' => Str::slug($unit['name'], '-'),
                'singkat' => $unit['singkat'],
            ]);
        }
    }
}
