<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('profile.page_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Messages --}}
            @if (session('status') === 'profile-updated')
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ __('profile.profile_updated') }}</span>
                </div>
            @endif
            
            @if (session('status') === 'password-updated')
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ __('profile.password_updated') }}</span>
                </div>
            @endif

            {{-- Profile Header (Read-Only) --}}
            <div class="bg-gradient-to-r from-[#0F5132] to-[#1B6B47] overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-4xl font-bold text-white relative">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @if(auth()->user()->role === 'admin')
                                    <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center border-4 border-white">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- User Info --}}
                        <div class="ml-6 flex-1">
                            <h3 class="text-2xl font-bold text-white">{{ auth()->user()->name }}</h3>
                            <p class="text-green-100 text-sm mt-1">{{ auth()->user()->email }}</p>
                            
                            <div class="flex gap-3 mt-3">
                                @if(auth()->user()->role === 'admin')
                                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('profile.administrator') }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full">
                                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                    </span>
                                    @if(auth()->user()->bagian)
                                        <span class="px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full">
                                            {{ auth()->user()->bagian }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'admin')
                {{-- ADMIN: Profile Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#0F5132]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('profile.account_info') }}</h3>
                            </div>
                            
                            <button @click="showLogoutModal()" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                {{ __('profile.logout') }}
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="max-w-2xl">
                            <header class="mb-6">
                                <p class="text-sm text-gray-600">
                                    {{ __('profile.update_account_description') }}
                                </p>
                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="text-sm text-blue-700">
                                            <p class="font-semibold">{{ __('profile.admin_info_title') }}</p>
                                            <p class="mt-1">{{ __('profile.admin_info_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </header>
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- ✅ TAMBAHKAN: Change Password Section untuk Admin --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#0F5132]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('profile.change_password') }}</h3>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="max-w-2xl">
                            <header class="mb-6">
                                <p class="text-sm text-gray-600">
                                    {{ __('profile.change_password_description') }}
                                </p>
                            </header>
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

            @else
                {{-- NON-ADMIN: Full Profile with Tabs --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div x-data="{ activeTab: 'akun' }">
                        {{-- Tab Navigation --}}
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px">
                                <button @click="activeTab = 'akun'" 
                                        :class="activeTab === 'akun' ? 'border-[#0F5132] text-[#0F5132]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition">
                                    <svg class="w-5 h-5 mr-2" :class="activeTab === 'akun' ? 'text-[#0F5132]' : 'text-gray-400 group-hover:text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ __('profile.account_tab') }}
                                </button>
                                
                                <button @click="activeTab = 'keamanan'" 
                                        :class="activeTab === 'keamanan' ? 'border-[#0F5132] text-[#0F5132]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition">
                                    <svg class="w-5 h-5 mr-2" :class="activeTab === 'keamanan' ? 'text-[#0F5132]' : 'text-gray-400 group-hover:text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    {{ __('profile.security_tab') }}
                                </button>
                                
                                <button @click="showLogoutModal()" 
                                        class="group inline-flex items-center py-4 px-6 border-b-2 border-transparent text-red-600 hover:text-red-700 hover:border-red-300 font-medium text-sm transition ml-auto">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('profile.logout_tab') }}
                                </button>
                            </nav>
                        </div>

                        {{-- Tab Content --}}
                        <div class="p-6">
                            {{-- TAB: Akun --}}
                            <div x-show="activeTab === 'akun'" x-transition>
                                <div class="max-w-2xl">
                                    <header class="mb-6">
                                        <h2 class="text-lg font-semibold text-gray-900">{{ __('profile.update_account_info') }}</h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('profile.update_account_description') }}
                                        </p>
                                    </header>
                                    @include('profile.partials.update-profile-information-form')
                                </div>
                            </div>

                            {{-- TAB: Keamanan --}}
                            <div x-show="activeTab === 'keamanan'" x-transition style="display: none;">
                                <div class="max-w-2xl space-y-6">
                                    {{-- Change Password --}}
                                    <div>
                                        <header class="mb-6">
                                            <h2 class="text-lg font-semibold text-gray-900">{{ __('profile.change_password') }}</h2>
                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ __('profile.change_password_description') }}
                                            </p>
                                        </header>
                                        @include('profile.partials.update-password-form')
                                    </div>

                                    {{-- Danger Zone --}}
                                    <div class="pt-6 border-t border-gray-200">
                                        <div class="border-2 border-red-200 rounded-lg">
                                            <div class="p-4">
                                                <div x-data="{ dangerZoneOpen: false }">
                                                    {{-- Header dengan Toggle Button --}}
                                                    <div class="flex items-center justify-between mb-4">
                                                        <div class="flex items-center">
                                                            <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                            </svg>
                                                            <h3 class="text-lg font-semibold text-red-900">
                                                                {{ __('profile.danger_zone') }}
                                                            </h3>
                                                        </div>
                                                        
                                                        <button @click="dangerZoneOpen = !dangerZoneOpen" 
                                                                type="button"
                                                                class="inline-flex items-center px-4 py-2 bg-red-100 border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest hover:bg-red-200 focus:bg-red-200 active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                            <span x-text="dangerZoneOpen ? '{{ __('profile.hide_danger_zone') }}' : '{{ __('profile.show_danger_zone') }}'"></span>
                                                            <svg class="w-4 h-4 ml-2 transition-transform" :class="dangerZoneOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    
                                                    <p class="text-sm text-gray-600 mb-4">
                                                        {{ __('profile.danger_zone_description') }}
                                                    </p>
                                                    
                                                    {{-- Danger Zone Content - Collapsible --}}
                                                    <div x-show="dangerZoneOpen" 
                                                         x-transition:enter="transition ease-out duration-200"
                                                         x-transition:enter-start="opacity-0 transform scale-95"
                                                         x-transition:enter-end="opacity-100 transform scale-100"
                                                         x-transition:leave="transition ease-in duration-150"
                                                         x-transition:leave-start="opacity-100 transform scale-100"
                                                         x-transition:leave-end="opacity-0 transform scale-95"
                                                         class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg"
                                                         style="display: none;">
                                                        @include('profile.partials.delete-user-form')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Logout Confirmation Modal --}}
    <div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50" onclick="closeLogoutModal(event)">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white" onclick="event.stopPropagation()">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('profile.logout_confirmation') }}
                    </h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            {{ __('profile.logout_message') }}
                        </p>
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button onclick="closeLogoutModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">
                            {{ __('profile.cancel') }}
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                                {{ __('profile.yes_logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeLogoutModal(event) {
            const modal = document.getElementById('logoutModal');
            if (!event || event.target === modal || event.type === 'click') {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>
</x-app-layout>