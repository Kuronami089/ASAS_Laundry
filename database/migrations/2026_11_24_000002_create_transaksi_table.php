<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration Tabel Transaksi Laundry
 * ---------------------------------
 * Menggunakan Foreign Key ke tabel layanan dengan restrictOnDelete()
 * agar layanan yang masih terpakai di transaksi tidak bisa terhapus sembarangan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 20)->unique();
            $table->string('nama_pelanggan', 100);
            $table->string('no_hp', 20);
            $table->foreignId('layanan_id')
                ->constrained('layanan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->decimal('berat_kg', 6, 2);
            $table->unsignedInteger('harga_saat_transaksi');
            $table->unsignedInteger('total_harga');
            $table->enum('status', ['Proses', 'Selesai'])->default('Proses');
            $table->dateTime('tanggal_masuk')->useCurrent();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
