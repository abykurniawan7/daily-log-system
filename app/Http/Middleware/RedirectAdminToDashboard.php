<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user adalah admin dan mengakses dashboard
        if (auth()->check() && auth()->user()->role === 'admin' && $request->routeIs('dashboard')) {
            // Redirect ke User Management
            return redirect()->route('admin.users.index');
        }

        return $next($request);
    }
}