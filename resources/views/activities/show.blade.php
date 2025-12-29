<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('activities.activity_detail') }}
            </h2>
            
            {{-- ✅ FIXED: Dynamic Back Button --}}
            <a href="{{ url()->previous() }}" 
               class="inline-flex items-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('activities.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Info Aktivitas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">{{ __('activities.activity_info') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.activity_name_label') }}</p>
                            <p class="text-base font-semibold">{{ $activity->nama_aktivitas }}</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.related_project') }}</p>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('projects.show', $activity->project) }}" 
                                   class="text-base font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $activity->project->nama_project }}
                                </a>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-medium">{{ __('activities.division') }}:</span> {{ $activity->project->pemilikProject->nama_divisi }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.activity_type') }}</p>
                            <span class="inline-flex items-center mt-1 px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ $activity->jenis_kegiatan }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.status') }}</p>
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
                            <span class="mt-1 px-3 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$activity->status] }}">
                                {{ $statusLabels[$activity->status] }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.activity_pic') }}</p>
                            <div class="flex items-center mt-1">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-base font-medium">{{ $activity->user->name }}</span>
                                {{-- ✅ FIX: Handle Kadiv (no bagian) --}}
                                @if($activity->user->role === 'supervisi')
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full font-semibold">Supervisi</span>
                                @else
                                    <span class="ml-2 text-xs text-gray-500">({{ $activity->user->bagian }})</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-2">{{ __('activities.project_pic') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($activity->project->pics as $pic)
                                    @php
                                        // Determine badge color
                                        if ($pic->role === 'supervisi') {
                                            $badgeColor = 'bg-red-100 text-red-800';
                                        } elseif ($pic->bagian === 'PGB') {
                                            $badgeColor = 'bg-green-100 text-green-800';
                                        } elseif ($pic->bagian === 'PKJ') {
                                            $badgeColor = 'bg-purple-100 text-purple-800';
                                        } else {
                                            $badgeColor = 'bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium {{ $badgeColor }}">
                                        <span class="w-6 h-6 rounded-full bg-white/40 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($pic->name, 0, 1)) }}
                                        </span>
                                        <span>{{ $pic->name }}</span>
                                        
                                        {{-- Role badge --}}
                                        @if($pic->role === 'supervisi')
                                            <span class="text-xs bg-white/40 px-1.5 py-0.5 rounded">Pengawas</span>
                                        @elseif(in_array($pic->role, ['kabag_pgb', 'perizinan']))
                                            <span class="text-xs bg-white/40 px-1.5 py-0.5 rounded">Kabag</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.start_date') }}</p>
                            <div class="flex items-center mt-1">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-base">{{ $activity->tanggal_mulai->format('d F Y') }}</span>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.end_date') }}</p>
                            <div class="flex items-center mt-1">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-base">
                                    @if($activity->tanggal_selesai)
                                        {{ $activity->tanggal_selesai->format('d F Y') }}
                                    @else
                                        <span class="text-gray-400 italic">{{ __('activities.not_determined') }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-2">{{ __('activities.description') }}</p>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-base whitespace-pre-wrap">{{ $activity->deskripsi ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <!-- Lampiran -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('activities.attachment') }}</label>
                            @if($activity->lampiran)
                            <div class="flex items-center gap-2">
                                <!-- Preview Button -->
                                <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    LIHAT FILE
                                </a>
                                
                                <!-- Download Button -->
                                <a href="{{ route('activities.download-attachment', $activity) }}" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    DOWNLOAD
                                </a>
                            </div>
                            
                            <!-- Filename -->
                            <p class="text-xs text-gray-500 mt-2">
                                📎 {{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}
                            </p>
                            @elseif($activity->lampiran_link)
                                <a href="{{ $activity->lampiran_link }}" 
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    {{ __('activities.open_external_link') }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($activity->lampiran_link, 80) }}</p>
                            @else
                                <p class="text-gray-500 text-sm">{{ __('activities.no_attachment') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Action Buttons dengan Authorization --}}
                    @php
                        $user = auth()->user();
                        // ✅ PERBAIKAN: Supervisi BISA edit aktivitas yang DIA BUAT SENDIRI
                        if ($user->role === 'supervisi') {
                            $canEditActivity = ($user->id === $activity->user_id);
                        } else {
                            // Non-supervisi: Hanya bisa edit aktivitas sendiri
                            $canEditActivity = ($user->id === $activity->user_id);
                        }
                    @endphp

                    @if($canEditActivity)
                        {{-- Tombol Edit/Hapus untuk Pemilik Aktivitas --}}
                        <div class="mt-6 pt-4 border-t flex items-center space-x-2">
                            <span class="text-sm text-gray-500 mr-2">{{ __('activities.actions') }}:</span>
                            
                            <a href="{{ route('activities.edit', $activity) }}" 
                            class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ __('activities.edit') }}
                            </a>
                            
                            {{-- ✅ PERBAIKAN: Supervisi TIDAK BISA delete (sesuai FASE 4) --}}
                            @if($user->role !== 'supervisi')
                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('activities.confirm_delete_redirect') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    {{ __('activities.delete') }}
                                </button>
                            </form>
                            @else
                            {{-- Info: Supervisi tidak bisa delete --}}
                            <span class="inline-flex items-center px-3 py-1.5 bg-gray-200 text-gray-500 text-sm font-medium rounded cursor-not-allowed" 
                                title="Supervisi tidak dapat menghapus aktivitas">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ __('activities.delete') }}
                            </span>
                            @endif
                        </div>
                    @elseif($user->role === 'supervisi' && $user->id !== $activity->user_id)
                        {{-- Info untuk Supervisi: Mode Monitoring Only --}}
                        <div class="mt-6 pt-4 border-t">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span class="text-sm text-blue-800">
                                    <strong>{{ __('activities.monitoring_mode') }}</strong>
                                </span>
                            </div>
                        </div>
                    @else
                        {{-- Info untuk User lain: Tidak punya akses edit --}}
                        <div class="mt-6 pt-4 border-t">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-sm text-gray-600">
                                    {{ __('activities.no_edit_access') }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>