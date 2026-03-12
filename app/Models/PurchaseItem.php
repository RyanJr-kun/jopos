<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function pajak(): BelongsTo
    {
        return $this->belongsTo(Taxe::class);
    }
}
