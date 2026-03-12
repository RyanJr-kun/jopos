<?php

namespace App\Models;

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

    /**
     * Get the pelanggan that owns the Sale
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withDefault([
            'name' => 'Customer Umum',
        ]);
    }
}
