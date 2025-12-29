<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = ['supervisi', 'kabag_pgb', 'perizinan'];
        
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized. Only Supervisi, Kabag PGB, and PKJ can access employees.');
        }
        
        return $next($request);
    }
}