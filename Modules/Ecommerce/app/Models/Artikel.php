<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Artikel extends Model
{
    protected $fillable = [
        'user_id',
        'judul_artikel',
        'slug',
        'isi_artikel',
        'thumbnail',
        'status',
        'kategori',
    ];

    protected $casts = [
        'kategori' => 'array',
    ];

    /**
     * Relasi ke User (penulis artikel).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: URL thumbnail lengkap.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->thumbnail) {
                    // Pastikan Storage::url memanggil disk yang tepat jika menggunakan R2
                    return Storage::url($this->thumbnail);
                }
                return asset('assets/img/default-thumbnail.jpg');
            }
        );
    }
}
