<x-guest-layout>
    <!-- Header Login -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
        <p class="text-gray-600 mt-2 text-sm">Silakan login untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold text-gray-700" />
            <x-text-input 
                id="email" 
                class="block mt-2 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="nama@bpdbali.co.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-700" />
            <x-text-input 
                id="password" 
                class="block mt-2 w-full"
                type="password"
                name="password"
                required 
                autocomplete="current-password"
                placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-green-800 shadow-sm focus:ring-green-800" 
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a 
                    class="text-sm font-medium text-green-800 hover:text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-800" 
                    href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold shadow-lg hover:shadow-xl">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

        <!-- Register Link (Optional - Uncomment jika ada fitur register) -->
        <!-- 
        @if (Route::has('register'))
            <div class="text-center border-t border-gray-200 pt-4 mt-6">
                <span class="text-sm text-gray-600">Belum punya akun?</span>
                <a href="{{ route('register') }}" 
                   class="text-sm font-medium text-green-800 hover:text-green-900 ml-1">
                    Daftar sekarang
                </a>
            </div>
        @endif
        -->

        {{-- Session Timeout Warning --}}
        @if(request()->has('timeout') || session('warning'))
            <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            {{ session('warning') ?? 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </form>
</x-guest-layout>