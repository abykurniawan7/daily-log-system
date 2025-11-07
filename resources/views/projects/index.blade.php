<x-app-layout>
    <x-slot name="title">{{ __('projects.page_title') }}</x-slot>
    
    {{-- Alert untuk pesan success/error --}}
    @if (session('success'))
        <div class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">{{ __('projects.success') }}</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">{{ __('projects.error') }}</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('projects.project_list') }}
            </h2>
            
            {{-- Tambah Project Button - SUPERVISI & PERIZINAN --}}
            @if(auth()->user()->role === 'supervisi' || auth()->user()->role === 'perizinan')
                <a href="{{ route('projects.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('projects.add_project') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Quick Stats - Responsive Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-2 md:p-3">
                                <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 md:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('projects.total_projects') }}</dt>
                                    <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2 md:p-3">
                                <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 md:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('projects.progress') }}</dt>
                                    <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['progress'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-2 md:p-3">
                                <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 md:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('projects.pending') }}</dt>
                                    <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-4 md:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-500 rounded-lg p-3">
                                <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 md:ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('projects.done') }}</dt>
                                    <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['done'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Section - Collapsible --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="{ filterOpen: {{ request()->hasAny(['search', 'status', 'urgensi', 'division_id']) ? 'true' : 'false' }} }">
                <div class="p-4 md:p-6">
                    {{-- Toggle Button --}}
                    <div class="flex justify-between items-center">
                        <h3 class="text-base md:text-lg font-semibold">{{ __('projects.filter_search') }}</h3>
                        <button @click="filterOpen = !filterOpen" 
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span x-text="filterOpen ? '{{ __('projects.hide_filter') }}' : '{{ __('projects.show_filter') }}'"></span>
                            <svg class="w-4 h-4 ml-2 transition-transform" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Filter Badge (Active Filters Info) --}}
                    @if(request()->hasAny(['search', 'status', 'urgensi', 'division_id']))
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="text-xs font-medium text-gray-600">{{ __('projects.active_filters') }}</span>
                            @if(request('search'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ __('projects.search_label') }}: "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ __('projects.status_label') }}: {{ request('status') }}
                                </span>
                            @endif
                            @if(request('urgensi'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ __('projects.urgency_label') }}: {{ request('urgensi') }}
                                </span>
                            @endif
                            @if(request('division_id'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ __('projects.division_label') }}: {{ $divisions->firstWhere('id', request('division_id'))->nama_divisi ?? 'Unknown' }}
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    {{-- Filter Form (Collapsible) --}}
                    <div x-show="filterOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mt-4">
                        <form method="GET" action="{{ route('projects.index') }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 md:gap-4 items-end">
                                <!-- Search -->
                                <div class="sm:col-span-2 lg:col-span-2">
                                    <label for="search" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('projects.search_project') }}</label>
                                    <input type="text" name="search" id="search" 
                                        value="{{ request('search') }}"
                                        placeholder="{{ __('projects.search_placeholder') }}"
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                
                                <!-- Filter by Status -->
                                <div>
                                    <label for="status" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('projects.status_label') }}</label>
                                    <select name="status" id="status" 
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('projects.all_status') }}</option>
                                        <option value="Progress" {{ request('status') == 'Progress' ? 'selected' : '' }}>{{ __('projects.progress') }}</option>
                                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('projects.pending') }}</option>
                                        <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>{{ __('projects.done') }}</option>
                                    </select>
                                </div>
                                
                                <!-- Filter by Urgensi -->
                                <div>
                                    <label for="urgensi" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('projects.urgency_label') }}</label>
                                    <select name="urgensi" id="urgensi" 
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('projects.all_urgency') }}</option>
                                        <option value="Low" {{ request('urgensi') == 'Low' ? 'selected' : '' }}>{{ __('projects.low') }}</option>
                                        <option value="Medium" {{ request('urgensi') == 'Medium' ? 'selected' : '' }}>{{ __('projects.medium') }}</option>
                                        <option value="High" {{ request('urgensi') == 'High' ? 'selected' : '' }}>{{ __('projects.high') }}</option>
                                        <option value="Very High" {{ request('urgensi') == 'Very High' ? 'selected' : '' }}>{{ __('projects.very_high') }}</option>
                                    </select>
                                </div>
                                
                                <!-- Filter by Divisi -->
                                <div>
                                    <label for="division_id" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('projects.division_label') }}</label>
                                    <select name="division_id" id="division_id" 
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('projects.all_divisions') }}</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                {{ $division->nama_divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Action Buttons (Icon Only) -->
                                <div class="flex gap-2">
                                    <button type="submit" 
                                            class="p-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition shadow-sm"
                                            title="{{ __('projects.apply_filter') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('projects.index') }}" 
                                       class="p-2.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition shadow-sm"
                                       title="{{ __('projects.reset_filter') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Projects Table/Cards --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 md:p-6 text-gray-900">
                    @if($projects->count() > 0)
                        {{-- Info & Sorting - Responsive --}}
                        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="text-xs md:text-sm text-gray-600">
                                {{ __('projects.showing') }} {{ $projects->count() }} {{ __('projects.of') }} {{ $projects->total() }} {{ __('projects.projects_text') }}
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-xs md:text-sm">
                                <span class="text-gray-600">{{ __('projects.sort_by') }}</span>
                                <a href="{{ route('projects.index', array_merge(request()->query(), ['sort_by' => 'nama_project', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                    class="text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                    {{ __('projects.name') }} {{ request('sort_by') == 'nama_project' ? (request('sort_order') == 'asc' ? '↑' : '↓') : '' }}
                                </a>
                                <span class="text-gray-400">|</span>
                                <a href="{{ route('projects.index', array_merge(request()->query(), ['sort_by' => 'tanggal_inisiasi', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                    class="text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                    {{ __('projects.initiation_date') }} {{ request('sort_by') == 'tanggal_inisiasi' ? (request('sort_order') == 'asc' ? '↑' : '↓') : '' }}
                                </a>
                                <span class="text-gray-400">|</span>
                                <a href="{{ route('projects.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                    class="text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                    {{ __('projects.status') }} {{ request('sort_by') == 'status' ? (request('sort_order') == 'asc' ? '↑' : '↓') : '' }}
                                </a>
                            </div>
                        </div>

                        {{-- Desktop Table --}}
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.no') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.project_name') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.owner') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.pic') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.urgency') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.target') }}</th>
                                        
                                        {{-- Kolom Aksi - Semua kecuali Karyawan --}}
                                        @if(auth()->user()->role !== 'karyawan')
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('projects.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($projects as $index => $project)
                                        <tr class="hover:bg-[#D1FAE5] transition-colors duration-150 cursor-pointer group" 
                                            onclick="window.location='{{ route('projects.show', $project) }}'">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $projects->firstItem() + $index }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900 group-hover:text-[#0F5132] transition-colors">{{ $project->nama_project }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $project->pemilikProject->nama_divisi }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $project->picProyek->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $urgencyColors = [
                                                        'Low' => 'bg-green-100 text-green-800',
                                                        'Medium' => 'bg-yellow-100 text-yellow-800',
                                                        'High' => 'bg-orange-100 text-orange-800',
                                                        'Very High' => 'bg-red-100 text-red-800'
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $urgencyColors[$project->urgensi] }}">
                                                    {{ $project->urgensi }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'Progress' => 'bg-blue-100 text-blue-800',
                                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                                        'Done' => 'bg-green-100 text-green-800'
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] }}">
                                                    {{ $project->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $project->target_implementasi }}
                                            </td>
                                            
                                            {{-- Kolom Aksi --}}
                                           @if(auth()->user()->role !== 'karyawan')
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium" onclick="event.stopPropagation()">
                                                    <div class="flex items-center justify-center gap-2">
                                                        @php
                                                            $user = auth()->user();
                                                            $canEditProject = false;
                                                            
                                                            if ($user->role === 'supervisi' && $project->user_id === $user->id) {
                                                                $canEditProject = true;
                                                            }
                                                            elseif ($user->role === 'perizinan' && $project->user_id === $user->id) {
                                                                $canEditProject = true;
                                                            }
                                                        @endphp

                                                        @if($canEditProject)
                                                            {{-- Edit Button --}}
                                                            <a href="{{ route('projects.edit', $project) }}" 
                                                            class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50 transition"
                                                            title="{{ __('projects.edit') }}">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            
                                                            {{-- Delete Button --}}
                                                            <form action="{{ route('projects.destroy', $project) }}" 
                                                                method="POST" 
                                                                class="inline" 
                                                                onsubmit="return confirm('{{ __('projects.confirm_delete_project') }}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition"
                                                                        title="{{ __('projects.delete') }}">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="text-gray-400 text-xs">-</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile/Tablet Cards --}}
                        <div class="lg:hidden space-y-4">
                            @foreach($projects as $project)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-[#0F5132] hover:bg-[#D1FAE5] hover:shadow-md transition-all duration-150 cursor-pointer"
                                     onclick="window.location='{{ route('projects.show', $project) }}'">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-semibold text-gray-900 text-sm flex-1 hover:text-[#0F5132] transition-colors">
                                            {{ $project->nama_project }}
                                        </h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full ml-2 whitespace-nowrap
                                            @if($project->status === 'Progress') bg-blue-100 text-blue-800
                                            @elseif($project->status === 'Pending') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 
                                            @endif">
                                            {{ $project->status }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-xs md:text-sm text-gray-600 mb-3">
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ __('projects.owner') }}:</span>
                                            <span class="text-right">{{ $project->pemilikProject->nama_divisi }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ __('projects.pic') }}:</span>
                                            <span class="text-right">{{ $project->picProyek->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ __('projects.target') }}:</span>
                                            <span class="text-right">{{ $project->target_implementasi }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium">{{ __('projects.urgency') }}:</span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($project->urgensi === 'Low') bg-green-100 text-green-800
                                                @elseif($project->urgensi === 'Medium') bg-yellow-100 text-yellow-800
                                                @elseif($project->urgensi === 'High') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $project->urgensi }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Action Buttons untuk Mobile --}}
                                    @if(auth()->user()->role !== 'karyawan')
                                        @php
                                            $user = auth()->user();
                                            $canEditProject = false;
                                            
                                            if ($user->role === 'supervisi' && $project->user_id === $user->id) {
                                                $canEditProject = true;
                                            }
                                            elseif ($user->role === 'perizinan' && $project->user_id === $user->id) {
                                                $canEditProject = true;
                                            }
                                        @endphp

                                        @if($canEditProject)
                                            <div class="flex gap-2 pt-3 border-t border-gray-200" onclick="event.stopPropagation()">
                                                <a href="{{ route('projects.edit', $project) }}" 
                                                class="flex-1 text-center px-3 py-2 bg-yellow-500 text-white text-xs font-semibold rounded hover:bg-yellow-600 transition flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    {{ __('projects.edit') }}
                                                </a>
                                                
                                                <form action="{{ route('projects.destroy', $project) }}" 
                                                    method="POST" 
                                                    class="flex-1"
                                                    onsubmit="return confirm('{{ __('projects.confirm_delete_project') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-full px-3 py-2 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        {{ __('projects.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $projects->appends(request()->query())->links() }}
                        </div>
                        

                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-4 text-base md:text-lg font-medium text-gray-900">
                                @if(request()->hasAny(['search', 'status', 'urgensi', 'division_id']))
                                    {{ __('projects.no_projects_found') }}
                                @else
                                    {{ __('projects.no_projects_yet') }}
                                @endif
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'status', 'urgensi', 'division_id']))
                                    {{ __('projects.try_different_filter') }}
                                @else
                                    @if(auth()->user()->role === 'supervisi' || auth()->user()->role === 'perizinan')
                                        {{ __('projects.start_first_project') }}
                                    @else
                                        {{ __('projects.not_assigned_pic') }}
                                    @endif
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'status', 'urgensi', 'division_id']) && (auth()->user()->role === 'supervisi' || auth()->user()->role === 'perizinan'))
                                <div class="mt-6">
                                    <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                        + {{ __('projects.add_project') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>