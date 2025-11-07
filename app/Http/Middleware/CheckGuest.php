<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user adalah guest
        if (auth()->check() && auth()->user()->role === 'guest') {
            // PENTING: Allow logout route - jangan block!
            if ($request->routeIs('logout')) {
                return $next($request);
            }
            
            // Allow dashboard dan profile route
            if ($request->routeIs('dashboard') || $request->routeIs('profile.*')) {
                return $next($request);
            }
            
            // Allow role-requests routes untuk guest bisa submit form
            if ($request->routeIs('role-requests.*')) {
                return $next($request);
            }
            
            // ✅ FIXED: Allow language switcher (set-locale)
            if ($request->routeIs('set-locale')) {
                return $next($request);
            }
            
            // ✅ FIXED: Allow API routes (keep-alive, session-status, dll)
            if ($request->routeIs('api.*')) {
                return $next($request);
            }
            
            // Block semua route lain, redirect ke dashboard dengan error
            return redirect()->route('dashboard')->with('error', 'Akun Anda masih dalam status Guest. Silakan hubungi administrator untuk aktivasi.');
        }

        return $next($request);
    }
}