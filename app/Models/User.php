<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Product;
use App\Models\Income;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use Notifiable, HasRoles;
  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $guarded = ['id'];
  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'mulai_kerja' => 'date',
      'password' => 'hashed',
      'status' => 'boolean',
    ];
  }

  public function products(): HasMany
  {
    return $this->hasMany(Product::class);
  }

  public function sales(): HasMany
  {
    return $this->hasMany(Sale::class);
  }

  public function purchases(): HasMany
  {
    return $this->hasMany(Purchase::class);
  }

  public function Incomes(): HasMany
  {
    return $this->hasMany(Income::class);
  }

  public function expenses(): HasMany
  {
    return $this->hasMany(Expense::class);
  }

  /**
   * Get the route key for the model.
   *
   * @return string
   */
  public function getRouteKeyName(): string
  {
    return 'username';
  }
}
