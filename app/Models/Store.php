<?php

namespace App\Models;

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'stores';

    /**
     * Atribut yang dapat diisi (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'name_toko',
        'type',
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'desa',
        'alamat',
        'map_url',
        'latitude',
        'longitude',
        'telepon',
        'email',
        'logo',
        'pic_id',
        'is_active',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELASI (RELATIONSHIPS)
    // =========================================================================

    /**
     * Relasi ke Kepala Toko / PIC (One-to-One / BelongsTo).
     * Mengambil data user yang bertanggung jawab atas lokasi ini.
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * Relasi ke Staff / Karyawan (One-to-Many).
     * Mengambil semua user yang ditempatkan di lokasi ini.
     */
    public function employees()
    {
        return $this->hasMany(EmployeeProfile::class, 'store_id');
    }

    // =========================================================================
    // SCOPES (QUERY FILTERS)
    // =========================================================================

    /**
     * Filter hanya lokasi tipe 'toko'.
     */
    public function scopeToko($query)
    {
        return $query->where('type', 'toko');
    }

    /**
     * Filter hanya lokasi tipe 'gudang'.
     */
    public function scopeGudang($query)
    {
        return $query->where('type', 'gudang');
    }

    /**
     * Filter hanya lokasi yang berstatus aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // =========================================================================
    // ACCESSORS (VIRTUAL ATTRIBUTES)
    // =========================================================================

    /**
     * Mendapatkan alamat lengkap dalam satu string (kecuali alamat detail).
     */
    public function getFullRegionAttribute(): string
    {
        return "{$this->desa}, {$this->kecamatan}, {$this->kabupaten_kota}, {$this->provinsi}";
    }

    /**
     * Cek apakah lokasi memiliki logo, jika tidak gunakan placeholder.
     */
    public function getLogoPathAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('assets/img/produk.png');
    } 

    public static function getTypes()
    {
        return [
            'toko',
            'gudang'
        ];
    }
}
