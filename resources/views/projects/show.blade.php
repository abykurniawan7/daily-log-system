<x-app-layout>
    <x-slot name="title">{{ __('projects.project_detail') }}</x-slot>
    
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('projects.project_detail') }}
            </h2>
            
            {{-- ✅ FIXED: Smart Back Button with Proper Referrer Handling --}}
            @php
                // ✅ SIMPLE FIX: Ambil filter dari session untuk kembali ke halaman yang benar
                $filter = session('projects_filter', 'all');
                $backUrl = route('projects.index', ['filter' => $filter]);
            @endphp

            <a href="{{ $backUrl }}" 
            class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('projects.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Info Project --}}
            <div class="bg-gradient-to-r from-[#0F5132] to-[#1B6B47] overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 pb-3 border-b border-white/20 text-white">{{ __('projects.project_information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.project_name_label') }}</p>
                            <p class="text-base font-semibold text-white">{{ $project->nama_project }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.status') }}</p>
                            @php
                                $statusColors = [
                                    'Progress' => 'bg-blue-500 text-white',
                                    'Pending' => 'bg-yellow-500 text-white',
                                    'Done' => 'bg-green-500 text-white'
                                ];
                                
                                $statusLabels = [
                                    'Progress' => __('projects.in_progress'),
                                    'Pending' => __('projects.pending_status'),
                                    'Done' => __('projects.completed')
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] }}">
                                {{ $statusLabels[$project->status] }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.initiation_date') }}</p>
                            <p class="text-base text-white">{{ $project->tanggal_inisiasi->format('d F Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.target_implementation') }}</p>
                            <p class="text-base text-white">{{ $project->target_implementasi }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.urgency') }}</p>
                            @php
                                $urgencyColors = [
                                    'Low' => 'bg-green-500 text-white',
                                    'Medium' => 'bg-yellow-500 text-white',
                                    'High' => 'bg-orange-500 text-white',
                                    'Very High' => 'bg-red-500 text-white'
                                ];
                                
                                $urgencyLabels = [
                                    'Low' => __('projects.low'),
                                    'Medium' => __('projects.medium'),
                                    'High' => __('projects.high'),
                                    'Very High' => __('projects.very_high')
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $urgencyColors[$project->urgensi] }}">
                                {{ $urgencyLabels[$project->urgensi] }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.project_nature') }}</p>
                            <p class="text-base text-white">{{ $project->sifat_project }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.project_owner') }}</p>
                            <p class="text-base text-white">{{ $project->pemilikProject->nama_divisi }}</p>
                        </div>
                        
                        {{-- ✅ FASE 3 - REVISI 1 & 5: Display Pengawas (Kadiv) ONLY --}}
                        @if($project->hasPengawas())
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">
                                {{ __('projects.person_in_charge') }}
                            </p>
                            
                            @php
                                $pengawas = $project->pengawas;
                            @endphp
                            
                            @if(auth()->user()->role === 'supervisi')
                                {{-- Supervisi: Pengawas sebagai link ke profile --}}
                                <a href="{{ route('employees.show', $pengawas) }}" 
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold text-white hover:text-yellow-300 hover:bg-white/10 transition bg-red-900/80">
                                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($pengawas->name, 0, 1)) }}
                                    </span>
                                    <span>{{ $pengawas->name }}</span>
                                </a>
                            @else
                                {{-- Non-Supervisi: Pengawas sebagai badge biasa --}}
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold text-white bg-red-900/80">
                                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($pengawas->name, 0, 1)) }}
                                    </span>
                                    <span>{{ $pengawas->name }}</span>
                                    @if($pengawas->id === auth()->id())
                                        <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded">{{ __('common.me') }}</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                        @endif

                        {{-- ✅ FIXED: Display Project PIC (Sorted by Hierarchy) --}}
                        <div class="{{ $project->hasPengawas() ? '' : 'md:col-span-2' }}">
                            <p class="text-xs font-medium text-green-100 mb-1">
                                {{ __('projects.pic_project') }}
                                @if($project->pics->count() > 1)
                                    <span class="ml-1">({{ $project->pics->count() }} {{ __('common.people') }})</span>
                                @endif
                            </p>
                            
                            @php
                                // ✅ FIXED: Sort PICs by Hierarchy (Highest to Lowest)
                                $sortedPics = $project->pics->sortByDesc(function($pic) {
                                    // Hierarki dari tertinggi ke terendah
                                    if ($pic->role === 'supervisi') {
                                        return 5; // Superadmin/Kadiv - Tertinggi
                                    } elseif ($pic->role === 'kabag_pgb') {
                                        return 4; // Kabag PGB
                                    } elseif ($pic->role === 'perizinan') {
                                        return 3; // Kabag PKJ
                                    } elseif ($pic->role === 'karyawan' && $pic->bagian === 'PKJ') {
                                        return 2; // Staff PKJ
                                    } elseif ($pic->role === 'karyawan' && $pic->bagian === 'PGB') {
                                        return 1; // Staff PGB - Terendah
                                    }
                                    return 0; // Default (jika ada role lain)
                                });
                            @endphp
                            
                            @if($sortedPics->count() > 0)
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($sortedPics as $pic)
                                        @php
                                            // Badge color berdasarkan role & bagian
                                            if ($pic->role === 'supervisi') {
                                                $badgeColor = 'bg-red-900/80'; // Maroon
                                            } elseif ($pic->bagian === 'PGB') {
                                                $badgeColor = 'bg-green-600/80'; // Hijau
                                            } elseif ($pic->bagian === 'PKJ') {
                                                $badgeColor = 'bg-purple-600/80'; // Ungu
                                            } else {
                                                $badgeColor = 'bg-gray-600/80';
                                            }
                                        @endphp
                                                                
                                        @if(auth()->user()->role === 'supervisi')
                                            {{-- Supervisi: PIC sebagai link ke profile --}}
                                            <a href="{{ route('employees.show', $pic) }}" 
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium text-white hover:text-yellow-300 hover:bg-white/10 transition {{ $badgeColor }}">
                                                <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">
                                                    {{ strtoupper(substr($pic->name, 0, 1)) }}
                                                </span>
                                                <span>{{ $pic->name }}</span>
                                                
                                                {{-- Badge "Saya" ONLY --}}
                                                @if($pic->id === auth()->id())
                                                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded">
                                                        {{ __('common.me') }}
                                                    </span>
                                                @endif
                                            </a>
                                        @else
                                            {{-- Non-Supervisi: PIC sebagai badge biasa --}}
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium text-white {{ $badgeColor }}">
                                                <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">
                                                    {{ strtoupper(substr($pic->name, 0, 1)) }}
                                                </span>
                                                <span>{{ $pic->name }}</span>
                                                
                                                {{-- Badge "Saya" ONLY --}}
                                                @if($pic->id === auth()->id())
                                                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded">
                                                        {{ __('common.me') }}
                                                    </span>
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-base text-white">-</p>
                            @endif
                        </div>

                        {{-- Info jika Kadiv adalah creator --}}
                        @if($project->creator && $project->creator->role === 'supervisi' && !$project->hasPengawas())
                            <div class="md:col-span-2">
                                <p class="text-xs text-green-100 italic">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __('projects.created_by_kadiv_info') }}
                                </p>
                            </div>
                        @endif
                        
                        <div class="md:col-span-2">
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.description') }}</p>
                            <p class="text-base text-white">{{ $project->deskripsi ?? '-' }}</p>
                        </div>
                    </div>
                    
                    {{-- ✅ REVISED: Action Buttons - Authorization untuk Role Baru --}}
                    @php
                        $user = auth()->user();
                        $isProjectCreator = $project->user_id === $user->id;
                        $canEditProject = false;
                        
                        // Authorization logic untuk role baru
                        if ($user->role === 'supervisi' && $isProjectCreator) {
                            $canEditProject = true;
                        } elseif ($user->role === 'kabag_pgb' && $isProjectCreator) {
                            $canEditProject = true; // NEW: Kabag PGB bisa edit project yang dia buat
                        } elseif ($user->role === 'perizinan' && $isProjectCreator) {
                            $canEditProject = true;
                        }
                    @endphp
                    
                    @if($canEditProject)
                        <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-2">
                            <a href="{{ route('projects.edit', $project) }}" 
                            class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 border border-transparent rounded-lg text-white hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md"
                            title="{{ __('projects.edit') }} {{ __('projects.page_title') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('projects.confirm_delete_project') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center justify-center w-9 h-9 bg-red-600 border border-transparent rounded-lg text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md"
                                        title="{{ __('projects.delete') }} {{ __('projects.page_title') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Daftar Aktivitas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('projects.activity_list') }}
                    </h3>
                    
                    @php
                        $currentUser = auth()->user();
                        $projectCreator = $project->creator;
                    @endphp
                    
                    {{-- ========== CONDITIONAL TOGGLE RENDERING ========== --}}
                    @if($showToggle && $toggleType === 'SUPERVISI_PGB_PKJ')
                        {{-- ===== TOGGLE 3 TAB: SUPERVISI / PGB / PKJ ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                {{-- Tab Navigation (3 tabs) --}}
                                <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50 w-full sm:w-auto">
                                    {{-- Tab Supervisi --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'SUPERVISI']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'SUPERVISI' 
                                                ? 'bg-[#800000] text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span>Supervisi</span>
                                        @if($viewBagian === 'SUPERVISI')
                                        <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                    
                                    {{-- Tab PGB --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'PGB']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'PGB' 
                                                ? 'bg-[#0F5132] text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <span>PGB</span>
                                        @if($viewBagian === 'PGB')
                                        <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                    
                                    {{-- Tab PKJ --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'PKJ']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'PKJ' 
                                                ? 'bg-purple-600 text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>PKJ</span>
                                        @if($viewBagian === 'PKJ')
                                        <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                </div>
                                
                                {{-- Tombol Tambah Aktivitas --}}
                                @php
                                    $canAddActivity = false;
                                    $isPicOfProject = $project->pics->contains('id', $currentUser->id);
                                    
                                    if ($currentUser->role === 'supervisi' && $project->user_id === $currentUser->id && $viewBagian === 'SUPERVISI') {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivity = true;
                                    }
                                @endphp
                                
                                @if($canAddActivity)
                                @php
                                    if ($viewBagian === 'SUPERVISI') {
                                        $addBtnColor = 'bg-[#800000] hover:bg-[#600000] text-white';
                                    } elseif ($viewBagian === 'PGB') {
                                        $addBtnColor = 'bg-[#0F5132] hover:bg-[#0A3D24] text-white';
                                    } else {
                                        $addBtnColor = 'bg-purple-600 hover:bg-purple-700 text-white';
                                    }
                                @endphp
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md {{ $addBtnColor }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_first_activity') }}
                                </a>
                                @endif
                            </div>
                            
                            {{-- Info Badge --}}
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                @php
                                    if ($viewBagian === 'SUPERVISI') {
                                        $badgeBg = 'bg-red-50';
                                        $dotColor = 'bg-[#800000]';
                                        $textColor = 'text-red-900';
                                        $labelText = 'Supervisi';
                                    } elseif ($viewBagian === 'PGB') {
                                        $badgeBg = 'bg-green-50';
                                        $dotColor = 'bg-green-500';
                                        $textColor = 'text-green-700';
                                        $labelText = __('projects.pgb');
                                    } else {
                                        $badgeBg = 'bg-purple-50';
                                        $dotColor = 'bg-purple-500';
                                        $textColor = 'text-purple-700';
                                        $labelText = __('projects.pkj');
                                    }
                                @endphp
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $badgeBg }}">
                                    <span class="w-2 h-2 rounded-full {{ $dotColor }} animate-pulse"></span>
                                    <span class="font-medium {{ $textColor }}">{{ $labelText }}</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                            </div>
                        </div>
                        
                    @elseif($showToggle && $toggleType === 'SUPERVISI_PKJ')
                        {{-- ===== TOGGLE SUPERVISI / PKJ (Maroon & Ungu) ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                {{-- Tab Navigation (Supervisi/PKJ) --}}
                                <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50 w-full sm:w-auto">
                                    {{-- Tab Supervisi --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'SUPERVISI']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'SUPERVISI' 
                                                ? 'bg-[#800000] text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span>Supervisi</span>
                                        @if($viewBagian === 'SUPERVISI')
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-white/20 rounded-full">
                                            {{ $activities->total() }}
                                        </span>
                                        @endif
                                    </a>
                                    
                                    {{-- Tab PKJ --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'PKJ']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'PKJ' 
                                                ? 'bg-purple-600 text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>PKJ</span>
                                        @if($viewBagian === 'PKJ')
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-white/20 rounded-full">
                                            {{ $activities->total() }}
                                        </span>
                                        @endif
                                    </a>
                                </div>
                                
                                {{-- Tombol Tambah Aktivitas --}}
                                @php
                                    $canAddActivity = false;
                                    
                                    if ($currentUser->role === 'supervisi' && $project->user_id === $currentUser->id) {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivity = true;
                                    }
                                @endphp
                                
                                @if($canAddActivity)
                                @php
                                    $addBtnColor = ($viewBagian === 'SUPERVISI') 
                                        ? 'bg-[#800000] hover:bg-[#600000] text-white'
                                        : 'bg-purple-600 hover:bg-purple-700 text-white';
                                @endphp
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md {{ $addBtnColor }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                            
                            {{-- Info Badge --}}
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                @php
                                    if ($viewBagian === 'SUPERVISI') {
                                        $badgeBg = 'bg-red-50';
                                        $dotColor = 'bg-[#800000]';
                                        $textColor = 'text-red-900';
                                        $labelText = 'Supervisi';
                                    } else {
                                        $badgeBg = 'bg-purple-50';
                                        $dotColor = 'bg-purple-500';
                                        $textColor = 'text-purple-700';
                                        $labelText = __('projects.pkj');
                                    }
                                @endphp
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $badgeBg }}">
                                    <span class="w-2 h-2 rounded-full {{ $dotColor }} animate-pulse"></span>
                                    <span class="font-medium {{ $textColor }}">{{ $labelText }}</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                            </div>
                        </div>
                        
                    @elseif($showToggle && $toggleType === 'SUPERVISI_PGB')
                        {{-- ===== TOGGLE SUPERVISI / PGB (Maroon & Hijau) ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50 w-full sm:w-auto">
                                    {{-- Tab Supervisi --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'SUPERVISI']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'SUPERVISI' 
                                                ? 'bg-[#800000] text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span>Supervisi</span>
                                        @if($viewBagian === 'SUPERVISI')
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                    
                                    {{-- Tab PGB --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'PGB']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'PGB' 
                                                ? 'bg-[#0F5132] text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <span>PGB</span>
                                        @if($viewBagian === 'PGB')
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                </div>
                                
                                {{-- Tombol Tambah Aktivitas --}}
                                @php
                                    $canAddActivity = false;
                                    $isPicOfProject = $project->pics->contains('id', $currentUser->id);
                                    
                                    if ($currentUser->role === 'supervisi' && $project->user_id === $currentUser->id) {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivity = true;
                                    }
                                @endphp
                                
                                @if($canAddActivity)
                                @php
                                    $addBtnColor = ($viewBagian === 'SUPERVISI') 
                                        ? 'bg-[#800000] hover:bg-[#600000] text-white'
                                        : 'bg-[#0F5132] hover:bg-[#0A3D24] text-white';
                                @endphp
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md {{ $addBtnColor }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                            
                            {{-- Info Badge --}}
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                @php
                                    if ($viewBagian === 'SUPERVISI') {
                                        $badgeBg = 'bg-red-50';
                                        $dotColor = 'bg-[#800000]';
                                        $textColor = 'text-red-900';
                                        $labelText = 'Supervisi';
                                    } else {
                                        $badgeBg = 'bg-green-50';
                                        $dotColor = 'bg-green-500';
                                        $textColor = 'text-green-700';
                                        $labelText = __('projects.pgb');
                                    }
                                @endphp
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $badgeBg }}">
                                    <span class="w-2 h-2 rounded-full {{ $dotColor }} animate-pulse"></span>
                                    <span class="font-medium {{ $textColor }}">{{ $labelText }}</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                            </div>
                        </div>
                        
                    @elseif($showToggle && $toggleType === 'PGB_PKJ')
                        {{-- ===== TOGGLE PGB / PKJ (Hijau & Ungu) - ORIGINAL ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50 w-full sm:w-auto">
                                    {{-- Tab PGB --}}
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'PGB']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'PGB' 
                                                ? 'bg-[#0F5132] text-white shadow-sm' 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <span>PGB</span>
                                        @if($viewBagian === 'PGB')
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                    
                                    {{-- Tab PKJ --}}
                                    @php
                                        $pkjTabColor = ($projectCreator && $projectCreator->role === 'supervisi') 
                                            ? 'bg-red-700 text-white shadow-sm' 
                                            : 'bg-purple-600 text-white shadow-sm';
                                    @endphp
                                    <a href="{{ route('projects.show', ['project' => $project->uuid, 'view_bagian' => 'PKJ']) }}" 
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold rounded-md transition-all duration-200
                                            {{ $viewBagian === 'PKJ' 
                                                ? $pkjTabColor 
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>PKJ</span>
                                        @if($viewBagian === 'PKJ')
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-white/20 rounded-full">{{ $activities->total() }}</span>
                                        @endif
                                    </a>
                                </div>
                                
                                {{-- Tombol Tambah Aktivitas --}}
                                @php
                                    $canAddActivity = false;
                                    $isPicOfProject = $project->pics->contains('id', $currentUser->id);
                                    
                                    if ($currentUser->role === 'supervisi') {
                                        $canAddActivity = ($project->user_id === $currentUser->id);
                                    } elseif ($currentUser->role === 'kabag_pgb' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->role === 'perizinan' && $viewBagian === 'PKJ') {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivity = true;
                                    }
                                @endphp
                                
                                @if($canAddActivity)
                                @php
                                    $addBtnColor = ($viewBagian === 'PGB') 
                                        ? 'bg-[#0F5132] hover:bg-[#0A3D24] text-white'
                                        : (($projectCreator && $projectCreator->role === 'supervisi')
                                            ? 'bg-red-700 hover:bg-red-800 text-white'
                                            : 'bg-purple-600 hover:bg-purple-700 text-white');
                                @endphp
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md {{ $addBtnColor }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                            
                            {{-- Info Badge --}}
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                @php
                                    if ($viewBagian === 'PGB') {
                                        $badgeBg = 'bg-green-50';
                                        $dotColor = 'bg-green-500';
                                        $textColor = 'text-green-700';
                                    } else {
                                        if ($projectCreator && $projectCreator->role === 'supervisi') {
                                            $badgeBg = 'bg-red-50';
                                            $dotColor = 'bg-red-700';
                                            $textColor = 'text-red-700';
                                        } else {
                                            $badgeBg = 'bg-purple-50';
                                            $dotColor = 'bg-purple-500';
                                            $textColor = 'text-purple-700';
                                        }
                                    }
                                @endphp
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $badgeBg }}">
                                    <span class="w-2 h-2 rounded-full {{ $dotColor }} animate-pulse"></span>
                                    <span class="font-medium {{ $textColor }}">
                                        {{ $viewBagian === 'PGB' ? __('projects.pgb') : __('projects.pkj') }}
                                    </span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                            </div>
                        </div>
                        
                    @elseif(!$showToggle && $projectCreator && $projectCreator->role === 'supervisi')
                        {{-- ===== PROJECT KADIV TANPA PIC LAIN: NO TOGGLE ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50">
                                        <span class="w-2 h-2 rounded-full bg-[#800000] animate-pulse"></span>
                                        <span class="font-medium text-red-900">Aktivitas Supervisi</span>
                                    </div>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                                </div>
                                
                                @if($currentUser->role === 'supervisi' && $project->user_id === $currentUser->id)
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center px-4 py-2 bg-[#800000] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#600000] focus:bg-[#600000] active:bg-[#500000] focus:outline-none focus:ring-2 focus:ring-[#800000] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                        </div>
                        
                    @else
                        {{-- ===== PROJECT PKJ TANPA TOGGLE ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-purple-50">
                                        <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                        <span class="font-medium text-purple-700">{{ __('projects.pkj') }}</span>
                                    </div>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                                </div>
                                
                                @php
                                    $canAddActivityPKJ = false;
                                    if ($currentUser->role === 'supervisi') {
                                        $canAddActivityPKJ = ($project->user_id === $currentUser->id);
                                    } elseif ($currentUser->bagian === 'PKJ') {
                                        $canAddActivityPKJ = true;
                                    }
                                @endphp

                                @if($canAddActivityPKJ)
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($activities->count() > 0)
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">{{ __('projects.no') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.activity_name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.date') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.description') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ __('projects.status') }}</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">{{ __('projects.attachment') }}</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">{{ __('projects.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activities as $index => $activity)
                                @php
                                    // ✅ UPDATED: Row/Bar color based on toggleType and viewBagian
                                    $isKadivActivity = ($activity->user && $activity->user->role === 'supervisi');
                                    
                                    if ($isKadivActivity) {
                                        // Aktivitas Kadiv: MAROON
                                        $rowColor = 'red';
                                        $barColor = 'bg-[#800000]';
                                    } elseif ($showToggle) {
                                        if ($toggleType === 'SUPERVISI_PGB_PKJ') {
                                            // 3 tab toggle
                                            if ($viewBagian === 'SUPERVISI') {
                                                $rowColor = 'red';
                                                $barColor = 'bg-[#800000]';
                                            } elseif ($viewBagian === 'PGB') {
                                                $rowColor = 'green';
                                                $barColor = 'bg-green-500';
                                            } else {
                                                $rowColor = 'purple';
                                                $barColor = 'bg-purple-500';
                                            }
                                        } elseif ($toggleType === 'SUPERVISI_PKJ') {
                                            $rowColor = ($viewBagian === 'SUPERVISI') ? 'red' : 'purple';
                                            $barColor = ($viewBagian === 'SUPERVISI') ? 'bg-[#800000]' : 'bg-purple-500';
                                        } elseif ($toggleType === 'SUPERVISI_PGB') {
                                            $rowColor = ($viewBagian === 'SUPERVISI') ? 'red' : 'green';
                                            $barColor = ($viewBagian === 'SUPERVISI') ? 'bg-[#800000]' : 'bg-green-500';
                                        } else {
                                            // PGB_PKJ
                                            $rowColor = ($viewBagian === 'PGB') ? 'green' : 'purple';
                                            $barColor = ($viewBagian === 'PGB') ? 'bg-green-500' : 'bg-purple-500';
                                        }
                                    } else {
                                        // No toggle
                                        if ($projectCreator && $projectCreator->role === 'supervisi') {
                                            $rowColor = 'red';
                                            $barColor = 'bg-[#800000]';
                                        } else {
                                            $rowColor = 'purple';
                                            $barColor = 'bg-purple-500';
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-{{ $rowColor }}-50 transition-colors cursor-pointer" 
                                    onclick="openActivityModal('{{ $activity->uuid }}')">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $activities->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-8 rounded-full {{ $barColor }}"></div>
                                            <span class="text-sm font-medium text-gray-900">{{ $activity->nama_aktivitas }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <div>{{ $activity->tanggal_mulai->format('d M Y') }}</div>
                                        @if($activity->tanggal_selesai)
                                        <div class="text-xs text-gray-400">{{ __('activities.until') }} {{ $activity->tanggal_selesai->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $activity->deskripsi ? Str::limit($activity->deskripsi, 50) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'Progress' => 'bg-blue-100 text-blue-800',
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'Done' => 'bg-green-100 text-green-800'
                                            ];
                                            $statusLabels = [
                                                'Progress' => __('projects.in_progress'),
                                                'Pending' => __('projects.pending_status'),
                                                'Done' => __('projects.completed')
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusColors[$activity->status] }}">
                                            {{ $statusLabels[$activity->status] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                        @if($activity->lampiran || $activity->lampiran_link)
                                            <div class="flex items-center justify-center gap-2">
                                                @if($activity->lampiran)
                                                    {{-- Preview Icon Button --}}
                                                    <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                                    target="_blank"
                                                    title="Lihat File: {{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                    
                                                    {{-- Download Icon Button --}}
                                                    <a href="{{ route('activities.download-attachment', $activity) }}" 
                                                    title="Download: {{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}"
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                
                                                @if($activity->lampiran_link)
                                                    {{-- External Link Icon Button --}}
                                                    <a href="{{ $activity->lampiran_link }}" 
                                                    target="_blank" 
                                                    rel="noopener noreferrer"
                                                    title="Buka Link: {{ $activity->lampiran_link }}"
                                                    class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                        @php
                                            $canEditActivity = ($currentUser->id === $activity->user_id && $currentUser->role !== 'supervisi');
                                        @endphp
                                        
                                        @if($canEditActivity)
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('activities.edit', $activity) }}" 
                                            class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline"
                                                onsubmit="return confirm('{{ __('projects.confirm_delete_activity') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center p-1.5 bg-red-600 border border-transparent rounded-md text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-4">
                        @foreach($activities as $index => $activity)
                        @php
                            $isKadivActivity = ($activity->user && $activity->user->role === 'supervisi');
                            
                            if ($isKadivActivity) {
                                $borderColor = 'border-[#800000]';
                            } elseif ($showToggle) {
                                if ($toggleType === 'SUPERVISI_PGB_PKJ') {
                                    if ($viewBagian === 'SUPERVISI') {
                                        $borderColor = 'border-[#800000]';
                                    } elseif ($viewBagian === 'PGB') {
                                        $borderColor = 'border-green-500';
                                    } else {
                                        $borderColor = 'border-purple-500';
                                    }
                                } elseif ($toggleType === 'SUPERVISI_PKJ') {
                                    $borderColor = ($viewBagian === 'SUPERVISI') ? 'border-[#800000]' : 'border-purple-500';
                                } elseif ($toggleType === 'SUPERVISI_PGB') {
                                    $borderColor = ($viewBagian === 'SUPERVISI') ? 'border-[#800000]' : 'border-green-500';
                                } else {
                                    $borderColor = ($viewBagian === 'PGB') ? 'border-green-500' : 'border-purple-500';
                                }
                            } else {
                                $borderColor = ($projectCreator && $projectCreator->role === 'supervisi') ? 'border-[#800000]' : 'border-purple-500';
                            }
                        @endphp
                        <div class="border-l-4 {{ $borderColor }} border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer bg-white"
                            onclick="openActivityModal('{{ $activity->uuid }}')">
                            
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-900 flex-1">{{ $activity->nama_aktivitas }}</h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$activity->status] ?? 'bg-gray-100' }}">
                                    {{ $statusLabels[$activity->status] ?? $activity->status }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-2">
                                {{ $activity->deskripsi ? Str::limit($activity->deskripsi, 60) : '-' }}
                            </p>
                            
                            <div class="text-xs text-gray-500 mb-3">
                                {{ $activity->tanggal_mulai->format('d M Y') }}
                                @if($activity->tanggal_selesai) - {{ $activity->tanggal_selesai->format('d M Y') }} @endif
                            </div>
                            
                            {{-- ✅ TAMBAHKAN BAGIAN LAMPIRAN DI SINI --}}
                            @if($activity->lampiran || $activity->lampiran_link)
                            <div class="mb-3 pb-3 border-b border-gray-200" onclick="event.stopPropagation()">
                                <p class="text-xs font-medium text-gray-500 mb-2">📎 Lampiran:</p>
                                
                                @if($activity->lampiran)
                                    <div class="flex flex-col gap-1.5">
                                        {{-- Preview --}}
                                        <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Lihat File</span>
                                        </a>
                                        
                                        {{-- Download --}}
                                        <a href="{{ route('activities.download-attachment', $activity) }}" 
                                        class="inline-flex items-center gap-1.5 text-xs text-green-600 hover:text-green-800 font-semibold hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Download</span>
                                        </a>
                                        
                                        {{-- Filename --}}
                                        <span class="text-xs text-gray-400 truncate">
                                            {{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}
                                        </span>
                                    </div>
                                @endif
                                
                                @if($activity->lampiran_link)
                                    <a href="{{ $activity->lampiran_link }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 text-xs text-purple-600 hover:text-purple-800 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        <span class="truncate">{{ Str::limit($activity->lampiran_link, 30) }}</span>
                                    </a>
                                @endif
                            </div>
                            @endif
                            
                            {{-- Action buttons (Edit/Delete) --}}
                            @php $canEditActivity = ($currentUser->id === $activity->user_id && $currentUser->role !== 'supervisi'); @endphp
                            
                            @if($canEditActivity)
                            <div class="flex gap-2" onclick="event.stopPropagation()">
                                <a href="{{ route('activities.edit', $activity) }}" 
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('projects.edit') }}
                                </a>
                                <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="flex-1" 
                                    onsubmit="return confirm('{{ __('projects.confirm_delete_activity') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('projects.delete') }}
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    @if($activities->hasPages())
                    <div class="mt-6">{{ $activities->links() }}</div>
                    @endif
                @else
                    {{-- ========== EMPTY STATE - UPDATED ========== --}}
                    <div class="text-center py-12">
                        @php
                            // Determine empty state colors based on toggle type
                            if ($showToggle) {
                                if ($toggleType === 'SUPERVISI_PGB_PKJ') {
                                    if ($viewBagian === 'SUPERVISI') {
                                        $emptyIconBg = 'bg-red-100';
                                        $emptyIconColor = 'text-red-700';
                                    } elseif ($viewBagian === 'PGB') {
                                        $emptyIconBg = 'bg-green-100';
                                        $emptyIconColor = 'text-green-600';
                                    } else {
                                        $emptyIconBg = 'bg-purple-100';
                                        $emptyIconColor = 'text-purple-600';
                                    }
                                } elseif ($toggleType === 'SUPERVISI_PKJ') {
                                    if ($viewBagian === 'SUPERVISI') {
                                        $emptyIconBg = 'bg-red-100';
                                        $emptyIconColor = 'text-red-700';
                                    } else {
                                        $emptyIconBg = 'bg-purple-100';
                                        $emptyIconColor = 'text-purple-600';
                                    }
                                } elseif ($toggleType === 'SUPERVISI_PGB') {
                                    if ($viewBagian === 'SUPERVISI') {
                                        $emptyIconBg = 'bg-red-100';
                                        $emptyIconColor = 'text-red-700';
                                    } else {
                                        $emptyIconBg = 'bg-green-100';
                                        $emptyIconColor = 'text-green-600';
                                    }
                                } else {
                                    // PGB_PKJ
                                    if ($viewBagian === 'PGB') {
                                        $emptyIconBg = 'bg-green-100';
                                        $emptyIconColor = 'text-green-600';
                                    } else {
                                        $emptyIconBg = 'bg-purple-100';
                                        $emptyIconColor = 'text-purple-600';
                                    }
                                }
                            } else {
                                if ($projectCreator && $projectCreator->role === 'supervisi') {
                                    $emptyIconBg = 'bg-red-100';
                                    $emptyIconColor = 'text-red-700';
                                } else {
                                    $emptyIconBg = 'bg-purple-100';
                                    $emptyIconColor = 'text-purple-600';
                                }
                            }
                        @endphp
                        <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4 {{ $emptyIconBg }}">
                            <svg class="w-8 h-8 {{ $emptyIconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-gray-900 mb-1">
                            @if($showToggle)
                                @if($toggleType === 'SUPERVISI_PKJ')
                                    {{ $viewBagian === 'SUPERVISI' ? 'Belum ada aktivitas Supervisi' : __('projects.no_activities_pkj') }}
                                @elseif($toggleType === 'SUPERVISI_PGB')
                                    {{ $viewBagian === 'SUPERVISI' ? 'Belum ada aktivitas Supervisi' : __('projects.no_activities_pgb') }}
                                @else
                                    {{ $viewBagian === 'PGB' ? __('projects.no_activities_pgb') : __('projects.no_activities_pkj') }}
                                @endif
                            @else
                                {{ ($projectCreator && $projectCreator->role === 'supervisi') ? 'Belum ada aktivitas Supervisi' : __('projects.no_activities_pkj') }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 mb-6">{{ __('projects.start_first_activity') }}</p>
                        
                        {{-- Add Activity Button in Empty State --}}
                        @php
                            $canAddActivityEmpty = false;
                            $isPicOfProject = $project->pics->contains('id', $currentUser->id);
                            
                            if ($showToggle) {
                                if ($toggleType === 'SUPERVISI_PKJ') {
                                    if ($currentUser->role === 'supervisi' && $project->user_id === $currentUser->id) {
                                        $canAddActivityEmpty = true;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivityEmpty = true;
                                    }
                                } elseif ($toggleType === 'SUPERVISI_PGB') {
                                    if ($currentUser->role === 'supervisi' && $project->user_id === $currentUser->id) {
                                        $canAddActivityEmpty = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivityEmpty = true;
                                    }
                                } else {
                                    // PGB_PKJ - original logic
                                    if ($currentUser->role === 'supervisi') {
                                        $canAddActivityEmpty = ($project->user_id === $currentUser->id);
                                    } elseif ($currentUser->role === 'kabag_pgb' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivityEmpty = true;
                                    } elseif ($currentUser->role === 'perizinan' && $viewBagian === 'PKJ') {
                                        $canAddActivityEmpty = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $isPicOfProject) {
                                        $canAddActivityEmpty = true;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivityEmpty = true;
                                    }
                                }
                            } else {
                                if ($currentUser->role === 'supervisi') {
                                    $canAddActivityEmpty = ($project->user_id === $currentUser->id);
                                } elseif ($currentUser->bagian === 'PKJ') {
                                    $canAddActivityEmpty = true;
                                }
                            }
                        @endphp
                        
                        @if($canAddActivityEmpty)
                        @php
                            // Button color based on context
                            if ($showToggle) {
                                if ($toggleType === 'SUPERVISI_PGB_PKJ') {
                                    // ✅ NEW: 3 tab toggle
                                    if ($viewBagian === 'SUPERVISI') {
                                        $emptyBtnClass = 'bg-[#800000] hover:bg-[#600000]'; // Maroon
                                    } elseif ($viewBagian === 'PGB') {
                                        $emptyBtnClass = 'bg-[#0F5132] hover:bg-[#0A3D24]'; // Hijau
                                    } else {
                                        $emptyBtnClass = 'bg-purple-600 hover:bg-purple-700'; // Ungu
                                    }
                                } elseif ($toggleType === 'SUPERVISI_PKJ') {
                                    $emptyBtnClass = ($viewBagian === 'SUPERVISI') ? 'bg-[#800000] hover:bg-[#600000]' : 'bg-purple-600 hover:bg-purple-700';
                                } elseif ($toggleType === 'SUPERVISI_PGB') {
                                    $emptyBtnClass = ($viewBagian === 'SUPERVISI') ? 'bg-[#800000] hover:bg-[#600000]' : 'bg-[#0F5132] hover:bg-[#0A3D24]';
                                } else {
                                    // PGB_PKJ
                                    $emptyBtnClass = ($viewBagian === 'PGB') ? 'bg-[#0F5132] hover:bg-[#0A3D24]' : 'bg-purple-600 hover:bg-purple-700';
                                }
                            } else {
                                $emptyBtnClass = ($projectCreator && $projectCreator->role === 'supervisi') ? 'bg-[#800000] hover:bg-[#600000]' : 'bg-purple-600 hover:bg-purple-700';
                            }
                        @endphp
                        <a href="{{ route('activities.create', ['project_id' => $project->id]) }}"
                        class="inline-flex items-center justify-center p-1.5 bg-yellow-500 border border-transparent rounded-md text-white hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                @endif
                </div>
            </div>

            {{-- ✅ FIXED: Export Project Button - Authorization untuk Role Baru --}}
            @php
                $canExport = false;
                
                if ($currentUser->role === 'supervisi') {
                    // Supervisi: bisa export semua project
                    $canExport = true;
                    
                } elseif ($currentUser->role === 'kabag_pgb') {
                    // ✅ FIXED: Kabag PGB bisa export jika project melibatkan PGB
                    $hasPGBPics = $project->pics->filter(fn($pic) => $pic->bagian === 'PGB')->count() > 0;
                    $hasPGBActivities = $project->activities->filter(function($activity) {
                        return $activity->user && $activity->user->bagian === 'PGB';
                    })->count() > 0;
                    
                    $canExport = $hasPGBPics || $hasPGBActivities;
                    
                } elseif ($currentUser->role === 'perizinan' || ($currentUser->role === 'karyawan' && $currentUser->bagian === 'PKJ')) {
                    // PKJ (Kabag atau Staff): bisa export jika dia pembuat ATAU ada aktivitasnya
                    $canExport = $project->user_id === $currentUser->id 
                            || $project->activities->where('user_id', $currentUser->id)->count() > 0;
                            
                } elseif ($currentUser->role === 'karyawan' && $currentUser->bagian === 'PGB') {
                    // Staff PGB: bisa export jika dia PIC ATAU ada aktivitasnya
                    $isPicOfProject = $project->pics->contains('id', $currentUser->id);
                    $canExport = $isPicOfProject 
                            || $project->activities->where('user_id', $currentUser->id)->count() > 0;
                }
            @endphp

            @if($canExport)
            <div class="mt-6 bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            {{ __('projects.export_project') }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ __('projects.export_project_description') }}
                        </p>
                    </div>
                    
                    <a href="{{ route('projects.single-export-preview', $project) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] focus:bg-[#0A3D24] active:bg-[#083D22] focus:outline-none focus:ring-2 focus:ring-[#0F5132] focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('projects.preview_export') }}
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
    
    {{-- Include Activity Modal Component --}}
    <x-activity-modal />
</x-app-layout>