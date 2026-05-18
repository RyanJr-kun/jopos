<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $guarded = ['id'];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
}
