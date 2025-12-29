<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🛡️ Admin Dashboard
                </h2>
                <p class="text-sm text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}!</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Welcome Card --}}
            <div class="bg-gradient-to-r from-red-500 to-red-600 overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">👋 Selamat Datang, Administrator!</h3>
                            <p class="text-red-100 text-lg">
                                Kelola sistem WorkLog dengan mudah dan efisien melalui panel kontrol di bawah ini.
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <svg class="w-24 h-24 text-red-200 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Access Section --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📌 Quick Access</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- User Management Card --}}
                    <a href="{{ route('admin.users.index') }}" 
                       class="block bg-white overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg border-2 border-transparent hover:border-blue-500 group">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3 group-hover:bg-blue-500 transition-colors duration-300">
                                    <svg class="h-10 w-10 text-blue-600 group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                    </svg>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            
                            <h4 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                                👥 User Management
                            </h4>
                            <p class="text-gray-600 mb-4">
                                Kelola semua user sistem, tambah user baru, edit role & bagian, reset password.
                            </p>
                            
                            {{-- Stats --}}
                            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['total'] }}</p>
                                    <p class="text-xs text-gray-500">Total User</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $stats['users']['perizinan'] }}</p>
                                    <p class="text-xs text-gray-500">PKJ</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['users']['karyawan'] }}</p>
                                    <p class="text-xs text-gray-500">PGB</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Role Requests Card --}}
                    <a href="{{ route('admin.role-requests.index') }}" 
                       class="block bg-white overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg border-2 border-transparent hover:border-yellow-500 group">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3 group-hover:bg-yellow-500 transition-colors duration-300">
                                    <svg class="h-10 w-10 text-yellow-600 group-hover:text-white transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center">
                                    @if($stats['role_requests']['pending'] > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-500 text-white animate-pulse">
                                            {{ $stats['role_requests']['pending'] }} Pending
                                        </span>
                                    @endif
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-yellow-600 transition-colors duration-300 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h4 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-yellow-600 transition-colors duration-300">
                                📋 Role Requests
                            </h4>
                            <p class="text-gray-600 mb-4">
                                Kelola pengajuan role dari user guest, approve atau reject permintaan akses.
                            </p>
                            
                            {{-- Stats --}}
                            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['role_requests']['pending'] }}</p>
                                    <p class="text-xs text-gray-500">Pending</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $stats['role_requests']['approved'] }}</p>
                                    <p class="text-xs text-gray-500">Approved</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-red-600">{{ $stats['role_requests']['rejected'] }}</p>
                                    <p class="text-xs text-gray-500">Rejected</p>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>

            {{-- System Overview --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 System Overview</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        {{-- Admin --}}
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Admin</p>
                            <p class="text-2xl font-bold text-red-600">{{ $stats['users']['admin'] ?? 0 }}</p>
                        </div>
                        
                        {{-- Supervisi --}}
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Supervisi</p>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['users']['supervisi'] ?? 0 }}</p>
                        </div>
                        
                        {{-- ✅ REMOVED: Kabag PGB card dihapus --}}
                        
                        {{-- PKJ (Kabag + Staff) --}}
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">PKJ</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $stats['users']['perizinan'] ?? 0 }}</p>
                        </div>
                        
                        {{-- PGB (Kabag + Staff) --}}
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">PGB</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $stats['users']['karyawan'] ?? 0 }}</p>
                        </div>
                        
                        {{-- Guest --}}
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Guest</p>
                            <p class="text-2xl font-bold text-gray-600">{{ $stats['users']['guest'] ?? 0 }}</p>
                        </div>
                        
                        {{-- Total --}}
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Total</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['users']['total'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- Additional System Stats tetap sama --}}
                <div class="p-6 bg-gray-50">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center p-4 bg-white rounded-lg shadow-sm">
                            <svg class="w-8 h-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Total Projects</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['system']['total_projects'] ?? 0 }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-white rounded-lg shadow-sm">
                            <svg class="w-8 h-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Total Activities</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['system']['total_activities'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>