<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\POS\Models\Sale;

class SerialNumber extends Model
{
    protected $guarded = ['id'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
