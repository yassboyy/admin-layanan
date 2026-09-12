@extends('layouts.app')

@section('title', '')

@section('content')
    <!-- Hero Section -->
    <section
        class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 text-white overflow-hidden py-24 md:py-32">
        <!-- Background accents -->
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-20">
        </div>
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
                    Satu Solusi untuk <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">Semua Kebutuhan Teknik industri Anda</span>
                </h1>
                <p class="text-lg text-slate-300 leading-relaxed mb-10 max-w-2xl">
                    Kami terus berinovasi dalam teknologi, sistem kerja, dan pelayanan guna memberikan solusi yang efektif, efisien, dan berdaya saing tinggi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ url('/layanan') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg text-base font-bold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Jelajahi Layanan</span>
                    </a>
                    <a href="{{ url('/tentang-kami') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg text-base font-bold text-slate-300 border border-slate-700 hover:bg-slate-800 hover:text-white transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pelajari Tentang Kami</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats / Feature Highlights -->
    <section class="bg-white py-12 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-3xl md:text-4xl font-extrabold text-blue-600 mb-1">10+</p>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Tahun Pengalaman</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-extrabold text-blue-600 mb-1">100+</p>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Proyek Selesai</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-extrabold text-blue-600 mb-1">50+</p>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Klien Korporat</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-extrabold text-blue-600 mb-1">24/7</p>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Layanan Teknis</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left: Description -->
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-3">Tentang
                        Perusahaan</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight mb-6">
                        Menyediakan Keahlian Teknik yang Tangguh & Presisi
                    </h2>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        CV Tomo Teknik Mandiri didirikan dengan visi untuk menjadi perusahaan terpercaya dan terdepan dalam menyediakan solusi terpadu di bidang kelistrikan, energi, sumber daya air, dan layanan perizinan usaha melalui pelayanan profesional, inovatif, berkualitas, serta berorientasi pada kepuasan pelanggan.
                    </p>
                    <p class="text-slate-600 leading-relaxed mb-8">
                        Dengan berorientasi pada kepuasan pelanggan, kami selalu menjamin setiap pekerjaan diselesaikan
                        secara aman, efisien, dan menggunakan standar mutu kerja terbaik.
                    </p>
                    <a href="{{ url('/tentang-kami') }}"
                        class="inline-flex items-center text-blue-600 font-bold hover:text-blue-700 hover:underline transition-colors">
                        Lihat Selengkapnya Profil Kami &rarr;
                    </a>
                </div>

                <!-- Right: Service Cards / Visual Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Mengutamakan Kepuasan Pelanggan</h3>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Layanan Terpadu</h3>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Pelayanan Cepat & Responsive</h3>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Pengurusan Perizinan Yang Mudah dan Transparan</h3>
                        </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section (Placeholder Tampilan Saja) -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                    Layanan Jasa Kami
                </h2>
                <p class="text-slate-500 text-sm md:text-base leading-relaxed">
                    Menyajikan berbagai bidang jasa serivice dan perizinan.
                </p>
            </div>

            <!-- Grid of Service Placeholders (Dinamis dari Database) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                @forelse($layanan as $item)
                    <div class="group bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 hover:border-blue-100 hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                        <div class="h-56 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            @if($item->gambar && file_exists(public_path($item->gambar)))
                                <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_layanan }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <svg class="w-16 h-16 text-slate-300 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            @endif
                            <div class="absolute top-4 left-4 px-3 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">
                                {{ $item->jenis_layanan }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-2 truncate">{{ $item->nama_layanan }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6 line-clamp-2 h-10">
                                {{ $item->deskripsi }}
                            </p>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100/50">
                                <div>
                                    <span class="block text-[10px] text-slate-400 font-medium">Biaya</span>
                                    <span class="text-base font-extrabold text-blue-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ url('/layanan/' . $item->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Booking Layanan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Belum ada data layanan tersedia.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="text-center">
                <a href="{{ url('/layanan') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-sm font-bold text-blue-600 border border-blue-200 hover:bg-blue-50 transition-colors">
                    <span>Lihat Semua Daftar Layanan</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Dokumentasi Proyek Section (Placeholder Tampilan Saja) -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight mb-4">
                    Dokumentasi Proyek Kami
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Tampilan dari dokumentasi proyek pengerjaan yang diselesaikan oleh tim teknisi kami di lapangan.
                </p>
            </div>

            <!-- Grid of Project Documentation (Dinamis dari Database) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @forelse($dokumentasi as $item)
                    <div
                        class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col">
                        <div class="h-64 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            @if($item->gambar && file_exists(public_path($item->gambar)))
                                <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_layanan }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <span
                                    class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-[11px] font-extrabold rounded-full uppercase tracking-wider mb-3">
                                    {{ $item->jenis_layanan }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">
                                    {{ $item->nama_layanan }}
                                </h3>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-100 shadow-sm px-6">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Belum ada dokumentasi proyek tersedia.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="text-center">
                <a href="{{ url('/dokumentasi') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-sm font-bold text-blue-600 border border-blue-200 hover:bg-blue-50 transition-colors">
                    <span>Lihat Semua Dokumentasi Proyek</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
@endsection