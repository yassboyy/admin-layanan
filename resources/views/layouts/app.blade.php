<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@hasSection('title')@yield('title') | @endif CV Tomo Teknik Mandiri</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Header / Navbar -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo CV Tomo Teknik Mandiri"
                        class="w-10 h-10 object-contain drop-shadow-sm transition-transform duration-200 group-hover:scale-105">
                    <div>
                        <span class="text-lg font-extrabold text-slate-900 tracking-tight block leading-tight">TOMO
                            TEKNIK</span>
                        <span class="text-xs font-semibold text-blue-600 tracking-wider block">MANDIRI</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}"
                        class="text-sm font-semibold {{ Request::is('/') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition-colors">Beranda</a>
                    <a href="{{ url('/tentang-kami') }}"
                        class="text-sm font-semibold {{ Request::is('tentang-kami') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition-colors">Tentang
                        Kami</a>
                    <a href="{{ url('/layanan') }}"
                        class="text-sm font-semibold {{ Request::is('layanan') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition-colors">Layanan</a>
                    <a href="{{ url('/dokumentasi') }}"
                        class="text-sm font-semibold {{ Request::is('dokumentasi') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition-colors">Dokumentasi</a>
                    <a href="{{ url('/kontak') }}"
                        class="text-sm font-semibold {{ Request::is('kontak') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-600' }} transition-colors">Kontak</a>
                </nav>

                <!-- Action Buttons (Keranjang, Pesanan Saya, User Avatar) -->
                <div class="hidden md:flex items-center space-x-5">
                    <!-- Keranjang Belanja -->
                    <a href="{{ url('/keranjang') }}"
                        class="relative p-2 rounded-full hover:bg-slate-50 text-slate-600 hover:text-blue-600 transition-colors {{ Request::is('keranjang') ? 'text-blue-600 bg-blue-50/50' : '' }}"
                        aria-label="Keranjang Belanja">
                        <svg class="h-6 w-6 text-slate-700 hover:text-blue-600 transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if(session()->has('user'))
                            @php
                                $cartCount = \App\Models\Keranjang::where('user_id', getUserId())->sum('jumlah');
                            @endphp
                            @if($cartCount > 0)
                                <span
                                    class="absolute top-1.5 right-1.5 w-4 h-4 text-[9px] font-extrabold rounded-full bg-blue-600 text-white flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        @endif
                    </a>

                    <!-- Pesanan Saya -->
                    <a href="{{ url('/pesanan') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 border {{ Request::is('pesanan') ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50/80 text-blue-700 border-blue-100 hover:bg-blue-100 hover:border-blue-200' }}">
                        <svg class="h-4 w-4 {{ Request::is('pesanan') ? 'text-white' : 'text-blue-700' }}" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Pesanan Saya
                    </a>

                    <!-- User Account / Login Status -->
                    @if(session()->has('user'))
                        <div class="relative">
                            <button type="button" id="user-menu-btn"
                                class="w-9 h-9 rounded-full border-2 border-blue-500 hover:border-blue-600 flex items-center justify-center bg-slate-50 overflow-hidden hover:shadow-md transition-all duration-200"
                                aria-expanded="false" aria-haspopup="true">
                                <svg class="h-5 w-5 text-slate-500 hover:text-blue-600 transition-colors" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-dropdown"
                                class="hidden absolute right-0 mt-2.5 w-64 bg-white rounded-2xl border border-slate-100 shadow-xl py-3 z-50 text-left">
                                <div class="px-5 py-3 border-b border-slate-100">
                                    <span
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Masuk
                                        Sebagai</span>
                                    <p class="text-sm font-black text-slate-800 leading-tight">{{ session('user.name') }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ session('user.email') }}</p>
                                </div>
                                <div class="py-2">
                                    @php
                                        $userRole = session('user.role');
                                        $canAccessDashboard = false;
                                        $dashboardUrl = '#';

                                        if ($userRole === 'admin') {
                                            $canAccessDashboard = true;
                                            $dashboardUrl = url('/admin/dashboard');
                                        } elseif ($userRole === 'direktur') {
                                            $canAccessDashboard = true;
                                            $dashboardUrl = url('/direktur/dashboard');
                                        } elseif ($userRole === 'managerteknisi') {
                                            $canAccessDashboard = true;
                                            $dashboardUrl = url('/manager/dashboard');
                                        } elseif ($userRole === 'pelanggan') {
                                            $hasPaidOrder = \App\Models\Pemesanan::where('user_id', getUserId())
                                                ->where(function ($q) {
                                                    $q->whereNotNull('bukti_dp')
                                                        ->where('bukti_dp', '!=', '')
                                                        ->orWhereNotNull('bukti_lunas')
                                                        ->where('bukti_lunas', '!=', '')
                                                        ->orWhere('status_tipe', '>=', 2);
                                                })
                                                ->exists();

                                            if ($hasPaidOrder) {
                                                $canAccessDashboard = true;
                                                $dashboardUrl = url('/pelanggan/dashboard');
                                            }
                                        }
                                    @endphp

                                    @if($canAccessDashboard)
                                        <a href="{{ $dashboardUrl }}"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                                            <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            Dashboard Saya
                                        </a>
                                    @endif

                                    <a href="{{ url('/pesanan') }}"
                                        class="flex items-center gap-3 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Daftar Pesanan
                                    </a>
                                    <a href="{{ url('/keranjang') }}"
                                        class="flex items-center gap-3 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        Keranjang
                                    </a>
                                </div>
                                <div class="border-t border-slate-100 pt-2 px-2">
                                    <a href="{{ url('/logout') }}"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                        <svg class="h-4.5 w-4.5 text-red-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Keluar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ url('/login') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-100 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Login</span>
                        </a>
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn"
                    class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-white border-t border-slate-100 px-4 py-4 space-y-3 shadow-inner">
            <a href="{{ url('/') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('/') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Beranda</a>
            <a href="{{ url('/tentang-kami') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('tentang-kami') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Tentang
                Kami</a>
            <a href="{{ url('/layanan') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('layanan') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Layanan</a>
            <a href="{{ url('/dokumentasi') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('dokumentasi') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Dokumentasi</a>
            <a href="{{ url('/kontak') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('kontak') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Kontak</a>
            <a href="{{ url('/keranjang') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('keranjang') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-2">
                <svg class="h-5 w-5 text-slate-500 {{ Request::is('keranjang') ? 'text-blue-600' : '' }}" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Keranjang Pesanan
            </a>
            <a href="{{ url('/pesanan') }}"
                class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('pesanan') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Pesanan
                Saya</a>
            @if(session()->has('user'))
                <a href="{{ url('/dashboard') }}"
                    class="block px-3 py-2 rounded-lg text-base font-semibold {{ Request::is('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">Dashboard
                    Saya</a>
            @endif
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ url('/kontak') }}"
                    class="block w-full text-center px-4 py-2.5 rounded-lg text-base font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">Hubungi
                    Kami</a>
                @if(session()->has('user'))
                    <div class="px-3 py-2 bg-slate-50 rounded-lg text-left">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Masuk
                            Sebagai</span>
                        <span class="block text-sm font-bold text-slate-800 leading-none">{{ session('user.name') }}</span>
                        <span class="block text-xs text-slate-500 mt-1 truncate">{{ session('user.email') }}</span>
                    </div>
                    <a href="{{ url('/logout') }}"
                        class="block w-full text-center px-4 py-2.5 rounded-lg text-base font-bold text-white bg-red-600 hover:bg-red-700 transition-colors">Keluar</a>
                @else
                    <a href="{{ url('/login') }}"
                        class="block w-full text-center px-4 py-2.5 rounded-lg text-base font-bold text-white bg-blue-600 hover:bg-blue-700">Login</a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <!-- Company Bio -->
                <div class="col-span-1 md:col-span-2">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 mb-6 group">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo CV Tomo Teknik Mandiri"
                            class="w-11 h-11 object-contain drop-shadow-md transition-transform duration-200 group-hover:scale-105">
                        <div>
                            <span class="text-lg font-extrabold text-white tracking-tight block leading-tight">TOMO
                                TEKNIK</span>
                            <span class="text-xs font-semibold text-blue-400 tracking-wider block">MANDIRI</span>
                        </div>
                    </a>
                    <p class="text-sm leading-relaxed max-w-sm mb-6">
                        CV. Tomo Teknik Mandiri adalah perusahaan yang bergerak di bidang solusi Kelistrikan, Sumur Bor,
                        dan Layanan Perizinan Usaha. Dengan mengedepankan Profesionalisme, Kualitas, dan Integritas,
                        Kami berkomitmen memberikan layanan terbaik kepada pelanggan dari sektor Perorangan, Bisnis,
                        Industri, maupun Instansi Pemerintah.
                    </p>
                </div>

                <!-- Navigation Links -->
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Navigasi</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ url('/') }}" class="text-sm hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/tentang-kami') }}"
                                class="text-sm hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ url('/layanan') }}"
                                class="text-sm hover:text-white transition-colors">Layanan</a></li>
                        <li><a href="{{ url('/dokumentasi') }}"
                                class="text-sm hover:text-white transition-colors">Dokumentasi</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Hubungi Kami</h3>
                    <ul class="space-y-4 text-sm leading-relaxed">
                        <li>
                            <span class="block text-white font-medium">Alamat:</span>
                            Jl. Madura No. 17, Kelurahan Hadimulyo Barat, Metro Pusat, Kota Metro, Lampung
                        </li>
                        <li>
                            <span class="block text-white font-medium">Telepon/WA:</span>
                            <span class="block text-slate-400">+62 812-7960-316</span>
                            <span class="block text-slate-400">+62 813-7988-7120</span>
                        </li>
                        <li>
                            <span class="block text-white font-medium">Email:</span>
                            cvtomoteknikmandiri@gmail.com
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer Bar -->
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between">
                <p class="text-xs">
                    &copy; {{ date('Y') }} CV Tomo Teknik Mandiri. Hak Cipta Dilindungi Undang-Undang.
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile & User Menu Script -->
    <script>
        // Mobile Menu Toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }

        // User Dropdown Menu Toggle
        const userBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userBtn && userDropdown) {
            userBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown if clicked outside
            document.addEventListener('click', (event) => {
                if (!userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    </script>

    <!-- Floating Chat Widget (Hanya untuk Tamu / Pelanggan) -->
    @if(!session()->has('user') || session('user.role') === 'pelanggan')
        @include('components.floating-chat')
    @endif

</body>

</html>