<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // ✅ UPDATED: Gunakan helper function
        if (!$user->canAccessEmployees()) {
            abort(403, 'Unauthorized. You do not have access to employee data.');
        }
        
        // Base query: Exclude Supervisi
        $query = User::whereIn('role', ['karyawan', 'kabag_pgb', 'perizinan']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by bagian
        if ($request->filled('bagian')) {
            $query->where('bagian', $request->bagian);
        }

        // Paginate
        $employees = $query->paginate(9);

        // ✅ Transform with accurate counts + activity breakdown
        $employees->getCollection()->transform(function ($employee) {
            // Projects where employee is CREATOR
            $createdProjectIds = \App\Models\Project::where('user_id', $employee->id)
                ->pluck('id')->toArray();
            
            // Projects where employee is PIC
            $picProjectIds = \DB::table('project_user')
                ->where('user_id', $employee->id)
                ->pluck('project_id')->toArray();
            
            // Merge & unique
            $allProjectIds = array_unique(array_merge($createdProjectIds, $picProjectIds));
            
            // Activities CREATED BY this employee
            $employeeActivities = \App\Models\Activity::where('user_id', $employee->id)->get();
            
            $totalActivities = $employeeActivities->count();
            $completedActivities = $employeeActivities->where('status', 'Done')->count();
            
            // ✅ NEW: Activity breakdown by status
            $activitiesDone = $employeeActivities->where('status', 'Done')->count();
            $activitiesProgress = $employeeActivities->where('status', 'Progress')->count();
            $activitiesPending = $employeeActivities->where('status', 'Pending')->count();
            
            // Set data
            $employee->total_projects = count($allProjectIds);
            $employee->total_activities = $totalActivities;
            $employee->completion_rate = $totalActivities > 0 
                ? round(($completedActivities / $totalActivities) * 100, 1) 
                : 0;
            
            // ✅ NEW: Add activity status breakdown
            $employee->activities_done = $activitiesDone;
            $employee->activities_progress = $activitiesProgress;
            $employee->activities_pending = $activitiesPending;
            
            return $employee;
        });

        // ✅ Hitung stats untuk header (dari SEMUA data, bukan paginated)
        $countKabagPGB = User::where('role', 'kabag_pgb')->count();
        $countKabagPKJ = User::where('role', 'perizinan')->count();
        $countKaryawanPGB = User::where('role', 'karyawan')->where('bagian', 'PGB')->count();
        $countKaryawanPKJ = User::where('role', 'karyawan')->where('bagian', 'PKJ')->count();

        return view('employees.index', compact(
            'employees',
            'countKabagPGB',
            'countKabagPKJ',
            'countKaryawanPGB',
            'countKaryawanPKJ'
        ));
    }

    /**
     * Display the specified employee
     */
    public function show(User $user)
    {
        // ✅ UPDATED: Gunakan helper function
        if (!auth()->user()->canAccessEmployees()) {
            abort(403, 'Unauthorized. You do not have access to employee details.');
        }
        
        $employee = $user;
        
        // Load projects (created + as PIC)
        $projects = \App\Models\Project::where(function($q) use ($employee) {
                $q->where('user_id', $employee->id)
                  ->orWhereHas('pics', function($subQ) use ($employee) {
                      $subQ->where('user_id', $employee->id);
                  });
            })
            ->with(['pemilikProject', 'pics', 'activities'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $employee->load(['activities.project']);

        $stats = [
            'total_projects' => $projects->count(),
            'total_activities' => $employee->activities->count(),
            'progress' => $employee->activities->where('status', 'Progress')->count(),
            'done' => $employee->activities->where('status', 'Done')->count(),
            'pending' => $employee->activities->where('status', 'Pending')->count(),
            'completion_rate' => $this->calculateCompletionRate($employee),
            'productivity_score' => $this->calculateProductivityScore($employee),
            'avg_completion_time' => $this->calculateAverageCompletionTime($employee),
            'active_projects' => $projects->where('status', 'Progress')->count(),
        ];

        $activities = $employee->activities()
            ->with('project')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('employees.show', compact('employee', 'stats', 'activities', 'projects'));
    }

    /**
     * Get chart data for employee
     */
    public function getChartData($id)
    {
        $employee = User::with('activities')->findOrFail($id);

        $months = [];
        $activitiesData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $count = $employee->activities()
                ->whereYear('tanggal_mulai', $date->year)
                ->whereMonth('tanggal_mulai', $date->month)
                ->count();
            
            $activitiesData[] = $count;
        }

        return response()->json([
            'months' => $months,
            'activities' => $activitiesData,
            'done' => $employee->activities->where('status', 'Done')->count(),
            'progress' => $employee->activities->where('status', 'Progress')->count(),
            'pending' => $employee->activities->where('status', 'Pending')->count(),
        ]);
    }

    /**
     * Show export preview for employee report
     */
    public function exportPreview(User $user)
    {
        // ✅ UPDATED: Gunakan helper function
        if (!auth()->user()->canExport()) {
            abort(403, 'Unauthorized action.');
        }

        $employee = $user;
        
        $projects = \App\Models\Project::where(function($q) use ($employee) {
                $q->where('user_id', $employee->id)
                  ->orWhereHas('pics', function($subQ) use ($employee) {
                      $subQ->where('user_id', $employee->id);
                  });
            })
            ->with(['pemilikProject', 'pics', 'activities'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_projects' => $projects->count(),
            'total_activities' => $employee->activities->count(),
            'completion_rate' => $this->calculateCompletionRate($employee),
        ];

        return view('employees.export-preview', compact('employee', 'stats', 'projects'));
    }

    /**
     * Export employee report to PDF
     * ✅ FIXED: Gunakan $projects variable yang sudah di-query, bukan $employee->projects
     */
    public function exportPdf(User $user)
    {
        \App::setLocale('id');
        
        // ✅ UPDATED: Gunakan helper function
        if (!auth()->user()->canExport()) {
            abort(403, 'Unauthorized action.');
        }

        $employee = $user;
        
        // ✅ Query projects (created + as PIC)
        $projects = \App\Models\Project::where(function($q) use ($employee) {
                $q->where('user_id', $employee->id)
                ->orWhereHas('pics', function($subQ) use ($employee) {
                    $subQ->where('user_id', $employee->id);
                });
            })
            ->with(['pemilikProject', 'pics', 'activities'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ FIXED: Gunakan $projects yang sudah di-query, BUKAN $employee->projects
        $stats = [
            'total_projects' => $projects->count(),  // ✅ FIXED: Pakai $projects
            'total_activities' => $employee->activities->count(),
            
            // ✅ FIXED: Hitung status projects dari $projects yang sudah di-query
            'projects_progress' => $projects->where('status', 'Progress')->count(),
            'projects_done' => $projects->where('status', 'Done')->count(),
            'projects_pending' => $projects->where('status', 'Pending')->count(),
            
            // ✅ Activities status (sudah benar)
            'activities_progress' => Activity::where('user_id', $employee->id)->where('status', 'Progress')->count(),
            'activities_done' => Activity::where('user_id', $employee->id)->where('status', 'Done')->count(),
            'activities_pending' => Activity::where('user_id', $employee->id)->where('status', 'Pending')->count(),
        ];

        // ✅ Completion rates
        $totalActivities = Activity::where('user_id', $employee->id)->count();
        $doneActivities = $stats['activities_done'];
        $stats['completion_rate'] = $totalActivities > 0 
            ? round(($doneActivities / $totalActivities) * 100, 1)
            : 0;

        $stats['project_completion_rate'] = $stats['total_projects'] > 0
            ? round(($stats['projects_done'] / $stats['total_projects']) * 100, 1)
            : 0;

        // ✅ Recent activities
        $recentActivities = Activity::where('user_id', $employee->id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ✅ IMPORTANT: Pass $projects to PDF view
        $pdf = Pdf::loadView('pdf.employee-report', compact('employee', 'stats', 'recentActivities', 'projects'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'employee_report_' . str_replace(' ', '_', strtolower($employee->name)) . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Calculate completion rate percentage
     */
    private function calculateCompletionRate($employee)
    {
        $totalActivities = $employee->activities->count();
        
        if ($totalActivities === 0) {
            return 0;
        }
        
        $completedActivities = $employee->activities->where('status', 'Done')->count();
        
        return round(($completedActivities / $totalActivities) * 100, 1);
    }

    /**
     * Calculate productivity score
     */
    private function calculateProductivityScore($employee)
    {
        $activities = $employee->activities;
        
        if ($activities->isEmpty()) {
            return 0;
        }
        
        $completedActivities = $activities->where('status', 'Done')->count();
        
        $firstActivity = $activities->min('tanggal_mulai');
        $lastActivity = $activities->max('tanggal_mulai');
        
        if (!$firstActivity || !$lastActivity) {
            return 0;
        }
        
        $daysDiff = \Carbon\Carbon::parse($firstActivity)->diffInDays(\Carbon\Carbon::parse($lastActivity));
        
        if ($daysDiff === 0) {
            $daysDiff = 1;
        }
        
        $score = $completedActivities / $daysDiff;
        
        return round($score, 2);
    }

    /**
     * Calculate average completion time
     */
    private function calculateAverageCompletionTime($employee)
    {
        $completedActivities = $employee->activities->where('status', 'Done');
        
        if ($completedActivities->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        $count = 0;
        
        foreach ($completedActivities as $activity) {
            if ($activity->tanggal_mulai && $activity->tanggal_selesai) {
                $days = \Carbon\Carbon::parse($activity->tanggal_mulai)
                    ->diffInDays(\Carbon\Carbon::parse($activity->tanggal_selesai));
                
                $totalDays += $days;
                $count++;
            }
        }
        
        if ($count === 0) {
            return 0;
        }
        
        return round($totalDays / $count, 1);
    }

    public function performanceData(User $user)
    {
        $employee = $user;
        
        $months = [];
        $activities = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            
            $count = Activity::where('user_id', $employee->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $months[] = $monthLabel;
            $activities[] = $count;
        }
        
        $done = Activity::where('user_id', $employee->id)->where('status', 'Done')->count();
        $progress = Activity::where('user_id', $employee->id)->where('status', 'Progress')->count();
        $pending = Activity::where('user_id', $employee->id)->where('status', 'Pending')->count();
        
        return response()->json([
            'months' => $months,
            'activities' => $activities,
            'done' => $done,
            'progress' => $progress,
            'pending' => $pending
        ]);
    }
}