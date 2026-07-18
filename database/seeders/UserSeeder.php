<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\EmployeeProfile;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // 1. Buat Data Toko (Sebagai Pondasi)
    // Gunakan ::insert() lalu bungkus semua data ke dalam satu array utama [...]
    Store::insert([
      [
        'name_toko' => 'JO Computer Kartasura',
        'type' => 'toko',
        'provinsi' => 'JAWA TENGAH',
        'kabupaten_kota' => 'KABUPATEN SUKOHARJO',
        'kecamatan' => 'KARTASURA',
        'desa' => 'NGADIREJO',
        'alamat' => 'Jl. Slamet Riyadi Somodinalan No.250, Ngadirejo, Kec. Kartasura, Kab. Sukoharjo',
        'map_url' =>
          'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.123905406264!2d110.75370057481605!3d-7.5614670924524265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a14f83e78f24b%3A0x76f6f20de70e8d57!2sJO%20Computer!5e0!3m2!1sen!2sid!4v1783144983741!5m2!1sen!2sid',
        'latitude' => -7.56129692,
        'longitude' => 110.75618966,
        'telepon' => '081318000699',
        'email' => 'cs@jocomputer.com',
        'logo' => 'profil-toko/CnRgJ7pnpUa5wwQaXBKuU7k9tOJ2lhzaqlVHo7zn.png',
        'pic_id' => null, // PASTIKAN user dengan ID 1 sudah ada di tabel users!
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name_toko' => 'JO Computer Baturetno', // Sedikit disesuaikan agar tidak bingung bedanya
        'type' => 'toko',
        'provinsi' => 'JAWA TENGAH',
        'kabupaten_kota' => 'KABUPATEN WONOGIRI',
        'kecamatan' => 'BATURETNO',
        'desa' => 'BATURETNO',
        'alamat' => 'Jl. Raya Baturetno-Batuwarno, Batu Lor, Baturetno, Kec. Baturetno, Kab. Wonogiri',
        'map_url' =>
          'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.1874681565914!2d110.93293327482056!3d-7.979565692045684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7bd3b3ea85de39%3A0xba70d60ab0ce991b!2sJo%20Computer%20Baturetno!5e0!3m2!1sen!2sid!4v1783145276404!5m2!1sen!2sid',
        'latitude' => -7.97957632,
        'longitude' => 110.93547601,
        'telepon' => '081318000699',
        'email' => 'cs@jocomputer.com',
        'logo' => 'profil-toko/knVqkn70IfwTh1IC9lenqRdHtuzXqMS4YEUyHpQ6.png',
        'pic_id' => null,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
    // 2. Buat User Admin
    $admin = User::create([
      'name' => 'Ryan Junior',
      'username' => 'RyanJr-kun',
      'email' => 'admin@example.com',
      'password' => Hash::make('admin'),
      'status' => 1,
    ]);

    // 3. Hubungkan ke Employee employee
    // Di sini kita menyatukan user_id dan store_id
    EmployeeProfile::create([
      'user_id' => $admin->id,
      'store_id' => 1, // Ambil ID toko pertama (JO Computer Kartasura)
      'jabatan' => 'staff_it', // Atau 'Full Stack Developer'
      'nik' => '1234567890',
      'tanggal_bergabung' => now(),
    ]);

    Account::insert([
      // --- AKUN TUNAI PER TOKO ---
      [
        'store_id' => 1, // ID Toko Kartasura
        'tipe_akun' => 'tunai',
        'account_name' => 'Kas Tunai Kartasura',
        'nomor_rekening' => null,
        'nama_pemilik' => null,
        'saldo_awal' => 0,
        'logo_bank' => null,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'store_id' => 2, // ID Toko Baturetno
        'tipe_akun' => 'tunai',
        'account_name' => 'Kas Tunai Baturetno',
        'nomor_rekening' => null,
        'nama_pemilik' => null,
        'saldo_awal' => 0,
        'logo_bank' => null,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],

      // --- AKUN BANK PUSAT ---
      [
        'store_id' => null, // Pusat (Bisa dipakai semua toko)
        'tipe_akun' => 'bank',
        'account_name' => 'BCA',
        'nomor_rekening' => '392.029.4277',
        'nama_pemilik' => 'Gunawan SetyaBudi N',
        'saldo_awal' => 0,
        'logo_bank' => 'banks/bank-bca-1781754601.png',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'store_id' => null,
        'tipe_akun' => 'bank',
        'account_name' => 'Mandiri',
        'nomor_rekening' => '138.001.728.4147',
        'nama_pemilik' => 'Gunawan SetyaBudi N',
        'saldo_awal' => 0,
        'logo_bank' => 'banks/mandiri-1780995956.png',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'store_id' => null,
        'tipe_akun' => 'bank',
        'account_name' => 'BRI',
        'nomor_rekening' => '6959.01.028402.53.8',
        'nama_pemilik' => 'Etik Yudiarini',
        'saldo_awal' => 0,
        'logo_bank' => 'banks/bank-bri-1781107738.png',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],

      // --- AKUN QRIS PUSAT ---
      [
        'store_id' => null,
        'tipe_akun' => 'qris',
        'account_name' => 'JO COMPUTER 1', // Bebas bisa disesuaikan namanya
        'nomor_rekening' => null,
        'nama_pemilik' => 'JO Computer',
        'saldo_awal' => 0,
        'logo_bank' => null,
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
