<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RoleRequest;
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with quick access cards
     */
    public function dashboard()
    {
        // ✅ FIXED: Hitung PKJ dengan benar (Kabag PKJ + Staff PKJ)
        $pkjCount = User::where(function($query) {
            $query->where('role', 'perizinan') // Kabag PKJ
                  ->orWhere(function($q) {
                      $q->where('role', 'karyawan')
                        ->where('bagian', 'PKJ'); // Staff PKJ
                  });
        })->count();
        
        // ✅ FIXED: Hitung PGB dengan benar (Kabag PGB + Staff PGB)
        $pgbCount = User::where(function($query) {
            $query->where('role', 'kabag_pgb') // Kabag PGB
                  ->orWhere(function($q) {
                      $q->where('role', 'karyawan')
                        ->where('bagian', 'PGB'); // Staff PGB
                  });
        })->count();
        
        // Get statistics for dashboard cards
        $stats = [
            // User Management Stats
            'users' => [
                'total' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'supervisi' => User::where('role', 'supervisi')->count(),
                'kabag_pgb' => User::where('role', 'kabag_pgb')->count(),
                
                // ✅ FIXED: PKJ sekarang include Kabag + Staff
                'perizinan' => $pkjCount,
                
                // ✅ FIXED: PGB sekarang include Kabag + Staff
                'karyawan' => $pgbCount,
                
                'guest' => User::where('role', 'guest')->count(),
            ],
            
            // Role Requests Stats
            'role_requests' => [
                'total' => RoleRequest::count(),
                'pending' => RoleRequest::where('status', 'pending')->count(),
                'approved' => RoleRequest::where('status', 'approved')->count(),
                'rejected' => RoleRequest::where('status', 'rejected')->count(),
            ],
            
            // System Overview
            'system' => [
                'total_projects' => Project::count(),
                'total_activities' => Activity::count(),
            ]
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display a listing of users
     */
    public function users(Request $request)
    {
        // Build query
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Bagian filter (jika ada)
        if ($request->filled('bagian')) {
            $query->where('bagian', $request->bagian);
        }

        // Get paginated users
        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics - PERBAIKAN DI SINI (struktur flat, bukan nested)
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'supervisi' => User::where('role', 'supervisi')->count(),
            'perizinan' => User::where('role', 'perizinan')->count(),
            'karyawan' => User::where('role', 'karyawan')->count(),
            'guest' => User::where('role', 'guest')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,supervisi,perizinan,karyawan,guest',
            'bagian' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Show the form for editing user
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,supervisi,perizinan,karyawan,guest',
            'bagian' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified user
     */
    public function destroyUser(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Show reset password form
     */
    public function resetPasswordForm(User $user)
    {
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password user berhasil direset!');
    }
}