@extends('layouts.dashboard')

@section('title', 'Tentang Kami')
@section('page_title', 'Kelola Informasi Tentang Kami')

@section('content')
    <div class="space-y-6 max-w-6xl mx-auto">

        {{-- Flash Message --}}
        @if(session('success'))
            <div id="flash-msg"
                class="flex items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm font-semibold transition-all">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div
                class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm font-semibold">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Terdapat kesalahan pengisian data:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Container -->
        <form action="{{ url('/admin/tentang-kami') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Card: Image Upload & Preview -->
                <div
                    class="lg:col-span-1 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-5 flex flex-col">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Foto / Gambar Profil</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gambar utama yang tampil di halaman
                            Tentang Kami publik</p>
                    </div>

                    <!-- Preview Area -->
                    <div
                        class="relative group rounded-2xl overflow-hidden border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col items-center justify-center p-4 min-h-[260px]">
                        @php
                            $hasImage = !empty($tentangKami->gambar) && file_exists(public_path($tentangKami->gambar));
                            $imageSrc = $hasImage ? asset($tentangKami->gambar) : asset('images/logo.png');
                        @endphp

                        <img id="image-preview" src="{{ $imageSrc }}" alt="Preview Gambar"
                            class="max-h-56 w-auto object-contain rounded-xl drop-shadow-md transition-transform duration-200 group-hover:scale-105">

                        <div id="image-hint" class="mt-3 text-center">
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block">
                                {{ $hasImage ? 'Gambar Aktif Saat Ini' : 'Logo Default Digunakan' }}
                            </span>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="space-y-2">
                        <label for="gambar"
                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Unggah Foto Baru
                        </label>
                        <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)"
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 dark:file:bg-blue-900/40 file:text-blue-600 dark:file:text-blue-400 hover:file:bg-blue-100 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Mendukung file JPG, PNG, WEBP, SVG (Maks.
                            5MB)</p>
                    </div>
                </div>

                <!-- Right Card: Description, Vision & Mission -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Konten Informasi Perusahaan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola deskripsi profil, visi, dan misi
                            perusahaan CV Tomo Teknik Mandiri</p>
                    </div>

                    <!-- Deskripsi Profil -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="deskripsi_profil"
                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Deskripsi Profil Perusahaan <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <textarea id="deskripsi_profil" name="deskripsi_profil" rows="6" required
                            placeholder="Tuliskan deskripsi lengkap profil perusahaan..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:outline-none focus:border-blue-600 transition-colors leading-relaxed">{{ old('deskripsi_profil', $tentangKami->deskripsi_profil ?? '') }}</textarea>
                    </div>

                    <!-- Visi Perusahaan -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="visi"
                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Visi Perusahaan <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <textarea id="visi" name="visi" rows="3" required placeholder="Tuliskan visi perusahaan..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:outline-none focus:border-blue-600 transition-colors leading-relaxed">{{ old('visi', $tentangKami->visi ?? '') }}</textarea>
                    </div>

                    <!-- Misi Perusahaan -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="misi"
                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Misi Perusahaan <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <textarea id="misi" name="misi" rows="5" required
                            placeholder="Tuliskan poin-poin misi perusahaan (pisahkan setiap baris untuk format daftar)..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm focus:outline-none focus:border-blue-600 transition-colors leading-relaxed">{{ old('misi', $tentangKami->misi ?? '') }}</textarea>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Tips: Buat baris baru untuk setiap butir
                            misi agar otomatis tersusun menjadi daftar rapi di tampilan website.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="pt-6 border-t border-slate-100 dark:border-slate-700 flex flex-wrap items-center justify-end gap-3">
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-600/20 flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('image-preview');
                    preview.src = e.target.result;
                    const hint = document.getElementById('image-hint');
                    if (hint) {
                        hint.innerHTML = '<span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 block">Pratinjau Gambar Baru Dipilih</span>';
                    }
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection