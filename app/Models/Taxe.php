<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Models\Product;

class Taxe extends Model
{
  
  protected $guarded = ['id'];

  public function produk(): HasMany
  {
    return $this->hasMany(Product::class);
  }
}
