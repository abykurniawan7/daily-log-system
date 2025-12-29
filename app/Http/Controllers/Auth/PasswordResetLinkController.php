<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem.',
            'reason.required' => 'Alasan reset password harus diisi.',
            'reason.min' => 'Alasan minimal 10 karakter.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah user sudah punya pending request
        $existingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->withErrors([
                'email' => 'Anda sudah memiliki request reset password yang sedang diproses. Mohon tunggu approval dari admin.'
            ])->withInput();
        }

        // Buat request baru
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // SELALU return back dengan success message
        // User harus login dulu untuk lihat status
        return back()->with('status', 'Request reset password berhasil dikirim ke admin. Silakan login untuk melihat status request Anda.');
    }
}