<?php

namespace App\Enums;

enum Jabatan: string
{
    // Level Manajemen / Leader
    case KEPALA_TOKO = 'kepala_toko';

        // Level Operasional Harian
    case ADMIN_OPERASIONAL = 'admin_operasional'; // Untuk teman Anda yang "Palugada"
    case KASIR = 'kasir';
    case ADMIN_GUDANG = 'admin_gudang';
    case FINANCE = 'finance'; // Khusus yang ngurus kwitansi, laporan laba rugi, pajak

        // Level Pemasaran & Konten (Digabung agar lebih luas)
    case DIGITAL_MARKETING = 'digital_marketing'; // Sudah mencakup Ads, Sosmed, Bikin Poster

        // Level Layanan / Servis
    case TEKNISI = 'teknisi';
    case KURIR = 'kurir'; // PENTING! Untuk fitur antar-jemput barang servis

        // Level Support / Lainnya
    case STAFF_IT = 'staff_it'; // Maintenance sistem JOPOS / Jaringan Toko
    case MAGANG = 'magang';

    public function getLabel(): string
    {
        return match ($this) {
            self::KEPALA_TOKO => 'Kepala Toko',
            self::ADMIN_OPERASIONAL => 'Admin Operasional',
            self::KASIR => 'Kasir / Frontdesk',
            self::ADMIN_GUDANG => 'Admin Gudang',
            self::FINANCE => 'Finance & Administrasi',
            self::DIGITAL_MARKETING => 'Digital Marketing',
            self::TEKNISI => 'Teknisi Komputer',
            self::KURIR => 'Kurir / Driver',
            self::STAFF_IT => 'Staff IT',
            self::MAGANG => 'Internship / Magang',
        };
    }
}
