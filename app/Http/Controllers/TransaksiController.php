<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Transaksi;
use App\Support\FormatHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller Transaksi Laundry (Admin & Kasir)
 * --------------------------------------------
 * Menangani pencatatan transaksi baru (kalkulasi otomatis berat x harga_per_kg),
 * perubahan status dari Proses menjadi Selesai, serta penghapusan rekap pesanan.
 */
class TransaksiController extends Controller
{
    public function index(): View
    {
        $daftarLayanan = Layanan::orderBy('harga_per_kg')->orderBy('id')->get();

        $transaksiProses = Transaksi::with('layanan')
            ->where('status', 'Proses')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        return view('transaksi.index', compact('daftarLayanan', 'transaksiProses'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Normalisasi koma menjadi titik untuk input berat desimal
        if ($request->has('berat_kg')) {
            $request->merge([
                'berat_kg' => str_replace(',', '.', trim((string) $request->input('berat_kg'))),
            ]);
        }

        $validated = $request->validate(
            [
                'nama_pelanggan' => ['required', 'string', 'max:100'],
                'no_hp'          => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
                'layanan_id'     => ['required', 'integer', 'exists:layanan,id'],
                'berat_kg'       => ['required', 'numeric', 'min:0.1', 'max:500'],
            ],
            [
                'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
                'no_hp.required'          => 'Nomor HP pelanggan wajib diisi.',
                'no_hp.regex'             => 'Masukkan nomor HP yang valid (8 sampai 20 karakter angka).',
                'layanan_id.required'     => 'Pilih jenis layanan terlebih dahulu.',
                'layanan_id.exists'       => 'Jenis layanan yang dipilih tidak ditemukan.',
                'berat_kg.required'       => 'Berat cucian wajib diisi.',
                'berat_kg.numeric'        => 'Berat cucian harus berupa angka.',
                'berat_kg.min'            => 'Berat cucian harus lebih dari 0 kg.',
                'berat_kg.max'            => 'Berat cucian maksimal 500 kg per transaksi.',
            ]
        );

        $layanan    = Layanan::findOrFail($validated['layanan_id']);
        $beratKg    = round((float) $validated['berat_kg'], 2);
        $totalHarga = (int) round($beratKg * $layanan->harga_per_kg);
        $kode       = Transaksi::generateKode();

        Transaksi::create([
            'kode_transaksi'       => $kode,
            'nama_pelanggan'       => trim($validated['nama_pelanggan']),
            'no_hp'                => trim($validated['no_hp']),
            'layanan_id'           => $layanan->id,
            'user_id'              => Auth::id(),
            'berat_kg'             => $beratKg,
            'harga_saat_transaksi' => $layanan->harga_per_kg,
            'total_harga'          => $totalHarga,
            'status'               => 'Proses',
            'tanggal_masuk'        => now(),
        ]);

        $nominal = FormatHelper::rupiah($totalHarga);

        return redirect()
            ->route('transaksi.index')
            ->with('flash_type', 'success')
            ->with('flash_message', "Transaksi {$kode} untuk {$validated['nama_pelanggan']} berhasil dicatat dengan total {$nominal} (status: Proses).");
    }

    /**
     * Memperbarui status pengerjaan dari 'Proses' menjadi 'Selesai'.
     */
    public function markSelesai(Transaksi $transaksi): RedirectResponse
    {
        if ($transaksi->status !== 'Proses') {
            return back()
                ->with('flash_type', 'warning')
                ->with('flash_message', 'Transaksi ini sudah berstatus Selesai.');
        }

        $transaksi->update([
            'status'          => 'Selesai',
            'tanggal_selesai' => now(),
        ]);

        return back()
            ->with('flash_type', 'success')
            ->with('flash_message', "Status transaksi {$transaksi->kode_transaksi} berhasil diperbarui menjadi Selesai.");
    }

    /**
     * Menghapus satu transaksi terpilih dari rekap pesanan.
     */
    public function destroy(Transaksi $transaksi): RedirectResponse
    {
        $kode = $transaksi->kode_transaksi;
        $nama = $transaksi->nama_pelanggan;
        $transaksi->delete();

        return back()
            ->with('flash_type', 'success')
            ->with('flash_message', "Transaksi {$kode} ({$nama}) berhasil dihapus dari rekap pesanan.");
    }
}
