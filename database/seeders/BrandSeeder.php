<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;


class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel untuk memastikan tidak ada duplikasi
        Brand::query()->delete();

        // Daftar merek yang relevan dengan toko komputer & elektronik
        $brands = [
            // Laptop & PC
            'Dell',
            'HP',
            'Lenovo',
            'Apple',
            'Asus',
            'Acer',
            'MSI',
            'Razer',
            'Microsoft',

            // Komponen PC (CPU, GPU, Motherboard)
            'Intel',
            'AMD',
            'NVIDIA',
            'Gigabyte',
            'ASRock',
            'Sapphire',
            'Zotac',
            'Palit',
            'EVGA',

            // Komponen PC (RAM, Storage, PSU)
            'Corsair',
            'G.Skill',
            'Kingston',
            'HyperX',
            'Crucial',
            'TeamGroup',
            'Seagate',
            'Western Digital',
            'Samsung',
            'SanDisk',
            'ADATA',
            'Cooler Master',
            'Seasonic',
            'Thermaltake',
            'NZXT',
            'Noctua',
            'be quiet!',

            // Periferal (Monitor, Keyboard, Mouse)
            'Logitech',
            'SteelSeries',
            'Keychron',
            'BenQ',
            'LG',
            'ViewSonic',
            'AOC',
            'Fantech',
            'Rexus',

            // Printer & Audio
            'Canon',
            'Epson',
            'Brother',
            'Sony',
            'JBL',
            'Sennheiser',
            'Bose',
            'Harman Kardon',

            // Jaringan
            'TP-Link',
            'D-Link',
            'Linksys',
            'Netgear',
            'Ubiquiti',
            'Mikrotik',

            // Smartphone & TV
            'Xiaomi',
            'Oppo',
            'Vivo',
            'Google',
            'TCL',
            'Philips',
        ];

        // Urutkan merek berdasarkan abjad untuk kerapian
        sort($brands);

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand),
            ]);
        }
    }
}
