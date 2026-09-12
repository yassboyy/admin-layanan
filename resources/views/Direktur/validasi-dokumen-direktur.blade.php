@extends('layouts.dashboard')

@section('title', 'Validasi Dokumen')
@section('page_title', 'Validasi Dokumen Proyek')

@section('content')
    <div class="space-y-6">
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

        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors space-y-6">
            {{-- Toolbar: Title & Buttons --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Validasi Dokumen</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Daftar pengajuan dokumen proyek yang memerlukan validasi Direktur</p>
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
                                    $sortUrl = url('/direktur/validasi-dokumen') . (count($qp) ? '?' . http_build_query($qp) : '');
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
                        $isFiltered = request()->hasAny(['q', 'tipe_dokumen', 'status', 'tanggal_diajukan_dari', 'tanggal_diajukan_sampai', 'tanggal_diajukan_mulai', 'tanggal_diajukan_selesai', 'tanggal_validasi_dari', 'tanggal_validasi_sampai', 'tanggal_validasi_mulai', 'tanggal_validasi_selesai']); 
                        $activeCount = collect([
                            request('q'),
                            request('tipe_dokumen'),
                            request('status'),
                            request('tanggal_diajukan_dari') ?? request('tanggal_diajukan_mulai'),
                            request('tanggal_validasi_dari') ?? request('tanggal_validasi_mulai')
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

                            <form method="GET" action="{{ url('/direktur/validasi-dokumen') }}" id="main-filter-form">
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
                                        <span class="text-sm font-bold text-slate-800">Filter Validasi Dokumen</span>
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

                                    {{-- Tipe Dokumen --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Tipe
                                                Dokumen</label>
                                            <button type="button" id="reset-tipe-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <select id="filter-tipe-dokumen" name="tipe_dokumen"
                                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            <option value="">Semua Tipe Dokumen</option>
                                            <option value="bukti_po" {{ request('tipe_dokumen') == 'bukti_po' ? 'selected' : '' }}>Dokumen P.O</option>
                                            <option value="dokumen_persyaratan" {{ request('tipe_dokumen') == 'dokumen_persyaratan' ? 'selected' : '' }}>Dokumen Persyaratan Layanan</option>
                                            <option value="bukti_surat_perjanjian_kerja" {{ request('tipe_dokumen') == 'bukti_surat_perjanjian_kerja' ? 'selected' : '' }}>Surat Perjanjian Kerja (SPK)</option>
                                            <option value="surat_jalan" {{ request('tipe_dokumen') == 'surat_jalan' ? 'selected' : '' }}>Surat Jalan</option>
                                            <option value="berita_acara" {{ request('tipe_dokumen') == 'berita_acara' ? 'selected' : '' }}>Berita Acara (BAST)</option>
                                            <option value="invoice" {{ request('tipe_dokumen') == 'invoice' ? 'selected' : '' }}>Invoice Tagihan</option>
                                        </select>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Status Validasi --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Status
                                                Validasi</label>
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
                                                'disetujui' => 'Disetujui',
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

                                    {{-- Tanggal Diajukan --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Tanggal
                                                Diajukan</label>
                                            <button type="button" id="reset-diajukan-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Dari</p>
                                                <input type="date" id="filter-diajukan-dari" name="tanggal_diajukan_dari"
                                                    value="{{ request('tanggal_diajukan_dari', request('tanggal_diajukan_mulai')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Sampai</p>
                                                <input type="date" id="filter-diajukan-sampai" name="tanggal_diajukan_sampai"
                                                    value="{{ request('tanggal_diajukan_sampai', request('tanggal_diajukan_selesai')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Tanggal Divalidasi --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Tanggal
                                                Divalidasi</label>
                                            <button type="button" id="reset-validasi-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Dari</p>
                                                <input type="date" id="filter-validasi-dari" name="tanggal_validasi_dari"
                                                    value="{{ request('tanggal_validasi_dari', request('tanggal_validasi_mulai')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Sampai</p>
                                                <input type="date" id="filter-validasi-sampai" name="tanggal_validasi_sampai"
                                                    value="{{ request('tanggal_validasi_sampai', request('tanggal_validasi_selesai')) }}"
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
                                    <a href="{{ url('/direktur/validasi-dokumen') . (request('sort') ? '?sort=' . request('sort') : '') }}"
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
                            <a href="{{ url('/direktur/validasi-dokumen') . '?' . http_build_query(request()->except('q')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter pencarian">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('tipe_dokumen'))
                        @php
                            $tipeLabels = [
                                'bukti_po' => 'Dokumen P.O',
                                'dokumen_persyaratan' => 'Dokumen Persyaratan Layanan',
                                'bukti_surat_perjanjian_kerja' => 'SPK',
                                'surat_jalan' => 'Surat Jalan',
                                'berita_acara' => 'Berita Acara (BAST)',
                                'invoice' => 'Invoice Tagihan',
                            ];
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Tipe: {{ $tipeLabels[request('tipe_dokumen')] ?? request('tipe_dokumen') }}</span>
                            <a href="{{ url('/direktur/validasi-dokumen') . '?' . http_build_query(request()->except('tipe_dokumen')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tipe dokumen">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('status'))
                        @php
                            $valStatusLabels = ['menunggu' => 'Menunggu Validasi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Status: {{ $valStatusLabels[request('status')] ?? request('status') }}</span>
                            <a href="{{ url('/direktur/validasi-dokumen') . '?' . http_build_query(request()->except('status')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter status">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('tanggal_diajukan_dari') || request('tanggal_diajukan_sampai') || request('tanggal_diajukan_mulai') || request('tanggal_diajukan_selesai'))
                        @php
                            $tAjukanDari = request('tanggal_diajukan_dari', request('tanggal_diajukan_mulai'));
                            $tAjukanSampai = request('tanggal_diajukan_sampai', request('tanggal_diajukan_selesai'));
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Diajukan: {{ $tAjukanDari }}{{ $tAjukanSampai ? ' – ' . $tAjukanSampai : '' }}</span>
                            <a href="{{ url('/direktur/validasi-dokumen') . '?' . http_build_query(request()->except(['tanggal_diajukan_dari', 'tanggal_diajukan_sampai', 'tanggal_diajukan_mulai', 'tanggal_diajukan_selesai'])) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tanggal diajukan">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('tanggal_validasi_dari') || request('tanggal_validasi_sampai') || request('tanggal_validasi_mulai') || request('tanggal_validasi_selesai'))
                        @php
                            $tValDari = request('tanggal_validasi_dari', request('tanggal_validasi_mulai'));
                            $tValSampai = request('tanggal_validasi_sampai', request('tanggal_validasi_selesai'));
                        @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>Divalidasi: {{ $tValDari }}{{ $tValSampai ? ' – ' . $tValSampai : '' }}</span>
                            <a href="{{ url('/direktur/validasi-dokumen') . '?' . http_build_query(request()->except(['tanggal_validasi_dari', 'tanggal_validasi_sampai', 'tanggal_validasi_mulai', 'tanggal_validasi_selesai'])) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tanggal divalidasi">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    <a href="{{ url('/direktur/validasi-dokumen') . (request('sort') ? '?sort=' . request('sort') : '') }}"
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
                            <th class="py-3.5 px-6 text-center w-16">No</th>
                            <th class="py-3.5 px-6 text-left">ID Pesanan</th>
                            <th class="py-3.5 px-6 text-left">Pelanggan</th>
                            <th class="py-3.5 px-6 text-left">Tipe Dokumen</th>
                            <th class="py-3.5 px-6 text-center">Tanggal Diajukan</th>
                            <th class="py-3.5 px-6 text-center">Tanggal Divalidasi</th>
                            <th class="py-3.5 px-6 text-center">Status Validasi</th>
                            <th class="py-3.5 px-6 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($validasiList as $index => $item)
                            @php
                                $namaDokumen = match ($item->tipe_dokumen) {
                                    'bukti_po' => 'Dokumen P.O',
                                    'dokumen_persyaratan' => 'Dokumen Persyaratan Layanan',
                                    'bukti_dp' => 'Bukti DP 50%',
                                    'bukti_surat_perjanjian_kerja' => 'Surat Perjanjian Kerja (SPK)',
                                    'surat_jalan' => 'Surat Jalan',
                                    'berita_acara' => 'Berita Acara (BAST)',
                                    'invoice' => 'Invoice Tagihan',
                                    'bukti_lunas' => 'Bukti Pelunasan',
                                    default => 'Dokumen P.O'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-blue-600 dark:text-blue-400">
                                    @if($item->pemesanan)
                                        <a href="{{ url('/direktur/validasi-dokumen/detail/' . $item->id) }}"
                                            class="hover:underline">
                                            {{ $item->pemesanan->id_pesanan }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $item->pemesanan->nama_pelanggan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-300">
                                    {{ $namaDokumen }}
                                </td>
                                <td class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ ($item->status_dokumen != 'menunggu' && $item->updated_at) ? $item->updated_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->status_dokumen == 'disetujui')
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400">
                                            Disetujui</span>
                                    @elseif($item->status_dokumen == 'ditolak')
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-400">
                                            Ditolak: {{ $item->alasan }}</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400">
                                            Menunggu Validasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->pemesanan)
                                        <a href="{{ url('/direktur/validasi-dokumen/detail/' . $item->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada dokumen yang harus di validasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
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
            const rTipeBtn = document.getElementById('reset-tipe-btn');
            if (rTipeBtn) rTipeBtn.addEventListener('click', () => {
                const sel = document.getElementById('filter-tipe-dokumen');
                if (sel) sel.value = '';
            });

            const rsBtn = document.getElementById('reset-status-btn');
            if (rsBtn) rsBtn.addEventListener('click', () => {
                if (statusHidden) statusHidden.value = '';
                pills.forEach(p => {
                    p.className = pillBase + ' ' + (p.dataset.statusVal === '' ? activeClass : idleClass);
                });
            });

            const rDiajukanBtn = document.getElementById('reset-diajukan-btn');
            if (rDiajukanBtn) rDiajukanBtn.addEventListener('click', () => {
                const d1 = document.getElementById('filter-diajukan-dari');
                const d2 = document.getElementById('filter-diajukan-sampai');
                if (d1) d1.value = '';
                if (d2) d2.value = '';
            });

            const rValidasiBtn = document.getElementById('reset-validasi-btn');
            if (rValidasiBtn) rValidasiBtn.addEventListener('click', () => {
                const d1 = document.getElementById('filter-validasi-dari');
                const d2 = document.getElementById('filter-validasi-sampai');
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