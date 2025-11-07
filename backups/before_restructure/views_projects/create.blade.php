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
                                <option value="" disabled selected hidden>{{ __('projects.select_urgency') }}</option>
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
                                <option value="" disabled selected hidden>{{ __('projects.select_nature') }}</option>
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
                                <option value="" disabled selected hidden>{{ __('projects.select_division') }}</option>
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

                        {{-- PIC Proyek (User) --}}
                        <div class="mb-4">
                            <label for="pic_proyek_id" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.pic_project') }} <span class="text-red-600">*</span>
                                
                                {{-- Helper Text berdasarkan role --}}
                                @if(auth()->user()->role === 'supervisi')
                                    <span class="text-xs text-gray-500 font-normal ml-2">(Hanya Karyawan PGB)</span>
                                @elseif(auth()->user()->role === 'perizinan')
                                    <span class="text-xs text-gray-500 font-normal ml-2">(Hanya Karyawan PKJ)</span>
                                @endif
                            </label>
                            
                            <select name="pic_proyek_id" id="pic_proyek_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('pic_proyek_id') border-red-500 @enderror">
                                <option value="" disabled selected hidden>{{ __('projects.select_pic') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('pic_proyek_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->bagian }})
                                    </option>
                                @endforeach
                            </select>
                            
                            @if($users->isEmpty())
                                <p class="mt-1 text-sm text-orange-600">
                                    ⚠️ Tidak ada user yang tersedia untuk bagian ini.
                                </p>
                            @endif
                            
                            @error('pic_proyek_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                {{ __('projects.status') }} <span class="text-red-600">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('status') border-red-500 @enderror">
                                <option value="" disabled selected hidden>{{ __('projects.select_status') }}</option>
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
                            <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

    @push('scripts')
    <script>
        // ===== PROJECTS CREATE - FIXED JAVASCRIPT FOR SELECT2 =====

        document.addEventListener('DOMContentLoaded', function() {
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

            targetDateInput.addEventListener('change', function() {
                const quartalString = getQuartalFromDate(this.value);
                quartalDisplay.textContent = quartalString;
                targetImplementasiHidden.value = quartalString;
            });

            if (targetDateInput.value) {
                const quartalString = getQuartalFromDate(targetDateInput.value);
                quartalDisplay.textContent = quartalString;
                targetImplementasiHidden.value = quartalString;
            }

            // ===== SIFAT PROJECT - OTHERS HANDLING (FIXED FOR SELECT2) =====
            const sifatProjectSelect = $('#sifat_project_select'); // jQuery selector for Select2
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

            // ✅ PENTING: Gunakan Select2's event listener
            sifatProjectSelect.on('select2:select', function(e) {
                updateSifatProject();
            });

            // Untuk input manual "Others"
            sifatProjectOthers.addEventListener('input', function() {
                sifatProjectHidden.value = this.value;
            });

            // Initial update saat page load
            updateSifatProject();

            // ===== FORM VALIDATION =====
            const form = document.getElementById('projectForm');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Validate target implementasi
                if (!targetImplementasiHidden.value || targetImplementasiHidden.value === '-') {
                    isValid = false;
                    errorMessage = 'Target Implementasi wajib diisi!';
                    targetDateInput.focus();
                }

                // Validate urgensi
                if (!$('#urgensi').val()) {
                    isValid = false;
                    errorMessage = 'Urgensi wajib dipilih!';
                    $('#urgensi').select2('open');
                }

                // Validate sifat project
                if (!sifatProjectSelect.val()) {
                    isValid = false;
                    errorMessage = 'Sifat Project wajib dipilih!';
                    sifatProjectSelect.select2('open');
                }

                // Validate sifat project others
                if (sifatProjectSelect.val() === 'Others' && !sifatProjectOthers.value.trim()) {
                    isValid = false;
                    errorMessage = 'Mohon isi Sifat Project lainnya!';
                    sifatProjectOthers.focus();
                }

                // Validate division
                if (!$('#pemilik_project_id').val()) {
                    isValid = false;
                    errorMessage = 'Divisi wajib dipilih!';
                    $('#pemilik_project_id').select2('open');
                }

                // Validate PIC
                if (!$('#pic_proyek_id').val()) {
                    isValid = false;
                    errorMessage = 'PIC Proyek wajib dipilih!';
                    $('#pic_proyek_id').select2('open');
                }

                // Validate status
                if (!$('#status').val()) {
                    isValid = false;
                    errorMessage = 'Status wajib dipilih!';
                    $('#status').select2('open');
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
            });
        });

        // ===== INITIALIZE SELECT2 SAAT DOCUMENT READY =====
        $(document).ready(function() {
            // Urgensi dropdown
            $('#urgensi').select2({
                placeholder: 'Pilih Urgensi',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Sifat Project dropdown
            $('#sifat_project_select').select2({
                placeholder: 'Pilih Sifat Project',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Divisi dropdown
            $('#pemilik_project_id').select2({
                placeholder: 'Pilih Divisi',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // PIC dropdown
            $('#pic_proyek_id').select2({
                placeholder: 'Pilih PIC',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Status dropdown
            $('#status').select2({
                placeholder: 'Pilih Status',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });
        });
    </script>
    @endpush
</x-app-layout>