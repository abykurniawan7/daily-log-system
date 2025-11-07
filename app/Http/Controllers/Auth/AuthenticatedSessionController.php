<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\LogActivity; // TAMBAHAN: Import helper

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        $userName = $user->name;
        
        // ✅ LOG: User login
        LogActivity::auth('login');
        
        // Redirect berdasarkan role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Selamat datang, ' . $userName . '!');
        }
        
        if ($user->isGuest()) {
            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, ' . $userName . '!');
        }
        
        // Default untuk role lain (supervisi, karyawan, perizinan)
        return redirect()->intended(route('dashboard', absolute: false))
            ->with('success', 'Selamat datang, ' . $userName . '!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userName = auth()->user()->name;
        
        // ✅ LOG: User logout (sebelum logout!)
        LogActivity::auth('logout');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Berhasil logout. Sampai jumpa, ' . $userName . '!');
    }
}