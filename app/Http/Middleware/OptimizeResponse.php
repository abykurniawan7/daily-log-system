<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add caching headers for static content
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            $response->header('Cache-Control', 'public, max-age=31536000');
        }

        // Enable gzip compression
        if (!$response->headers->has('Content-Encoding') && 
            function_exists('gzencode') && 
            $request->header('Accept-Encoding') && 
            str_contains($request->header('Accept-Encoding'), 'gzip')) {
            
            $content = $response->getContent();
            if ($content && strlen($content) > 860) {
                $response->setContent(gzencode($content, 6));
                $response->header('Content-Encoding', 'gzip');
                $response->header('Vary', 'Accept-Encoding');
            }
        }

        return $response;
    }
}