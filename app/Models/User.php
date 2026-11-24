<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model User (Admin & Kasir)
 */
class User extends Authenticatable
{
    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Mengecek apakah akun ini berperan sebagai Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relasi 1-ke-banyak: satu petugas bisa mencatat banyak transaksi.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'user_id');
    }
}
