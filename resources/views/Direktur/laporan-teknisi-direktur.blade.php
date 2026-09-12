@extends('layouts.dashboard')

@section('title', 'Laporan Teknisi')
@section('page_title', 'Konfirmasi Pekerjaan Manager Teknisi')

@section('content')
    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs sm:text-sm flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold">Sukses:</span> {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div
                class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-800 text-xs sm:text-sm flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold">Terjadi Kesalahan:</span>
                    <ul class="list-disc list-inside mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Main Content Table Card --}}
        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors space-y-6">
            {{-- Toolbar: Title & Buttons --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Daftar Laporan Pekerjaan Teknisi</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Lihat dan konfirmasi laporan hasil pengerjaan dari Manager Teknisi</p>
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
                                @else Sort by
                                @endif
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="sort-dropdown-menu"
                            class="hidden absolute right-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 focus:outline-none">
                            @php
                                $sortOptions = [
                                    'terbaru' => 'Terbaru (Default)',
                                    'terlama' => 'Terlama',
                                ];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/direktur/laporan-teknisi') . (count($qp) ? '?' . http_build_query($qp) : '');
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
                        $isFiltered = request()->hasAny(['q', 'layanan_id', 'status', 'tanggal_selesai_dari', 'tanggal_selesai_sampai', 'tanggal_selesai_mulai', 'tanggal_selesai_akhir']); 
                        $activeCount = collect([
                            request('q'),
                            request('layanan_id'),
                            request('status'),
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

                            <form method="GET" action="{{ url('/direktur/laporan-teknisi') }}" id="main-filter-form">
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
                                        <span class="text-sm font-bold text-slate-800">Filter Laporan Teknisi</span>
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

                                    {{-- Status Konfirmasi --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Status
                                                Konfirmasi</label>
                                            <button type="button" id="reset-status-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <input type="hidden" id="filter-status-value" name="status"
                                            value="{{ request('status', '') }}">
                                        @php
                                            $currentStatus = request('status', '');
                                            $pillDefs = [
                                                '' => 'Semua',
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

                                    {{-- Tanggal Selesai --}}
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
                                                placeholder="ID Pesanan, Pelanggan, Alat..."
                                                class="w-full pl-11 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="flex items-center gap-2 px-4 py-3 bg-slate-50 border-t border-slate-100">
                                    <a href="{{ url('/direktur/laporan-teknisi') . (request('sort') ? '?sort=' . request('sort') : '') }}"
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
                            <a href="{{ url('/direktur/laporan-teknisi') . '?' . http_build_query(request()->except('q')) }}"
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
                                <a href="{{ url('/direktur/laporan-teknisi') . '?' . http_build_query(request()->except('layanan_id')) }}"
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
                            $stLabels = ['menunggu' => 'Menunggu Konfirmasi', 'disetujui' => 'Dikonfirmasi', 'ditolak' => 'Ditolak'];
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Status: {{ $stLabels[request('status')] ?? request('status') }}</span>
                            <a href="{{ url('/direktur/laporan-teknisi') . '?' . http_build_query(request()->except('status')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter status">
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
                            <a href="{{ url('/direktur/laporan-teknisi') . '?' . http_build_query(request()->except(['tanggal_selesai_dari', 'tanggal_selesai_sampai', 'tanggal_selesai_mulai', 'tanggal_selesai_akhir'])) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tanggal selesai">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    <a href="{{ url('/direktur/laporan-teknisi') . (request('sort') ? '?sort=' . request('sort') : '') }}"
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
                            <th class="py-3.5 px-5 text-left">Pelanggan</th>
                            <th class="py-3.5 px-5 text-left">Layanan</th>
                            <th class="py-3.5 px-5 text-left">Alat yang Digunakan</th>
                            <th class="py-3.5 px-4 text-center">Tanggal Selesai</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($reports as $index => $rep)
                            @php
                                $order = $rep->pemesanan;
                                $valDok = $order ? $order->getValidasi('laporan_teknisi') : null;

                                if ($valDok && $valDok->status_dokumen === 'disetujui') {
                                    $statusKey = 'dikonfirmasi';
                                    $statusText = 'Dikonfirmasi';
                                    $statusClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300';
                                } elseif ($valDok && $valDok->status_dokumen === 'ditolak') {
                                    $statusKey = 'ditolak';
                                    $statusText = 'Ditolak';
                                    $statusClass = 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300';
                                } else {
                                    $statusKey = 'menunggu';
                                    $statusText = 'Menunggu Konfirmasi';
                                    $statusClass = 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300';
                                }

                                $namaLayananList = $order ? $order->pemesananLayanan->map(function ($pl) {
                                    return $pl->layanan ? $pl->layanan->nama_layanan : $pl->nama_layanan;
                                })->filter()->implode(', ') : ($rep->nama_layanan ?? '-');
                                if (empty($namaLayananList)) {
                                    $namaLayananList = '-';
                                }

                                $noPo = $order ? $order->id_pesanan : ('TTM-' . $rep->pemesanan_id);
                                $namaPelanggan = $order ? ($order->nama_pelanggan ?? '-') : '-';
                                $tglSelesaiFormatted = $rep->tanggal_selesai ? $rep->tanggal_selesai->format('d/m/Y') : '-';
                                $gambarUrl = $rep->gambar ? asset($rep->gambar) : '';
                                $alasanTolak = ($valDok && $valDok->status_dokumen === 'ditolak') ? ($valDok->alasan ?? '') : '';
                                $servicesData = ($order && $order->pemesananLayanan)
                                    ? $order->pemesananLayanan->map(fn($pl) => ['nama' => $pl->nama_layanan, 'jumlah' => $pl->jumlah])->values()
                                    : [['nama' => ($rep->nama_layanan ?? '-'), 'jumlah' => 1]];
                            @endphp

                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-searchable
                                data-po="{{ $noPo }}" data-pelanggan="{{ $namaPelanggan }}"
                                data-layanan="{{ $namaLayananList }}" data-alat="{{ $rep->nama_alat }}">

                                <td class="px-4 py-3.5 text-center text-slate-500 dark:text-slate-400 font-semibold">
                                    {{ $index + 1 }}
                                    <span id="services-data-{{ $rep->id }}" class="hidden">@json($servicesData)</span>
                                </td>

                                <td class="px-5 py-3.5 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                    {{ $noPo }}
                                </td>

                                <td class="px-5 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $namaPelanggan }}
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <div class="space-y-1.5 min-w-[180px] max-w-xs">
                                        @if($order && $order->pemesananLayanan && $order->pemesananLayanan->count() > 0)
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
                                            <span
                                                class="text-xs text-slate-800 dark:text-slate-200 font-semibold">{{ $rep->nama_layanan ?? '-' }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                    {{ $rep->nama_alat ?? '-' }}
                                </td>

                                <td
                                    class="px-4 py-3.5 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                    {{ $tglSelesaiFormatted }}
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                    @if($statusKey === 'ditolak' && $alasanTolak)
                                        <p class="text-[10px] text-red-500 dark:text-red-400 mt-1 max-w-[140px] truncate mx-auto"
                                            title="{{ $alasanTolak }}">
                                            Ket: {{ $alasanTolak }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                        {{-- Tombol Detail --}}
                                        <button type="button"
                                            onclick="openDetailModal('{{ $rep->id }}', '{{ $noPo }}', '{{ addslashes($namaPelanggan) }}', '{{ addslashes($rep->nama_alat) }}', '{{ $tglSelesaiFormatted }}', '{{ $gambarUrl }}', '{{ $statusKey }}', '{{ addslashes($alasanTolak) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 rounded-lg text-xs font-bold transition-all cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </button>

                                        {{-- Tombol Setujui / Konfirmasi --}}
                                        @if($statusKey !== 'dikonfirmasi')
                                            <form action="{{ url('/direktur/laporan-teknisi/' . $rep->id . '/konfirmasi') }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui dan mengonfirmasi laporan pengerjaan teknisi untuk pesanan {{ $noPo }}?')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            {{-- Tombol Tolak --}}
                                            <button type="button" onclick="openRejectModal('{{ $rep->id }}', '{{ $noPo }}')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Tolak</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data laporan pengerjaan dari Manager Teknisi.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="empty-search-row" style="display: none;">
                            <td colspan="9" class="py-8 text-center text-slate-400 dark:text-slate-500 text-sm">
                                Laporan pengerjaan tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- MODAL DETAIL LAPORAN PENGERJAAN --}}
    <div id="detail-modal"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
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
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100" id="modal-detail-title">Detail Laporan
                        Pengerjaan Teknisi</h3>
                </div>
                <button type="button" onclick="closeDetailModal()"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span
                            class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Nomor
                            PO / ID Pesanan</span>
                        <span class="text-sm font-black text-blue-600 dark:text-blue-400" id="detail-modal-po">-</span>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span
                            class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Nama
                            Pelanggan</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200"
                            id="detail-modal-pelanggan">-</span>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span
                            class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Layanan</span>
                        <div id="detail-modal-layanan" class="space-y-1">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span
                            class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Tanggal
                            Selesai Pengerjaan</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200" id="detail-modal-tgl">-</span>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span
                        class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1.5">Alat
                            yang Digunakan</span>
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-relaxed"
                        id="detail-modal-alat">-</p>
                </div>

                <div>
                    <span
                        class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-2">Foto
                        / Dokumentasi Hasil Pengerjaan</span>
                    <div id="detail-modal-image-wrapper">
                        <a id="detail-modal-img-btn" href="#" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Lihat Dokumentasi Pekerjaan</span>
                        </a>
                        <div id="detail-modal-no-img"
                            class="hidden p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-100 dark:border-slate-700 text-xs text-slate-400 italic">
                            Belum ada foto / dokumentasi pengerjaan yang diunggah.
                        </div>
                    </div>
                </div>

                <div id="detail-modal-reject-box"
                    class="hidden p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-xs text-red-700 dark:text-red-300">
                    <span class="font-bold block mb-0.5">Alasan Penolakan Sebelumnya:</span>
                    <p id="detail-modal-reject-reason">-</p>
                </div>
            </div>

            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/30">
                <button type="button" onclick="closeDetailModal()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Tutup</span>
                </button>

                <div class="flex items-center gap-2" id="detail-modal-action-buttons">
                    <!-- Dynamic action buttons injected via JS -->
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK LAPORAN PENGERJAAN --}}
    <div id="reject-modal"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full border border-slate-100 dark:border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
            <form id="reject-form" action="" method="POST">
                @csrf
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-red-50/50 dark:bg-red-950/20">
                    <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="text-sm font-bold">Tolak Laporan Pengerjaan</h3>
                    </div>
                    <button type="button" onclick="closeRejectModal()"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Anda akan menolak laporan pengerjaan teknisi untuk pesanan <span
                            class="font-bold text-slate-900 dark:text-white" id="reject-modal-po">-</span>. Berikan catatan
                        atau alasan penolakan agar manager teknisi dapat memperbaikinya.
                    </p>

                    <div>
                        <label for="alasan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alasan
                            Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="alasan" id="alasan" rows="3" required
                            placeholder="Contoh: Foto dokumentasi kurang jelas / Alat yang digunakan belum lengkap..."
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 placeholder-slate-400"></textarea>
                    </div>
                </div>

                <div
                    class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2 bg-slate-50/50 dark:bg-slate-700/30">
                    <button type="button" onclick="closeRejectModal()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-md cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span>Kirim Penolakan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ZOOM IMAGE PREVIEW --}}
    <div id="image-modal"
        class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <div class="max-w-4xl max-h-[90vh] relative" onclick="event.stopPropagation()">
            <button type="button" onclick="closeImageModal()"
                class="absolute -top-10 right-0 text-white hover:text-slate-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="preview-full-img" src="" alt="Preview Foto"
                class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl">
            <p id="preview-full-title" class="text-center text-xs text-slate-300 mt-2 font-bold"></p>
        </div>
    </div>

    {{-- JS SCRIPT UNTUK MODAL & FILTER --}}
    <script>
        // Search Filter
        const searchInput = document.getElementById('search-laporan-direktur');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const po = (row.getAttribute('data-po') || '').toLowerCase();
                    const pelanggan = (row.getAttribute('data-pelanggan') || '').toLowerCase();
                    const layanan = (row.getAttribute('data-layanan') || '').toLowerCase();
                    const alat = (row.getAttribute('data-alat') || '').toLowerCase();

                    if (po.includes(keyword) || pelanggan.includes(keyword) || layanan.includes(keyword) || alat.includes(keyword)) {
                        row.style.display = '';
                        visibleCount++;
                        const noCell = row.querySelector('td:first-child');
                        if (noCell) noCell.textContent = visibleCount;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const emptyRow = document.getElementById('empty-search-row');
                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            });
        }

        // Image Zoom Preview
        function previewImage(url, title) {
            document.getElementById('preview-full-img').src = url;
            document.getElementById('preview-full-title').textContent = 'Dokumentasi Pengerjaan: ' + title;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
        }

        function zoomCurrentImage() {
            const currentSrc = document.getElementById('detail-modal-img').src;
            const currentTitle = document.getElementById('detail-modal-po').textContent;
            if (currentSrc) {
                previewImage(currentSrc, currentTitle);
            }
        }

        // Detail Modal
        function openDetailModal(id, po, pelanggan, alat, tglSelesai, gambarUrl, status, alasanTolak) {
            document.getElementById('detail-modal-po').textContent = po;
            document.getElementById('detail-modal-pelanggan').textContent = pelanggan;
            document.getElementById('detail-modal-alat').textContent = alat || '-';
            document.getElementById('detail-modal-tgl').textContent = tglSelesai || '-';

            const layananContainer = document.getElementById('detail-modal-layanan');
            let parsedLayanan = [];
            const dataEl = document.getElementById('services-data-' + id);
            if (dataEl) {
                try {
                    parsedLayanan = JSON.parse(dataEl.textContent);
                } catch (e) {
                    console.error('Error parsing services:', e);
                }
            }

            if (Array.isArray(parsedLayanan) && parsedLayanan.length > 0) {
                layananContainer.innerHTML = parsedLayanan.map(s => `
                    <div class="flex items-start gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 mt-1 shrink-0"></span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">
                            ${s.nama}
                            ${s.jumlah > 1 ? `<span class="text-[10px] text-blue-600 dark:text-blue-400 font-extrabold ml-0.5">(×${s.jumlah})</span>` : ''}
                        </span>
                    </div>
                `).join('');
            } else {
                layananContainer.innerHTML = `<span class="text-xs font-semibold text-slate-800 dark:text-slate-200">-</span>`;
            }

            const btnEl = document.getElementById('detail-modal-img-btn');
            const noImgEl = document.getElementById('detail-modal-no-img');
            if (gambarUrl) {
                btnEl.href = gambarUrl;
                btnEl.classList.remove('hidden');
                noImgEl.classList.add('hidden');
            } else {
                btnEl.classList.add('hidden');
                noImgEl.classList.remove('hidden');
            }

            const rejectBox = document.getElementById('detail-modal-reject-box');
            if (status === 'ditolak' && alasanTolak) {
                document.getElementById('detail-modal-reject-reason').textContent = alasanTolak;
                rejectBox.classList.remove('hidden');
            } else {
                rejectBox.classList.add('hidden');
            }

            // Action Buttons in Modal
            const actionContainer = document.getElementById('detail-modal-action-buttons');
            actionContainer.innerHTML = '';

            if (status !== 'dikonfirmasi') {
                actionContainer.innerHTML = `
                                <form action="{{ url('/direktur/laporan-teknisi') }}/${id}/konfirmasi" method="POST" class="inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui laporan pengerjaan ini?')"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Setujui Laporan</span>
                                    </button>
                                </form>
                                <button type="button" onclick="closeDetailModal(); openRejectModal('${id}', '${po}')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-md cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Tolak Laporan</span>
                                </button>
                            `;
            }

            document.getElementById('detail-modal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }

        // Reject Modal
        function openRejectModal(id, po) {
            document.getElementById('reject-modal-po').textContent = po;
            document.getElementById('reject-form').action = "{{ url('/direktur/laporan-teknisi') }}/" + id + "/tolak";
            document.getElementById('alasan').value = '';
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

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