<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'referensi_id',
        'referensi_type',
        'tipe_gerakan',
        'jumlah_masuk',
        'jumlah_keluar',
        'keterangan',
        'tanggal',
    ];

    /**
     * Mendapatkan model induk dari referensi (polymorphic).
     */
    public function referensiSource(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'referensi_type', 'referensi_id');
    }

    /**
     * Accessor untuk mendapatkan name route berdasarkan tipe gerakan.
     */
    protected function routeName(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->tipe_gerakan) {
                'Purchase' => 'pembelian.show',
                'Sale' => 'penjualan.show',
                'Stock Opname' => 'stok-opname.show',
                'Penyesuaian' => 'stok-penyesuaian.show',
                default => null,
            },
        );
    }

    /**
     * Accessor untuk mendapatkan teks referensi yang akan ditampilkan.
     */
    protected function referensi(): Attribute
    {
        return Attribute::make(
            get: function () {
                $source = $this->referensiSource;
                if (!$source) return $this->keterangan ?: '-';

                return $source->referensi ?? $source->id;
            }
        );
    }
}
