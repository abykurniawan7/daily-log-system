<x-app-layout>
    <x-slot name="title">{{ __('employees.employee_detail') }} - {{ $employee->name }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header dengan jarak lebih kecil --}}
            <div class="space-y-2">
                {{-- Tombol Kembali --}}
                <div>
                    <a href="{{ route('employees.index') }}" 
                       class="inline-flex items-center text-sm font-medium hover:text-green-700 transition-colors"
                       style="color: var(--primary-green, #0F5132);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('employees.back_to_list') }}
                    </a>
                </div>

                {{-- Judul Halaman --}}
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ __('employees.employee_detail') }} - {{ $employee->name }}
                </h2>
            </div>

            {{-- Employee Profile Card --}}
            <div class="rounded-lg shadow-lg p-8 text-white" 
                 style="background: linear-gradient(135deg, var(--primary-green, #0F5132) 0%, var(--primary-green-light, #1B6B47) 100%);">
                <div class="flex items-center space-x-6">
                    {{-- Avatar --}}
                    <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-4xl font-bold backdrop-blur-sm">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                    
                    {{-- Employee Info --}}
                    <div class="flex-1">
                        <h2 class="text-3xl font-bold mb-2">{{ $employee->name }}</h2>
                        <p class="text-white/90 mb-1">{{ $employee->email }}</p>
                        <div class="flex items-center gap-4 mt-3">
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                                {{ $employee->role === 'karyawan' ? __('employees.employee_pgb') : __('employees.licensing_pkj') }}
                            </span>
                            @if($employee->bagian)
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                                {{ __('employees.section') }} {{ $employee->bagian }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Total Projects --}}
                <div class="bg-white rounded-lg shadow-sm p-6 border-t-4" style="border-color: var(--primary-green, #0F5132);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">{{ __('employees.total_projects') }}</p>
                            <p class="text-3xl font-bold" style="color: var(--primary-green, #0F5132);">
                                {{ $stats['total_projects'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                             style="background-color: var(--primary-green-lighter, #D1FAE5);">
                            <svg class="w-6 h-6" style="color: var(--primary-green, #0F5132);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Activities --}}
                <div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">{{ __('employees.total_activities') }}</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['total_activities'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Completion Rate --}}
                <div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">{{ __('employees.completion_rate') }}</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['completion_rate'] }}%</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Projects Table --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('employees.projects_worked_on') }}</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('employees.project_name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('employees.status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('employees.start_date') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('employees.deadline') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('employees.activities') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('employees.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($employee->projects as $project)
                            <tr class="hover:bg-green-50 transition-colors cursor-pointer group" 
                                onclick="window.location='{{ route('projects.show', $project) }}';">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $project->nama_project }}</div>
                                    @if($project->deskripsi)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($project->deskripsi, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($project->status === 'Done')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ __('employees.completed') }}
                                        </span>
                                    @elseif($project->status === 'Progress')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ __('employees.in_progress') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ __('employees.pending') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($project->tanggal_mulai)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-semibold">{{ $project->activities->count() }}</span> {{ __('employees.activity_count') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $project->activities->where('status', 'Done')->count() }} {{ __('employees.completed_count') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('projects.show', $project) }}" 
                                        class="inline-flex items-center gap-1 font-medium hover:underline group-hover:text-green-700 transition-colors"
                                        style="color: var(--primary-green, #0F5132);"
                                        onclick="event.stopPropagation();">
                                        {{ __('employees.view_detail') }}
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">{{ __('employees.no_projects') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tombol Preview Export PDF di Kanan Bawah --}}
                @if($employee->projects->isNotEmpty())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('employees.export-preview', $employee) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
                       style="background: linear-gradient(135deg, var(--primary-green, #0F5132) 0%, var(--primary-green-light, #1B6B47) 100%);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('employees.preview_export_pdf') }}
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>