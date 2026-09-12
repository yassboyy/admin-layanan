@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-slate-900 via-slate-850 to-blue-950 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Tentang Kami</h1>
        </div>
    </section>

    <!-- Company Profile Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Company Image / Logo Showcase -->
                <div>
                    @php
                        $gambar = $tentangKami->gambar ?? 'images/logo.png';
                        $isCustomPhoto = !empty($gambar) && !str_contains($gambar, 'logo') && file_exists(public_path($gambar));
                    @endphp

                    @if($isCustomPhoto)
                        <div class="relative group rounded-3xl overflow-hidden border border-slate-200 shadow-2xl h-96">
                            <img src="{{ asset($gambar) }}" alt="Profil CV Tomo Teknik Mandiri"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent">
                            </div>
                            <div class="absolute bottom-6 left-6 right-6">
                                <span
                                    class="text-xs font-bold text-blue-400 uppercase tracking-widest block mb-1">Perusahaan</span>
                                <h3 class="text-lg font-bold text-white tracking-tight">CV TOMO TEKNIK MANDIRI</h3>
                            </div>
                        </div>
                    @else
                        <div class="relative group flex items-center justify-center">
                            <div
                                class="absolute -inset-2 bg-gradient-to-r from-blue-600 via-sky-500 to-indigo-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-35 transition duration-500">
                            </div>
                            <div
                                class="relative w-full h-96 bg-gradient-to-br from-slate-900 via-slate-850 to-blue-950 rounded-2xl flex flex-col items-center justify-center p-8 border border-slate-800 shadow-2xl overflow-hidden text-center">
                                <div
                                    class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(59,130,246,0.15),transparent_70%)] pointer-events-none">
                                </div>
                                <img src="{{ asset($gambar) }}" alt="Logo CV Tomo Teknik Mandiri"
                                    class="w-48 h-48 sm:w-52 sm:h-52 object-contain drop-shadow-[0_10px_25px_rgba(0,0,0,0.5)] mb-4 transform group-hover:scale-105 transition-transform duration-300">
                                <span class="text-lg font-extrabold text-white tracking-wide">CV TOMO TEKNIK MANDIRI</span>
                                <span class="text-xs text-blue-400 font-semibold tracking-wider mt-1">Solusi Teknik Profesional
                                    & Terpercaya</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Profile Info -->
                <div class="space-y-6">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-2">Profil
                            Perusahaan</span>
                        <h2 class="text-3xl font-bold text-slate-900 tracking-tight">
                            Dedikasi Terbaik untuk Rekayasa Teknik & Industri
                        </h2>
                    </div>

                    <div class="text-slate-600 leading-relaxed text-base space-y-4">
                        @if(!empty($tentangKami->deskripsi_profil))
                            {!! nl2br(e($tentangKami->deskripsi_profil)) !!}
                        @else
                            <p>CV Tomo Teknik Mandiri didirikan dengan tekad kuat untuk menghadirkan layanan teknik profesional
                                yang aman, handal, dan berorientasi pada kepuasan pelanggan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="py-20 bg-slate-50 border-t border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Visi -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-start">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Visi Perusahaan
                        </h3>
                    </div>
                    <p class="text-slate-600 leading-relaxed text-base">
                        {{ $tentangKami->visi ?? 'Menjadi perusahaan penyedia jasa teknik terintegrasi yang paling terpercaya, unggul dalam pelayanan, dan berkomitmen kuat terhadap kualitas kerja guna memajukan industri dan infrastruktur nasional.' }}
                    </p>
                </div>

                <!-- Misi -->
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-start">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Misi Perusahaan
                        </h3>
                    </div>

                    @php
                        $misiRaw = $tentangKami->misi ?? '';
                        $misiLines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $misiRaw)));
                    @endphp

                    @if(count($misiLines) > 0)
                        <ul class="space-y-3.5 text-slate-600 text-sm sm:text-base leading-relaxed">
                            @foreach($misiLines as $line)
                                @php
                                    $cleanLine = ltrim($line, '-*•1234567890. ');
                                @endphp
                                @if(!empty($cleanLine))
                                    <li class="flex items-start gap-3">
                                        <span class="w-2 h-2 rounded-full bg-blue-600 mt-2.5 shrink-0"></span>
                                        <span>{{ $cleanLine }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-slate-600 leading-relaxed text-base">{{ $misiRaw }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection