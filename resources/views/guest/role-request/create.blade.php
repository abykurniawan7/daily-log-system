<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('role-request.page_title') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('role-request.back_to_dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Info Box --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">{{ __('role-request.info_title') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>{{ __('role-request.info_points.choose_role') }}</li>
                                <li>{{ __('role-request.info_points.description') }}</li>
                                <li>{{ __('role-request.info_points.upload_doc') }}</li>
                                <li>{{ __('role-request.info_points.process') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('role-requests.store') }}" enctype="multipart/form-data" x-data="roleRequestForm()">
                        @csrf

                        {{-- User Info (Read Only) --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('role-request.applicant_info') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">{{ __('role-request.name') }}:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ Auth::user()->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('role-request.email') }}:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Role Selection --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                {{ __('role-request.choose_role') }} <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="space-y-3">
                                {{-- Supervisi --}}
                                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                       :class="selectedRole === 'supervisi' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="requested_role" value="supervisi" 
                                           x-model="selectedRole"
                                           class="mt-1 h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <div class="ml-3 flex-1">
                                        <span class="block text-sm font-semibold text-gray-900">{{ __('role-request.role_supervisi') }}</span>
                                        <span class="block text-xs text-gray-600 mt-1">
                                            {{ __('role-request.role_supervisi_desc') }}
                                        </span>
                                    </div>
                                </label>

                                {{-- PKJ (Perizinan) --}}
                                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                       :class="selectedRole === 'perizinan' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="requested_role" value="perizinan" 
                                           x-model="selectedRole"
                                           class="mt-1 h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <div class="ml-3 flex-1">
                                        <span class="block text-sm font-semibold text-gray-900">{{ __('role-request.role_pkj') }}</span>
                                        <span class="block text-xs text-gray-600 mt-1">
                                            {{ __('role-request.role_pkj_desc') }}
                                        </span>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                            {{ __('role-request.role_pkj_badge') }}
                                        </span>
                                    </div>
                                </label>

                                {{-- PGB (Pembangunan) --}}
                                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                       :class="selectedRole === 'karyawan' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="requested_role" value="karyawan" 
                                           x-model="selectedRole"
                                           class="mt-1 h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <div class="ml-3 flex-1">
                                        <span class="block text-sm font-semibold text-gray-900">{{ __('role-request.role_pgb') }}</span>
                                        <span class="block text-xs text-gray-600 mt-1">
                                            {{ __('role-request.role_pgb_desc') }}
                                        </span>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">
                                            {{ __('role-request.role_pgb_badge') }}
                                        </span>
                                    </div>
                                </label>
                            </div>

                            @error('requested_role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi/Alasan --}}
                        <div class="mb-6">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('role-request.description_label') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="deskripsi" 
                                name="deskripsi" 
                                rows="5" 
                                x-model="deskripsi"
                                @input="updateCharCount()"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                placeholder="{{ __('role-request.description_placeholder') }}"
                                required>{{ old('deskripsi') }}</textarea>
                            
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span class="text-gray-500" x-show="charCount < 50" x-cloak>
                                    {{ __('role-request.min_characters') }}
                                </span>
                                <span class="text-gray-500" 
                                      :class="charCount >= 50 ? 'text-green-600' : 'text-gray-500'">
                                    <span x-text="charCount"></span> / 1000 {{ app()->getLocale() == 'id' ? 'karakter' : 'characters' }}
                                </span>
                            </div>

                            @error('deskripsi')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Upload Dokumen --}}
                        <div class="mb-6">
                            <label for="dokumen" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('role-request.upload_document') }} <span class="text-gray-500">({{ __('role-request.optional') }})</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="dokumen" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>{{ __('role-request.upload_file') }}</span>
                                            <input id="dokumen" name="dokumen" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf" x-ref="fileInput" @change="handleFileUpload($event)">
                                        </label>
                                        <p class="pl-1">{{ __('role-request.drag_drop') }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        {{ __('role-request.file_types') }}
                                    </p>
                                    <p class="text-xs text-gray-400 italic">
                                        {{ __('role-request.file_examples') }}
                                    </p>
                                </div>
                            </div>

                            <div x-show="fileName" x-cloak class="mt-3 p-3 bg-gray-50 rounded-md flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700" x-text="fileName"></span>
                                </div>
                                <button type="button" @click="clearFile()" class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>

                            @error('dokumen')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                ← {{ __('role-request.cancel') }}
                            </a>
                            
                            <button type="submit" 
                                    :disabled="!canSubmit()"
                                    :class="canSubmit() ? 'bg-green-800 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('role-request.submit_request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function roleRequestForm() {
            return {
                selectedRole: '{{ old('requested_role') }}',
                deskripsi: '{{ old('deskripsi') }}',
                charCount: {{ old('deskripsi') ? strlen(old('deskripsi')) : 0 }},
                fileName: '',

                updateCharCount() {
                    this.charCount = this.deskripsi.length;
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.fileName = file.name;
                    }
                },

                clearFile() {
                    this.fileName = '';
                    this.$refs.fileInput.value = '';
                },

                canSubmit() {
                    return this.selectedRole !== '' && this.charCount >= 50;
                }
            }
        }
    </script>
    @endpush
</x-app-layout> 