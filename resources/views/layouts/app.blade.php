<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- TITLE TAB BROWSER - DIGANTI --}}
        <title>{{ isset($title) ? $title . ' - Bank BPD Bali' : 'WorkLog - Bank BPD Bali' }}</title>

        {{-- FAVICON - ICON TAB BROWSER --}}
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Select2 CSS --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* BPD Bank Comprehensive Color Theme */
            :root {
                /* PRIMARY GREEN - Aktivitas & Main Actions */
                --primary-green: #0F5132;
                --primary-green-hover: #0A3D24;
                --primary-green-light: #1B6B47;
                --primary-green-lighter: #D1FAE5;
                --primary-green-dark: #062818;

                /* BLUE - Project Actions */
                --project-blue: #3B82F6;
                --project-blue-hover: #2563EB;
                --project-blue-light: #60A5FA;
                --project-blue-lighter: #DBEAFE;
                --project-blue-dark: #1E40AF;

                /* PURPLE - PKJ Related */
                --pkj-purple: #9333EA;
                --pkj-purple-hover: #7E22CE;
                --pkj-purple-light: #A855F7;
                --pkj-purple-lighter: #F3E8FF;
                --pkj-purple-dark: #6B21A8;

                /* YELLOW - Edit Actions */
                --edit-yellow: #EAB308;
                --edit-yellow-hover: #CA8A04;
                --edit-yellow-light: #FCD34D;
                --edit-yellow-lighter: #FEF3C7;
                --edit-yellow-dark: #A16207;

                /* RED - Delete/Danger Actions */
                --delete-red: #EF4444;
                --delete-red-hover: #DC2626;
                --delete-red-light: #F87171;
                --delete-red-lighter: #FEE2E2;
                --delete-red-dark: #B91C1C;

                /* GRAY - Neutral */
                --gray-50: #F9FAFB;
                --gray-100: #F3F4F6;
                --gray-200: #E5E7EB;
                --gray-300: #D1D5DB;
                --gray-400: #9CA3AF;
                --gray-500: #6B7280;
                --gray-600: #4B5563;
                --gray-700: #374151;
                --gray-800: #1F2937;
                --gray-900: #111827;

                /* STATUS COLORS */
                --status-progress: #3B82F6;
                --status-pending: #F59E0B;
                --status-done: #10B981;

                /* URGENCY COLORS */
                --urgency-low: #10B981;
                --urgency-medium: #F59E0B;
                --urgency-high: #F97316;
                --urgency-very-high: #EF4444;
            }

            /* Smooth transitions */
            * {
                transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
            }

            /* ===== BUTTON CLASSES ===== */
            
            /* Green Button - Aktivitas & Primary */
            .btn-green {
                background-color: var(--primary-green);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-green:hover {
                background-color: var(--primary-green-hover);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
            .btn-green:active {
                transform: translateY(0);
            }

            /* Blue Button - Project Actions */
            .btn-blue {
                background-color: var(--project-blue);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-blue:hover {
                background-color: var(--project-blue-hover);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
            .btn-blue:active {
                transform: translateY(0);
            }

            /* Purple Button - PKJ Actions */
            .btn-purple {
                background-color: var(--pkj-purple);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-purple:hover {
                background-color: var(--pkj-purple-hover);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
            .btn-purple:active {
                transform: translateY(0);
            }

            /* Yellow Button - Edit */
            .btn-yellow {
                background-color: var(--edit-yellow);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-yellow:hover {
                background-color: var(--edit-yellow-hover);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
            .btn-yellow:active {
                transform: translateY(0);
            }

            /* Red Button - Delete */
            .btn-red {
                background-color: var(--delete-red);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-red:hover {
                background-color: var(--delete-red-hover);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
            .btn-red:active {
                transform: translateY(0);
            }

            /* Gray Button - Secondary/Cancel */
            .btn-gray {
                background-color: var(--gray-500);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.2s;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .btn-gray:hover {
                background-color: var(--gray-700);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }
            .btn-gray:active {
                transform: translateY(0);
            }

            /* ===== ICON BUTTON CLASSES (Smaller) ===== */
            
            .btn-icon-yellow {
                color: var(--edit-yellow);
                padding: 0.25rem;
                border-radius: 0.375rem;
                transition: all 0.2s;
            }
            .btn-icon-yellow:hover {
                color: var(--edit-yellow-hover);
                background-color: var(--edit-yellow-lighter);
            }

            .btn-icon-red {
                color: var(--delete-red);
                padding: 0.25rem;
                border-radius: 0.375rem;
                transition: all 0.2s;
            }
            .btn-icon-red:hover {
                color: var(--delete-red-hover);
                background-color: var(--delete-red-lighter);
            }

            /* ===== BADGE CLASSES ===== */
            
            .badge-green {
                background-color: var(--primary-green-lighter);
                color: var(--primary-green-dark);
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .badge-blue {
                background-color: var(--project-blue-lighter);
                color: var(--project-blue-dark);
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .badge-purple {
                background-color: var(--pkj-purple-lighter);
                color: var(--pkj-purple-dark);
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            /* ===== SELECT2 CUSTOM STYLING - GREEN THEME (UPDATED) ===== */

            /* Container */
            .select2-container--default .select2-selection--single {
                height: 42px !important;
                padding: 6px 12px !important;
                border: 1px solid var(--gray-300) !important;
                border-radius: 0.375rem !important;
                transition: all 0.2s ease !important;
            }

            /* Text di dalam select */
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 28px !important;
                color: var(--gray-700) !important;
            }

            /* Arrow icon */
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 40px !important;
            }

            /* Focus state - HIJAU */
            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: var(--primary-green) !important;
                box-shadow: 0 0 0 1px var(--primary-green) !important;
            }

            /* ✅ HOVER STATE - HIJAU (INI YANG PENTING!) */
            .select2-container--default .select2-results__option--highlighted[aria-selected],
            .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
                background-color: var(--primary-green) !important;
                color: white !important;
            }

            /* Additional hover styling untuk lebih konsisten */
            .select2-results__option--selectable:hover {
                background-color: var(--primary-green) !important;
                color: white !important;
            }

            /* Dropdown container */
            .select2-dropdown {
                border: 1px solid var(--gray-300) !important;
                border-radius: 0.375rem !important;
                z-index: 9999 !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            }

            /* Search box di dalam dropdown */
            .select2-search--dropdown .select2-search__field {
                border: 1px solid var(--gray-300) !important;
                border-radius: 0.375rem !important;
                padding: 6px 12px !important;
            }

            /* Search box focus - HIJAU */
            .select2-search--dropdown .select2-search__field:focus {
                border-color: var(--primary-green) !important;
                outline: none !important;
                box-shadow: 0 0 0 1px var(--primary-green) !important;
            }

            /* Selected option (yang sudah dipilih) - HIJAU MUDA */
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: var(--primary-green-lighter) !important;
                color: var(--primary-green-dark) !important;
                font-weight: 600 !important;
            }

            /* Disabled option */
            .select2-container--default .select2-results__option--disabled {
                background-color: var(--gray-100) !important;
                color: var(--gray-400) !important;
                cursor: not-allowed !important;
            }

            /* Hover effects untuk rows */
            .hover-green:hover {
                background-color: var(--primary-green-lighter) !important;
            }

            .hover-blue:hover {
                background-color: var(--project-blue-lighter) !important;
            }

            .hover-purple:hover {
                background-color: var(--pkj-purple-lighter) !important;
            }
            
            /* Select2 Custom Styling - Match dengan Green Theme */
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: var(--primary-green-lighter) !important;
                color: var(--primary-green) !important;
            }

            /* Hover effects untuk rows */
            .hover-green:hover {
                background-color: var(--primary-green-lighter) !important;
            }

            .hover-blue:hover {
                background-color: var(--project-blue-lighter) !important;
            }

            .hover-purple:hover {
                background-color: var(--pkj-purple-lighter) !important;
            }

            /* ===== PAGINATION LIGHT THEME ===== */
            
            /* Container pagination */
            nav[role="navigation"] {
                background-color: transparent !important;
            }

            /* Pagination wrapper */
            nav[role="navigation"] > div {
                background-color: white !important;
            }

            /* Pagination text (Showing X to Y of Z results) */
            nav[role="navigation"] p {
                color: var(--gray-600) !important;
            }

            /* Pagination buttons/links */
            nav[role="navigation"] a,
            nav[role="navigation"] span {
                background-color: white !important;
                color: var(--gray-700) !important;
                border-color: var(--gray-300) !important;
            }

            /* Pagination hover state */
            nav[role="navigation"] a:hover {
                background-color: var(--gray-100) !important;
                color: var(--gray-900) !important;
                border-color: var(--gray-400) !important;
            }

            /* Active/current page - MATCH WITH GREEN THEME */
            nav[role="navigation"] span[aria-current="page"] {
                background-color: var(--primary-green) !important;
                color: white !important;
                border-color: var(--primary-green) !important;
            }

            /* Disabled state (previous/next di ujung) */
            nav[role="navigation"] span[aria-disabled="true"] {
                background-color: var(--gray-50) !important;
                color: var(--gray-400) !important;
                border-color: var(--gray-200) !important;
                cursor: not-allowed !important;
                opacity: 0.6;
            }

            /* Previous/Next buttons - MATCH WITH GREEN THEME */
            nav[role="navigation"] a[rel="prev"],
            nav[role="navigation"] a[rel="next"] {
                background-color: white !important;
                color: var(--primary-green) !important;
                border-color: var(--gray-300) !important;
            }

            nav[role="navigation"] a[rel="prev"]:hover,
            nav[role="navigation"] a[rel="next"]:hover {
                background-color: var(--primary-green-lighter) !important;
                color: var(--primary-green-dark) !important;
                border-color: var(--primary-green) !important;
            }

            /* SVG icons di prev/next buttons */
            nav[role="navigation"] svg {
                color: currentColor !important;
            }

            /* Three dots separator */
            nav[role="navigation"] .relative.inline-flex.items-center.-ml-px {
                background-color: white !important;
                color: var(--gray-700) !important;
                border-color: var(--gray-300) !important;
            }

            /* Mobile pagination (sm:hidden) */
            nav[role="navigation"] .sm\\:hidden a,
            nav[role="navigation"] .sm\\:hidden span {
                background-color: white !important;
                color: var(--gray-700) !important;
                border-color: var(--gray-300) !important;
            }

            nav[role="navigation"] .sm\\:hidden a:hover {
                background-color: var(--primary-green-lighter) !important;
                color: var(--primary-green-dark) !important;
                border-color: var(--primary-green) !important;
            }

            nav[role="navigation"] .sm\\:hidden span[aria-disabled="true"] {
                background-color: var(--gray-50) !important;
                color: var(--gray-400) !important;
                border-color: var(--gray-200) !important;
            }

            /* ===== NATIVE SELECT DROPDOWN HOVER STYLING - HIJAU ===== */

            /* Base styling untuk select dropdown */
            select {
                appearance: none;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 0.5rem center;
                background-repeat: no-repeat;
                background-size: 1.5em 1.5em;
                padding-right: 2.5rem;
            }

            /* Hover effect untuk select element */
            select:hover {
                border-color: var(--primary-green) !important;
                box-shadow: 0 0 0 1px var(--primary-green) !important;
            }

            /* Focus state untuk select */
            select:focus {
                border-color: var(--primary-green) !important;
                box-shadow: 0 0 0 1px var(--primary-green), 0 0 0 3px var(--primary-green-lighter) !important;
                outline: none !important;
            }

            /* Styling untuk option elements (dropdown items) */
            select option {
                background-color: white;
                color: var(--gray-700);
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }

            /* ✅ HOVER EFFECT UNTUK OPTIONS - HIJAU */
            select option:hover {
                background-color: var(--primary-green) !important;
                color: white !important;
                cursor: pointer;
            }

            /* Active/Selected option - Hijau muda */
            select option:checked {
                background-color: var(--primary-green-lighter) !important;
                color: var(--primary-green-dark) !important;
                font-weight: 600;
            }

            /* When option is being clicked/selected */
            select option:active {
                background-color: var(--primary-green-hover) !important;
                color: white !important;
            }

            /* Firefox specific - karena Firefox punya rendering berbeda */
            @-moz-document url-prefix() {
                select option:hover {
                    background-color: var(--primary-green) !important;
                    color: white !important;
                }
                
                select option:checked {
                    background-color: var(--primary-green-lighter) !important;
                    color: var(--primary-green-dark) !important;
                }
            }

            /* Disabled option styling */
            select option:disabled {
                background-color: var(--gray-100) !important;
                color: var(--gray-400) !important;
                cursor: not-allowed !important;
                opacity: 0.6;
            }

            /* Placeholder option (yang disabled selected hidden) */
            select option[disabled][selected][hidden] {
                color: var(--gray-400);
                display: none;
            }

            /* ===== ENHANCED DROPDOWN VISUAL FEEDBACK ===== */

            /* Animasi smooth untuk dropdown */
            select {
                transition: all 0.2s ease;
            }

            select:hover {
                transform: translateY(-1px);
            }

            select:active {
                transform: translateY(0);
            }

            /* ===== WEBKIT/CHROME SPECIFIC STYLING ===== */
            /* Scrollbar styling untuk dropdown panjang */
            select::-webkit-scrollbar {
                width: 8px;
            }

            select::-webkit-scrollbar-track {
                background: var(--gray-100);
                border-radius: 4px;
            }

            select::-webkit-scrollbar-thumb {
                background: var(--primary-green);
                border-radius: 4px;
            }

            select::-webkit-scrollbar-thumb:hover {
                background: var(--primary-green-hover);
            }

            /* ===== DATALIST STYLING (untuk input dengan suggestions) ===== */
            datalist {
                background-color: white;
                border: 1px solid var(--gray-300);
                border-radius: 0.375rem;
            }

            option[value]:hover {
                background-color: var(--primary-green) !important;
                color: white !important;
            }

            /* Text Input, Textarea, Date Input - Focus Green */
            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus,
            input[type="number"]:focus,
            input[type="tel"]:focus,
            input[type="url"]:focus,
            input[type="date"]:focus,
            input[type="datetime-local"]:focus,
            input[type="time"]:focus,
            textarea:focus {
                border-color: var(--primary-green) !important;
                ring-color: var(--primary-green) !important;
                outline: none !important;
                box-shadow: 0 0 0 1px var(--primary-green), 0 0 0 3px var(--primary-green-lighter) !important;
            }

            /* Hover effect untuk input fields */
            input[type="text"]:hover,
            input[type="email"]:hover,
            input[type="password"]:hover,
            input[type="number"]:hover,
            input[type="tel"]:hover,
            input[type="url"]:hover,
            input[type="date"]:hover,
            input[type="datetime-local"]:hover,
            input[type="time"]:hover,
            textarea:hover {
                border-color: var(--primary-green) !important;
            }

            /* ===== DATE INPUT STYLING - HIJAU ===== */

            /* Calendar icon color */
            input[type="date"]::-webkit-calendar-picker-indicator {
                cursor: pointer;
                filter: invert(29%) sepia(95%) saturate(726%) hue-rotate(112deg) brightness(94%) contrast(94%);
                /* Filter ini mengubah icon jadi hijau sesuai var(--primary-green) */
            }

            input[type="date"]::-webkit-calendar-picker-indicator:hover {
                filter: invert(14%) sepia(68%) saturate(1682%) hue-rotate(113deg) brightness(95%) contrast(98%);
                /* Lebih gelap saat hover */
            }

            /* Date input focus - hijau */
            input[type="date"]:focus::-webkit-calendar-picker-indicator {
                filter: invert(29%) sepi(95%) saturate(726%) hue-rotate(112deg) brightness(94%) contrast(94%);
            }

            /* ===== TAILWIND OVERRIDE - GREEN FOCUS ===== */

            /* Override Tailwind default focus (biru) ke hijau */
            .focus\:border-blue-500:focus {
                border-color: var(--primary-green) !important;
            }

            .focus\:ring-blue-500:focus {
                --tw-ring-color: var(--primary-green) !important;
            }

            /* Override untuk semua class yang ada focus:border-blue */
            [class*="focus:border-blue"]:focus {
                border-color: var(--primary-green) !important;
            }

            [class*="focus:ring-blue"]:focus {
                --tw-ring-color: var(--primary-green) !important;
            }

            /* ===== SIFAT PROJECT OTHERS INPUT - HIJAU ===== */

            /* Input manual untuk "Others" - styling hijau */
            #sifat_project_others {
                transition: all 0.2s ease;
            }

            #sifat_project_others:focus {
                border-color: var(--primary-green) !important;
                box-shadow: 0 0 0 1px var(--primary-green), 0 0 0 3px var(--primary-green-lighter) !important;
                outline: none !important;
            }

            #sifat_project_others:hover {
                border-color: var(--primary-green) !important;
            }

            /* ===== SMOOTH TRANSITIONS ===== */
            input, textarea, select {
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            /* ===== PLACEHOLDER STYLING ===== */
            input::placeholder,
            textarea::placeholder {
                color: var(--gray-400);
                opacity: 1;
            }

            input:focus::placeholder,
            textarea:focus::placeholder {
                color: var(--gray-500);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">
            
            {{-- Top Navigation Bar --}}
            <nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0">
                <div class="px-3 py-3 lg:px-5 lg:pl-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center justify-start">
                            {{-- Toggle Button Desktop --}}
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="hidden lg:inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            
                            {{-- Toggle Button Mobile --}}
                            <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                                    class="lg:hidden inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            
                            {{-- Logo & Title --}}
                            <a href="{{ route('dashboard') }}" class="flex items-center ml-2 md:mr-24">
                                <img src="{{ asset('images/bank-bpd-bali-seeklogo.png') }}" 
                                     alt="BPD Bank Bali" 
                                     class="h-8 w-auto mr-3"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                
                                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full mr-3" 
                                     style="background-color: var(--primary-green); display: none;">
                                    <span class="text-white font-bold text-sm">BPD</span>
                                </div>
                                
                                <div class="flex flex-col">
                                    <span class="text-lg font-bold" style="color: var(--primary-green); line-height: 1.2;">
                                        Bank BPD Bali
                                    </span>
                                    <span class="text-xs text-gray-500 hidden sm:block">
                                        WorkLog System
                                    </span>
                                </div>
                            </a>
                        </div>
                        
                        {{-- User Menu --}}
                        <div class="flex items-center">
                            <x-language-switcher />
                            <div class="flex items-center ml-3" x-data="{ userMenuOpen: false }">
                                <button @click="userMenuOpen = !userMenuOpen" 
                                        type="button" 
                                        class="flex text-sm rounded-full focus:ring-4 focus:ring-green-300"
                                        style="background-color: var(--primary-green);">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold" 
                                         style="background-color: var(--primary-green);">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                </button>
                                
                                <div x-show="userMenuOpen" 
                                     @click.away="userMenuOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 top-12 z-50 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow"
                                     style="display: none;">
                                    <div class="px-4 py-3">
                                        <p class="text-sm text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ ucfirst(Auth::user()->role) }}</p>
                                    </div>
                                    <ul class="py-1">
                                        <li>
                                            <a href="{{ route('profile.edit') }}" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50">
                                                Profile
                                            </a>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-green-50">
                                                    Sign out
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Sidebar Desktop --}}
            <aside 
                x-show="sidebarOpen" 
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="hidden lg:block fixed top-0 left-0 z-20 w-64 h-screen pt-20 bg-white border-r border-gray-200">
                <div class="h-full px-3 pb-4 overflow-y-auto">
                    @include('layouts.sidebar-menu')
                </div>
            </aside>

            {{-- Mobile Sidebar --}}
            <aside 
                x-show="mobileSidebarOpen" 
                @click.away="mobileSidebarOpen = false"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="lg:hidden fixed top-0 left-0 z-40 w-64 h-screen pt-20 bg-white border-r border-gray-200"
                style="display: none;">
                <div class="h-full px-3 pb-4 overflow-y-auto">
                    @include('layouts.sidebar-menu')
                </div>
            </aside>

            {{-- Overlay for Mobile --}}
            <div x-show="mobileSidebarOpen" 
                 @click="mobileSidebarOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="lg:hidden fixed inset-0 z-30 bg-gray-900 bg-opacity-50"
                 style="display: none;">
            </div>

            {{-- Main Content --}}
            <main :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'" 
                  class="pt-20 transition-all duration-300">
                <div class="p-4">
                    @if (isset($header))
                        <header class="bg-white shadow rounded-lg mb-6">
                            <div class="px-4 py-6 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- jQuery (Required by Select2) --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        {{-- Select2 JS --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        {{-- Alpine.js CDN --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        {{-- Stack untuk custom scripts dari blade lain --}}
        @stack('scripts')

        <!-- Session Timeout Warning (FIXED - Working Countdown) -->
        <script>
            // Konfigurasi timeout (dalam milidetik)
            const SESSION_LIFETIME = {{ config('session.lifetime') * 60 * 1000 }}; // Convert ke ms
            const WARNING_TIME = 5 * 60 * 1000; // Peringatan 5 menit sebelum timeout
            const KEEP_ALIVE_INTERVAL = 5 * 60 * 1000; // Ping setiap 5 menit

            let lastActivityTime = Date.now();
            let warningShown = false;
            let keepAliveInterval;
            let timeoutCheckInterval;
            let countdownInterval; // ✅ TAMBAHAN: Interval untuk countdown

            // Update last activity pada user interaction
            function updateActivity() {
                lastActivityTime = Date.now();
                warningShown = false;
                
                // Hide warning modal jika ada
                const warningModal = document.getElementById('sessionWarningModal');
                if (warningModal) {
                    warningModal.classList.add('hidden');
                }

                // ✅ STOP countdown interval jika ada
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }
            }

            // Check timeout status
            function checkSessionTimeout() {
                const elapsedTime = Date.now() - lastActivityTime;
                const timeRemaining = SESSION_LIFETIME - elapsedTime;

                // Jika waktu tersisa < WARNING_TIME, tampilkan warning
                if (timeRemaining <= WARNING_TIME && !warningShown) {
                    showTimeoutWarning();
                    warningShown = true;
                }

                // Jika sudah timeout
                if (timeRemaining <= 0) {
                    handleSessionTimeout();
                }
            }

            // ✅ FIXED: Tampilkan warning modal dengan countdown yang jalan
            function showTimeoutWarning() {
                // Create modal jika belum ada
                if (!document.getElementById('sessionWarningModal')) {
                    const modal = document.createElement('div');
                    modal.id = 'sessionWarningModal';
                    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center';
                    modal.innerHTML = `
                        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">⚠️ Sesi Akan Berakhir</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        Sesi Anda akan berakhir dalam 
                                    </p>
                                    <p class="text-4xl font-bold text-red-600 mt-2 mb-2" id="timeRemainingText">
                                        --:--
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        Klik tombol di bawah untuk melanjutkan sesi.
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3">
                                    <button id="continueSessionBtn" 
                                            class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        🔄 Lanjutkan Sesi
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    // Event listener untuk tombol continue
                    document.getElementById('continueSessionBtn').addEventListener('click', function() {
                        console.log('✅ User clicked continue session');
                        updateActivity();
                        sendKeepAlive();
                    });
                }

                // Show modal
                const modal = document.getElementById('sessionWarningModal');
                modal.classList.remove('hidden');

                // ✅ START countdown interval (update setiap detik)
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }

                countdownInterval = setInterval(() => {
                    const elapsedTime = Date.now() - lastActivityTime;
                    const timeRemaining = SESSION_LIFETIME - elapsedTime;
                    const secondsLeft = Math.max(0, Math.floor(timeRemaining / 1000));

                    // Update countdown text
                    const minutes = Math.floor(secondsLeft / 60);
                    const seconds = secondsLeft % 60;
                    const timeText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    
                    const timeElement = document.getElementById('timeRemainingText');
                    if (timeElement) {
                        timeElement.textContent = timeText;
                        
                        // ✅ Ubah warna jadi lebih merah kalau < 1 menit
                        if (secondsLeft < 60) {
                            timeElement.classList.add('animate-pulse');
                        } else {
                            timeElement.classList.remove('animate-pulse');
                        }
                    }

                    // ✅ Auto logout kalau sudah 0
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        handleSessionTimeout();
                    }
                }, 1000); // Update setiap 1 detik
            }

            // ✅ FIXED: Handle timeout dengan AJAX logout + Triple Fallback
            async function handleSessionTimeout() {
                console.log('⏱️ Session timeout - forcing logout via API...');
                
                // Stop all intervals
                clearInterval(keepAliveInterval);
                clearInterval(timeoutCheckInterval);
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }

                // Hide modal
                const modal = document.getElementById('sessionWarningModal');
                if (modal) {
                    modal.remove();
                }

                // Show loading overlay (user feedback)
                const loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'loadingOverlay';
                loadingOverlay.className = 'fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50';
                loadingOverlay.innerHTML = `
                    <div class="bg-white rounded-lg p-6 text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-700">Sesi berakhir, redirecting...</p>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);

                try {
                    // ✅ METHOD 1: Call force-logout API (Best)
                    const response = await fetch('{{ route('session.force-logout') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        const data = await response.json();
                        
                        if (data.status === 'logged_out') {
                            console.log('✅ Logout successful via API, redirecting...');
                            
                            // Redirect dengan delay singkat
                            setTimeout(() => {
                                window.location.replace('/');
                            }, 500);
                            return;
                        }
                    }
                    
                    // Jika response tidak OK, fallback ke method 2
                    throw new Error('API logout failed, using fallback');
                    
                } catch (error) {
                    console.warn('⚠️ API logout failed, using form POST fallback:', error);
                    
                    // ✅ METHOD 2: Fallback - Form POST (jika AJAX gagal)
                    try {
                        const logoutForm = document.createElement('form');
                        logoutForm.method = 'POST';
                        logoutForm.action = '{{ route('logout') }}';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        
                        logoutForm.appendChild(csrfToken);
                        document.body.appendChild(logoutForm);
                        
                        // Submit form
                        console.log('⚠️ Submitting logout form...');
                        logoutForm.submit();
                        
                    } catch (formError) {
                        console.error('❌ Form submission failed:', formError);
                        
                        // ✅ METHOD 3: Ultimate fallback - direct redirect
                        console.warn('⚠️ Using direct redirect as last resort...');
                        setTimeout(() => {
                            window.location.href = '{{ route('login') }}?timeout=1';
                        }, 500);
                    }
                }
            }

            // Send keep-alive request ke server
            function sendKeepAlive() {
                console.log('📡 Sending keep-alive ping...');
                
                fetch('{{ route('api.keep-alive') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'alive') {
                        console.log('✅ Keep-alive successful - session extended');
                        updateActivity();
                    }
                })
                .catch(error => {
                    console.error('❌ Keep-alive failed:', error);
                });
            }

            // Event listeners untuk user activity
            const activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
            activityEvents.forEach(event => {
                document.addEventListener(event, updateActivity, true);
            });

            // Start intervals
            keepAliveInterval = setInterval(sendKeepAlive, KEEP_ALIVE_INTERVAL);
            timeoutCheckInterval = setInterval(checkSessionTimeout, 10000); // Check setiap 10 detik

            // Initial activity update
            updateActivity();

            // ✅ Debug info (bisa dihapus nanti kalau sudah production)
            console.log('🕐 Session Timeout Initialized:');
            console.log(`- Lifetime: ${SESSION_LIFETIME / 1000 / 60} minutes`);
            console.log(`- Warning: ${WARNING_TIME / 1000 / 60} minutes before timeout`);
            console.log(`- Keep-alive: every ${KEEP_ALIVE_INTERVAL / 1000 / 60} minutes`);
        </script>

        <!-- ✅ CSS untuk animasi pulse (kalau belum ada) -->
        <style>
            @keyframes pulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.5;
                }
            }
            .animate-pulse {
                animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
        </style>
    </body>
</html>