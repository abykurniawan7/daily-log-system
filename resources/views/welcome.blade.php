<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- TITLE TAB BROWSER --}}
    <title>WorkLog System - Bank BPD Bali</title>

    {{-- FAVICON - ICON TAB BROWSER --}}
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bg-image-overlay {
            position: relative;
            background-image: url('/images/Depan Bank.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .bg-image-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }
        
        /* ✅ TAMBAHAN: Animation untuk notification */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .animate-slide-in {
            animation: slideInRight 0.5s ease-out;
        }
    </style>
</head>
<body class="font-sans antialiased">
    
    {{-- ✅ NOTIFICATION ALERT - TAMBAHKAN DI SINI --}}
    @if (session('info'))
    <div class="fixed top-4 right-4 z-50 max-w-md animate-slide-in">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-lg shadow-2xl" role="alert">
            <div class="flex items-start">
                {{-- Icon --}}
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                {{-- Message --}}
                <div class="ml-3 flex-1">
                    <p class="font-semibold text-sm">Sesi Berakhir</p>
                    <p class="text-sm mt-1 text-white/90">{{ session('info') }}</p>
                </div>
                
                {{-- Close Button --}}
                <button onclick="this.closest('[role=alert]').remove()" 
                        class="ml-4 flex-shrink-0 text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Auto-hide script --}}
    <script>
        // Auto hide notification after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('[role="alert"]');
            if (alert) {
                alert.style.transition = 'all 0.5s ease-out';
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(100px)';
                setTimeout(() => alert.closest('.fixed').remove(), 500);
            }
        }, 5000);
    </script>
    @endif
    {{-- END NOTIFICATION --}}

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-image-overlay">
        
        {{-- Content Wrapper (relative untuk z-index) --}}
        <div class="relative z-10 w-full max-w-md px-6">
            
            {{-- Logo & Brand --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/bank-bpd-bali-seeklogo.png') }}" 
                         alt="Logo BPD Bali" 
                         class="h-24 w-auto drop-shadow-2xl">
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">WorkLog System</h1>
                <p class="text-white/90 font-medium drop-shadow-md">Divisi PGD Bank BPD Bali</p>
            </div>

            {{-- Main Card --}}
            <div class="w-full px-6 py-8 bg-white/95 backdrop-blur-sm shadow-2xl overflow-hidden rounded-2xl">
                
                {{-- Welcome Text --}}
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
                    <p class="text-gray-600 mt-2 text-sm">Sistem Manajemen Aktivitas Karyawan</p>
                </div>

                {{-- Info Box --}}
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-800 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-800" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-900 font-medium">
                                Kelola project dan aktivitas dengan mudah
                            </p>
                            <ul class="mt-2 text-xs text-green-800 space-y-1">
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Manajemen project terintegrasi
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Tracking aktivitas karyawan
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Laporan dan export PDF
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if (Route::has('login'))
                    <div class="space-y-3">
                        @auth
                            {{-- Already Logged In --}}
                            <a href="{{ url('/dashboard') }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-3 bg-green-800 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Buka Dashboard
                            </a>
                        @else
                            {{-- Login Button --}}
                            <a href="{{ route('login') }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-3 bg-green-800 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Masuk
                            </a>

                            {{-- Register Button (if enabled) --}}
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-3 bg-white border-2 border-green-800 rounded-lg font-semibold text-sm text-green-800 uppercase tracking-widest hover:bg-green-50 focus:bg-green-50 active:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Daftar Akun Baru
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="mt-6 text-center text-sm text-white drop-shadow-lg">
                <p class="font-medium">&copy; {{ date('Y') }} Bank BPD Bali. All rights reserved.</p>
                <p class="mt-1 text-xs text-white/80">WorkLog System v1.0 - Divisi PGD</p>
            </div>
        </div>
    </div>
</body>
</html>