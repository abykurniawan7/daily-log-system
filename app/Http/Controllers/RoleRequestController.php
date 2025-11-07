<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoleRequestController extends Controller
{
    /**
     * Show form untuk mengajukan role request
     */
    public function create()
    {
        // Check jika sudah ada pending request
        $pendingRequest = Auth::user()->pendingRoleRequest();
        
        if ($pendingRequest) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Anda sudah memiliki pengajuan role yang sedang diproses.');
        }

        return view('role-requests.create');
    }

    /**
     * Store role request
     */
    public function store(Request $request)
    {
        // Validasi: user tidak boleh punya pending request
        if (Auth::user()->hasPendingRoleRequest()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda sudah memiliki pengajuan role yang sedang diproses.');
        }

        // Validasi input
        $validated = $request->validate([
            'requested_role' => 'required|in:supervisi,perizinan,karyawan',
            'deskripsi' => 'required|string|min:50|max:1000',
            'dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'requested_role.required' => 'Pilih role yang diinginkan.',
            'requested_role.in' => 'Role yang dipilih tidak valid.',
            'deskripsi.required' => 'Deskripsi/alasan harus diisi.',
            'deskripsi.min' => 'Deskripsi minimal 50 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'dokumen.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'dokumen.max' => 'Ukuran file maksimal 2MB.',
        ]);

        // Set bagian berdasarkan role
        $bagian = null;
        if ($validated['requested_role'] === 'perizinan') {
            $bagian = 'PKJ';
        } elseif ($validated['requested_role'] === 'karyawan') {
            $bagian = 'PGB';
        }

        // Handle file upload
        $dokumenPath = null;
        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('role-requests', 'public');
        }

        // Create role request
        RoleRequest::create([
            'user_id' => Auth::id(),
            'requested_role' => $validated['requested_role'],
            'requested_bagian' => $bagian,
            'deskripsi' => $validated['deskripsi'],
            'dokumen_path' => $dokumenPath,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengajuan role berhasil dikirim! Silakan tunggu persetujuan dari admin.');
    }

    /**
     * Cancel pending role request
     */
    public function cancel(RoleRequest $roleRequest)
    {
        // Pastikan request milik user yang login
        if ($roleRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa cancel jika masih pending
        if ($roleRequest->status !== 'pending') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Hanya pengajuan yang sedang diproses yang bisa dibatalkan.');
        }

        // Delete dokumen jika ada
        if ($roleRequest->dokumen_path) {
            Storage::disk('public')->delete($roleRequest->dokumen_path);
        }

        $roleRequest->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengajuan role berhasil dibatalkan.');
    }
}