@extends('layouts.app')

@section('title', $layanan->nama_layanan)

@section('content')
    <!-- Page Header / Breadcrumb -->
    <section class="bg-slate-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-xs text-slate-400 mb-2">
                <a href="{{ url('/') }}" class="hover:text-white">Beranda</a> &sol;
                <a href="{{ url('/layanan') }}" class="hover:text-white">Layanan</a> &sol;
                <span class="text-slate-200">{{ $layanan->nama_layanan }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $layanan->nama_layanan }}</h1>
        </div>
    </section>

    <!-- Detail Section -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left: Service Details & FAQ -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Detail Card -->
                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm space-y-8">
                        <div
                            class="h-96 bg-slate-100 rounded-xl overflow-hidden flex items-center justify-center text-slate-400 border border-slate-200">
                            @if($layanan->gambar && file_exists(public_path($layanan->gambar)))
                                <img src="{{ asset($layanan->gambar) }}" alt="{{ $layanan->nama_layanan }}"
                                    class="w-full h-full object-cover">
                            @else
                                <svg class="w-20 h-20 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <span
                                class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full uppercase tracking-wider">
                                {{ $layanan->jenis_layanan }}
                            </span>
                            <h2 class="text-2xl font-bold text-slate-900 mt-6 mb-2">{{ $layanan->nama_layanan }}</h2>
                            <div class="text-xl font-extrabold text-blue-600 mb-6 flex items-center gap-2">
                                <span>Rp {{ number_format($layanan->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-slate-600 leading-relaxed space-y-4 text-sm sm:text-base">
                                {!! nl2br(e($layanan->deskripsi)) !!}
                            </div>

                            @if($layanan->dokumenLayanan && $layanan->dokumenLayanan->isNotEmpty())
                                <div class="mt-8 pt-6 border-t border-slate-100">
                                    <h3 class="text-sm font-bold text-slate-900 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Dokumen Persyaratan Layanan
                                    </h3>
                                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                                        Untuk memproses pesanan layanan ini, Anda akan diminta mengunggah dokumen-dokumen
                                        berikut pada detail pesanan Anda:
                                    </p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        @foreach($layanan->dokumenLayanan as $idx => $doc)
                                            <div
                                                class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs font-semibold text-slate-700">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold shrink-0">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <span>{{ $doc->nama_dokumen }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pertanyaan Umum (FAQ) Accordion -->
                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm space-y-6">
                        <h3 class="text-xl font-bold text-slate-900">Pertanyaan Umum</h3>

                        <div class="space-y-4">
                            <!-- FAQ Item 1 -->
                            <details class="group border-b border-slate-100 pb-5" open>
                                <summary
                                    class="flex justify-between items-center font-bold text-slate-900 cursor-pointer list-none text-sm sm:text-base">
                                    <span>Apakah dokumen atau persyaratan berbeda untuk setiap layanan?</span>
                                    <span class="transition group-open:rotate-180 text-slate-500">
                                        <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24" width="24"
                                            class="h-4 w-4">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </summary>
                                <p class="text-slate-500 text-xs sm:text-sm mt-3 leading-relaxed">
                                    Ya, persyaratan dapat berbeda tergantung jenis layanan yang dipilih. Informasi
                                    persyaratan akan diberikan kepada pelanggan sebelum proses dilakukan.
                                </p>
                            </details>

                            <!-- FAQ Item 2 -->
                            <details class="group border-b border-slate-100 pb-5" open>
                                <summary
                                    class="flex justify-between items-center font-bold text-slate-900 cursor-pointer list-none text-sm sm:text-base">
                                    <span>Berapa lama proses pengerjaan layanan?</span>
                                    <span class="transition group-open:rotate-180 text-slate-500">
                                        <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24" width="24"
                                            class="h-4 w-4">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </summary>
                                <p class="text-slate-500 text-xs sm:text-sm mt-3 leading-relaxed">
                                    Waktu pengerjaan bergantung pada jenis layanan, tingkat pekerjaan, kelengkapan dokumen,
                                    dan kondisi di lapangan.
                                </p>
                            </details>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Card / Booking Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm sticky top-24">
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Form Booking Layanan</h3>
                        <div class="mb-5">
                            <span class="block text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Biaya
                                Layanan</span>
                            <span class="text-2xl font-extrabold text-blue-600">Rp
                                {{ number_format($layanan->harga, 0, ',', '.') }}</span>
                        </div>

                        @if($errors->any())
                            <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-800 text-xs flex flex-col gap-1.5 shadow-sm">
                                <div class="flex items-center gap-2 font-bold">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Perhatian:</span>
                                </div>
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ url('/keranjang/tambah/' . $layanan->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <!-- Customer Name -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nama
                                    Pelanggan</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="nama_pelanggan" placeholder="Masukkan nama lengkap anda"
                                        required value="{{ old('nama_pelanggan', session('user.name')) }}" {{ session()->has('user') ? 'readonly' : '' }}
                                        class="w-full pl-11 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-semibold bg-slate-50/50 text-slate-800">
                                </div>
                            </div>

                            <!-- Full Address -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                    Alamat Lengkap Pengerjaan
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>
                                    <textarea name="alamat_lengkap" rows="3"
                                        placeholder="Masukkan alamat lengkap tempat pengerjaan..." required
                                        class="w-full pl-11 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-semibold bg-slate-50/50 text-slate-800 resize-none">{{ old('alamat_lengkap') }}</textarea>
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Nomor
                                    HP/WA</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="nomor_hp" placeholder="Contoh: 08123456789" required
                                        value="{{ old('nomor_hp') }}"
                                        class="w-full pl-11 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-semibold bg-slate-50/50 text-slate-800">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <input type="email" name="email" placeholder="contoh@email.com" required
                                        value="{{ old('email', session('user.email')) }}" {{ session()->has('user') ? 'readonly' : '' }}
                                        class="w-full pl-11 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-semibold bg-slate-50/50 text-slate-800">
                                </div>
                            </div>

                            <!-- Extra Notes -->
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan
                                    Tambahan</label>
                                <textarea name="catatan_tambahan" rows="3" placeholder="Tulis keluhan atau detail kendala"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-semibold bg-slate-50/50 text-slate-800 resize-none">{{ old('catatan_tambahan') }}</textarea>
                            </div>

                            <!-- Quantity Selector -->
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah
                                    / Kuantitas</label>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50/80">
                                        <button type="button"
                                            onclick="const el = document.getElementById('qty-input'); if(el.value > 1) el.value--;"
                                            class="px-3.5 py-2 text-slate-600 hover:bg-slate-200 font-bold transition-colors cursor-pointer text-sm">-</button>
                                        <input type="number" id="qty-input" name="jumlah" value="1" min="1" max="99"
                                            class="w-14 text-center py-2 text-sm font-extrabold bg-white border-x border-slate-200 focus:outline-none">
                                        <button type="button"
                                            onclick="const el = document.getElementById('qty-input'); el.value++;"
                                            class="px-3.5 py-2 text-slate-600 hover:bg-slate-200 font-bold transition-colors cursor-pointer text-sm">+</button>
                                    </div>
                                    <span class="text-xs text-slate-400 font-medium">Bisa pesan lebih dari satu</span>
                                </div>
                            </div>

                            <!-- Action button -->
                            @if(session()->has('user'))
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 py-3.5 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-md transition-colors text-center cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            @else
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 py-3.5 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-md transition-colors text-center cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <span>Daftar</span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
    </section>

@endsection