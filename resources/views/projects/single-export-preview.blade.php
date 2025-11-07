<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('projects.preview_export') }}: {{ $project->nama_project }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Sticky Action Buttons --}}
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-6 py-4 mb-6 shadow-sm sm:rounded-lg">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    {{-- Back Button - FIX: Gunakan object --}}
                    <a href="{{ route('projects.show', $project) }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('projects.back_to_detail') }}
                    </a>

                    {{-- Download Button - FIX: Gunakan object --}}
                    <a href="{{ route('projects.single-export-pdf', $project) }}" 
                    class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('projects.download_pdf') }}
                    </a>
                </div>
            </div>

            {{-- Export Summary - Blue Gradient Card --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">{{ __('projects.project_report') }}</h3>
                    <p class="text-sm opacity-90 mb-4">{{ __('projects.preview_export_data') }}</p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-xs opacity-90 mb-1">{{ __('projects.total_activities') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['total_activities'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-xs opacity-90 mb-1">PGB {{ __('projects.total_activities') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['pgb_activities'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-xs opacity-90 mb-1">PKJ {{ __('projects.total_activities') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['pkj_activities'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-xs opacity-90 mb-1">{{ __('projects.progress') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['progress'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-xs opacity-90 mb-1">{{ __('projects.pending') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-xs opacity-90 mb-1">{{ __('projects.done') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['done'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Project Information --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('projects.project_information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.project_name_label') }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $project->nama_project }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.status') }}</p>
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
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] }}">
                                {{ $statusLabels[$project->status] }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.initiation_date') }}</p>
                            <p class="text-sm text-gray-900">{{ $project->tanggal_inisiasi->format('d F Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.target_implementation') }}</p>
                            <p class="text-sm text-gray-900">{{ $project->target_implementasi }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.urgency') }}</p>
                            @php
                                $urgencyColors = [
                                    'Low' => 'bg-green-100 text-green-800',
                                    'Medium' => 'bg-yellow-100 text-yellow-800',
                                    'High' => 'bg-orange-100 text-orange-800',
                                    'Very High' => 'bg-red-100 text-red-800'
                                ];
                                $urgencyLabels = [
                                    'Low' => __('projects.low'),
                                    'Medium' => __('projects.medium'),
                                    'High' => __('projects.high'),
                                    'Very High' => __('projects.very_high')
                                ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $urgencyColors[$project->urgensi] }}">
                                {{ $urgencyLabels[$project->urgensi] }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.project_nature') }}</p>
                            <p class="text-sm text-gray-900">{{ $project->sifat_project }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.project_owner') }}</p>
                            <p class="text-sm text-gray-900">{{ $project->pemilikProject->nama_divisi }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.pic_project') }}</p>
                            <p class="text-sm text-gray-900">{{ $project->picProyek->name }}</p>
                        </div>
                        
                        @if($project->deskripsi)
                        <div class="md:col-span-2">
                            <p class="text-xs font-medium text-gray-500 mb-1">{{ __('projects.description') }}</p>
                            <p class="text-sm text-gray-700">{{ $project->deskripsi }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Activities PGB --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        {{ __('projects.pgb') }} ({{ $activitiesPGB->count() }})
                    </h3>
                    
                    @if($activitiesPGB->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.no') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.activity_name') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.date') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.status') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.person_in_charge') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activitiesPGB->values() as $activity)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $activity->nama_aktivitas }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                                {{ $activity->tanggal_mulai->format('d M Y') }}
                                                @if($activity->tanggal_selesai)
                                                    <br><small>{{ __('activities.until') }} {{ $activity->tanggal_selesai->format('d M Y') }}</small>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2">
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
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $activity->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">{{ __('projects.no_activities_pgb') }}</p>
                    @endif
                </div>
            </div>

            {{-- Activities PKJ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                        {{ __('projects.pkj') }} ({{ $activitiesPKJ->count() }})
                    </h3>
                    
                    @if($activitiesPKJ->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.no') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.activity_name') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.date') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.status') }}</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.person_in_charge') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activitiesPKJ->values() as $activity)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $activity->nama_aktivitas }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                                {{ $activity->tanggal_mulai->format('d M Y') }}
                                                @if($activity->tanggal_selesai)
                                                    <br><small>{{ __('activities.until') }} {{ $activity->tanggal_selesai->format('d M Y') }}</small>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2">
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
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $activity->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">{{ __('projects.no_activities_pkj') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>