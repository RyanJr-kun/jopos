<?php

namespace App\Enums;

// 1. Tambahkan ": string" untuk menjadikannya "Backed Enum"
enum BannerPosition: string
{
    case MAIN = 'main';
    case MAIN2 = 'main2';
    case MAIN3 = 'main3';
    case PROMO = 'promo';
    case BESTSELLER = 'bestseller';
    case BESTSELLERMOBILE = 'bestseller_mobile';

    // 💡 Pro Tip: Anda bisa menambahkan helper function di sini jika perlu,
    // misalnya untuk mendapatkan daftar name yang lebih ramah untuk dropdown.
    public function getLabel(): string
    {
        return match ($this) {
            self::MAIN => 'Main',
            self::MAIN2 => 'Main2',
            self::MAIN3 => 'Main3',
            self::PROMO => 'Promotion',
            self::BESTSELLER => 'Bestseller',
            self::BESTSELLERMOBILE => 'Bestseller Mobile',
        };
    }
}
