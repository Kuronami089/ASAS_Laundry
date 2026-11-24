<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller Dashboard Operasional
 * --------------------------------
 * Menampilkan ringkasan metrik nyata dari database, distribusi pendapatan
 * per layanan untuk grafik donut interaktif, daftar tarif layanan aktif,
 * dan antrean cucian yang sedang diproses (status Proses).
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

        // Data distribusi pendapatan per layanan untuk donut chart (diurutkan terbesar dulu)
        $distribusiLayanan = DB::table('layanan as l')
            ->leftJoin('transaksi as t', 't.layanan_id', '=', 'l.id')
            ->select([
                'l.id',
                'l.nama_layanan as name',
                DB::raw('COUNT(t.id) as orders'),
                DB::raw('COALESCE(SUM(t.total_harga), 0) as value'),
            ])
            ->groupBy('l.id', 'l.nama_layanan')
            ->orderByDesc('value')
            ->orderBy('l.id')
            ->get()
            ->map(fn ($row) => [
                'name'   => (string) $row->name,
                'orders' => (int) $row->orders,
                'value'  => (int) $row->value,
            ])
            ->values();

        $antreanAktif = Transaksi::with('layanan')
            ->where('status', 'Proses')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        $daftarLayanan = Layanan::orderBy('harga_per_kg')->get();

        return view('dashboard.index', compact(
            'ringkasan',
            'distribusiLayanan',
            'antreanAktif',
            'daftarLayanan'
        ));
    }
}
