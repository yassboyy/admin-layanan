@extends('layouts.dashboard')

@section('title', 'Data Pelanggan')
@section('page_title', 'Laporan Data Pelanggan')

@section('content')
    <div class="space-y-6">
        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Pelanggan</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Laporan data akun pelanggan terdaftar dan total pemesanan</p>
                </div>
                <div class="flex items-center flex-wrap gap-2.5">
                    {{-- Sort by --}}
                    <div class="relative" id="sort-dropdown-wrapper">
                        <button type="button" id="sort-dropdown-btn"
                            class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-slate-300 hover:bg-slate-50 text-slate-700 dark:text-slate-200 rounded-xl text-xs sm:text-sm font-medium transition-all shadow-xs">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                            <span class="text-slate-600 dark:text-slate-200">
                                @if(request('sort') == 'terlama') Terlama
                                @elseif(request('sort') == 'nama_asc') Nama (A-Z)
                                @elseif(request('sort') == 'nama_desc') Nama (Z-A)
                                @elseif(request('sort') == 'pesanan_terbanyak') Pesanan Terbanyak
                                @elseif(request('sort') == 'pesanan_tersedikit') Pesanan Tersedikit
                                @else Sort by
                                @endif
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="sort-dropdown-menu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-1.5 z-50 focus:outline-none">
                            @php
                                $sortOptions = [
                                    'terbaru' => 'Terbaru (Default)',
                                    'terlama' => 'Terlama',
                                    'nama_asc' => 'Nama (A-Z)',
                                    'nama_desc' => 'Nama (Z-A)',
                                    'pesanan_terbanyak' => 'Pesanan Terbanyak',
                                    'pesanan_tersedikit' => 'Pesanan Tersedikit',
                                ];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/direktur/data-pelanggan') . (count($qp) ? '?' . http_build_query($qp) : '');
                                @endphp
                                <a href="{{ $sortUrl }}"
                                    class="flex items-center justify-between px-4 py-2 text-xs font-semibold {{ $currentSort == $key ? 'text-teal-600 bg-teal-50 dark:bg-teal-900/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
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
                </div>
            </div>

            {{-- Search Filter --}}
            <div class="relative max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 dark:text-slate-500 pointer-events-none"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="search-pelanggan" placeholder="Cari nama atau email pelanggan..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 divide-x divide-slate-100 dark:divide-slate-700">
                            <th class="py-3.5 px-6 text-center w-16">No</th>
                            <th class="py-3.5 px-6 text-left">Nama Pelanggan</th>
                            <th class="py-3.5 px-6 text-left">Email</th>
                            <th class="py-3.5 px-6 text-center">Tanggal Register</th>
                            <th class="py-3.5 px-6 text-center">Total Pesanan Pelanggan</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($pelanggan as $index => $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-searchable
                                data-name="{{ $item->name }}" data-email="{{ $item->email }}">
                                <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $item->name }}</td>
                                <td class="px-6 py-4 text-slate-550 dark:text-slate-400">{{ $item->email }}</td>
                                <td class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-blue-600 dark:text-blue-400">
                                    {{ $item->total_pemesanan }} Pesanan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada data pelanggan.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="empty-search-row" style="display: none;">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data pelanggan yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search-pelanggan');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr[data-searchable]');
                let visibleIndex = 0;

                rows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const email = row.getAttribute('data-email') || '';
                    const combined = (name + ' ' + email).toLowerCase();

                    if (combined.includes(keyword)) {
                        row.style.display = '';
                        visibleIndex++;
                        const noCell = row.querySelector('td:first-child');
                        if (noCell) noCell.textContent = visibleIndex;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const emptyRow = document.getElementById('empty-search-row');
                if (emptyRow) {
                    emptyRow.style.display = visibleIndex === 0 ? '' : 'none';
                }
            });
        }

        // Sort dropdown toggle
        const sortBtn = document.getElementById('sort-dropdown-btn');
        const sortMenu = document.getElementById('sort-dropdown-menu');
        const sortWrapper = document.getElementById('sort-dropdown-wrapper');

        if (sortBtn && sortMenu) {
            sortBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sortMenu.classList.toggle('hidden');
            });
        }

        document.addEventListener('click', function (e) {
            if (sortWrapper && !sortWrapper.contains(e.target) && sortMenu) {
                sortMenu.classList.add('hidden');
            }
        });
    </script>
@endsection