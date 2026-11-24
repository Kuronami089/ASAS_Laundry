<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Transaksi;
use Illuminate\View\View;

/**
 * Controller Dashboard Operasional
 * --------------------------------
 * Menampilkan ringkasan angka nyata dari database, tarif layanan aktif,
 * dan daftar antrean cucian yang sedang diproses (status Proses).
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $ringkasan = [
            'total_transaksi'  => Transaksi::count(),
            'total_proses'     => Transaksi::where('status', 'Proses')->count(),
            'total_selesai'    => Transaksi::where('status', 'Selesai')->count(),
            'total_pendapatan' => (int) Transaksi::sum('total_harga'),
        ];

        $antreanAktif = Transaksi::with('layanan')
            ->where('status', 'Proses')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        $daftarLayanan = Layanan::orderBy('harga_per_kg')->get();

        return view('dashboard.index', compact('ringkasan', 'antreanAktif', 'daftarLayanan'));
    }
}
