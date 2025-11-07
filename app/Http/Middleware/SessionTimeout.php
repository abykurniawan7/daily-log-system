<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip untuk route API keep-alive
        if ($request->is('api/keep-alive')) {
            return $next($request);
        }

        // Skip untuk guest (belum login)
        if (!Auth::check()) {
            return $next($request);
        }

        // Get session timeout dari config (default 30 menit = 1800 detik)
        $timeout = config('session.lifetime') * 60; // Convert menit ke detik
        
        // Get last activity time
        $lastActivity = Session::get('lastActivityTime');
        
        if ($lastActivity) {
            $elapsedTime = time() - $lastActivity;
            
            // Jika sudah timeout
            if ($elapsedTime > $timeout) {
                // ✅ LOGOUT MANUAL (tanpa redirect ke route POST)
                Auth::logout();
                Session::flush();
                Session::regenerate();
                
                // ✅ Redirect ke login dengan pesan
                return redirect()->route('login')
                    ->with('warning', 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.');
            }
        }
        
        // Update last activity time
        Session::put('lastActivityTime', time());
        
        return $next($request);
    }
}