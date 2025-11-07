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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tanggal_inisiasi') border-red-500 @enderror">
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
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('target_implementasi') border-red-500 @enderror">
                                
                                {{-- Display Quarter (Auto-generated) --}}
                                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">{{ __('projects.quarter') }}:</span> 
                                        <span id="quartal_display" class="text-blue-600 font-bold">{{ old('target_implementasi', $project->target_implementasi) }}</span>
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nama_project') border-red-500 @enderror">
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('urgensi') border-red-500 @enderror">
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
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('sifat_project') border-red-500 @enderror">
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
                                       class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('pemilik_project_id') border-red-500 @enderror">
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

                            <!-- Project PIC -->
                            <div>
                                <label for="pic_proyek_id" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.pic_project') }} <span class="text-red-500">*</span>
                                    
                                    {{-- Helper Text berdasarkan pembuat project --}}
                                    @php
                                        $projectCreator = $project->creator;
                                    @endphp
                                    
                                    @if($projectCreator && $projectCreator->role === 'supervisi')
                                        <span class="text-xs text-gray-500 font-normal ml-2">(Hanya Karyawan PGB)</span>
                                    @elseif($projectCreator && $projectCreator->role === 'perizinan')
                                        <span class="text-xs text-gray-500 font-normal ml-2">(Hanya Karyawan PKJ)</span>
                                    @endif
                                </label>
                                
                                <select name="pic_proyek_id" id="pic_proyek_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('pic_proyek_id') border-red-500 @enderror">
                                    @if(!old('pic_proyek_id') && !$project->pic_proyek_id)
                                        <option value="" disabled selected hidden>{{ __('projects.select_pic') }}</option>
                                    @endif
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('pic_proyek_id', $project->pic_proyek_id) == $user->id ? 'selected' : '' }}>
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

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    {{ __('projects.status') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $project->deskripsi) }}</textarea>
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
            const sifatProjectSelect = document.getElementById('sifat_project_select');
            const sifatProjectOthers = document.getElementById('sifat_project_others');
            const sifatProjectHidden = document.getElementById('sifat_project');

            function updateSifatProject() {
                const selectedValue = sifatProjectSelect.value;
                
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

            sifatProjectSelect.addEventListener('change', updateSifatProject);
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
                if (sifatProjectSelect.value === 'Others' && !sifatProjectOthers.value.trim()) {
                    isValid = false;
                    errorMessage = '{{ __("projects.enter_other_nature") }}';
                    sifatProjectOthers.focus();
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>