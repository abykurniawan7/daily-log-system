<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Request Reset Password') }}
            </h2>
            <a href="{{ route('user.password-requests.status') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Lihat Status Request →
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 text-sm text-gray-600">
                        <p>Lupa password Anda? Isi form di bawah ini untuk mengajukan request reset password ke admin.</p>
                    </div>

                    <form method="POST" action="{{ route('user.password-requests.store') }}">
                        @csrf

                        <!-- Reason -->
                        <div>
                            <x-input-label for="reason" value="Alasan Reset Password" />
                            <textarea id="reason" 
                                      name="reason" 
                                      rows="4" 
                                      class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('reason') border-red-500 @enderror" 
                                      required 
                                      placeholder="Contoh: Lupa password, tidak bisa login, password tidak berfungsi, dll.">{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter. Jelaskan alasan Anda membutuhkan reset password.</p>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('user.password-requests.status') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                ← Kembali ke Status
                            </a>
                            <x-primary-button>
                                {{ __('Kirim Request ke Admin') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Info Box -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Catatan Penting:</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Request Anda akan diproses oleh admin</li>
                                        <li>Admin akan memberikan password baru kepada Anda</li>
                                        <li>Anda dapat memantau status request di halaman "Status Reset Password"</li>
                                        <li>Setelah approved, Anda dapat mengubah password di halaman Profile</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>