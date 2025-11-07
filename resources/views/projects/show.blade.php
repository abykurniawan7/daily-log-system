<x-app-layout>
    <x-slot name="title">{{ __('projects.project_detail') }}</x-slot>
    
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('projects.project_detail') }}
            </h2>
            <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
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
                        
                        <div>
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.pic_project') }}</p>
                            @if(auth()->user()->role === 'supervisi')
                                <a href="{{ route('employees.show', $project->picProyek) }}" 
                                class="text-base text-white hover:text-yellow-300 hover:underline font-medium inline-flex items-center group">
                                    {{ $project->picProyek->name }}
                                    <svg class="w-4 h-4 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </a>
                            @else
                                <p class="text-base font-semibold text-white">{{ $project->picProyek->name }}</p>
                            @endif
                        </div>
                        
                        <div class="md:col-span-2">
                            <p class="text-xs font-medium text-green-100 mb-1">{{ __('projects.description') }}</p>
                            <p class="text-base text-white">{{ $project->deskripsi ?? '-' }}</p>
                        </div>
                    </div>
                    
                    {{-- Action Buttons - Edit/Hapus Project --}}
                    @php
                        $user = auth()->user();
                        $isProjectCreator = $project->user_id === $user->id;
                        $canEditProject = false;
                        
                        if ($user->role === 'supervisi' && $isProjectCreator) {
                            $canEditProject = true;
                        } elseif ($user->role === 'perizinan' && $isProjectCreator) {
                            $canEditProject = true;
                        }
                    @endphp
                    
                    @if($canEditProject)
                        <div class="mt-4 pt-4 border-t border-white/20 flex items-center gap-2">
                            <a href="{{ route('projects.edit', $project) }}" 
                            class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition shadow-sm hover:shadow-md"
                            title="{{ __('projects.edit') }} {{ __('projects.page_title') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('projects.confirm_delete_project') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center justify-center w-9 h-9 bg-red-500 hover:bg-red-600 text-white rounded-lg transition shadow-sm hover:shadow-md"
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
                        
                        // Decision: Tampilkan toggle HANYA jika project dibuat oleh Supervisi
                        $showToggle = ($projectCreator && $projectCreator->role === 'supervisi');
                    @endphp
                    
                    {{-- CONDITIONAL: Tab Toggle atau Simple Header --}}
                    @if($showToggle)
                        {{-- ===== PROJECT SUPERVISI: ADA TOGGLE PGB/PKJ ===== --}}
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                {{-- Tab Navigation (PKJ/PGB) --}}
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
                                    
                                    if ($currentUser->role === 'supervisi') {
                                        $canAddActivity = false;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivity = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $currentUser->id === $project->pic_proyek_id) {
                                        $canAddActivity = true;
                                    }
                                @endphp
                                
                                @if($canAddActivity)
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md
                                        {{ $viewBagian === 'PGB' 
                                            ? 'bg-[#0F5132] hover:bg-[#0A3D24] text-white' 
                                            : 'bg-purple-600 hover:bg-purple-700 text-white' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                            
                            {{-- Info Badge dengan warna sesuai bagian --}}
                            <div class="mt-4 flex items-center gap-2 text-sm">
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $viewBagian === 'PGB' ? 'bg-green-50' : 'bg-purple-50' }}">
                                    <span class="w-2 h-2 rounded-full {{ $viewBagian === 'PGB' ? 'bg-green-500' : 'bg-purple-500' }} animate-pulse"></span>
                                    <span class="font-medium {{ $viewBagian === 'PGB' ? 'text-green-700' : 'text-purple-700' }}">
                                        {{ $viewBagian === 'PGB' ? __('projects.pgb') : __('projects.pkj') }}
                                    </span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-600">{{ $activities->total() }} {{ __('projects.total_activities') }}</span>
                            </div>
                        </div>
                    @else
                        {{-- ===== PROJECT PKJ: TIDAK ADA TOGGLE ===== --}}
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
                                
                                {{-- Tombol Tambah Aktivitas untuk PKJ --}}
                                @if($currentUser->bagian === 'PKJ')
                                <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md bg-purple-600 hover:bg-purple-700 text-white">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('projects.add_activity') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    {{-- ===== TABLE/CARDS CONTENT ===== --}}
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
                                        $rowColor = $showToggle 
                                            ? ($viewBagian === 'PGB' ? 'green' : 'purple')
                                            : 'purple';
                                        $barColor = $showToggle 
                                            ? ($viewBagian === 'PGB' ? 'bg-green-500' : 'bg-purple-500')
                                            : 'bg-purple-500';
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
                                                <div class="flex flex-col items-center gap-1.5">
                                                    {{-- File Upload --}}
                                                    @if($activity->lampiran)
                                                        <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                                        target="_blank"
                                                        download
                                                        class="text-blue-600 hover:text-blue-800 text-xs flex items-center gap-1 max-w-[200px]"
                                                        title="{{ basename($activity->lampiran) }}">
                                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                            </svg>
                                                            <span class="truncate">{{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}</span>
                                                        </a>
                                                    @endif
                                                    
                                                    {{-- URL Link --}}
                                                    @if($activity->lampiran_link)
                                                        <a href="{{ $activity->lampiran_link }}" 
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="text-purple-600 hover:text-purple-800 text-xs flex items-center gap-1 max-w-[200px]"
                                                        title="{{ $activity->lampiran_link }}">
                                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                            </svg>
                                                            <span class="truncate">{{ $activity->lampiran_link }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                            @php
                                                $canEditActivity = false;
                                                
                                                if ($currentUser->role === 'supervisi') {
                                                    $canEditActivity = false;
                                                } elseif ($currentUser->bagian === 'PKJ' && $currentUser->id === $activity->user_id) {
                                                    $canEditActivity = true;
                                                } elseif ($currentUser->bagian === 'PGB' && $currentUser->id === $activity->user_id && $currentUser->id === $project->pic_proyek_id) {
                                                    $canEditActivity = true;
                                                }
                                            @endphp
                                            
                                            @if($canEditActivity)
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('activities.edit', $activity) }}" 
                                                    class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50 transition"
                                                    title="{{ __('projects.edit') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                </a>
                                                
                                                <form action="{{ route('activities.destroy', $activity) }}" 
                                                    method="POST" 
                                                    class="inline"
                                                    onsubmit="return confirm('{{ __('projects.confirm_delete_activity') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition"
                                                            title="{{ __('projects.delete') }}">
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
                                $borderColor = $showToggle 
                                    ? ($viewBagian === 'PGB' ? 'border-green-500' : 'border-purple-500')
                                    : 'border-purple-500';
                            @endphp
                            <div class="border-l-4 {{ $borderColor }} border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer bg-white"
                                onclick="openActivityModal('{{ $activity->uuid }}')">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900 flex-1">{{ $activity->nama_aktivitas }}</h4>
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
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$activity->status] }}">
                                        {{ $statusLabels[$activity->status] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $activity->deskripsi ? Str::limit($activity->deskripsi, 60) : '-' }}</p>
                                <div class="text-xs text-gray-500 mb-3">
                                    {{ $activity->tanggal_mulai->format('d M Y') }}
                                    @if($activity->tanggal_selesai)
                                    - {{ $activity->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </div>
                                
                                @php
                                    $canEditActivity = false;
                                    
                                    if ($currentUser->role === 'supervisi') {
                                        $canEditActivity = false;
                                    } elseif ($currentUser->bagian === 'PKJ' && $currentUser->id === $activity->user_id) {
                                        $canEditActivity = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $currentUser->id === $activity->user_id && $currentUser->id === $project->pic_proyek_id) {
                                        $canEditActivity = true;
                                    }
                                @endphp
                                
                                @if($canEditActivity)
                                <div class="flex gap-2" onclick="event.stopPropagation()">
                                    <a href="{{ route('activities.edit', $activity->id) }}" 
                                    class="flex-1 text-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-lg transition">
                                        {{ __('projects.edit') }}
                                    </a>
                                    <form action="{{ route('activities.destroy', $activity->id) }}" 
                                        method="POST" 
                                        class="flex-1"
                                        onsubmit="return confirm('{{ __('projects.confirm_delete_activity') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition">
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
                        <div class="mt-6">
                            {{ $activities->links() }}
                        </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-12">
                            @php
                                $emptyIconBg = $showToggle 
                                    ? ($viewBagian === 'PGB' ? 'bg-green-100' : 'bg-purple-100')
                                    : 'bg-purple-100';
                                $emptyIconColor = $showToggle 
                                    ? ($viewBagian === 'PGB' ? 'text-green-600' : 'text-purple-600')
                                    : 'text-purple-600';
                            @endphp
                            <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4 {{ $emptyIconBg }}">
                                <svg class="w-8 h-8 {{ $emptyIconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-lg font-medium text-gray-900 mb-1">
                                @if($showToggle)
                                    {{ $viewBagian === 'PGB' ? __('projects.no_activities_pgb') : __('projects.no_activities_pkj') }}
                                @else
                                    {{ __('projects.no_activities_pkj') }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 mb-6">
                                @if($showToggle)
                                    @if($currentUser->bagian === $viewBagian)
                                        {{ __('projects.start_first_activity') }}
                                    @elseif($currentUser->bagian === 'PGB' || $currentUser->bagian === 'PKJ')
                                        {{ __('projects.toggle_to') }} {{ $viewBagian === 'PGB' ? 'PKJ' : 'PGB' }} {{ __('projects.to_see_other') }}
                                    @else
                                        {{ __('projects.no_activities_added') }}
                                    @endif
                                @else
                                    @if($currentUser->bagian === 'PKJ')
                                        {{ __('projects.start_first_activity') }}
                                    @else
                                        {{ __('projects.no_activities_added') }}
                                    @endif
                                @endif
                            </p>
                            
                            @php
                                $canAddActivityEmpty = false;
                                
                                if ($showToggle) {
                                    if ($currentUser->role === 'supervisi') {
                                        $canAddActivityEmpty = false;
                                    } elseif ($currentUser->bagian === 'PKJ' && $viewBagian === 'PKJ') {
                                        $canAddActivityEmpty = true;
                                    } elseif ($currentUser->bagian === 'PGB' && $viewBagian === 'PGB' && $currentUser->id === $project->pic_proyek_id) {
                                        $canAddActivityEmpty = true;
                                    }
                                } else {
                                    // Project PKJ - hanya PKJ yang bisa tambah
                                    if ($currentUser->bagian === 'PKJ') {
                                        $canAddActivityEmpty = true;
                                    }
                                }
                            @endphp
                            
                            @if($canAddActivityEmpty)
                            @php
                                $emptyBtnClass = $showToggle 
                                    ? ($viewBagian === 'PGB' 
                                        ? 'bg-[#0F5132] hover:bg-[#0A3D24] text-white' 
                                        : 'bg-purple-600 hover:bg-purple-700 text-white')
                                    : 'bg-purple-600 hover:bg-purple-700 text-white';
                            @endphp
                            <a href="{{ route('activities.create', ['project_id' => $project->id]) }}" 
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md {{ $emptyBtnClass }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('projects.add_first_activity') }}
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Export Project Button (Supervisi, PKJ, dan PGB) --}}
            @php
                $canExport = false;
                
                if ($currentUser->role === 'supervisi') {
                    $canExport = true;
                } elseif ($currentUser->role === 'perizinan') {
                    $canExport = $project->user_id === $currentUser->id 
                            || $project->activities()->where('user_id', $currentUser->id)->exists();
                } elseif ($currentUser->role === 'karyawan' && $currentUser->bagian === 'PGB') {
                    $canExport = $currentUser->id === $project->pic_proyek_id 
                            || $project->activities()->where('user_id', $currentUser->id)->exists();
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
                    class="ml-4 inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, var(--primary-green, #0F5132) 0%, var(--primary-green-light, #1B6B47) 100%);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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