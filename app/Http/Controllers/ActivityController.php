<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\LogActivity;
use App\Helpers\QueryOptimizer;

class ActivityController extends Controller
{
    /**
     * Check if user can export activities
     */
    private function canExportActivities($user): bool
    {
        return $user->role === 'supervisi' ||
            $user->role === 'kabag_pgb' ||
            $user->role === 'perizinan' ||
            ($user->role === 'karyawan' && in_array($user->bagian, ['PKJ', 'PGB']));
    }
    
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Base query
        $query = Activity::with(['project.pemilikProject', 'user']);
        
        // ✅ UPDATED: Filter berdasarkan role (PKJ bisa lihat SEMUA aktivitas PKJ)
        if ($user->role === 'supervisi') {
            // Supervisi: lihat semua aktivitas
            // Tidak ada filter tambahan
            
        } elseif ($user->role === 'kabag_pgb') {
            $query->where(function($q) use ($user) {
                // 1. Karyawan PGB (exclude self)
                $q->whereHas('user', function($userQuery) use ($user) {
                    $userQuery->where('role', 'karyawan')
                            ->where('bagian', 'PGB')
                            ->where('id', '!=', $user->id);
                })
                // 2. PKJ di project milik Kabag PGB
                ->orWhere(function($pkjQuery) use ($user) {
                    $pkjQuery->whereHas('user', function($u) {
                        $u->where('bagian', 'PKJ');
                    })
                    ->whereHas('project', function($p) use ($user) {
                        $p->where('user_id', $user->id);
                    });
                });
            });
            
        } elseif ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            // ✅ PKJ (Kabag + Staff): Lihat SEMUA aktivitas PKJ
            $query->whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            });
            
        } else {
            // ✅ Staff PGB: hanya lihat aktivitas sendiri
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
        
        // ✅ UPDATED: Quick stats (PKJ lihat semua stats PKJ)
        $statsQuery = Activity::query();
        
        // Apply same role filter untuk stats
        if ($user->role === 'supervisi') {
            // All activities
        } elseif ($user->role === 'kabag_pgb') {
            $statsQuery->where(function($q) use ($user) {
                $q->whereHas('user', function($userQuery) use ($user) {
                    $userQuery->where('role', 'karyawan')
                            ->where('bagian', 'PGB')
                            ->where('id', '!=', $user->id);
                })
                ->orWhere(function($pkjQuery) use ($user) {
                    $pkjQuery->whereHas('user', function($u) {
                        $u->where('bagian', 'PKJ');
                    })
                    ->whereHas('project', function($p) use ($user) {
                        $p->where('user_id', $user->id);
                    });
                });
            });
        } elseif ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            // PKJ: stats untuk semua aktivitas PKJ
            $statsQuery->whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
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
        
        // ✅ SAVE REFERRER: Simpan dari mana user datang
        $referrer = $request->headers->get('referer');
        session(['activity_referrer' => $referrer]);
        
        // ✅ PERBAIKAN: Kadiv BISA tambah aktivitas dengan 2 cara:
        // 1. Dari detail project (dengan project_id)
        // 2. Dari halaman Aktivitas Saya (tanpa project_id, pilih manual)
        
        if ($user->role === 'supervisi') {
            // Ambil project_id dari query parameter (jika ada)
            $requestedProjectId = $request->query('project_id');
            
            if ($requestedProjectId) {
                // ✅ SCENARIO 1: Dari detail project - Validasi project milik Kadiv
                $project = Project::find($requestedProjectId);
                
                if (!$project || $project->user_id !== $user->id) {
                    return redirect()->route('activities.my-activities')
                        ->with('error', 'Sebagai Kadiv, Anda hanya dapat menambah aktivitas di project yang Anda buat sendiri.');
                }
            }
            
            // ✅ SCENARIO 2: Dari Aktivitas Saya - Tampilkan semua project yang dia buat
            $projects = Project::with('pemilikProject')
                ->where('user_id', $user->id)
                ->where('status', '!=', 'Done')
                ->orderBy('nama_project')
                ->get();
            
            // Jika tidak ada project sama sekali
            if ($projects->isEmpty()) {
                return redirect()->route('activities.my-activities')
                    ->with('error', 'Anda belum memiliki project. Silakan buat project terlebih dahulu untuk menambah aktivitas.');
            }
            
            $selectedProjectId = $requestedProjectId;
            
            return view('activities.create', compact('projects', 'selectedProjectId'));
        }
        
        // Ambil project_id dari query parameter jika ada
        $selectedProjectId = $request->query('project_id');
        
        // ✅ EXISTING: Query projects berdasarkan role (kabag_pgb, perizinan, karyawan)
        if ($user->role === 'kabag_pgb') {
            $projects = Project::with('pemilikProject')
                ->where('status', '!=', 'Done')
                ->whereHas('pics', function($q) {
                    $q->where('users.bagian', 'PGB');
                })
                ->orderBy('nama_project')
                ->get();
                
        } elseif ($user->role === 'perizinan') {
            $projects = Project::with('pemilikProject')
                ->where('status', '!=', 'Done')
                ->orderBy('nama_project')
                ->get();
                
        } else {
            // PGB Staff
            $projects = Project::with('pemilikProject')
                ->where('status', '!=', 'Done')
                ->whereHas('pics', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('nama_project')
                ->get();
            
            // ✅ TAMBAHAN: Validasi jika PGB Staff tidak punya project
            if ($projects->isEmpty()) {
                return redirect()->route('activities.my-activities')
                    ->with('error', __('activities.no_project_assigned'));
            }
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
        $user = auth()->user();

        // ✅ PERBAIKAN: Validasi Kadiv lebih fleksibel
        if ($user->role === 'supervisi') {
            $projectId = $request->input('project_id');
            $project = Project::find($projectId);
            
            if (!$project || $project->user_id !== $user->id) {
                return back()
                    ->withInput()
                    ->withErrors(['project_id' => 'Sebagai Kadiv, Anda hanya dapat menambah aktivitas di project yang Anda buat sendiri.']);
            }
        }

        if ($user->role === 'karyawan' && $user->bagian === 'PGB') {
            $hasProject = Project::where('status', '!=', 'Done')
                ->whereHas('pics', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->exists();
            
            if (!$hasProject) {
                return back()
                    ->withInput()
                    ->withErrors(['project_id' => __('activities.no_project_assigned')]);
            }
        }

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
        
        // ✅ SMART REDIRECT: Cek dari mana user berasal
        $referrer = session('activity_referrer');
        session()->forget('activity_referrer');
        
        // Jika dari detail project (URL mengandung /projects/)
        if ($referrer && str_contains($referrer, '/projects/')) {
            return redirect()->route('projects.show', $activity->project_id)
                ->with('success', __('activities.activity_added_success'));
        }
        
        // Default: Kembali ke My Activities
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
        if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
            // Allow view all
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
        
        // ✅ UPDATED: Load pics instead of picProyek
        $activity->load(['project.pemilikProject', 'project.pics', 'user']);
        
        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $user = auth()->user();
        
        // ✅ SAVE REFERRER: Simpan dari mana user datang
        $referrer = request()->headers->get('referer');
        session(['activity_referrer' => $referrer]);
        
        // ✅ PERBAIKAN: Supervisi BISA edit aktivitas yang DIA BUAT SENDIRI
        if ($user->role === 'supervisi') {
            // Supervisi hanya bisa edit aktivitas yang dia buat sendiri
            if ($user->id !== $activity->user_id) {
                abort(403, 'Sebagai Supervisi, Anda hanya dapat mengedit aktivitas yang Anda buat sendiri.');
            }
        } else {
            // Non-supervisi: Hanya bisa edit aktivitas sendiri
            if ($user->id !== $activity->user_id) {
                abort(403, __('activities.can_only_edit_own'));
            }
        }
        
        // ✅ Query projects berdasarkan role (+ kabag_pgb + multi-PIC)
        if ($user->role === 'supervisi') {
            // ✅ Supervisi: Hanya project yang dia buat
            $projects = Project::with('pemilikProject')
                ->where('user_id', $user->id)
                ->get();
                
        } elseif ($user->role === 'kabag_pgb') {
            // Kabag PGB: semua project PGB
            $projects = Project::with('pemilikProject')
                ->whereHas('pics', function($q) {
                    $q->where('bagian', 'PGB');
                })
                ->get();
                
        } elseif ($user->role === 'perizinan') {
            // PKJ: semua project
            $projects = Project::with('pemilikProject')->get();
            
        } else {
            // PGB Staff: project assigned via pivot
            $projects = Project::with('pemilikProject')
                ->whereHas('pics', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
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
        
        if ($user->role === 'supervisi') {
            // Supervisi hanya bisa update aktivitas yang dia buat sendiri
            if ($user->id !== $activity->user_id) {
                abort(403, 'Sebagai Supervisi, Anda hanya dapat mengupdate aktivitas yang Anda buat sendiri.');
            }
        } else {
            // Non-supervisi: Hanya bisa update aktivitas sendiri
            if ($user->id !== $activity->user_id) {
                abort(403, __('activities.can_only_edit_own'));
            }
        }
        
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
        
        // ✅ SMART REDIRECT: Cek dari mana user berasal
        $referrer = session('activity_referrer');
        session()->forget('activity_referrer');
        
        // Jika dari detail project (URL mengandung /projects/)
        if ($referrer && str_contains($referrer, '/projects/')) {
            return redirect()->route('projects.show', $activity->project_id)
                ->with('success', __('activities.activity_updated_success'));
        }
        
        // Default: Kembali ke My Activities
        return redirect()->route('activities.my-activities')
            ->with('success', __('activities.activity_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Activity $activity)
    {
        $user = auth()->user();
    
        // ✅ FASE 4: Kadiv TIDAK BISA delete aktivitas (bahkan aktivitas sendiri)
        if ($user->role === 'supervisi') {
            abort(403, 'Sebagai Kadiv, Anda tidak dapat menghapus aktivitas. Kadiv hanya dapat membuat aktivitas baru di project yang Anda buat.');
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
        
        // ✅ SMART REDIRECT: Cek dari mana user berasal
        $referrer = request()->headers->get('referer');
        
        // Jika dari detail project (URL mengandung /projects/)
        if ($referrer && str_contains($referrer, '/projects/')) {
            return redirect()->route('projects.show', $projectId)
                ->with('success', __('activities.activity_deleted_success', ['name' => $namaAktivitas]));
        }
        
        // Default: Kembali ke My Activities
        return redirect()->route('activities.my-activities')
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
        
        // ✅ UPDATED: Logic berdasarkan role (+ kabag_pgb)
        if ($user->role === 'kabag_pgb') {
            if ($viewBagian === 'PKJ') {
                $query->whereHas('user', function ($q) {
                    $q->where('bagian', 'PKJ');
                });
            } else {
                $query->where('user_id', $user->id);
            }
            
        } elseif ($user->role === 'karyawan') {
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
        
        // ✅ UPDATED: Base query berdasarkan role (+ kabag_pgb)
        if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
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
        
        // ✅ NEW: Authorization check
        if (!$this->canExportActivities($user)) {
            abort(403, __('activities.unauthorized_export'));
        }
        
        // ✅ NEW: Query berdasarkan role (SAMA seperti logic di index())
        if ($user->role === 'supervisi') {
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user']);
        } 
        elseif ($user->role === 'kabag_pgb') {
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
                ->where(function($q) use ($user) {
                    // 1. Karyawan PGB (exclude self)
                    $q->whereHas('user', function($userQuery) use ($user) {
                        $userQuery->where('role', 'karyawan')
                                ->where('bagian', 'PGB')
                                ->where('id', '!=', $user->id);
                    })
                    // 2. PKJ di project milik Kabag PGB
                    ->orWhere(function($pkjQuery) use ($user) {
                        $pkjQuery->whereHas('user', function($u) {
                            $u->where('bagian', 'PKJ');
                        })
                        ->whereHas('project', function($p) use ($user) {
                            $p->where('user_id', $user->id);
                        });
                    });
                });
        }
        elseif ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            // ✅ NEW: PKJ (Kabag + Staff): Export SEMUA aktivitas PKJ
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
                ->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                });
        }
        else {
            // ✅ NEW: Staff PGB: Export aktivitas sendiri saja
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
                ->where('user_id', $user->id);
        }
        
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
        
        if ($request->filled('user_id')) {
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
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
        
        // ✅ FIXED: Scope untuk PKJ dan Kabag PGB
        if ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            $filterInfo['Scope'] = 'Aktivitas Bagian PKJ';
        } elseif ($user->role === 'kabag_pgb') {
            $filterInfo['Scope'] = 'Aktivitas Bagian PGB'; // ✅ FIXED: Bukan "Kabag PGB"
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
        
        // ✅ NEW: Authorization check
        if (!$this->canExportActivities($user)) {
            abort(403, 'Unauthorized. Only authorized users can export activities.');
        }
        
        // ✅ NEW: Query berdasarkan role (SAMA seperti exportPreview)
        if ($user->role === 'supervisi') {
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user']);
        } 
        elseif ($user->role === 'kabag_pgb') {
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
                ->where(function($q) use ($user) {
                    $q->whereHas('user', function($userQuery) use ($user) {
                        $userQuery->where('role', 'karyawan')
                                ->where('bagian', 'PGB')
                                ->where('id', '!=', $user->id);
                    })
                    ->orWhere(function($pkjQuery) use ($user) {
                        $pkjQuery->whereHas('user', function($u) {
                            $u->where('bagian', 'PKJ');
                        })
                        ->whereHas('project', function($p) use ($user) {
                            $p->where('user_id', $user->id);
                        });
                    });
                });
        }
        elseif ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            // ✅ NEW: PKJ: Export SEMUA aktivitas PKJ
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
                ->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                });
        }
        else {
            // ✅ NEW: Staff PGB: Export aktivitas sendiri
            $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
                ->where('user_id', $user->id);
        }
        
        // Apply filters (sama seperti exportPreview)
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
            if ($user->role === 'supervisi' || $user->role === 'kabag_pgb') {
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
        
        // ✅ FIXED: Scope untuk PKJ dan Kabag PGB
        if ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            $filterInfo['Scope'] = 'Aktivitas Bagian PKJ';
        } elseif ($user->role === 'kabag_pgb') {
            $filterInfo['Scope'] = 'Aktivitas Bagian PGB'; // ✅ FIXED
        }
        
        $pdf = Pdf::loadView('pdf.activities', compact('activities', 'filterInfo'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'activities_' . ($user->role === 'perizinan' || $user->bagian === 'PKJ' ? 'PKJ_' : ($user->role === 'kabag_pgb' ? 'PGB_' : '')) . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show export preview for My Activities page
     * Accessible by: Supervisi & PKJ (only their own activities)
     */
    public function myActivitiesExportPreview(Request $request)
    {
        $user = auth()->user();
        
        // ✅ NEW: Authorization check
        if (!$this->canExportActivities($user)) {
            abort(403, __('activities.unauthorized_export'));
        }
        
        // ✅ FIXED: Base query - ALWAYS filter by current user
        // Tidak peduli role apa, di halaman MY ACTIVITIES = aktivitas sendiri
        $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
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
        
        // ✅ FIXED: Prepare filter info - selalu menampilkan scope My Activities
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
        
        // ✅ NEW: Authorization check
        if (!$this->canExportActivities($user)) {
            abort(403, 'Unauthorized. Only authorized users can export activities.');
        }
        
        // ✅ FIXED: Base query - ALWAYS filter by current user
        $query = Activity::with(['project.pemilikProject', 'project.pics', 'user'])
            ->where('user_id', $user->id);
        
        // Apply filters (sama seperti myActivitiesExportPreview)
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
        
        // ✅ FIXED: Filter info selalu menampilkan scope My Activities
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

    /**
     * Download attachment file with proper headers
     */
    public function downloadAttachment(Activity $activity)
    {
        $user = auth()->user();
        
        // Authorization: User harus punya akses ke activity
        if ($user->role !== 'supervisi' && $user->role !== 'kabag_pgb' && $user->role !== 'perizinan') {
            if ($user->id !== $activity->user_id && $user->bagian !== $activity->user->bagian) {
                abort(403, 'Unauthorized access to download this attachment.');
            }
        }
        
        // Check if file exists
        if (!$activity->lampiran) {
            abort(404, 'No attachment found for this activity.');
        }
        
        $filePath = storage_path('app/public/' . $activity->lampiran);
        
        if (!file_exists($filePath)) {
            abort(404, 'Attachment file not found on server.');
        }
        
        // Get original filename
        $filename = basename($activity->lampiran);
        
        // Return file download with proper headers
        return response()->download($filePath, $filename, [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}