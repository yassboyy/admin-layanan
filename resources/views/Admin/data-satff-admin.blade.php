@extends('layouts.dashboard')

@section('title', 'Data Staff')
@section('page_title', 'Data Staff & Pengelola')

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
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Staff</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola seluruh data staff dan pengelola sistem</p>
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
                                @elseif(request('sort') == 'role_asc') Role / Jabatan
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
                                    'nama_asc' => 'Nama Staff (A-Z)',
                                    'nama_desc' => 'Nama Staff (Z-A)',
                                    'role_asc' => 'Role / Jabatan',
                                ];
                                $currentSort = request('sort', 'terbaru');
                            @endphp
                            @foreach($sortOptions as $key => $label)
                                @php
                                    $qp = request()->except('sort');
                                    if ($key !== 'terbaru')
                                        $qp['sort'] = $key;
                                    $sortUrl = url('/admin/data-staff') . (count($qp) ? '?' . http_build_query($qp) : '');
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
                        Tambah Staff
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
                <input type="text" id="search-staff" placeholder="Cari nama, email, atau role staff..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700 divide-x divide-slate-100 dark:divide-slate-700">
                            <th class="py-3.5 px-6 text-center w-16">No</th>
                            <th class="py-3.5 px-6 text-left">Nama</th>
                            <th class="py-3.5 px-6 text-left">Email</th>
                            <th class="py-3.5 px-6 text-left">Role</th>
                            <th class="py-3.5 px-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-x divide-slate-100 dark:divide-slate-700 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                        @forelse($staffs as $index => $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-searchable
                                data-nama="{{ $item->name }}" data-email="{{ $item->email }}" data-role="{{ $item->role }}">
                                <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-semibold">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $item->name }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $item->email }}</td>
                                <td class="px-6 py-4">
                                    @if($item->role === 'direktur')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-700 dark:bg-purple-950/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                            Direktur
                                        </span>
                                    @elseif($item->role === 'admin')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                            Admin
                                        </span>
                                    @elseif($item->role === 'managerteknisi')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Manager Teknisi
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ ucfirst($item->role) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->email) }}', '{{ $item->role }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-950/30 dark:hover:bg-amber-950/50 transition-colors">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-red-600 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-950/30 dark:hover:bg-red-950/50 transition-colors">
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
                                        <svg class="h-12 w-12 text-slate-300 dark:text-slate-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Belum ada data staff.</p>
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
                                    <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Data staff tidak ditemukan.</p>
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
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Staff Baru</h3>
                    <button onclick="closeModal('modal-tambah')"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ url('/admin/data-staff') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Nama
                            Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Masukkan nama staff"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Email
                            <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="nama@gmail.com"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Role
                            <span class="text-red-500">*</span></label>
                        <select name="role" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="admin">Admin</option>
                            <option value="managerteknisi">Manager Teknisi</option>
                            <option value="direktur">Direktur</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Password
                            <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" minlength="6"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
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
                            <span>Simpan Staff</span>
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
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Staff</h3>
                    <button onclick="closeModal('modal-edit')"
                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="form-edit" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Nama
                            Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Email
                            <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="edit-email" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Role
                            <span class="text-red-500">*</span></label>
                        <select name="role" id="edit-role" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="admin">Admin</option>
                            <option value="managerteknisi">Manager Teknisi</option>
                            <option value="direktur">Direktur</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Password
                            <span class="text-slate-400 text-[10px] normal-case">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" minlength="6"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
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
                            <span>Perbarui Data Staff</span>
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
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Staff?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Anda yakin ingin menghapus staff <strong
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
        function openEditModal(id, name, email, role) {
            document.getElementById('form-edit').action = '/admin/data-staff/' + id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-role').value = role;
            openModal('modal-edit');
        }
        function openDeleteModal(id, nama) {
            document.getElementById('form-hapus').action = '/admin/data-staff/' + id;
            document.getElementById('delete-nama').textContent = nama;
            openModal('modal-hapus');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-staff');
            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    const email = row.getAttribute('data-email') || '';
                    const role = row.getAttribute('data-role') || '';
                    const combined = (nama + ' ' + email + ' ' + role).toLowerCase();

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