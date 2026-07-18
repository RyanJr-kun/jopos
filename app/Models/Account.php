<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Account extends Model
{
  protected $fillable = ['store_id', 'tipe_akun', 'account_name', 'nomor_rekening', 'nama_pemilik', 'saldo_awal', 'logo_bank', 'is_active'];
  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function getLogoUrlAttribute(): string
  {
    if ($this->logo_bank && Storage::disk('r2')->exists($this->logo_bank)) {
      return Storage::disk('r2')->url($this->logo_bank);
    }

    return asset('assets/img/logo.png');
  }

  public function getInisialAttribute(): string
  {
    $words = explode(' ', $this->account_name);
    return strtoupper(count($words) >= 2 ? substr($words[0], 0, 1) . substr($words[1], 0, 1) : substr($this->account_name, 0, 2));
  }

  public function scopeAktif($query)
  {
    return $query->where('is_active', true);
  }
}
