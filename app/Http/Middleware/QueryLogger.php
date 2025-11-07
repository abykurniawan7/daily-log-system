<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QueryLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only enable in local environment
        if (!app()->environment('local')) {
            return $next($request);
        }
        
        // Enable query logging
        DB::enableQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Process request
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        // Get query log
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Calculate metrics
        $executionTime = round(($endTime - $startTime) * 1000, 2); // ms
        $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB
        
        // Detect N+1 problems
        $hasN1Problem = $this->detectN1Problem($queries);
        $slowQueries = $this->getSlowQueries($queries);
        
        // Log to file
        $logData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'query_count' => $queryCount,
            'execution_time' => $executionTime . 'ms',
            'memory_used' => $memoryUsed . 'MB',
            'has_n1_problem' => $hasN1Problem,
            'slow_queries_count' => count($slowQueries),
        ];
        
        // Color-coded console output
        if ($queryCount > 20 || $hasN1Problem || count($slowQueries) > 0) {
            Log::warning('⚠️ Performance Issue Detected', $logData);
        } else {
            Log::info('✅ Request Processed', $logData);
        }
        
        // Add debug header (visible in browser DevTools)
        if (app()->environment('local')) {
            $response->headers->set('X-Query-Count', $queryCount);
            $response->headers->set('X-Execution-Time', $executionTime . 'ms');
            $response->headers->set('X-Memory-Used', $memoryUsed . 'MB');
            
            if ($hasN1Problem) {
                $response->headers->set('X-N1-Problem', 'Detected');
            }
        }
        
        DB::disableQueryLog();
        
        return $response;
    }
    
    /**
     * Detect N+1 query problem
     */
    private function detectN1Problem(array $queries): bool
    {
        if (count($queries) < 10) {
            return false; // Not enough queries to be N+1
        }
        
        // Group queries by SQL pattern
        $patterns = [];
        
        foreach ($queries as $query) {
            // Normalize query (remove specific IDs)
            $normalized = preg_replace('/\d+/', '?', $query['query']);
            
            if (!isset($patterns[$normalized])) {
                $patterns[$normalized] = 0;
            }
            
            $patterns[$normalized]++;
        }
        
        // If any pattern repeats more than 5 times, it's likely N+1
        foreach ($patterns as $count) {
            if ($count > 5) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get slow queries (>100ms)
     */
    private function getSlowQueries(array $queries): array
    {
        $slowQueries = [];
        
        foreach ($queries as $query) {
            if ($query['time'] > 100) {
                $slowQueries[] = [
                    'query' => $query['query'],
                    'time' => $query['time'] . 'ms',
                ];
            }
        }
        
        return $slowQueries;
    }
}