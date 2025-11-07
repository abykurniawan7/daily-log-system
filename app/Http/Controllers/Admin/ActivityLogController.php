<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs (Admin only)
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Quick date filter
        if ($request->filled('quick_date')) {
            $today = now();
            
            switch ($request->quick_date) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', $today->copy()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereYear('created_at', $today->year)
                          ->whereMonth('created_at', $today->month);
                    break;
                case 'last_month':
                    $lastMonth = $today->copy()->subMonth();
                    $query->whereYear('created_at', $lastMonth->year)
                          ->whereMonth('created_at', $lastMonth->month);
                    break;
            }
        }
        
        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        $logs = $query->paginate(20)->withQueryString();
        
        // Get all users for filter dropdown
        $users = User::orderBy('name')->get();
        
        // Available actions for filter
        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');
        
        // Available model types for filter
        $modelTypes = ActivityLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->orderBy('model_type')
            ->pluck('model_type');
        
        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', now())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => ActivityLog::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];
        
        return view('admin.activity-logs.index', compact('logs', 'users', 'actions', 'modelTypes', 'stats'));
    }
    
    /**
     * Show detailed view of a single log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        
        return view('admin.activity-logs.show', compact('activityLog'));
    }
    
    /**
     * Export logs to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('quick_date')) {
            $today = now();
            
            switch ($request->quick_date) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', $today->copy()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereYear('created_at', $today->year)
                          ->whereMonth('created_at', $today->month);
                    break;
            }
        }
        
        $logs = $query->get();
        
        // Prepare filter info
        $filterInfo = [];
        
        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            $filterInfo['User'] = $user ? $user->name : 'Unknown';
        }
        
        if ($request->filled('action')) {
            $filterInfo['Action'] = ucfirst($request->action);
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filterInfo['Periode'] = \Carbon\Carbon::parse($request->date_from)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->date_to)->format('d M Y');
        }
        
        $pdf = Pdf::loadView('pdf.activity-logs', compact('logs', 'filterInfo'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Clear old logs (optional: for maintenance)
     */
    public function clearOldLogs(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:30|max:365',
        ]);
        
        $cutoffDate = now()->subDays($validated['days']);
        
        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();
        
        return redirect()->route('admin.activity-logs.index')
            ->with('success', "Berhasil menghapus {$deleted} log lama (lebih dari {$validated['days']} hari).");
    }
}