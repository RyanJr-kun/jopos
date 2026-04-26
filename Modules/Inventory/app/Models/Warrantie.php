<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Cviebrock\EloquentSluggable\Sluggable;

class Warrantie extends Model
{
    use Sluggable;
    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getFormattedDurationAttribute(): string
    {
        $totalDays = $this->duration; // Ini sekarang adalah TOTAL HARI (integer)

        if (!$totalDays || $totalDays <= 0) {
            return 'N/A';
        }

        $parts = [];
        $daysRemaining = $totalDays;

        // 1. Hitung Tahun
        $years = floor($daysRemaining / 360);
        if ($years > 0) {
            $parts[] = $years . ' Tahun';
            $daysRemaining %= 360;
        }

        // 2. Hitung Bulan
        $months = floor($daysRemaining / 30);
        if ($months > 0) {
            $parts[] = $months . ' Bulan';
            $daysRemaining %= 30;
        }

        // 3. Hitung Minggu
        $weeks = floor($daysRemaining / 7);
        if ($weeks > 0) {
            $parts[] = $weeks . ' Minggu';
            $daysRemaining %= 7;
        }

        // 4. Sisa Hari
        if ($daysRemaining > 0) {
            $parts[] = $daysRemaining . ' Hari';
        }

        // Gabungkan bagian-bagian tersebut dengan spasi
        return !empty($parts) ? implode(' ', $parts) : 'Kurang dari 1 hari';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
