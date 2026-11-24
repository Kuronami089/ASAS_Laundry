<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Layanan (Reguler, Ekspres, Superkilat)
 */
class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama_layanan',
        'harga_per_kg',
        'estimasi_selesai',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_kg' => 'integer',
        ];
    }

    /**
     * Relasi 1-ke-banyak: satu jenis layanan dapat digunakan banyak transaksi.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'layanan_id');
    }
}
