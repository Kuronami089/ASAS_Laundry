<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder Data Awal ASAS Laundry
 * -----------------------------
 * Mengisi akun Admin (Faqih Rachman) & Kasir (Nadia Putri),
 * 3 tarif layanan utama (Reguler, Ekspres, Superkilat),
 * serta 14 transaksi awal untuk demo Dashboard, Rekap, dan Struk.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin & Kasir
        $admin = User::create([
            'nama_lengkap' => 'Faqih Rachman',
            'username'     => 'admin',
            'password'     => Hash::make('admin123'),
            'role'         => 'admin',
        ]);

        $kasir = User::create([
            'nama_lengkap' => 'Nadia Putri',
            'username'     => 'kasir',
            'password'     => Hash::make('kasir123'),
            'role'         => 'kasir',
        ]);

        // 2. Paket Layanan sesuai spesifikasi
        $reguler = Layanan::create([
            'nama_layanan'     => 'Reguler',
            'harga_per_kg'     => 7000,
            'estimasi_selesai' => '3-4 hari',
        ]);

        $ekspres = Layanan::create([
            'nama_layanan'     => 'Ekspres',
            'harga_per_kg'     => 12000,
            'estimasi_selesai' => '1-2 hari',
        ]);

        $superkilat = Layanan::create([
            'nama_layanan'     => 'Superkilat',
            'harga_per_kg'     => 25000,
            'estimasi_selesai' => '5 jam',
        ]);

        // 3. 14 Data Transaksi Awal (10 Selesai, 4 Proses, Total Rp 636.000)
        $now  = now();
        $rows = [
            ['LDR-0001', 'Budi Santoso',    '081234567890', $reguler,    $kasir, 4.00, 'Selesai', $now->copy()->subDays(7),  $now->copy()->subDays(4)],
            ['LDR-0002', 'Siti Aminah',     '085611223344', $ekspres,    $kasir, 3.50, 'Selesai', $now->copy()->subDays(6),  $now->copy()->subDays(5)],
            ['LDR-0003', 'Andi Wijaya',     '082198765432', $superkilat, $admin, 2.00, 'Selesai', $now->copy()->subDays(5),  $now->copy()->subDays(5)->addHours(5)],
            ['LDR-0004', 'Dewi Lestari',    '087855667788', $reguler,    $kasir, 5.00, 'Selesai', $now->copy()->subDays(5),  $now->copy()->subDays(2)],
            ['LDR-0005', 'Hendra Gunawan',  '081377889900', $ekspres,    $admin, 4.50, 'Selesai', $now->copy()->subDays(4),  $now->copy()->subDays(3)],
            ['LDR-0006', 'Rina Kusuma',     '085722334455', $reguler,    $kasir, 6.00, 'Selesai', $now->copy()->subDays(4),  $now->copy()->subDays(1)],
            ['LDR-0007', 'Dimas Pratama',   '082244556677', $superkilat, $admin, 1.50, 'Selesai', $now->copy()->subDays(3),  $now->copy()->subDays(3)->addHours(5)],
            ['LDR-0008', 'Kartika Sari',    '081566778899', $ekspres,    $kasir, 2.50, 'Selesai', $now->copy()->subDays(3),  $now->copy()->subDays(2)],
            ['LDR-0009', 'Fajar Ramadhan',  '087711229988', $reguler,    $kasir, 7.50, 'Selesai', $now->copy()->subDays(2),  $now->copy()->subHours(8)],
            ['LDR-0010', 'Maya Anggraini',  '081299001122', $superkilat, $admin, 3.00, 'Selesai', $now->copy()->subDays(1),  $now->copy()->subHours(19)],
            ['LDR-0011', 'Reza Mahendra',   '085233445566', $ekspres,    $kasir, 3.00, 'Proses',  $now->copy()->subHours(9), null],
            ['LDR-0012', 'Putri Wulandari', '081844552233', $reguler,    $kasir, 4.50, 'Proses',  $now->copy()->subHours(6), null],
            ['LDR-0013', 'Bagus Setiawan',  '082166554433', $superkilat, $admin, 2.50, 'Proses',  $now->copy()->subHours(3), null],
            ['LDR-0014', 'Aulia Rahmawati', '085877665544', $ekspres,    $kasir, 5.00, 'Proses',  $now->copy()->subHours(1), null],
        ];

        foreach ($rows as [$kode, $nama, $hp, $layanan, $petugas, $berat, $status, $masuk, $selesai]) {
            Transaksi::create([
                'kode_transaksi'       => $kode,
                'nama_pelanggan'       => $nama,
                'no_hp'                => $hp,
                'layanan_id'           => $layanan->id,
                'user_id'              => $petugas->id,
                'berat_kg'             => $berat,
                'harga_saat_transaksi' => $layanan->harga_per_kg,
                'total_harga'          => (int) round($berat * $layanan->harga_per_kg),
                'status'               => $status,
                'tanggal_masuk'        => $masuk,
                'tanggal_selesai'      => $selesai,
            ]);
        }
    }
}
