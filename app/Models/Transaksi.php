<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Transaksi Laundry
 * -----------------------
 * Wajib berelasi dengan Model Layanan (belongsTo) dan opsional dengan petugas (User).
 */
class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'nama_pelanggan',
        'no_hp',
        'layanan_id',
        'user_id',
        'berat_kg',
        'harga_saat_transaksi',
        'total_harga',
        'status',
        'tanggal_masuk',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'berat_kg'             => 'float',
            'harga_saat_transaksi' => 'integer',
            'total_harga'          => 'integer',
            'tanggal_masuk'        => 'datetime',
            'tanggal_selesai'      => 'datetime',
        ];
    }

    /**
     * Relasi ke tabel layanan.
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    /**
     * Relasi ke tabel users (petugas yang mencatat transaksi).
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Membuat kode transaksi berurutan seperti LDR-0015 berdasarkan ID terakhir.
     */
    public static function generateKode(): string
    {
        $lastId = (int) static::max('id');
        return sprintf('LDR-%04d', $lastId + 1);
    }
}
