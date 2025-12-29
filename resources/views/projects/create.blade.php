<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('projects.add_new_project') }}
            </h2>
            <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('projects.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => __('projects.page_title'), 'url' => route('projects.index')],
                ['label' => __('projects.add_project')]
            ]" />
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('projects.store') }}" method="POST" data-loading="true" id="projectForm">
                        @csrf

                        {{-- Tanggal Inisiasi --}}
                        <div class="mb-4">
                            <label for="tanggal_inisiasi" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.initiation_date') }} <span class="text-red-600">*</span>
                            </label>
                            <input type="date" name="tanggal_inisiasi" id="tanggal_inisiasi" value="{{ old('tanggal_inisiasi') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('tanggal_inisiasi') border-red-500 @enderror">
                            @error('tanggal_inisiasi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Target Implementasi (Date Picker + Auto Quartal) --}}
                        <div class="mb-4">
                            <label for="target_date" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.target_implementation') }} <span class="text-red-600">*</span>
                            </label>
                            <input type="date" 
                                   name="target_date" 
                                   id="target_date" 
                                   value="{{ old('target_date') }}" 
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('target_implementasi') border-red-500 @enderror">
                            
                            {{-- Display Quartal (Auto-generated) --}}
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold">{{ __('projects.quarter') }}:</span> 
                                    <span id="quartal_display" class="text-green-600 font-bold">-</span>
                                </p>
                            </div>
                            
                            {{-- Hidden input untuk submit quartal string --}}
                            <input type="hidden" name="target_implementasi" id="target_implementasi" value="{{ old('target_implementasi') }}">
                            
                            @error('target_implementasi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Project --}}
                        <div class="mb-4">
                            <label for="nama_project" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.project_name_label') }} <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="nama_project" id="nama_project" value="{{ old('nama_project') }}" required
                                placeholder="{{ __('projects.enter_project_name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('nama_project') border-red-500 @enderror">
                            @error('nama_project')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Urgensi --}}
                        <div class="mb-4">
                            <label for="urgensi" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.urgency_label') }} <span class="text-red-600">*</span>
                            </label>
                            <select name="urgensi" id="urgensi" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('urgensi') border-red-500 @enderror">
                                <option></option>
                                <option value="Low" {{ old('urgensi') == 'Low' ? 'selected' : '' }}>{{ __('projects.low') }}</option>
                                <option value="Medium" {{ old('urgensi') == 'Medium' ? 'selected' : '' }}>{{ __('projects.medium') }}</option>
                                <option value="High" {{ old('urgensi') == 'High' ? 'selected' : '' }}>{{ __('projects.high') }}</option>
                                <option value="Very High" {{ old('urgensi') == 'Very High' ? 'selected' : '' }}>{{ __('projects.very_high') }}</option>
                            </select>
                            @error('urgensi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Sifat Project (Dropdown + Others) --}}
                        <div class="mb-4">
                            <label for="sifat_project" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.project_nature') }} <span class="text-red-600">*</span>
                            </label>
                            <select name="sifat_project_select" 
                                    id="sifat_project_select" 
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('sifat_project') border-red-500 @enderror">
                                <option></option>
                                <option value="RBB" {{ old('sifat_project') == 'RBB' ? 'selected' : '' }}>{{ __('projects.rbb') }}</option>
                                <option value="Non RBB" {{ old('sifat_project') == 'Non RBB' ? 'selected' : '' }}>{{ __('projects.non_rbb') }}</option>
                                <option value="Regulator" {{ old('sifat_project') == 'Regulator' ? 'selected' : '' }}>{{ __('projects.regulator') }}</option>
                                <option value="Kedinasan" {{ old('sifat_project') == 'Kedinasan' ? 'selected' : '' }}>{{ __('projects.kedinasan') }}</option>
                                <option value="TL Audit" {{ old('sifat_project') == 'TL Audit' ? 'selected' : '' }}>{{ __('projects.tl_audit') }}</option>
                                <option value="Others" {{ old('sifat_project') != '' && !in_array(old('sifat_project'), ['RBB', 'Non RBB', 'Regulator', 'Kedinasan', 'TL Audit']) ? 'selected' : '' }}>{{ __('projects.others') }}</option>
                            </select>
                            
                            {{-- Input manual jika pilih "Others" --}}
                            <input type="text" 
                                   name="sifat_project_others" 
                                   id="sifat_project_others" 
                                   value="{{ old('sifat_project') != '' && !in_array(old('sifat_project'), ['RBB', 'Non RBB', 'Regulator', 'Kedinasan', 'TL Audit']) ? old('sifat_project') : '' }}"
                                   placeholder="{{ __('projects.enter_other_nature') }}"
                                   style="display: none;"
                                   class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                            
                            {{-- Hidden input untuk submit --}}
                            <input type="hidden" name="sifat_project" id="sifat_project" value="{{ old('sifat_project') }}">
                            
                            @error('sifat_project')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.project_description') }}
                            </label>
                            <textarea name="deskripsi" id="deskripsi" rows="4"
                                placeholder="{{ __('projects.enter_description') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Pemilik Project (Divisi) --}}
                        <div class="mb-4">
                            <label for="pemilik_project_id" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.project_owner') }} <span class="text-red-600">*</span>
                            </label>
                            <select name="pemilik_project_id" id="pemilik_project_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('pemilik_project_id') border-red-500 @enderror">
                                <option></option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('pemilik_project_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->nama_divisi }} ({{ $division->kode_divisi }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pemilik_project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ✅ PIC Proyek (Multiple Select) - WITH VISIBLE PLACEHOLDER --}}
                        <div class="mb-4">
                            <label for="pic_proyek_ids" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('projects.pic_project') }} <span class="text-red-600">*</span>
                            </label>
                            
                            @php
                                $currentUserRole = auth()->user()->role;
                            @endphp

                            {{-- ✅ SIMPLIFIED HINT - Small text only --}}
                            @if($currentUserRole === 'kabag_pgb')
                                <p class="text-xs text-gray-600 mb-2">
                                    {!! __('projects.pic_hint_kabag_pgb') !!}
                                </p>
                            @elseif($currentUserRole === 'perizinan')
                                <p class="text-xs text-gray-600 mb-2">
                                    {{ __('projects.pic_hint_perizinan') }}
                                </p>
                            @elseif($currentUserRole === 'supervisi')
                                <p class="text-xs text-gray-600 mb-2">
                                    {{ __('projects.pic_hint_supervisi') }}
                                </p>
                            @endif

                            {{-- ✅ Multi-select dropdown --}}
                            <div class="relative">
                                <select name="pic_proyek_ids[]" 
                                        id="pic_proyek_ids" 
                                        multiple 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('pic_proyek_ids') border-red-500 @enderror">

                                    @if(auth()->user()->role === 'supervisi')
                                        {{-- SUPERVISI --}}
                                        @php
                                            $supervisiUsers = $users->where('role', 'supervisi');
                                            $pgbUsers = $users->where('bagian', 'PGB')->sortBy('name');
                                            $pkjUsers = $users->where('bagian', 'PKJ')->sortBy('name');
                                        @endphp
                                        
                                        @if($supervisiUsers->count() > 0)
                                            <optgroup label="🔰 ═══ SUPERVISI ═══">
                                                @foreach($supervisiUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="SUPERVISI"
                                                            data-role="{{ $user->role }}"
                                                            {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                        {{ $user->name }}{{ $user->id === auth()->id() ? ' (Saya)' : '' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        @if($pgbUsers->count() > 0)
                                            <optgroup label="📁 ═══ PGB ({{ $pgbUsers->count() }} orang) ═══">
                                                @foreach($pgbUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="PGB"
                                                            data-role="{{ $user->role }}"
                                                            {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                        {{-- @if($user->role === 'kabag_pgb') (Kabag) ★@endif --}}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        @if($pkjUsers->count() > 0)
                                            <optgroup label="📋 ═══ PKJ ({{ $pkjUsers->count() }} orang) ═══">
                                                @foreach($pkjUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="PKJ"
                                                            data-role="{{ $user->role }}"
                                                            {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                        {{-- @if($user->role === 'perizinan') (Kabag PKJ)@endif --}}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                            
                                    @elseif(auth()->user()->role === 'kabag_pgb')
                                        {{-- KABAG PGB --}}
                                        @php
                                            $pgbUsers = $users->where('bagian', 'PGB')->sortBy('name');
                                            $pkjUsers = $users->where('bagian', 'PKJ')->sortBy('name');
                                        @endphp
                                        
                                        @if($pgbUsers->count() > 0)
                                            <optgroup label="📁 ═══ PGB ({{ $pgbUsers->count() }} orang) ═══">
                                                @foreach($pgbUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="PGB"
                                                            data-role="{{ $user->role }}"
                                                            {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                        @if($user->id === auth()->id()) (Saya)@endif
                                                        @if($user->role === 'kabag_pgb')@endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        @if($pkjUsers->count() > 0)
                                            <optgroup label="📋 ═══ PKJ ({{ $pkjUsers->count() }} orang) ═══">
                                                @foreach($pkjUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="PKJ"
                                                            data-role="{{ $user->role }}"
                                                            {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                        @if($user->role === 'perizinan')@endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                    @elseif(auth()->user()->role === 'perizinan')
                                        {{-- PERIZINAN --}}
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    data-bagian="{{ $user->bagian }}"
                                                    data-role="{{ $user->role }}"
                                                    {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                {{ $user->name }}{{ $user->id === auth()->id() ? ' (Saya)' : '' }}
                                            </option>
                                        @endforeach
                                        
                                    @else
                                        {{-- FALLBACK --}}
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    data-bagian="{{ $user->bagian ?? '' }}"
                                                    data-role="{{ $user->role }}"
                                                    {{ collect(old('pic_proyek_ids', $existingPicIds ?? []))->contains($user->id) ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            {{-- ✅ Counter (TETAP - Sudah Bagus) --}}
                            <div id="pic_counter" class="mt-3 hidden">
                                <div class="p-3 bg-gradient-to-r from-gray-50 to-green-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                            </svg>
                                            <p class="text-sm font-semibold text-gray-700">
                                                <span id="pic_count_text" class="text-green-600">0 PIC</span>
                                                <span class="text-gray-500">terpilih</span>
                                            </p>
                                        </div>
                                        <div id="pic_breakdown" class="flex gap-2"></div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($users->isEmpty())
                                <p class="mt-2 text-sm text-orange-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Tidak ada user yang tersedia
                                </p>
                            @endif
                            
                            @error('pic_proyek_ids')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.status') }} <span class="text-red-600">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('status') border-red-500 @enderror">
                                <option></option>
                                <option value="Progress" {{ old('status') == 'Progress' ? 'selected' : '' }}>{{ __('projects.in_progress') }}</option>
                                <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>{{ __('projects.pending_status') }}</option>
                                <option value="Done" {{ old('status') == 'Done' ? 'selected' : '' }}>{{ __('projects.completed') }}</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end mt-6 space-x-2">
                            <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                                {{ __('projects.cancel') }}
                            </a>
                            <x-loading-button color="blue">
                                {{ __('projects.save') }}
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ JAVASCRIPT - COMPLETE & WORKING --}}
    @push('scripts')
    <script>
    $(document).ready(function() {
        console.log('📦 DOM Ready');
        // ===== TARGET IMPLEMENTASI - AUTO QUARTAL =====
        const targetDateInput = document.getElementById('target_date');
        const quartalDisplay = document.getElementById('quartal_display');
        const targetImplementasiHidden = document.getElementById('target_implementasi');

        function getQuartalFromDate(dateString) {
            if (!dateString) return '-';
            
            const date = new Date(dateString);
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            
            let quartal;
            if (month >= 1 && month <= 3) {
                quartal = 'Q1';
            } else if (month >= 4 && month <= 6) {
                quartal = 'Q2';
            } else if (month >= 7 && month <= 9) {
                quartal = 'Q3';
            } else {
                quartal = 'Q4';
            }
            
            return `${quartal} ${year}`;
        }

        if (targetDateInput) {
            targetDateInput.addEventListener('change', function() {
                const quartalString = getQuartalFromDate(this.value);
                quartalDisplay.textContent = quartalString;
                targetImplementasiHidden.value = quartalString;
                console.log('Quarter updated:', quartalString);
            });

            // Initial load jika ada value
            if (targetDateInput.value) {
                const quartalString = getQuartalFromDate(targetDateInput.value);
                quartalDisplay.textContent = quartalString;
                targetImplementasiHidden.value = quartalString;
            }
        }

        // ===== SIFAT PROJECT - OTHERS HANDLING =====
        const sifatProjectSelect = $('#sifat_project_select');
        const sifatProjectOthers = document.getElementById('sifat_project_others');
        const sifatProjectHidden = document.getElementById('sifat_project');

        function updateSifatProject() {
            const selectedValue = sifatProjectSelect.val();
            
            if (selectedValue === 'Others') {
                sifatProjectOthers.style.display = 'block';
                sifatProjectOthers.required = true;
                sifatProjectHidden.value = sifatProjectOthers.value;
            } else {
                sifatProjectOthers.style.display = 'none';
                sifatProjectOthers.required = false;
                sifatProjectHidden.value = selectedValue;
            }
        }

        sifatProjectSelect.on('select2:select', function(e) {
            updateSifatProject();
        });

        sifatProjectOthers.addEventListener('input', function() {
            sifatProjectHidden.value = this.value;
        });

        // Initial
        updateSifatProject();

        // ===== FORM VALIDATION =====
        const form = document.getElementById('projectForm');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Validate target_implementasi (hidden field harus terisi)
                if (!targetImplementasiHidden.value || targetImplementasiHidden.value === '-') {
                    isValid = false;
                    errorMessage = 'Target Implementasi wajib diisi!';
                    targetDateInput.focus();
                }

                // Validate PIC
                const selectedPics = $('#pic_proyek_ids').val();
                if (!selectedPics || selectedPics.length === 0) {
                    isValid = false;
                    errorMessage = 'PIC Proyek wajib dipilih minimal 1 orang!';
                    $('#pic_proyek_ids').select2('open');
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
            });
        }
    });

    // ===== INITIALIZE SELECT2 (Wait for jQuery) =====
    $(document).ready(function() {
        console.log('📦 jQuery Ready');
        
        // ===== SINGLE-SELECT DROPDOWNS =====
        
        // Urgensi
        $('#urgensi').select2({
            placeholder: "{{ __('projects.select_urgency') }}", // ✅ TAMBAH PLACEHOLDER
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: Infinity
        });
        
        // Sifat Project
        $('#sifat_project_select').select2({
            placeholder: "{{ __('projects.select_nature') }}", // ✅ TAMBAH PLACEHOLDER
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: Infinity
        });
        
        // Pemilik Project (Divisi)
        $('#pemilik_project_id').select2({
            placeholder: "{{ __('projects.select_division') }}", // ✅ TAMBAH PLACEHOLDER
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: Infinity
        });
        
        // Status
        $('#status').select2({
            placeholder: "{{ __('projects.select_status') }}", // ✅ TAMBAH PLACEHOLDER
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: Infinity
        });
        
        console.log('✅ Single selects initialized');

        $('#pic_proyek_ids').select2({
            width: '100%',
            closeOnSelect: false,
            language: {
                noResults: () => "{{ __('projects.no_results') }}",
                searching: () => "{{ __('projects.searching') }}...",
            }
        });

        // ✅ Force placeholder dengan translation
        $('#pic_proyek_ids').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', "{{ __('projects.search_pic') }}");
        });

        // Show placeholder when empty
        function checkPlaceholder() {
            const selectedCount = $('#pic_proyek_ids').select2('data').length;
            const $container = $('#pic_proyek_ids').next('.select2-container').find('.select2-selection--multiple');
            
            if (selectedCount === 0) {
                if ($container.find('.placeholder-text').length === 0) {
                    $container.prepend('<span class="placeholder-text">{{ __("projects.select_pic_placeholder") }}</span>');
                }
            } else {
                $container.find('.placeholder-text').remove();
            }
        }

        checkPlaceholder();
        $('#pic_proyek_ids').on('change', checkPlaceholder);

        // ===== Set data-bagian untuk styling chips =====
        function updateChipAttributes() {
            setTimeout(() => {
                $('.select2-selection__choice').each(function() {
                    const $chip = $(this);
                    const titleText = $chip.attr('title') || '';
                    
                    $('#pic_proyek_ids option').each(function() {
                        const optionText = $(this).text().trim();
                        const cleanTitle = titleText.replace(/\s*\(.*?\)\s*$/g, '').replace('★', '').trim();
                        const cleanOption = optionText.replace(/\s*\(.*?\)\s*$/g, '').replace('★', '').trim();
                        
                        if (cleanTitle === cleanOption || titleText.includes(cleanOption)) {
                            const bagian = $(this).data('bagian');
                            $chip.attr('data-bagian', bagian);
                            return false;
                        }
                    });
                });
            }, 50);
        }

        // ===== Update Counter =====
        function updateCounter() {
            const selectedOptions = $('#pic_proyek_ids').select2('data');
            const counter = $('#pic_counter');
            const countText = $('#pic_count_text');
            const breakdown = $('#pic_breakdown');
            
            if (selectedOptions.length > 0) {
                counter.removeClass('hidden');
                countText.text(`${selectedOptions.length} PIC`);
                
                // Count by bagian
                let pgbCount = 0, pkjCount = 0, supervisiCount = 0;
                
                selectedOptions.forEach(opt => {
                    const bagian = $(opt.element).data('bagian');
                    if (bagian === 'PGB') pgbCount++;
                    else if (bagian === 'PKJ') pkjCount++;
                    else if (bagian === 'SUPERVISI') supervisiCount++;
                });
                
                // Build badges
                let badges = '';
                if (supervisiCount > 0) {
                    badges += `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">Kadiv: ${supervisiCount}</span>`;
                }
                if (pgbCount > 0) {
                    badges += `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300">PGB: ${pgbCount}</span>`;
                }
                if (pkjCount > 0) {
                    badges += `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300">PKJ: ${pkjCount}</span>`;
                }
                
                breakdown.html(badges);
            } else {
                counter.addClass('hidden');
            }
            
            updateChipAttributes(); // ✅ PENTING: Panggil setiap update
        }

        // ===== Event Listeners =====
        $('#pic_proyek_ids').on('select2:select select2:unselect select2:close', updateCounter);

        // ===== Initial State =====
        updateCounter();
    });
    </script>
    @endpush
</x-app-layout>