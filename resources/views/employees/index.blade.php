<x-app-layout>
    <x-slot name="title">{{ __('employees.page_title') }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header Summary --}}
            <div class="bg-gradient-to-r from-[#0F5132] to-[#1B6B47] overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold">{{ $employees->total() }} {{ __('employees.employees') }}</h2>
                            <p class="text-green-100 text-sm mt-1">{{ __('employees.total_registered') }}</p>
                        </div>
                        
                        {{-- Counter by category --}}
                        <div class="flex gap-6">
                            {{-- Kabag PGB --}}
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $countKabagPGB }}</div>
                                <div class="text-xs text-green-100 mt-1">{{ __('employees.kabag_pgb') }}</div>
                            </div>
                            
                            {{-- Kabag PKJ --}}
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $countKabagPKJ }}</div>
                                <div class="text-xs text-green-100 mt-1">{{ __('employees.kabag_pkj') }}</div>
                            </div>
                            
                            {{-- Karyawan PGB --}}
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $countKaryawanPGB }}</div>
                                <div class="text-xs text-green-100 mt-1">{{ __('employees.pgb_employee') }}</div>
                            </div>
                            
                            {{-- Karyawan PKJ --}}
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $countKaryawanPKJ }}</div>
                                <div class="text-xs text-green-100 mt-1">{{ __('employees.pkj_employee') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="{{ __('employees.search_placeholder') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        
                        <select name="bagian" class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">{{ __('employees.all_sections') }}</option>
                            <option value="PGB" {{ request('bagian') === 'PGB' ? 'selected' : '' }}>PGB</option>
                            <option value="PKJ" {{ request('bagian') === 'PKJ' ? 'selected' : '' }}>PKJ</option>
                        </select>
                        
                        <select name="role" class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">{{ __('employees.all_roles') }}</option>
                            <option value="kabag_pgb" {{ request('role') === 'kabag_pgb' ? 'selected' : '' }}>Kabag PGB</option>
                            <option value="perizinan" {{ request('role') === 'perizinan' ? 'selected' : '' }}>Kabag PKJ</option>
                            <option value="karyawan" {{ request('role') === 'karyawan' ? 'selected' : '' }}>{{ __('employees.employee') }}</option>
                        </select>
                        
                        <button type="submit" class="px-4 py-2 bg-[#0F5132] text-white rounded-lg hover:bg-[#0A3D24] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        
                        @if(request()->hasAny(['search', 'bagian', 'role']))
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                            {{ __('employees.reset') }}
                        </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Employee Cards Grid --}}
            @if($employees->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($employees as $employee)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
                    {{-- Card Header with gradient based on bagian --}}
                    @php
                        // Kabag (PGB & PKJ) = Amber/Emas
                        if ($employee->role === 'kabag_pgb') {
                            $headerGradient = 'from-[#0F5132] to-[#1B6B47]';
                        }

                        elseif ($employee->role === 'perizinan') {
                            $headerGradient = 'from-[#0F5132] to-[#1B6B47]';
                        }
                        // Karyawan PGB = Hijau
                        elseif ($employee->bagian === 'PGB') {
                            $headerGradient = 'from-[#0F5132] to-[#1B6B47]';
                        }
                        // Karyawan PKJ = Ungu
                        elseif ($employee->bagian === 'PKJ') {
                            $headerGradient = 'from-[#0F5132] to-[#1B6B47]';
                        }
                        else {
                            $headerGradient = 'from-gray-600 to-gray-700';
                        }
                    @endphp
                    
                    <div class="bg-gradient-to-r {{ $headerGradient }} p-4">
                        <div class="flex items-center gap-4">
                            {{-- Avatar --}}
                            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold text-white">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                            
                            {{-- Name & Email --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-white truncate">{{ $employee->name }}</h3>
                                <p class="text-sm text-white/80 truncate">{{ $employee->email }}</p>
                            </div>
                        </div>
                        
                        {{-- Role & Section Badges --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            {{-- Role Badge --}}
                            @if($employee->role === 'kabag_pgb')
                                <span class="px-2.5 py-1 bg-amber-200 text-amber-800 text-xs font-semibold rounded-full">
                                    {{ __('employees.kabag_pgb') }}
                                </span>
                            @elseif($employee->role === 'perizinan')
                                <span class="px-2.5 py-1 bg-amber-200 text-amber-800 text-xs font-semibold rounded-full">
                                    {{ __('employees.kabag_pkj') }}
                                </span>
                            @elseif($employee->role === 'karyawan')
                                <span class="px-2.5 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full">
                                    {{ __('employees.employee') }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full">
                                    {{ ucfirst($employee->role) }}
                                </span>
                            @endif
                            
                            {{-- Section Badge --}}
                            @if($employee->bagian === 'PGB')
                                <span class="px-2.5 py-1 bg-green-200 text-green-800 text-xs font-semibold rounded-full">
                                    {{ __('employees.section') }} PGB
                                </span>
                            @elseif($employee->bagian === 'PKJ')
                                <span class="px-2.5 py-1 bg-purple-200 text-purple-800 text-xs font-semibold rounded-full">
                                    {{ __('employees.section') }} PKJ
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Card Body - Stats --}}
                    <div class="p-4">
                        {{-- Projects & Activities Count --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            {{-- Total Projects - Warna Biru --}}
                            <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-100">
                                <div class="flex items-center justify-center gap-1.5 text-blue-600 text-xs mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ __('employees.projects') }}
                                </div>
                                <p class="text-2xl font-bold text-blue-700">{{ $employee->total_projects }}</p>
                            </div>
                            
                            {{-- ✅ UPDATED: Total Activities dengan label "All Activities" --}}
                            <div class="bg-green-50 rounded-lg p-3 text-center border border-green-100">
                                <div class="flex items-center justify-center gap-1.5 text-green-600 text-xs mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    {{ __('employees.all_activities') }}
                                </div>
                                <p class="text-2xl font-bold text-green-700">{{ $employee->total_activities }}</p>
                            </div>
                        </div>
                        
                        {{-- ✅ NEW: Activity Breakdown by Status (Balanced) --}}
                        <div class="mb-4">
                            <div class="text-xs font-medium text-gray-600 mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                {{ __('employees.activities_by_status') }}
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                {{-- Done --}}
                                <div class="bg-green-50 border border-green-200 rounded-lg px-2 py-1.5 text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $employee->activities_done }}</div>
                                    <div class="text-xs text-green-700">{{ __('employees.done_activities') }}</div>
                                </div>
                                {{-- Progress --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-lg px-2 py-1.5 text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $employee->activities_progress }}</div>
                                    <div class="text-xs text-blue-700">{{ __('employees.progress_activities') }}</div>
                                </div>
                                {{-- Pending --}}
                                <div class="bg-orange-50 border border-orange-200 rounded-lg px-2 py-1.5 text-center">
                                    <div class="text-lg font-bold text-orange-600">{{ $employee->activities_pending }}</div>
                                    <div class="text-xs text-orange-700">{{ __('employees.pending_activities') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Completion Rate Progress Bar --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ __('employees.completion_rate') }}</span>
                                <span class="font-semibold text-gray-900">{{ $employee->completion_rate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                @php
                                    $progressColor = $employee->completion_rate >= 75 ? 'bg-green-500' 
                                        : ($employee->completion_rate >= 50 ? 'bg-yellow-500' 
                                        : ($employee->completion_rate >= 25 ? 'bg-orange-500' : 'bg-red-500'));
                                @endphp
                                <div class="{{ $progressColor }} h-2.5 rounded-full transition-all duration-500" 
                                     style="width: {{ $employee->completion_rate }}%"></div>
                            </div>
                        </div>
                        
                        {{-- View Detail Button --}}
                        <a href="{{ route('employees.show', $employee) }}" 
                           class="block w-full text-center px-4 py-2.5 bg-[#0F5132] text-white font-semibold rounded-lg hover:bg-[#0A3D24] transition">
                            {{ __('employees.view_detail') }}
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-6">
                {{ $employees->appends(request()->query())->links() }}
            </div>
            @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('employees.no_employees') }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('employees.no_employees_desc') }}</p>
            </div>
            @endif
            
        </div>
    </div>
</x-app-layout>