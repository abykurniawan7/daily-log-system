<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('activities.activity_detail') }}
            </h2>
            
            {{-- Kembali ke Detail Project atau My Activities --}}
            @if($activity->project_id)
                <a href="{{ route('projects.show', $activity->project) }}" 
                   class="inline-flex items-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('activities.back_to_project') }}
                </a>
            @else
                <a href="{{ route('activities.my-activities') }}" 
                   class="inline-flex items-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke My Activities
                </a>
            @endif
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

                    {{-- Action Buttons dengan Authorization --}}
                    @php
                        $user = auth()->user();
                        $canEditActivity = $user->role !== 'supervisi' && $user->id === $activity->user_id;
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.activity_name_label') }}</p>
                            <p class="text-base font-semibold">{{ $activity->nama_aktivitas }}</p>
                        </div>
                        
                        {{-- PROJECT INFO - CONDITIONAL --}}
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.related_project') }}</p>
                            
                            @if($activity->project_id)
                                {{-- Project Real --}}
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('projects.show', $activity->project) }}" 
                                       class="text-base font-semibold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center">
                                        <svg class="w-5 h-5 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                        {{ $activity->project->nama_project }}
                                    </a>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span class="font-medium">{{ __('activities.division') }}:</span> {{ $activity->project->pemilikProject->nama_divisi }}
                                </p>
                            @else
                                {{-- Placeholder Project --}}
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-2">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-base font-semibold text-yellow-800">
                                                {{ $activity->placeholder_project_name ?? 'Tanpa Project' }}
                                            </p>
                                            
                                            {{-- ✅ CONDITIONAL: Cek apakah user adalah owner aktivitas --}}
                                            @if($canEditActivity)
                                                {{-- Owner aktivitas: Tampilkan link assign --}}
                                                <p class="text-sm text-yellow-700 mt-1 flex items-start">
                                                    <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>
                                                        Aktivitas ini belum di-assign ke project. 
                                                        <a href="{{ route('activities.edit', $activity) }}" 
                                                        class="inline-flex items-center underline font-medium hover:text-yellow-900">
                                                            Klik di sini untuk assign
                                                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </p>
                                            @else
                                                {{-- Supervisi atau user lain: Tampilkan info saja --}}
                                                <div class="mt-2">
                                                    @if($user->role === 'supervisi')
                                                        {{-- Info untuk Supervisi --}}
                                                        <p class="text-sm text-yellow-700 flex items-start">
                                                            <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span>
                                                                Menunggu assignment ke project oleh 
                                                                <strong>{{ $activity->user->name }}</strong> ({{ $activity->user->bagian }})
                                                            </span>
                                                        </p>
                                                    @else
                                                        {{-- Info untuk User Lain --}}
                                                        <p class="text-sm text-yellow-700 flex items-start">
                                                            <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                            <span>
                                                                Aktivitas ini dibuat oleh <strong>{{ $activity->user->name }}</strong> 
                                                                dan belum di-assign ke project.
                                                            </span>
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                                <span class="ml-2 text-xs text-gray-500">({{ $activity->user->bagian }})</span>
                            </div>
                        </div>
                        
                        {{-- PROJECT PIC - CONDITIONAL --}}
                        @if($activity->project_id)
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('activities.project_pic') }}</p>
                            <div class="flex items-center mt-1">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-base">{{ $activity->project->picProyek->name }}</span>
                            </div>
                        </div>
                        @endif
                        
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
                                <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                   target="_blank"
                                   download
                                   class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    📎 {{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}
                                </a>
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
                        </div>
                    @elseif($user->role === 'supervisi')
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