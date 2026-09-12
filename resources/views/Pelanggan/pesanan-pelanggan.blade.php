@extends('layouts.dashboard')

@section('title', 'Pesanan Saya')
@section('page_title', 'Riwayat Pesanan Saya')

@section('content')
    <div class="space-y-6">
        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors space-y-6">
            {{-- Toolbar: Title & Buttons --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Pesanan Saya</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Daftar pesanan layanan yang pernah atau
                        sedang dipesan</p>
                </div>
                <div class="flex items-center flex-wrap gap-2.5">
                    <a href="{{ url('/layanan') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-sm font-semibold transition-all shadow-sm shadow-blue-500/20">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Pesan Layanan Baru</span>
                    </a>

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
                                @elseif(request('sort') == 'harga_tertinggi') Harga Tertinggi
                                @elseif(request('sort') == 'harga_terendah') Harga Terendah
                                @elseif(request('sort') == 'id_asc') ID Pesanan (A-Z)
                                @elseif(request('sort') == 'id_desc') ID Pesanan (Z-A)
                                @else Urutkan
                                @endif
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="sort-dropdown-menu"
                            class="hidden absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 z-50 overflow-hidden">
                            @php
                                $sortOptions = ['terbaru' => 'Terbaru', 'terlama' => 'Terlama', 'harga_tertinggi' => 'Harga Tertinggi', 'harga_terendah' => 'Harga Terendah', 'id_asc' => 'ID Pesanan (A-Z)', 'id_desc' => 'ID Pesanan (Z-A)'];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/pelanggan/pesanan') . (count($qp) ? '?' . http_build_query($qp) : '');
                                @endphp
                                <a href="{{ $sortUrl }}"
                                    class="flex items-center justify-between px-4 py-2.5 text-sm {{ $currentSort == $key ? 'text-teal-700 bg-teal-50 font-semibold' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
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
                    @php $isFiltered = request()->hasAny(['q', 'status', 'tanggal_dari', 'tanggal_sampai', 'tanggal_pesan', 'tanggal_mulai']); @endphp
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
                            @if($isFiltered)
                                <span
                                    class="flex items-center justify-center w-5 h-5 rounded-full bg-white/25 text-white text-[10px] font-bold">
                                    {{ collect([request('q'), request('status'), request('tanggal_dari') ?? request('tanggal_pesan')])->filter()->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- ===== FILTER POPOVER CARD ===== --}}
                        <div id="filter-popover-card"
                            class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl border border-slate-200 z-50 overflow-hidden"
                            style="box-shadow: 0 8px 30px -4px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04);">

                            <form method="GET" action="{{ url('/pelanggan/pesanan') }}" id="main-filter-form">
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
                                        <span class="text-sm font-bold text-slate-800">Filter Pesanan</span>
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

                                    {{-- Rentang Tanggal --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Rentang
                                                Tanggal</label>
                                            <button type="button" id="reset-date-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Dari</p>
                                                <input type="date" id="filter-tanggal-dari" name="tanggal_dari"
                                                    value="{{ request('tanggal_dari', request('tanggal_pesan')) }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                            <div>
                                                <p class="text-[11px] text-slate-400 mb-1">Sampai</p>
                                                <input type="date" id="filter-tanggal-sampai" name="tanggal_sampai"
                                                    value="{{ request('tanggal_sampai') }}"
                                                    class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-dashed border-slate-200"></div>

                                    {{-- Status --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Status
                                                Pesanan</label>
                                            <button type="button" id="reset-status-btn"
                                                class="text-[11px] text-teal-600 hover:text-teal-800 font-semibold transition-colors">Hapus</button>
                                        </div>
                                        <input type="hidden" id="filter-status-value" name="status"
                                            value="{{ request('status', '') }}">
                                        @php
                                            $currentStatus = request('status', '');
                                            $pillDefs = [
                                                '' => 'Semua',
                                                '1' => 'Pemesanan',
                                                '2' => 'Pengerjaan',
                                                '3' => 'Pelunasan',
                                                '4' => 'Selesai',
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
                                                placeholder="Cari ID, layanan, harga, status..."
                                                class="w-full pl-11 pr-3 py-2.5 text-xs rounded-lg border border-slate-200 bg-slate-50 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 focus:bg-white transition-all">
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="flex items-center gap-2 px-4 py-3 bg-slate-50 border-t border-slate-100">
                                    <a href="{{ url('/pelanggan/pesanan') . (request('sort') ? '?sort=' . request('sort') : '') }}"
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
                            <a href="{{ url('/pelanggan/pesanan') . '?' . http_build_query(request()->except('q')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter pencarian">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('status'))
                        @php $statusLabels = [1 => 'Proses Pemesanan', 2 => 'Proses Pengerjaan', 3 => 'Menunggu Pelunasan', 4 => 'Selesai']; @endphp
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>{{ $statusLabels[request('status')] ?? '' }}</span>
                            <a href="{{ url('/pelanggan/pesanan') . '?' . http_build_query(request()->except('status')) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter status">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('tanggal_dari') || request('tanggal_sampai') || request('tanggal_pesan'))
                        <span
                            class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full bg-teal-50 text-teal-700 font-medium text-xs">
                            <span>{{ request('tanggal_dari', request('tanggal_pesan')) }}{{ request('tanggal_sampai') ? ' – ' . request('tanggal_sampai') : '' }}</span>
                            <a href="{{ url('/pelanggan/pesanan') . '?' . http_build_query(request()->except(['tanggal_dari', 'tanggal_sampai', 'tanggal_pesan'])) }}"
                                class="w-4 h-4 rounded-full bg-teal-200/70 hover:bg-teal-200 flex items-center justify-center text-teal-800 transition-colors"
                                title="Hapus filter tanggal">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </span>
                    @endif
                    <a href="{{ url('/pelanggan/pesanan') . (request('sort') ? '?sort=' . request('sort') : '') }}"
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
                            <th class="py-3.5 px-6 text-left">Layanan</th>
                            <th class="py-3.5 px-6 text-left">Total Harga (PPN)</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-left">Tanggal Pesan</th>
                            <th class="py-3.5 px-6 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 text-center font-bold text-slate-400 align-middle">{{ $loop->iteration }}
                                </td>
                                <td
                                    class="px-6 py-4 font-extrabold text-blue-600 dark:text-blue-400 whitespace-nowrap align-middle">
                                    {{ $order->id_pesanan }}
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <div class="space-y-1.5 min-w-[200px] max-w-sm">
                                        @foreach($order->pemesananLayanan as $item)
                                            <div class="flex items-start gap-2">
                                                <span
                                                    class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 mt-1.5 shrink-0"></span>
                                                <div class="leading-tight">
                                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs sm:text-sm">
                                                        {{ $item->nama_layanan }}
                                                    </span>
                                                    @if($item->jumlah > 1)
                                                        <span class="text-[11px] font-extrabold text-blue-600 dark:text-blue-400 ml-1">
                                                            (×{{ $item->jumlah }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap align-middle">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap align-middle">
                                    @if($order->status_tipe == 1)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                            Proses Pemesanan
                                        </span>
                                    @elseif($order->status_tipe == 2)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                            Proses Pengerjaan
                                        </span>
                                    @elseif($order->status_tipe == 3)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                            Menunggu Pelunasan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap align-middle">
                                    {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap align-middle">
                                    <a href="{{ url('/pelanggan/detail-pesanan/' . $order->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 font-bold text-xs transition-all border border-blue-200 dark:border-blue-800">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 dark:text-slate-500 text-sm">
                                    Anda belum memiliki pesanan.
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
            const rdBtn = document.getElementById('reset-date-btn');
            if (rdBtn) rdBtn.addEventListener('click', () => {
                const d1 = document.getElementById('filter-tanggal-dari');
                const d2 = document.getElementById('filter-tanggal-sampai');
                if (d1) d1.value = '';
                if (d2) d2.value = '';
            });

            const rsBtn = document.getElementById('reset-status-btn');
            if (rsBtn) rsBtn.addEventListener('click', () => {
                if (statusHidden) statusHidden.value = '';
                pills.forEach(p => {
                    p.className = pillBase + ' ' + (p.dataset.statusVal === '' ? activeClass : idleClass);
                });
            });

            const rkBtn = document.getElementById('reset-keyword-btn');
            if (rkBtn) rkBtn.addEventListener('click', () => {
                const kw = document.getElementById('filter-keyword');
                if (kw) kw.value = '';
            });
        });
    </script>
@endsection