<x-app-layout>
    <x-slot name="title">{{ __('activities.my_activities') }}</x-slot>
    
    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">{{ __('activities.success') }}</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('activities.my_activities') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('activities.all_activities_you_work_on') }}
                </p>
            </div>
            <a href="{{ route('activities.create') }}" 
               class="btn-green inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('activities.add_activity') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Filter Section - Collapsible --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="{ filterOpen: {{ request()->hasAny(['search', 'project_uuid', 'status', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
                <div class="p-4 md:p-6">
                    {{-- Toggle Button --}}
                    <div class="flex justify-between items-center">
                        <h3 class="text-base md:text-lg font-semibold">{{ __('activities.filter_search') }}</h3>
                        <button @click="filterOpen = !filterOpen" 
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span x-text="filterOpen ? '{{ __('activities.hide_filter') }}' : '{{ __('activities.show_filter') }}'"></span>
                            <svg class="w-4 h-4 ml-2 transition-transform" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Filter Badge (Active Filters Info) --}}
                    @if(request()->hasAny(['search', 'project_uuid', 'status', 'date_from', 'date_to']))
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="text-xs font-medium text-gray-600">{{ __('activities.active_filters') }}</span>
                            @if(request('search'))
                                <span class="badge-green">
                                    {{ __('activities.search_label') }} "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('project_uuid') && isset($userProjects))
                                <span class="badge-green">
                                    {{ __('activities.project_label') }} {{ $userProjects->firstWhere('uuid', request('project_uuid'))->nama_project ?? 'Unknown' }}
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="badge-green">
                                    {{ __('activities.status_label') }} {{ request('status') }}
                                </span>
                            @endif
                            @if(request('date_from'))
                                <span class="badge-green">
                                    {{ __('activities.from_date') }}: {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
                                </span>
                            @endif
                            @if(request('date_to'))
                                <span class="badge-green">
                                    {{ __('activities.until_date') }}: {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
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
                        <form method="GET" action="{{ route('activities.my-activities') }}" onsubmit="removeEmptyFields(this)">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 md:gap-4 items-end">
                                <!-- Search -->
                                <div class="sm:col-span-2">
                                    <label for="search" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('activities.search_activity') }}</label>
                                    <input type="text" name="search" id="search" 
                                        value="{{ request('search') }}"
                                        placeholder="{{ __('activities.search_placeholder_myact') }}"
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[var(--primary-green)] focus:ring-[var(--primary-green)]">
                                </div>
                                
                                <!-- Filter by Project -->
                                @if(isset($userProjects))
                                    <div>
                                        <label for="project_uuid" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('activities.project') }}</label>
                                        <select name="project_uuid" id="project_uuid" 
                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[var(--primary-green)] focus:ring-[var(--primary-green)]">
                                            <option value="">{{ __('activities.all_projects') }}</option>
                                            @foreach($userProjects as $project)
                                                <option value="{{ $project->uuid }}" {{ request('project_uuid') == $project->uuid ? 'selected' : '' }}>
                                                    {{ $project->nama_project }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                
                                <!-- Filter by Status -->
                                <div>
                                    <label for="status" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('activities.status') }}</label>
                                    <select name="status" id="status" 
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('activities.all_status') }}</option>
                                        <option value="Progress" {{ request('status') == 'Progress' ? 'selected' : '' }}>{{ __('activities.progress') }}</option>
                                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('activities.pending') }}</option>
                                        <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>{{ __('activities.done') }}</option>
                                    </select>
                                </div>
                                
                                <!-- Date From -->
                                <div>
                                    <label for="date_from" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('activities.from_date_label') }}</label>
                                    <input type="date" name="date_from" id="date_from" 
                                        value="{{ request('date_from') }}"
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                
                                <!-- Date To -->
                                <div>
                                    <label for="date_to" class="block text-xs md:text-sm font-medium text-gray-700 mb-1">{{ __('activities.to_date_label') }}</label>
                                    <input type="date" name="date_to" id="date_to" 
                                        value="{{ request('date_to') }}"
                                        class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <button type="submit" 
                                            class="btn-green p-2.5 shadow-sm"
                                            title="{{ __('activities.apply_filter') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('activities.my-activities') }}" 
                                       class="p-2.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition shadow-sm"
                                       title="{{ __('activities.reset_filter') }}">
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
            
            {{-- Activities List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 md:p-6 text-gray-900">
                    @if(isset($activities) && $activities->count() > 0)
                        <div class="mb-4 text-xs md:text-sm text-gray-600">
                            {{ __('activities.showing_results', [
                                'first' => $activities->firstItem(),
                                'last' => $activities->lastItem(),
                                'total' => $activities->total()
                            ]) }}
                        </div>
                        
                        {{-- Table Desktop --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">{{ __('activities.no') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('activities.activity_name') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('activities.project') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('activities.date') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('activities.description') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ __('activities.status') }}</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">{{ __('activities.attachment') }}</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">{{ __('activities.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activities as $index => $activity)
                                    <tr class="hover-green transition-colors cursor-pointer"
                                        onclick="window.location='{{ route('activities.show', $activity) }}'">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $activities->firstItem() + $index }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $activity->nama_aktivitas }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div>{{ $activity->project->nama_project }}</div>
                                            <div class="text-xs text-gray-400">{{ $activity->project->pemilikProject->nama_divisi ?? '-' }}</div>
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
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusColors[$activity->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ __('activities.' . strtolower($activity->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                            @if($activity->lampiran)
                                                <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                                   target="_blank"
                                                   download
                                                   class="text-blue-600 hover:text-blue-800 text-xs flex items-center justify-center gap-1"
                                                   title="{{ basename($activity->lampiran) }}">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    <span class="truncate max-w-[100px]">{{ Str::limit(basename(str_replace('lampiran/', '', $activity->lampiran)), 15) }}</span>
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('activities.edit', $activity) }}" 
                                                class="btn-icon-yellow"
                                                title="{{ __('activities.edit') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                
                                                <form action="{{ route('activities.destroy', $activity) }}" 
                                                    method="POST" 
                                                    class="inline"
                                                    onsubmit="return confirm('{{ __('activities.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn-icon-red"
                                                            title="{{ __('activities.delete') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Cards Mobile --}}
                        <div class="md:hidden space-y-4">
                            @foreach($activities as $index => $activity)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="window.location='{{ route('activities.show', $activity) }}'">
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-semibold text-gray-900 flex-1 text-sm pr-2">{{ $activity->nama_aktivitas }}</h4>
                                    @php
                                        $statusColors = [
                                            'Progress' => 'bg-blue-100 text-blue-800',
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Done' => 'bg-green-100 text-green-800'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusColors[$activity->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ __('activities.' . strtolower($activity->status)) }}
                                    </span>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="text-gray-600 font-medium">{{ $activity->project->nama_project }}</span>
                                            <p class="text-xs text-gray-400">{{ $activity->project->pemilikProject->nama_divisi ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-600">
                                            {{ $activity->tanggal_mulai->format('d M Y') }}
                                            @if($activity->tanggal_selesai)
                                                - {{ $activity->tanggal_selesai->format('d M Y') }}
                                            @endif
                                        </span>
                                    </div>

                                    @if($activity->deskripsi)
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                        </svg>
                                        <span class="text-gray-600">{{ Str::limit($activity->deskripsi, 100) }}</span>
                                    </div>
                                    @endif

                                    @if($activity->lampiran)
                                    <div class="flex items-center" onclick="event.stopPropagation()">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                           target="_blank"
                                           download
                                           class="text-blue-600 hover:text-blue-800 truncate">
                                            {{ Str::limit(basename(str_replace('lampiran/', '', $activity->lampiran)), 30) }}
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-200 flex gap-2" onclick="event.stopPropagation()">
                                    <a href="{{ route('activities.edit', $activity) }}" 
                                    class="btn-yellow flex-1 inline-flex items-center justify-center text-xs">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        {{ __('activities.edit') }}
                                    </a>
                                    
                                    <form action="{{ route('activities.destroy', $activity) }}" 
                                        method="POST" 
                                        class="flex-1"
                                        onsubmit="return confirm('{{ __('activities.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn-red w-full inline-flex items-center justify-center text-xs">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            {{ __('activities.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $activities->appends(request()->query())->links() }}
                        </div>

                        {{-- Preview Export Button (Bottom Right) - SUPERVISI & PKJ --}}
                        @if(auth()->user()->role === 'supervisi' || auth()->user()->role === 'perizinan')
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex justify-end">
                                    <a href="{{ route('activities.my-activities-export-preview', request()->query()) }}" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{ __('activities.preview_export_pdf') }}
                                        @if(auth()->user()->role === 'perizinan')
                                            <span class="ml-1 text-xs">({{ __('activities.my_activities_only') }})</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('activities.no_activities') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'project_uuid', 'status', 'date_from', 'date_to']))
                                    {{ __('activities.no_activities_match_filter') }}
                                @else
                                    {{ __('activities.start_first_activity') }}
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('activities.create') }}" 
                                    class="btn-green inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('activities.add_activity') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function removeEmptyFields(form) {
        // Clone the form data
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Only add non-empty fields
        for (let [key, value] of formData.entries()) {
            // Always include hidden fields (like view_bagian)
            const field = form.querySelector(`[name="${key}"]`);
            const isHidden = field && field.type === 'hidden';
            
            if (isHidden || (value && value.trim() !== '')) {
                params.append(key, value);
            }
        }
        
        // Redirect with clean URL
        const baseUrl = form.action;
        const queryString = params.toString();
        
        if (queryString) {
            window.location.href = baseUrl + '?' + queryString;
        } else {
            window.location.href = baseUrl;
        }
        
        // Prevent default form submission
        return false;
    }
    </script>
    @endpush
</x-app-layout>