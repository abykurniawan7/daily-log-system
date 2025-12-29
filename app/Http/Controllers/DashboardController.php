<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Activity;
use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Helpers\QueryOptimizer;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Jika user adalah admin, redirect ke admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Jika user adalah guest, tampilkan guest dashboard
        if ($user->role === 'guest') {
            return view('guest-dashboard');
        }
        
        // ✅ FIXED: Query data berdasarkan role dengan logic yang konsisten
        if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
            // Supervisi & Kabag PGB: lihat semua data
            $totalProjects = Project::count();
            $totalActivities = Activity::count();
            $projectsProgress = Project::where('status', 'Progress')->count();
            $projectsDone = Project::where('status', 'Done')->count();
            $projectsPending = Project::where('status', 'Pending')->count();
            
            // Activity stats
            $activitiesProgress = Activity::where('status', 'Progress')->count();
            $activitiesDone = Activity::where('status', 'Done')->count();
            $activitiesPending = Activity::where('status', 'Pending')->count();
            
            $recentActivities = Activity::with([
                'project.pemilikProject',
                'project.pics',
                'user'
            ])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
        } elseif ($user->role === 'perizinan') {
            // ✅ FIXED: Kabag PKJ - lihat semua data PKJ (bukan hanya milik sendiri)
            $totalProjects = Project::whereHas('pics', function($q) {
                $q->where('bagian', 'PKJ');
            })->count();
            
            $totalActivities = Activity::whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            })->count();
            
            $projectsProgress = Project::whereHas('pics', function($q) {
                $q->where('bagian', 'PKJ');
            })->where('status', 'Progress')->count();
            
            $projectsDone = Project::whereHas('pics', function($q) {
                $q->where('bagian', 'PKJ');
            })->where('status', 'Done')->count();
            
            $projectsPending = Project::whereHas('pics', function($q) {
                $q->where('bagian', 'PKJ');
            })->where('status', 'Pending')->count();
            
            // Activity stats - semua aktivitas PKJ
            $activitiesProgress = Activity::whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            })->where('status', 'Progress')->count();
            
            $activitiesDone = Activity::whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            })->where('status', 'Done')->count();
            
            $activitiesPending = Activity::whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            })->where('status', 'Pending')->count();
            
            $recentActivities = Activity::with([
                'project.pemilikProject',
                'project.pics',
                'user'
            ])
                ->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
        } else {
            // ✅ FIXED: Karyawan (PGB/PKJ) - hanya data sendiri
            $assignedProjectIds = $user->assignedProjects()->pluck('projects.id');
            
            $totalProjects = $assignedProjectIds->count();
            $totalActivities = Activity::where('user_id', $user->id)->count();
            
            $projectsProgress = Project::whereIn('id', $assignedProjectIds)
                ->where('status', 'Progress')
                ->count();
            $projectsDone = Project::whereIn('id', $assignedProjectIds)
                ->where('status', 'Done')
                ->count();
            $projectsPending = Project::whereIn('id', $assignedProjectIds)
                ->where('status', 'Pending')
                ->count();
            
            // Activity stats - hanya milik sendiri
            $activitiesProgress = Activity::where('user_id', $user->id)
                ->where('status', 'Progress')
                ->count();
            $activitiesDone = Activity::where('user_id', $user->id)
                ->where('status', 'Done')
                ->count();
            $activitiesPending = Activity::where('user_id', $user->id)
                ->where('status', 'Pending')
                ->count();
            
            $recentActivities = Activity::with([
                'project.pemilikProject',
                'project.pics',
                'user'
            ])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Calculate percentages
        $projectProgressPercentage = $totalProjects > 0 
            ? round(($projectsProgress / $totalProjects) * 100, 1) 
            : 0;
        $projectDonePercentage = $totalProjects > 0 
            ? round(($projectsDone / $totalProjects) * 100, 1) 
            : 0;
        $activityDonePercentage = $totalActivities > 0 
            ? round(($activitiesDone / $totalActivities) * 100, 1) 
            : 0;

        return view('dashboard', compact(
            'totalProjects',
            'totalActivities',
            'projectsProgress',
            'projectsDone',
            'projectsPending',
            'activitiesProgress',
            'activitiesDone',
            'activitiesPending',
            'projectProgressPercentage',
            'projectDonePercentage',
            'activityDonePercentage',
            'recentActivities'
        ));
    }

    /**
     * ✅ FIXED: Projects per user with consistent logic
     */
    public function projectsPerUser()
    {
        $user = auth()->user();
        $cacheKey = "projects_per_user_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 5 menit (300 detik)
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            $query = User::select('users.id', 'users.name', 'users.bagian')
                ->selectRaw('COUNT(DISTINCT project_user.project_id) as total')
                ->leftJoin('project_user', 'users.id', '=', 'project_user.user_id')
                ->whereIn('users.role', ['kabag_pgb', 'perizinan', 'karyawan'])
                ->groupBy('users.id', 'users.name', 'users.bagian');
            
            // ✅ FIXED: Filter berdasarkan role & bagian
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
                // Lihat semua user
            } elseif ($user->role === 'perizinan') {
                // Kabag PKJ: hanya user PKJ
                $query->where('users.bagian', 'PKJ');
            } elseif ($user->role === 'karyawan') {
                // Staff: hanya bagian sendiri
                $query->where('users.bagian', $user->bagian);
            }
            
            return $query->orderBy('total', 'desc')
                ->get()
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'bagian' => $user->bagian,
                        'total' => (int) $user->total
                    ];
                });
        });
        
        return response()->json($data);
    }

    /**
     * ✅ FIXED: Projects per division with better filtering
     */
    public function projectsPerDivision()
    {
        $user = auth()->user();
        $cacheKey = "projects_per_division_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 5 menit
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
                // Supervisi & Kabag PGB - Semua divisi (exclude Done projects)
                $allDivisions = Division::select('divisions.id', 'divisions.kode_divisi', 'divisions.nama_divisi')
                    ->selectRaw('COUNT(projects.id) as total')
                    ->leftJoin('projects', function($join) {
                        $join->on('divisions.id', '=', 'projects.pemilik_project_id')
                            ->where('projects.status', '!=', 'Done');
                    })
                    ->groupBy('divisions.id', 'divisions.kode_divisi', 'divisions.nama_divisi')
                    ->having('total', '>', 0)
                    ->orderBy('total', 'desc')
                    ->get();
                
                // Ambil top 6, sisanya jadi "Others"
                $topDivisions = $allDivisions->take(6);
                $otherDivisions = $allDivisions->skip(6);
                
                $result = $topDivisions->map(function ($division) {
                    return [
                        'kode_divisi' => $division->kode_divisi, 
                        'nama_divisi' => $division->nama_divisi,
                        'total' => $division->total,
                        'is_others' => false
                    ];
                });
                
                // Jika ada divisi selain top 6, gabungkan jadi "Others"
                if ($otherDivisions->count() > 0) {
                    $othersTotal = $otherDivisions->sum('total');
                    
                    $othersDetail = $otherDivisions->map(function($div) {
                        return [
                            'kode' => $div->kode_divisi,
                            'nama' => $div->nama_divisi,
                            'total' => $div->total
                        ];
                    })->toArray();
                    
                    $result->push([
                        'kode_divisi' => 'Others',
                        'nama_divisi' => 'Divisi Lainnya',
                        'total' => $othersTotal,
                        'is_others' => true,
                        'details' => $othersDetail
                    ]);
                }
                
                return $result;
                
            } elseif ($user->role === 'perizinan') {
                // ✅ FIXED: Kabag PKJ - project yang ada PIC dari PKJ
                return Project::select('pemilik_project_id')
                    ->selectRaw('COUNT(*) as total')
                    ->whereHas('pics', function($q) {
                        $q->where('bagian', 'PKJ');
                    })
                    ->where('status', '!=', 'Done')
                    ->with('pemilikProject:id,kode_divisi,nama_divisi')
                    ->groupBy('pemilik_project_id')
                    ->get()
                    ->map(function ($project) {
                        return [
                            'kode_divisi' => $project->pemilikProject->kode_divisi,
                            'nama_divisi' => $project->pemilikProject->nama_divisi,
                            'total' => $project->total,
                            'is_others' => false
                        ];
                    });
                    
            } else {
                // ✅ FIXED: Karyawan - project assigned via pivot
                $assignedProjectIds = $user->assignedProjects()->pluck('projects.id');
                
                return Project::select('pemilik_project_id')
                    ->selectRaw('COUNT(*) as total')
                    ->whereIn('id', $assignedProjectIds)
                    ->where('status', '!=', 'Done')
                    ->with('pemilikProject:id,kode_divisi,nama_divisi')
                    ->groupBy('pemilik_project_id')
                    ->get()
                    ->map(function ($project) {
                        return [
                            'kode_divisi' => $project->pemilikProject->kode_divisi,
                            'nama_divisi' => $project->pemilikProject->nama_divisi,
                            'total' => $project->total,
                            'is_others' => false
                        ];
                    });
            }
        });

        return response()->json($data);
    }

    /**
     * ✅ FIXED: Activities status distribution with proper bagian filter
     */
    public function activitiesStatusDistribution()
    {
        $user = auth()->user();
        $cacheKey = "activities_status_dist_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 3 menit (karena data ini sering update)
        $activities = Cache::remember($cacheKey, 180, function () use ($user) {
            $query = Activity::select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status');
            
            // ✅ FIXED: Filter berdasarkan role & bagian
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
                // Lihat semua aktivitas
            } elseif ($user->role === 'perizinan') {
                // Kabag PKJ: semua aktivitas PKJ
                $query->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                });
            } else {
                // Karyawan: hanya aktivitas sendiri
                $query->where('user_id', $user->id);
            }
            
            return $query->get();
        });

        return response()->json($activities);
    }

    /**
     * ✅ FIXED: Activities per month with bagian filter
     */
    public function activitiesPerMonth()
    {
        $user = auth()->user();
        $cacheKey = "activities_per_month_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 10 menit (data historical jarang berubah)
        $result = Cache::remember($cacheKey, 600, function () use ($user) {
            $months = [];
            $data = [];

            // Generate last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->format('M Y');
                
                $query = Activity::whereYear('tanggal_mulai', $date->year)
                    ->whereMonth('tanggal_mulai', $date->month);
                
                // ✅ FIXED: Filter berdasarkan role & bagian
                if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
                    // Lihat semua
                } elseif ($user->role === 'perizinan') {
                    $query->whereHas('user', function($q) {
                        $q->where('bagian', 'PKJ');
                    });
                } else {
                    $query->where('user_id', $user->id);
                }
                
                $data[] = $query->count();
            }

            return [
                'labels' => $months,
                'data' => $data
            ];
        });

        return response()->json($result);
    }

    /**
     * ✅ FIXED: Project urgency distribution with consistent filter
     */
    public function projectUrgencyDistribution()
    {
        $user = auth()->user();
        $cacheKey = "project_urgency_dist_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 5 menit
        $projects = Cache::remember($cacheKey, 300, function () use ($user) {
            $query = Project::select('urgensi')
                ->selectRaw('COUNT(*) as total')
                ->where('status', '!=', 'Done') // ✅ FIXED: Exclude Done projects
                ->groupBy('urgensi');
            
            // ✅ FIXED: Filter berdasarkan role & bagian
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
                // Lihat semua project
            } elseif ($user->role === 'perizinan') {
                // Kabag PKJ: project yang ada PIC PKJ
                $query->whereHas('pics', function($q) {
                    $q->where('bagian', 'PKJ');
                });
            } else {
                // Karyawan: assigned projects
                $assignedProjectIds = $user->assignedProjects()->pluck('projects.id');
                $query->whereIn('id', $assignedProjectIds);
            }
            
            return $query->get();
        });

        return response()->json($projects);
    }
    
    /**
     * ✅ FIXED: Employee activities - Last Week ONLY
     * 
     * Chart ini menampilkan aktivitas karyawan dalam 1 minggu terakhir.
     * Breakdown: Progress + Pending (Done tidak ditampilkan di tooltip)
     * 
     * Logic:
     * - Total = SEMUA aktivitas last week (Progress + Pending + Done)
     * - Progress = Aktivitas status Progress last week
     * - Pending = Aktivitas status Pending last week
     * 
     * Note: Progress + Pending bisa < Total karena ada Done yang tidak ditampilkan
     */
    public function employeeActivities()
    {
        $user = auth()->user();
        $cacheKey = "employee_activities_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 3 menit (data sering update)
        $data = Cache::remember($cacheKey, 180, function () use ($user) {
            // ✅ Tanggal 1 minggu yang lalu
            $oneWeekAgo = Carbon::now()->subWeek();
            
            $query = User::select('users.id', 'users.name', 'users.bagian')
                ->selectRaw('
                    COUNT(CASE WHEN activities.status = "Progress" AND activities.tanggal_mulai >= ? THEN 1 END) as progress_count,
                    COUNT(CASE WHEN activities.status = "Pending" AND activities.tanggal_mulai >= ? THEN 1 END) as pending_count,
                    COUNT(CASE WHEN activities.tanggal_mulai >= ? THEN 1 END) as total
                ', [$oneWeekAgo, $oneWeekAgo, $oneWeekAgo])
                ->leftJoin('activities', 'users.id', '=', 'activities.user_id')
                ->whereIn('users.role', ['kabag_pgb', 'perizinan', 'karyawan'])
                ->groupBy('users.id', 'users.name', 'users.bagian');
            
            // ✅ Filter berdasarkan role & bagian
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
                // Lihat semua user
            } elseif ($user->role === 'perizinan') {
                // Kabag PKJ: user PKJ saja
                $query->where('users.bagian', 'PKJ');
            } elseif ($user->role === 'karyawan') {
                // Staff: hanya bagian sendiri
                $query->where('users.bagian', $user->bagian);
            }
            
            return $query->having('total', '>', 0) // ✅ Only show users with activities
                ->orderBy('total', 'desc')
                ->orderBy('users.name', 'asc')
                ->get()
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'bagian' => $user->bagian,
                        'progress' => (int) $user->progress_count,
                        'pending' => (int) $user->pending_count,
                        'total' => (int) $user->total
                    ];
                });
        });
        
        return response()->json($data);
    }
}