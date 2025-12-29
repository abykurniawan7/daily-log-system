<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Password Reset Request Detail') }}
            </h2>
            <a href="{{ route('admin.password-requests.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Success -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Alert Error -->
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="mb-6">
                        @if($passwordResetRequest->status === 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                🕐 Pending - Menunggu Approval
                            </span>
                        @elseif($passwordResetRequest->status === 'approved')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                ✓ Approved
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                ✗ Rejected
                            </span>
                        @endif
                    </div>

                    <!-- User Information -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi User</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $passwordResetRequest->user->name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email (Asli - Didekripsi)</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">
                                    {{ $passwordResetRequest->email }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100">
                                        {{ ucfirst($passwordResetRequest->user->role) }}
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bagian</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $passwordResetRequest->user->bagian ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Request Information -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Request</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alasan Reset Password</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">
                                    {{ $passwordResetRequest->reason }}
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Request</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $passwordResetRequest->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                
                                @if($passwordResetRequest->processed_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Diproses</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ $passwordResetRequest->processed_at->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Admin Response (if exists) -->
                    @if($passwordResetRequest->admin_response)
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Respon Admin</h3>
                            <p class="text-sm text-gray-900 bg-blue-50 p-3 rounded border border-blue-200">
                                {{ $passwordResetRequest->admin_response }}
                            </p>
                        </div>
                    @endif

                    <!-- New Password (if approved) -->
                    @if($passwordResetRequest->status === 'approved' && $passwordResetRequest->new_password)
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Password Baru</h3>
                            <div class="bg-green-50 border border-green-200 rounded p-4">
                                <p class="text-sm text-gray-700 mb-2">Password baru user:</p>
                                <p class="text-lg font-mono font-bold text-green-800">{{ $passwordResetRequest->new_password }}</p>
                                <p class="text-xs text-gray-600 mt-2">⚠️ Berikan password ini kepada user. User dapat mengubahnya setelah login.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons (Only for Pending) -->
                    @if($passwordResetRequest->status === 'pending')
                        <div class="flex space-x-4">
                            <!-- Approve Form -->
                            <form action="{{ route('admin.password-requests.approve', $passwordResetRequest) }}" 
                                  method="POST" 
                                  class="flex-1"
                                  onsubmit="return confirm('Apakah Anda yakin ingin APPROVE request ini? Password baru akan digenerate otomatis.')">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password Baru (Opsional - akan auto-generate jika kosong)
                                    </label>
                                    <input type="text" 
                                           name="new_password" 
                                           id="new_password" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                           placeholder="Biarkan kosong untuk generate otomatis">
                                    <p class="mt-1 text-xs text-gray-500">Format: minimal 8 karakter. Kosongkan untuk generate random password.</p>
                                </div>

                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Approve & Reset Password
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <form action="{{ route('admin.password-requests.reject', $passwordResetRequest) }}" 
                                  method="POST" 
                                  class="flex-1"
                                  onsubmit="return confirm('Apakah Anda yakin ingin REJECT request ini?')">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                                        Alasan Reject (Opsional)
                                    </label>
                                    <textarea name="admin_response" 
                                              id="admin_response" 
                                              rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                              placeholder="Berikan alasan mengapa request ditolak..."></textarea>
                                </div>

                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Reject Request
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>