<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\SerialNumber;

class ProductStock extends Model
{
    protected $table = 'product_stocks';

    protected $fillable = [
        'store_id',
        'product_id',
        'product_variant_id',
        'qty',
    ];

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class, 'product_id');
    }
}
