<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Pembatasan Hak Akses Berdasarkan Role (Admin / Kasir)
 * ----------------------------------------------------------------
 * Dipakai pada route Data Layanan (`middleware('role:admin')`) agar
 * hanya Admin yang dapat menambah, mengubah, dan menghapus paket layanan.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== $role) {
            return redirect()
                ->route('dashboard')
                ->with('flash_type', 'error')
                ->with('flash_message', 'Akses ditolak: pengelolaan Data Layanan hanya dapat dilakukan oleh Admin.');
        }

        return $next($request);
    }
}
