<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller CRUD Data Layanan (Khusus Admin)
 * -------------------------------------------
 * Menangani tambah, lihat, ubah, dan hapus paket layanan cucian
 * lengkap dengan validasi harga > 0 dan proteksi Foreign Key saat hapus.
 */
class LayananController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $editLayanan = null;
        if ($request->filled('edit')) {
            $editLayanan = Layanan::find((int) $request->query('edit'));
            if (!$editLayanan) {
                return redirect()
                    ->route('layanan.index')
                    ->with('flash_type', 'error')
                    ->with('flash_message', 'Data layanan yang akan diubah tidak ditemukan.');
            }
        }

        // Hitung jumlah transaksi per layanan menggunakan withCount (relasi hasMany)
        $daftarLayanan = Layanan::withCount('transaksi')
            ->orderBy('harga_per_kg')
            ->orderBy('id')
            ->get();

        return view('layanan.index', compact('daftarLayanan', 'editLayanan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'nama_layanan'     => ['required', 'string', 'max:100'],
                'harga_per_kg'     => ['required', 'integer', 'min:1'],
                'estimasi_selesai' => ['required', 'string', 'max:60'],
            ],
            [
                'nama_layanan.required'     => 'Nama layanan wajib diisi.',
                'harga_per_kg.required'     => 'Harga per kg wajib diisi berupa angka.',
                'harga_per_kg.integer'      => 'Harga per kg harus berupa angka bulat.',
                'harga_per_kg.min'          => 'Harga per kg harus lebih besar dari 0 (tidak boleh negatif atau nol).',
                'estimasi_selesai.required' => 'Estimasi selesai wajib diisi (contoh: 1-2 hari).',
            ]
        );

        $layanan = Layanan::create($validated);

        return redirect()
            ->route('layanan.index')
            ->with('flash_type', 'success')
            ->with('flash_message', "Layanan baru \"{$layanan->nama_layanan}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Layanan $layanan): RedirectResponse
    {
        $validated = $request->validate(
            [
                'nama_layanan'     => ['required', 'string', 'max:100'],
                'harga_per_kg'     => ['required', 'integer', 'min:1'],
                'estimasi_selesai' => ['required', 'string', 'max:60'],
            ],
            [
                'nama_layanan.required'     => 'Nama layanan wajib diisi.',
                'harga_per_kg.required'     => 'Harga per kg wajib diisi berupa angka.',
                'harga_per_kg.integer'      => 'Harga per kg harus berupa angka bulat.',
                'harga_per_kg.min'          => 'Harga per kg harus lebih besar dari 0 (tidak boleh negatif atau nol).',
                'estimasi_selesai.required' => 'Estimasi selesai wajib diisi (contoh: 1-2 hari).',
            ]
        );

        $layanan->update($validated);

        return redirect()
            ->route('layanan.index')
            ->with('flash_type', 'success')
            ->with('flash_message', "Perubahan pada layanan \"{$layanan->nama_layanan}\" berhasil disimpan.");
    }

    public function destroy(Layanan $layanan): RedirectResponse
    {
        // Lapis 1: Cek relasi transaksi sebelum menghapus agar laporan tetap utuh
        $dipakai = $layanan->transaksi()->count();
        if ($dipakai > 0) {
            return redirect()
                ->route('layanan.index')
                ->with('flash_type', 'error')
                ->with('flash_message', "Layanan \"{$layanan->nama_layanan}\" tidak dapat dihapus karena masih digunakan pada {$dipakai} transaksi. Data laporan tetap terlindungi.");
        }

        // Lapis 2: Tangkap error Foreign Key Constraint MySQL (ON DELETE RESTRICT)
        try {
            $nama = $layanan->nama_layanan;
            $layanan->delete();

            return redirect()
                ->route('layanan.index')
                ->with('flash_type', 'success')
                ->with('flash_message', "Layanan \"{$nama}\" berhasil dihapus.");
        } catch (QueryException) {
            return redirect()
                ->route('layanan.index')
                ->with('flash_type', 'error')
                ->with('flash_message', "Layanan \"{$layanan->nama_layanan}\" masih terhubung dengan data transaksi sehingga tidak dapat dihapus.");
        }
    }
}
