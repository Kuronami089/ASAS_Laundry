<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Transaksi;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller Rekap Transaksi Laundry & Cetak Struk
 * ------------------------------------------------
 * Menggunakan SQL INNER JOIN antara tabel transaksi dan layanan
 * (serta LEFT JOIN ke tabel users), dilengkapi pengelompokan rapi
 * (Per Status, Per Layanan, Per Tanggal) dan mode Per Halaman agar
 * tidak perlu scroll panjang saat data banyak.
 */
class RekapController extends Controller
{
    public function index(Request $request): View
    {
        $keyword   = trim((string) $request->query('q', ''));
        $status    = (string) $request->query('status', 'semua');
        $layananId = (int) $request->query('layanan_id', 0);
        $groupBy   = (string) $request->query('group', 'status');

        if (!in_array($status, ['semua', 'Proses', 'Selesai'], true)) {
            $status = 'semua';
        }

        if (!in_array($groupBy, ['status', 'layanan', 'tanggal', 'halaman'], true)) {
            $groupBy = 'status';
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

        // Mode Per Halaman (5 baris per halaman) atau Mode Per Grup (<details>)
        $paginatedRows = null;
        $groupedRekap  = collect();

        if ($groupBy === 'halaman') {
            $perPage = 5;
            $page    = max(1, (int) $request->query('page', 1));
            $items   = $rekapTransaksi->forPage($page, $perPage)->values();

            $paginatedRows = new LengthAwarePaginator(
                $items,
                $jumlahTransaksi,
                $perPage,
                $page,
                [
                    'path'  => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            $groupedRekap = $this->buildGroups($rekapTransaksi, $groupBy, $hasActiveFilter);
        }

        return view('rekap.index', compact(
            'rekapTransaksi',
            'groupedRekap',
            'paginatedRows',
            'groupBy',
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
     * Mengelompokkan hasil query JOIN berdasarkan mode grup pilihan pengguna.
     * Hanya grup prioritas pertama yang dibuka default agar layar tetap ringkas.
     */
    private function buildGroups(Collection $rows, string $groupBy, bool $openAll): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        if ($groupBy === 'status') {
            $buckets = [
                'Proses'  => ['title' => 'Sedang diproses', 'badge' => 'proses',  'items' => $rows->where('status', 'Proses')->values()],
                'Selesai' => ['title' => 'Transaksi selesai', 'badge' => 'selesai', 'items' => $rows->where('status', 'Selesai')->values()],
            ];

            $result = collect();
            foreach ($buckets as $key => $bucket) {
                if ($bucket['items']->isEmpty()) {
                    continue;
                }
                $result->push([
                    'key'      => $key,
                    'title'    => $bucket['title'],
                    'badge'    => $bucket['badge'],
                    'count'    => $bucket['items']->count(),
                    'subtotal' => (int) $bucket['items']->sum('total_harga'),
                    'open'     => $openAll || $key === 'Proses' || $result->isEmpty(),
                    'items'    => $bucket['items'],
                ]);
            }

            return $result;
        }

        if ($groupBy === 'layanan') {
            $grouped = $rows->groupBy('nama_layanan');
            $index   = 0;

            return $grouped->map(function (Collection $items, string $namaLayanan) use (&$index, $openAll): array {
                $isOpen = $openAll || $index === 0;
                $index++;

                return [
                    'key'      => $namaLayanan,
                    'title'    => 'Layanan ' . $namaLayanan,
                    'badge'    => 'neutral',
                    'count'    => $items->count(),
                    'subtotal' => (int) $items->sum('total_harga'),
                    'open'     => $isOpen,
                    'items'    => $items->values(),
                ];
            })->values();
        }

        // Default fallback: groupBy === 'tanggal'
        $grouped = $rows->groupBy(fn ($row) => date('Y-m-d', strtotime((string) $row->tanggal_masuk)));
        $index   = 0;

        return $grouped->map(function (Collection $items, string $ymd) use (&$index, $openAll): array {
            $isOpen = $openAll || $index === 0;
            $index++;

            $labelTanggal = explode(',', FormatHelper::tanggal($ymd . ' 00:00:00'))[0];

            return [
                'key'      => $ymd,
                'title'    => $labelTanggal,
                'badge'    => 'neutral',
                'count'    => $items->count(),
                'subtotal' => (int) $items->sum('total_harga'),
                'open'     => $isOpen,
                'items'    => $items->values(),
            ];
        })->values();
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
