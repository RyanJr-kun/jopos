<?php

namespace App\Models;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bank extends Model
{
    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik',
        'logo_bank',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_bank && Storage::disk('r2')->exists($this->logo_bank)) {
            return Storage::disk('r2')->url($this->logo_bank);
        }
 
        return asset('assets/img/banks/default-bank.png');
    }

    public function getInisialAttribute(): string
    {
        $words = explode(' ', $this->nama_bank);
        return strtoupper(
            count($words) >= 2
                ? substr($words[0], 0, 1) . substr($words[1], 0, 1)
                : substr($this->nama_bank, 0, 2)
        );
    }

    public function incomes()
    {
        return $this->hasMany(Income::class, 'bank_id');
    }
 
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'bank_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
