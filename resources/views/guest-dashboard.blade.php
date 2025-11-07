<x-app-layout>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-2xl font-bold text-green-800">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ __('guest.welcome') }}, {{ Auth::user()->name }}!
                            </h2>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                {{ __('guest.guest_user') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $pendingRequest = Auth::user()->pendingRoleRequest();
                $latestRequest = Auth::user()->latestRoleRequest();
            @endphp

            {{-- FIXED: Show Rejection Alert --}}
            @if($latestRequest && $latestRequest->isRejected())
                <div class="bg-red-50 border-l-4 border-red-400 p-6 mb-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-red-800">
                                {{ __('guest.request_rejected') }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p class="mb-2">{{ __('guest.request_rejected_message', ['role' => $latestRequest->getRoleLabel()]) }}</p>
                                <p class="mb-3"><strong>{{ __('guest.rejection_reason') }}:</strong></p>
                                <div class="bg-white rounded p-3 border border-red-200 mb-3">
                                    <p class="text-red-800">{{ $latestRequest->rejection_reason }}</p>
                                </div>
                                <p class="text-xs text-red-600 italic">{{ __('guest.rejection_note') }}</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('role-requests.create') }}" 
                                   class="text-sm text-red-600 hover:text-red-800 font-medium">
                                    {{ __('guest.resubmit') }} →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Show Approved Alert --}}
            @if($latestRequest && $latestRequest->isApproved())
                <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-green-800">
                                {{ __('guest.request_approved') }}
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p class="mb-2">{{ __('guest.request_approved_message', ['role' => $latestRequest->getRoleLabel()]) }}</p>
                                <p class="mb-2">{{ __('guest.request_approved_access') }}</p>
                                <p class="text-xs text-green-600 italic">{{ __('guest.request_approved_note') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($pendingRequest)
                {{-- Pending Request Status --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-6 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-yellow-800">
                                {{ __('guest.request_pending') }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p class="mb-2">{{ __('guest.request_pending_role', ['role' => $pendingRequest->getRoleLabel()]) }}</p>
                                <p class="mb-2">{{ __('guest.request_pending_date', ['date' => $pendingRequest->created_at->format('d M Y, H:i')]) }}</p>
                                <p class="text-xs text-yellow-600 italic">{{ __('guest.request_pending_note') }}</p>
                            </div>
                            <div class="mt-4">
                                <form action="{{ route('role-requests.cancel', $pendingRequest) }}" method="POST" 
                                      onsubmit="return confirm('{{ __('guest.cancel_request_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        {{ __('guest.cancel_request') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Role Request Call-to-Action --}}
                <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ __('guest.get_full_access') }}
                            </h3>
                            <p class="text-sm text-gray-700 mb-4">
                                {{ __('guest.limited_access_description') }}
                            </p>
                            <a href="{{ route('role-requests.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('guest.request_role_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Limited Access Notice --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('guest.limited_access') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('guest.limited_access_intro') }}
                        </p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('guest.access_view_dashboard') }}
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('guest.access_manage_profile') }}
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('guest.access_request_role') }}
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Contact Admin --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('guest.need_help') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('guest.need_help_description') }}
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ __('guest.admin_email') }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('guest.office_hours') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('guest.quick_menu') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 ease-in-out border border-gray-200">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ __('guest.manage_profile') }}</p>
                                <p class="text-xs text-gray-500">{{ __('guest.manage_profile_description') }}</p>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('{{ __('guest.logout_confirm') }}')">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 ease-in-out border border-gray-200 text-left">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ __('guest.logout') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('guest.logout_description') }}</p>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>