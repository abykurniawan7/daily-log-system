<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\LogActivity; // ✅ TAMBAHAN: Import helper
use App\Helpers\QueryOptimizer;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Base query
        $query = Activity::with(['project.pemilikProject', 'user']);
        
        // Filter berdasarkan role
        if ($user->role === 'supervisi') {
            // Supervisi: lihat semua aktivitas
            // Tidak ada filter tambahan
        } elseif ($user->role === 'perizinan') {
            // Perizinan: bisa switch view antara PKJ dan PGB
            $viewBagian = $request->get('view_bagian', 'PKJ'); // Default: PKJ
            
            if ($viewBagian === 'PGB') {
                // Lihat aktivitas dari user dengan bagian PGB
                $query->whereHas('user', function($q) {
                    $q->where('bagian', 'PGB');
                });
            } else {
                // Lihat aktivitas dari user dengan bagian PKJ (termasuk diri sendiri)
                $query->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                });
            }
        } else {
            // Karyawan (PGB): hanya lihat aktivitas sendiri
            $query->where('user_id', $user->id);
        }
        
        // ========== SEARCH FILTER ==========
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_aktivitas', 'like', "%{$search}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%")
                ->orWhereHas('project', function($subQuery) use ($search) {
                    $subQuery->where('nama_project', 'like', "%{$search}%");
                });
            });
        }
        
        // ========== STATUS FILTER ==========
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // ========== PROJECT FILTER ==========
        if ($request->filled('project_uuid')) {
            $query->where('project_uuid', $request->project_uuid);
        }
        
        // ========== PIC FILTER ==========
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // ========== DATE RANGE FILTER ==========

        // Quick Filter
        if ($request->filled('quick_filter')) {
            $today = now();
            
            switch ($request->quick_filter) {
                case 'today':
                    $query->whereDate('tanggal_mulai', $today);
                    break;
                    
                case 'this_week':
                    $query->whereBetween('tanggal_mulai', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                    
                case 'this_month':
                    $query->whereYear('tanggal_mulai', $today->year)
                        ->whereMonth('tanggal_mulai', $today->month);
                    break;
            }
        }

        // Manual Date Range Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_mulai', [
                $request->start_date, 
                $request->end_date
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal_mulai', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal_mulai', '<=', $request->end_date);
        }
        
        // ========== SORTING ==========
        $sortBy = $request->get('sort_by', 'tanggal_mulai');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'nama_aktivitas') {
            $query->orderBy('nama_aktivitas', $sortOrder);
        } elseif ($sortBy === 'status') {
            $query->orderBy('status', $sortOrder);
        } else {
            $query->orderBy('tanggal_mulai', $sortOrder);
        }
        
        // ========== PAGINATION ==========
        $activities = $query->paginate(10)->withQueryString();
        
        // ========== QUICK STATS ==========
        $statsQuery = Activity::query();
        
        // Apply same role filter untuk stats
        if ($user->role === 'supervisi') {
            // All activities
        } elseif ($user->role === 'perizinan') {
            $viewBagian = $request->get('view_bagian', 'PKJ');
            $statsQuery->whereHas('user', function($q) use ($viewBagian) {
                $q->where('bagian', $viewBagian);
            });
        } else {
            $statsQuery->where('user_id', $user->id);
        }
        
        $stats = [
            'total' => $statsQuery->count(),
            'progress' => (clone $statsQuery)->where('status', 'Progress')->count(),
            'pending' => (clone $statsQuery)->where('status', 'Pending')->count(),
            'done' => (clone $statsQuery)->where('status', 'Done')->count(),
        ];
        
        // Data untuk filter dropdown
        $projects = Project::select('id', 'uuid', 'nama_project')->orderBy('nama_project')->get();
        
        return view('activities.index', compact('activities', 'stats', 'projects'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Supervisi TIDAK bisa tambah aktivitas
        if ($user->role === 'supervisi') {
            abort(403, __('activities.supervisi_cannot_add'));
        }
        
        // Ambil project_id dari query parameter jika ada
        $selectedProjectId = $request->query('project_id');
        
        // Query projects berdasarkan role + FILTER HANYA PROJECT AKTIF
        if ($user->role === 'perizinan') {
            $projects = Project::with('pemilikProject')
                ->where('status', '!=', 'Done')
                ->orderBy('nama_project')
                ->get();
        } else {
            $projects = Project::with('pemilikProject')
                ->where('pic_proyek_id', $user->id)
                ->where('status', '!=', 'Done')
                ->orderBy('nama_project')
                ->get();
        }
        
        // Validasi jika project_id di-set tapi tidak valid
        if ($selectedProjectId) {
            $projectExists = $projects->contains('id', $selectedProjectId);
            
            if (!$projectExists) {
                return redirect()->route('activities.my-activities')
                    ->with('error', __('activities.no_access_to_project'));
            }
        }
        
        return view('activities.create', compact('projects', 'selectedProjectId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'nama_aktivitas' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'status' => 'required|in:Progress,Pending,Done',
            'deskripsi' => 'nullable|string',
            'lampiran_type' => 'required|in:file,link',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'lampiran_link' => 'nullable|url|max:500',
        ]);

        $project = Project::find($validated['project_id']);
        if ($project) {
            $validated['project_uuid'] = $project->uuid;
        }
        
        // Handle file upload (jika type = file)
        if ($request->lampiran_type === 'file' && $request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lampiran', $filename, 'public');
            $validated['lampiran'] = $path;
            $validated['lampiran_link'] = null;
        }
        // Handle link (jika type = link)
        elseif ($request->lampiran_type === 'link' && $request->filled('lampiran_link')) {
            $validated['lampiran'] = null;
            $validated['lampiran_link'] = $request->lampiran_link;
        }
        // Tidak ada lampiran sama sekali
        else {
            $validated['lampiran'] = null;
            $validated['lampiran_link'] = null;
        }
        
        // Remove lampiran_type dari validated
        unset($validated['lampiran_type']);
        
        // Set user_id
        $validated['user_id'] = auth()->id();
        
        // Create activity
        $activity = Activity::create($validated);
        
        // Load relationship
        $activity->load('project');
        
        // Log activity
        try {
            \App\Helpers\LogActivity::created(
                $activity, 
                "Menambah aktivitas '{$activity->nama_aktivitas}' pada project '{$activity->project->nama_project}'"
            );
        } catch (\Exception $e) {
            \Log::error('Failed to log activity creation: ' . $e->getMessage());
        }

        QueryOptimizer::clearUserCache(auth()->id());
        
        return redirect()->route('activities.my-activities')
            ->with('success', __('activities.activity_added_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $user = auth()->user();
        
        // Authorization logic
        if ($user->role === 'supervisi') {
            // Allow view
        }
        elseif ($user->bagian === 'PKJ') {
            // Allow view all
        }
        elseif ($user->bagian === 'PGB') {
            // Allow view all
        }
        else {
            if ($user->id !== $activity->user_id) {
                abort(403, __('activities.no_access_view'));
            }
        }
        
        $activity->load(['project.pemilikProject', 'project.picProyek', 'user']);
        
        return view('activities.show', compact('activity'));
    }

   /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $user = auth()->user();
        
        // Supervisi TIDAK BISA edit aktivitas
        if ($user->role === 'supervisi') {
            abort(403, __('activities.supervisi_cannot_edit'));
        }
        
        // Hanya bisa edit aktivitas sendiri
        if ($user->id !== $activity->user_id) {
            abort(403, __('activities.can_only_edit_own'));
        }
        
        // Query projects berdasarkan role
        if ($user->role === 'perizinan') {
            $projects = Project::with('pemilikProject')->get();
        } else {
            $projects = Project::with('pemilikProject')
                ->where('pic_proyek_id', $user->id)
                ->get();
        }
        
        return view('activities.edit', compact('activity', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $user = auth()->user();
        
        // Simpan old values
        $oldValues = $activity->only([
            'nama_aktivitas', 
            'jenis_kegiatan',
            'status', 
            'tanggal_mulai', 
            'tanggal_selesai',
            'project_id'
        ]);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'nama_aktivitas' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'status' => 'required|in:Progress,Pending,Done',
            'deskripsi' => 'nullable|string',
            'lampiran_type' => 'required|in:file,link',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'lampiran_link' => 'nullable|url|max:500',
        ]);

        if (isset($validated['project_id'])) {
            $project = Project::find($validated['project_id']);
            if ($project) {
                $validated['project_uuid'] = $project->uuid;
            }
        }
        
        // Handle file upload
        if ($request->lampiran_type === 'file' && $request->hasFile('lampiran')) {
            // Hapus file lama jika ada
            if ($activity->lampiran) {
                Storage::disk('public')->delete($activity->lampiran);
            }
            
            $file = $request->file('lampiran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lampiran', $filename, 'public');
            $validated['lampiran'] = $path;
            $validated['lampiran_link'] = null;
        }
        // Handle link
        elseif ($request->lampiran_type === 'link' && $request->filled('lampiran_link')) {
            // Hapus file lama jika ada
            if ($activity->lampiran) {
                Storage::disk('public')->delete($activity->lampiran);
            }
            
            $validated['lampiran'] = null;
            $validated['lampiran_link'] = $request->lampiran_link;
        }
        
        // Remove lampiran_type
        unset($validated['lampiran_type']);
        
        $activity->update($validated);
        
        // Log activity
        \App\Helpers\LogActivity::updated(
            $activity, 
            $oldValues, 
            "Mengubah aktivitas '{$activity->nama_aktivitas}'"
        );

        QueryOptimizer::clearUserCache(auth()->id());
        
        return redirect()->route('projects.show', $activity->project_id)
            ->with('success', __('activities.activity_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Activity $activity)
    {
        $user = auth()->user();
        
        // Supervisi TIDAK BISA delete
        if ($user->role === 'supervisi') {
            abort(403, __('activities.supervisi_cannot_delete'));
        }
        
        // Hanya bisa delete aktivitas sendiri
        if ($user->id !== $activity->user_id) {
            abort(403, __('activities.can_only_delete_own'));
        }
        
        $projectId = $activity->project_id;
        $namaAktivitas = $activity->nama_aktivitas;
        $projectName = $activity->project->nama_project;
        
        // LOG: Deleted activity (SEBELUM delete!)
        LogActivity::deleted(
            $activity, 
            "Menghapus aktivitas '{$namaAktivitas}' dari project '{$projectName}'"
        );
        
        // Hapus file lampiran jika ada
        if ($activity->lampiran) {
            Storage::disk('public')->delete($activity->lampiran);
        }
        
        $activity->delete();

        QueryOptimizer::clearUserCache(auth()->id());
        
        return redirect()->route('projects.show', $projectId)
            ->with('success', __('activities.activity_deleted_success', ['name' => $namaAktivitas]));
    }

    /**
     * Display user's own activities with advanced filters and switch view PKJ
     */
    public function myActivities(Request $request)
    {
        $user = auth()->user();
        $viewBagian = $request->get('view_bagian', null);
        
        // Base query
        $query = Activity::with(['project.pemilikProject', 'user']);
        
        // Logic berdasarkan role dan switch view
        if ($user->role === 'karyawan') {
            if ($viewBagian === 'PKJ') {
                $query->whereHas('user', function ($q) {
                    $q->where('bagian', 'PKJ');
                });
            } else {
                $query->where('user_id', $user->id);
            }
        } elseif ($user->role === 'perizinan') {
            if ($viewBagian === 'all_pkj') {
                $query->whereHas('user', function ($q) {
                    $q->where('bagian', 'PKJ');
                });
            } else {
                $query->where('user_id', $user->id);
            }
        } else {
            $query->where('user_id', $user->id);
        }
        
        // Filter by project
        if ($request->filled('project_uuid') && !$viewBagian) {
            $query->where('project_uuid', $request->project_uuid);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_mulai', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_mulai', '<=', $request->date_to);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_aktivitas', 'like', "%{$search}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $activities = $query->paginate(10)->withQueryString();
        
        // Get user's projects untuk filter dropdown
        if (!$viewBagian) {
            $userProjectIds = Activity::where('user_id', $user->id)
                ->distinct()
                ->pluck('project_id');
            
            $userProjects = Project::whereIn('id', $userProjectIds)
                ->orderBy('nama_project')
                ->get();
        } else {
            $userProjects = collect();
        }
        
        return view('activities.my-activities', compact('activities', 'userProjects', 'viewBagian'));
    }

    /**
     * Display activities in timeline view
     */
    public function timeline(Request $request)
    {
        $user = auth()->user();
        
        // Base query berdasarkan role
        if ($user->role === 'supervisi') {
            $query = Activity::with(['project.pemilikProject', 'user']);
        } elseif ($user->role === 'perizinan') {
            $viewBagian = $request->get('view_bagian', 'PKJ');
            
            $query = Activity::with(['project.pemilikProject', 'user'])
                ->whereHas('user', function($q) use ($viewBagian) {
                    $q->where('bagian', $viewBagian);
                });
        } else {
            $query = Activity::with(['project.pemilikProject', 'user'])
                ->where('user_id', $user->id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_mulai', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_mulai', '<=', $request->date_to);
        }
        
        // Get activities dan group by date
        $activities = $query->orderBy('tanggal_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($activity) {
                return $activity->tanggal_mulai->format('Y-m-d');
            });
        
        return view('activities.timeline', compact('activities'));
    }

    /**
     * Prepare filter info for PDF display
     */
    private function prepareFilterInfo(Request $request)
    {
        $filterInfo = [];
        
        if ($request->filled('search')) {
            $filterInfo['Pencarian'] = $request->search;
        }
        
        if ($request->filled('status')) {
            $filterInfo['Status'] = $request->status;
        }
        
        if ($request->filled('project_uuid')) {
            $project = Project::where('uuid', $request->project_uuid)->first();
            $filterInfo['Project'] = $project ? $project->nama_project : 'Unknown';
        }
        
        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
            $filterInfo['PIC'] = $user ? $user->name . ' (' . $user->bagian . ')' : 'Unknown';
        }
        
        if ($request->filled('quick_filter')) {
            $labels = [
                'today' => 'Hari Ini',
                'this_week' => 'Minggu Ini',
                'this_month' => 'Bulan Ini'
            ];
            $filterInfo['Periode'] = $labels[$request->quick_filter] ?? $request->quick_filter;
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filterInfo['Tanggal'] = \Carbon\Carbon::parse($request->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        } elseif ($request->filled('start_date')) {
            $filterInfo['Dari Tanggal'] = \Carbon\Carbon::parse($request->start_date)->format('d M Y');
        } elseif ($request->filled('end_date')) {
            $filterInfo['Sampai Tanggal'] = \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        }
        
        return $filterInfo;
    }

    /**
     * Show export preview before downloading PDF
     * Accessible by: Supervisi (ALL) & PKJ (ONLY THEIR ACTIVITIES)
     */
    public function exportPreview(Request $request)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($user->role === 'supervisi') {
            $query = Activity::with(['project.pemilikProject', 'project.picProyek', 'user']);
        } 
        elseif ($user->role === 'perizinan') {
            $query = Activity::with(['project.pemilikProject', 'project.picProyek', 'user'])
                ->where('user_id', $user->id);
        }
        else {
            abort(403, __('activities.unauthorized_export'));
        }
        
        // ========== APPLY FILTERS (sama seperti sebelumnya) ==========
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_aktivitas', 'like', "%{$search}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%")
                ->orWhereHas('project', function($subQuery) use ($search) {
                    $subQuery->where('nama_project', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('project_uuid')) {
            $query->where('project_uuid', $request->project_uuid);
        }
        
        if ($request->filled('user_id')) {
            // Jika PKJ, ignore filter user_id (hanya bisa lihat punya dia)
            if ($user->role === 'supervisi') {
                $query->where('user_id', $request->user_id);
            }
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '<=', $request->end_date);
        }
        
        if ($request->filled('quick_filter')) {
            $today = now();
            switch ($request->quick_filter) {
                case 'today':
                    $query->whereDate('tanggal_mulai', $today);
                    break;
                case 'this_week':
                    $query->whereBetween('tanggal_mulai', [
                        $today->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereYear('tanggal_mulai', $today->year)
                        ->whereMonth('tanggal_mulai', $today->month);
                    break;
            }
        }
        
        $activities = $query->orderBy('tanggal_mulai', 'desc')->get();
        
        $filterInfo = $this->prepareFilterInfo($request);
        
        // Tambahkan info role di filter
        if ($user->role === 'perizinan') {
            $filterInfo['Scope'] = 'Aktivitas Saya (' . $user->bagian . ')';
        }
        
        $stats = [
            'total' => $activities->count(),
            'progress' => $activities->where('status', 'Progress')->count(),
            'pending' => $activities->where('status', 'Pending')->count(),
            'done' => $activities->where('status', 'Done')->count(),
        ];
        
        return view('activities.export-preview', compact('activities', 'filterInfo', 'stats'));
    }

    /**
     * Export activities to PDF with filters
     * Accessible by: Supervisi (ALL) & PKJ (ONLY THEIR ACTIVITIES)
     */
    public function exportPdf(Request $request)
    {
        // 🔒 LOCK LOCALE KE BAHASA INDONESIA UNTUK PDF
        \App::setLocale('id');
        
        $user = auth()->user();
        
        // Authorization check
        if ($user->role === 'supervisi') {
            $query = Activity::with(['project.pemilikProject', 'project.picProyek', 'user']);
        } 
        elseif ($user->role === 'perizinan') {
            $query = Activity::with(['project.pemilikProject', 'project.picProyek', 'user'])
                ->where('user_id', $user->id);
        }
        else {
            abort(403, 'Unauthorized. Only Supervisi and PKJ can export activities.');
        }
        
        // Apply filters (sama seperti sebelumnya)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_aktivitas', 'like', "%{$search}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%")
                ->orWhereHas('project', function($subQuery) use ($search) {
                    $subQuery->where('nama_project', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('project_uuid')) {
            $query->where('project_uuid', $request->project_uuid);
        }
        
        if ($request->filled('user_id')) {
            if ($user->role === 'supervisi') {
                $query->where('user_id', $request->user_id);
            }
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '<=', $request->end_date);
        }
        
        if ($request->filled('quick_filter')) {
            $today = now();
            switch ($request->quick_filter) {
                case 'today':
                    $query->whereDate('tanggal_mulai', $today);
                    break;
                case 'this_week':
                    $query->whereBetween('tanggal_mulai', [
                        $today->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereYear('tanggal_mulai', $today->year)
                        ->whereMonth('tanggal_mulai', $today->month);
                    break;
            }
        }
        
        $activities = $query->orderBy('tanggal_mulai', 'desc')->get();
        
        $filterInfo = $this->prepareFilterInfo($request);
        
        if ($user->role === 'perizinan') {
            $filterInfo['Scope'] = 'Aktivitas Saya (' . $user->bagian . ')';
        }
        
        $pdf = Pdf::loadView('pdf.activities', compact('activities', 'filterInfo'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'activities_' . ($user->role === 'perizinan' ? $user->bagian . '_' : '') . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show export preview for My Activities page
     * Accessible by: Supervisi & PKJ (only their own activities)
     */
    public function myActivitiesExportPreview(Request $request)
    {
        $user = auth()->user();
        
        // Authorization check
        if (!in_array($user->role, ['supervisi', 'perizinan'])) {
            abort(403, __('activities.unauthorized_export'));
        }
        
        // Base query - ALWAYS filter by current user
        $query = Activity::with(['project.pemilikProject', 'project.picProyek', 'user'])
            ->where('user_id', $user->id);
        
        // ========== APPLY FILTERS ==========
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_aktivitas', 'like', "%{$search}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%")
                ->orWhereHas('project', function($subQuery) use ($search) {
                    $subQuery->where('nama_project', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('project_uuid')) {
            $query->where('project_uuid', $request->project_uuid);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_mulai', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_mulai', '<=', $request->date_to);
        }
        
        $activities = $query->orderBy('tanggal_mulai', 'desc')->get();
        
        // Prepare filter info
        $filterInfo = [
            'Scope' => 'Aktivitas Saya - ' . $user->name . ' (' . $user->bagian . ')'
        ];
        
        if ($request->filled('search')) {
            $filterInfo['Pencarian'] = $request->search;
        }
        
        if ($request->filled('status')) {
            $filterInfo['Status'] = $request->status;
        }
        
        if ($request->filled('project_uuid')) {
            $project = Project::where('uuid', $request->project_uuid)->first();
            $filterInfo['Project'] = $project ? $project->nama_project : 'Unknown';
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filterInfo['Tanggal'] = \Carbon\Carbon::parse($request->date_from)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->date_to)->format('d M Y');
        } elseif ($request->filled('date_from')) {
            $filterInfo['Dari Tanggal'] = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        } elseif ($request->filled('date_to')) {
            $filterInfo['Sampai Tanggal'] = \Carbon\Carbon::parse($request->date_to)->format('d M Y');
        }
        
        $stats = [
            'total' => $activities->count(),
            'progress' => $activities->where('status', 'Progress')->count(),
            'pending' => $activities->where('status', 'Pending')->count(),
            'done' => $activities->where('status', 'Done')->count(),
        ];
        
        return view('activities.export-preview', compact('activities', 'filterInfo', 'stats'));
    }

    /**
     * Export My Activities to PDF
     * Accessible by: Supervisi & PKJ (only their own activities)
     */
   
    public function myActivitiesExportPdf(Request $request)
    {
        // 🔒 LOCK LOCALE KE BAHASA INDONESIA UNTUK PDF
        \App::setLocale('id');
        
        $user = auth()->user();
        
        // Authorization check
        if (!in_array($user->role, ['supervisi', 'perizinan'])) {
            abort(403, 'Unauthorized. Only Supervisi and PKJ can export activities.');
        }
        
        // Base query - ALWAYS filter by current user
        $query = Activity::with(['project.pemilikProject', 'project.picProyek', 'user'])
            ->where('user_id', $user->id);
        
        // Apply filters (sama seperti sebelumnya)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_aktivitas', 'like', "%{$search}%")
                ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%")
                ->orWhereHas('project', function($subQuery) use ($search) {
                    $subQuery->where('nama_project', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('project_uuid')) {
            $query->where('project_uuid', $request->project_uuid);
        }
                
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_mulai', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_mulai', '<=', $request->date_to);
        }
        
        $activities = $query->orderBy('tanggal_mulai', 'desc')->get();
        
        $filterInfo = [
            'Scope' => 'Aktivitas Saya - ' . $user->name . ' (' . $user->bagian . ')'
        ];
        
        if ($request->filled('search')) {
            $filterInfo['Pencarian'] = $request->search;
        }
        
        if ($request->filled('status')) {
            $filterInfo['Status'] = $request->status;
        }
        
        if ($request->filled('project_uuid')) {
            $project = Project::where('uuid', $request->project_uuid)->first();
            $filterInfo['Project'] = $project ? $project->nama_project : 'Unknown';
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filterInfo['Tanggal'] = \Carbon\Carbon::parse($request->date_from)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->date_to)->format('d M Y');
        } elseif ($request->filled('date_from')) {
            $filterInfo['Dari Tanggal'] = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        } elseif ($request->filled('date_to')) {
            $filterInfo['Sampai Tanggal'] = \Carbon\Carbon::parse($request->date_to)->format('d M Y');
        }
        
        $pdf = Pdf::loadView('pdf.activities', compact('activities', 'filterInfo'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'my_activities_' . $user->bagian . '_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}