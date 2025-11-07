<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Base query berdasarkan role & bagian
        if ($user->role === 'supervisi') {
            // Supervisi: lihat semua project
            $query = Project::with(['pemilikProject', 'picProyek']);
        } elseif ($user->bagian === 'PKJ') {
            // PKJ: lihat SEMUA project (NEW!)
            $query = Project::with(['pemilikProject', 'picProyek']);
        } else {
            // PGB: hanya project milik sendiri
            $query = Project::with(['pemilikProject', 'picProyek'])
                ->where('pic_proyek_id', $user->id);
        }
        
        // Search by nama project, PIC, atau divisi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                  ->orWhereHas('picProyek', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pemilikProject', function($q) use ($search) {
                      $q->where('nama_divisi', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by Urgensi
        if ($request->filled('urgensi')) {
            $query->where('urgensi', $request->urgensi);
        }
        
        // Filter by Divisi
        if ($request->filled('division_id')) {
            $query->where('pemilik_project_id', $request->division_id);
        }
        
        // Date Range Filter
        if ($request->filled('start_date')) {
            $query->where('tanggal_inisiasi', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('tanggal_inisiasi', '<=', $request->end_date);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['nama_project', 'tanggal_inisiasi', 'target_implementasi', 'status', 'urgensi', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $projects = $query->paginate(10)->appends($request->query());
        
        // Get divisions untuk filter dropdown
        $divisions = Division::all();
        
        // Quick stats - UPDATED untuk PKJ
        if ($user->role === 'supervisi') {
            $stats = [
                'total' => Project::count(),
                'progress' => Project::where('status', 'Progress')->count(),
                'pending' => Project::where('status', 'Pending')->count(),
                'done' => Project::where('status', 'Done')->count(),
            ];
        } elseif ($user->bagian === 'PKJ') {
            // PKJ: stats dari semua project
            $stats = [
                'total' => Project::count(),
                'progress' => Project::where('status', 'Progress')->count(),
                'pending' => Project::where('status', 'Pending')->count(),
                'done' => Project::where('status', 'Done')->count(),
            ];
        } else {
            // PGB: stats hanya project sendiri
            $stats = [
                'total' => Project::where('pic_proyek_id', $user->id)->count(),
                'progress' => Project::where('pic_proyek_id', $user->id)->where('status', 'Progress')->count(),
                'pending' => Project::where('pic_proyek_id', $user->id)->where('status', 'Pending')->count(),
                'done' => Project::where('pic_proyek_id', $user->id)->where('status', 'Done')->count(),
            ];
        }
        
        return view('projects.index', compact('projects', 'divisions', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Ambil semua divisi untuk dropdown
        $divisions = Division::all();
        
        // ✅ LOGIC PIC BERDASARKAN ROLE PEMBUAT PROJECT
        if ($user->role === 'supervisi') {
            // Supervisi: Hanya tampilkan karyawan PGB
            $users = User::where('role', 'karyawan')
                ->where('bagian', 'PGB')
                ->orderBy('name', 'asc')
                ->get();
        } elseif ($user->role === 'perizinan') {
            // PKJ: Hanya tampilkan karyawan PKJ (termasuk dirinya sendiri)
            $users = User::where('role', 'perizinan')
                ->where('bagian', 'PKJ')
                ->orderBy('name', 'asc')
                ->get();
        } else {
            // PGB: Tidak boleh create project (fallback, seharusnya sudah di-handle middleware)
            abort(403, 'Unauthorized. PGB tidak dapat membuat project.');
        }

        return view('projects.create', compact('divisions', 'users'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dengan custom messages
        $validated = $request->validate([
            'tanggal_inisiasi' => 'required|date',
            'target_implementasi' => 'required|string|max:255',
            'nama_project' => 'required|string|max:255',
            'urgensi' => 'required|in:Low,Medium,High,Very High',
            'sifat_project' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'pemilik_project_id' => 'required|exists:divisions,id',
            'pic_proyek_id' => 'required|exists:users,id',
            'status' => 'required|in:Progress,Pending,Done',
        ], [
            'urgensi.required' => __('projects.urgency_required'),
            'sifat_project.required' => __('projects.nature_required'),
            'pemilik_project_id.required' => __('projects.division_required'),
            'pic_proyek_id.required' => __('projects.pic_required'),
            'status.required' => __('projects.status_required'),
        ]);

        // Set user_id ke user yang sedang login (creator)
        $validated['user_id'] = auth()->id();

        // Simpan ke database
        Project::create($validated);

        // Redirect dengan pesan sukses
        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil ditambahkan!');
    }

    /**
     * Display the specified project with activities (paginated & filtered)
     */
    public function show(Request $request, Project $project)
    {
        $user = auth()->user();
        
        // Load project creator untuk decision logic
        $project->load(['pemilikProject', 'picProyek', 'creator']);
        
        // ✅ DECISION: Tentukan apakah perlu toggle berdasarkan pembuat project
        $projectCreator = $project->creator;
        $showToggle = ($projectCreator && $projectCreator->role === 'supervisi');
        
        // Determine view mode (PGB or PKJ)
        if ($showToggle) {
            // Project dibuat Supervisi → ada toggle, default sesuai user bagian
            $viewBagian = $request->get('view_bagian', $user->bagian ?? 'PGB');
        } else {
            // Project dibuat PKJ → tidak ada toggle, selalu tampilkan aktivitas PKJ
            $viewBagian = 'PKJ';
        }
        
        // Base query for activities
        $activitiesQuery = $project->activities()->with('user');
        
        // Filter based on view_bagian (HANYA jika ada toggle)
        if ($showToggle) {
            if ($viewBagian === 'PGB') {
                $activitiesQuery->whereHas('user', function($q) {
                    $q->where('bagian', 'PGB');
                });
            } elseif ($viewBagian === 'PKJ') {
                $activitiesQuery->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                });
            }
        } else {
            // Jika tidak ada toggle (project PKJ), tampilkan semua aktivitas (seharusnya hanya PKJ)
            // Tidak perlu filter tambahan karena project PKJ biasanya hanya ada aktivitas PKJ
            // Tapi untuk safety, bisa tambahkan filter:
            $activitiesQuery->whereHas('user', function($q) {
                $q->where('bagian', 'PKJ');
            });
        }
        
        // Sorting: Done activities at bottom, recent at top
        $activitiesQuery->orderByRaw("CASE WHEN status = 'Done' THEN 1 ELSE 0 END")
                        ->orderBy('tanggal_mulai', 'desc');
        
        // Paginate activities (10 per page)
        if ($showToggle) {
            // Jika ada toggle, append view_bagian ke pagination
            $activities = $activitiesQuery->paginate(10)->appends(['view_bagian' => $viewBagian]);
        } else {
            // Jika tidak ada toggle, pagination biasa
            $activities = $activitiesQuery->paginate(10);
        }
        
        return view('projects.show', compact('project', 'activities', 'viewBagian', 'showToggle'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $user = auth()->user();
        
        // Authorization: FIXED LOGIC
        if ($user->role === 'supervisi') {
            // Supervisi HANYA bisa edit project yang dia buat sendiri (user_id)
            if ($project->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit project yang Anda buat sendiri.');
            }
        } elseif ($user->bagian === 'PKJ') {
            // PKJ HANYA bisa edit project yang dia buat sendiri (user_id)
            if ($project->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit project yang Anda buat sendiri.');
            }
        } else {
            // PGB hanya bisa edit project sendiri (pic_proyek_id)
            if ($project->pic_proyek_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit project yang Anda buat sendiri.');
            }
        }

        $divisions = Division::all();
        
        // ✅ LOGIC PIC BERDASARKAN PEMBUAT PROJECT (dari $project->creator)
        $projectCreator = $project->creator;
        
        if ($projectCreator && $projectCreator->role === 'supervisi') {
            // Project dibuat Supervisi → Hanya tampilkan PGB
            $users = User::where('role', 'karyawan')
                ->where('bagian', 'PGB')
                ->orderBy('name', 'asc')
                ->get();
        } elseif ($projectCreator && $projectCreator->role === 'perizinan') {
            // Project dibuat PKJ → Hanya tampilkan PKJ
            $users = User::where('role', 'perizinan')
                ->where('bagian', 'PKJ')
                ->orderBy('name', 'asc')
                ->get();
        } else {
            // Fallback (seharusnya tidak terjadi)
            $users = User::whereIn('role', ['perizinan', 'karyawan'])
                ->orderBy('name', 'asc')
                ->get();
        }
        
        return view('projects.edit', compact('project', 'divisions', 'users'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = auth()->user();
        
        // Authorization: FIXED LOGIC (sama seperti edit)
        if ($user->role === 'supervisi') {
            if ($project->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengupdate project yang Anda buat sendiri.');
            }
        } elseif ($user->bagian === 'PKJ') {
            if ($project->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengupdate project yang Anda buat sendiri.');
            }
        } else {
            if ($project->pic_proyek_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengupdate project yang Anda buat sendiri.');
            }
        }

        // Validation dengan custom messages
        $validated = $request->validate([
            'tanggal_inisiasi' => 'required|date',
            'target_implementasi' => 'required|string|max:255',
            'nama_project' => 'required|string|max:255',
            'urgensi' => 'required|in:Low,Medium,High,Very High',
            'sifat_project' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'pemilik_project_id' => 'required|exists:divisions,id',
            'pic_proyek_id' => 'required|exists:users,id',
            'status' => 'required|in:Progress,Pending,Done',
        ], [
            'urgensi.required' => __('projects.urgency_required'),
            'sifat_project.required' => __('projects.nature_required'),
            'pemilik_project_id.required' => __('projects.division_required'),
            'pic_proyek_id.required' => __('projects.pic_required'),
            'status.required' => __('projects.status_required'),
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil diupdate!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $user = auth()->user();
        
        // Authorization: FIXED LOGIC
        if ($user->role === 'supervisi') {
            // Supervisi HANYA bisa delete project yang dia buat sendiri
            if ($project->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat menghapus project yang Anda buat sendiri.');
            }
        } elseif ($user->bagian === 'PKJ') {
            // PKJ HANYA bisa delete project yang dia buat sendiri
            if ($project->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat menghapus project yang Anda buat sendiri.');
            }
        } else {
            // PGB hanya bisa delete project sendiri
            if ($project->pic_proyek_id !== $user->id) {
                abort(403, 'Anda hanya dapat menghapus project yang Anda buat sendiri.');
            }
        }

        $namaProject = $project->nama_project;
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', "Project '$namaProject' berhasil dihapus!");
    }

    /**
     * Show export preview before downloading PDF
     * Accessible by: 
     * - Supervisi: ALL projects
     * - PKJ: Projects they created OR projects with their activities
     */
    public function exportPreview(Request $request)
    {
        $user = auth()->user();
        
        // Authorization & Query Logic
        if ($user->role === 'supervisi') {
            // Supervisi: bisa preview SEMUA project
            $query = Project::with(['pemilikProject', 'picProyek', 'activities']);
        } 
        elseif ($user->role === 'perizinan') {
            // PKJ: hanya project yang:
            // 1. DIA yang buat (user_id = PKJ), ATAU
            // 2. Ada aktivitas dia di project tersebut
            $query = Project::with(['pemilikProject', 'picProyek', 'activities'])
                ->where(function($q) use ($user) {
                    // Project yang DIA BUAT
                    $q->where('user_id', $user->id)
                    // ATAU project yang ada aktivitas dia
                    ->orWhereHas('activities', function($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                });
        }
        else {
            abort(403, 'Unauthorized. Only Supervisi and PKJ can preview project exports.');
        }
        
        // ========== APPLY FILTERS ==========
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                ->orWhereHas('picProyek', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('pemilikProject', function($q) use ($search) {
                    $q->where('nama_divisi', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('urgensi')) {
            $query->where('urgensi', $request->urgensi);
        }
        
        if ($request->filled('division_id')) {
            $query->where('pemilik_project_id', $request->division_id);
        }
        
        // Date Range Filter
        if ($request->filled('start_date')) {
            $query->where('tanggal_inisiasi', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('tanggal_inisiasi', '<=', $request->end_date);
        }
        
        // Get all projects (no pagination)
        $projects = $query->orderBy('tanggal_inisiasi', 'desc')->get();
        
        // Prepare filter info
        $filterInfo = [];
        
        if ($request->filled('search')) {
            $filterInfo['search'] = $request->search;
        }
        
        if ($request->filled('status')) {
            $filterInfo['status'] = $request->status;
        }
        
        if ($request->filled('urgensi')) {
            $filterInfo['urgensi'] = $request->urgensi;
        }
        
        if ($request->filled('division_id')) {
            $division = Division::find($request->division_id);
            $filterInfo['division'] = $division ? $division->nama_divisi : '-';
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filterInfo['date_range'] = \Carbon\Carbon::parse($request->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        } elseif ($request->filled('start_date')) {
            $filterInfo['date_from'] = \Carbon\Carbon::parse($request->start_date)->format('d M Y');
        } elseif ($request->filled('end_date')) {
            $filterInfo['date_to'] = \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        }
        
        // Tambahkan info scope untuk PKJ
        if ($user->role === 'perizinan') {
            $filterInfo['scope'] = 'Project yang saya buat atau saya kontribusi (' . $user->bagian . ')';
        }
        
        // Stats summary
        $stats = [
            'total' => $projects->count(),
            'progress' => $projects->where('status', 'Progress')->count(),
            'pending' => $projects->where('status', 'Pending')->count(),
            'done' => $projects->where('status', 'Done')->count(),
        ];
        
        return view('projects.export-preview', compact('projects', 'filterInfo', 'stats'));
    }

    /**
     * Export projects to PDF
     * Accessible by: 
     * - Supervisi: ALL projects
     * - PKJ: Projects they created OR projects with their activities
     */
    public function exportPdf(Request $request)
    {
        // 🔒 LOCK LOCALE KE BAHASA INDONESIA UNTUK PDF
        \App::setLocale('id');
        
        $user = auth()->user();
        
        // Authorization & Query Logic
        if ($user->role === 'supervisi') {
            $query = Project::with(['pemilikProject', 'picProyek', 'activities']);
        } 
        elseif ($user->role === 'perizinan') {
            $query = Project::with(['pemilikProject', 'picProyek', 'activities'])
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                    ->orWhereHas('activities', function($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                });
        }
        else {
            abort(403, 'Unauthorized. Only Supervisi and PKJ can export projects.');
        }
        
        // Apply filters (sama seperti sebelumnya)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                ->orWhereHas('picProyek', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('pemilikProject', function($q) use ($search) {
                    $q->where('nama_divisi', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('urgensi')) {
            $query->where('urgensi', $request->urgensi);
        }
        
        if ($request->filled('division_id')) {
            $query->where('pemilik_project_id', $request->division_id);
        }
        
        if ($request->filled('start_date')) {
            $query->where('tanggal_inisiasi', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('tanggal_inisiasi', '<=', $request->end_date);
        }
        
        $projects = $query->orderBy('tanggal_inisiasi', 'desc')->get();
        $divisions = Division::all();
        
        $pdf = Pdf::loadView('pdf.projects', compact('projects', 'divisions'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'projects_' . ($user->role === 'perizinan' ? $user->bagian . '_' : '') . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show export preview for SINGLE project
     * Accessible by: Supervisi, PKJ (yang buat/kontribusi), dan PGB (PIC/kontributor)
     */
    public function singleProjectExportPreview(Project $project)
    {
        $user = auth()->user();
        
        // Authorization check
        $canExport = false;
        
        if ($user->role === 'supervisi') {
            // Supervisi bisa export semua project
            $canExport = true;
        } elseif ($user->role === 'perizinan') {
            // PKJ: bisa export jika dia pembuat ATAU ada aktivitasnya
            $canExport = $project->user_id === $user->id 
                    || $project->activities()->where('user_id', $user->id)->exists();
        } elseif ($user->role === 'karyawan' && $user->bagian === 'PGB') {
            // ✅ PGB: bisa export jika dia PIC ATAU ada aktivitasnya
            $canExport = $user->id === $project->pic_proyek_id
                    || $project->activities()->where('user_id', $user->id)->exists();
        }
        
        if (!$canExport) {
            abort(403, 'Unauthorized. You cannot export this project.');
        }
        
        // Load relationships
        $project->load([
            'pemilikProject', 
            'picProyek', 
            'activities' => function($query) {
                $query->with('user')
                    ->orderBy('tanggal_mulai', 'desc');
            }
        ]);
        
        // Separate activities by bagian
        $activitiesPGB = $project->activities->filter(function($activity) {
            return $activity->user->bagian === 'PGB';
        });
        
        $activitiesPKJ = $project->activities->filter(function($activity) {
            return $activity->user->bagian === 'PKJ';
        });
        
        // Stats for this project
        $stats = [
            'total_activities' => $project->activities->count(),
            'pgb_activities' => $activitiesPGB->count(),
            'pkj_activities' => $activitiesPKJ->count(),
            'progress' => $project->activities->where('status', 'Progress')->count(),
            'pending' => $project->activities->where('status', 'Pending')->count(),
            'done' => $project->activities->where('status', 'Done')->count(),
        ];
        
        return view('projects.single-export-preview', compact('project', 'activitiesPGB', 'activitiesPKJ', 'stats'));
    }

    /**
     * Export SINGLE project to PDF
     * Accessible by: Supervisi, PKJ (yang buat/kontribusi), dan PGB (PIC/kontributor)
     */
    public function singleProjectExportPdf(Project $project)
    {
        // 🔒 LOCK LOCALE KE BAHASA INDONESIA UNTUK PDF
        \App::setLocale('id');
        
        $user = auth()->user();
        
        // Authorization check
        $canExport = false;
        
        if ($user->role === 'supervisi') {
            // Supervisi bisa export semua project
            $canExport = true;
        } elseif ($user->role === 'perizinan') {
            // PKJ: bisa export jika dia pembuat ATAU ada aktivitasnya
            $canExport = $project->user_id === $user->id 
                    || $project->activities()->where('user_id', $user->id)->exists();
        } elseif ($user->role === 'karyawan' && $user->bagian === 'PGB') {
            // ✅ PGB: bisa export jika dia PIC ATAU ada aktivitasnya
            $canExport = $user->id === $project->pic_proyek_id
                    || $project->activities()->where('user_id', $user->id)->exists();
        }
        
        if (!$canExport) {
            abort(403, 'Unauthorized. You cannot export this project.');
        }
        
        // Load relationships
        $project->load([
            'pemilikProject', 
            'picProyek', 
            'activities' => function($query) {
                $query->with('user')
                    ->orderBy('tanggal_mulai', 'desc');
            }
        ]);
        
        // Separate activities by bagian
        $activitiesPGB = $project->activities->filter(function($activity) {
            return $activity->user->bagian === 'PGB';
        });
        
        $activitiesPKJ = $project->activities->filter(function($activity) {
            return $activity->user->bagian === 'PKJ';
        });
        
        // Generate PDF
        $pdf = Pdf::loadView('pdf.single-project', compact('project', 'activitiesPGB', 'activitiesPKJ'));
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'project_' . \Str::slug($project->nama_project) . '_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}