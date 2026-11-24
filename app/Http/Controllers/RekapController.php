<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller Rekap Transaksi Laundry & Cetak Struk
 * ------------------------------------------------
 * Menggunakan SQL INNER JOIN antara tabel transaksi dan layanan
 * (serta LEFT JOIN ke tabel users) untuk laporan keuangan yang akurat.
 */
class RekapController extends Controller
{
    public function index(Request $request): View
    {
        $keyword   = trim((string) $request->query('q', ''));
        $status    = (string) $request->query('status', 'semua');
        $layananId = (int) $request->query('layanan_id', 0);

        if (!in_array($status, ['semua', 'Proses', 'Selesai'], true)) {
            $status = 'semua';
        }

        // Query SQL JOIN eksplisit antara tabel transaksi, layanan, dan users
        $query = DB::table('transaksi as t')
            ->join('layanan as l', 't.layanan_id', '=', 'l.id')
            ->leftJoin('users as u', 't.user_id', '=', 'u.id')
            ->select([
                't.id',
                't.kode_transaksi',
                't.nama_pelanggan',
                't.no_hp',
                'l.nama_layanan',
                'l.estimasi_selesai',
                't.berat_kg',
                't.harga_saat_transaksi',
                't.total_harga',
                't.status',
                't.tanggal_masuk',
                't.tanggal_selesai',
                'u.nama_lengkap as nama_petugas',
            ]);

        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $query->where(function ($sub) use ($like): void {
                $sub->where('t.nama_pelanggan', 'like', $like)
                    ->orWhere('t.kode_transaksi', 'like', $like)
                    ->orWhere('t.no_hp', 'like', $like);
            });
        }

        if ($status !== 'semua') {
            $query->where('t.status', $status);
        }

        if ($layananId > 0) {
            $query->where('t.layanan_id', $layananId);
        }

        $rekapTransaksi = $query
            ->orderByDesc('t.tanggal_masuk')
            ->orderByDesc('t.id')
            ->get();

        $jumlahTransaksi = $rekapTransaksi->count();
        $jumlahProses    = $rekapTransaksi->where('status', 'Proses')->count();
        $jumlahSelesai   = $rekapTransaksi->where('status', 'Selesai')->count();
        $totalPendapatan = (int) $rekapTransaksi->sum('total_harga');
        $hasActiveFilter = ($keyword !== '' || $status !== 'semua' || $layananId > 0);
        $daftarLayanan   = Layanan::orderBy('nama_layanan')->get();

        return view('rekap.index', compact(
            'rekapTransaksi',
            'daftarLayanan',
            'keyword',
            'status',
            'layananId',
            'jumlahTransaksi',
            'jumlahProses',
            'jumlahSelesai',
            'totalPendapatan',
            'hasActiveFilter'
        ));
    }

    /**
     * Menampilkan halaman struk resmi siap cetak untuk satu transaksi.
     */
    public function struk(Transaksi $transaksi): View
    {
        $transaksi->load(['layanan', 'petugas']);

        return view('rekap.struk', ['trx' => $transaksi]);
    }
}
