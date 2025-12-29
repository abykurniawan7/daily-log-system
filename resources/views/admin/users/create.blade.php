<x-app-layout>
    <x-slot name="title">Tambah User Baru</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tambah User Baru
                </h2>
                <p class="text-sm text-gray-600 mt-1">Buat akun user baru untuk sistem WorkLog</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.store') }}" x-data="{ 
                        role: '{{ old('role', '') }}',
                        bagian: '{{ old('bagian', 'PGB') }}',
                        isKaryawan() {
                            return this.role === 'karyawan';
                        },
                        updateBagian() {
                            if (this.role === 'supervisi') {
                                this.bagian = '';
                            } else if (this.role === 'kabag_pgb') {
                                this.bagian = 'PGB';
                            } else if (this.role === 'perizinan') {
                                this.bagian = 'PKJ';
                            }
                            // Untuk karyawan, biarkan user pilih sendiri
                        }
                    }" x-init="updateBagian()">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Masukkan nama lengkap user"
                                   required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ✅ Role (4 Opsi Utama) --}}
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" 
                                    id="role" 
                                    x-model="role"
                                    @change="updateBagian()"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                                <option value="">Pilih Role</option>
                                <option value="supervisi" {{ old('role') === 'supervisi' ? 'selected' : '' }}>
                                    Supervisi (Kadiv)
                                </option>
                                <option value="kabag_pgb" {{ old('role') === 'kabag_pgb' ? 'selected' : '' }}>
                                    Kabag PGB
                                </option>
                                <option value="perizinan" {{ old('role') === 'perizinan' ? 'selected' : '' }}>
                                    Kabag PKJ
                                </option>
                                <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>
                                    Karyawan
                                </option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            {{-- ✅ Info Box - Dynamic berdasarkan role --}}
                            <div class="mt-2 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                                <p class="text-xs text-blue-700">
                                    <span class="font-semibold">ℹ️ Keterangan:</span><br>
                                    <span x-show="role === 'supervisi'">• Supervisi: Kadiv dengan akses penuh ke semua bagian</span>
                                    <span x-show="role === 'kabag_pgb'">• Kabag PGB: Kepala Bagian Pengembangan (PGB)</span>
                                    <span x-show="role === 'perizinan'">• Kabag PKJ: Kepala Bagian Perizinan (PKJ)</span>
                                    <span x-show="role === 'karyawan'">• Karyawan: Staff (Pilih bagian di bawah: PGB atau PKJ)</span>
                                    <span x-show="!role || role === ''">Pilih role untuk melihat keterangan</span>
                                </p>
                            </div>
                        </div>

                        {{-- ✅ Bagian (Tampil hanya untuk role Karyawan) --}}
                        <div class="mb-4" x-show="isKaryawan()" x-transition>
                            <label for="bagian" class="block text-sm font-medium text-gray-700 mb-2">
                                Bagian <span class="text-red-500">*</span>
                            </label>
                            <select name="bagian" 
                                    id="bagian" 
                                    x-model="bagian"
                                    :required="isKaryawan()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('bagian') border-red-500 @enderror">
                                <option value="">Pilih Bagian</option>
                                <option value="PGB" {{ old('bagian') === 'PGB' ? 'selected' : '' }}>
                                    PGB (Pengembangan)
                                </option>
                                <option value="PKJ" {{ old('bagian') === 'PKJ' ? 'selected' : '' }}>
                                    PKJ (Perizinan)
                                </option>
                            </select>
                            @error('bagian')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Pilih bagian tempat karyawan ini bekerja
                            </p>
                        </div>

                        {{-- ✅ Hidden Bagian Field (untuk role selain Karyawan) --}}
                        <input type="hidden" 
                               name="bagian_auto" 
                               :value="!isKaryawan() ? (role === 'supervisi' ? '' : (role === 'kabag_pgb' ? 'PGB' : (role === 'perizinan' ? 'PKJ' : ''))) : ''">

                        {{-- ✅ Email (Opsional - Auto-generate) --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-gray-400 text-xs">(Opsional)</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   placeholder="Kosongkan untuk auto-generate (contoh: namalengkap@bpdbali.co.id)"
                                   class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:bg-white @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Jika dikosongkan, email akan otomatis digenerate dari nama user
                            </p>
                        </div>

                        {{-- ✅ Password (Opsional - Auto-generate) --}}
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-gray-400 text-xs">(Opsional)</span>
                            </label>
                            <input type="text" 
                                   name="password" 
                                   id="password"
                                   value="{{ old('password') }}"
                                   placeholder="Kosongkan untuk password default: Password123"
                                   class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:bg-white @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Jika dikosongkan, password default <strong>Password123</strong> akan digunakan
                            </p>
                        </div>

                        {{-- ✅ Buttons (Fixed Spacing) --}}
                        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                BATAL
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                SIMPAN USER
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>