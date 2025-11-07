<x-app-layout>
    <x-slot name="title">{{ __('employees.page_title') }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('employees.page_title') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Summary Info -->
            <div class="mb-6 bg-gradient-to-r from-[#0F5132] to-[#1B6B47] rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-1">{{ $employees->total() }} {{ __('employees.total_employees') }}</h3>
                        <p class="text-green-100 text-sm">{{ __('employees.total_registered') }}</p>
                    </div>
                    <div class="hidden md:flex items-center gap-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $employees->where('role', 'karyawan')->count() }}</div>
                            <div class="text-xs text-green-100 mt-1">{{ __('employees.employee_pgb') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $employees->where('role', 'perizinan')->count() }}</div>
                            <div class="text-xs text-green-100 mt-1">{{ __('employees.licensing_pkj') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($employees as $employee)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-[#0F5132]">
                    <!-- Card Header with Gradient -->
                    <div class="bg-gradient-to-r from-[#0F5132] to-[#1B6B47] p-6 text-white">
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                            
                            <!-- Employee Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold mb-1 truncate">
                                    {{ $employee->name }}
                                </h3>
                                <p class="text-sm text-green-100 truncate">{{ $employee->email }}</p>
                            </div>
                        </div>
                        
                        <!-- Role & Bagian Badges -->
                        <div class="flex items-center gap-2 mt-4">
                            @if($employee->role === 'karyawan')
                                <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full">
                                    {{ __('employees.employee') }}
                                </span>
                            @else
                                <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full">
                                    {{ __('employees.licensing') }}
                                </span>
                            @endif
                            
                            @if($employee->bagian)
                                <span class="px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full">
                                    {{ __('employees.section') }} {{ $employee->bagian }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div class="bg-blue-50 p-4 rounded-lg text-center border border-blue-100">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-xs text-gray-600 font-medium">{{ __('employees.projects') }}</p>
                                </div>
                                <p class="text-2xl font-bold text-blue-600">{{ $employee->total_projects }}</p>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg text-center border border-green-100">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    <p class="text-xs text-gray-600 font-medium">{{ __('employees.activities') }}</p>
                                </div>
                                <p class="text-2xl font-bold text-green-600">{{ $employee->total_activities }}</p>
                            </div>
                        </div>

                        <!-- Performance Bar -->
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-600">{{ __('employees.completion_rate') }}</span>
                                <span class="text-sm font-bold text-[#0F5132]">{{ $employee->completion_rate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-[#0F5132] to-[#1B6B47] h-3 rounded-full transition-all duration-500 flex items-center justify-end pr-2" 
                                     style="width: {{ $employee->completion_rate }}%">
                                    @if($employee->completion_rate > 15)
                                        <span class="text-white text-[10px] font-bold">{{ $employee->completion_rate }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ✅ FIX: Pakai uuid bukan id -->
                        <a href="{{ route('employees.show', $employee->uuid) }}" 
                           class="block w-full text-center bg-[#0F5132] hover:bg-[#0A3D24] text-white py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-300 shadow-sm hover:shadow-md">
                            {{ __('employees.view_detail') }}
                        </a>
                    </div>
                </div>
                @empty
                <!-- Empty State -->
                <div class="col-span-full bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('employees.no_employees') }}</h3>
                    <p class="text-gray-600">{{ __('employees.no_employees_desc') }}</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
            <div class="mt-8">
                {{ $employees->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>