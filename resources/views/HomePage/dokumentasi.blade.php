@extends('layouts.app')

@section('title', 'Dokumentasi')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Dokumentasi Proyek</h1>
        </div>
    </section>

    <!-- Projects Documentation Section -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($dokumentasi->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-100 shadow-sm max-w-md mx-auto px-6">
                    <div
                        class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Dokumentasi Proyek</h3>
                    <p class="text-xs text-slate-400">Dokumentasi portofolio pengerjaan akan segera diperbarui oleh admin.</p>
                </div>
            @else
                <!-- Grid of Project Cards (Dinamis dari Database) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($dokumentasi as $item)
                        <div
                            class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col">
                            <div class="h-64 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                                @if($item->gambar && file_exists(public_path($item->gambar)))
                                    <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_layanan }}"
                                        class="w-full h-full object-cover">
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
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection