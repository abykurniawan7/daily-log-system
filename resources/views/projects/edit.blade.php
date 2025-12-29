<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('projects.edit_project') }}
            </h2>
            <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('projects.back_to_detail') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => __('projects.page_title'), 'url' => route('projects.index')],
                ['label' => $project->nama_project, 'url' => route('projects.show', $project)],
                ['label' => __('projects.edit')]
            ]" />
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('projects.update', $project) }}" method="POST" data-loading="true" id="projectEditForm">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Initiation Date -->
                            <div>
                                <label for="tanggal_inisiasi" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.initiation_date') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_inisiasi" id="tanggal_inisiasi" 
                                    value="{{ old('tanggal_inisiasi', $project->tanggal_inisiasi->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('tanggal_inisiasi') border-red-500 @enderror">
                                @error('tanggal_inisiasi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Target Completion Date (Date Picker + Auto Quarter) -->
                            <div>
                                <label for="target_date" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.target_implementation') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="target_date" 
                                       id="target_date" 
                                       value="{{ old('target_date') }}" 
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('target_implementasi') border-red-500 @enderror">
                                
                                {{-- Display Quarter (Auto-generated) --}}
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">{{ __('projects.quarter') }}:</span> 
                                        <span id="quartal_display" class="text-green-600 font-bold">{{ old('target_implementasi', $project->target_implementasi) }}</span>
                                    </p>
                                </div>
                                
                                {{-- Hidden input for submitting quarter string --}}
                                <input type="hidden" name="target_implementasi" id="target_implementasi" value="{{ old('target_implementasi', $project->target_implementasi) }}">
                                
                                @error('target_implementasi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Project Name -->
                            <div class="md:col-span-2">
                                <label for="nama_project" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.project_name_label') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_project" id="nama_project" 
                                    value="{{ old('nama_project', $project->nama_project) }}"
                                    placeholder="{{ __('projects.enter_project_name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('nama_project') border-red-500 @enderror">
                                @error('nama_project')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Urgency -->
                            <div>
                                <label for="urgensi" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.urgency_label') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="urgensi" id="urgensi" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('urgensi') border-red-500 @enderror">
                                    @if(!old('urgensi') && !$project->urgensi)
                                        <option value="" disabled selected hidden>{{ __('projects.select_urgency') }}</option>
                                    @endif
                                    <option value="Low" {{ old('urgensi', $project->urgensi) == 'Low' ? 'selected' : '' }}>{{ __('projects.low') }}</option>
                                    <option value="Medium" {{ old('urgensi', $project->urgensi) == 'Medium' ? 'selected' : '' }}>{{ __('projects.medium') }}</option>
                                    <option value="High" {{ old('urgensi', $project->urgensi) == 'High' ? 'selected' : '' }}>{{ __('projects.high') }}</option>
                                    <option value="Very High" {{ old('urgensi', $project->urgensi) == 'Very High' ? 'selected' : '' }}>{{ __('projects.very_high') }}</option>
                                </select>
                                @error('urgensi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Project Nature (Dropdown + Others) -->
                            <div>
                                <label for="sifat_project" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.project_nature') }} <span class="text-red-500">*</span>
                                </label>
                                
                                @php
                                    $sifatProjectValue = old('sifat_project', $project->sifat_project);
                                    $predefinedValues = ['RBB', 'Non RBB', 'Regulator', 'Official', 'Kedinasan', 'TL Audit'];
                                    $isOthers = !in_array($sifatProjectValue, $predefinedValues);
                                @endphp
                                
                                <select name="sifat_project_select" 
                                        id="sifat_project_select" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('sifat_project') border-red-500 @enderror">
                                    @if(!$sifatProjectValue)
                                        <option value="" disabled selected hidden>{{ __('projects.select_nature') }}</option>
                                    @endif
                                    <option value="RBB" {{ $sifatProjectValue == 'RBB' ? 'selected' : '' }}>{{ __('projects.rbb') }}</option>
                                    <option value="Non RBB" {{ $sifatProjectValue == 'Non RBB' ? 'selected' : '' }}>{{ __('projects.non_rbb') }}</option>
                                    <option value="Regulator" {{ $sifatProjectValue == 'Regulator' ? 'selected' : '' }}>{{ __('projects.regulator') }}</option>
                                    <option value="Official" {{ $sifatProjectValue == 'Official' ? 'selected' : '' }}>{{ __('projects.official') }}</option>
                                    <option value="Kedinasan" {{ $sifatProjectValue == 'Kedinasan' ? 'selected' : '' }}>{{ __('projects.kedinasan') }}</option>
                                    <option value="TL Audit" {{ $sifatProjectValue == 'TL Audit' ? 'selected' : '' }}>{{ __('projects.tl_audit') }}</option>
                                    <option value="Others" {{ $isOthers ? 'selected' : '' }}>{{ __('projects.others') }}</option>
                                </select>
                                
                                {{-- Manual input if "Others" selected --}}
                                <input type="text" 
                                       name="sifat_project_others" 
                                       id="sifat_project_others" 
                                       value="{{ $isOthers ? $sifatProjectValue : '' }}"
                                       placeholder="{{ __('projects.enter_other_nature') }}"
                                       style="display: {{ $isOthers ? 'block' : 'none' }};"
                                       class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">
                                
                                {{-- Hidden input for submission --}}
                                <input type="hidden" name="sifat_project" id="sifat_project" value="{{ $sifatProjectValue }}">
                                
                                @error('sifat_project')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Project Owner (Division) -->
                            <div>
                                <label for="pemilik_project_id" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.project_owner') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="pemilik_project_id" id="pemilik_project_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('pemilik_project_id') border-red-500 @enderror">
                                    @if(!old('pemilik_project_id') && !$project->pemilik_project_id)
                                        <option value="" disabled selected hidden>{{ __('projects.select_division') }}</option>
                                    @endif
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('pemilik_project_id', $project->pemilik_project_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->nama_divisi }} ({{ $division->kode_divisi }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pemilik_project_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ✅ REVISED: Project PIC (Multi-Select) -->
                            <div class="md:col-span-2">
                                <label for="pic_proyek_ids" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.pic_project') }} <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500 font-normal ml-2">(Bisa pilih lebih dari 1 orang)</span>
                                    
                                    {{-- ✅ Helper Text berdasarkan role editor --}}
                                    @if(auth()->user()->role === 'supervisi')
                                        <span class="text-xs text-blue-600 font-normal block mt-1">
                                            {{ __('projects.pic_hint_edit_supervisi') }}
                                        </span>
                                    @elseif(auth()->user()->role === 'kabag_pgb')
                                        <span class="text-xs text-green-600 font-normal block mt-1">
                                            {{ __('projects.pic_hint_edit_kabag') }}
                                        </span>
                                    @elseif(auth()->user()->role === 'perizinan')
                                        <span class="text-xs text-purple-600 font-normal block mt-1">
                                            {{ __('projects.pic_hint_edit_perizinan') }}
                                        </span>
                                    @endif
                                </label>

                                @php
                                    $projectOwnerRole = $project->creator->role ?? null;
                                @endphp
                                
                                @if($projectOwnerRole === 'kabag_pgb')
                                    <div class="mb-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ __('projects.pic_filter_kabag') }}
                                    </div>
                                @elseif($projectOwnerRole === 'perizinan')
                                    <div class="mb-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ __('projects.pic_filter_pkj') }}
                                    </div>
                                @endif
                                
                                @php
                                    // Get existing PICs dari pivot table
                                    $existingPicIds = old('pic_proyek_ids', $project->pics->pluck('id')->toArray());
                                @endphp
                                
                                {{-- ✅ MULTI-SELECT untuk PIC --}}
                                <select name="pic_proyek_ids[]" 
                                        id="pic_proyek_ids" 
                                        multiple 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('pic_proyek_ids') border-red-500 @enderror">
                                    
                                    @if(auth()->user()->role === 'supervisi')
                                        {{-- ✅ SUPERVISI: Group by Bagian + Include Self --}}
                                        @php
                                            $supervisiUsers = $users->where('role', 'supervisi');
                                            $pgbUsers = $users->where('bagian', 'PGB');
                                            $pkjUsers = $users->where('bagian', 'PKJ');
                                        @endphp
                                        
                                        {{-- Group Supervisi --}}
                                        @if($supervisiUsers->count() > 0)
                                            <optgroup label="🔰 SUPERVISI">
                                                @foreach($supervisiUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="SUPERVISI"
                                                            data-role="{{ $user->role }}"
                                                            {{ in_array($user->id, $existingPicIds) ? 'selected' : '' }}>
                                                        {{ $user->name }} (Super Admin)
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        {{-- Group PGB --}}
                                        @if($pgbUsers->count() > 0)
                                            <optgroup label="📁 PGB">
                                                @foreach($pgbUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="PGB"
                                                            data-role="{{ $user->role }}"
                                                            {{ in_array($user->id, $existingPicIds) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                        @if($user->role === 'kabag_pgb') ★ @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        {{-- Group PKJ --}}
                                        @if($pkjUsers->count() > 0)
                                            <optgroup label="📁 PKJ">
                                                @foreach($pkjUsers as $user)
                                                    <option value="{{ $user->id }}" 
                                                            data-bagian="PKJ"
                                                            data-role="{{ $user->role }}"
                                                            {{ in_array($user->id, $existingPicIds) ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                        @if($user->role === 'perizinan') (Kabag PKJ) @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                    @elseif(auth()->user()->role === 'kabag_pgb')
                                        {{-- ✅ KABAG PGB: Hanya PGB --}}
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    data-bagian="{{ $user->bagian }}"
                                                    data-role="{{ $user->role }}"
                                                    {{ in_array($user->id, $existingPicIds) ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                @if($user->id === auth()->id()) (Saya) @endif
                                                @if($user->role === 'kabag_pgb') ★ @endif
                                            </option>
                                        @endforeach
                                        
                                    @elseif(auth()->user()->role === 'perizinan')
                                        {{-- ✅ PERIZINAN: Hanya PKJ --}}
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    data-bagian="{{ $user->bagian }}"
                                                    data-role="{{ $user->role }}"
                                                    {{ in_array($user->id, $existingPicIds) ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                @if($user->id === auth()->id()) (Saya - Kabag PKJ) @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                
                                @if($users->isEmpty())
                                    <p class="mt-1 text-sm text-orange-600">
                                        ⚠️ Tidak ada user yang tersedia untuk bagian ini.
                                    </p>
                                @endif
                                
                                @error('pic_proyek_ids')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                @error('pic_proyek_ids.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.status') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('status') border-red-500 @enderror">
                                    @if(!old('status') && !$project->status)
                                        <option value="" disabled selected hidden>{{ __('projects.select_status') }}</option>
                                    @endif
                                    <option value="Progress" {{ old('status', $project->status) == 'Progress' ? 'selected' : '' }}>{{ __('projects.in_progress') }}</option>
                                    <option value="Pending" {{ old('status', $project->status) == 'Pending' ? 'selected' : '' }}>{{ __('projects.pending_status') }}</option>
                                    <option value="Done" {{ old('status', $project->status) == 'Done' ? 'selected' : '' }}>{{ __('projects.completed') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.description') }}
                                </label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" 
                                    placeholder="{{ __('projects.enter_description') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $project->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a href="{{ route('projects.show', $project) }}" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                {{ __('projects.cancel') }}
                            </a>
                            <x-loading-button color="blue">
                                {{ __('projects.update') }}
                            </x-loading-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ CSS Custom untuk Select2 Multi-select --}}
    @push('styles')
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 42px !important;
            border-color: #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 2px 8px !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #10b981 !important;
            border-color: #059669 !important;
            color: white !important;
            padding: 4px 10px !important;
            border-radius: 9999px !important;
            font-size: 0.875rem !important;
            margin: 3px !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px !important;
            font-weight: bold !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #fca5a5 !important;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== TARGET COMPLETION DATE - AUTO QUARTER =====
            const targetDateInput = document.getElementById('target_date');
            const quartalDisplay = document.getElementById('quartal_display');
            const targetImplementasiHidden = document.getElementById('target_implementasi');

            function getQuartalFromDate(dateString) {
                if (!dateString) return '-';
                
                const date = new Date(dateString);
                const month = date.getMonth() + 1;
                const year = date.getFullYear();
                
                let quartal;
                if (month >= 1 && month <= 3) quartal = 'Q1';
                else if (month >= 4 && month <= 6) quartal = 'Q2';
                else if (month >= 7 && month <= 9) quartal = 'Q3';
                else quartal = 'Q4';
                
                return `${quartal} ${year}`;
            }

            function parseQuartalToDate(quartalString) {
                if (!quartalString || quartalString === '-') return null;
                
                const match = quartalString.match(/Q(\d)\s+(\d{4})/);
                if (!match) return null;
                
                const quartal = parseInt(match[1]);
                const year = parseInt(match[2]);
                
                const monthMap = { 1: '01', 2: '04', 3: '07', 4: '10' };
                const month = monthMap[quartal];
                return `${year}-${month}-01`;
            }

            const existingQuartal = targetImplementasiHidden.value;
            if (existingQuartal && existingQuartal !== '-') {
                const initialDate = parseQuartalToDate(existingQuartal);
                if (initialDate) targetDateInput.value = initialDate;
            }

            targetDateInput.addEventListener('change', function() {
                const quartalString = getQuartalFromDate(this.value);
                quartalDisplay.textContent = quartalString;
                targetImplementasiHidden.value = quartalString;
            });

            // ===== PROJECT NATURE - OTHERS HANDLING =====
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

            sifatProjectSelect.on('select2:select', updateSifatProject);
            sifatProjectOthers.addEventListener('input', function() {
                sifatProjectHidden.value = this.value;
            });

            updateSifatProject();

            // ===== FORM VALIDATION =====
            const form = document.getElementById('projectEditForm');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Validate target implementasi
                if (!targetImplementasiHidden.value || targetImplementasiHidden.value === '-') {
                    isValid = false;
                    errorMessage = '{{ __("projects.target_implementation") }} {{ __("projects.required_field") }}';
                    targetDateInput.focus();
                }

                // Validate sifat project others
                if (sifatProjectSelect.val() === 'Others' && !sifatProjectOthers.value.trim()) {
                    isValid = false;
                    errorMessage = '{{ __("projects.enter_other_nature") }}';
                    sifatProjectOthers.focus();
                }

                // ✅ Validate PIC (Multi-select)
                var selectedPics = $('#pic_proyek_ids').val();
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
        });

        // ===== INITIALIZE SELECT2 =====
        $(document).ready(function() {
            // Urgensi
            $('#urgensi').select2({
                placeholder: 'Pilih Urgensi',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Sifat Project
            $('#sifat_project_select').select2({
                placeholder: 'Pilih Sifat Project',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Divisi
            $('#pemilik_project_id').select2({
                placeholder: 'Pilih Divisi',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // ✅ PIC Multi-select
            $('#pic_proyek_ids').select2({
                placeholder: 'Pilih PIC (bisa lebih dari 1)',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                templateResult: formatPicOption,
                templateSelection: formatPicSelection
            });

            function formatPicOption(option) {
                if (!option.id) return option.text;
                
                var $option = $(option.element);
                var bagian = $option.data('bagian');
                var role = $option.data('role');
                var name = option.text.replace(/\s*\(.*?\)\s*$/g, '').replace('★', '').trim();
                
                var badge = '';
                if (bagian === 'PGB') {
                    badge = '<span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full font-medium">PGB</span>';
                } else if (bagian === 'PKJ') {
                    badge = '<span class="ml-2 px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full font-medium">PKJ</span>';
                }
                
                var roleIndicator = '';
                if (role === 'kabag_pgb') {
                    roleIndicator = '<span class="ml-1 text-xs text-green-600 font-bold">★</span>';
                } else if (role === 'perizinan') {
                    roleIndicator = '<span class="ml-1 text-xs text-purple-600 font-bold">(Kabag)</span>';
                }
                
                return $('<span>' + name + roleIndicator + badge + '</span>');
            }

            function formatPicSelection(option) {
                if (!option.id) return option.text;
                
                var $option = $(option.element);
                var bagian = $option.data('bagian');
                var name = option.text.replace(/\s*\(.*?\)\s*$/g, '').replace('★', '').trim();
                
                var badge = '';
                if (bagian === 'PGB') {
                    badge = '<span class="ml-1 px-1.5 py-0.5 text-xs bg-green-500 text-white rounded font-medium">P</span>';
                } else if (bagian === 'PKJ') {
                    badge = '<span class="ml-1 px-1.5 py-0.5 text-xs bg-purple-500 text-white rounded font-medium">K</span>';
                }
                
                return $('<span>' + name + badge + '</span>');
            }

            // Status
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