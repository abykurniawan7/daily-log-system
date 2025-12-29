<x-guest-layout>
    <!-- Session Status - Success Message -->
    @if(session('status'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="font-semibold">✓ Request Berhasil Dikirim!</p>
                    <p class="text-sm mt-1">{{ session('status') }}</p>
                    
                    <div class="mt-3 pt-3 border-t border-green-300">
                        <p class="text-sm font-medium mb-2">Untuk memantau status request Anda:</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-green-700 text-white text-sm font-medium rounded hover:bg-green-800 transition">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Login untuk Cek Status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-4 text-sm text-gray-600">
        <p>Lupa password Anda? Tidak masalah. Isi form di bawah ini untuk mengajukan request reset password ke admin.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Reason -->
        <div class="mt-4">
            <x-input-label for="reason" value="Alasan Reset Password" />
            <textarea id="reason" 
                      name="reason" 
                      rows="3" 
                      class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                      required 
                      placeholder="Contoh: Lupa password, tidak bisa login, dll.">{{ old('reason') }}</textarea>
            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter. Jelaskan alasan Anda membutuhkan reset password.</p>
        </div>

        <div class="flex items-center justify-end mt-4">
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
                        <li>Login ke akun Anda untuk memantau status request</li>
                        <li>Setelah login dengan password baru, Anda dapat mengubahnya di halaman Profile</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>