<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_opname' => 'datetime', // Ini akan mengubah string tanggal menjadi objek Carbon
    ];


    /**
     * Mendapatkan semua detail untuk StockOpname.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Mendapatkan user yang melakukan StockOpname.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
