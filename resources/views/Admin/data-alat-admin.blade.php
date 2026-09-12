@extends('layouts.dashboard')

@section('title', 'Data Alat')
@section('page_title', 'Inventaris Alat & Mesin')

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

        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Alat</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola seluruh inventaris alat kerja dan mesin operasional</p>
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
                                @elseif(request('sort') == 'nama_asc') Nama Alat (A-Z)
                                @elseif(request('sort') == 'nama_desc') Nama Alat (Z-A)
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
                                    'nama_asc' => 'Nama Alat (A-Z)',
                                    'nama_desc' => 'Nama Alat (Z-A)',
                                ];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/admin/data-alat') . (count($qp) ? '?' . http_build_query($qp) : '');
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
                        Tambah Alat
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
                <input type="text" id="search-alat" placeholder="Cari nama alat atau deskripsi..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 divide-x divide-slate-100 dark:divide-slate-700">
                            <th class="py-3.5 px-6 text-center w-16">No</th>
                            <th class="py-3.5 px-6 text-center w-28">Gambar</th>
                            <th class="py-3.5 px-6 text-left">Nama Alat</th>
                            <th class="py-3.5 px-6 text-left">Deskripsi</th>
                            <th class="py-3.5 px-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($alats as $index => $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-searchable
                                data-nama="{{ $item->nama_alat }}" data-deskripsi="{{ $item->deskripsi }}">
                                <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-semibold">
                                    {{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="w-16 h-12 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center mx-auto">
                                        @if($item->gambar && file_exists(public_path($item->gambar)))
                                            <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_alat }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $item->nama_alat }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 max-w-md truncate">
                                    {{ $item->deskripsi }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                            onclick="openEditModal(this)"
                                            data-alat="{{ json_encode([
                                                'id' => $item->id,
                                                'nama' => $item->nama_alat,
                                                'deskripsi' => $item->deskripsi,
                                                'gambar' => asset($item->gambar)
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
                                            data-nama="{{ $item->nama_alat }}"
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Belum ada data alat.
                                        </p>
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
                                    <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Data alat tidak
                                        ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modal-tambah" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-tambah')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
                <div
                    class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-slate-800 rounded-t-2xl z-10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Alat Baru</h3>
                    <button onclick="closeModal('modal-tambah')"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ url('/admin/data-alat') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Nama
                            Alat <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_alat" required placeholder="Masukkan nama alat"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi
                            <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" rows="3" required placeholder="Detail deskripsi alat..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Gambar
                            Alat <span class="text-red-500">*</span></label>
                        <input type="file" name="gambar" required accept="image/*"
                            onchange="previewImage(this, 'add-preview')"
                            class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-blue-100 transition-all">
                        <div id="add-preview-container" class="mt-3 hidden">
                            <img id="add-preview"
                                class="w-full h-32 object-cover rounded-xl border border-slate-200 dark:border-slate-600" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal('modal-tambah')"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Alat</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modal-edit" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
                <div
                    class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-slate-800 rounded-t-2xl z-10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Alat</h3>
                    <button onclick="closeModal('modal-edit')"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="form-edit" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Nama
                            Alat <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_alat" id="edit-nama" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi
                            <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" id="edit-deskripsi" rows="3" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none"></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Ganti
                            Gambar <span class="text-slate-400 text-[10px] normal-case">(kosongkan jika tidak
                                diubah)</span></label>
                        <input type="file" name="gambar" accept="image/*" onchange="previewImage(this, 'edit-preview')"
                            class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-700 dark:file:text-slate-200 hover:file:bg-blue-100 transition-all">
                        <div id="edit-preview-container" class="mt-3">
                            <img id="edit-preview"
                                class="w-full h-32 object-cover rounded-xl border border-slate-200 dark:border-slate-600" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal('modal-edit')"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Perbarui Data Alat</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div id="modal-hapus" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-hapus')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 w-full max-w-sm relative">
                <div class="p-6 text-center">
                    <div
                        class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-950/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-7 w-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Alat?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Anda yakin ingin menghapus data alat <strong
                            id="delete-nama" class="text-slate-800 dark:text-slate-200"></strong>? Tindakan ini tidak bisa
                            dibatalkan.</p>
                    <form id="form-hapus" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeModal('modal-hapus')"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Ya, Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = '';
        }
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById(previewId);
                    img.src = e.target.result;
                    document.getElementById(previewId + '-container').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
        function openEditModal(buttonOrId, nama, deskripsi, gambarSrc) {
            let id, nm, dsk, gbr;
            if (buttonOrId instanceof HTMLElement || (typeof buttonOrId === 'object' && buttonOrId !== null)) {
                let rawData = buttonOrId instanceof HTMLElement ? buttonOrId.getAttribute('data-alat') : buttonOrId;
                let data = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
                id = data.id;
                nm = data.nama || '';
                dsk = data.deskripsi || '';
                gbr = data.gambar || '';
            } else {
                id = buttonOrId;
                nm = nama || '';
                dsk = deskripsi || '';
                gbr = gambarSrc || '';
            }

            document.getElementById('form-edit').action = '/admin/data-alat/' + id;
            document.getElementById('edit-nama').value = nm;
            document.getElementById('edit-deskripsi').value = dsk;

            const preview = document.getElementById('edit-preview');
            const previewContainer = document.getElementById('edit-preview-container');
            if (gbr && !gbr.includes('/undefined') && !gbr.endsWith('/')) {
                preview.src = gbr;
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
            }

            openModal('modal-edit');
        }
        function openDeleteModal(id, nama) {
            document.getElementById('form-hapus').action = '/admin/data-alat/' + id;
            document.getElementById('delete-nama').textContent = nama || '';
            openModal('modal-hapus');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-alat');
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    const deskripsi = row.getAttribute('data-deskripsi') || '';
                    const combined = (nama + ' ' + deskripsi).toLowerCase();

                    if (combined.includes(query)) {
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