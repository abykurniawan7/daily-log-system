<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();
        
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
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Stats
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
    public function create()
    {
        return view('admin.users.create');
    }
    
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,supervisi,perizinan,karyawan,guest'],
            'bagian' => ['nullable', 'in:PGB,PKJ'],
        ]);
        
        // Validasi: Jika role bukan admin/supervisi, bagian wajib diisi
        if (!in_array($validated['role'], ['admin', 'supervisi']) && empty($validated['bagian'])) {
            return back()->withErrors(['bagian' => 'Bagian wajib diisi untuk role Perizinan dan Karyawan.'])->withInput();
        }
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'bagian' => $validated['bagian'],
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }
    
    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        // Prevent admin from editing their own account (use profile page instead)
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.edit')
                ->with('error', 'Gunakan halaman Profile untuk edit akun Anda sendiri.');
        }
        
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Prevent admin from updating their own role
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri!');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,supervisi,perizinan,karyawan'],
            'bagian' => ['nullable', 'in:PGB,PKJ'],
        ]);
        
        // Validasi: Jika role bukan admin/supervisi, bagian wajib diisi
        if (!in_array($validated['role'], ['admin', 'supervisi']) && empty($validated['bagian'])) {
            return back()->withErrors(['bagian' => 'Bagian wajib diisi untuk role Perizinan dan Karyawan.'])->withInput();
        }
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'bagian' => $validated['bagian'],
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diupdate!");
    }
    
    /**
     * Show reset password form
     */
    public function showResetPasswordForm(User $user)
    {
        // Prevent admin from resetting their own password here
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.edit')
                ->with('error', 'Gunakan halaman Profile untuk ganti password Anda sendiri.');
        }
        
        return view('admin.users.reset-password', compact('user'));
    }
    
    /**
     * Reset user password (Admin only)
     */
    public function resetPassword(Request $request, User $user)
    {
        // Prevent admin from resetting their own password here
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat reset password sendiri di sini!');
        }
        
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', "Password user {$user->name} berhasil direset!");
    }
    
    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }
        
        // Prevent deleting if user has projects or activities
        if ($user->projects()->count() > 0) {
            return back()->with('error', "User {$user->name} tidak dapat dihapus karena masih memiliki project!");
        }
        
        if ($user->activities()->count() > 0) {
            return back()->with('error', "User {$user->name} tidak dapat dihapus karena masih memiliki aktivitas!");
        }
        
        $userName = $user->name;
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "User {$userName} berhasil dihapus!");
    }
}