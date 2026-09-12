@extends('layouts.dashboard')

@section('title', 'Data Pelanggan')
@section('page_title', 'Data Pelanggan')

@section('content')
    <div class="space-y-6">

        {{-- Flash Message --}}
        @if(session('success'))
            <div id="flash-msg"
                class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-semibold transition-all">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Pelanggan</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola seluruh data akun pelanggan terdaftar</p>
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
                                    $sortUrl = url('/admin/data-pelanggan') . (count($qp) ? '?' . http_build_query($qp) : '');
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

                    <button onclick="openModal('modal-tambah')"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-600/20 flex items-center gap-2 shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pelanggan
                    </button>
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
                                <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $item->name }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $item->email }}</td>
                                <td class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-blue-600 dark:text-blue-400">
                                    {{ $item->total_pemesanan }} Pesanan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Belum ada data
                                            pelanggan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="empty-search-row" style="display: none;">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-12 w-12 text-slate-300 dark:text-slate-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Pelanggan tidak
                                        ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pelanggan -->
    <div id="modal-tambah" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-tambah')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full p-6 space-y-6 relative z-10">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700 pb-4">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tambah Pelanggan Baru</h3>
                    <button onclick="closeModal('modal-tambah')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold p-1 cursor-pointer">&times;</button>
                </div>
                <form action="{{ url('/admin/data-pelanggan') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Pelanggan <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="budi@example.com"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Password Akun <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" onclick="closeModal('modal-tambah')"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 font-bold text-xs transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-sm shadow-blue-600/20 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Pelanggan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-pelanggan');
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const email = row.getAttribute('data-email') || '';
                    const combined = (name + ' ' + email).toLowerCase();

                    if (combined.includes(keyword)) {
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
                    emptyRow.style.display = (visibleCount === 0) ? '' : 'none';
                }
            });

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
        });
    </script>
@endsection