<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\TransactionCategory;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel untuk menghindari duplikasi saat seeder dijalankan ulang
        TransactionCategory::query()->delete();

        $categories = [
            // =================================================================
            // == KATEGORI PEMASUKAN ==
            // =================================================================

            [
                'name' => 'Sale Aksesoris',
                'type' => 'income',
                'description' => 'Income dari penjualan mouse, keyboard, headset.',
            ],
            [
                'name' => 'Sale Software',
                'type' => 'income',
                'description' => 'Income dari penjualan OS, Antivirus, aplikasi.',
            ],
            [
                'name' => 'Jasa Servis',
                'type' => 'income',
                'description' => 'Pendapatan dari jasa perbaikan hardware & software.',
            ],
            [
                'name' => 'Jasa Instalasi',
                'type' => 'income',
                'description' => 'Pendapatan dari jasa instalasi OS atau program.',
            ],
            [
                'name' => 'Jasa Perakitan PC',
                'type' => 'income',
                'description' => 'Pendapatan dari jasa merakit komputer custom.',
            ],
            [
                'name' => 'Sale Barang Bekas',
                'type' => 'income',
                'description' => 'Income dari penjualan komponen atau unit bekas.',
            ],
            [
                'name' => 'Pendapatan Bunga Bank',
                'type' => 'income',
                'description' => 'Income non-operasional dari bunga simpanan bank.',
            ],

            // =================================================================
            // == KATEGORI PENGELUARAN ==
            // =================================================================
            [
                'name' => 'Purchase Stock Barang',
                'type' => 'expense',
                'description' => 'Expense untuk membeli barang dagangan dari pemasok.',
            ],
            [
                'name' => 'Gaji Karyawan',
                'type' => 'expense',
                'description' => 'Pembayaran gaji bulanan untuk semua staf dan teknisi.',
            ],
            [
                'name' => 'Biaya Listrik',
                'type' => 'expense',
                'description' => 'Pembayaran tagihan listrik bulanan untuk operasional.',
            ],
            [
                'name' => 'Biaya Internet & Telepon',
                'type' => 'expense',
                'description' => 'Pembayaran tagihan internet dan telepon untuk toko.',
            ],
            [
                'name' => 'Sewa Tempat Usaha',
                'type' => 'expense',
                'description' => 'Biaya sewa ruko atau gedung tempat usaha.',
            ],
            [
                'name' => 'Biaya Pemasaran',
                'type' => 'expense',
                'description' => 'Expense untuk iklan online, brosur, dan promotionsi.',
            ],
            [
                'name' => 'Purchase Peralatan Toko',
                'type' => 'expense',
                'description' => 'Belanja aset seperti etalase, kursi, atau alat kasir.',
            ],
            [
                'name' => 'Purchase Peralatan Servis',
                'type' => 'expense',
                'description' => 'Belanja alat-alat untuk teknisi seperti solder, obeng.',
            ],
            [
                'name' => 'Biaya Transportasi',
                'type' => 'expense',
                'description' => 'Expense untuk bensin atau pengiriman barang.',
            ],
            [
                'name' => 'Alat Tulis Kantor (ATK)',
                'type' => 'expense',
                'description' => 'Purchase kebutuhan kantor seperti kertas, tinta, pulpen.',
            ],
            [
                'name' => 'Biaya Administrasi Bank',
                'type' => 'expense',
                'description' => 'Biaya bulanan yang dikenakan oleh pihak bank.',
            ],
            [
                'name' => 'Taxe & Retribusi',
                'type' => 'expense',
                'description' => 'Pembayaran pajak usaha dan retribusi daerah.',
            ],
        ];

        foreach ($categories as $category) {
            TransactionCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'type' => $category['type'],
                'description' => $category['description'],
            ]);
        }
    }
}
