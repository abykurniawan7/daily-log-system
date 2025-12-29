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
    $pendingRequestsCount = 0;
    if(auth()->user()->role === 'admin') {
        $pendingRequestsCount = \App\Models\RoleRequest::where('status', 'pending')->count();
    }
    
    // ✅ KEY FIX: Staff PKJ = karyawan + bagian PKJ → same access as Kabag PKJ
    $user = auth()->user();
    $isStaffPKJ = $user->role === 'karyawan' && $user->bagian === 'PKJ';
@endphp

<ul class="space-y-2 font-medium">
    
    @if(auth()->user()->role === 'admin')
        {{-- ========================================
            MENU ADMIN - Simplified & Focused
        ======================================== --}}
        
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

        {{-- Role Requests --}}
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

        {{-- Password Reset Requests --}}
        <li>
            <a href="{{ route('admin.password-requests.index') }}" 
            class="flex items-center justify-between p-2 rounded-lg group {{ request()->routeIs('admin.password-requests.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.password-requests.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">Password Requests</span>
                </div>
                @php
                    $pendingPasswordRequests = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
                @endphp
                @if($pendingPasswordRequests > 0)
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                        {{ $pendingPasswordRequests }}
                    </span>
                @endif
            </a>
        </li>

    @elseif(auth()->user()->role === 'guest')
        {{-- ========================================
            MENU GUEST - Limited Access
        ======================================== --}}
        
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
        {{-- ========================================
            MENU SUPERVISI (Super Admin - Full Access PGB + PKJ)
        ======================================== --}}
        
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

        {{-- ✅ NEW: Dropdown Projects Menu --}}
        <li x-data="{ 
            open: {{ request()->routeIs('projects.*') ? 'true' : 'false' }} 
        }">
            {{-- Parent Menu --}}
            <button @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full p-2 rounded-lg group {{ request()->routeIs('projects.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('projects.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.projects') }}</span>
                </div>
                {{-- Arrow Icon --}}
                <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('projects.*') ? 'text-green-600' : 'text-gray-500' }}" 
                    :class="{ 'rotate-180': open }"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            {{-- Submenu --}}
            <ul x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="py-2 space-y-2 ml-6 border-l-2 pl-3"
                :class="open ? 'border-green-500' : 'border-gray-200'">
                
                {{-- My Projects --}}
                <li>
                    <a href="{{ route('projects.index', ['filter' => 'my']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->get('filter') === 'my' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->get('filter') === 'my' ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.my_projects') }}</span>
                    </a>
                </li>

                {{-- All Projects --}}
                <li>
                    <a href="{{ route('projects.index', ['filter' => 'all']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->get('filter') === 'all' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->get('filter') === 'all' ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.all_projects') }}</span>
                    </a>
                </li>
            </ul>
        </li>

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

        {{-- ✅ NEW: Dropdown Activities Menu (Employee Activities + My Activities) --}}
        <li x-data="{ 
            open: {{ request()->routeIs('activities.*') ? 'true' : 'false' }} 
        }">
            {{-- Parent Menu --}}
            <button @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full p-2 rounded-lg group {{ request()->routeIs('activities.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('activities.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.activities') }}</span>
                </div>
                {{-- Arrow Icon --}}
                <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('activities.*') ? 'text-green-600' : 'text-gray-500' }}" 
                    :class="{ 'rotate-180': open }"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            {{-- Submenu --}}
            <ul x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="py-2 space-y-2 ml-6 border-l-2 pl-3"
                :class="open ? 'border-green-500' : 'border-gray-200'">
                
                {{-- Employee Activities --}}
                <li>
                    <a href="{{ route('activities.index') }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('activities.index') && !request()->routeIs('activities.my-activities') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('activities.index') && !request()->routeIs('activities.my-activities') ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.employee_activities') }}</span>
                    </a>
                </li>

                {{-- My Activities --}}
                <li>
                    <a href="{{ route('activities.my-activities') }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('activities.my-activities') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('activities.my-activities') ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.my_activities') }}</span>
                    </a>
                </li>
            </ul>
        </li>

    @elseif(auth()->user()->role === 'kabag_pgb')
        {{-- ========================================
            MENU KABAG PGB (Leader PGB - NEW ROLE)
            - Bisa create project PGB
            - Lihat semua project (monitoring)
            - Dropdown: Employee Activities + My Activities
        ======================================== --}}

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

        {{-- ✅ NEW: Dropdown Projects Menu (My vs All) --}}
        <li x-data="{ 
            open: {{ request()->routeIs('projects.*') ? 'true' : 'false' }} 
        }">
            {{-- Parent Menu --}}
            <button @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full p-2 rounded-lg group {{ request()->routeIs('projects.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('projects.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.projects') }}</span>
                </div>
                {{-- Arrow Icon --}}
                <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('projects.*') ? 'text-green-600' : 'text-gray-500' }}" 
                    :class="{ 'rotate-180': open }"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            {{-- Submenu --}}
            <ul x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="py-2 space-y-2 ml-6 border-l-2 pl-3"
                :class="open ? 'border-green-500' : 'border-gray-200'">
                
                {{-- My Projects --}}
                <li>
                    <a href="{{ route('projects.index', ['filter' => 'my']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->get('filter') === 'my' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->get('filter') === 'my' ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.my_projects') }}</span>
                    </a>
                </li>

                {{-- All Projects --}}
                <li>
                    <a href="{{ route('projects.index', ['filter' => 'all']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('projects.index') && (request()->get('filter') === 'all' || !request()->has('filter')) ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('projects.index') && (request()->get('filter') === 'all' || !request()->has('filter')) ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.all_projects') }}</span>
                    </a>
                </li>
            </ul>
        </li>

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

        {{-- ✅ NEW: Dropdown Activities Menu --}}
        <li x-data="{ 
            open: {{ request()->routeIs('activities.*') ? 'true' : 'false' }} 
        }">
            {{-- Parent Menu --}}
            <button @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full p-2 rounded-lg group {{ request()->routeIs('activities.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('activities.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.activities') }}</span>
                </div>
                {{-- Arrow Icon --}}
                <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('activities.*') ? 'text-green-600' : 'text-gray-500' }}" 
                    :class="{ 'rotate-180': open }"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            {{-- Submenu --}}
            <ul x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="py-2 space-y-2 ml-6 border-l-2 pl-3"
                :class="open ? 'border-green-500' : 'border-gray-200'">
                
                {{-- Employee Activities --}}
                <li>
                    <a href="{{ route('activities.index') }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('activities.index') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('activities.index') ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.employee_activities') }}</span>
                    </a>
                </li>

                {{-- My Activities --}}
                <li>
                    <a href="{{ route('activities.my-activities') }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('activities.my-activities') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('activities.my-activities') ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.my_activities') }}</span>
                    </a>
                </li>
            </ul>
        </li>

    @elseif(auth()->user()->role === 'perizinan' || $isStaffPKJ)
        {{-- ========================================
            MENU PERIZINAN (Kabag PKJ)
            - Bisa create project PKJ
            - Dropdown Projects (My vs All)
            - Dropdown Activities (PKJ Activities vs My Activities)
        ======================================== --}}
        
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

        {{-- ✅ NEW: Dropdown Projects Menu (My vs All) --}}
        <li x-data="{ 
            open: {{ request()->routeIs('projects.*') ? 'true' : 'false' }} 
        }">
            {{-- Parent Menu --}}
            <button @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full p-2 rounded-lg group {{ request()->routeIs('projects.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('projects.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.projects') }}</span>
                </div>
                {{-- Arrow Icon --}}
                <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('projects.*') ? 'text-green-600' : 'text-gray-500' }}" 
                    :class="{ 'rotate-180': open }"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            {{-- Submenu --}}
            <ul x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="py-2 space-y-2 ml-6 border-l-2 pl-3"
                :class="open ? 'border-green-500' : 'border-gray-200'">
                
                {{-- My Projects --}}
                <li>
                    <a href="{{ route('projects.index', ['filter' => 'my']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->get('filter') === 'my' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->get('filter') === 'my' ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.my_projects') }}</span>
                    </a>
                </li>

                {{-- All Projects --}}
                <li>
                    <a href="{{ route('projects.index', ['filter' => 'all']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('projects.index') && request()->get('filter') === 'all' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('projects.index') && request()->get('filter') === 'all' ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.all_projects') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ✅ NEW: Menu Employee for PKJ --}}
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
        
        {{-- ✅ NEW: Dropdown Activities Menu (PKJ Activities vs My Activities) --}}
        <li x-data="{ 
            open: {{ request()->routeIs('activities.*') ? 'true' : 'false' }} 
        }">
            {{-- Parent Menu --}}
            <button @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full p-2 rounded-lg group {{ request()->routeIs('activities.*') ? 'menu-active' : 'text-gray-900 menu-hover' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('activities.*') ? 'icon-active' : 'text-gray-500 group-hover:text-gray-900' }}" 
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">{{ __('sidebar.activities') }}</span>
                </div>
                {{-- Arrow Icon --}}
                <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('activities.*') ? 'text-green-600' : 'text-gray-500' }}" 
                    :class="{ 'rotate-180': open }"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>

            {{-- Submenu --}}
            <ul x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="py-2 space-y-2 ml-6 border-l-2 pl-3"
                :class="open ? 'border-green-500' : 'border-gray-200'">
                
                {{-- PKJ Activities (All) --}}
                <li>
                    <a href="{{ route('activities.index', ['view_bagian' => 'PKJ']) }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->get('view_bagian') === 'PKJ' || (!request()->has('view_bagian') && request()->routeIs('activities.index')) ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->get('view_bagian') === 'PKJ' || (!request()->has('view_bagian') && request()->routeIs('activities.index')) ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.pkj_activities') }}</span>
                    </a>
                </li>

                {{-- My Activities --}}
                <li>
                    <a href="{{ route('activities.my-activities') }}" 
                    class="flex items-center p-2 pl-3 rounded-lg group {{ request()->routeIs('activities.my-activities') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4 mr-2 {{ request()->routeIs('activities.my-activities') ? 'text-green-600' : 'text-gray-400' }}" 
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ __('sidebar.my_activities') }}</span>
                    </a>
                </li>
            </ul>
        </li>

    @else
        {{-- ========================================
            MENU KARYAWAN (Staff PGB - Default)
            - View project saja (yang assigned)
            - Manage aktivitas sendiri
        ======================================== --}}
        
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
            @elseif(auth()->user()->role === 'supervisi')
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center border-2 border-white">
                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"></path>
                    </svg>
                </div>
            @elseif(auth()->user()->role === 'kabag_pgb')
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                    <span class="text-white text-xs font-bold">★</span>
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
                @elseif(auth()->user()->role === 'supervisi')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ __('sidebar.supervisi') }}
                    </span>
                @elseif(auth()->user()->role === 'kabag_pgb')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        ★ Kabag PGB
                    </span>
                @elseif(auth()->user()->role === 'guest')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                        {{ __('sidebar.guest') }}
                    </span>
                @else
                    {{ ucfirst(auth()->user()->role) }}
                    @if(auth()->user()->bagian)
                        <span class="text-gray-400">•</span> {{ auth()->user()->bagian }}
                    @endif
                @endif
            </p>
        </div>
    </div>
</div>