<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller untuk halaman Guest (user yang baru register)
 */
class GuestController extends Controller
{
    /**
     * Guest Dashboard - Halaman utama untuk guest
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get latest role request if exists
        $latestRequest = $user->latestRoleRequest;
        
        return view('guest.dashboard', compact('latestRequest'));
    }

    /**
     * Show form untuk request role
     */
    public function showRequestForm()
    {
        $user = auth()->user();
        
        // Cek apakah sudah punya pending request
        if ($user->hasPendingRoleRequest()) {
            return redirect()->route('guest.dashboard')
                ->with('warning', 'Anda sudah memiliki pengajuan role yang sedang diproses.');
        }

        return view('guest.request-role');
    }

    /**
     * Store role request
     */
    public function storeRequest(Request $request)
    {
        $user = auth()->user();

        // Cek apakah sudah punya pending request
        if ($user->hasPendingRoleRequest()) {
            return redirect()->route('guest.dashboard')
                ->with('error', 'Anda sudah memiliki pengajuan role yang sedang diproses.');
        }

        $validated = $request->validate([
            'requested_role' => ['required', 'in:supervisi,perizinan,karyawan'],
            'requested_bagian' => ['nullable', 'required_if:requested_role,perizinan,karyawan', 'in:PKJ,PGB'],
            'reason' => ['required', 'string', 'min:50', 'max:1000'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'], // Max 2MB
        ], [
            'requested_role.required' => 'Role yang diinginkan harus dipilih.',
            'requested_bagian.required_if' => 'Bagian harus dipilih untuk role Perizinan atau Karyawan.',
            'reason.required' => 'Alasan pengajuan harus diisi.',
            'reason.min' => 'Alasan minimal 50 karakter.',
            'reason.max' => 'Alasan maksimal 1000 karakter.',
            'supporting_document.mimes' => 'Dokumen harus berformat PDF, JPG, JPEG, atau PNG.',
            'supporting_document.max' => 'Ukuran dokumen maksimal 2MB.',
        ]);

        // Upload supporting document if exists
        $documentPath = null;
        if ($request->hasFile('supporting_document')) {
            $documentPath = $request->file('supporting_document')->store('role_requests', 'public');
        }

        // Create role request
        RoleRequest::create([
            'user_id' => $user->id,
            'requested_role' => $validated['requested_role'],
            'requested_bagian' => $validated['requested_bagian'] ?? null,
            'reason' => $validated['reason'],
            'supporting_document' => $documentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('guest.dashboard')
            ->with('success', 'Pengajuan role berhasil dikirim! Admin akan segera meninjau permintaan Anda.');
    }

    /**
     * Show request status detail
     */
    public function requestStatus()
    {
        $user = auth()->user();
        
        // Get all role requests history
        $requests = $user->roleRequests()
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest.request-status', compact('requests'));
    }
}