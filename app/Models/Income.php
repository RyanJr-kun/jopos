<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $guarded = ['id'];
    protected $with = ['transaction_category', 'user'];

    protected function hargaFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp. ' . number_format($this->attributes['jumlah'], 2, ',', '.')
        );
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function transaction_category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class);
    }
}
