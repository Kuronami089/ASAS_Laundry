<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routing Web ASAS Laundry (Laravel)
|--------------------------------------------------------------------------
| - Guest      : Halaman Login (/login)
| - Auth       : Dashboard, Transaksi Laundry, Rekap Transaksi, Cetak Struk, Logout
| - Role Admin : CRUD Data Layanan (/layanan)
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Route Tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Route Pengguna Terautentikasi (Admin & Kasir)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi Laundry (Admin & Kasir)
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::patch('/transaksi/{transaksi}/selesai', [TransaksiController::class, 'markSelesai'])->name('transaksi.selesai');
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

    // Rekap Transaksi & Cetak Struk (Admin & Kasir)
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/{transaksi}/struk', [RekapController::class, 'struk'])->name('rekap.struk');

    // CRUD Data Layanan (Khusus Admin — dilindungi RoleMiddleware)
    Route::middleware('role:admin')->group(function () {
        Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
        Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
        Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('layanan.update');
        Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    });
});
