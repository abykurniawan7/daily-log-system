<x-guest-layout>
    <!-- Header Register -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
        <p class="text-gray-600 mt-2 text-sm">Daftar untuk mengakses WorkLog System</p>
        <p class="text-xs text-gray-500 mt-1">Bank Pembangunan Daerah Bali</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm">
        @csrf

        <!-- Name / Username -->
        <div>
            <x-input-label for="name" class="font-semibold text-gray-700">
                Nama Pengguna <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input 
                id="name" 
                class="block mt-2 w-full border-gray-300 focus:border-green-600 focus:ring-green-600" 
                type="text" 
                name="name" 
                :value="old('name')" 
                required 
                autofocus 
                autocomplete="name"
                placeholder="Contoh: Budi Setiawan" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <p class="mt-1.5 text-xs text-gray-500 flex items-start">
                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Bisa nama lengkap atau username (minimal 2 kata). Gunakan huruf besar di awal setiap kata
            </p>
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" class="font-semibold text-gray-700">
                Alamat Email <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input 
                id="email" 
                class="block mt-2 w-full border-gray-300 focus:border-green-600 focus:ring-green-600" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autocomplete="username"
                placeholder="username@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="mt-1.5 text-xs text-gray-500 flex items-start">
                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>
                    <strong class="text-amber-600">Tidak harus email yang sudah ada.</strong> 
                    Disarankan buat email baru khusus untuk sistem ini
                </span>
            </p>
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" class="font-semibold text-gray-700">
                Kata Sandi <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input 
                id="password" 
                class="block mt-2 w-full border-gray-300 focus:border-green-600 focus:ring-green-600"
                type="password"
                name="password"
                required 
                autocomplete="new-password"
                placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="mt-1.5 text-xs text-gray-500 flex items-start">
                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>
                    Minimal 8 karakter. 
                    <strong class="text-amber-600">Gunakan password yang berbeda</strong> dari email lain untuk keamanan
                </span>
            </p>
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" class="font-semibold text-gray-700">
                Konfirmasi Kata Sandi <span class="text-red-500">*</span>
            </x-input-label>
            <x-text-input 
                id="password_confirmation" 
                class="block mt-2 w-full border-gray-300 focus:border-green-600 focus:ring-green-600"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Masukkan ulang kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <p class="mt-1.5 text-xs text-gray-500 flex items-start">
                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Harus sama persis dengan kata sandi di atas
            </p>
        </div>

        <!-- Terms & Conditions Info -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs text-gray-700 leading-relaxed">
                    Dengan mendaftar, Anda menyetujui untuk menggunakan sistem WorkLog sesuai dengan kebijakan internal Bank BPD Bali.
                </p>
            </div>
        </div>

        <!-- Register Button -->
        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('Daftar Sekarang') }}
            </x-primary-button>
        </div>

        <!-- Login Link -->
        <div class="text-center border-t border-gray-200 pt-4 mt-6">
            <span class="text-sm text-gray-600">Sudah punya akun?</span>
            <a 
                href="{{ route('login') }}" 
                class="text-sm font-medium text-green-800 hover:text-green-900 ml-1">
                Masuk di sini
            </a>
        </div>

        <!-- Support Info -->
        <div class="mt-4 pt-3 border-t border-gray-100">
            <p class="text-xs text-gray-500 text-center">
                Butuh bantuan? Hubungi 
                <a href="mailto:support@bpdbali.co.id" class="text-green-800 hover:text-green-900 font-medium">
                    support@bpdbali.co.id
                </a>
            </p>
        </div>
    </form>

    <!-- ✅ Custom Validation Styling untuk Required Fields -->
    <style>
        /* Styling untuk input yang invalid saat submit */
        input:invalid:not(:placeholder-shown) {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        /* Styling untuk input yang belum diisi saat required */
        input:required:invalid {
            border-color: #d1d5db;
        }

        /* Ketika form sudah di-submit dan ada field kosong */
        .was-validated input:invalid,
        input.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        /* Label untuk field yang invalid */
        .was-validated input:invalid + label,
        input.is-invalid + label {
            color: #ef4444;
        }

        /* Focus state tetap hijau meski invalid */
        input:invalid:focus {
            border-color: #0F5132 !important;
            box-shadow: 0 0 0 3px rgba(15, 81, 50, 0.1) !important;
        }

        /* Animasi shake untuk field invalid */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s;
        }
    </style>

    <!-- ✅ JavaScript untuk Validasi Real-time -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('input[required]');
            
            // Validasi saat form di-submit
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid', 'shake');
                        
                        // Hapus class shake setelah animasi selesai
                        setTimeout(() => {
                            input.classList.remove('shake');
                        }, 500);
                        
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                // Jika ada field kosong, prevent submit dan scroll ke field pertama yang error
                if (!isValid) {
                    e.preventDefault();
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                    
                    // Tampilkan alert
                    alert('⚠️ Mohon lengkapi semua field yang bertanda bintang merah (*)');
                }
                
                form.classList.add('was-validated');
            });
            
            // Hapus error styling ketika user mulai mengetik
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
                
                // Hapus error styling ketika focus
                input.addEventListener('focus', function() {
                    this.classList.remove('is-invalid');
                });
            });
            
            // Validasi nama (minimal 2 kata)
            const nameInput = document.getElementById('name');
            nameInput.addEventListener('blur', function() {
                const words = this.value.trim().split(/\s+/);
                if (words.length < 2 && this.value.trim() !== '') {
                    alert('⚠️ Nama harus terdiri dari minimal 2 kata (contoh: Budi Setiawan)');
                    this.classList.add('is-invalid', 'shake');
                    setTimeout(() => this.classList.remove('shake'), 500);
                }
            });
            
            // Auto-capitalize nama
            nameInput.addEventListener('input', function() {
                // Capitalize setiap kata
                const words = this.value.split(' ');
                const capitalizedWords = words.map(word => {
                    if (word.length > 0) {
                        return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                    }
                    return word;
                });
                this.value = capitalizedWords.join(' ');
            });
            
            // Validasi password match
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');
            
            passwordConfirm.addEventListener('input', function() {
                if (this.value !== password.value && this.value.length > 0) {
                    this.setCustomValidity('Password tidak cocok');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            password.addEventListener('input', function() {
                if (passwordConfirm.value && passwordConfirm.value !== this.value) {
                    passwordConfirm.setCustomValidity('Password tidak cocok');
                } else {
                    passwordConfirm.setCustomValidity('');
                }
            });
        });
    </script>
</x-guest-layout>