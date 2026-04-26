<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\SerialNumber;

class SaleItem extends Model
{
    protected $guarded = ['id'];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class, 'item_sale_id');
    }
}
