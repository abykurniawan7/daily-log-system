<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('projects.preview_export') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Sticky Action Buttons - Positioned Between Header and Content --}}
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-6 py-4 mb-6 shadow-sm sm:rounded-lg">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <a href="{{ route('projects.index', request()->query()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('projects.back') }}
                    </a>

                    @if($projects->count() > 0)
                        <a href="{{ route('projects.export-pdf', request()->query()) }}" 
                           class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('projects.download_pdf') }} ({{ $projects->count() }} {{ __('projects.projects_text') }})
                        </a>
                    @else
                        <button disabled 
                                class="inline-flex items-center px-6 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('projects.no_data') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Export Summary - Blue Gradient Card --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">{{ __('projects.project_report') }}</h3>
                    <p class="text-sm opacity-90 mb-4">{{ __('projects.preview_export_data') }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">{{ __('projects.total_projects') }}</p>
                            <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">{{ __('projects.progress') }}</p>
                            <p class="text-3xl font-bold">{{ $stats['progress'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">{{ __('projects.pending') }}</p>
                            <p class="text-3xl font-bold">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">{{ __('projects.done') }}</p>
                            <p class="text-3xl font-bold">{{ $stats['done'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter yang Diterapkan --}}
            @if(!empty($filterInfo))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('projects.applied_filters') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($filterInfo as $key => $value)
                            <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                                <span class="text-xs font-medium text-gray-600">
                                    @switch($key)
                                        @case('search')
                                            {{ __('projects.filter_search_key') }}
                                            @break
                                        @case('status')
                                            {{ __('projects.filter_status_key') }}
                                            @break
                                        @case('division')
                                            {{ __('projects.filter_division_key') }}
                                            @break
                                        @case('urgency')
                                            {{ __('projects.filter_urgency_key') }}
                                            @break
                                        @case('quick_filter')
                                            {{ __('projects.filter_period_key') }}
                                            @break
                                        @case('date_range')
                                            {{ __('projects.filter_range_key') }}
                                            @break
                                        @case('date_from')
                                            {{ __('projects.filter_from_key') }}
                                            @break
                                        @case('date_to')
                                            {{ __('projects.filter_to_key') }}
                                            @break
                                    @endswitch
                                </span>
                                <span class="text-xs font-semibold text-blue-800">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Preview Data Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('projects.data_projects') }} ({{ $projects->count() }})</h3>
                    
                    @if($projects->count() > 0)
                        <div class="overflow-x-auto">
                            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.no') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.project_name') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.owner') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.pic') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.date') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.status') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('projects.urgency') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($projects as $index => $project)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="font-medium text-gray-900">{{ $project->nama_project }}</div>
                                                    <div class="text-xs text-gray-500">{{ Str::limit($project->sifat_project, 30) }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ $project->pemilikProject->nama_divisi }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ $project->picProyek->name }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($project->tanggal_inisiasi)->format('d M Y') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $statusColors = [
                                                            'Progress' => 'bg-blue-100 text-blue-800',
                                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                                            'Done' => 'bg-green-100 text-green-800'
                                                        ];
                                                        
                                                        // Translate status
                                                        $statusLabels = [
                                                            'Progress' => __('projects.in_progress'),
                                                            'Pending' => __('projects.pending_status'),
                                                            'Done' => __('projects.completed')
                                                        ];
                                                    @endphp
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$project->status] }}">
                                                        {{ $statusLabels[$project->status] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $urgencyColors = [
                                                            'Low' => 'bg-gray-100 text-gray-800',
                                                            'Medium' => 'bg-yellow-100 text-yellow-800',
                                                            'High' => 'bg-orange-100 text-orange-800',
                                                            'Very High' => 'bg-red-100 text-red-800'
                                                        ];
                                                        
                                                        // Translate urgency
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
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Warning jika terlalu banyak data --}}
                        @if($projects->count() > 50)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">
                                    {!! __('projects.warning_large_export', ['count' => $projects->count()]) !!}
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm font-medium">{{ __('projects.no_export_data') }}</p>
                            <p class="text-xs">{{ __('projects.adjust_filter') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>