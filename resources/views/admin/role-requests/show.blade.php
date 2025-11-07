<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Role Request') }}
            </h2>
            <a href="{{ route('admin.role-requests.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Status Badge --}}
            <div class="mb-6 flex items-center justify-between">
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $roleRequest->status_color }}">
                    Status: {{ ucfirst($roleRequest->status) }}
                </span>
                <span class="text-sm text-gray-500">
                    ID Request: #{{ $roleRequest->id }}
                </span>
            </div>

            {{-- User Information --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi User</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-green-800">
                                            {{ strtoupper(substr($roleRequest->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">{{ $roleRequest->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $roleRequest->user->email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Role Saat Ini:</span>
                                <span class="font-medium text-gray-900">{{ ucfirst($roleRequest->user->role) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Daftar:</span>
                                <span class="font-medium text-gray-900">{{ $roleRequest->user->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pengajuan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Role yang Diajukan</label>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $roleRequest->getRoleLabel() }}
                                </span>
                                @if($roleRequest->requested_bagian)
                                    <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        Bagian: {{ $roleRequest->requested_bagian }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $roleRequest->created_at->format('d F Y, H:i') }} WITA
                                <span class="text-gray-500">({{ $roleRequest->created_at->diffForHumans() }})</span>
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Deskripsi/Alasan Pengajuan</label>
                            <div class="mt-1 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-900 whitespace-pre-line">{{ $roleRequest->deskripsi }}</p>
                            </div>
                        </div>

                        @if($roleRequest->dokumen_path)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Dokumen Pendukung</label>
                                <div class="mt-1">
                                    <a href="{{ Storage::url($roleRequest->dokumen_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                                        </svg>
                                        Lihat Dokumen
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Approval Information (jika sudah diproses) --}}
            @if(!$roleRequest->isPending())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi {{ ucfirst($roleRequest->status) }}</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600">Diproses Oleh:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $roleRequest->approver ? $roleRequest->approver->name : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600">Tanggal Diproses:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $roleRequest->approved_at ? $roleRequest->approved_at->format('d M Y, H:i') : 'N/A' }}
                                </span>
                            </div>
                            @if($roleRequest->admin_notes)
                                <div>
                                    <span class="text-sm text-gray-600 block mb-2">Catatan Admin:</span>
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-sm text-gray-900">{{ $roleRequest->admin_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            @if($roleRequest->isPending())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Approve Button --}}
                            <button type="button" 
                                    onclick="openApproveModal()"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve Request
                            </button>

                            {{-- Reject Button --}}
                            <button type="button"
                                    onclick="openRejectModal()"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reject Request
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Delete Button (untuk approved/rejected) --}}
            @if(!$roleRequest->isPending())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 border-t border-gray-200">
                        <form method="POST" action="{{ route('admin.role-requests.destroy', $roleRequest) }}" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus history request ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus History
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Approve Modal --}}
        <div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 text-center mt-4">Approve Role Request?</h3>
                    <p class="text-sm text-gray-600 text-center mt-2">
                        User akan mendapat role <strong>{{ $roleRequest->getRoleLabel() }}</strong>
                        @if ($roleRequest->requested_bagian)
                            dengan bagian <strong>{{ $roleRequest->requested_bagian }}</strong>
                        @endif
                    </p>

                    <form method="POST" action="{{ route('admin.role-requests.approve', $roleRequest) }}" class="mt-4">
                        @csrf
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                Confirm Approve
                            </button>
                            <button type="button" onclick="closeApproveModal()"
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 text-center mt-4">Reject Role Request?</h3>
                    <p class="text-sm text-gray-600 text-center mt-2">
                        User akan tetap sebagai <strong>Guest</strong> dan akan diberitahu tentang penolakan.
                    </p>

                    {{-- FIXED: Form method dan action sudah benar --}}
                    <form method="POST" action="{{ route('admin.role-requests.reject', $roleRequest) }}" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            {{-- FIXED: Gunakan name yang sesuai dengan Controller --}}
                            <textarea name="rejection_reason" rows="4" required placeholder="Berikan alasan penolakan yang jelas..." class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 shadow-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 resize-none"></textarea>
                            
                            {{-- FIXED: Error handling dengan field name yang benar --}}
                            @error('rejection_reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            <div class="flex items-start gap-2 mt-2">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-xs text-gray-600">
                                    Minimal 10 karakter. Alasan ini akan dilihat oleh user saat mereka login.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 ease-in-out">
                                Confirm Reject
                            </button>
                            <button type="button" 
                                    onclick="closeRejectModal()"
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200 ease-in-out">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openApproveModal() {
                document.getElementById('approveModal').classList.remove('hidden');
            }
            
            function closeApproveModal() {
                document.getElementById('approveModal').classList.add('hidden');
            }
            
            function openRejectModal() {
                document.getElementById('rejectModal').classList.remove('hidden');
            }
            
            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }
        </script>
    </div>
</x-app-layout>