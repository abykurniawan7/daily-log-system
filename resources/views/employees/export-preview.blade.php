<x-app-layout>
    <x-slot name="title">{{ __('employees.preview_export_pdf_title') }} - {{ $employee->name }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <a href="{{ route('employees.show', $employee) }}"
                       class="inline-flex items-center text-sm font-medium hover:text-green-700 transition-colors"
                       style="color: var(--primary-green, #0F5132);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('employees.back_to_detail') }}
                    </a>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ __('employees.preview_export_pdf_title') }} - {{ $employee->name }}
                    </h2>
                </div>

                {{-- Export Button --}}
                {{-- <a href="{{ route('employees.export-pdf', $employee) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
                   style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('employees.download_pdf_now') }}
                </a> --}}
            </div>

            {{-- Info Alert --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            {!! __('employees.preview_info') !!}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Preview Container (Paper-like) --}}
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
                {{-- PDF Preview Content --}}
                <div class="bg-white p-12" style="min-height: 842px;"> <!-- A4 Height simulation -->
                    
                    {{-- Header Laporan --}}
                    <div class="text-center mb-8 pb-6 border-b-2" style="border-color: var(--primary-green, #0F5132);">
                        <h1 class="text-3xl font-bold mb-2" style="color: var(--primary-green, #0F5132);">
                            {{ __('employees.performance_report') }}
                        </h1>
                        <p class="text-gray-600">{{ __('employees.bank_name') }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ __('employees.period') }}: {{ now()->format('d F Y') }}
                        </p>
                    </div>

                    {{-- Employee Profile --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4" style="color: var(--primary-green, #0F5132);">
                            {{ __('employees.employee_information') }}
                        </h2>
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-6 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('employees.full_name') }}</p>
                                <p class="font-semibold text-gray-900">{{ $employee->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('employees.email') }}</p>
                                <p class="font-semibold text-gray-900">{{ $employee->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('employees.role') }}</p>
                                <p class="font-semibold text-gray-900">
                                    @if($employee->role === 'karyawan')
                                        {{ __('employees.employee_pgb_role') }}
                                    @elseif($employee->role === 'kabag_pgb')
                                        {{ __('employees.kabag_pgb_role') }}
                                    @else
                                        {{ __('employees.licensing_pkj_role') }}
                                    @endif
                                </p>
                            </div>
                            @if($employee->bagian)
                            <div>
                                <p class="text-sm text-gray-600">{{ __('employees.section') }}</p>
                                <p class="font-semibold text-gray-900">{{ $employee->bagian }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4" style="color: var(--primary-green, #0F5132);">
                            {{ __('employees.performance_summary') }}
                        </h2>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center border-2" style="border-color: var(--primary-green-lighter, #D1FAE5);">
                                <p class="text-3xl font-bold mb-1" style="color: var(--primary-green, #0F5132);">
                                    {{ $stats['total_projects'] }}
                                </p>
                                <p class="text-sm text-gray-600">{{ __('employees.total_projects') }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center border-2 border-green-200">
                                <p class="text-3xl font-bold text-green-600 mb-1">{{ $stats['total_activities'] }}</p>
                                <p class="text-sm text-gray-600">{{ __('employees.total_activities') }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center border-2 border-purple-200">
                                <p class="text-3xl font-bold text-purple-600 mb-1">{{ $stats['completion_rate'] }}%</p>
                                <p class="text-sm text-gray-600">{{ __('employees.completion_rate') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Projects List --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4" style="color: var(--primary-green, #0F5132);">
                            {{ __('employees.projects_list') }}
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('employees.no') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('employees.project_name') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('employees.status') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('employees.start_date') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('employees.deadline') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($projects as $index => $project)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $project->nama_project }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($project->status === 'Done')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ __('employees.completed') }}
                                                </span>
                                            @elseif($project->status === 'Progress')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ __('employees.in_progress') }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('employees.pending') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($project->tanggal_inisiasi)->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $project->target_implementasi }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                            {{ __('employees.no_projects_assigned') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-12 pt-6 border-t border-gray-300 text-center text-sm text-gray-500">
                        <p>{{ __('employees.document_footer') }}</p>
                        <p class="mt-1">{{ __('employees.generated_date') }}: {{ now()->format('d F Y, H:i') }} WITA</p>
                    </div>

                </div>
            </div>

            {{-- Bottom Action Buttons --}}
            <div class="flex items-center justify-between bg-white p-6 rounded-lg shadow-sm">
                <a href="{{ route('employees.show', $employee) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('employees.back') }}
                </a>

                <a href="{{ route('employees.export-pdf', $employee) }}" 
                   class="inline-flex items-center gap-2 px-8 py-3 rounded-lg text-base font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
                   style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('employees.download_pdf_now') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>