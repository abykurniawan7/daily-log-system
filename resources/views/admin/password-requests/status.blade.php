<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Status Request Reset Password') }}
            </h2>
            <a href="{{ route('user.password-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Request Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($requests->count() > 0)
                        <div class="space-y-4">
                            @foreach($requests as $request)
                                <div class="border rounded-lg p-4 {{ 
                                    $request->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : 
                                    ($request->status === 'approved' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') 
                                }}">
                                    <!-- Status Badge -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            @if($request->status === 'pending')
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    🕐 Menunggu Approval Admin
                                                </span>
                                            @elseif($request->status === 'approved')
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Disetujui
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    ✗ Ditolak
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            {{ $request->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>

                                    <!-- Request Info -->
                                    <div class="mb-3">
                                        <label class="text-xs font-medium text-gray-500 uppercase">Alasan Request</label>
                                        <p class="text-sm text-gray-700 mt-1">{{ $request->reason }}</p>
                                    </div>

                                    <!-- Admin Response (if rejected or has response) -->
                                    @if($request->admin_response)
                                        <div class="mt-3 p-3 {{ $request->status === 'rejected' ? 'bg-red-100 border border-red-200' : 'bg-blue-50 border border-blue-200' }} rounded">
                                            <label class="text-xs font-medium {{ $request->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }} uppercase">
                                                Respon Admin
                                            </label>
                                            <p class="text-sm {{ $request->status === 'rejected' ? 'text-red-800' : 'text-blue-800' }} mt-1">
                                                {{ $request->admin_response }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Approved - Show New Password -->
                                    @if($request->status === 'approved' && $request->new_password)
                                        <div class="mt-3 p-4 bg-green-100 border-2 border-green-300 rounded">
                                            <label class="text-sm font-medium text-green-800 mb-2 block">
                                                🔑 Password Baru Anda
                                            </label>
                                            <div class="bg-white p-3 rounded border border-green-300">
                                                <p class="text-xl font-mono font-bold text-green-800">{{ $request->new_password }}</p>
                                            </div>
                                            <p class="text-xs text-green-700 mt-2">
                                                ⚠️ Simpan password ini dengan aman. Anda dapat mengubahnya setelah login.
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Rejected - Action Suggestion -->
                                    @if($request->status === 'rejected')
                                        <div class="mt-3">
                                            <a href="{{ route('password.request') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                                Buat Request Baru
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Request</h3>
                            <p class="mt-1 text-sm text-gray-500">Anda belum pernah membuat request reset password.</p>
                            <div class="mt-6">
                                <a href="{{ route('password.request') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Buat Request Baru
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>