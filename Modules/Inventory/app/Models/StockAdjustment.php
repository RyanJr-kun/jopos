<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_penyesuaian' => 'datetime',
    ];

    /**
     * Menggunakan `kode_penyesuaian` untuk route model binding.
     */
    public function getRouteKeyName()
    {
        return 'kode_penyesuaian';
    }

    /**
     * Relasi ke User yang melakukan penyesuaian.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke detail item penyesuaian.
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
