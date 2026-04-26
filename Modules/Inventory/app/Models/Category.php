<?php

namespace Modules\Inventory\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Product;

class Category extends Model
{
    use Sluggable;
    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relasi ke sub kategori (children).
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relasi ke produk.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Scope: hanya kategori utama.
     */
    public function scopeUtama($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: hanya sub kategori.
     */
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
