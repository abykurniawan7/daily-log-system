<x-app-layout>
    <x-slot name="title">Ajukan Role</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('guest.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📋 Ajukan Role Akses
                </h2>
                <p class="text-sm text-gray-600 mt-1">Isi formulir untuk mengajukan akses ke sistem</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Info Box --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Penting:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan data yang Anda isi sesuai dengan kebutuhan dan posisi Anda</li>
                            <li>Pengajuan akan direview oleh Administrator dalam 1-3 hari kerja</li>
                            <li>Anda akan menerima notifikasi via email setelah pengajuan disetujui/ditolak</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('guest.request.store') }}" enctype="multipart/form-data" x-data="{ role: 'karyawan' }">
                        @csrf

                        {{-- Requested Role --}}
                        <div class="mb-6">
                            <label for="requested_role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role yang Diminta <span class="text-red-500">*</span>
                            </label>
                            <select name="requested_role" 
                                    id="requested_role" 
                                    x-model="role"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Role</option>
                                <option value="karyawan">Karyawan (PGB - Pengembangan)</option>
                                <option value="perizinan">Perizinan (PKJ)</option>
                                <option value="supervisi">Supervisi</option>
                            </select>
                            @error('requested_role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Pilih role sesuai dengan posisi/tugas Anda di Bank BPD Bali
                            </p>
                        </div>

                        {{-- Requested Bagian (Conditional) --}}
                        <div class="mb-6" x-show="role === 'karyawan' || role === 'perizinan'">
                            <label for="requested_bagian" class="block text-sm font-medium text-gray-700 mb-2">
                                Bagian <span class="text-red-500" x-show="role === 'karyawan' || role === 'perizinan'">*</span>
                            </label>
                            <select name="requested_bagian" 
                                    id="requested_bagian"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Bagian</option>
                                <option value="PGB" :selected="role === 'karyawan'">PGB (Pengembangan)</option>
                                <option value="PKJ" :selected="role === 'perizinan'">PKJ (Perizinan)</option>
                            </select>
                            @error('requested_bagian')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Bagian di divisi PGD (Pengembangan/Perizinan)
                            </p>
                        </div>

                        {{-- Reason --}}
                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Alasan Pengajuan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" 
                                      id="reason" 
                                      rows="5"
                                      required
                                      placeholder="Jelaskan alasan Anda mengajukan role ini, posisi/tugas Anda di bank, dan mengapa Anda memerlukan akses ke sistem WorkLog..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Minimal 50 karakter. Jelaskan dengan jelas dan detail.
                            </p>
                        </div>

                        {{-- Supporting Document (Optional) --}}
                        <div class="mb-6">
                            <label for="supporting_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Dokumen Pendukung <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="file" 
                                   name="supporting_document" 
                                   id="supporting_document"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('supporting_document')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Upload surat tugas, SK, atau dokumen pendukung lainnya (Max: 2MB, Format: PDF/JPG/PNG)
                            </p>
                        </div>

                        {{-- Info Box Preview --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200" x-show="role">
                            <h4 class="font-semibold text-gray-900 mb-2">Preview Pengajuan:</h4>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p><strong>Role:</strong> <span x-text="role === 'karyawan' ? 'Karyawan (PGB)' : (role === 'perizinan' ? 'Perizinan (PKJ)' : 'Supervisi')"></span></p>
                                <p x-show="role === 'karyawan' || role === 'perizinan'">
                                    <strong>Bagian:</strong> <span x-text="role === 'karyawan' ? 'PGB (Pengembangan)' : 'PKJ (Perizinan)'"></span>
                                </p>
                                <p><strong>Diajukan oleh:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t">
                            <a href="{{ route('guest.dashboard') }}" class="btn-gray">
                                Batal
                            </a>
                            <button type="submit" class="btn-blue">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>