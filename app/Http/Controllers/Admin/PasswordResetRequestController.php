<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class PasswordResetRequestController extends Controller
{
    /**
     * Display a listing of password reset requests
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        
        $query = PasswordResetRequest::with('user')->latest();
        
        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }
        
        $requests = $query->paginate(15);
        
        // Get counts for tabs
        $counts = [
            'all' => PasswordResetRequest::count(),
            'pending' => PasswordResetRequest::where('status', 'pending')->count(),
            'approved' => PasswordResetRequest::where('status', 'approved')->count(),
            'rejected' => PasswordResetRequest::where('status', 'rejected')->count(),
        ];
        
        return view('admin.password-requests.index', compact('requests', 'counts'));
    }

    /**
     * Display the specified password reset request
     */
    public function show(PasswordResetRequest $passwordResetRequest)
    {
        $passwordResetRequest->load('user', 'processedBy');
        
        return view('admin.password-requests.show', [
            'passwordResetRequest' => $passwordResetRequest,
        ]);
    }

    /**
     * Approve password reset request and reset user password
     */
    public function approve(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        // Validasi bahwa status masih pending
        if ($passwordResetRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }
        
        // Validasi custom password (jika ada)
        $newPassword = $request->input('new_password');
        
        if ($newPassword) {
            // Jika admin input password manual
            if (strlen($newPassword) < 8) {
                return back()->with('error', 'Password minimal 8 karakter.');
            }
        } else {
            // Generate random password (8 karakter: huruf + angka)
            $newPassword = Str::upper(Str::random(4)) . rand(1000, 9999);
        }
        
        // Update user password
        $user = $passwordResetRequest->user;
        $user->password = Hash::make($newPassword);
        $user->save();
        
        // Update request status
        $passwordResetRequest->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'new_password' => $newPassword, // Simpan plain password untuk ditampilkan ke admin (TEMPORARY)
            'admin_response' => 'Password berhasil direset. User dapat login dengan password baru.'
        ]);
        
        return back()->with('success', 'Request berhasil di-approve! Password baru: ' . $newPassword);
    }

    /**
     * Reject password reset request
     */
    public function reject(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        // Validasi bahwa status masih pending
        if ($passwordResetRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }
        
        $passwordResetRequest->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_response' => $request->input('admin_response', 'Request ditolak oleh admin.')
        ]);
        
        return back()->with('success', 'Request berhasil ditolak.');
    }
}