    <x-app-layout>
        <x-slot name="title">{{ __('activities.page_title') }}</x-slot>
        
        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('activities.success') }}</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('activities.error') }}</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <x-slot name="header">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        @if(auth()->user()->role === 'supervisi')
                            {{ __('activities.all_activities') }}
                        @elseif(auth()->user()->role === 'perizinan')
                            {{ __('activities.activities_' . strtolower(request('view_bagian', 'PKJ'))) }}
                        @else
                            {{ __('activities.my_activities') }}
                        @endif
                    </h2>
                    @if(auth()->user()->role === 'perizinan')
                        <p class="text-sm text-gray-600 mt-1">
                            {{ __('activities.switch_section_instruction') }}
                        </p>
                    @endif
                </div>

                {{-- Actions untuk Non-Supervisi --}}
                @if(auth()->user()->role !== 'supervisi')
                    <a href="{{ route('activities.create') }}" 
                    class="inline-flex items-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('activities.add_activity') }}
                    </a>
                @endif
            </div>
        </x-slot>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                {{-- Switch View untuk Perizinan --}}
                @if(auth()->user()->role === 'perizinan')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('activities.select_section') }}</h3>
                            <div class="flex space-x-3">
                                <a href="{{ route('activities.index', array_merge(request()->except('view_bagian'), ['view_bagian' => 'PKJ'])) }}" 
                                    class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition 
                                        {{ request('view_bagian', 'PKJ') === 'PKJ' 
                                            ? 'bg-[#0F5132] text-white border-[#0F5132] hover:bg-[#0A3D24]' 
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ __('activities.activities_pkj') }}
                                </a>
                                
                                <a href="{{ route('activities.index', array_merge(request()->except('view_bagian'), ['view_bagian' => 'PGB'])) }}" 
                                    class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition 
                                        {{ request('view_bagian') === 'PGB' 
                                            ? 'bg-[#0F5132] text-white border-[#0F5132] hover:bg-[#0A3D24]' 
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                    </svg>
                                    {{ __('activities.activities_pgb') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4 md:p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                                    <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 md:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('activities.total_activities') }}</dt>
                                        <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4 md:p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                                    <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 md:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('activities.progress') }}</dt>
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
                                        <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('activities.pending') }}</dt>
                                        <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4 md:p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-emerald-500 rounded-md p-2 md:p-3">
                                    <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 md:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">{{ __('activities.done') }}</dt>
                                        <dd class="text-xl md:text-2xl font-semibold text-gray-900">{{ $stats['done'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Section - Collapsible --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="{ filterOpen: {{ request()->hasAny(['search', 'status', 'project_uuid', 'start_date', 'end_date', 'quick_filter']) ? 'true' : 'false' }} }">
                    <div class="p-4 md:p-6">
                        {{-- Toggle Button --}}
                        <div class="flex justify-between items-center">
                            <h3 class="text-base md:text-lg font-semibold">{{ __('activities.filter_search') }}</h3>
                            <button @click="filterOpen = !filterOpen" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span x-text="filterOpen ? '{{ __('activities.hide_filter') }}' : '{{ __('activities.show_filter') }}'"></span>
                                <svg class="w-4 h-4 ml-2 transition-transform" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Active Filters Badge --}}
                        @if(request()->hasAny(['search', 'status', 'project_uuid', 'user_id', 'start_date', 'end_date', 'quick_filter']))
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="text-xs font-medium text-gray-600">{{ __('activities.active_filters') }}</span>
                                @if(request('search'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#D1FAE5] text-[#0F5132]">
                                        {{ __('activities.search_label') }} "{{ request('search') }}"
                                    </span>
                                @endif
                                @if(request('status'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#D1FAE5] text-[#0F5132]">
                                        {{ __('activities.status_label') }} {{ request('status') }}
                                    </span>
                                @endif
                                @if(request('project_uuid'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#D1FAE5] text-[#0F5132]">
                                        {{ __('activities.project_label') }} {{ $projects->firstWhere('uuid', request('project_uuid'))->nama_project ?? 'Unknown' }}
                                    </span>
                                @endif
                                @if(request('user_id'))
                                    @php
                                        $selectedUser = \App\Models\User::find(request('user_id'));
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#D1FAE5] text-[#0F5132]">
                                        {{ __('activities.pic_label') }} {{ $selectedUser ? $selectedUser->name : 'Unknown' }}
                                    </span>
                                @endif
                                @if(request('start_date') || request('end_date'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#D1FAE5] text-[#0F5132]">
                                        {{ __('activities.date_label') }} 
                                        @if(request('start_date') && request('end_date'))
                                            {{ \Carbon\Carbon::parse(request('start_date'))->format('d M') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                                        @elseif(request('start_date'))
                                            {{ __('activities.from_date') }} {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}
                                        @else
                                            {{ __('activities.until_date') }} {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Filter Form (Collapsible) --}}
                        <div x-show="filterOpen" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            class="mt-6">
                            
                            <form method="GET" action="{{ route('activities.index') }}" class="space-y-6" onsubmit="removeEmptyFields(this)">
                                {{-- Preserve view_bagian --}}
                                @if(request('view_bagian'))
                                    <input type="hidden" name="view_bagian" value="{{ request('view_bagian') }}">
                                @endif

                                {{-- Quick Date Filter --}}
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-700">{{ __('activities.quick_date_filter') }}</label>
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('activities.index', array_merge(request()->except(['quick_filter', 'start_date', 'end_date', 'page']), ['quick_filter' => 'today'])) }}" 
                                        class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request('quick_filter') === 'today' ? 'bg-[#0F5132] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                            {{ __('activities.today') }}
                                        </a>
                                        <a href="{{ route('activities.index', array_merge(request()->except(['quick_filter', 'start_date', 'end_date', 'page']), ['quick_filter' => 'this_week'])) }}" 
                                        class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request('quick_filter') === 'this_week' ? 'bg-[#0F5132] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                            {{ __('activities.this_week') }}
                                        </a>
                                        <a href="{{ route('activities.index', array_merge(request()->except(['quick_filter', 'start_date', 'end_date', 'page']), ['quick_filter' => 'this_month'])) }}" 
                                        class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request('quick_filter') === 'this_month' ? 'bg-[#0F5132] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                            {{ __('activities.this_month') }}
                                        </a>
                                        @if(request()->has('quick_filter'))
                                            <a href="{{ route('activities.index', request()->except(['quick_filter', 'page'])) }}" 
                                            class="px-4 py-2.5 text-sm font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                                                {{ __('activities.reset_date') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- All Filters in One Row --}}
                                <div class="space-y-4">
                                    <label class="block text-sm font-semibold text-gray-700">{{ __('activities.advanced_filter') }}</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-8 gap-4 items-end">
                                        {{-- Search --}}
                                        <div class="lg:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('activities.search_activity') }}</label>
                                            <input type="text" name="search" value="{{ request('search') }}"
                                                placeholder="{{ __('activities.search_placeholder') }}"
                                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                        </div>

                                        {{-- Date Range --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('activities.from_date_label') }}</label>
                                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                                max="{{ now()->format('Y-m-d') }}"
                                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('activities.to_date_label') }}</label>
                                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                                max="{{ now()->format('Y-m-d') }}"
                                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                        </div>

                                        {{-- Status --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('activities.status') }}</label>
                                            <select name="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                                <option value="">{{ __('activities.all_status') }}</option>
                                                <option value="Progress" {{ request('status') === 'Progress' ? 'selected' : '' }}>{{ __('activities.progress') }}</option>
                                                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>{{ __('activities.pending') }}</option>
                                                <option value="Done" {{ request('status') === 'Done' ? 'selected' : '' }}>{{ __('activities.done') }}</option>
                                            </select>
                                        </div>

                                        {{-- Project --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('activities.project') }}</label>
                                            <select name="project_uuid" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                                <option value="">{{ __('activities.all_projects') }}</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->uuid }}" {{ request('project_uuid') == $project->uuid ? 'selected' : '' }}>
                                                        {{ Str::limit($project->nama_project, 30) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- PIC --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('activities.pic') }}</label>
                                            <select name="user_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                                <option value="">{{ __('activities.all_pics') }}</option>
                                                @foreach(\App\Models\User::whereIn('role', ['perizinan', 'karyawan'])->orderBy('name')->get() as $user)
                                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->bagian }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex gap-2">
                                            <button type="submit" 
                                                    class="p-2.5 bg-[#0F5132] text-white rounded-md hover:bg-[#0A3D24] transition"
                                                    title="{{ __('activities.apply_filter') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('activities.index') }}" 
                                            class="p-2.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition"
                                            title="{{ __('activities.reset_filter') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Activities Table --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 md:p-6">
                        @if($activities->count() > 0)
                            {{-- Desktop Table --}}
                            <div class="hidden lg:block overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('activities.no') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('activities.activity_name') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('activities.project') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('activities.pic') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('activities.status') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('activities.date') }}</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('activities.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($activities as $index => $activity)
                                            <tr class="hover:bg-gray-50 transition cursor-pointer" 
                                                onclick="window.location='{{ route('activities.show', $activity) }}'">
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ $activities->firstItem() + $index }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="font-medium text-gray-900">{{ $activity->nama_aktivitas }}</div>
                                                    <div class="text-xs text-gray-500">{{ $activity->jenis_kegiatan }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    <div>{{ $activity->project->nama_project }}</div>
                                                    <div class="text-xs text-gray-500">{{ $activity->project->pemilikProject->nama_divisi }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->user->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @php
                                                        $statusColors = [
                                                            'Progress' => 'bg-blue-100 text-blue-800',
                                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                                            'Done' => 'bg-green-100 text-green-800'
                                                        ];
                                                        $statusLabels = [
                                                            'Progress' => __('activities.progress'),
                                                            'Pending' => __('activities.pending'),
                                                            'Done' => __('activities.done')
                                                        ];
                                                    @endphp
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$activity->status] }}">
                                                        {{ $statusLabels[$activity->status] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ $activity->tanggal_mulai->format('d M Y') }}
                                                    @if($activity->tanggal_selesai)
                                                        <br><span class="text-xs text-gray-500">{{ __('activities.until') }} {{ $activity->tanggal_selesai->format('d M Y') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                                    <div class="flex items-center justify-center gap-2">
                                                        {{-- Hanya OWNER yang bisa edit/hapus, Supervisi TIDAK --}}
                                                        @if(auth()->user()->role !== 'supervisi' && auth()->user()->id === $activity->user_id)
                                                            <a href="{{ route('activities.edit', $activity) }}" 
                                                            class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50 transition"
                                                            title="{{ __('activities.edit') }}">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            
                                                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline" 
                                                                onsubmit="return confirm('{{ __('activities.confirm_delete') }} {{ $activity->nama_aktivitas }}?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition"
                                                                        title="{{ __('activities.delete') }}">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @else
                                                            {{-- Supervisi atau bukan owner: Tombol View Only --}}
                                                            <a href="{{ route('activities.show', $activity) }}" 
                                                            class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition"
                                                            title="{{ __('activities.view_detail') }}">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile Cards --}}
                            <div class="lg:hidden space-y-4">
                                @foreach($activities as $activity)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition cursor-pointer"
                                        onclick="window.location='{{ route('activities.show', $activity) }}'">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-semibold text-gray-900 text-sm flex-1">
                                                {{ $activity->nama_aktivitas }}
                                            </h4>
                                            @php
                                                $statusColors = [
                                                    'Progress' => 'bg-blue-100 text-blue-800',
                                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                                    'Done' => 'bg-green-100 text-green-800'
                                                ];
                                                $statusLabels = [
                                                    'Progress' => __('activities.progress'),
                                                    'Pending' => __('activities.pending'),
                                                    'Done' => __('activities.done')
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full ml-2 {{ $statusColors[$activity->status] }}">
                                                {{ $statusLabels[$activity->status] }}
                                            </span>
                                        </div>
                                        
                                        <div class="space-y-2 text-xs text-gray-600 mb-3">
                                            <div class="flex justify-between">
                                                <span class="font-medium">{{ __('activities.project') }}:</span>
                                                <span class="text-right">{{ Str::limit($activity->project->nama_project, 25) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="font-medium">{{ __('activities.pic') }}:</span>
                                                <span class="text-right">{{ $activity->user->name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="font-medium">{{ __('activities.date') }}:</span>
                                                <span class="text-right">{{ $activity->tanggal_mulai->format('d M Y') }}</span>
                                            </div>
                                        </div>

                                        @if(auth()->user()->role !== 'supervisi' && auth()->user()->id === $activity->user_id)
                                            <div class="flex gap-2 pt-3 border-t border-gray-200" onclick="event.stopPropagation()">
                                                <a href="{{ route('activities.edit', $activity) }}" 
                                                class="flex-1 text-center px-3 py-2 bg-yellow-500 text-white text-xs font-semibold rounded hover:bg-yellow-600 transition flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    {{ __('activities.edit') }}
                                                </a>
                                                
                                                <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="flex-1"
                                                    onsubmit="return confirm('{{ __('activities.confirm_delete') }} {{ $activity->nama_aktivitas }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-full px-3 py-2 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        {{ __('activities.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            {{-- Supervisi atau bukan owner: View Only Button --}}
                                            <div class="pt-3 border-t border-gray-200" onclick="event.stopPropagation()">
                                                <a href="{{ route('activities.show', $activity) }}" 
                                                class="block text-center px-3 py-2 bg-blue-500 text-white text-xs font-semibold rounded hover:bg-blue-600 transition flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    {{ __('activities.view_detail') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination --}}
                            <div class="mt-6">
                                {{ $activities->appends(request()->query())->links() }}
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-4 text-base font-medium text-gray-900">
                                    @if(request()->hasAny(['search', 'status', 'project_uuid', 'start_date', 'end_date']))
                                        {{ __('activities.no_activities') }}
                                    @else
                                        {{ __('activities.no_activities_yet') }}
                                    @endif
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    @if(request()->hasAny(['search', 'status', 'project_uuid', 'start_date', 'end_date']))
                                        {{ __('activities.try_change_filter') }}
                                    @else
                                        {{ __('activities.start_first_activity') }}
                                    @endif
                                </p>
                                @if(!request()->hasAny(['search', 'status', 'project_uuid', 'start_date', 'end_date']) && auth()->user()->role !== 'supervisi')
                                    <div class="mt-6">
                                        <a href="{{ route('activities.create') }}" 
                                        class="inline-flex items-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] transition">
                                            + {{ __('activities.add_activity') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- ✅ EXPORT BUTTON - Background putih sama dengan tabel --}}
                    @if($activities->count() > 0 && (auth()->user()->role === 'supervisi' || auth()->user()->role === 'perizinan'))
                        <div class="px-6 py-4 bg-white border-t border-gray-200 flex justify-end">
                            <a href="{{ route('activities.export-preview', request()->query()) }}" 
                            class="inline-flex items-center gap-2 px-6 py-3 bg-[#0F5132] hover:bg-[#0A3D24] rounded-lg text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ __('activities.preview_export_pdf') }}
                                @if(auth()->user()->role === 'supervisi')
                                @elseif(auth()->user()->role === 'perizinan')
                                    <span class="ml-1 text-xs opacity-90">({{ __('activities.activities_' . strtolower(request('view_bagian', 'PKJ'))) }})</span>
                                @endif
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        function removeEmptyFields(form) {
            // Get all input, select, and textarea elements
            const fields = form.querySelectorAll('input, select, textarea');
            
            fields.forEach(field => {
                // Skip hidden fields (like view_bagian)
                if (field.type === 'hidden') return;
                
                // Remove field if empty
                if (!field.value || field.value.trim() === '') {
                    field.removeAttribute('name');
                }
            });
            
            return true;
        }
        </script>
        @endpush
    </x-app-layout>