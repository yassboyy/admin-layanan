@extends('layouts.dashboard')

@section('title', 'Pelaporan Proyek')
@section('page_title', 'Pelaporan Proyek Lapangan')

@section('content')
    <div class="space-y-6">
        {{-- Flash Message --}}
        @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-semibold flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div
                class="p-4 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Table Card --}}
        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors space-y-6">
            
            {{-- Toolbar: Title & Buttons --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Daftar Pengerjaan Proyek</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pesanan dengan SPK yang telah disetujui Direktur</p>
                </div>
                <div class="flex items-center flex-wrap gap-2.5">
                    {{-- Sort by --}}
                    <div class="relative" id="sort-dropdown-wrapper">
                        <button type="button" id="sort-dropdown-btn"
                            class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-medium transition-all shadow-xs">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                            <span class="text-slate-600">
                                @if(request('sort') == 'terlama') Terlama
                                @elseif(request('sort') == 'id_asc') ID Pesanan (A-Z)
                                @elseif(request('sort') == 'id_desc') ID Pesanan (Z-A)
                                @else Sort by
                                @endif
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="sort-dropdown-menu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 focus:outline-none">
                            @php
                                $sortOptions = [
                                    'terbaru' => 'Terbaru (Default)',
                                    'terlama' => 'Terlama',
                                    'id_asc' => 'ID Pesanan (A-Z)',
                                    'id_desc' => 'ID Pesanan (Z-A)',
                                ];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/manager/pelaporan') . (count($qp) ? '?' . http_build_query($qp) : '');
                                @endphp
                                <a href="{{ $sortUrl }}"
                                    class="flex items-center justify-between px-4 py-2 text-xs font-semibold {{ $currentSort == $key ? 'text-teal-600 bg-teal-50' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span>{{ $label }}</span>
                                    @if($currentSort == $key)
                                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Filter Toggle Button --}}
                    @php 
                        $isFiltered = request()->hasAny(['q', 'layanan_id', 'status', 'tanggal_pesan_dari', 'tanggal_pesan_sampai', 'tanggal_pesan_mulai', 'tanggal_pesan_selesai', 'tanggal_selesai_dari', 'tanggal_selesai_sampai', 'tanggal_selesai_mulai', 'tanggal_selesai_akhir']); 
                        $activeCount = collect([
                            request('q'),
                            request('layanan_id'),
                            request('status'),
                            request('tanggal_pesan_dari') ?? request('tanggal_pesan_mulai'),
                            request('tanggal_selesai_dari') ?? request('tanggal_selesai_mulai')
                        ])->filter()->count();
                    @endphp
                    <div class="relative" id="filter-popover-wrapper">
                        <button type="button" id="filter-toggle-btn" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all border shadow-xs
                            {{ $isFiltered
                                ? 'bg-teal-600 text-white border-teal-600 shadow-teal-500/25 shadow-md'
                                : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                            <svg class="w-4 h-4 {{ $isFiltered ? 'text-white' : 'text-slate-400' }}" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter</span>
                            @if($isFiltered && $activeCount > 0)
                                <span
                                    class="flex items-center justify-center w-5 h-5 rounded-full bg-white/25 text-white text-[10px] font-bold">
                                    {{ $activeCount }}
                                </span>
                            @endif
                        </button>

                        {{-- ===== FILTER POPOVER CARD ===== --}}
                        <div id="filter-popover-card"
                            class="hidden absolute right-0 top-full mt-2 w-80 sm:w-88 bg-white rounded-2xl border border-slate-200 z-50 overflow-hidden"
                            style="box-shadow: 0 8px 30px -4px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04);">

                            <form method="GET" action="{{ url('/manager/pelaporan') }}" id="main-filter-form">
                                @if(request('sort'))
                                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                                @endif

                                {{-- Header --}}
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5 text-teal-600" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold text-slate-800">Filter Pelaporan</span>
                                    </div>
                                    <button type="button" id="filter-close-btn"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Scrollable Body --}}
                                <div class="px-4 py-4 space-y-4 max-h-[70vh] overflow-y-auto">

                                    {{-- Filter Layanan --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Layanan</label>
                                            <button type="button" id="reset-layanan-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <select id="filter-layanan" name="layanan_id"
                                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            <option value="">Semua Layanan</option>
                                            @foreach($layananList as $layanan)
                                                <option value="{{ $layanan->id }}" {{ request('layanan_id') == $layanan->id ? 'selected' : '' }}>
                                                    {{ $layanan->nama_layanan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Status Laporan --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Status
                                                Laporan</label>
                                            <button type="button" id="reset-status-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <input type="hidden" id="filter-status-value" name="status"
                                            value="{{ request('status', '') }}">
                                        @php
                                            $currentStatus = request('status', '');
                                            $pillDefs = [
                                                '' => 'Semua',
                                                'belum_lapor' => 'Belum Lapor',
                                                'menunggu' => 'Menunggu',
                                                'disetujui' => 'Dikonfirmasi',
                                                'ditolak' => 'Ditolak',
                                            ];
                                        @endphp
                                        <div class="grid grid-cols-2 gap-2" id="status-pills">
                                            @foreach($pillDefs as $val => $label)
                                                <button type="button" data-status-val="{{ $val }}"
                                                    class="status-pill-btn flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold border transition-all cursor-pointer
                                                        {{ (string)$currentStatus === (string)$val
                                                            ? 'bg-slate-800 text-white border-slate-800'
                                                            : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50' }}">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Tanggal Pesan --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Tanggal
                                                Pesan</label>
                                            <button type="button" id="reset-pesan-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Dari</p>
                                                <input type="date" id="filter-pesan-dari" name="tanggal_pesan_dari"
                                                    value="{{ request('tanggal_pesan_dari', request('tanggal_pesan_mulai')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Sampai</p>
                                                <input type="date" id="filter-pesan-sampai" name="tanggal_pesan_sampai"
                                                    value="{{ request('tanggal_pesan_sampai', request('tanggal_pesan_selesai')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Tanggal Selesai Laporan --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Tanggal
                                                Selesai</label>
                                            <button type="button" id="reset-selesai-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Dari</p>
                                                <input type="date" id="filter-selesai-dari" name="tanggal_selesai_dari"
                                                    value="{{ request('tanggal_selesai_dari', request('tanggal_selesai_mulai')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Sampai</p>
                                                <input type="date" id="filter-selesai-sampai" name="tanggal_selesai_sampai"
                                                    value="{{ request('tanggal_selesai_sampai', request('tanggal_selesai_akhir')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Kata Kunci --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Kata
                                                Kunci</label>
                                            <button type="button" id="reset-keyword-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <div class="relative">
                                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input type="text" id="filter-keyword" name="q" value="{{ request('q') }}"
                                                placeholder="ID Pesanan, Pelanggan..."
                                                class="w-full pl-11 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="flex items-center gap-2 px-4 py-3 bg-slate-50 border-t border-slate-100">
                                    <a href="{{ url('/manager/pelaporan') . (request('sort') ? '?sort=' . request('sort') : '') }}"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Reset All
                                    </a>
                                    <button type="submit"
                                        class="flex-[2] flex items-center justify-center gap-1.5 px-3 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-blue-500/30 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Apply
                                    </button>
                                </div>
                            </form>
                        </div>
                        {{-- ================================= --}}

                    </div>
                </div>
            </div>

            {{-- Active Filter Pills --}}
            @if($isFiltered)
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-slate-500 font-medium shrink-0">Filter aktif:</span>
                    @if(request('q'))
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>"{{ Str::limit(request('q'), 20) }}"</span>
                            <a href="{{ url('/manager/pelaporan') . '?' . http_build_query(request()->except('q')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter pencarian">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('layanan_id'))
                        @php
                            $selectedLayanan = $layananList->firstWhere('id', request('layanan_id'));
                        @endphp
                        @if($selectedLayanan)
                            <span
                                class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                                <span>Layanan: {{ $selectedLayanan->nama_layanan }}</span>
                                <a href="{{ url('/manager/pelaporan') . '?' . http_build_query(request()->except('layanan_id')) }}"
                                    class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                    title="Hapus filter layanan">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                    @endif
                    @if(request('status'))
                        @php
                            $mngStatusLabels = [
                                'belum_lapor' => 'Belum Laporan',
                                'menunggu' => 'Menunggu Konfirmasi',
                                'disetujui' => 'Dikonfirmasi',
                                'ditolak' => 'Ditolak'
                            ];
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Status: {{ $mngStatusLabels[request('status')] ?? request('status') }}</span>
                            <a href="{{ url('/manager/pelaporan') . '?' . http_build_query(request()->except('status')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter status">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('tanggal_pesan_dari') || request('tanggal_pesan_sampai') || request('tanggal_pesan_mulai') || request('tanggal_pesan_selesai'))
                        @php
                            $tPesanDari = request('tanggal_pesan_dari', request('tanggal_pesan_mulai'));
                            $tPesanSampai = request('tanggal_pesan_sampai', request('tanggal_pesan_selesai'));
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Pesan: {{ $tPesanDari }}{{ $tPesanSampai ? ' – ' . $tPesanSampai : '' }}</span>
                            <a href="{{ url('/manager/pelaporan') . '?' . http_build_query(request()->except(['tanggal_pesan_dari', 'tanggal_pesan_sampai', 'tanggal_pesan_mulai', 'tanggal_pesan_selesai'])) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tanggal pesan">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('tanggal_selesai_dari') || request('tanggal_selesai_sampai') || request('tanggal_selesai_mulai') || request('tanggal_selesai_akhir'))
                        @php
                            $tSelesaiDari = request('tanggal_selesai_dari', request('tanggal_selesai_mulai'));
                            $tSelesaiSampai = request('tanggal_selesai_sampai', request('tanggal_selesai_akhir'));
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Selesai: {{ $tSelesaiDari }}{{ $tSelesaiSampai ? ' – ' . $tSelesaiSampai : '' }}</span>
                            <a href="{{ url('/manager/pelaporan') . '?' . http_build_query(request()->except(['tanggal_selesai_dari', 'tanggal_selesai_sampai', 'tanggal_selesai_mulai', 'tanggal_selesai_akhir'])) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tanggal selesai">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    <a href="{{ url('/manager/pelaporan') . (request('sort') ? '?sort=' . request('sort') : '') }}"
                        class="ml-auto text-xs text-slate-500 hover:text-red-500 font-medium transition-colors">
                        Hapus semua
                    </a>
                </div>
            @endif

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 divide-x divide-slate-100 dark:divide-slate-700">
                            <th class="py-3.5 px-4 text-center w-14">No</th>
                            <th class="py-3.5 px-5 text-left">ID Pesanan</th>
                            <th class="py-3.5 px-5 text-left">Nama Pelanggan</th>
                            <th class="py-3.5 px-5 text-left">Layanan</th>
                            <th class="py-3.5 px-5 text-left">Alat yang Digunakan</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Tanggal Pesan</th>
                            <th class="py-3.5 px-4 text-center">Tanggal Selesai</th>
                            <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($orders as $index => $order)
                            @php
                                $laporan = $order->pelaporanTeknisi->last();
                                $valLaporan = $order->getValidasi('laporan_teknisi');

                                if (!$laporan) {
                                    $statusKey = 'belum_laporan';
                                    $statusText = 'Belum Laporan';
                                    $statusClass = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300';
                                } elseif ($valLaporan && $valLaporan->status_dokumen === 'disetujui') {
                                    $statusKey = 'dikonfirmasi';
                                    $statusText = 'Dikonfirmasi';
                                    $statusClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300';
                                } elseif ($valLaporan && $valLaporan->status_dokumen === 'ditolak') {
                                    $statusKey = 'ditolak';
                                    $statusText = 'Ditolak';
                                    $statusClass = 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300';
                                } else {
                                    $statusKey = 'menunggu_konfirmasi';
                                    $statusText = 'Menunggu Konfirmasi';
                                    $statusClass = 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300';
                                }

                                $namaLayananList = $order->pemesananLayanan->map(function ($pl) {
                                    return $pl->layanan ? $pl->layanan->nama_layanan : $pl->nama_layanan;
                                })->filter()->implode(', ');
                                if (empty($namaLayananList)) {
                                    $namaLayananList = '-';
                                }

                                $alatDipakai = $laporan ? $laporan->nama_alat : '-';

                                $tglSelesaiFormatted = ($statusKey === 'dikonfirmasi' && $laporan && $laporan->tanggal_selesai)
                                    ? $laporan->tanggal_selesai->format('d/m/Y')
                                    : '-';

                                $tglPesanFormatted = $order->created_at ? $order->created_at->format('d/m/Y') : '-';
                                $rawTglSelesai = ($laporan && $laporan->tanggal_selesai) ? $laporan->tanggal_selesai->format('Y-m-d') : date('Y-m-d');
                                $laporanGambar = $laporan ? asset($laporan->gambar) : '';
                                $alasanTolak = ($valLaporan && $valLaporan->status_dokumen === 'ditolak') ? ($valLaporan->alasan ?? '') : '';
                                $servicesData = $order->pemesananLayanan->map(fn($pl) => ['nama' => $pl->nama_layanan, 'jumlah' => $pl->jumlah])->values();
                            @endphp

                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-searchable
                                data-po="{{ $order->id_pesanan }}" data-pelanggan="{{ $order->nama_pelanggan }}" data-layanan="{{ $namaLayananList }}">
                                
                                <td class="px-4 py-3.5 text-center text-slate-500 dark:text-slate-400 font-semibold">
                                    {{ $index + 1 }}
                                    <span id="services-data-{{ $order->id }}" class="hidden">@json($servicesData)</span>
                                </td>

                                <td class="px-5 py-3.5 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                    {{ $order->id_pesanan }}
                                </td>

                                <td class="px-5 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $order->nama_pelanggan ?? '-' }}
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <div class="space-y-1.5 min-w-[180px] max-w-xs">
                                        @if($order->pemesananLayanan && $order->pemesananLayanan->count() > 0)
                                            @foreach($order->pemesananLayanan as $item)
                                                <div class="flex items-start gap-2">
                                                    <span
                                                        class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 mt-1.5 shrink-0"></span>
                                                    <div class="leading-tight">
                                                        <span
                                                            class="font-bold text-slate-800 dark:text-slate-200 text-xs sm:text-sm">
                                                            {{ $item->nama_layanan }}
                                                        </span>
                                                        @if($item->jumlah > 1)
                                                            <span
                                                                class="text-[11px] font-extrabold text-blue-600 dark:text-blue-400 ml-1">
                                                                (×{{ $item->jumlah }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                    {{ $alatDipakai }}
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-center text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                    {{ $tglPesanFormatted }}
                                </td>

                                <td class="px-4 py-3.5 text-center text-xs font-semibold whitespace-nowrap {{ $statusKey === 'dikonfirmasi' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                    {{ $tglSelesaiFormatted }}
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @if($statusKey === 'belum_laporan' || $statusKey === 'ditolak')
                                        <button type="button"
                                            onclick="openModalLaporkan('{{ $order->id }}', '{{ addslashes($order->id_pesanan) }}', '{{ addslashes($order->nama_pelanggan) }}', '{{ addslashes($alatDipakai !== '-' ? $alatDipakai : '') }}', '{{ $rawTglSelesai }}', '{{ addslashes($alasanTolak) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-all shadow-xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>{{ $statusKey === 'ditolak' ? 'Lapor Ulang' : 'Laporkan' }}</span>
                                        </button>
                                    @else
                                        <button type="button"
                                            onclick="openModalDetailLaporan('{{ $order->id }}', '{{ addslashes($order->id_pesanan) }}', '{{ addslashes($order->nama_pelanggan) }}', '{{ addslashes($alatDipakai) }}', '{{ $tglPesanFormatted }}', '{{ $tglSelesaiFormatted }}', '{{ $statusText }}', '{{ $statusKey }}', '{{ $laporanGambar }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada pesanan yang siap dilaporkan (menunggu SPK disetujui Direktur).
                                </td>
                            </tr>
                        @endforelse
                        <tr id="empty-search-row" style="display: none;">
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada data pengerjaan yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ==================== MODAL LAPORKAN (GAYA DETAIL DIREKTUR) ==================== --}}
    <div id="modal-laporkan"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150 relative z-10">
            
            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                    <div
                        class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100" id="modal-laporkan-title">Form Pelaporan Pengerjaan Teknisi</h3>
                </div>
                <button type="button" onclick="closeModalLaporkan()"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ url('/manager/pelaporan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pemesanan_id" id="laporkan-pemesanan-id" required>

                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    {{-- Alert Alasan Penolakan jika ada --}}
                    <div id="laporkan-alasan-box" class="hidden p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-xs text-red-700 dark:text-red-300">
                        <span class="font-bold block mb-0.5">Alasan Penolakan Sebelumnya:</span>
                        <p id="laporkan-alasan-text">-</p>
                    </div>

                    {{-- Grid Informasi Pesanan & Tanggal Selesai --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                                Nomor PO / ID Pesanan
                            </span>
                            <span class="text-sm font-black text-blue-600 dark:text-blue-400" id="laporkan-no-po">-</span>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                                Nama Pelanggan
                            </span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200" id="laporkan-nama-pelanggan">-</span>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                                Layanan
                            </span>
                            <div id="laporkan-layanan" class="space-y-1">
                                <!-- Populated via JS -->
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                            <label for="laporkan-tanggal-selesai" class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                                Tanggal Selesai Pengerjaan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_selesai" id="laporkan-tanggal-selesai" required
                                class="w-full px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Alat yang Digunakan (Box Input) --}}
                    <div class="bg-slate-50 dark:bg-slate-700/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                        <label for="laporkan-nama-alat" class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1.5">
                            Alat yang Digunakan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_alat" id="laporkan-nama-alat" required
                            placeholder="Contoh: Tang Ampere, Obeng Set, Kabel NYY, dll."
                            class="w-full px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Foto / Dokumentasi Hasil Pengerjaan (Upload) --}}
                    <div>
                        <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-2">
                            Foto / Dokumentasi Hasil Pengerjaan <span class="text-red-500">*</span>
                        </label>
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 space-y-3">
                            <input type="file" name="gambar" id="laporkan-file-input" accept="image/*" required onchange="previewUploadImage(this)"
                                class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-600 dark:file:bg-blue-900/30 dark:file:text-blue-400 hover:file:bg-blue-100 cursor-pointer">
                            
                            {{-- Info file & Tombol Lihat Foto (tanpa menampilkan gambar langsung di form) --}}
                            <div id="laporkan-preview-box" class="hidden flex items-center justify-between gap-3 p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-600 shadow-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p id="laporkan-file-name" class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">File terpilih</p>
                                        <p id="laporkan-file-size" class="text-[10px] text-slate-400 font-medium">Siap diunggah</p>
                                    </div>
                                </div>
                                <a id="laporkan-lihat-foto-btn" href="#" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs shrink-0 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>Lihat Foto</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/30">
                    <button type="button" onclick="closeModalLaporkan()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Kirim Laporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL DETAIL (GAYA DIREKTUR) ==================== --}}
    <div id="modal-detail-laporan"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150 relative z-10">
            
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                    <div
                        class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Detail Laporan Pengerjaan Teknisi</h3>
                </div>
                <button type="button" onclick="closeModalDetailLaporan()"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                            Nomor PO / ID Pesanan
                        </span>
                        <span class="text-sm font-black text-blue-600 dark:text-blue-400" id="detail-no-po">-</span>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                            Nama Pelanggan
                        </span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200" id="detail-pelanggan">-</span>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                            Layanan
                        </span>
                        <div id="detail-layanan" class="space-y-1">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">
                            Tanggal Selesai Pengerjaan
                        </span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200" id="detail-tgl-selesai">-</span>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1.5">
                        Alat yang Digunakan
                    </span>
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-relaxed" id="detail-alat">-</p>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-2">
                        Foto / Dokumentasi Hasil Pengerjaan
                    </span>
                    <div id="detail-gambar-container">
                        <a id="detail-gambar-btn" href="#" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Lihat Dokumentasi Pekerjaan</span>
                        </a>
                        <div id="detail-no-gambar"
                            class="hidden p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-100 dark:border-slate-700 text-xs text-slate-400 italic">
                            Belum ada foto / dokumentasi pengerjaan yang diunggah.
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-start bg-slate-50/50 dark:bg-slate-700/30">
                <button type="button" onclick="closeModalDetailLaporan()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Script Controller --}}
    <script>
        function renderServicesById(pemesananId) {
            let parsed = [];
            const el = document.getElementById('services-data-' + pemesananId);
            if (el) {
                try {
                    parsed = JSON.parse(el.textContent);
                } catch (e) {
                    console.error('Error parsing services data:', e);
                }
            }

            if (Array.isArray(parsed) && parsed.length > 0) {
                return parsed.map(s => `
                    <div class="flex items-start gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 mt-1 shrink-0"></span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">
                            ${s.nama}
                            ${s.jumlah > 1 ? `<span class="text-[10px] text-blue-600 dark:text-blue-400 font-extrabold ml-0.5">(×${s.jumlah})</span>` : ''}
                        </span>
                    </div>
                `).join('');
            }
            return '<span class="text-xs font-semibold text-slate-800 dark:text-slate-200">-</span>';
        }

        let currentUploadedPhotoUrl = '';
        let currentUploadedFileName = '';

        function openModalLaporkan(pemesananId, noPo, namaPelanggan, alatDipakai, tglSelesai, alasanTolak) {
            document.getElementById('laporkan-pemesanan-id').value = pemesananId;
            document.getElementById('laporkan-no-po').textContent = noPo;
            document.getElementById('laporkan-nama-pelanggan').textContent = namaPelanggan;
            document.getElementById('laporkan-layanan').innerHTML = renderServicesById(pemesananId);
            document.getElementById('laporkan-nama-alat').value = alatDipakai || '';
            document.getElementById('laporkan-tanggal-selesai').value = tglSelesai || '';

            const alasanBox = document.getElementById('laporkan-alasan-box');
            const alasanText = document.getElementById('laporkan-alasan-text');
            if (alasanTolak && alasanTolak.trim() !== '') {
                alasanText.textContent = alasanTolak;
                alasanBox.classList.remove('hidden');
            } else {
                alasanBox.classList.add('hidden');
            }

            // Reset input file & state tombol lihat foto
            const fileInput = document.getElementById('laporkan-file-input');
            if (fileInput) fileInput.value = '';
            const previewBox = document.getElementById('laporkan-preview-box');
            if (previewBox) previewBox.classList.add('hidden');
            const btn = document.getElementById('laporkan-lihat-foto-btn');
            if (btn) btn.href = '#';

            if (currentUploadedPhotoUrl && currentUploadedPhotoUrl.startsWith('blob:')) {
                URL.revokeObjectURL(currentUploadedPhotoUrl);
            }
            currentUploadedPhotoUrl = '';
            currentUploadedFileName = '';

            document.getElementById('modal-laporkan').classList.remove('hidden');
        }

        function closeModalLaporkan() {
            document.getElementById('modal-laporkan').classList.add('hidden');
        }

        function previewUploadImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                currentUploadedFileName = file.name;

                let sizeStr = '';
                if (file.size < 1024 * 1024) {
                    sizeStr = (file.size / 1024).toFixed(1) + ' KB';
                } else {
                    sizeStr = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                }

                if (currentUploadedPhotoUrl && currentUploadedPhotoUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(currentUploadedPhotoUrl);
                }
                currentUploadedPhotoUrl = URL.createObjectURL(file);

                const nameEl = document.getElementById('laporkan-file-name');
                const sizeEl = document.getElementById('laporkan-file-size');
                const box = document.getElementById('laporkan-preview-box');
                const btn = document.getElementById('laporkan-lihat-foto-btn');

                if (nameEl) nameEl.textContent = file.name;
                if (sizeEl) sizeEl.textContent = `${sizeStr} • Siap diunggah`;
                if (btn) btn.href = currentUploadedPhotoUrl;
                if (box) box.classList.remove('hidden');
            } else {
                if (currentUploadedPhotoUrl && currentUploadedPhotoUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(currentUploadedPhotoUrl);
                }
                currentUploadedPhotoUrl = '';
                currentUploadedFileName = '';
                const btn = document.getElementById('laporkan-lihat-foto-btn');
                if (btn) btn.href = '#';
                const box = document.getElementById('laporkan-preview-box');
                if (box) box.classList.add('hidden');
            }
        }

        function openModalDetailLaporan(pemesananId, noPo, namaPelanggan, alatDipakai, tglPesan, tglSelesai, statusText, statusKey, gambarUrl) {
            document.getElementById('detail-no-po').textContent = noPo;
            document.getElementById('detail-pelanggan').textContent = namaPelanggan;
            document.getElementById('detail-layanan').innerHTML = renderServicesById(pemesananId);
            document.getElementById('detail-tgl-selesai').textContent = tglSelesai || '-';
            document.getElementById('detail-alat').textContent = (alatDipakai && alatDipakai !== '-') ? alatDipakai : '-';

            const btn = document.getElementById('detail-gambar-btn');
            const noImg = document.getElementById('detail-no-gambar');
            if (gambarUrl && gambarUrl.trim() !== '') {
                btn.href = gambarUrl;
                btn.classList.remove('hidden');
                noImg.classList.add('hidden');
            } else {
                btn.classList.add('hidden');
                noImg.classList.remove('hidden');
            }

            document.getElementById('modal-detail-laporan').classList.remove('hidden');
        }

        function closeModalDetailLaporan() {
            document.getElementById('modal-detail-laporan').classList.add('hidden');
        }

        // Close on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModalLaporkan();
                closeModalDetailLaporan();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const sortBtn = document.getElementById('sort-dropdown-btn');
            const sortMenu = document.getElementById('sort-dropdown-menu');
            const sortWrapper = document.getElementById('sort-dropdown-wrapper');
            const filterBtn = document.getElementById('filter-toggle-btn');
            const filterCard = document.getElementById('filter-popover-card');
            const filterClose = document.getElementById('filter-close-btn');
            const filterWrap = document.getElementById('filter-popover-wrapper');

            // ---- Sort dropdown toggle ----
            if (sortBtn && sortMenu) {
                sortBtn.addEventListener('click', e => {
                    e.stopPropagation();
                    sortMenu.classList.toggle('hidden');
                    if (filterCard) filterCard.classList.add('hidden');
                });
            }

            // ---- Filter popover toggle ----
            if (filterBtn && filterCard) {
                filterBtn.addEventListener('click', e => {
                    e.stopPropagation();
                    filterCard.classList.toggle('hidden');
                    if (sortMenu) sortMenu.classList.add('hidden');
                });
            }

            if (filterClose && filterCard) {
                filterClose.addEventListener('click', e => {
                    e.stopPropagation();
                    filterCard.classList.add('hidden');
                });
            }

            // ---- Click outside to close ----
            document.addEventListener('click', e => {
                if (sortWrapper && !sortWrapper.contains(e.target) && sortMenu)
                    sortMenu.classList.add('hidden');
                if (filterWrap && !filterWrap.contains(e.target) && filterCard)
                    filterCard.classList.add('hidden');
            });

            if (filterCard) {
                filterCard.addEventListener('click', e => e.stopPropagation());
            }

            // ---- Status pill selector ----
            const activeClass  = 'bg-slate-800 text-white border-slate-800';
            const idleClass    = 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50';
            const pillBase     = 'status-pill-btn flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold border transition-all cursor-pointer';

            const statusHidden = document.getElementById('filter-status-value');
            const pills = document.querySelectorAll('.status-pill-btn');

            pills.forEach(pill => {
                pill.addEventListener('click', () => {
                    const val = pill.dataset.statusVal;
                    if (statusHidden) statusHidden.value = val;
                    pills.forEach(p => {
                        p.className = pillBase + ' ' + (p.dataset.statusVal === val ? activeClass : idleClass);
                    });
                });
            });

            // ---- Reset buttons ----
            const rLayananBtn = document.getElementById('reset-layanan-btn');
            if (rLayananBtn) rLayananBtn.addEventListener('click', () => {
                const sel = document.getElementById('filter-layanan');
                if (sel) sel.value = '';
            });

            const rsBtn = document.getElementById('reset-status-btn');
            if (rsBtn) rsBtn.addEventListener('click', () => {
                if (statusHidden) statusHidden.value = '';
                pills.forEach(p => {
                    p.className = pillBase + ' ' + (p.dataset.statusVal === '' ? activeClass : idleClass);
                });
            });

            const rPesanBtn = document.getElementById('reset-pesan-btn');
            if (rPesanBtn) rPesanBtn.addEventListener('click', () => {
                const d1 = document.getElementById('filter-pesan-dari');
                const d2 = document.getElementById('filter-pesan-sampai');
                if (d1) d1.value = '';
                if (d2) d2.value = '';
            });

            const rSelesaiBtn = document.getElementById('reset-selesai-btn');
            if (rSelesaiBtn) rSelesaiBtn.addEventListener('click', () => {
                const d1 = document.getElementById('filter-selesai-dari');
                const d2 = document.getElementById('filter-selesai-sampai');
                if (d1) d1.value = '';
                if (d2) d2.value = '';
            });

            const rkBtn = document.getElementById('reset-keyword-btn');
            if (rkBtn) rkBtn.addEventListener('click', () => {
                const kw = document.getElementById('filter-keyword');
                if (kw) kw.value = '';
            });
        });
    </script>
@endsection