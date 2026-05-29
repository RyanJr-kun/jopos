<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bank extends Model
{
    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik',
        'logo_bank',
        'status_aktif'
    ];

    // Accessor untuk mempermudah pemanggilan URL Logo
    public function getLogoUrlAttribute()
    {
        if ($this->logo_bank && Storage::disk('r2')->exists($this->logo_bank)) {
            return Storage::url($this->logo_bank);
        }
        return asset('assets/img/banks/default-bank.png'); // Gambar fallback jika logo belum diupload
    }
}
