<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlow extends Model
{
  // Nilai kolom `type` — dipakai juga sebagai segmen URL {type}
  const TYPE_INCOME = 'income';
  const TYPE_EXPENSE = 'expense';
  const TYPE_TRANSFER = 'transfer';

  protected $fillable = [
    'store_id',
    'transaction_category_id',
    'tanggal',
    'nominal',
    'metode_pembayaran',
    'bank_id',
    'metode_pembayaran_tujuan',
    'bank_id_tujuan',
    'referensi',
    'bukti',
    'keterangan',
    'description',
    'user_id',
    'source_type',
    'source_id',
    'dibatalkan_at',
  ];

  protected $with = ['transaction_category', 'user'];

  protected $casts = [
    'tanggal' => 'datetime',
    'nominal' => 'decimal:0',
    'dibatalkan_at' => 'datetime',
  ];

  public function scopeAktif(Builder $query): Builder
  {
    return $query->whereNull('dibatalkan_at');
  }

  protected function isOtomatis(): Attribute
  {
    return Attribute::make(get: fn() => !is_null($this->source_type));
  }

  protected function nominalFormatted(): Attribute
  {
    return Attribute::make(get: fn() => 'Rp ' . number_format((float) $this->attributes['nominal'], 0, ',', '.'));
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function transaction_category(): BelongsTo
  {
    return $this->belongsTo(TransactionCategory::class);
  }

  /** Bank sisi ASAL (dipakai juga oleh income/expense sebagai bank tujuan setor/tarik) */
  public function bank(): BelongsTo
  {
    return $this->belongsTo(Bank::class, 'bank_id');
  }

  /** Bank sisi TUJUAN — hanya terisi kalau type = transfer */
  public function bankTujuan(): BelongsTo
  {
    return $this->belongsTo(Bank::class, 'bank_id_tujuan');
  }

  public function store(): BelongsTo
  {
    return $this->belongsTo(Store::class);
  }

  public function scopeIncome(Builder $query): Builder
  {
    return $query->where('type', self::TYPE_INCOME);
  }

  public function scopeExpense(Builder $query): Builder
  {
    return $query->where('type', self::TYPE_EXPENSE);
  }

  public function scopeTransfer(Builder $query): Builder
  {
    return $query->where('type', self::TYPE_TRANSFER);
  }

  public function getRouteKeyName(): string
  {
    return 'referensi';
  }

  public function getPaymentMethods(): array
  {
    return ['TUNAI', 'TRANSFER', 'QRIS'];
  }

  public static function typeLabel(string $type): string
  {
    return match ($type) {
      self::TYPE_INCOME => 'Pemasukan',
      self::TYPE_EXPENSE => 'Pengeluaran',
      self::TYPE_TRANSFER => 'Transfer',
      default => $type,
    };
  }
}
