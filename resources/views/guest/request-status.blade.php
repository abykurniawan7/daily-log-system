<x-app-layout>
    <x-slot name="title">Status Pengajuan</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('guest.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📋 Status Pengajuan Role
                </h2>
                <p class="text-sm text-gray-600 mt-1">Detail pengajuan akses Anda</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($request)
                {{-- Status Card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Pengajuan</h3>
                            @php
                                $statusConfig = [
                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => '⏳', 'label' => 'Menunggu Review'],
                                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => '✅', 'label' => 'Disetujui'],
                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => '❌', 'label' => 'Ditolak'],
                                ];
                                $config = $statusConfig[$request->status];
                            @endphp
                            <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $config['bg'] }} {{ $config['text'] }}">
                                {{ $config['icon'] }} {{ $config['label'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Role yang Diminta</p>
                                <p class="text-base font-semibold text-gray-900">{{ ucfirst($request->requested_role) }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Bagian</p>
                                <p class="text-base font-semibold text-gray-900">{{ $request->requested_bagian ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Tanggal Pengajuan</p>
                                <p class="text-base font-semibold text-gray-900">{{ $request->created_at->format('d F Y, H:i') }}</p>
                            </div>

                            @if($request->reviewed_at)
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Tanggal Review</p>
                                <p class="text-base font-semibold text-gray-900">{{ $request->reviewed_at->format('d F Y, H:i') }}</p>
                            </div>
                            @endif

                            @if($request->reviewer)
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Direview oleh</p>
                                <p class="text-base font-semibold text-gray-900">{{ $request->reviewer->name }}</p>
                            </div>
                            @endif

                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600 mb-1">Alasan Pengajuan</p>
                                <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $request->reason }}</p>
                            </div>

                            @if($request->supporting_document)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600 mb-2">Dokumen Pendukung</p>
                                <a href="{{ asset('storage/' . $request->supporting_document) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Dokumen
                                </a>
                            </div>
                            @endif

                            @if($request->status === 'rejected' && $request->rejection_reason)
                            <div class="md:col-span-2">
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <p class="text-sm font-semibold text-red-800 mb-1">Alasan Penolakan:</p>
                                    <p class="text-sm text-red-700">{{ $request->rejection_reason }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Pengajuan</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                    ✓
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-semibold text-gray-900">Pengajuan Dibuat</p>
                                    <p class="text-sm text-gray-600">{{ $request->created_at->format('d F Y, H:i') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 {{ $request->status !== 'pending' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} rounded-full flex items-center justify-center">
                                    {{ $request->status !== 'pending' ? '✓' : '⏳' }}
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-semibold text-gray-900">
                                        {{ $request->status === 'pending' ? 'Menunggu Review Admin' : 'Direview oleh Admin' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        @if($request->reviewed_at)
                                            {{ $request->reviewed_at->format('d F Y, H:i') }}
                                        @else
                                            Sedang dalam antrian review
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($request->status !== 'pending')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 {{ $request->status === 'approved' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full flex items-center justify-center">
                                    {{ $request->status === 'approved' ? '✓' : '✗' }}
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-semibold text-gray-900">
                                        {{ $request->status === 'approved' ? 'Pengajuan Disetujui' : 'Pengajuan Ditolak' }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $request->reviewed_at->format('d F Y, H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if($request->status === 'rejected')
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('guest.request.create') }}" 
                       class="btn-blue">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Ajukan Ulang
                    </a>
                </div>
                @endif
            @else
                {{-- No Request --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengajuan</h3>
                        <p class="mt-1 text-sm text-gray-500">Anda belum mengajukan role akses.</p>
                        <div class="mt-6">
                            <a href="{{ route('guest.request.create') }}" class="btn-blue">
                                Ajukan Role Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>