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
        $query = User::whereIn('role', ['karyawan', 'perizinan']);

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

        // ✅ FIX: Jangan transform ke object, biarkan tetap User model instance
        $employees = $query->with(['projects', 'activities'])
            ->paginate(9);
        
        // ✅ TAMBAHKAN: Calculate stats untuk setiap employee
        $employees->getCollection()->transform(function ($employee) {
            $totalActivities = $employee->activities->count();
            $doneActivities = $employee->activities->where('status', 'Done')->count();
            
            // Attach stats sebagai attribute (tidak destroy object)
            $employee->total_projects = $employee->projects->count();
            $employee->total_activities = $totalActivities;
            $employee->completion_rate = $totalActivities > 0 
                ? round(($doneActivities / $totalActivities) * 100, 1) 
                : 0;
            
            return $employee;
        });

        return view('employees.index', compact('employees'));
    }

    /**
     * Display the specified employee
     */
     public function show(User $user)
    {
        $employee = $user;
        
        // Load relationships
        $employee->load(['projects', 'activities.project']);

        // Calculate statistics
        $stats = [
            'total_projects' => $employee->projects->count(),
            'total_activities' => $employee->activities->count(),
            'progress' => $employee->activities->where('status', 'Progress')->count(),
            'done' => $employee->activities->where('status', 'Done')->count(),
            'pending' => $employee->activities->where('status', 'Pending')->count(),
            'completion_rate' => $this->calculateCompletionRate($employee),
            'productivity_score' => $this->calculateProductivityScore($employee),
            'avg_completion_time' => $this->calculateAverageCompletionTime($employee),
            'active_projects' => $employee->projects->where('status', 'Progress')->count(),
        ];

        // Get activities sorted by date
        $activities = $employee->activities()
            ->with('project')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('employees.show', compact('employee', 'stats', 'activities'));
    }

    /**
     * Get chart data for employee
     */
    public function getChartData($id)
    {
        $employee = User::with('activities')->findOrFail($id);

        // Get last 6 months data
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
        // Authorization
        if (auth()->user()->role !== 'supervisi') {
            abort(403, 'Unauthorized action.');
        }

        $employee = $user;
        $employee->load(['projects.activities']);

        $stats = [
            'total_projects' => $employee->projects->count(),
            'total_activities' => $employee->activities->count(),
            'completion_rate' => $this->calculateCompletionRate($employee),
        ];

        return view('employees.export-preview', compact('employee', 'stats'));
    }

    /**
     * Export employee report to PDF
     */
    public function exportPdf(User $user)
    {
        \App::setLocale('id');
        
        if (auth()->user()->role !== 'supervisi') {
            abort(403, 'Unauthorized action.');
        }

        $employee = $user;
        $employee->load([
            'projects' => function ($query) {
                $query->with(['pemilikProject', 'activities']);
            },
            'activities' => function ($query) {
                $query->with('project')->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        // Calculate statistics
        $stats = [
            'total_projects' => $employee->projects->count(),
            'total_activities' => $employee->activities->count(),
            
            'projects_progress' => $employee->projects->where('status', 'Progress')->count(),
            'projects_done' => $employee->projects->where('status', 'Done')->count(),
            'projects_pending' => $employee->projects->where('status', 'Pending')->count(),
            
            'activities_progress' => Activity::where('user_id', $employee->id)
                ->where('status', 'Progress')->count(),
            'activities_done' => Activity::where('user_id', $employee->id)
                ->where('status', 'Done')->count(),
            'activities_pending' => Activity::where('user_id', $employee->id)
                ->where('status', 'Pending')->count(),
        ];

        $totalActivities = Activity::where('user_id', $employee->id)->count();
        $doneActivities = $stats['activities_done'];
        $stats['completion_rate'] = $totalActivities > 0 
            ? round(($doneActivities / $totalActivities) * 100, 1)
            : 0;

        $stats['project_completion_rate'] = $stats['total_projects'] > 0
            ? round(($stats['projects_done'] / $stats['total_projects']) * 100, 1)
            : 0;

        $recentActivities = Activity::where('user_id', $employee->id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pdf = Pdf::loadView('pdf.employee-report', compact('employee', 'stats', 'recentActivities'));
        $pdf->setPaper('a4', 'portrait');
        
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
     * Based on: completed activities / total days since first activity
     */
     private function calculateProductivityScore($employee)
    {
        $activities = $employee->activities;
        
        if ($activities->isEmpty()) {
            return 0;
        }
        
        $completedActivities = $activities->where('status', 'Done')->count();
        
        // Get date range
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
     * Average days between start and finish for completed activities
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
        
        // Get last 6 months
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
        
        // Get activity status breakdown
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