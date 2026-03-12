<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WarrantieSeeder::class,
            CustomerSeeder::class,
            RoleSeeder::class,
            TaxeSeeder::class,
            CustomerSeeder::class,
            CategorySeeder::class,
            TransactionCategorySeeder::class,
            UnitSeeder::class,
            BrandSeeder::class,
            UserSeeder::class,
        ]);
    }
}
