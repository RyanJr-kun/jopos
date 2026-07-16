<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionCategory extends Model
{
  use Sluggable;
  protected $guarded = ['id'];

  public function cashFlows(): HasMany
  {
    return $this->hasMany(CashFlow::class);
  }

  public function getRouteKeyName(): string
  {
    return 'slug';
  }

  public function sluggable(): array
  {
    return ['slug' => ['source' => 'name']];
  }

  public static function getTypes()
  {
    return ['income', 'expense'];
  }
}
