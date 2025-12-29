<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKabagPGB
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN role-nya kabag_pgb
        if (auth()->check() && auth()->user()->role === 'kabag_pgb') {
            return $next($request); // ✅ Lolos, lanjut ke controller
        }

        // ❌ Kalau bukan kabag_pgb, redirect ke dashboard dengan error
        return redirect('/dashboard')->with('error', 'Akses ditolak! Hanya Kepala Bagian PGB yang bisa mengakses halaman ini.');
    }
}