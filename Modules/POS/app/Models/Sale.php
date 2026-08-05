<?php

namespace Modules\POS\Models;

use App\Models\Customer;
use App\Models\SalePayment;
use App\Models\Taxe;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
  protected $guarded = ['id'];
  protected $casts = [
    'subtotal' => 'float',
    'diskon' => 'float',
    'pajak' => 'float',
    'total_akhir' => 'float',
    'tanggal_penjualan' => 'datetime', // 👈 Tambahkan baris ini
    'tanggal_jatuh_tempo' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function getRouteKeyName()
  {
    return 'referensi';
  }

  /**
   * Get all of the items for the Sale
   */
  public function items(): HasMany
  {
    return $this->hasMany(SaleItem::class);
  }

  /**
   * Get the user that owns the Sale
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function tax(): BelongsTo
  {
    return $this->belongsTo(Taxe::class);
  }

  /**
   * Get the customer that owns the Sale
   */
  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'customer_id')->withDefault([
      'name' => 'Customer Umum',
    ]);
  }

  public function payments()
  {
    return $this->hasMany(SalePayment::class, 'sale_id');
  }

  protected function persentaseBayar(): Attribute
  {
    return Attribute::make(
      get: function () {
        if (!$this->total_akhir || $this->total_akhir == 0) {
          return 0;
        }
        return min(100, round(($this->jumlah_dibayar / $this->total_akhir) * 100));
      },
    );
  }

  protected function isOverdue(): Attribute
  {
    return Attribute::make(
      get: function () {
        if (!$this->tanggal_jatuh_tempo) {
          return false;
        }
        if ($this->status_pembayaran === 'Lunas' || $this->status_pembayaran === 'Batal') {
          return false;
        }
        return \Carbon\Carbon::parse($this->tanggal_jatuh_tempo)->isPast();
      },
    );
  }

  public static function getPaymentStatuses()
  {
    return ['Lunas', 'Batal', 'Piutang'];
  }

  public static function getPaymentMethods()
  {
    return ['TUNAI', 'TRANSFER', 'QRIS'];
  }
}
