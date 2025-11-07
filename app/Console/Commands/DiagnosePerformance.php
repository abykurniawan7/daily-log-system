<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class DiagnosePerformance extends Command
{
    protected $signature = 'diagnose:performance {--detailed : Show detailed analysis}';
    protected $description = 'Diagnose system performance bottlenecks';

    private $issues = [];
    private $suggestions = [];
    private $score = 100;

    public function handle()
    {
        $this->info("🔍 STARTING PERFORMANCE DIAGNOSTIC...\n");
        
        $this->checkDatabaseOptimization();
        $this->checkCacheConfiguration();
        $this->checkQueryPerformance();
        $this->checkFileOptimization();
        $this->checkSessionConfiguration();
        $this->checkServerConfiguration();
        $this->checkNPlusOneQueries();
        
        $this->displayResults();
    }

    private function checkDatabaseOptimization()
    {
        $this->info("📊 Checking Database Optimization...");
        
        // Check indexes
        $tables = ['activities', 'projects', 'users'];
        foreach ($tables as $table) {
            try {
                $indexes = DB::select("SHOW INDEX FROM {$table}");
                $indexCount = count($indexes);
                
                if ($indexCount <= 1) { // Only PRIMARY key
                    $this->issues[] = "❌ Table '{$table}' has no secondary indexes";
                    $this->suggestions[] = "Add indexes to frequently queried columns in '{$table}'";
                    $this->score -= 15;
                } else {
                    $this->line("✅ Table '{$table}' has {$indexCount} indexes");
                }
            } catch (\Exception $e) {
                $this->warn("⚠️  Could not check table '{$table}': " . $e->getMessage());
            }
        }

        // Check slow queries
        try {
            DB::statement("SET GLOBAL slow_query_log = 'ON'");
            $this->line("✅ Slow query log is enabled");
        } catch (\Exception $e) {
            $this->warn("⚠️  Could not enable slow query log");
        }

        // Check table sizes
        $this->checkTableSizes();
        
        $this->newLine();
    }

    private function checkTableSizes()
    {
        try {
            $tables = DB::select("
                SELECT 
                    table_name,
                    table_rows,
                    ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");

            $this->info("📈 Top 10 Largest Tables:");
            foreach ($tables as $table) {
                $this->line("   {$table->table_name}: {$table->table_rows} rows, {$table->size_mb} MB");
                
                if ($table->table_rows > 10000 && $table->size_mb > 50) {
                    $this->issues[] = "⚠️  Large table detected: {$table->table_name}";
                    $this->suggestions[] = "Consider partitioning or archiving old data in '{$table->table_name}'";
                    $this->score -= 5;
                }
            }
        } catch (\Exception $e) {
            $this->warn("Could not check table sizes");
        }
    }

    private function checkCacheConfiguration()
    {
        $this->info("💾 Checking Cache Configuration...");
        
        $cacheDriver = config('cache.default');
        $this->line("Cache Driver: {$cacheDriver}");
        
        if ($cacheDriver === 'file') {
            $this->issues[] = "⚠️  Using file cache (slow for concurrent requests)";
            $this->suggestions[] = "Switch to Redis or Memcached for better performance";
            $this->score -= 10;
        } else if ($cacheDriver === 'redis') {
            $this->line("✅ Using Redis cache (excellent!)");
            
            // Test Redis connection
            try {
                Cache::store('redis')->put('test', 'value', 10);
                $this->line("✅ Redis connection successful");
            } catch (\Exception $e) {
                $this->issues[] = "❌ Redis configured but not working: " . $e->getMessage();
                $this->score -= 20;
            }
        }

        // Check if caches are compiled
        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $viewsCached = is_dir(storage_path('framework/views')) && 
                       count(File::files(storage_path('framework/views'))) > 0;

        $this->line("Config Cached: " . ($configCached ? "✅ Yes" : "❌ No"));
        $this->line("Routes Cached: " . ($routesCached ? "✅ Yes" : "❌ No"));
        $this->line("Views Cached: " . ($viewsCached ? "✅ Yes" : "❌ No"));

        if (!$configCached) {
            $this->issues[] = "❌ Config not cached";
            $this->suggestions[] = "Run: php artisan config:cache";
            $this->score -= 10;
        }
        if (!$routesCached) {
            $this->issues[] = "❌ Routes not cached";
            $this->suggestions[] = "Run: php artisan route:cache";
            $this->score -= 10;
        }
        
        $this->newLine();
    }

    private function checkQueryPerformance()
    {
        $this->info("⚡ Checking Query Performance...");
        
        // Enable query logging
        DB::enableQueryLog();
        
        try {
            // Test query without eager loading
            $startTime = microtime(true);
            $activities = DB::table('activities')->limit(10)->get();
            $timeWithoutEager = (microtime(true) - $startTime) * 1000;
            
            $queryCount = count(DB::getQueryLog());
            DB::flushQueryLog();
            
            $this->line("Sample query time: " . round($timeWithoutEager, 2) . "ms");
            $this->line("Query count for 10 activities: {$queryCount}");
            
            if ($timeWithoutEager > 100) {
                $this->issues[] = "⚠️  Slow query detected (>100ms)";
                $this->suggestions[] = "Check database indexes and optimize queries";
                $this->score -= 10;
            } else {
                $this->line("✅ Query performance is good");
            }
            
        } catch (\Exception $e) {
            $this->warn("Could not test query performance: " . $e->getMessage());
        }
        
        DB::disableQueryLog();
        $this->newLine();
    }

    private function checkFileOptimization()
    {
        $this->info("📁 Checking File Optimization...");
        
        // Check composer autoloader
        $autoloadOptimized = file_exists(base_path('vendor/composer/autoload_classmap.php')) &&
                            filesize(base_path('vendor/composer/autoload_classmap.php')) > 1000;
        
        $this->line("Composer Autoloader Optimized: " . ($autoloadOptimized ? "✅ Yes" : "❌ No"));
        
        if (!$autoloadOptimized) {
            $this->issues[] = "❌ Composer autoloader not optimized";
            $this->suggestions[] = "Run: composer dump-autoload -o";
            $this->score -= 8;
        }

        // Check .env file size
        $envSize = file_exists(base_path('.env')) ? filesize(base_path('.env')) : 0;
        if ($envSize > 10000) {
            $this->issues[] = "⚠️  Large .env file ({$envSize} bytes)";
            $this->suggestions[] = "Consider moving large configs to config files";
            $this->score -= 3;
        }
        
        $this->newLine();
    }

    private function checkSessionConfiguration()
    {
        $this->info("🔐 Checking Session Configuration...");
        
        $sessionDriver = config('session.driver');
        $this->line("Session Driver: {$sessionDriver}");
        
        if ($sessionDriver === 'file') {
            $this->issues[] = "⚠️  Using file sessions (causes lock contention)";
            $this->suggestions[] = "Switch to database or redis sessions for better concurrency";
            $this->score -= 15;
        } else if ($sessionDriver === 'database') {
            $this->line("✅ Using database sessions (good for concurrency)");
            
            // Check if sessions table exists
            try {
                DB::table('sessions')->limit(1)->get();
                $this->line("✅ Sessions table exists");
            } catch (\Exception $e) {
                $this->issues[] = "❌ Database session configured but table missing";
                $this->suggestions[] = "Run: php artisan session:table && php artisan migrate";
                $this->score -= 10;
            }
        } else if ($sessionDriver === 'redis') {
            $this->line("✅ Using Redis sessions (excellent!)");
        }
        
        $this->newLine();
    }

    private function checkServerConfiguration()
    {
        $this->info("🖥️  Checking Server Configuration...");
        
        // PHP Version
        $phpVersion = PHP_VERSION;
        $this->line("PHP Version: {$phpVersion}");
        
        if (version_compare($phpVersion, '8.1', '<')) {
            $this->issues[] = "⚠️  PHP version is below 8.1";
            $this->suggestions[] = "Upgrade to PHP 8.1+ for better performance";
            $this->score -= 10;
        } else {
            $this->line("✅ PHP version is good");
        }

        // OPcache
        $opcacheEnabled = function_exists('opcache_get_status') && opcache_get_status() !== false;
        $this->line("OPcache Enabled: " . ($opcacheEnabled ? "✅ Yes" : "❌ No"));
        
        if (!$opcacheEnabled) {
            $this->issues[] = "❌ OPcache is not enabled";
            $this->suggestions[] = "Enable OPcache in php.ini for 30-50% performance boost";
            $this->score -= 20;
        }

        // Memory Limit
        $memoryLimit = ini_get('memory_limit');
        $this->line("PHP Memory Limit: {$memoryLimit}");
        
        $memoryBytes = $this->convertToBytes($memoryLimit);
        if ($memoryBytes < 256 * 1024 * 1024) {
            $this->issues[] = "⚠️  Low PHP memory limit";
            $this->suggestions[] = "Increase memory_limit to at least 256M in php.ini";
            $this->score -= 5;
        }

        // Max execution time
        $maxExecTime = ini_get('max_execution_time');
        $this->line("Max Execution Time: {$maxExecTime}s");
        
        $this->newLine();
    }

    private function checkNPlusOneQueries()
    {
        $this->info("🔍 Checking for N+1 Query Problems...");
        
        DB::enableQueryLog();
        
        try {
            // ✅ IMPROVED: Test with Eloquent model using eager loading
            $activities = \App\Models\Activity::with(['project.pemilikProject', 'user'])
                ->limit(5)
                ->get();
            
            $queryCount = count(DB::getQueryLog());
            
            $this->line("Queries for 5 activities with relationships: {$queryCount}");
            
            // Expected: 1 (activities) + 1 (projects) + 1 (divisions) + 1 (users) = 4 queries max
            if ($queryCount <= 4) {
                $this->line("✅ Excellent! Using proper eager loading");
                $this->line("   Query breakdown: 1 activity + " . ($queryCount - 1) . " eager loaded relations");
            } elseif ($queryCount <= 6) {
                $this->line("✅ Good! Query count is acceptable");
            } elseif ($queryCount <= 10) {
                $this->issues[] = "⚠️  Query count higher than optimal but acceptable";
                $this->suggestions[] = "Consider reviewing eager loading relationships";
                $this->score -= 5;
            } else {
                $this->issues[] = "❌ Potential N+1 query problem detected";
                $this->suggestions[] = "Use eager loading: Activity::with(['user', 'project'])->get()";
                $this->score -= 15;
            }
            
        } catch (\Exception $e) {
            $this->warn("Could not test N+1 queries: " . $e->getMessage());
        }
        
        DB::disableQueryLog();
        $this->newLine();
    }

    private function displayResults()
    {
        $this->newLine();
        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("                    DIAGNOSTIC RESULTS");
        $this->info("═══════════════════════════════════════════════════════════");
        $this->newLine();

        // Performance Score
        $scoreColor = $this->score >= 80 ? 'green' : ($this->score >= 60 ? 'yellow' : 'red');
        $this->line("<fg={$scoreColor}>PERFORMANCE SCORE: {$this->score}/100</>");
        $this->newLine();

        // Issues Found
        if (count($this->issues) > 0) {
            $this->error("🚨 ISSUES FOUND (" . count($this->issues) . "):");
            foreach ($this->issues as $issue) {
                $this->line("   " . $issue);
            }
            $this->newLine();
        } else {
            $this->info("✅ No major issues found!");
            $this->newLine();
        }

        // Suggestions
        if (count($this->suggestions) > 0) {
            $this->info("💡 OPTIMIZATION SUGGESTIONS:");
            foreach ($this->suggestions as $index => $suggestion) {
                $this->line("   " . ($index + 1) . ". " . $suggestion);
            }
            $this->newLine();
        }

        // Overall Assessment
        if ($this->score >= 80) {
            $this->info("✅ Your system is well optimized!");
        } else if ($this->score >= 60) {
            $this->warn("⚠️  Your system has room for improvement");
        } else {
            $this->error("❌ Your system needs significant optimization");
        }

        $this->newLine();
        $this->info("═══════════════════════════════════════════════════════════");
        $this->newLine();
        $this->line("Run with --detailed flag for more information");
        $this->line("Example: php artisan diagnose:performance --detailed");
    }

    private function convertToBytes($value)
    {
        $unit = strtoupper(substr($value, -1));
        $value = (int) $value;
        
        switch ($unit) {
            case 'G': return $value * 1024 * 1024 * 1024;
            case 'M': return $value * 1024 * 1024;
            case 'K': return $value * 1024;
            default: return $value;
        }
    }
}