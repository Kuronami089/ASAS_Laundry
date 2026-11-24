<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller Autentikasi (Login & Logout untuk Role Admin dan Kasir)
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses login menggunakan username & password dari tabel users.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ],
            [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Kata sandi wajib diisi.',
            ]
        );

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $nama = Auth::user()->nama_lengkap;

            return redirect()
                ->route('dashboard')
                ->with('flash_type', 'success')
                ->with('flash_message', "Selamat datang, {$nama}.");
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => 'Username atau kata sandi salah. Periksa kembali akun Admin atau Kasir Anda.',
            ]);
    }

    /**
     * Menghapus session autentikasi saat logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('flash_type', 'success')
            ->with('flash_message', 'Anda berhasil keluar dari aplikasi.');
    }
}
