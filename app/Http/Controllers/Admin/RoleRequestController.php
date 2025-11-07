<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleRequestController extends Controller
{
    /**
     * Display list of role requests
     */
    public function index(Request $request)
    {
        $query = RoleRequest::with(['user', 'reviewer'])
            ->orderBy('created_at', 'desc');

        // Filter by status - gunakan filled() bukan has()
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by requested role - gunakan filled() bukan has()
        if ($request->filled('role')) {
            $query->where('requested_role', $request->role);
        }

        // Search by user name or email - gunakan filled() bukan has()
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $requests = $query->paginate(15);
        $pendingCount = RoleRequest::where('status', 'pending')->count();

        return view('admin.role-requests.index', [
            'requests' => $requests,
            'pendingCount' => $pendingCount
        ]);
    }

    /**
     * Show detail of specific role request
     */
    public function show(RoleRequest $roleRequest)
    {
        $roleRequest->load(['user', 'reviewer', 'approvedBy']);
        
        return view('admin.role-requests.show', compact('roleRequest'));
    }

    /**
     * Approve role request
     */
    public function approve(RoleRequest $roleRequest)
    {
        if ($roleRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update user role & bagian
            $roleRequest->user->update([
                'role' => $roleRequest->requested_role,
                'bagian' => $roleRequest->requested_bagian,
            ]);

            // Update request status
            $roleRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.role-requests.index')
                ->with('success', "Pengajuan dari {$roleRequest->user->name} berhasil disetujui!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject role request
     */
    public function reject(Request $request, RoleRequest $roleRequest)
    {
        if ($roleRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi.',
            'rejection_reason.min' => 'Alasan minimal 10 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $roleRequest->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            DB::commit();

            return redirect()->route('admin.role-requests.index')
                ->with('success', 'Pengajuan berhasil ditolak dan notifikasi telah dikirim ke user.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete role request (untuk history yang sudah diproses)
     */
    public function destroy(RoleRequest $roleRequest)
    {
        try {
            $roleRequest->delete();
            
            return redirect()->route('admin.role-requests.index')
                ->with('success', 'History role request berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}