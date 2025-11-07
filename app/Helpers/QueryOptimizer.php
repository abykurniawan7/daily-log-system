<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class QueryOptimizer
{
    /**
     * Cache user activities with eager loading
     */
    public static function getUserActivities($userId, $limit = 10)
    {
        $cacheKey = "user_activities_{$userId}_" . date('Y-m-d-H');
        
        return Cache::remember($cacheKey, 3600, function () use ($userId, $limit) {
            return \App\Models\Activity::where('user_id', $userId)
                ->with(['project:id,name,status', 'user:id,name'])
                ->select(['id', 'user_id', 'project_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'deskripsi', 'status', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Cache user projects with eager loading
     */
    public static function getUserProjects($userId)
    {
        $cacheKey = "user_projects_{$userId}_" . date('Y-m-d');
        
        return Cache::remember($cacheKey, 7200, function () use ($userId) {
            return \App\Models\Project::where('user_id', $userId)
                ->orWhere('pic_proyek_id', $userId)
                ->orWhere('pemilik_project_id', $userId)
                ->with(['picProyek:id,name', 'pemilikProject:id,name'])
                ->select(['id', 'name', 'pic_proyek_id', 'pemilik_project_id', 'user_id', 'status', 'target_date'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Cache dashboard statistics
     */
    public static function getDashboardStats($userId)
    {
        $cacheKey = "dashboard_stats_{$userId}_" . date('Y-m-d-H');
        
        return Cache::remember($cacheKey, 1800, function () use ($userId) {
            return [
                'total_activities' => \App\Models\Activity::where('user_id', $userId)->count(),
                'pending_activities' => \App\Models\Activity::where('user_id', $userId)->where('status', 'pending')->count(),
                'completed_activities' => \App\Models\Activity::where('user_id', $userId)->where('status', 'completed')->count(),
                'total_projects' => \App\Models\Project::where('user_id', $userId)->count(),
                'active_projects' => \App\Models\Project::where('user_id', $userId)->where('status', 'active')->count(),
            ];
        });
    }

    /**
     * Clear user cache
     */
    public static function clearUserCache($userId)
    {
        Cache::forget("user_activities_{$userId}_" . date('Y-m-d-H'));
        Cache::forget("user_projects_{$userId}_" . date('Y-m-d'));
        Cache::forget("dashboard_stats_{$userId}_" . date('Y-m-d-H'));
    }
}