<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('activities.add_new_activity') }}
            </h2>
            <a href="{{ $selectedProjectId ? route('projects.show', $selectedProjectId) : route('activities.my-activities') }}" 
               class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('activities.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['label' => __('sidebar.dashboard'), 'url' => route('dashboard')],
                ['label' => __('activities.page_title'), 'url' => route('activities.my-activities')],
                ['label' => __('activities.add_new_activity')]
            ]" />
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data" data-loading="true">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Project -->
                            <div class="md:col-span-2">
                                <label for="project_id" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.project_required') }} <span class="text-red-500">*</span>
                                </label>
                                
                                @if($selectedProjectId)
                                    {{-- Pre-selected & Disabled --}}
                                    <div class="relative">
                                        <input type="text" 
                                            value="{{ $projects->firstWhere('id', $selectedProjectId)->nama_project ?? __('activities.not_determined') }} ({{ $projects->firstWhere('id', $selectedProjectId)->pemilikProject->nama_divisi ?? '-' }})"
                                            disabled
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed shadow-sm">
                                        <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                                        
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('activities.project_locked') }}
                                    </p>
                                @else
                                    {{-- Select2 Searchable Dropdown --}}
                                    <select name="project_id" id="project_id" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('project_id') border-red-500 @enderror select2-project">
                                        <option value="" disabled selected hidden>{{ __('activities.select_project') }}</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" 
                                                data-status="{{ $project->status }}"
                                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->nama_project }} - {{ $project->pemilikProject->nama_divisi }} ({{ $project->status }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('activities.type_to_search') }}
                                    </p>
                                @endif
                                
                                @error('project_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Aktivitas -->
                            <div class="md:col-span-2">
                                <label for="nama_aktivitas" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.activity_name_label') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_aktivitas" id="nama_aktivitas" 
                                    value="{{ old('nama_aktivitas') }}"
                                    placeholder="{{ __('activities.enter_activity_name') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('nama_aktivitas') border-red-500 @enderror">
                                @error('nama_aktivitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jenis Kegiatan (Dropdown + Custom Input) --}}
                            <div>
                                <label for="jenis_kegiatan" class="block text-sm font-medium text-gray-700">
                                    {{ __('activities.activity_type') }} <span class="text-red-500">*</span>
                                </label>
                                
                                <select name="jenis_kegiatan_select" 
                                        id="jenis_kegiatan_select" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('jenis_kegiatan') border-red-500 @enderror">
                                    <option value="" disabled selected hidden>{{ __('activities.select_activity_type') }}</option>
                                    <option value="Meeting" {{ old('jenis_kegiatan') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="Coding" {{ old('jenis_kegiatan') == 'Coding' ? 'selected' : '' }}>Coding</option>
                                    <option value="Dokumentasi" {{ old('jenis_kegiatan') == 'Dokumentasi' ? 'selected' : '' }}>Dokumentasi</option>
                                    <option value="Support" {{ old('jenis_kegiatan') == 'Support' ? 'selected' : '' }}>Support</option>
                                    <option value="Lainnya" {{ old('jenis_kegiatan') != '' && !in_array(old('jenis_kegiatan'), ['Meeting', 'Coding', 'Dokumentasi', 'Support']) ? 'selected' : '' }}>
                                        {{ __('activities.others') }}
                                    </option>
                                </select>
                                
                                {{-- Input manual jika pilih "Lainnya" --}}
                                <input type="text" 
                                    name="jenis_kegiatan_others" 
                                    id="jenis_kegiatan_others" 
                                    value="{{ old('jenis_kegiatan') != '' && !in_array(old('jenis_kegiatan'), ['Meeting', 'Coding', 'Dokumentasi', 'Support']) ? old('jenis_kegiatan') : '' }}"
                                    placeholder="{{ __('activities.enter_other_type') }}"
                                    style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132]">
                                
                                {{-- Hidden input untuk submit --}}
                                <input type="hidden" name="jenis_kegiatan" id="jenis_kegiatan" value="{{ old('jenis_kegiatan') }}">
                                
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('status') border-red-500 @enderror">
                                    <option value="" disabled selected hidden>{{ __('activities.select_status') }}</option>
                                    <option value="Progress" {{ old('status') == 'Progress' ? 'selected' : '' }}>{{ __('activities.progress') }}</option>
                                    <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>{{ __('activities.pending') }}</option>
                                    <option value="Done" {{ old('status') == 'Done' ? 'selected' : '' }}>{{ __('activities.done') }}</option>
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
                                    value="{{ old('tanggal_mulai') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('tanggal_mulai') border-red-500 @enderror">
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
                                    value="{{ old('tanggal_selesai') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('tanggal_selesai') border-red-500 @enderror">
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lampiran (File OR Link) -->
                            <div class="md:col-span-2" x-data="{ lampiranType: '{{ old('lampiran_type', 'file') }}' }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('activities.attachment') }}
                                </label>
                                
                                <!-- Tab Buttons -->
                                <div class="flex border-b border-gray-200 mb-4">
                                    <button type="button" 
                                            @click="lampiranType = 'file'"
                                            :class="lampiranType === 'file' ? 'border-[#0F5132] text-[#0F5132]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                            class="py-2 px-4 border-b-2 font-medium text-sm transition">
                                        {{ __('activities.upload_file') }}
                                    </button>
                                    <button type="button" 
                                            @click="lampiranType = 'link'"
                                            :class="lampiranType === 'link' ? 'border-[#0F5132] text-[#0F5132]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                            class="py-2 px-4 border-b-2 font-medium text-sm transition">
                                        {{ __('activities.link_url') }}
                                    </button>
                                </div>

                                <!-- Hidden input to store type -->
                                <input type="hidden" name="lampiran_type" :value="lampiranType">

                                <!-- File Upload Tab -->
                                <div x-show="lampiranType === 'file'" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0">
                                    
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#0F5132] transition">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <div class="mt-4">
                                            <label for="lampiran" class="cursor-pointer">
                                                <span class="mt-2 block text-sm font-medium text-gray-900">
                                                    {{ __('activities.click_to_upload') }}
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

                                <!-- Link URL Tab - ✅ BEBAS TANPA BATASAN -->
                                <div x-show="lampiranType === 'link'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0">
                                    <input type="url" name="lampiran_link" id="lampiran_link" 
                                        value="{{ old('lampiran_link') }}"
                                        placeholder="https://example.com/document.pdf"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0F5132] focus:ring-[#0F5132] @error('lampiran_link') border-red-500 @enderror">
                                    @error('lampiran_link')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('activities.enter_any_valid_url') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a href="{{ $selectedProjectId ? route('projects.show', $selectedProjectId) : route('activities.my-activities') }}" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                {{ __('activities.cancel') }}
                            </a>
                            <x-loading-button color="green">
                                {{ __('activities.save') }}
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Select2 with Tags (taruh di @push('scripts')) --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== EXISTING CODE - JANGAN HAPUS =====
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

        updateJenisKegiatan();
    });

    // ===== ✅ NEW CODE - FILE PREVIEW =====
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
    </script>
    @endpush
</x-app-layout>