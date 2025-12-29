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

        $filter = $request->get('filter', 'all');
        session(['projects_filter' => $filter]);

        session(['projects_current_filter' => $filter]);
        
        // Base query berdasarkan role
        if ($user->role === 'supervisi') {
            $query = Project::with(['pemilikProject', 'pics']);
            
            if ($filter === 'my') {
                // ✅ FIXED: Project yang dia buat ATAU dia jadi PIC
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id) // Creator
                    ->orWhereHas('pics', function($subQ) use ($user) {
                        $subQ->where('user_id', $user->id); // PIC
                    });
                });
            }
            
        } elseif ($user->role === 'kabag_pgb') {
            $query = Project::with(['pemilikProject', 'pics']);
            
            if ($filter === 'my') {
                // ✅ FIXED: Project yang dia buat ATAU dia jadi PIC
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id) // Creator
                    ->orWhereHas('pics', function($subQ) use ($user) {
                        $subQ->where('user_id', $user->id); // PIC
                    });
                });
            }
            
        } elseif ($user->isPKJ()) {
            // ✅ FIXED: Gunakan isPKJ() helper - cover Kabag PKJ & Staff PKJ
            $query = Project::with(['pemilikProject', 'pics']);
            
            if ($filter === 'my') {
                // ✅ FIXED: Project yang dia buat ATAU dia jadi PIC
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id) // Creator
                    ->orWhereHas('pics', function($subQ) use ($user) {
                        $subQ->where('user_id', $user->id); // PIC (dari pivot table)
                    });
                });
            }
            
        } else {
            // PGB Staff: hanya project yang dia jadi PIC (tidak bisa create project)
            $query = Project::with(['pemilikProject', 'pics'])
                ->whereHas('pics', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        }
        
        // Search by nama project, PIC, atau divisi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                ->orWhereHas('pics', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('pemilikProject', function($subQ) use ($search) {
                    $subQ->where('nama_divisi', 'like', "%{$search}%");
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
        
        // Paginate
        $projects = $query->paginate(10)->appends($request->query());
        
        // Get divisions untuk filter dropdown
        $divisions = Division::all();
        
        // ✅ FIXED: Stats calculation
        if ($user->role === 'supervisi' || $user->role === 'kabag_pgb' || $user->isPKJ()) {
            if ($filter === 'my') {
                // ✅ FIXED: Stats untuk My Projects - include projects sebagai PIC
                $myProjectIds = Project::where('user_id', $user->id)
                    ->orWhereHas('pics', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->pluck('id');
                
                $stats = [
                    'total' => $myProjectIds->count(),
                    'progress' => Project::whereIn('id', $myProjectIds)->where('status', 'Progress')->count(),
                    'pending' => Project::whereIn('id', $myProjectIds)->where('status', 'Pending')->count(),
                    'done' => Project::whereIn('id', $myProjectIds)->where('status', 'Done')->count(),
                ];
            } else {
                // Stats untuk All Projects
                $stats = [
                    'total' => Project::count(),
                    'progress' => Project::where('status', 'Progress')->count(),
                    'pending' => Project::where('status', 'Pending')->count(),
                    'done' => Project::where('status', 'Done')->count(),
                ];
            }
        } else {
            // PGB Staff: stats dari project assigned
            $assignedProjectIds = $user->assignedProjects()->pluck('projects.id');
            
            $stats = [
                'total' => Project::whereIn('id', $assignedProjectIds)->count(),
                'progress' => Project::whereIn('id', $assignedProjectIds)->where('status', 'Progress')->count(),
                'pending' => Project::whereIn('id', $assignedProjectIds)->where('status', 'Pending')->count(),
                'done' => Project::whereIn('id', $assignedProjectIds)->where('status', 'Done')->count(),
            ];
        }
        
        return view('projects.index', compact('projects', 'divisions', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     * 
     * ✅ UPDATED: Staff PKJ sekarang bisa buat project
     */
    public function create()
    {
        $user = auth()->user();
        $divisions = Division::all();
        
        // ✅ UPDATED: Gunakan helper function
        if (!$user->canCreateProject()) {
            abort(403, 'Unauthorized. You do not have permission to create projects.');
        }
        
        // Logic PIC berdasarkan role pembuat project
        if ($user->role === 'supervisi') {
            // Super Admin: Ambil SEMUA user (PGB + PKJ) + diri sendiri
            $users = User::where(function($q) {
                    $q->whereIn('role', ['kabag_pgb', 'karyawan', 'perizinan'])
                    ->whereNotNull('bagian')
                    ->where('bagian', '!=', '');
                })
                ->orWhere('role', 'supervisi')
                ->orderBy('bagian')
                ->orderBy('name')
                ->get();
            
        } elseif ($user->role === 'kabag_pgb') {
            // Kabag PGB: Semua user (PGB + PKJ) KECUALI Supervisi
            $users = User::where('role', '!=', 'supervisi')
                ->whereNotNull('bagian')
                ->where('bagian', '!=', '')
                ->orderBy('bagian')
                ->orderBy('name')
                ->get();
            
        } elseif ($user->isPKJ()) {
            // ✅ UPDATED: PKJ (Kabag atau Staff): Bisa pilih semua PKJ
            $users = User::where(function($q) {
                    // Kabag PKJ (role = perizinan)
                    $q->where('role', 'perizinan')
                    // ATAU Karyawan dengan bagian PKJ
                    ->orWhere(function($subQ) {
                        $subQ->where('role', 'karyawan')
                            ->where('bagian', 'PKJ');
                    });
                })
                ->orderBy('name')
                ->get();
                
        } else {
            abort(403, 'Unauthorized. You do not have permission to create projects.');
        }

        return view('projects.create', compact('divisions', 'users'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validation
        $validated = $request->validate([
            'tanggal_inisiasi' => 'required|date',
            'target_implementasi' => 'required|string|max:255',
            'nama_project' => 'required|string|max:255',
            'urgensi' => 'required|in:Low,Medium,High,Very High',
            'sifat_project' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'pemilik_project_id' => 'required|exists:divisions,id',
            'pic_proyek_ids' => 'required|array|min:1',
            'pic_proyek_ids.*' => 'exists:users,id',
            'status' => 'required|in:Progress,Pending,Done',
        ], [
            'urgensi.required' => __('projects.urgency_required'),
            'sifat_project.required' => __('projects.nature_required'),
            'pemilik_project_id.required' => __('projects.division_required'),
            'pic_proyek_ids.required' => 'PIC wajib dipilih minimal 1 orang',
            'pic_proyek_ids.min' => 'PIC wajib dipilih minimal 1 orang',
            'pic_proyek_ids.*.exists' => 'Salah satu PIC yang dipilih tidak valid',
            'status.required' => __('projects.status_required'),
        ]);

        // Validate PIC sesuai bagian
        $picUsers = User::whereIn('id', $validated['pic_proyek_ids'])->get();
        
        if ($user->role === 'kabag_pgb') {
            $invalidPics = $picUsers->where('bagian', '!=', 'PGB')
                                    ->where('id', '!=', $user->id);
            
            if ($invalidPics->count() > 0) {
                return back()
                    ->withInput()
                    ->withErrors(['pic_proyek_ids' => 'Kabag PGB hanya boleh assign PIC dari bagian PGB']);
            }
            
        } elseif ($user->role === 'perizinan') {
            $invalidPics = $picUsers->where('bagian', '!=', 'PKJ');
            
            if ($invalidPics->count() > 0) {
                return back()
                    ->withInput()
                    ->withErrors(['pic_proyek_ids' => 'PKJ hanya boleh assign PIC dari bagian PKJ']);
            }
        }
        
        // Set creator
        $validated['user_id'] = $user->id;
        
        // Backward compatibility
        $validated['pic_proyek_id'] = $validated['pic_proyek_ids'][0];
        
        // ✅ NEW: Auto-assign Kadiv sebagai Pengawas
        // HANYA jika project BUKAN dibuat oleh Kadiv sendiri
        if ($user->role !== 'supervisi') {
            // Cari Kadiv (user dengan role supervisi)
            $kadiv = User::where('role', 'supervisi')->first();
            
            if ($kadiv) {
                $validated['pengawas_id'] = $kadiv->id;
                
                \Log::info('✅ Auto-assigned Kadiv as Pengawas', [
                    'project_creator' => $user->name,
                    'project_creator_role' => $user->role,
                    'kadiv_assigned' => $kadiv->name,
                    'kadiv_id' => $kadiv->id
                ]);
            } else {
                \Log::warning('⚠️ No Kadiv found for auto-assignment', [
                    'project_creator' => $user->name
                ]);
            }
        } else {
            \Log::info('ℹ️ Project created by Kadiv - No auto-assignment', [
                'kadiv_creator' => $user->name
            ]);
        }
        
        // Remove pic_proyek_ids from validated
        $picIds = $validated['pic_proyek_ids'];
        unset($validated['pic_proyek_ids']);

        // Create project
        $project = Project::create($validated);

        // Sync PICs
        $project->pics()->sync($picIds);

        // ✅ Success message with Pengawas info
        $successMessage = 'Project berhasil ditambahkan dengan ' . count($picIds) . ' PIC!';
        
        if ($project->hasPengawas()) {
            $successMessage .= ' Pengawas: ' . $project->pengawas->name;
        }

        return redirect()->route('projects.index')
            ->with('success', $successMessage);
    }

    /**
     * Display the specified project with activities (paginated & filtered)
     * 
     * ✅ UPDATED: Enhanced toggle logic untuk Kadiv + PKJ (tanpa PGB)
     */
    public function show(Request $request, Project $project)
    {
        if ($request->hasHeader('Referer')) {
            $referer = $request->header('Referer');
            
            if (str_contains($referer, '/projects') && 
                !str_contains($referer, '/export-preview') && 
                !str_contains($referer, '/export-pdf')) {
                
                session(['projects_back_url' => $referer]);
            }
        }
        
        $user = auth()->user();
        
        // Load project relationships
        $project->load(['pemilikProject', 'pics', 'creator', 'pengawas']);
        
        $projectCreator = $project->creator;
        
        // ✅ NEW: Determine toggle type
        // 'SUPERVISI_PGB_PKJ' = Toggle 3 tab: Supervisi, PGB, PKJ (maroon, hijau, ungu)
        // 'PGB_PKJ' = Toggle antara PGB dan PKJ (hijau & ungu)
        // 'SUPERVISI_PKJ' = Toggle antara Supervisi dan PKJ (maroon & ungu)
        // 'SUPERVISI_PGB' = Toggle antara Supervisi dan PGB (maroon & hijau)
        // null = Tidak ada toggle
        $toggleType = null;
        $showToggle = false;
        
        if ($projectCreator && $projectCreator->role === 'supervisi') {
            // ========== PROJECT DIBUAT OLEH KADIV ==========
            $hasPGBPics = $project->pics->filter(fn($pic) => $pic->bagian === 'PGB')->count() > 0;
            $hasPKJPics = $project->pics->filter(fn($pic) => $pic->bagian === 'PKJ')->count() > 0;
            
            if ($hasPGBPics && $hasPKJPics) {
                // ✅ NEW: Kadiv + PGB + PKJ → Toggle 3 TAB: Supervisi/PGB/PKJ
                $showToggle = true;
                $toggleType = 'SUPERVISI_PGB_PKJ';
                
            } elseif ($hasPKJPics && !$hasPGBPics) {
                // Kadiv + PKJ only (tanpa PGB) → Toggle Supervisi/PKJ
                $showToggle = true;
                $toggleType = 'SUPERVISI_PKJ';
                
            } elseif ($hasPGBPics && !$hasPKJPics) {
                // Kadiv + PGB only → Toggle Supervisi/PGB
                $showToggle = true;
                $toggleType = 'SUPERVISI_PGB';
                
            } else {
                // Kadiv saja tanpa PIC lain → No toggle
                $showToggle = false;
                $toggleType = null;
            }
            
            \Log::info('📊 Toggle Logic - Kadiv Project', [
                'project_id' => $project->id,
                'creator' => $projectCreator->name,
                'has_pgb_pics' => $hasPGBPics,
                'has_pkj_pics' => $hasPKJPics,
                'toggle_type' => $toggleType,
                'show_toggle' => $showToggle
            ]);
            
        } elseif ($projectCreator && $projectCreator->role === 'perizinan') {
            // ========== PROJECT DIBUAT OLEH PKJ ==========
            $hasPGBPics = $project->pics->filter(fn($pic) => $pic->bagian === 'PGB')->count() > 0;
            
            if ($hasPGBPics) {
                // PKJ + PGB → Toggle PGB/PKJ
                $showToggle = true;
                $toggleType = 'PGB_PKJ';
            } else {
                // ✅ PKJ only (tanpa PGB) → No toggle
                $showToggle = false;
                $toggleType = null;
            }
            
            \Log::info('📊 Toggle Logic - PKJ Project', [
                'project_id' => $project->id,
                'creator' => $projectCreator->name,
                'has_pgb_pics' => $hasPGBPics,
                'toggle_type' => $toggleType,
                'show_toggle' => $showToggle
            ]);
            
        } else {
            // ========== PROJECT DIBUAT OLEH KABAG PGB ==========
            // Always show PGB/PKJ toggle
            $showToggle = true;
            $toggleType = 'PGB_PKJ';
        }
        
        // ✅ Determine default view bagian based on toggle type
        if ($showToggle) {
            if ($toggleType === 'SUPERVISI_PGB_PKJ') {
                // ✅ NEW: 3 tab toggle - default ke user's bagian atau SUPERVISI untuk Kadiv
                if ($user->role === 'supervisi') {
                    $defaultBagian = 'SUPERVISI';
                } elseif ($user->bagian === 'PGB') {
                    $defaultBagian = 'PGB';
                } elseif ($user->bagian === 'PKJ') {
                    $defaultBagian = 'PKJ';
                } else {
                    $defaultBagian = 'PGB';
                }
                $viewBagian = $request->get('view_bagian', $defaultBagian);
            } elseif ($toggleType === 'SUPERVISI_PKJ') {
                // Default ke PKJ untuk toggle Supervisi/PKJ
                $defaultBagian = ($user->role === 'supervisi') ? 'SUPERVISI' : 'PKJ';
                $viewBagian = $request->get('view_bagian', $defaultBagian);
            } elseif ($toggleType === 'SUPERVISI_PGB') {
                // Default ke PGB untuk toggle Supervisi/PGB
                $defaultBagian = ($user->role === 'supervisi') ? 'SUPERVISI' : 'PGB';
                $viewBagian = $request->get('view_bagian', $defaultBagian);
            } else {
                // Default PGB/PKJ toggle
                $viewBagian = $request->get('view_bagian', $user->bagian ?? 'PGB');
            }
        } else {
            $viewBagian = null;
        }
        
        // Base query for activities
        $activitiesQuery = $project->activities()->with('user');
        
        // ✅ UPDATED: Filter logic based on toggle type
        if ($showToggle) {
            if ($toggleType === 'SUPERVISI_PGB_PKJ') {
                // ✅ 3 tab toggle - STRICT filtering, no overlap
                if ($viewBagian === 'SUPERVISI') {
                    // HANYA aktivitas dari Kadiv pembuat project
                    $activitiesQuery->where('user_id', $project->user_id)
                        ->whereHas('user', function($q) {
                            $q->where('role', 'supervisi');
                        });
                } elseif ($viewBagian === 'PGB') {
                    // HANYA aktivitas dari user PGB (exclude Kadiv)
                    $activitiesQuery->whereHas('user', function($q) {
                        $q->where('bagian', 'PGB')
                        ->where('role', '!=', 'supervisi');
                    });
                } else {
                    // HANYA aktivitas dari user PKJ (exclude Kadiv)
                    $activitiesQuery->whereHas('user', function($q) {
                        $q->where('bagian', 'PKJ')
                        ->where('role', '!=', 'supervisi');
                    });
                }
                
            } elseif ($toggleType === 'SUPERVISI_PKJ') {
                // Toggle Supervisi/PKJ - STRICT filtering
                if ($viewBagian === 'SUPERVISI') {
                    // HANYA aktivitas dari Kadiv pembuat project
                    $activitiesQuery->where('user_id', $project->user_id)
                        ->whereHas('user', function($q) {
                            $q->where('role', 'supervisi');
                        });
                } else {
                    // HANYA aktivitas dari PKJ (exclude Kadiv)
                    $activitiesQuery->whereHas('user', function($q) {
                        $q->where('bagian', 'PKJ')
                        ->where('role', '!=', 'supervisi');
                    });
                }
                
            } elseif ($toggleType === 'SUPERVISI_PGB') {
                // Toggle Supervisi/PGB - STRICT filtering
                if ($viewBagian === 'SUPERVISI') {
                    // HANYA aktivitas dari Kadiv pembuat project
                    $activitiesQuery->where('user_id', $project->user_id)
                        ->whereHas('user', function($q) {
                            $q->where('role', 'supervisi');
                        });
                } else {
                    // HANYA aktivitas dari PGB (exclude Kadiv)
                    $activitiesQuery->whereHas('user', function($q) {
                        $q->where('bagian', 'PGB')
                        ->where('role', '!=', 'supervisi');
                    });
                }
                
            } else {
                // Toggle PGB/PKJ (original logic untuk non-Kadiv projects)
                if ($viewBagian === 'PGB') {
                    $activitiesQuery->where(function($q) use ($project) {
                        $q->whereHas('user', function($subQ) {
                            $subQ->where('bagian', 'PGB');
                        })
                        // Include aktivitas Kadiv jika dia pembuat project
                        ->orWhere(function($subQ) use ($project) {
                            $subQ->where('user_id', $project->user_id)
                                ->whereHas('user', function($userQ) {
                                    $userQ->where('role', 'supervisi');
                                });
                        });
                    });
                    
                } elseif ($viewBagian === 'PKJ') {
                    $activitiesQuery->where(function($q) use ($project) {
                        $q->whereHas('user', function($subQ) {
                            $subQ->where('bagian', 'PKJ');
                        })
                        // Include aktivitas Kadiv jika dia pembuat project
                        ->orWhere(function($subQ) use ($project) {
                            $subQ->where('user_id', $project->user_id)
                                ->whereHas('user', function($userQ) {
                                    $userQ->where('role', 'supervisi');
                                });
                        });
                    });
                }
            }
            
        } else {
            // TIDAK ADA TOGGLE: Tampilkan aktivitas sesuai pembuat
            if ($projectCreator && $projectCreator->role === 'supervisi') {
                // Kadiv project tanpa PIC lain → aktivitas Kadiv saja
                $activitiesQuery->where('user_id', $project->user_id);
            } elseif ($projectCreator && $projectCreator->role === 'perizinan') {
                // PKJ project tanpa PGB → aktivitas PKJ saja
                $activitiesQuery->whereHas('user', function($q) {
                    $q->where('bagian', 'PKJ');
                });
            } else {
                // Fallback
                $activitiesQuery->where('user_id', $project->user_id);
            }
        }
        
        // Sorting
        $activitiesQuery->orderByRaw("CASE WHEN status = 'Done' THEN 1 ELSE 0 END")
                        ->orderBy('tanggal_mulai', 'desc');
        
        // Paginate
        if ($showToggle) {
            $activities = $activitiesQuery->paginate(10)->appends(['view_bagian' => $viewBagian]);
        } else {
            $activities = $activitiesQuery->paginate(10);
        }
        
        // ✅ Pass toggleType to view
        return view('projects.show', compact('project', 'activities', 'viewBagian', 'showToggle', 'toggleType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $user = auth()->user();
        
        // ✅ AUTHORIZATION LOGIC
        $canEdit = false;
        
        if ($user->role === 'supervisi') {
            $canEdit = ($project->user_id === $user->id);
            
        } elseif ($user->role === 'kabag_pgb') {
            $canEdit = ($project->user_id === $user->id);
            
        } elseif ($user->bagian === 'PKJ') {
            $projectCreator = $project->creator;
            $canEdit = ($projectCreator && $projectCreator->bagian === 'PKJ');
        }
        
        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit project ini.');
        }

        $divisions = Division::all();
        
        // ✅ Load existing PICs dari pivot table
        $existingPicIds = $project->pics->pluck('id')->toArray();
        
        // Get available users (sama seperti create)
        $projectCreator = $project->creator;
        
        if ($projectCreator && $projectCreator->role === 'supervisi') {
            // ✅ FIXED: Super Admin bisa pilih dari PGB + PKJ + DIRI SENDIRI
            $users = User::where(function($q) use ($projectCreator) {
                    $q->whereIn('role', ['kabag_pgb', 'karyawan', 'perizinan'])
                    ->whereNotNull('bagian')
                    ->where('bagian', '!=', '')
                    // ✅ ATAU supervisi pembuat
                    ->orWhere('id', $projectCreator->id); // ← PINDAHKAN KE DALAM CLOSURE
                })
                ->orderBy('bagian')
                ->orderBy('name')
                ->get();
                
        } elseif ($projectCreator && $projectCreator->role === 'kabag_pgb') {
            // ✅ Kabag PGB: Semua user (PGB + PKJ) KECUALI Supervisi/Kadiv
            $users = User::where('role', '!=', 'supervisi') // Exclude Kadiv/Supervisi
                ->whereNotNull('bagian')
                ->where('bagian', '!=', '')
                ->orderBy('bagian')
                ->orderBy('name')
                ->get();
            
        } elseif ($projectCreator && $projectCreator->role === 'perizinan') {
            // PKJ: hanya PKJ
            $users = User::where('role', 'perizinan')
                ->where('bagian', 'PKJ')
                ->orderBy('name')
                ->get();
        } else {
            // Fallback
            $users = collect();
        }
        
        return view('projects.edit', compact('project', 'divisions', 'users', 'existingPicIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = auth()->user();
        
        // Authorization check
        $canEdit = false;
        
        if ($user->role === 'supervisi') {
            $canEdit = ($project->user_id === $user->id);
            
        } elseif ($user->role === 'kabag_pgb') {
            $canEdit = ($project->user_id === $user->id);
            
        } elseif ($user->bagian === 'PKJ') {
            $projectCreator = $project->creator;
            $canEdit = ($projectCreator && $projectCreator->bagian === 'PKJ');
        }
        
        if (!$canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate project ini.');
        }

        // Validation
        $validated = $request->validate([
            'tanggal_inisiasi' => 'required|date',
            'target_implementasi' => 'required|string|max:255',
            'nama_project' => 'required|string|max:255',
            'urgensi' => 'required|in:Low,Medium,High,Very High',
            'sifat_project' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'pemilik_project_id' => 'required|exists:divisions,id',
            'pic_proyek_ids' => 'required|array|min:1',
            'pic_proyek_ids.*' => 'exists:users,id',
            'status' => 'required|in:Progress,Pending,Done',
        ], [
            'pic_proyek_ids.required' => 'PIC wajib dipilih minimal 1 orang',
            'pic_proyek_ids.min' => 'PIC wajib dipilih minimal 1 orang',
        ]);

        // Validate PIC
        $picUsers = User::whereIn('id', $validated['pic_proyek_ids'])->get();
        $projectCreator = $project->creator;
        
        if ($projectCreator && $projectCreator->role === 'kabag_pgb') {
            $invalidPics = $picUsers->where('bagian', '!=', 'PGB')
                                    ->where('id', '!=', $projectCreator->id);
            
            if ($invalidPics->count() > 0) {
                return back()->withErrors(['pic_proyek_ids' => 'Kabag PGB hanya boleh assign PIC dari bagian PGB'])
                            ->withInput();
            }
            
        } elseif ($projectCreator && $projectCreator->role === 'perizinan') {
            $invalidPics = $picUsers->where('bagian', '!=', 'PKJ');
            
            if ($invalidPics->count() > 0) {
                return back()->withErrors(['pic_proyek_ids' => 'PKJ hanya boleh assign PIC dari bagian PKJ'])
                            ->withInput();
            }
        }

        // Backward compatibility
        $validated['pic_proyek_id'] = $validated['pic_proyek_ids'][0];

        // ✅ IMPORTANT: Preserve pengawas_id (don't overwrite)
        // Pengawas tetap sama saat update, KECUALI null (baru assign)
        if (!$project->hasPengawas() && $projectCreator && $projectCreator->role !== 'supervisi') {
            $kadiv = User::where('role', 'supervisi')->first();
            if ($kadiv) {
                $validated['pengawas_id'] = $kadiv->id;
            }
        }

        // Update project
        $project->update($validated);

        // Sync PICs
        $project->pics()->sync($validated['pic_proyek_ids']);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil diupdate dengan ' . count($validated['pic_proyek_ids']) . ' PIC!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $user = auth()->user();
        
        // ✅ AUTHORIZATION LOGIC (tetap sama)
        $canDelete = false;
        
        if ($user->role === 'supervisi') {
            $canDelete = ($project->user_id === $user->id);
        } elseif ($user->role === 'kabag_pgb') {
            $canDelete = ($project->user_id === $user->id);
        } elseif ($user->bagian === 'PKJ') {
            $projectCreator = $project->creator;
            $canDelete = ($projectCreator && $projectCreator->bagian === 'PKJ');
        }
        
        if (!$canDelete) {
            return redirect()->route('projects.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus project ini.');
        }

        try {
            $namaProject = $project->nama_project;
            
            // ✅ Cascade delete will automatically remove pivot table entries
            $project->delete();
            
            // ✅ SMART REDIRECT: Gunakan saved filter dari session
            $currentFilter = session('projects_current_filter', 'all');
            $referrer = session('projects_back_url');
            
            // Jika dari detail page (ada referrer dari show)
            if ($referrer && str_contains($referrer, 'filter=my')) {
                session()->forget('projects_back_url');
                return redirect()->route('projects.index', ['filter' => 'my'])
                    ->with('success', "Project '$namaProject' berhasil dihapus!");
            }
            
            if ($referrer && (str_contains($referrer, 'filter=all') || str_contains($referrer, '/projects'))) {
                session()->forget('projects_back_url');
                return redirect()->route('projects.index', ['filter' => 'all'])
                    ->with('success', "Project '$namaProject' berhasil dihapus!");
            }
            
            // ✅ DEFAULT: Gunakan filter yang tersimpan (untuk delete dari list)
            return redirect()->route('projects.index', ['filter' => $currentFilter])
                ->with('success', "Project '$namaProject' berhasil dihapus!");
                
        } catch (\Exception $e) {
            return redirect()->route('projects.index')
                ->with('error', 'Gagal menghapus project: ' . $e->getMessage());
        }
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
            $query = Project::with(['pemilikProject', 'pics', 'activities']);
        } 
        elseif ($user->role === 'perizinan') {
            // PKJ: hanya project yang:
            // 1. DIA yang buat (user_id = PKJ), ATAU
            // 2. Ada aktivitas dia di project tersebut
            $query = Project::with(['pemilikProject', 'pics', 'activities'])
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
                ->orWhereHas('pics', function($q) use ($search) {
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
            $query = Project::with(['pemilikProject', 'pics', 'activities']);
        } 
        elseif ($user->role === 'perizinan') {
            $query = Project::with(['pemilikProject', 'pics', 'activities'])
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
                ->orWhereHas('pics', function($q) use ($search) {
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
     * ✅ FIXED: Kabag PGB bisa export project yang melibatkan PGB
     */
    public function singleProjectExportPreview(Project $project)
    {
        $user = auth()->user();
        
        // ✅ FIXED: Authorization check
        $canExport = false;
        
        if ($user->role === 'supervisi') {
            // Supervisi bisa export semua project
            $canExport = true;
            
        } elseif ($user->role === 'kabag_pgb') {
            // ✅ FIXED: Kabag PGB bisa export jika:
            // 1. Project melibatkan PIC dari PGB, ATAU
            // 2. Ada aktivitas dari user PGB
            $hasPGBPics = $project->pics->filter(fn($pic) => $pic->bagian === 'PGB')->count() > 0;
            $hasPGBActivities = $project->activities()->whereHas('user', function($q) {
                $q->where('bagian', 'PGB');
            })->exists();
            
            $canExport = $hasPGBPics || $hasPGBActivities;
            
        } elseif ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            // PKJ (Kabag atau Staff): bisa export jika dia pembuat ATAU ada aktivitasnya
            $canExport = $project->user_id === $user->id 
                    || $project->activities()->where('user_id', $user->id)->exists();
                    
        } elseif ($user->role === 'karyawan' && $user->bagian === 'PGB') {
            // Staff PGB: bisa export jika dia PIC ATAU ada aktivitasnya
            $canExport = $user->isPicOf($project->id)
                    || $project->activities()->where('user_id', $user->id)->exists();
        }
        
        if (!$canExport) {
            abort(403, 'Unauthorized. You cannot export this project.');
        }
        
        // Load relationships
        $project->load([
            'pemilikProject', 
            'pics', 
            'creator',
            'pengawas',
            'activities' => function($query) {
                $query->with('user')
                    ->orderBy('tanggal_mulai', 'desc');
            }
        ]);
        
        // ✅ FIXED: Separate activities by bagian + Superadmin/Kadiv
        $activitiesSuperadmin = $project->activities->filter(function($activity) {
            return $activity->user && $activity->user->role === 'supervisi';
        });
        
        $activitiesPGB = $project->activities->filter(function($activity) {
            return $activity->user 
                && $activity->user->bagian === 'PGB'
                && $activity->user->role !== 'supervisi'; // Exclude Kadiv
        });
        
        $activitiesPKJ = $project->activities->filter(function($activity) {
            return $activity->user 
                && $activity->user->bagian === 'PKJ'
                && $activity->user->role !== 'supervisi'; // Exclude Kadiv
        });
        
        // Stats for this project
        $stats = [
            'total_activities' => $project->activities->count(),
            'superadmin_activities' => $activitiesSuperadmin->count(),
            'pgb_activities' => $activitiesPGB->count(),
            'pkj_activities' => $activitiesPKJ->count(),
            'progress' => $project->activities->where('status', 'Progress')->count(),
            'pending' => $project->activities->where('status', 'Pending')->count(),
            'done' => $project->activities->where('status', 'Done')->count(),
        ];
        
        return view('projects.single-export-preview', compact(
            'project', 
            'activitiesSuperadmin',
            'activitiesPGB', 
            'activitiesPKJ', 
            'stats'
        ));
    }

    /**
     * Export SINGLE project to PDF
     * ✅ FIXED: Kabag PGB bisa export project yang melibatkan PGB
     */
    public function singleProjectExportPdf(Project $project)
    {
        // 🔒 LOCK LOCALE KE BAHASA INDONESIA UNTUK PDF
        \App::setLocale('id');
        
        $user = auth()->user();
        
        // ✅ FIXED: Authorization check (sama seperti preview)
        $canExport = false;
        
        if ($user->role === 'supervisi') {
            $canExport = true;
            
        } elseif ($user->role === 'kabag_pgb') {
            // ✅ FIXED: Kabag PGB bisa export jika melibatkan PGB
            $hasPGBPics = $project->pics->filter(fn($pic) => $pic->bagian === 'PGB')->count() > 0;
            $hasPGBActivities = $project->activities()->whereHas('user', function($q) {
                $q->where('bagian', 'PGB');
            })->exists();
            
            $canExport = $hasPGBPics || $hasPGBActivities;
            
        } elseif ($user->role === 'perizinan' || ($user->role === 'karyawan' && $user->bagian === 'PKJ')) {
            $canExport = $project->user_id === $user->id 
                    || $project->activities()->where('user_id', $user->id)->exists();
                    
        } elseif ($user->role === 'karyawan' && $user->bagian === 'PGB') {
            $canExport = $user->isPicOf($project->id)
                    || $project->activities()->where('user_id', $user->id)->exists();
        }
        
        if (!$canExport) {
            abort(403, 'Unauthorized. You cannot export this project.');
        }
        
        // Load relationships
        $project->load([
            'pemilikProject', 
            'pics', 
            'creator',
            'pengawas',
            'activities' => function($query) {
                $query->with('user')
                    ->orderBy('tanggal_mulai', 'desc');
            }
        ]);
        
        // ✅ FIXED: Separate activities by bagian + Superadmin/Kadiv
        $activitiesSuperadmin = $project->activities->filter(function($activity) {
            return $activity->user && $activity->user->role === 'supervisi';
        });
        
        $activitiesPGB = $project->activities->filter(function($activity) {
            return $activity->user 
                && $activity->user->bagian === 'PGB'
                && $activity->user->role !== 'supervisi';
        });
        
        $activitiesPKJ = $project->activities->filter(function($activity) {
            return $activity->user 
                && $activity->user->bagian === 'PKJ'
                && $activity->user->role !== 'supervisi';
        });
        
        // Generate PDF
        $pdf = Pdf::loadView('pdf.single-project', compact(
            'project', 
            'activitiesSuperadmin',
            'activitiesPGB', 
            'activitiesPKJ'
        ));
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'project_' . \Str::slug($project->nama_project) . '_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}