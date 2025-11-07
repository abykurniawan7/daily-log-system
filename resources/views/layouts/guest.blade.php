<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- TITLE TAB BROWSER --}}
        <title>Login - WorkLog System Bank BPD Bali</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ============================
               BPD BANK CUSTOM THEME
               ============================ */
            
            :root {
                /* Warna Hijau Dark Green - BISA DIGANTI SESUAI KEINGINAN */
                --primary-green: #0F5132;
                --primary-green-hover: #0A3D24;
                --primary-green-light: #1B6B47;
            }

            /* Background Image dengan Blur & Dark Overlay */
            body {
                background-image: 
                    linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                    url('{{ asset("images/Depan Bank.jpg") }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                min-height: 100vh;
            }

            /* ========================================
               🔧 CARA GANTI BACKGROUND FOTO:
               ----------------------------------------
               Ganti URL di atas dengan:
               url('{{ asset("images/bank-building.jpg") }}')
               
               Upload foto ke: public/images/bank-building.jpg
               ======================================== */

            /* Blur Effect Overlay */
            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                backdrop-filter: blur(4px); /* Ubah angka untuk atur blur */
                z-index: 1;
                pointer-events: none;
            }

            /* ========================================
               🔧 CARA ATUR BLUR:
               ----------------------------------------
               backdrop-filter: blur(0px);   → Tanpa blur
               backdrop-filter: blur(4px);   → Blur sedang (default)
               backdrop-filter: blur(8px);   → Blur banyak
               ======================================== */

            /* Content Wrapper - Taruh di atas background */
            .content-wrapper {
                position: relative;
                z-index: 10;
            }

            /* Card Login - Glass Effect */
            .login-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
            }

            /* ============================
               OVERRIDE BREEZE STYLES
               ============================ */

            /* Button Hijau - Ganti warna indigo jadi hijau */
            button[type="submit"],
            .bg-indigo-600,
            .bg-indigo-500 {
                background-color: var(--primary-green) !important;
            }

            button[type="submit"]:hover,
            .bg-indigo-600:hover,
            .bg-indigo-500:hover,
            .hover\:bg-indigo-500:hover {
                background-color: var(--primary-green-hover) !important;
            }

            button[type="submit"]:focus,
            .focus\:bg-indigo-700:focus {
                background-color: var(--primary-green-hover) !important;
            }

            /* Input Focus Ring Hijau */
            input:focus,
            select:focus,
            textarea:focus {
                border-color: var(--primary-green) !important;
                --tw-ring-color: var(--primary-green-light) !important;
            }

            /* Link Hijau */
            .text-indigo-600,
            .text-indigo-500 {
                color: var(--primary-green) !important;
            }

            .hover\:text-indigo-500:hover,
            .hover\:text-gray-900:hover {
                color: var(--primary-green-hover) !important;
            }

            /* Checkbox Hijau */
            input[type="checkbox"]:checked,
            .text-indigo-600[type="checkbox"]:checked {
                background-color: var(--primary-green) !important;
                border-color: var(--primary-green) !important;
            }

            input[type="checkbox"]:focus,
            .focus\:ring-indigo-500[type="checkbox"]:focus {
                --tw-ring-color: var(--primary-green-light) !important;
            }

            /* Ring Focus Hijau */
            .focus\:ring-indigo-500:focus {
                --tw-ring-color: var(--primary-green-light) !important;
            }

            /* ============================
               RESPONSIVE & ANIMATIONS
               ============================ */

            /* Smooth transitions */
            button, input, a {
                transition: all 0.3s ease;
            }

            /* Button hover effect */
            button[type="submit"]:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(15, 81, 50, 0.3);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="content-wrapper min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            
            <!-- Logo Section -->
            <div class="mb-6">
                <!-- ========================================
                     🏦 LOGO BPD BANK
                     ======================================== -->
                
                {{-- <!-- OPTION 1: Text Logo (Default - Aktif Sekarang) -->
                <div class="text-center">
                    <div class="inline-block bg-gradient-to-br from-green-800 to-green-600 text-white px-8 py-4 rounded-xl shadow-2xl">
                        <h1 class="text-3xl font-bold tracking-wide">BPD BANK</h1>
                        <p class="text-sm opacity-90 mt-1">Bank Pembangunan Daerah Bali</p>
                    </div>
                </div> --}}

                <!-- OPTION 2: Image Logo (Uncomment jika sudah ada logo) -->
                <a href="/" class="block text-center">
                    <img src="{{ asset('images/bank-bpd-bali-seeklogo.png') }}" 
                         alt="BPD Bank Bali" 
                         class="h-48 w-auto mx-auto drop-shadow-2xl hover:scale-105 transition-transform">
                    <h2 class="text-white text-lg font-semibold mt-3 drop-shadow-lg">
                        {{-- Bank Pembangunan Daerah Bali --}}
                    </h2>
                </a>
                
                <!-- ========================================
                     🔧 CARA GANTI KE LOGO IMAGE:
                     ----------------------------------------
                     1. Upload logo ke: public/images/logo-bpd.png
                     2. Comment OPTION 1 (tambahkan  di awal & akhir)
                     3. Uncomment OPTION 2 (hapus  dan  )
                     4. Atur ukuran: h-16 (kecil), h-20, h-24, h-32 (besar)
                     ======================================== -->
            </div>

            <!-- Login Card -->
            <div class="w-full sm:max-w-md px-8 py-6 login-card shadow-2xl overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-white font-medium drop-shadow-lg">
                    &copy; {{ date('Y') }} BPD Bank Bali. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>