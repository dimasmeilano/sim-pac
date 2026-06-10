<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AksesPengurus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. Pastikan user sudah login
        if (!$user) {
            return redirect('login');
        }

        // 2. Jika user HANYA memiliki role 'anggota' (atau sejenisnya)
        // Sesuaikan nama role 'anggota' dengan yang ada di database Anda
        if ($user->hasRole('anggota_biasa') && $user->roles->count() == 1) {
            // Lempar ke halaman "Portal Anggota"
            return redirect()->route('anggota.portal');
        }

        // 3. Jika user baru daftar dan belum punya role sama sekali
        if ($user->roles->count() == 0) {
            abort(403, 'Akses Ditolak: Akun Anda belum diverifikasi atau belum diberi jabatan oleh Admin.');
        }

        // 4. Jika dia Pengurus (punya role selain anggota murni), silakan masuk!
        return $next($request);
    }
}
