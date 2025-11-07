<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RoleRequest;
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with quick access cards
     */
    public function dashboard()
    {
        // Get statistics for dashboard cards
        $stats = [
            // User Management Stats
            'users' => [
                'total' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'supervisi' => User::where('role', 'supervisi')->count(),
                'perizinan' => User::where('role', 'perizinan')->count(),
                'karyawan' => User::where('role', 'karyawan')->count(),
                'guest' => User::where('role', 'guest')->count(),
            ],
            
            // Role Requests Stats
            'role_requests' => [
                'total' => RoleRequest::count(),
                'pending' => RoleRequest::where('status', 'pending')->count(),
                'approved' => RoleRequest::where('status', 'approved')->count(),
                'rejected' => RoleRequest::where('status', 'rejected')->count(),
            ],
            
            // System Overview (Optional - tambahan info)
            'system' => [
                'total_projects' => Project::count(),
                'total_activities' => Activity::count(),
            ]
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
}