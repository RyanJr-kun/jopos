<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    protected $guarded = ['id'];

    /**
     * Mendapatkan data master stok opname.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stokOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    /**
     * Mendapatkan data produk terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produk()
    {
        return $this->belongsTo(Product::class);
    }
}
