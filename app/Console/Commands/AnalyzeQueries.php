<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Activity;
use App\Models\Project;

class AnalyzeQueries extends Command
{
    protected $signature = 'analyze:queries {route? : Specific route to analyze}';
    protected $description = 'Analyze queries for N+1 problems on specific routes';

    public function handle()
    {
        $this->info("🔍 ADVANCED QUERY ANALYZER\n");
        
        $routeName = $this->argument('route');
        
        if ($routeName) {
            $this->analyzeSpecificRoute($routeName);
        } else {
            $this->showAvailableRoutes();
        }
    }

    private function showAvailableRoutes()
    {
        $this->info("📋 Available Routes for Analysis:\n");
        
        $routes = [
            'dashboard' => 'Dashboard (index)',
            'activities.index' => 'Activities List',
            'activities.show' => 'Activity Detail (need ID)',
            'activities.my-activities' => 'My Activities',
            'projects.index' => 'Projects List',
            'projects.show' => 'Project Detail (need ID)',
        ];
        
        foreach ($routes as $name => $description) {
            $this->line("   {$name} → {$description}");
        }
        
        $this->newLine();
        $this->info("Usage: php artisan analyze:queries {route-name}");
        $this->line("Example: php artisan analyze:queries dashboard");
    }

    private function analyzeSpecificRoute($routeName)
    {
        $this->info("Analyzing route: {$routeName}\n");
        
        // Enable query logging
        DB::enableQueryLog();
        
        try {
            switch ($routeName) {
                case 'dashboard':
                    $this->analyzeDashboard();
                    break;
                    
                case 'activities.index':
                    $this->analyzeActivitiesIndex();
                    break;
                    
                case 'activities.my-activities':
                    $this->analyzeMyActivities();
                    break;
                    
                default:
                    $this->error("Route '{$routeName}' is not configured for analysis.");
                    $this->info("Run without arguments to see available routes.");
                    return;
            }
            
            $this->displayQueryAnalysis();
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
        
        DB::disableQueryLog();
    }

    private function analyzeDashboard()
    {
        $this->info("📊 Simulating Dashboard Query...\n");
        
        $user = auth()->user() ?? \App\Models\User::where('role', 'supervisi')->first();
        
        if (!$user) {
            $this->error("No user found for testing. Please login first.");
            return;
        }
        
        $this->line("Testing as: {$user->name} ({$user->role})");
        $this->newLine();
        
        // Simulate dashboard queries
        if ($user->role === 'supervisi') {
            $totalProjects = Project::count();
            $totalActivities = Activity::count();
            
            // This is the critical part - recent activities
            $recentActivities = Activity::with([
                'project.pemilikProject',
                'project.picProyek',
                'user'
            ])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Simulate blade access
            foreach ($recentActivities as $activity) {
                $temp = $activity->project->nama_project;
                $temp = $activity->project->pemilikProject->nama_divisi ?? 'N/A';
                $temp = $activity->project->picProyek->name ?? 'N/A';
                $temp = $activity->user->name;
            }
            
        } else {
            $totalProjects = Project::where('pic_proyek_id', $user->id)->count();
            $totalActivities = Activity::where('user_id', $user->id)->count();
            
            $recentActivities = Activity::with([
                'project.pemilikProject',
                'project.picProyek',
                'user'
            ])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($recentActivities as $activity) {
                $temp = $activity->project->nama_project;
                $temp = $activity->project->pemilikProject->nama_divisi ?? 'N/A';
                $temp = $activity->project->picProyek->name ?? 'N/A';
                $temp = $activity->user->name;
            }
        }
    }

    private function analyzeActivitiesIndex()
    {
        $this->info("📋 Simulating Activities Index...\n");
        
        $user = auth()->user() ?? \App\Models\User::first();
        
        $query = Activity::with(['project.pemilikProject', 'user']);
        
        if ($user->role === 'supervisi') {
            // All activities
        } elseif ($user->role === 'perizinan') {
            $query->whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            });
        } else {
            $query->where('user_id', $user->id);
        }
        
        $activities = $query->limit(10)->get();
        
        // Simulate blade access
        foreach ($activities as $activity) {
            $temp = $activity->nama_aktivitas;
            $temp = $activity->project->nama_project;
            $temp = $activity->project->pemilikProject->nama_divisi ?? 'N/A';
            $temp = $activity->user->name;
        }
    }

    private function analyzeMyActivities()
    {
        $this->info("👤 Simulating My Activities...\n");
        
        $user = auth()->user() ?? \App\Models\User::first();
        
        $query = Activity::with(['project.pemilikProject', 'user'])
            ->where('user_id', $user->id);
        
        $activities = $query->limit(10)->get();
        
        // Simulate blade access
        foreach ($activities as $activity) {
            $temp = $activity->nama_aktivitas;
            $temp = $activity->project->nama_project;
            $temp = $activity->project->pemilikProject->nama_divisi ?? 'N/A';
            $temp = $activity->user->name;
        }
    }

    private function displayQueryAnalysis()
    {
        $queries = DB::getQueryLog();
        $totalQueries = count($queries);
        
        $this->newLine();
        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("                    QUERY ANALYSIS RESULTS");
        $this->info("═══════════════════════════════════════════════════════════");
        $this->newLine();
        
        $this->line("Total Queries: <fg=cyan>{$totalQueries}</>");
        
        // Group queries by type
        $selectQueries = 0;
        $updateQueries = 0;
        $insertQueries = 0;
        $slowQueries = [];
        
        foreach ($queries as $index => $query) {
            $sql = strtolower($query['query']);
            
            if (strpos($sql, 'select') === 0) {
                $selectQueries++;
            } elseif (strpos($sql, 'update') === 0) {
                $updateQueries++;
            } elseif (strpos($sql, 'insert') === 0) {
                $insertQueries++;
            }
            
            // Check slow queries (>50ms)
            if ($query['time'] > 50) {
                $slowQueries[] = [
                    'index' => $index + 1,
                    'time' => $query['time'],
                    'query' => $query['query']
                ];
            }
        }
        
        $this->line("  SELECT: <fg=green>{$selectQueries}</>");
        $this->line("  UPDATE: <fg=yellow>{$updateQueries}</>");
        $this->line("  INSERT: <fg=blue>{$insertQueries}</>");
        
        $this->newLine();
        
        // N+1 Detection
        if ($selectQueries > 10) {
            $this->error("⚠️  HIGH QUERY COUNT DETECTED!");
            $this->line("   Possible N+1 problem. Expected: 3-5 queries for 5 activities.");
            $this->line("   Actual: {$selectQueries} SELECT queries");
        } else {
            $this->info("✅ Query count looks good!");
            $this->line("   {$selectQueries} SELECT queries for this operation is acceptable.");
        }
        
        $this->newLine();
        
        // Show slow queries
        if (count($slowQueries) > 0) {
            $this->warn("🐌 SLOW QUERIES DETECTED (>50ms):");
            foreach ($slowQueries as $slow) {
                $this->line("   #{$slow['index']}: {$slow['time']}ms");
                $this->line("   " . substr($slow['query'], 0, 100) . "...");
            }
        } else {
            $this->info("⚡ No slow queries detected!");
        }
        
        $this->newLine();
        
        // Detailed query list
        if ($this->option('verbose')) {
            $this->info("📋 DETAILED QUERY LIST:");
            foreach ($queries as $index => $query) {
                $this->line(sprintf(
                    "   %d. [%sms] %s",
                    $index + 1,
                    round($query['time'], 2),
                    substr($query['query'], 0, 80)
                ));
            }
        } else {
            $this->line("💡 Run with -v flag to see detailed query list");
        }
        
        $this->newLine();
        
        // Performance Score
        $score = 100;
        
        if ($selectQueries > 20) {
            $score -= 30;
        } elseif ($selectQueries > 10) {
            $score -= 15;
        }
        
        if (count($slowQueries) > 0) {
            $score -= (count($slowQueries) * 10);
        }
        
        $score = max(0, $score);
        
        $scoreColor = $score >= 80 ? 'green' : ($score >= 60 ? 'yellow' : 'red');
        $this->line("<fg={$scoreColor}>PERFORMANCE SCORE: {$score}/100</>");
        
        $this->newLine();
        $this->info("═══════════════════════════════════════════════════════════");
    }
}