@extends('layouts.dashboard')

@section('title', 'Data Layanan')
@section('page_title', 'Data Layanan')

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
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Layanan</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola seluruh katalog layanan dan jasa</p>
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
                                @elseif(request('sort') == 'harga_tertinggi') Harga Tertinggi
                                @elseif(request('sort') == 'harga_terendah') Harga Terendah
                                @elseif(request('sort') == 'kategori_asc') Kategori Layanan
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
                                    'harga_tertinggi' => 'Harga Tertinggi',
                                    'harga_terendah' => 'Harga Terendah',
                                    'kategori_asc' => 'Kategori Layanan',
                                ];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/admin/data-layanan') . (count($qp) ? '?' . http_build_query($qp) : '');
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
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-600/20 flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Layanan
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
                <input type="text" id="search-layanan" placeholder="Cari nama atau jenis layanan..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 divide-x divide-slate-100 dark:divide-slate-700">
                            <th class="py-3.5 px-6 text-center w-16">No</th>
                            <th class="py-3.5 px-6 text-center w-24">Gambar</th>
                            <th class="py-3.5 px-6 text-center">Kategori</th>
                            <th class="py-3.5 px-6 text-left">Nama Layanan</th>
                            <th class="py-3.5 px-6 text-center">Jenis Layanan</th>
                            <th class="py-3.5 px-6 text-left">Harga</th>
                            <th class="py-3.5 px-6 text-left">Deskripsi</th>
                            <th class="py-3.5 px-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($layanan as $index => $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-searchable
                                data-kategori="{{ $item->kategori_layanan }}" data-nama="{{ $item->nama_layanan }}"
                                data-jenis="{{ $item->jenis_layanan }}">
                                <td class="px-6 py-4 text-center text-slate-550 dark:text-slate-400 font-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="w-16 h-12 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center mx-auto">
                                        @if($item->gambar && file_exists(public_path($item->gambar)))
                                            <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_layanan }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(($item->kategori_layanan ?? 'jasa_service') === 'jasa_perizinan')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/30 dark:text-purple-400 border border-purple-100 dark:border-purple-900/50">
                                            Jasa Perizinan
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                                            Jasa Service
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left font-bold text-slate-800 dark:text-slate-200">
                                    <div>{{ $item->nama_layanan }}</div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-550 dark:text-slate-400">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50">
                                        {{ $item->jenis_layanan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-left font-bold text-slate-800 dark:text-slate-200">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-left text-slate-550 dark:text-slate-400 max-w-xs truncate">
                                    {{ $item->deskripsi }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                            onclick="openEditModal(this)"
                                            data-layanan="{{ json_encode([
                                                'id' => $item->id,
                                                'kategori_layanan' => $item->kategori_layanan ?? 'jasa_service',
                                                'nama_layanan' => $item->nama_layanan,
                                                'jenis_layanan' => $item->jenis_layanan,
                                                'harga' => $item->harga,
                                                'deskripsi' => $item->deskripsi,
                                                'gambar' => asset($item->gambar),
                                                'dokumen' => $item->dokumenLayanan ? $item->dokumenLayanan->pluck('nama_dokumen')->values()->all() : []
                                            ]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-950/30 dark:hover:bg-amber-950/50 transition-colors cursor-pointer">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button type="button"
                                            onclick="openDeleteModal({{ $item->id }}, this.getAttribute('data-nama'))"
                                            data-nama="{{ $item->nama_layanan }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-red-600 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-950/30 dark:hover:bg-red-950/50 transition-colors cursor-pointer">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="h-12 w-12 text-slate-300 dark:text-slate-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Belum ada data
                                            layanan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        {{-- Empty search result row --}}
                        <tr id="empty-search-row" style="display: none;">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-12 w-12 text-slate-300 dark:text-slate-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Tidak ada layanan
                                        yang cocok dengan pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Layanan -->
    <div id="modal-tambah" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-tambah')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto" onclick="if(event.target === this) closeModal('modal-tambah')">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-xl w-full p-6 sm:p-8 space-y-6 my-auto max-h-[90vh] overflow-y-auto relative z-10">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Layanan Baru</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Isi formulir rincian layanan dan persyaratan dokumen</p>
                    </div>
                    <button type="button" onclick="closeModal('modal-tambah')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl p-2 transition-colors cursor-pointer">&times;</button>
                </div>

                <!-- Form -->
                <form action="{{ url('/admin/data-layanan') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <!-- Kategori & Jenis -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Kategori Layanan <span class="text-red-500">*</span>
                            </label>
                            <select name="kategori_layanan" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="jasa_service">Jasa Service</option>
                                <option value="jasa_perizinan">Jasa Perizinan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="jenis_layanan" required placeholder="Contoh: Service, Perizinan"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>

                    <!-- Nama Layanan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Nama Layanan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_layanan" required placeholder="Contoh: Service AC Cassette"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Harga Layanan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="harga" required min="0" placeholder="0"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Deskripsi Layanan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="deskripsi" rows="3" required placeholder="Jelaskan ruang lingkup dan rincian layanan..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Dokumen Persyaratan Card -->
                    <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-700/30 border border-slate-200/80 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Dokumen Persyaratan Layanan
                                </label>
                                <span class="text-[11px] text-slate-400">Wajib diunggah pelanggan saat memesan</span>
                            </div>
                            <button type="button" onclick="addDokumenRow('tambah-dokumen-container')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 cursor-pointer transition-colors shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah</span>
                            </button>
                        </div>
                        
                        <div id="tambah-dokumen-container" class="space-y-2.5 max-h-40 overflow-y-auto pr-1">
                            <div class="flex items-center gap-2 dokumen-row">
                                <input type="text" name="dokumen_persyaratan[]" placeholder="Contoh: Foto KTP / NPWP / Surat Izin"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <button type="button" onclick="removeDokumenRow(this)"
                                    class="p-2 text-slate-400 hover:text-red-500 rounded-xl cursor-pointer transition-colors hover:bg-red-50 dark:hover:bg-red-950/30 shrink-0" title="Hapus dokumen">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Gambar -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Upload Gambar Layanan <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="gambar" required accept="image/*"
                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-blue-100 transition-all cursor-pointer">
                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('modal-tambah')"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 font-bold text-xs transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-md shadow-blue-600/20 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Layanan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Layanan -->
    <div id="modal-edit" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto" onclick="if(event.target === this) closeModal('modal-edit')">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-xl w-full p-6 sm:p-8 space-y-6 my-auto max-h-[90vh] overflow-y-auto relative z-10">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Layanan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui rincian informasi layanan dan dokumen persyaratan</p>
                    </div>
                    <button type="button" onclick="closeModal('modal-edit')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl p-2 transition-colors cursor-pointer">&times;</button>
                </div>

                <!-- Form -->
                <form id="form-edit" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')
                    
                    <!-- Kategori & Jenis -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Kategori Layanan <span class="text-red-500">*</span>
                            </label>
                            <select name="kategori_layanan" id="edit-kategori" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="jasa_service">Jasa Service</option>
                                <option value="jasa_perizinan">Jasa Perizinan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="jenis_layanan" id="edit-jenis" required placeholder="Contoh: Service, Perizinan"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>

                    <!-- Nama Layanan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Nama Layanan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_layanan" id="edit-nama" required placeholder="Nama layanan"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Harga Layanan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="harga" id="edit-harga" required min="0" placeholder="0"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Deskripsi Layanan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="deskripsi" id="edit-deskripsi" rows="3" required placeholder="Jelaskan ruang lingkup dan rincian layanan..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Dokumen Persyaratan Card -->
                    <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-700/30 border border-slate-200/80 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Dokumen Persyaratan Layanan
                                </label>
                                <span class="text-[11px] text-slate-400">Wajib diunggah pelanggan saat memesan</span>
                            </div>
                            <button type="button" onclick="addDokumenRow('edit-dokumen-container')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 cursor-pointer transition-colors shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah</span>
                            </button>
                        </div>
                        
                        <div id="edit-dokumen-container" class="space-y-2.5 max-h-40 overflow-y-auto pr-1">
                            <!-- Dynamic rows populated via openEditModal -->
                        </div>
                    </div>

                    <!-- Ganti Gambar -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Ganti Gambar Layanan <span class="text-slate-400 text-[10px] font-normal">(kosongkan jika tidak diubah)</span>
                        </label>
                        <input type="file" name="gambar" accept="image/*"
                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-blue-100 transition-all cursor-pointer">
                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('modal-edit')"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 font-bold text-xs transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-md shadow-blue-600/20 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Perbarui Data Layanan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Layanan -->
    <div id="modal-hapus" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-hapus')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4" onclick="if(event.target === this) closeModal('modal-hapus')">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-sm p-6 text-center relative z-10">
                <div
                    class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-950/30 flex items-center justify-center mx-auto mb-4 text-red-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-2">Hapus Layanan?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Anda yakin ingin menghapus layanan <strong
                        id="delete-nama" class="text-slate-800 dark:text-slate-200"></strong>? Tindakan ini tidak bisa
                    dibatalkan.</p>
                <form id="form-hapus" method="POST" class="flex justify-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('modal-hapus')"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm shadow-red-600/20 transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Ya, Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </div>



    {{-- ==================== SCRIPTS ==================== --}}
    <script>
        // Auto-hide flash message
        const flash = document.getElementById('flash-msg');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.5s ease';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }, 4000);
        }

        // Modal helpers
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = '';
        }

        // File Preview
        function previewImage(input, previewId) {
            const file = input.files[0];
            const img = document.getElementById(previewId);
            const container = document.getElementById(previewId + '-container');
            if (file && img && container) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    container.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        // Dynamic Document Rows helper
        function addDokumenRow(containerId, value = '') {
            const container = document.getElementById(containerId);
            if (!container) return;

            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 dokumen-row';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'dokumen_persyaratan[]';
            input.value = (value != null ? String(value) : '');
            input.placeholder = 'Contoh: Foto KTP / NPWP / Surat Izin';
            input.className = 'w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.onclick = function() { removeDokumenRow(this); };
            btn.className = 'p-2 text-slate-400 hover:text-red-500 rounded-xl cursor-pointer transition-colors hover:bg-red-50 dark:hover:bg-red-950/30 shrink-0';
            btn.title = 'Hapus dokumen';
            btn.innerHTML = `
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            `;

            row.appendChild(input);
            row.appendChild(btn);
            container.appendChild(row);
        }

        function removeDokumenRow(btn) {
            const row = btn.closest('.dokumen-row');
            const container = row.parentElement;
            if (container && container.children.length > 1) {
                row.remove();
            } else {
                const input = row.querySelector('input');
                if (input) input.value = '';
            }
        }

        // Edit modal (supports both passing button element `this` or positional arguments)
        function openEditModal(buttonOrId, kategori, nama, jenis, harga, deskripsi, gambarSrc, dokumenList = []) {
            let id, kat, nm, jns, hrg, dsk, gbr, docs;

            if (buttonOrId instanceof HTMLElement || (typeof buttonOrId === 'object' && buttonOrId !== null)) {
                let rawData = buttonOrId instanceof HTMLElement ? buttonOrId.getAttribute('data-layanan') : buttonOrId;
                let data = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
                id = data.id;
                kat = data.kategori_layanan || 'jasa_service';
                nm = data.nama_layanan || '';
                jns = data.jenis_layanan || '';
                hrg = data.harga || 0;
                dsk = data.deskripsi || '';
                gbr = data.gambar || '';
                docs = Array.isArray(data.dokumen) ? data.dokumen : [];
            } else {
                id = buttonOrId;
                kat = kategori || 'jasa_service';
                nm = nama || '';
                jns = jenis || '';
                hrg = harga || 0;
                dsk = deskripsi || '';
                gbr = gambarSrc || '';
                docs = Array.isArray(dokumenList) ? dokumenList : [];
            }

            document.getElementById('form-edit').action = '/admin/data-layanan/' + id;
            if (document.getElementById('edit-kategori')) {
                document.getElementById('edit-kategori').value = kat;
            }
            if (document.getElementById('edit-nama')) {
                document.getElementById('edit-nama').value = nm;
            }
            if (document.getElementById('edit-jenis')) {
                document.getElementById('edit-jenis').value = jns;
            }
            if (document.getElementById('edit-harga')) {
                document.getElementById('edit-harga').value = hrg;
            }
            if (document.getElementById('edit-deskripsi')) {
                document.getElementById('edit-deskripsi').value = dsk;
            }

            // Populate dokumen persyaratan
            const editContainer = document.getElementById('edit-dokumen-container');
            if (editContainer) {
                editContainer.innerHTML = '';
                if (docs.length > 0) {
                    docs.forEach(dok => {
                        addDokumenRow('edit-dokumen-container', dok);
                    });
                } else {
                    addDokumenRow('edit-dokumen-container', '');
                }
            }

            openModal('modal-edit');
        }

        // Delete modal
        function openDeleteModal(id, nama) {
            document.getElementById('form-hapus').action = '/admin/data-layanan/' + id;
            document.getElementById('delete-nama').textContent = nama || '';
            openModal('modal-hapus');
        }

        // Close modals with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal('modal-tambah');
                closeModal('modal-edit');
                closeModal('modal-hapus');
            }
        });

        // Search / Filter Layanan
        const searchInput = document.getElementById('search-layanan');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr[data-searchable]');
                let visibleIndex = 0;

                rows.forEach(row => {
                    const kategori = row.getAttribute('data-kategori') || '';
                    const nama = row.getAttribute('data-nama') || '';
                    const jenis = row.getAttribute('data-jenis') || '';
                    const combined = (nama + ' ' + jenis + ' ' + kategori).toLowerCase();

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