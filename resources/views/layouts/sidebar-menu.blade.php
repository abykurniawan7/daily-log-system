<style>
    /* Green Theme Variables */
    :root {
        --primary-green: #0F5132;
        --primary-green-hover: #0A3D24;
        --primary-green-light: #1B6B47;
        --primary-green-lighter: #D1FAE5;
    }

    /* Active Menu Background */
    .menu-active {
        background-color: var(--primary-green-lighter) !important;
        color: var(--primary-green) !important;
    }

    /* Active Icon Color */
    .icon-active {
        color: var(--primary-green) !important;
    }

    /* Hover Effect */
    .menu-hover:hover {
        background-color: #F3F4F6;
    }
</style>

@php
    // Get pending role requests count untuk admin
    $pendingRequestsCount = 0;
    if(auth()->user()->role === 'admin') {
        $pendingRequestsCount = \App\Models\RoleRequest::where('status', 'pending')->count();
    }
@endphp

<ul class="space-y-2 font-medium">
    
    @if(auth()->user()->role === 'admin')
        {{-- MENU ADMIN - Simplified & Focused --}}
        
        {{-- Dashboard Admin --}}
        <li>
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('admin.dashboard') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.dashboard') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.dashboard') }}</span>
            </a>
        </li>
        
        {{-- User Management --}}
        <li>
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('admin.users.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.users.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.user_management') }}</span>
            </a>
        </li>

        {{-- Role Requests (NEW) --}}
        <li>
            <a href="{{ route('admin.role-requests.index') }}" 
               class="flex items-center justify-between p-2 rounded-lg group {{ request()->routeIs('admin.role-requests.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.role-requests.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                         fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.role_requests') }}</span>
                </div>
                @if($pendingRequestsCount > 0)
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-yellow-500 rounded-full">
                        {{ $pendingRequestsCount }}
                    </span>
                @endif
            </a>
        </li>

    @elseif(auth()->user()->role === 'guest')
        {{-- MENU GUEST - Limited Access --}}
        
        {{-- Dashboard --}}
        <li>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('dashboard') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.dashboard') }}</span>
            </a>
        </li>

    @elseif(auth()->user()->role === 'supervisi')
        {{-- MENU SUPERVISI --}}
        
        {{-- Dashboard --}}
        <li>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('dashboard') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.dashboard') }}</span>
            </a>
        </li>

        {{-- Projects --}}
        <li>
            <a href="{{ route('projects.index') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('projects.*') && !request()->routeIs('projects.*.activities.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('projects.*') && !request()->routeIs('projects.*.activities.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.projects') }}</span>
            </a>
        </li>

        {{-- Karyawan --}}
        <li>
            <a href="{{ route('employees.index') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('employees.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('employees.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.employees') }}</span>
            </a>
        </li>

        {{-- Aktivitas --}}
        <li>
            <a href="{{ route('activities.index') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('activities.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('activities.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.employee_activities') }}</span>
            </a>
        </li>

    @else
        {{-- MENU NON-SUPERVISI (Karyawan PGB & Perizinan PKJ) --}}
        
        {{-- Dashboard --}}
        <li>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('dashboard') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.dashboard') }}</span>
            </a>
        </li>

        {{-- Projects --}}
        <li>
            <a href="{{ route('projects.index') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('projects.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('projects.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.projects') }}</span>
            </a>
        </li>

        {{-- Aktivitas Saya --}}
        <li>
            <a href="{{ route('activities.my-activities') }}" 
               class="flex items-center p-2 rounded-lg group {{ request()->routeIs('activities.my-activities') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('activities.my-activities') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-3">{{ __('sidebar.my_activities') }}</span>
            </a>
        </li>

    @endif

    {{-- Divider --}}
    <li class="pt-4 mt-4 space-y-2 border-t border-gray-200">
        <span class="text-xs font-semibold text-gray-400 uppercase px-2">{{ __('sidebar.settings') }}</span>
    </li>

    {{-- Profile --}}
    <li>
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center p-2 rounded-lg group {{ request()->routeIs('profile.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('profile.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                 fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
            </svg>
            <span class="ml-3">{{ __('sidebar.profile') }}</span>
        </a>
    </li>
</ul>

{{-- User Info Card (Bottom) --}}
<div class="mt-6 pt-6 border-t border-gray-200">
    <div class="flex items-center p-2 text-sm text-gray-500">
        {{-- Avatar dengan Badge Role --}}
        <div class="relative">
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold" 
                 style="background-color: var(--primary-green-lighter); color: var(--primary-green);">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            @if(auth()->user()->role === 'admin')
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center border-2 border-white">
                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            @endif
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500">
                @if(auth()->user()->role === 'admin')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('sidebar.administrator') }}
                    </span>
                @elseif(auth()->user()->role === 'guest')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                        {{ __('sidebar.guest') }}
                    </span>
                @else
                    {{ __('sidebar.role_display.' . auth()->user()->role) }}
                    @if(auth()->user()->bagian)
                        <span class="text-gray-400">•</span> {{ auth()->user()->bagian }}
                    @endif
                @endif
            </p>
        </div>
    </div>
</div>