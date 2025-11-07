<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupervisiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN role-nya supervisi
        if (auth()->check() && auth()->user()->role === 'supervisi') {
            return $next($request); // Lolos, lanjut ke controller
        }

        // Kalau bukan supervisi, redirect ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Akses ditolak! Hanya supervisi yang bisa mengakses halaman ini.');
    }
}