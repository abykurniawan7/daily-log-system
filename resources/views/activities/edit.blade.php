<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('activities.edit_activity') }}
            </h2>
            @if($activity->project_id)
                <a href="{{ route('projects.show', $activity->project_id) }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← {{ __('activities.back_to_detail_project') }}
                </a>
            @else
                <a href="{{ route('activities.my-activities') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Kembali ke My Activities
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="array_filter([
                ['label' => __('sidebar.dashboard'), 'url' => route('dashboard')],
                $activity->project_id ? ['label' => __('sidebar.projects'), 'url' => route('projects.index')] : null,
                $activity->project_id ? ['label' => $activity->project->nama_project, 'url' => route('projects.show', $activity->project_id)] : null,
                !$activity->project_id ? ['label' => 'My Activities', 'url' => route('activities.my-activities')] : null,
                ['label' => __('activities.edit_activity')]
            ])" />
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('activities.update', $activity) }}" method="POST" enctype="multipart/form-data" data-loading="true">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <<!-- Project -->
                            <div class="md:col-span-2">
                                <label for="project_id" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.project_required') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="project_id" id="project_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('project_id') border-red-500 @enderror">
                                    <option value="" disabled hidden>{{ __('activities.select_project') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" 
                                            {{ (old('project_id', $activity->project_id) == $project->id) ? 'selected' : '' }}>
                                            {{ $project->nama_project }} ({{ $project->pemilikProject->nama_divisi }})
                                        </option>
                                    @endforeach
                                    
                                    {{-- ✅ OPSI: "No Project" - tersedia untuk semua role kecuali supervisi --}}
                                    @if(auth()->user()->role !== 'supervisi')
                                        <option value="no_project" 
                                            {{ (old('project_id') === 'no_project' || (is_null(old('project_id')) && is_null($activity->project_id))) ? 'selected' : '' }}>
                                            ✨ {{ __('activities.no_project_yet') }}
                                        </option>
                                    @endif
                                </select>
                                
                                {{-- ✅ Info placeholder jika sedang di "no project" --}}
                                @if(is_null($activity->project_id) && $activity->placeholder_project_name)
                                    <p class="mt-1 text-xs text-blue-600 bg-blue-50 border border-blue-200 rounded px-3 py-2 flex items-start">
                                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>
                                            📂 Placeholder saat ini: <strong>{{ $activity->placeholder_project_name }}</strong>
                                        </span>
                                    </p>
                                @endif
                                
                                @error('project_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- ✅ FIELD: Placeholder Project Name --}}
                            <div id="placeholder-project-field" class="md:col-span-2" 
                                style="display: {{ (old('project_id') === 'no_project' || (is_null(old('project_id')) && is_null($activity->project_id))) ? 'block' : 'none' }};">
                                <label for="placeholder_project_name" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.placeholder_project_label') }} <span class="text-red-500">*</span>
                                </label>
                                
                                <input type="text" 
                                    name="placeholder_project_name" 
                                    id="placeholder_project_name"
                                    value="{{ old('placeholder_project_name', $activity->placeholder_project_name) }}"
                                    placeholder="{{ __('activities.placeholder_project_placeholder') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('placeholder_project_name') border-red-500 @enderror">
                                
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('activities.placeholder_project_help') }}
                                </p>
                                
                                @error('placeholder_project_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Aktivitas -->
                            <div class="md:col-span-2">
                                <label for="nama_aktivitas" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.activity_name_label') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_aktivitas" id="nama_aktivitas" 
                                    value="{{ old('nama_aktivitas', $activity->nama_aktivitas) }}"
                                    placeholder="{{ __('activities.enter_activity_name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nama_aktivitas') border-red-500 @enderror">
                                @error('nama_aktivitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jenis Kegiatan (Dropdown + Custom Input) --}}
                            <div>
                                <label for="jenis_kegiatan" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.activity_type') }} <span class="text-red-500">*</span>
                                </label>
                                
                                @php
                                    $currentJenis = old('jenis_kegiatan', $activity->jenis_kegiatan);
                                    $isCustom = !in_array($currentJenis, ['Meeting', 'Coding', 'Dokumentasi', 'Support']);
                                @endphp
                                
                                <select name="jenis_kegiatan_select" 
                                        id="jenis_kegiatan_select" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('jenis_kegiatan') border-red-500 @enderror">
                                    <option value="" disabled {{ !$currentJenis ? 'selected' : '' }} hidden>{{ __('activities.select_activity_type') }}</option>
                                    <option value="Meeting" {{ $currentJenis == 'Meeting' ? 'selected' : '' }}>
                                        {{ __('activities.activity_type_meeting') }}
                                    </option>
                                    <option value="Coding" {{ $currentJenis == 'Coding' ? 'selected' : '' }}>
                                        {{ __('activities.activity_type_coding') }}
                                    </option>
                                    <option value="Dokumentasi" {{ $currentJenis == 'Dokumentasi' ? 'selected' : '' }}>
                                        {{ __('activities.activity_type_documentation') }}
                                    </option>
                                    <option value="Support" {{ $currentJenis == 'Support' ? 'selected' : '' }}>
                                        {{ __('activities.activity_type_support') }}
                                    </option>
                                    <option value="Lainnya" {{ $isCustom ? 'selected' : '' }}>
                                        {{ __('activities.others') }}
                                    </option>
                                </select>
                                
                                {{-- Input manual jika pilih "Lainnya" --}}
                                <input type="text" 
                                    name="jenis_kegiatan_others" 
                                    id="jenis_kegiatan_others" 
                                    value="{{ $isCustom ? $currentJenis : '' }}"
                                    placeholder="{{ __('activities.enter_other_type') }}"
                                    style="display: {{ $isCustom ? 'block' : 'none' }};"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                
                                {{-- Hidden input untuk submit --}}
                                <input type="hidden" name="jenis_kegiatan" id="jenis_kegiatan" value="{{ $currentJenis }}">
                                
                                @error('jenis_kegiatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.status_required') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                    <option value="" disabled hidden>{{ __('activities.select_status') }}</option>
                                    <option value="Progress" {{ old('status', $activity->status) == 'Progress' ? 'selected' : '' }}>{{ __('activities.progress') }}</option>
                                    <option value="Pending" {{ old('status', $activity->status) == 'Pending' ? 'selected' : '' }}>{{ __('activities.pending') }}</option>
                                    <option value="Done" {{ old('status', $activity->status) == 'Done' ? 'selected' : '' }}>{{ __('activities.done') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.start_date') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" 
                                    value="{{ old('tanggal_mulai', $activity->tanggal_mulai->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_mulai') border-red-500 @enderror">
                                @error('tanggal_mulai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.end_date') }}
                                </label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" 
                                    value="{{ old('tanggal_selesai', $activity->tanggal_selesai?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_selesai') border-red-500 @enderror">
                                @error('tanggal_selesai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">{{ __('activities.end_date_optional') }}</p>
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.description') }}
                                </label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" 
                                    placeholder="{{ __('activities.enter_description') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $activity->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lampiran (File OR Link) - FULL IMPLEMENTATION -->
                            <div class="md:col-span-2" x-data="{ 
                                lampiranType: '{{ old('lampiran_type', $activity->lampiran_link ? 'link' : ($activity->lampiran ? 'file' : 'file')) }}' 
                            }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('activities.attachment') }}
                                </label>
                                
                                <!-- ✅ Current Attachment Info (jika ada) -->
                                @if($activity->lampiran || $activity->lampiran_link)
                                    <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-md">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-blue-900 mb-2">{{ __('activities.current_attachment') }}</p>
                                                
                                                @if($activity->lampiran)
                                                    <div class="flex items-center justify-between bg-white rounded-md p-2 mb-2">
                                                        <a href="{{ asset('storage/' . $activity->lampiran) }}" 
                                                        target="_blank"
                                                        download
                                                        class="flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                            </svg>
                                                            <span class="truncate">📎 {{ basename(str_replace('lampiran/', '', $activity->lampiran)) }}</span>
                                                        </a>
                                                        <span class="ml-2 text-xs text-gray-500 whitespace-nowrap">
                                                            {{ number_format(Storage::disk('public')->size($activity->lampiran) / 1024, 1) }} KB
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                @if($activity->lampiran_link)
                                                    <div class="flex items-center justify-between bg-white rounded-md p-2">
                                                        <a href="{{ $activity->lampiran_link }}" 
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium flex-1 min-w-0">
                                                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                            </svg>
                                                            <span class="truncate">🔗 {{ Str::limit($activity->lampiran_link, 50) }}</span>
                                                        </a>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1 ml-1 truncate" title="{{ $activity->lampiran_link }}">
                                                        {{ $activity->lampiran_link }}
                                                    </p>
                                                @endif
                                                
                                                <p class="text-xs text-blue-700 mt-2 italic">
                                                    {{ __('activities.upload_new_to_replace') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- ✅ Tab Buttons -->
                                <div class="flex border-b border-gray-200 mb-4">
                                    <button type="button" 
                                            @click="lampiranType = 'file'"
                                            :class="lampiranType === 'file' ? 'border-[#0F5132] text-[#0F5132] bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                            class="py-2.5 px-5 border-b-2 font-medium text-sm transition-all duration-150">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ __('activities.upload_file') }}
                                        </span>
                                    </button>
                                    <button type="button" 
                                            @click="lampiranType = 'link'"
                                            :class="lampiranType === 'link' ? 'border-[#0F5132] text-[#0F5132] bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                            class="py-2.5 px-5 border-b-2 font-medium text-sm transition-all duration-150">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                            {{ __('activities.link_url') }}
                                        </span>
                                    </button>
                                </div>

                                <!-- Hidden input to store type -->
                                <input type="hidden" name="lampiran_type" :value="lampiranType">

                                <!-- ✅ File Upload Tab -->
                                <div x-show="lampiranType === 'file'" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2">
                                    
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0F5132] transition">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <div class="mt-4">
                                            <label for="lampiran" class="cursor-pointer">
                                                <span class="mt-2 block text-sm font-medium text-gray-900">
                                                    {{ $activity->lampiran ? __('activities.upload_new_file') : __('activities.click_to_upload') }}
                                                </span>
                                                <input type="file" name="lampiran" id="lampiran" 
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                                    class="sr-only"
                                                    onchange="displayFileName(this)">
                                            </label>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ __('activities.supported_formats') }}
                                            </p>
                                        </div>
                                        
                                        <!-- ✅ File Preview Area -->
                                        <div id="file-preview" class="mt-4 hidden">
                                            <div class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div class="text-left">
                                                    <p id="file-name" class="text-sm font-medium text-gray-900"></p>
                                                    <p id="file-size" class="text-xs text-gray-500"></p>
                                                </div>
                                                <button type="button" onclick="clearFile()" class="ml-3 text-gray-400 hover:text-red-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @error('lampiran')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- ✅ Link URL Tab -->
                                <div x-show="lampiranType === 'link'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2">
                                    <div class="space-y-3">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                            </div>
                                            <input type="url" name="lampiran_link" id="lampiran_link" 
                                                value="{{ old('lampiran_link', $activity->lampiran_link) }}"
                                                placeholder="https://example.com/document.pdf"
                                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('lampiran_link') border-red-500 @enderror">
                                        </div>
                                        @error('lampiran_link')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <div class="bg-gray-50 rounded-md p-3">
                                            <p class="text-xs text-gray-600 flex items-start">
                                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>
                                                    {{ app()->getLocale() == 'id' 
                                                        ? 'Masukkan URL yang valid (contoh: website, cloud storage, atau link lainnya)' 
                                                        : 'Enter any valid URL (example: website, cloud storage, or other links)' 
                                                    }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ✅ JavaScript untuk display filename -->
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const jenisSelect = document.getElementById('jenis_kegiatan_select');
                                const jenisOthers = document.getElementById('jenis_kegiatan_others');
                                const jenisHidden = document.getElementById('jenis_kegiatan');

                                function updateJenisKegiatan() {
                                    const selectedValue = jenisSelect.value;
                                    
                                    if (selectedValue === 'Lainnya') {
                                        jenisOthers.style.display = 'block';
                                        jenisOthers.required = true;
                                        jenisHidden.value = jenisOthers.value;
                                    } else {
                                        jenisOthers.style.display = 'none';
                                        jenisOthers.required = false;
                                        jenisHidden.value = selectedValue;
                                    }
                                }

                                jenisSelect.addEventListener('change', updateJenisKegiatan);
                                jenisOthers.addEventListener('input', function() {
                                    jenisHidden.value = this.value;
                                });

                                // Initialize on page load
                                updateJenisKegiatan();
                            });

                            function displayFileName(input) {
                                const filePreview = document.getElementById('file-preview');
                                const fileName = document.getElementById('file-name');
                                const fileSize = document.getElementById('file-size');
                                
                                if (input.files && input.files[0]) {
                                    const file = input.files[0];
                                    fileName.textContent = file.name;
                                    
                                    // Format file size
                                    const size = file.size;
                                    let sizeText;
                                    if (size < 1024) {
                                        sizeText = size + ' B';
                                    } else if (size < 1024 * 1024) {
                                        sizeText = (size / 1024).toFixed(1) + ' KB';
                                    } else {
                                        sizeText = (size / (1024 * 1024)).toFixed(1) + ' MB';
                                    }
                                    fileSize.textContent = sizeText;
                                    
                                    filePreview.classList.remove('hidden');
                                }
                            }

                            function clearFile() {
                                const input = document.getElementById('lampiran');
                                input.value = '';
                                document.getElementById('file-preview').classList.add('hidden');
                            }

                            // ✅ TAMBAHAN: Toggle placeholder field
                            const projectSelect = document.getElementById('project_id');
                            const placeholderField = document.getElementById('placeholder-project-field');
                            const placeholderInput = document.getElementById('placeholder_project_name');

                            function togglePlaceholderField() {
                                if (projectSelect && projectSelect.value === 'no_project') {
                                    placeholderField.style.display = 'block';
                                    placeholderInput.required = true;
                                } else if (projectSelect) {
                                    placeholderField.style.display = 'none';
                                    placeholderInput.required = false;
                                    // Jangan clear value, biar bisa diedit
                                }
                            }

                            if (projectSelect) {
                                // Initial check
                                togglePlaceholderField();
                                
                                // Listen perubahan dropdown
                                projectSelect.addEventListener('change', togglePlaceholderField);
                            }
                            </script>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex items-center justify-end gap-4">
                            @if($activity->project_id)
                                <a href="{{ route('projects.show', $activity->project_id) }}" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                    {{ __('activities.cancel') }}
                                </a>
                            @else
                                <a href="{{ route('activities.my-activities') }}" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                    {{ __('activities.cancel') }}
                                </a>
                            @endif
                            <x-loading-button color="blue">
                                {{ __('activities.update') }}
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>