<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Available locales
        $availableLocales = config('app.available_locales', ['id', 'en']);
        $defaultLocale = config('app.locale', 'id');
        
        // Priority 1: Check session (user's explicit choice via language switcher)
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            
            if (in_array($sessionLocale, $availableLocales)) {
                App::setLocale($sessionLocale);
                return $next($request);
            }
        }
        
        // Priority 2: Check if user explicitly set language via URL parameter (legacy support)
        if ($request->has('lang')) {
            $requestedLocale = $request->get('lang');
            
            if (in_array($requestedLocale, $availableLocales)) {
                Session::put('locale', $requestedLocale);
                App::setLocale($requestedLocale);
                return $next($request);
            }
        }
        
        // Priority 3: Use default locale
        Session::put('locale', $defaultLocale);
        App::setLocale($defaultLocale);
        
        return $next($request);
    }
    
    /**
     * Detect locale from browser's Accept-Language header
     */
    private function detectBrowserLocale(Request $request, array $availableLocales, string $defaultLocale): string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return $defaultLocale;
        }
        
        // Parse Accept-Language header
        // Example: "en-US,en;q=0.9,id;q=0.8" → ['en-US' => 1.0, 'en' => 0.9, 'id' => 0.8]
        $languages = [];
        
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';q=', $lang);
            $locale = trim($parts[0]);
            $quality = isset($parts[1]) ? (float) $parts[1] : 1.0;
            
            // Extract primary language code (e.g., 'en-US' → 'en')
            $primaryLang = strtolower(substr($locale, 0, 2));
            
            if (!isset($languages[$primaryLang]) || $languages[$primaryLang] < $quality) {
                $languages[$primaryLang] = $quality;
            }
        }
        
        // Sort by quality (highest first)
        arsort($languages);
        
        // Find first available locale
        foreach (array_keys($languages) as $lang) {
            if (in_array($lang, $availableLocales)) {
                return $lang;
            }
        }
        
        // Default fallback
        return $defaultLocale;
    }
}