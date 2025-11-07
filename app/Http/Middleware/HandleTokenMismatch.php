<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleTokenMismatch
{
    /**
     * Handle token mismatch exception and redirect to homepage
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Log the event for debugging
            \Log::warning('CSRF Token Mismatch detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
            
            // Logout user if authenticated
            if (auth()->check()) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            
            // Handle AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan refresh halaman.',
                    'redirect' => url('/')
                ], 419);
            }
            
            // Redirect to homepage with notification
            return redirect('/')
                ->with('info', 'Sesi Anda telah berakhir. Silakan login kembali jika ingin melanjutkan.');
        }
    }
}