<x-app-layout>
    <x-slot name="title">Timeline</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Timeline Aktivitas
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Tampilan kronologis aktivitas berdasarkan tanggal
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('activities.index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    View List
                </a>
                <a href="{{ route('activities.create') }}" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    + Tambah Aktivitas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Switch View untuk Perizinan --}}
            @if(auth()->user()->role === 'perizinan')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Pilih Bagian:</h3>
                        <div class="flex space-x-3">
                            <a href="{{ route('activities.timeline', ['view_bagian' => 'PKJ']) }}" 
                                class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition 
                                    {{ request('view_bagian', 'PKJ') === 'PKJ' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300' }}">
                                Aktivitas PKJ
                            </a>
                            <a href="{{ route('activities.timeline', ['view_bagian' => 'PGB']) }}" 
                                class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition 
                                    {{ request('view_bagian') === 'PGB' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300' }}">
                                Aktivitas PGB
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            {{-- Filter Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filter Timeline</h3>
                    
                    <form method="GET" action="{{ route('activities.timeline') }}">
                        @if(auth()->user()->role === 'perizinan' && request('view_bagian'))
                            <input type="hidden" name="view_bagian" value="{{ request('view_bagian') }}">
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Semua Status</option>
                                    <option value="Progress" {{ request('status') == 'Progress' ? 'selected' : '' }}>Progress</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>Done</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="date_from" id="date_from" 
                                    value="{{ request('date_from') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="date_to" id="date_to" 
                                    value="{{ request('date_to') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Terapkan Filter
                            </button>
                            <a href="{{ route('activities.timeline') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            @if($activities->count() > 0)
                <div class="space-y-6">
                    @foreach($activities as $date => $dateActivities)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                {{-- Date Header --}}
                                <div class="flex items-center mb-6">
                                    <div class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                        {{ \Carbon\Carbon::parse($date)->format('d') }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ $dateActivities->count() }} aktivitas</p>
                                    </div>
                                </div>
                                
                                {{-- Activities List --}}
                                <div class="relative border-l-2 border-gray-200 ml-5 space-y-6">
                                    @foreach($dateActivities as $activity)
                                        <div class="relative pl-8 pb-6 last:pb-0">
                                            {{-- Timeline Dot --}}
                                            <div class="absolute -left-2.5 top-0 w-5 h-5 rounded-full border-2 border-white
                                                {{ $activity->status === 'Done' ? 'bg-green-500' : 
                                                   ($activity->status === 'Progress' ? 'bg-blue-500' : 'bg-yellow-500') }}">
                                            </div>
                                            
                                            {{-- Activity Card --}}
                                            <div class="bg-gray-50 rounded-lg p-4 hover:shadow-md transition">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-base text-gray-900">
                                                            {{ $activity->nama_aktivitas }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            {{ $activity->jenis_kegiatan }}
                                                        </p>
                                                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                                                            <span>
                                                                <strong>Project:</strong> {{ $activity->project->nama_project }}
                                                            </span>
                                                            <span>
                                                                <strong>PIC:</strong> {{ $activity->user->name }}
                                                            </span>
                                                            @if($activity->tanggal_selesai)
                                                                <span>
                                                                    <strong>Selesai:</strong> {{ $activity->tanggal_selesai->format('d M Y') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($activity->deskripsi)
                                                            <p class="text-sm text-gray-600 mt-2">
                                                                {{ Str::limit($activity->deskripsi, 150) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        @php
                                                            $statusColors = [
                                                                'Progress' => 'bg-blue-100 text-blue-800',
                                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                                'Done' => 'bg-green-100 text-green-800'
                                                            ];
                                                        @endphp
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$activity->status] }}">
                                                            {{ $activity->status }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3 flex space-x-2">
                                                    <a href="{{ route('activities.show', $activity) }}" 
                                                        class="text-xs text-blue-600 hover:text-blue-800">
                                                        Detail →
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aktivitas</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                Tidak ada aktivitas yang sesuai dengan filter
                            @else
                                Belum ada aktivitas untuk ditampilkan
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>