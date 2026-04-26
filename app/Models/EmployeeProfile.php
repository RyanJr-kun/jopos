<?php

namespace App\Models;

use App\Enums\Jabatan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Store;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $table = 'employee_profiles';

    protected $fillable = [
        'user_id',
        'store_id',
        'kontak',
        'alamat',
        'avatar',
        'jabatan',
        'nik',
        'tanggal_bergabung'
    ];

    /**
     * Casting atribut agar otomatis dikonversi ke tipe data/objek tertentu.
     */
    protected $casts = [
        'jabatan' => Jabatan::class, // Otomatis jadi Enum
        'tanggal_bergabung' => 'date',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Relasi ke akun login (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke lokasi penempatan (Store/Gudang)
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Helper untuk mendapatkan URL foto profil atau default avatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('assets/img/avatars/1.png'); // Sesuaikan path default Sneat
    }
}
