<x-app-layout>
    <x-slot name="title">{{ __('dashboard.title') }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('dashboard.title') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    @if(auth()->user()->role === 'supervisi')
                        {{ __('dashboard.monitoring_all') }}
                    @else
                        {{ __('dashboard.your_projects') }}
                    @endif
                </p>
            </div>
            
            <!-- ✅ REVISED: Quick Actions for NEW ROLES -->
            <div class="flex gap-2 w-full sm:w-auto">
                @if(auth()->user()->role === 'supervisi')
                    {{-- ✅ UPDATED: Supervisi - Project + Aktivitas --}}
                    <a href="{{ route('projects.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_project') }}
                    </a>
                    <a href="{{ route('activities.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] focus:bg-[#0A3D24] active:bg-[#083D22] focus:outline-none focus:ring-2 focus:ring-[#0F5132] focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_activity') }}
                    </a>
                    
                @elseif(auth()->user()->role === 'kabag_pgb')
                    {{-- Kabag PGB - Project + Aktivitas --}}
                    <a href="{{ route('projects.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_project') }}
                    </a>
                    <a href="{{ route('activities.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] focus:bg-[#0A3D24] active:bg-[#083D22] focus:outline-none focus:ring-2 focus:ring-[#0F5132] focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_activity') }}
                    </a>
                    
                @elseif(auth()->user()->role === 'perizinan')
                    {{-- Perizinan (Kabag PKJ): Project + Aktivitas --}}
                    <a href="{{ route('projects.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_project') }}
                    </a>
                    <a href="{{ route('activities.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] focus:bg-[#0A3D24] active:bg-[#083D22] focus:outline-none focus:ring-2 focus:ring-[#0F5132] focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_activity') }}
                    </a>
                    
                @else
                    {{-- Karyawan/Staff (PGB atau PKJ): Hanya Aktivitas --}}
                    <a href="{{ route('activities.create') }}" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] focus:bg-[#0A3D24] active:bg-[#083D22] focus:outline-none focus:ring-2 focus:ring-[#0F5132] focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('dashboard.add_activity') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Summary Cards with Progress Bars -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Card: Total Project -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $totalProjects }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('dashboard.total_projects') }}</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $projectProgressPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $projectProgressPercentage }}% {{ __('dashboard.in_progress') }}</p>
                    </div>
                </div>

                <!-- Card: Total Aktivitas -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $totalActivities }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('dashboard.total_activities') }}</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $activityDonePercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $activityDonePercentage }}% {{ __('dashboard.completed') }}</p>
                    </div>
                </div>

                <!-- Card: Progress Status -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $projectsProgress }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('dashboard.project_progress') }}</h3>
                        <div class="flex items-center justify-between text-xs text-gray-600 mt-2">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                {{ $activitiesProgress }} {{ __('dashboard.activities') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card: Done Status -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex-shrink-0 bg-emerald-500 rounded-lg p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">{{ $projectsDone }}</p>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('dashboard.project_done') }}</h3>
                        <div class="flex items-center justify-between text-xs text-gray-600 mt-2">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-1"></span>
                                {{ $activitiesDone }} {{ __('dashboard.activities') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section - 3 Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Horizontal Bar Chart: Project per Divisi -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.projects_per_division') }}</h3>
                            <span class="text-xs text-gray-500">{{ __('dashboard.distribution') }}</span>
                        </div>
                        
                        <div id="divisionChartLoading" class="animate-pulse">
                            <div class="h-48 bg-gray-200 rounded"></div>
                        </div>
                        
                        <div id="divisionChartContainer" style="display: none;">
                            <div style="position: relative; height: 200px;"> 
                                <canvas id="projectsPerDivisionChart"></canvas>
                            </div>
                        </div>

                        <div id="divisionChartError" style="display: none;" class="text-center py-8">
                            <p class="text-sm text-gray-500">{{ __('dashboard.failed_to_load') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Donut Chart: Status Aktivitas -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.activity_status') }}</h3>
                            <span class="text-xs text-gray-500">{{ __('dashboard.distribution') }}</span>
                        </div>
                        
                        <div id="statusChartLoading" class="animate-pulse">
                            <div class="h-48 bg-gray-200 rounded"></div>
                        </div>
                        
                        <div id="statusChartContainer" style="display: none;">
                            <div style="position: relative; height: 200px;">
                                <canvas id="activitiesStatusChart"></canvas>
                            </div>
                        </div>

                        <div id="statusChartError" style="display: none;" class="text-center py-8">
                            <p class="text-sm text-gray-500">{{ __('dashboard.failed_to_load') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Doughnut Chart: Urgensi Project -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.project_urgency') }}</h3>
                            <span class="text-xs text-gray-500">{{ __('dashboard.priority') }}</span>
                        </div>
                        
                        <div id="urgencyChartLoading" class="animate-pulse">
                            <div class="h-48 bg-gray-200 rounded"></div>
                        </div>
                        
                        <div id="urgencyChartContainer" style="display: none;">
                            <div style="position: relative; height: 200px;">
                                <canvas id="projectUrgencyChart"></canvas>
                            </div>
                        </div>

                        <div id="urgencyChartError" style="display: none;" class="text-center py-8">
                            <p class="text-sm text-gray-500">{{ __('dashboard.failed_to_load') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Charts (2 columns) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Bar Chart: Project per Karyawan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">{{ __('dashboard.projects_per_employee') }}</h3>
                        
                        <div id="userChartLoading" class="animate-pulse">
                            <div class="h-64 bg-gray-200 rounded"></div>
                        </div>
                        
                        <div id="userChartContainer" style="display: none;">
                            <div style="position: relative; height: 250px;">
                                <canvas id="projectsPerUserChart"></canvas>
                            </div>
                        </div>

                        <div id="userChartError" style="display: none;" class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">{{ __('dashboard.failed_to_load') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart: Employee Activities -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.employee_activities') }}</h3>
                            <span class="text-xs text-gray-500">{{ __('dashboard.last_week') }}</span>
                        </div>
                        
                        <div id="employeeActivityChartLoading" class="animate-pulse">
                            <div class="h-64 bg-gray-200 rounded"></div>
                        </div>
                        
                        <div id="employeeActivityChartContainer" style="display: none;">
                            <div style="position: relative; height: 250px;">
                                <canvas id="employeeActivitiesChart"></canvas>
                            </div>
                        </div>

                        <div id="employeeActivityChartError" style="display: none;" class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">{{ __('dashboard.failed_to_load') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.recent_activities') }}</h3>
                        
                        {{-- ✅ FIXED: Link berbeda berdasarkan role --}}
                        @if(auth()->user()->role === 'supervisi')
                            {{-- Super Admin: Lihat semua aktivitas --}}
                            <a href="{{ route('activities.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                {{ __('dashboard.view_all') }} →
                            </a>
                        @elseif(auth()->user()->role === 'perizinan')
                            {{-- PKJ: Ke halaman PKJ Activities --}}
                            <a href="{{ route('activities.index', ['view_bagian' => 'PKJ']) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                {{ __('dashboard.view_all') }} →
                            </a>
                        @elseif(auth()->user()->role === 'kabag_pgb')
                            {{-- Kabag PGB: Ke halaman Employee Activities --}}
                            <a href="{{ route('activities.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                {{ __('dashboard.view_all') }} →
                            </a>
                        @else
                            {{-- Karyawan: Ke halaman Aktivitas Saya --}}
                            <a href="{{ route('activities.my-activities') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition">
                                {{ __('dashboard.view_all') }} →
                            </a>
                        @endif
                    </div>
                    
                    @if($recentActivities->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('dashboard.activity') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('dashboard.project') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('dashboard.pic') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('dashboard.status') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('dashboard.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentActivities as $activity)
                                    <tr class="hover:bg-gray-50 transition cursor-pointer" 
                                        onclick="window.location='{{ route('projects.show', $activity->project_id) }}'">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ Str::limit($activity->nama_aktivitas, 40) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <span class="truncate">{{ Str::limit($activity->project->nama_project, 30) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $activity->user->bagian === 'PKJ' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} text-xs font-semibold">
                                                    {{ substr($activity->user->name, 0, 1) }}
                                                </span>
                                                <span>{{ $activity->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($activity->status == 'Done') bg-green-100 text-green-800
                                                @elseif($activity->status == 'Progress') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                @if($activity->status == 'Done')
                                                    {{ __('dashboard.done') }}
                                                @elseif($activity->status == 'Progress')
                                                    {{ __('dashboard.progress') }}
                                                @else
                                                    {{ __('dashboard.pending') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $activity->tanggal_mulai->format('d M Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-3">
                            @foreach($recentActivities as $activity)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:shadow-md transition cursor-pointer"
                                 onclick="window.location='{{ route('projects.show', $activity->project_id) }}'">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900 text-sm flex-1 pr-2">
                                        {{ Str::limit($activity->nama_aktivitas, 35) }}
                                    </h4>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full flex-shrink-0
                                        @if($activity->status == 'Done') bg-green-100 text-green-800
                                        @elseif($activity->status == 'Progress') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($activity->status == 'Done')
                                            {{ __('dashboard.done') }}
                                        @elseif($activity->status == 'Progress')
                                            {{ __('dashboard.progress') }}
                                        @else
                                            {{ __('dashboard.pending') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="space-y-1 text-xs text-gray-600">
                                    <p><span class="font-medium">{{ __('dashboard.project') }}:</span> {{ Str::limit($activity->project->nama_project, 30) }}</p>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium">{{ __('dashboard.pic') }}:</span>
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $activity->user->bagian === 'PKJ' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} text-[10px] font-semibold">
                                            {{ substr($activity->user->name, 0, 1) }}
                                        </span>
                                        <span>{{ $activity->user->name }}</span>
                                    </div>
                                    <p><span class="font-medium">{{ __('dashboard.date') }}:</span> {{ $activity->tanggal_mulai->format('d M Y') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- ✅ REVISED: Empty State berdasarkan role BARU --}}
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('dashboard.no_activities_yet') }}</h3>
                            
                            @if(auth()->user()->role === 'supervisi')
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('dashboard.no_activities_system') }}
                                </p>
                            @elseif(auth()->user()->role === 'kabag_pgb' || auth()->user()->role === 'perizinan')
                                <p class="mt-1 text-sm text-gray-500">
                                    Mulai tambahkan aktivitas atau project untuk tim Anda
                                </p>
                                <div class="mt-4 flex justify-center gap-2">
                                    <a href="{{ route('projects.create') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        TAMBAH PROJECT
                                    </a>
                                    <a href="{{ route('projects.create') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        TAMBAH PROJECT
                                    </a>
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('dashboard.start_adding_activities') }}
                                </p>
                                <a href="{{ route('activities.create') }}" 
                                class="mt-4 inline-flex items-center px-4 py-2 bg-[#0F5132] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0A3D24] focus:bg-[#0A3D24] active:bg-[#083D22] focus:outline-none focus:ring-2 focus:ring-[#0F5132] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('dashboard.add_first_activity') }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- CSS untuk Empty State Animation -->
    <style>
    .empty-chart-icon {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    </style>
    
    <script>
        // Chart color palette
        const colors = {
            blue: 'rgba(59, 130, 246, 0.8)',
            green: 'rgba(34, 197, 94, 0.8)',
            yellow: 'rgba(234, 179, 8, 0.8)',
            orange: 'rgba(249, 115, 22, 0.8)',
            red: 'rgba(239, 68, 68, 0.8)',
            purple: 'rgba(168, 85, 247, 0.8)',
            pink: 'rgba(236, 72, 153, 0.8)'
        };

        // Translation strings for charts
        const translations = {
            totalActivities: '{{ __("dashboard.total_project") }}',
            progress: '{{ __("dashboard.progress") }}',
            pending: '{{ __("dashboard.pending") }}',
            done: '{{ __("dashboard.done") }}'
        };

        // ===== HELPER: Show Empty Chart State =====
        function showEmptyChartState(containerId, loadingId, message, iconType = 'chart') {
            document.getElementById(loadingId).style.display = 'none';
            
            const icons = {
                chart: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>`,
                line: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>`,
                pie: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>`,
                users: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>`,
                folder: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>`
            };
            
            const container = document.getElementById(containerId);
            container.style.display = 'block';
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 text-center" style="height: 200px;">
                    <div class="mb-3 empty-chart-icon">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${icons[iconType] || icons.chart}
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">${message}</p>
                    <p class="text-xs text-gray-400 mt-1">Data will appear once you add ${iconType === 'users' ? 'users or projects' : iconType === 'pie' || iconType === 'folder' ? 'projects' : 'activities'}</p>
                </div>
            `;
        }

        // 1. Pie Chart: Project per Division
        fetch('/api/projects-per-division')
            .then(response => response.json())
            .then(data => {
                const hasData = data && data.length > 0 && data.some(item => item.total > 0);
                
                if (!hasData) {
                    showEmptyChartState('divisionChartContainer', 'divisionChartLoading', 'No active projects', 'folder');
                    return;
                }
                
                document.getElementById('divisionChartLoading').style.display = 'none';
                document.getElementById('divisionChartContainer').style.display = 'block';
                
                const ctx = document.getElementById('projectsPerDivisionChart').getContext('2d');
                
                const maxValue = Math.max(...data.map(item => item.total));
                const xAxisMax = maxValue === 0 ? 5 : Math.ceil(maxValue / 5) * 5;
                
                const gradientColors = data.map((item, index) => {
                    const gradient = ctx.createLinearGradient(0, 0, 400, 0);
                    const hue = index * 40;
                    gradient.addColorStop(0, `hsla(${hue}, 70%, 60%, 0.9)`);
                    gradient.addColorStop(1, `hsla(${hue}, 70%, 50%, 0.7)`);
                    return gradient;
                });
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.kode_divisi || item.nama_divisi),
                        datasets: [{
                            label: 'Active Projects',
                            data: data.map(item => item.total),
                            backgroundColor: gradientColors,
                            hoverBackgroundColor: data.map((item, index) => {
                                const hue = index * 40;
                                return `hsla(${hue}, 80%, 55%, 1)`;
                            }),
                            borderWidth: 0,
                            borderRadius: 6,
                            barThickness: 20,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        const item = data[index];
                                        
                                        if (item.is_others) {
                                            return 'Divisi Lainnya (' + item.details.length + ' divisi)';
                                        }
                                        return item.nama_divisi;
                                    },
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        const item = data[index];
                                        const total = context.parsed.x;
                                        
                                        if (!item.is_others) {
                                            return `${total} active project${total > 1 ? 's' : ''}`;
                                        }
                                        
                                        return [`Total: ${total} projects`, ''];
                                    },
                                    afterLabel: function(context) {
                                        const index = context.dataIndex;
                                        const item = data[index];
                                        
                                        if (item.is_others && item.details) {
                                            const details = item.details
                                                .sort((a, b) => b.total - a.total)
                                                .map(d => `  • ${d.kode}: ${d.total} project${d.total > 1 ? 's' : ''}`)
                                                .join('\n');
                                            
                                            return '\n' + details;
                                        }
                                        
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: xAxisMax,
                                ticks: { 
                                    stepSize: 1,
                                    precision: 0,
                                    font: { size: 10 }
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: true
                                }
                            },
                            y: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10 },
                                    autoSkip: false,
                                    callback: function(value, index) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 12 ? label.substring(0, 12) + '...' : label;
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 800,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading division chart:', error);
                document.getElementById('divisionChartLoading').style.display = 'none';
                document.getElementById('divisionChartError').style.display = 'block';
            });

        // 2. Donut Chart: Activities Status Distribution
        fetch('/api/activities-status-distribution')
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    showEmptyChartState('statusChartContainer', 'statusChartLoading', 'No activities yet', 'pie');
                    return;
                }
                
                document.getElementById('statusChartLoading').style.display = 'none';
                document.getElementById('statusChartContainer').style.display = 'block';
                
                const ctx = document.getElementById('activitiesStatusChart').getContext('2d');
                const statusColors = {
                    'Progress': colors.blue,
                    'Pending': colors.yellow,
                    'Done': colors.green
                };
                
                const translatedData = data.map(item => {
                    let translatedStatus = item.status;
                    if (item.status === 'Progress') translatedStatus = translations.progress;
                    if (item.status === 'Pending') translatedStatus = translations.pending;
                    if (item.status === 'Done') translatedStatus = translations.done;
                    return { ...item, translatedStatus };
                });
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: translatedData.map(item => item.translatedStatus),
                        datasets: [{
                            data: translatedData.map(item => item.total),
                            backgroundColor: data.map(item => statusColors[item.status] || colors.blue),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 10, font: { size: 11 } }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                document.getElementById('statusChartLoading').style.display = 'none';
                document.getElementById('statusChartError').style.display = 'block';
            });

        // 3. Doughnut Chart: Project Urgency
        fetch('/api/project-urgency-distribution')
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    showEmptyChartState('urgencyChartContainer', 'urgencyChartLoading', 'No projects yet', 'pie');
                    return;
                }
                
                document.getElementById('urgencyChartLoading').style.display = 'none';
                document.getElementById('urgencyChartContainer').style.display = 'block';
                
                const ctx = document.getElementById('projectUrgencyChart').getContext('2d');
                const urgencyColors = {
                    'Low': colors.green,
                    'Medium': colors.yellow,
                    'High': colors.orange,
                    'Very High': colors.red
                };
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.urgensi),
                        datasets: [{
                            data: data.map(item => item.total),
                            backgroundColor: data.map(item => urgencyColors[item.urgensi] || colors.blue),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 10, font: { size: 11 } }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                document.getElementById('urgencyChartLoading').style.display = 'none';
                document.getElementById('urgencyChartError').style.display = 'block';
            });

        // 4. Bar Chart: Project per User
        fetch('/api/projects-per-user')
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    showEmptyChartState('userChartContainer', 'userChartLoading', 'No projects assigned yet', 'users');
                    return;
                }
                
                document.getElementById('userChartLoading').style.display = 'none';
                document.getElementById('userChartContainer').style.display = 'block';
                
                const sortedData = data.sort((a, b) => b.total - a.total);
                const maxValue = Math.max(...sortedData.map(item => item.total));
                const yAxisMax = maxValue === 0 ? 5 : Math.ceil(maxValue / 5) * 5;
                
                const ctx = document.getElementById('projectsPerUserChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: sortedData.map(item => item.name),
                        datasets: [{
                            label: 'Total Proyek',
                            data: sortedData.map(item => item.total),
                            backgroundColor: colors.blue,
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return `Total Proyek: ${value}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: { size: 10 },
                                    callback: function(value, index) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 15 ? label.substring(0, 15) + '...' : label;
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: yAxisMax,
                                ticks: { 
                                    stepSize: 1,
                                    precision: 0,
                                    font: { size: 11 }
                                },
                                grid: {
                                    display: true,
                                    drawBorder: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                        animation: {
                            duration: 800,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading user chart:', error);
                document.getElementById('userChartLoading').style.display = 'none';
                document.getElementById('userChartError').style.display = 'block';
            });

        // 5. Bar Chart: Employee Activities
        fetch('/api/employee-activities')
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    showEmptyChartState('employeeActivityChartContainer', 'employeeActivityChartLoading', 'No employees found', 'users');
                    return;
                }
                
                document.getElementById('employeeActivityChartLoading').style.display = 'none';
                document.getElementById('employeeActivityChartContainer').style.display = 'block';
                
                const sortedData = data.sort((a, b) => b.total - a.total);
                const maxValue = Math.max(...sortedData.map(item => item.total));
                const yAxisMax = maxValue === 0 ? 5 : Math.ceil(maxValue / 5) * 5;
                
                const ctx = document.getElementById('employeeActivitiesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: sortedData.map(item => item.name),
                        datasets: [{
                            label: 'Total Aktivitas',
                            data: sortedData.map(item => item.total),
                            backgroundColor: colors.green,
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return value > 0 ? `Total Aktivitas: ${value}` : 'Tidak ada aktivitas';
                                    },
                                    afterLabel: function(context) {
                                        const index = context.dataIndex;
                                        const progress = sortedData[index].progress;
                                        const pending = sortedData[index].pending;
                                        
                                        if (progress > 0 || pending > 0) {
                                            let breakdown = [];
                                            if (progress > 0) breakdown.push(`Progress: ${progress}`);
                                            if (pending > 0) breakdown.push(`Pending: ${pending}`);
                                            return breakdown.join(', ');
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: { size: 10 },
                                    callback: function(value, index) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 15 ? label.substring(0, 15) + '...' : label;
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: yAxisMax,
                                ticks: { 
                                    stepSize: 1,
                                    precision: 0,
                                    font: { size: 11 }
                                },
                                grid: {
                                    display: true,
                                    drawBorder: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                        animation: {
                            duration: 800,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading employee activities:', error);
                document.getElementById('employeeActivityChartLoading').style.display = 'none';
                document.getElementById('employeeActivityChartError').style.display = 'block';
            });
    </script>
</x-app-layout>