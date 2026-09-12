<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | CV Tomo Teknik Mandiri</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Ensure light mode by default
        document.documentElement.classList.remove('dark');
        localStorage.removeItem('dark-mode');
    </script>
</head>

<body class="font-sans bg-slate-50 text-slate-800 antialiased min-h-screen">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-400 border-r border-slate-800 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <!-- Sidebar Header / Logo -->
            <div class="h-20 shrink-0 flex items-center justify-between px-6 border-b border-slate-800">
                <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo CV Tomo Teknik Mandiri"
                        class="w-9 h-9 object-contain drop-shadow-sm transition-transform duration-200 group-hover:scale-105">
                    <div class="text-left">
                        <span class="text-sm font-extrabold text-white tracking-tight block leading-tight">TOMO TEKNIK</span>
                        <span class="text-[10px] font-bold text-blue-500 tracking-wider block">MANDIRI</span>
                    </div>
                </a>

                <!-- Close Mobile Sidebar -->
                <button type="button" id="sidebar-close-btn"
                    class="md:hidden p-1 text-slate-400 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-grow py-6 px-4 space-y-1.5 overflow-y-auto">
                @php $role = session('user.role', 'pelanggan'); @endphp

                @if($role == 'admin')
                    <!-- ADMIN MENU -->
                    <span class="block px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Menu
                        Admin</span>

                    <a href="{{ url('/admin/dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/dashboard') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ url('/admin/data-pelanggan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/data-pelanggan') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Data Pelanggan
                    </a>

                    <a href="{{ url('/admin/pesanan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/pesanan') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pesanan
                    </a>


                    <a href="{{ url('/admin/data-layanan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/data-layanan') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Data Layanan
                    </a>

                    <a href="{{ url('/admin/data-staff') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/data-staff') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Data Staff
                    </a>

                    <a href="{{ url('/admin/data-alat') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/data-alat') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Data Alat
                    </a>

                    <a href="{{ url('/admin/chat') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/chat') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Chat
                    </a>

                    <!-- SETTING MENU -->
                    <span
                        class="block px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mt-4 mb-2">Setting</span>

                    <a href="{{ url('/admin/dokumentasi-proyek') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/dokumentasi-proyek') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Dokumentasi Proyek
                    </a>

                    <a href="{{ url('/admin/tentang-kami') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('admin/tentang-kami') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tentang Kami
                    </a>

                @elseif($role == 'direktur')
                    <!-- DIREKTUR MENU -->
                    <span class="block px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Menu
                        Direktur</span>

                    <a href="{{ url('/direktur/dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('direktur/dashboard') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ url('/direktur/data-pelanggan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('direktur/data-pelanggan') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Data Pelanggan
                    </a>

                    <a href="{{ url('/direktur/validasi-dokumen') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('direktur/validasi-dokumen*') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Validasi Dokumen
                    </a>

                    <a href="{{ url('/direktur/laporan-teknisi') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('direktur/laporan-teknisi*') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan Teknisi
                    </a>

                @elseif($role == 'managerteknisi')
                    <!-- MANAGER TEKNISI MENU -->
                    <span class="block px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Menu
                        Manager Teknisi</span>

                    <a href="{{ url('/manager/dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('manager/dashboard') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ url('/manager/pelaporan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('manager/pelaporan') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Pelaporan
                    </a>

                @else
                    <!-- PELANGGAN MENU -->
                    <span class="block px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Menu
                        Pelanggan</span>

                    <a href="{{ url('/pelanggan/dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('pelanggan/dashboard') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ url('/pelanggan/pesanan') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('pelanggan/pesanan') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pesanan
                    </a>

                    <a href="{{ url('/pelanggan/chat') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-colors {{ Request::is('pelanggan/chat') ? 'text-white bg-blue-600' : 'text-slate-400 hover:bg-slate-850 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Chat
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Content Area -->
        <div id="content-area"
            class="flex-grow flex flex-col md:pl-64 h-screen overflow-hidden transition-all duration-300">
            <!-- Header Navbar -->
            <header
                class="h-20 shrink-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 sticky top-0 z-30">
                <!-- Left Nav (Hamburger Button Only) -->
                <div class="flex items-center">
                    <button type="button" id="sidebar-toggle-btn"
                        class="p-2 rounded-lg text-slate-650 hover:bg-slate-150 dark:text-slate-400 dark:hover:bg-slate-800 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Right Nav (Notifications & Profile) -->
                <div class="flex items-center gap-3.5 relative">

                    <!-- Notifications -->
                    <div class="relative">
                        <button type="button" id="notif-btn"
                            class="p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 rounded-full transition-colors focus:outline-none relative"
                            title="Notifikasi">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notif-badge"
                                style="display: none;"
                                class="absolute -top-0.5 -right-0.5 min-w-[19px] h-[19px] px-1 bg-red-600 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900 leading-none shadow-sm z-10 pointer-events-none">
                                0
                            </span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div id="notif-dropdown"
                            class="hidden absolute right-0 mt-3.5 w-80 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl py-3 z-50 text-left">
                            <div
                                class="px-4 py-2 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Notifikasi</span>
                                    <span id="notif-dropdown-count"
                                        style="display: none;"
                                        class="px-1.5 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 rounded-full">
                                        0
                                    </span>
                                </div>
                                <button type="button" id="notif-read-all-btn"
                                    class="text-[10px] font-bold text-blue-600 dark:text-blue-400 cursor-pointer hover:underline bg-transparent border-none">Tandai
                                    Baca</button>
                            </div>
                            <div id="notif-list" class="max-h-72 overflow-y-auto py-1">
                                <!-- Dynamic notifications rendered here -->
                                <div id="notif-empty" class="px-4 py-8 text-center">
                                    <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <p class="text-[11px] text-slate-400">Belum ada notifikasi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button type="button" id="profile-btn" class="flex items-center gap-2.5 focus:outline-none">
                            <div
                                class="w-9 h-9 rounded-full border-2 border-blue-500 flex items-center justify-center bg-blue-50 text-blue-600 overflow-hidden font-extrabold text-sm shadow-md shadow-blue-200/20">
                                {{ strtoupper(substr(session('user.name', 'P'), 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <span
                                    class="block text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ session('user.name', 'Pelanggan') }}</span>
                                <span
                                    class="block text-[9px] font-semibold text-slate-450 uppercase leading-tight tracking-wider">
                                    @php
                                        $role = session('user.role', 'pelanggan');
                                        if ($role == 'managerteknisi')
                                            echo 'Manager';
                                        else
                                            echo $role;
                                    @endphp
                                </span>
                            </div>
                        </button>

                        <!-- Profile Dropdown Menu -->
                        <div id="profile-dropdown"
                            class="hidden absolute right-0 mt-3 w-56 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl py-3 z-50 text-left">
                            <div class="px-5 py-2.5 border-b border-slate-100 dark:border-slate-700">
                                <span
                                    class="block text-xs font-bold text-slate-900 dark:text-white">{{ session('user.name', 'Pelanggan') }}</span>
                                <span class="block text-[10px] text-slate-450 truncate mt-0.5">{{ session('user.email',
                                    'pelanggan@email.com') }}</span>
                            </div>
                            <div class="py-1">
                                <a href="{{ url('/logout') }}"
                                    class="flex items-center gap-2.5 px-5 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Dashboard Content -->
            <main class="flex-grow px-6 pt-6 pb-4 overflow-y-auto flex flex-col justify-between">
                <div class="w-full flex-grow">
                    @yield('content')
                </div>

                <!-- Dashboard Footer -->
                <footer
                    class="mt-6 pt-4 pb-2 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-400 dark:text-slate-500 shrink-0 transition-colors">
                    <p>&copy; {{ date('Y') }} <span class="font-semibold text-slate-600 dark:text-slate-400">CV Tomo Teknik Mandiri</span>. Seluruh Hak Cipta Dilindungi.</p>
                </footer>
            </main>
        </div>
    </div>

    <!-- Toggle scripts -->
    <script>
        // Sidebar Toggles
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const contentArea = document.getElementById('content-area');
        const closeBtn = document.getElementById('sidebar-close-btn');

        if (toggleBtn && sidebar && contentArea) {
            toggleBtn.addEventListener('click', (event) => {
                event.stopPropagation();

                if (window.innerWidth >= 768) {
                    // Desktop view toggle
                    sidebar.classList.toggle('md:translate-x-0');
                    sidebar.classList.toggle('md:-translate-x-full');
                    contentArea.classList.toggle('md:pl-64');
                } else {
                    // Mobile view toggle
                    sidebar.classList.toggle('-translate-x-full');
                }
            });
        }

        if (closeBtn && sidebar) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
            });
        }

        // Close mobile sidebar click outside
        document.addEventListener('click', (event) => {
            if (window.innerWidth < 768) {
                if (sidebar && !sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });

        // Notification Toggle
        const notifBtn = document.getElementById('notif-btn');
        const notifDropdown = document.getElementById('notif-dropdown');

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                notifDropdown.classList.toggle('hidden');
                if (typeof profileDropdown !== 'undefined') profileDropdown.classList.add('hidden');
            });
        }

        // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                profileDropdown.classList.toggle('hidden');
                if (typeof notifDropdown !== 'undefined') notifDropdown.classList.add('hidden');
            });
        }

        // Close dropdowns if clicked outside
        document.addEventListener('click', (event) => {
            if (notifDropdown && !notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
            if (profileDropdown && !profileBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // ============================================================
        // DYNAMIC NOTIFICATION SYSTEM
        // ============================================================
        const notifBadge = document.getElementById('notif-badge');
        const notifDropdownCount = document.getElementById('notif-dropdown-count');
        const notifList = document.getElementById('notif-list');
        const notifEmpty = document.getElementById('notif-empty');
        const notifReadAllBtn = document.getElementById('notif-read-all-btn');

        const NOTIF_ICON_MAP = {
            'info': 'bg-blue-500',
            'success': 'bg-emerald-500',
            'warning': 'bg-amber-500',
            'danger': 'bg-red-500',
        };

        function renderNotifikasi(data) {
            const { notifikasi, unread_count } = data;

            // Update badge & dropdown count
            if (unread_count > 0) {
                const countText = unread_count > 99 ? '99+' : unread_count;
                notifBadge.textContent = countText;
                notifBadge.style.display = 'flex';
                if (notifDropdownCount) {
                    notifDropdownCount.textContent = countText + ' baru';
                    notifDropdownCount.style.display = 'inline-block';
                }
            } else {
                notifBadge.style.display = 'none';
                if (notifDropdownCount) {
                    notifDropdownCount.style.display = 'none';
                }
            }

            // Clear existing (except empty state)
            const existingItems = notifList.querySelectorAll('.notif-item');
            existingItems.forEach(el => el.remove());

            if (notifikasi.length === 0) {
                notifEmpty.classList.remove('hidden');
                return;
            }

            notifEmpty.classList.add('hidden');

            notifikasi.forEach((n, idx) => {
                const dotColor = NOTIF_ICON_MAP[n.icon] || 'bg-blue-500';
                const opacity = n.is_read ? 'opacity-60' : '';
                const fontWeight = n.is_read ? 'font-semibold' : 'font-bold';
                const border = idx < notifikasi.length - 1 ? 'border-b border-slate-50 dark:border-slate-750' : '';

                const item = document.createElement('div');
                item.className = `notif-item px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 ${border} transition-colors flex gap-3 items-start cursor-pointer ${opacity}`;
                item.setAttribute('data-id', n.id);
                item.setAttribute('data-url', n.url || '');

                item.innerHTML = `
                    <span class="w-2.5 h-2.5 rounded-full ${dotColor} shrink-0 mt-1.5${n.is_read ? ' opacity-40' : ''}"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs ${fontWeight} text-slate-800 dark:text-slate-200 truncate">${escapeHtml(n.judul)}</p>
                        <p class="text-[10px] text-slate-450 mt-0.5 line-clamp-2">${escapeHtml(n.pesan)}</p>
                        <span class="text-[9px] text-slate-400 block mt-1">${escapeHtml(n.waktu)}</span>
                    </div>
                `;

                item.addEventListener('click', () => handleNotifClick(n.id, n.url));
                notifList.insertBefore(item, notifEmpty);
            });
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str || ''));
            return div.innerHTML;
        }

        function handleNotifClick(id, url) {
            fetch('/notifikasi/baca/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (url) {
                    window.location.href = url;
                } else {
                    fetchNotifikasi();
                }
            })
            .catch(() => {
                if (url) window.location.href = url;
            });
        }

        function fetchNotifikasi() {
            fetch('/notifikasi/data', {
                headers: { 'Accept': 'application/json' },
            })
            .then(res => res.json())
            .then(data => renderNotifikasi(data))
            .catch(err => console.warn('Gagal memuat notifikasi:', err));
        }

        // Mark all as read
        if (notifReadAllBtn) {
            notifReadAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                fetch('/notifikasi/baca-semua', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(() => fetchNotifikasi())
                .catch(err => console.warn('Gagal menandai semua notifikasi:', err));
            });
        }

        // Initial fetch + auto-polling every 30 seconds
        fetchNotifikasi();
        setInterval(fetchNotifikasi, 30000);

    </script>
</body>

</html>