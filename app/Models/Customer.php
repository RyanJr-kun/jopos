<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\POS\Models\Sale;

class Customer extends Model
{
    protected $guarded = ['id'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
