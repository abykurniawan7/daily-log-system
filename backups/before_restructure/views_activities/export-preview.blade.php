<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Preview Export Aktivitas
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Sticky Action Buttons - Positioned Between Header and Content --}}
            <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-6 py-4 mb-6 shadow-sm sm:rounded-lg">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <a href="{{ route('activities.index', request()->query()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>

                    @if($activities->count() > 0)
                        <a href="{{ route('activities.export-pdf', request()->query()) }}" 
                           class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF ({{ $activities->count() }} Aktivitas)
                        </a>
                    @else
                        <button disabled 
                                class="inline-flex items-center px-6 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Tidak Ada Data
                        </button>
                    @endif
                </div>
            </div>

            {{-- Export Summary - Blue Gradient Card --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">Laporan Aktivitas</h3>
                    <p class="text-sm opacity-90 mb-4">Preview data yang akan diekspor ke PDF</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">Total Aktivitas</p>
                            <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">Progress</p>
                            <p class="text-3xl font-bold">{{ $stats['progress'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">Pending</p>
                            <p class="text-3xl font-bold">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                            <p class="text-sm opacity-90 mb-1">Done</p>
                            <p class="text-3xl font-bold">{{ $stats['done'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter yang Diterapkan --}}
            @if(!empty($filterInfo))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">🔍 Filter yang Diterapkan:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($filterInfo as $key => $value)
                            <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                                <span class="text-xs font-medium text-gray-600">
                                    @switch($key)
                                        @case('search')
                                            🔍 Pencarian:
                                            @break
                                        @case('status')
                                            📊 Status:
                                            @break
                                        @case('project')
                                            📁 Project:
                                            @break
                                        @case('quick_filter')
                                            📅 Periode:
                                            @break
                                        @case('date_range')
                                            📅 Rentang:
                                            @break
                                        @case('date_from')
                                            📅 Dari:
                                            @break
                                        @case('date_to')
                                            📅 Sampai:
                                            @break
                                        @case('bagian')
                                            🏢 Bagian:
                                            @break
                                    @endswitch
                                </span>
                                <span class="text-xs font-semibold text-blue-800">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    ℹ️ <strong>Tidak ada filter diterapkan.</strong> Export akan mencakup semua aktivitas yang Anda miliki akses.
                </p>
            </div>
            @endif

            {{-- Preview Data Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">📋 Data Aktivitas ({{ $activities->count() }})</h3>
                    
                    @if($activities->count() > 0)
                        <div class="overflow-x-auto">
                            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PIC</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activities as $index => $activity)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="font-medium text-gray-900">{{ $activity->nama_aktivitas }}</div>
                                                <div class="text-xs text-gray-500">{{ $activity->jenis_kegiatan }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="text-gray-900">{{ $activity->project->nama_project }}</div>
                                                <div class="text-xs text-gray-500">{{ $activity->project->pemilikProject->nama_divisi }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->user->name }}</td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $statusColors = [
                                                        'Progress' => 'bg-blue-100 text-blue-800',
                                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                                        'Done' => 'bg-green-100 text-green-800'
                                                    ];
                                                @endphp
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$activity->status] }}">
                                                    {{ $activity->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $activity->tanggal_mulai->format('d M Y') }}
                                                @if($activity->tanggal_selesai)
                                                    <br><span class="text-xs text-gray-500">s/d {{ $activity->tanggal_selesai->format('d M Y') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        {{-- Warning jika terlalu banyak data --}}
                        @if($activities->count() > 100)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">
                                    ⚠️ <strong>Perhatian:</strong> Export akan menghasilkan file besar ({{ $activities->count() }} aktivitas). 
                                    Pertimbangkan untuk menambahkan filter untuk memperkecil data.
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm font-medium">Tidak ada data untuk di-export</p>
                            <p class="text-xs">Coba sesuaikan filter Anda</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>