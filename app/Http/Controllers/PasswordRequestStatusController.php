<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordRequestStatusController extends Controller
{
    /**
     * Constructor - pastikan user sudah login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan status password reset requests milik user yang login
     */
    public function index(): View
    {
        $requests = PasswordResetRequest::where('user_id', auth()->id())
            ->with('user') // Eager load user relationship
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.password-requests.status', [
            'requests' => $requests
        ]);
    }

    /**
     * Form untuk user yang sudah login mengajukan reset password
     */
    public function create(): View
    {
        return view('user.password-requests.create');
    }

    /**
     * Proses request password reset untuk user yang sudah login
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'reason.required' => 'Alasan reset password harus diisi.',
            'reason.min' => 'Alasan minimal 10 karakter.',
        ]);

        $user = auth()->user();

        // Cek apakah user sudah punya pending request
        $existingRequest = PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->withErrors([
                'reason' => 'Anda sudah memiliki request reset password yang sedang diproses. Mohon tunggu approval dari admin.'
            ])->withInput();
        }

        // Buat request baru
        PasswordResetRequest::create([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Redirect ke halaman status
        return redirect()
            ->route('user.password-requests.status')
            ->with('success', 'Request reset password berhasil dikirim ke admin. Status request Anda dapat dilihat di halaman ini.');
    }
}