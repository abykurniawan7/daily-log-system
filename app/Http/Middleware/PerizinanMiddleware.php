<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerizinanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'perizinan') {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Akses ditolak! Hanya perizinan PKJ yang bisa mengakses halaman ini.');
    }
}