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
        
        // Query data berdasarkan role
        if ($user->role === 'supervisi') {
            // Supervisi: lihat semua data
            $totalProjects = Project::count();
            $totalActivities = Activity::count();
            $projectsProgress = Project::where('status', 'Progress')->count();
            $projectsDone = Project::where('status', 'Done')->count();
            $projectsPending = Project::where('status', 'Pending')->count();
            
            // Activity stats
            $activitiesProgress = Activity::where('status', 'Progress')->count();
            $activitiesDone = Activity::where('status', 'Done')->count();
            $activitiesPending = Activity::where('status', 'Pending')->count();
            
            // ✅ FIX: Tambah eager loading lengkap untuk nested relationships
            $recentActivities = Activity::with([
                'project.pemilikProject',  // ✅ Load division
                'project.picProyek',       // ✅ Load PIC
                'user'                     // ✅ Load user
            ])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
        } else {
            // Karyawan & Perizinan: hanya project yang dia emban
            $totalProjects = Project::where('pic_proyek_id', $user->id)->count();
            $totalActivities = Activity::where('user_id', $user->id)->count();
            $projectsProgress = Project::where('pic_proyek_id', $user->id)
                ->where('status', 'Progress')
                ->count();
            $projectsDone = Project::where('pic_proyek_id', $user->id)
                ->where('status', 'Done')
                ->count();
            $projectsPending = Project::where('pic_proyek_id', $user->id)
                ->where('status', 'Pending')
                ->count();
            
            // Activity stats
            $activitiesProgress = Activity::where('user_id', $user->id)
                ->where('status', 'Progress')
                ->count();
            $activitiesDone = Activity::where('user_id', $user->id)
                ->where('status', 'Done')
                ->count();
            $activitiesPending = Activity::where('user_id', $user->id)
                ->where('status', 'Pending')
                ->count();
            
            // ✅ FIX: Tambah eager loading lengkap untuk nested relationships
            $recentActivities = Activity::with([
                'project.pemilikProject',  // ✅ Load division
                'project.picProyek',       // ✅ Load PIC
                'user'                     // ✅ Load user
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
     * OPTIMIZED: Projects per user with caching
     */
    public function projectsPerUser()
    {
        $user = auth()->user();
        $cacheKey = "projects_per_user_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 5 menit (300 detik)
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            // Optimized: Use direct JOIN and COUNT
            $query = User::select('users.id', 'users.name', 'users.bagian')
                ->selectRaw('COUNT(projects.id) as total')
                ->leftJoin('projects', 'users.id', '=', 'projects.pic_proyek_id')
                ->whereIn('users.role', ['perizinan', 'karyawan'])
                ->groupBy('users.id', 'users.name', 'users.bagian');
            
            // Filter berdasarkan role user yang login
            if ($user->role === 'perizinan') {
                $query->where('users.bagian', 'PKJ');
            } elseif ($user->role === 'karyawan') {
                $query->where('users.bagian', 'PGB');
            }
            
            return $query->orderBy('total', 'desc')
                ->get()
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'bagian' => $user->bagian,
                        'total' => $user->total
                    ];
                });
        });
        
        return response()->json($data);
    }

    /**
     * OPTIMIZED: Projects per division with caching and better query
     */
    public function projectsPerDivision()
    {
        $user = auth()->user();
        $cacheKey = "projects_per_division_{$user->id}_{$user->role}";
        
        // Cache selama 5 menit
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            if ($user->role === 'supervisi') {
                // SUPERVISI: Semua divisi, EXCLUDE Done projects
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
                
                // ✅ Ambil top 6, sisanya jadi "Others"
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
                
                // ✅ Jika ada divisi selain top 6, gabungkan jadi "Others" DENGAN DETAIL
                if ($otherDivisions->count() > 0) {
                    $othersTotal = $otherDivisions->sum('total');
                    
                    // ✅ Simpan detail divisi dalam Others
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
                        'details' => $othersDetail // ✅ DETAIL DIVISI
                    ]);
                }
                
                return $result;
            } else {
                // KARYAWAN/PKJ: Hanya project yang mereka pegang, EXCLUDE Done
                return Project::select('pemilik_project_id')
                    ->selectRaw('COUNT(*) as total')
                    ->where('pic_proyek_id', $user->id)
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
     * OPTIMIZED: Activities status distribution with caching
     */
    public function activitiesStatusDistribution()
    {
        $user = auth()->user();
        $cacheKey = "activities_status_dist_{$user->id}_{$user->role}";
        
        // Cache selama 3 menit (karena data ini sering update)
        $activities = Cache::remember($cacheKey, 180, function () use ($user) {
            $query = Activity::select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status');
            
            if ($user->role !== 'supervisi') {
                $query->where('user_id', $user->id);
            }
            
            return $query->get();
        });

        return response()->json($activities);
    }

    /**
     * OPTIMIZED: Activities per month with caching
     */
    public function activitiesPerMonth()
    {
        $user = auth()->user();
        $cacheKey = "activities_per_month_{$user->id}_{$user->role}";
        
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
                
                if ($user->role !== 'supervisi') {
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
     * OPTIMIZED: Project urgency distribution with caching
     */
    public function projectUrgencyDistribution()
    {
        $user = auth()->user();
        $cacheKey = "project_urgency_dist_{$user->id}_{$user->role}";
        
        // Cache selama 5 menit
        $projects = Cache::remember($cacheKey, 300, function () use ($user) {
            $query = Project::select('urgensi')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('urgensi');
            
            if ($user->role !== 'supervisi') {
                $query->where('pic_proyek_id', $user->id);
            }
            
            return $query->get();
        });

        return response()->json($projects);
    }
    
    /**
     * OPTIMIZED: Employee activities (last week, Progress & Pending) with caching
     */
    public function employeeActivities()
    {
        $user = auth()->user();
        $cacheKey = "employee_activities_{$user->id}_{$user->role}_{$user->bagian}";
        
        // Cache selama 3 menit (data sering update)
        $data = Cache::remember($cacheKey, 180, function () use ($user) {
            // Tanggal 1 minggu yang lalu
            $oneWeekAgo = Carbon::now()->subWeek();
            
            // Optimized: Use direct JOIN and COUNT - TAMPILKAN SEMUA USER
            $query = User::select('users.id', 'users.name', 'users.bagian')
                ->selectRaw('
                    COUNT(CASE WHEN activities.status = "Progress" AND activities.tanggal_mulai >= ? THEN 1 END) as progress_count,
                    COUNT(CASE WHEN activities.status = "Pending" AND activities.tanggal_mulai >= ? THEN 1 END) as pending_count,
                    COUNT(CASE WHEN activities.tanggal_mulai >= ? THEN activities.id END) as total
                ', [$oneWeekAgo, $oneWeekAgo, $oneWeekAgo])
                ->leftJoin('activities', 'users.id', '=', 'activities.user_id')
                ->whereIn('users.role', ['perizinan', 'karyawan'])
                ->groupBy('users.id', 'users.name', 'users.bagian');
            
            // Filter berdasarkan role user yang login
            if ($user->role === 'perizinan') {
                $query->where('users.bagian', 'PKJ');
            } elseif ($user->role === 'karyawan') {
                $query->where('users.bagian', 'PGB');
            }
            
            // ✅ HAPUS having() agar semua user muncul
            return $query->orderBy('total', 'desc')
                ->orderBy('users.name', 'asc') // Tambahan: order by name untuk yang total sama
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