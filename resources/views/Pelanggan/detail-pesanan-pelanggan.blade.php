@extends('layouts.dashboard')

@section('title', 'Detail Pesanan')
@section('page_title', 'Detail Pesanan: ' . $order->id_pesanan)

@section('content')
    @php
        $subtotalJasa = $order->pemesananLayanan->sum(function ($i) {
            return $i->harga * $i->jumlah;
        });
        $diskon = $order->diskon ?? 0;
        $ppnJasa = $subtotalJasa * 0.11; // PPN 11%
        $pphJasa = $subtotalJasa * 0.02; // PPh Jasa 2%
        $totalInklPpn = max(0, ($subtotalJasa + $ppnJasa - $pphJasa) - $diskon);
        $totalDp = $totalInklPpn * 0.50;
    @endphp

    <div class="space-y-6">
        <!-- Action Top Bar (Kembali & Detail Pesanan: id_pesanan) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                <a href="{{ url('/pelanggan/pesanan') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>

                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Detail Pesanan: {{ $order->id_pesanan }}
                </h2>
            </div>

            <div>
                @if($order->status_tipe == 1)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                        Status: Proses Pemesanan
                    </span>
                @elseif($order->status_tipe == 2)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                        Status: Proses Pengerjaan
                    </span>
                @elseif($order->status_tipe == 3)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                        Status: Menunggu Pelunasan
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        Status: Selesai
                    </span>
                @endif
            </div>
        </div>

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
                class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-800 text-xs sm:text-sm flex flex-col gap-1 shadow-2xs">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Terjadi Kesalahan:
                </div>
                <ul class="list-disc list-inside pl-7 text-xs space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            // Ambil semua dokumen persyaratan dinamis dari pemesananDokumen atau dari relasi layanan
            $dynamicDocs = collect();

            if ($order->pemesananDokumen && $order->pemesananDokumen->isNotEmpty()) {
                $dynamicDocs = $order->pemesananDokumen;
            } else {
                foreach ($order->pemesananLayanan as $pl) {
                    if ($pl->layanan && $pl->layanan->dokumenLayanan) {
                        foreach ($pl->layanan->dokumenLayanan as $dl) {
                            $dynamicDocs->push((object) [
                                'id' => null,
                                'nama_dokumen' => $dl->nama_dokumen,
                                'file_path' => null,
                            ]);
                        }
                    }
                }
            }
            $valPersyaratan = $order->getValidasi('dokumen_persyaratan');
            $statusPersyaratan = $order->getValidasiStatus('dokumen_persyaratan');
            $valDp = $order->getValidasi('bukti_dp');
            $valLunas = $order->getValidasi('bukti_lunas');
            $valSpk = $order->getValidasi('bukti_surat_perjanjian_kerja');
            $isDpApproved = ($valDp && $valDp->status_dokumen == 'disetujui') || (int) $order->status_tipe >= 2;
            $hasUploadedDp = !empty($order->bukti_dp);
            $canUploadSpk = $hasUploadedDp && $isDpApproved;
            $valSj = $order->getValidasi('surat_jalan');
            $valBa = $order->getValidasi('berita_acara');
            $valInv = $order->getValidasi('invoice');
            $isPerizinan = $order->isJasaPerizinan();
            $canShowSj = $order->canShowSuratJalan();
            $canShowBa = $order->canShowBeritaAcara();
            $canShowInv = (int) $order->status_tipe >= 3 || $order->isBeritaAcaraApproved();
        @endphp

        <!-- LAYOUT 2 KOLOM (KIRI & KANAN) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <!-- ================= KOLOM KIRI ================= -->
            <div class="space-y-6">

                <!-- 1. DATA PELANGGAN -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">DATA PELANGGAN</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-600">
                            <span class="text-slate-400 text-[10px] font-bold block uppercase">NAMA PELANGGAN</span>
                            <span
                                class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ $order->nama_pelanggan }}</span>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-600">
                            <span class="text-slate-400 text-[10px] font-bold block uppercase">NO. HP/WA</span>
                            <span
                                class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ $order->no_hp ?: '-' }}</span>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-600">
                            <span class="text-slate-400 text-[10px] font-bold block uppercase">EMAIL</span>
                            <span
                                class="font-bold text-slate-900 dark:text-white block mt-0.5 truncate">{{ $order->email ?: '-' }}</span>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-600">
                            <span class="text-slate-400 text-[10px] font-bold block uppercase">ALAMAT LENGKAP
                                PENGERJAAN</span>
                            <span
                                class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ $order->alamat ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. RINCIAN LAYANAN & PEMBAYARAN -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">RINCIAN LAYANAN</h3>
                    </div>

                    <div class="space-y-3">
                        @foreach($order->pemesananLayanan as $item)
                            <div
                                class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border border-slate-100 dark:border-slate-600">
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded uppercase">
                                        {{ $item->jenis_layanan }}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $item->nama_layanan }}
                                    </h4>
                                    <p class="text-xs text-slate-400">Harga: Rp {{ number_format($item->harga, 0, ',', '.') }} ×
                                        {{ $item->jumlah }} item
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-extrabold text-slate-900 dark:text-white">Rp
                                        {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($order->catatan)
                        <div
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 text-xs text-slate-600 dark:text-slate-300 border border-slate-100 dark:border-slate-600">
                            <span class="font-bold block text-slate-800 dark:text-white">Catatan Tambahan:</span>
                            <p class="mt-0.5">{{ $order->catatan }}</p>
                        </div>
                    @endif

                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700 space-y-4 text-xs sm:text-sm">
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 py-1">
                            <span class="font-medium">Subtotal Jasa</span>
                            <span class="font-bold text-slate-800 dark:text-white">Rp
                                {{ number_format($subtotalJasa, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 py-1">
                            <span class="font-medium">PPN 11%</span>
                            <span class="font-bold text-slate-800 dark:text-white">+ Rp
                                {{ number_format($ppnJasa, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 py-1">
                            <span class="font-medium">PPh Jasa 2%</span>
                            <span class="font-bold text-red-600 dark:text-red-400">- Rp
                                {{ number_format($pphJasa, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="p-3.5 rounded-xl bg-red-50/50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30">
                            <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                                <span class="text-red-600 dark:text-red-400 font-bold flex items-center gap-1.5 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span>Diskon Pesanan</span>
                                </span>
                                <span class="font-extrabold text-red-600 dark:text-red-400 text-xs">
                                    {{ $diskon > 0 ? '- Rp ' . number_format($diskon, 0, ',', '.') : '- Rp 0' }}
                                </span>
                            </div>
                        </div>
                        <div class="pt-6 pb-2 border-t-2 border-slate-200 dark:border-slate-600 space-y-4">
                            <div
                                class="flex justify-between items-center text-slate-900 dark:text-white font-black text-sm sm:text-base py-1">
                                <span class="font-black">Total Akhir</span>
                                <span class="text-slate-900 dark:text-white font-extrabold text-base sm:text-lg">Rp
                                    {{ number_format($totalInklPpn, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-4 space-y-3.5 border-t border-dashed border-slate-200 dark:border-slate-700">
                                <div
                                    class="flex justify-between items-center text-slate-600 dark:text-slate-300 font-bold text-xs py-1">
                                    <span>Total DP (50%)</span>
                                    <span class="text-slate-900 dark:text-white font-bold">Rp
                                        {{ number_format($totalDp, 0, ',', '.') }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-extrabold text-xs py-1">
                                    <span>Total Pelunasan</span>
                                    <span class="font-black">Rp
                                        {{ number_format($totalInklPpn - $totalDp, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. BUKTI TRANSFER PEMBAYARAN -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">BUKTI TRANSFER PEMBAYARAN
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div
                            class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Bukti DP
                                            (50%)</span>
                                        <span class="text-[10px] text-slate-400">Rp
                                            {{ number_format($totalDp, 0, ',', '.') }}</span>
                                    </div>
                                    @if($valDp && $valDp->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">Disetujui
                                            Admin</span>
                                    @elseif($valDp && $valDp->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full">Ditolak
                                            Admin</span>
                                    @elseif($order->bukti_dp)
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full">Menunggu
                                            Konfirmasi Admin</span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full">Belum
                                            Upload</span>
                                    @endif
                                </div>
                            </div>
                            <div class="pt-1 flex items-center justify-between gap-2 flex-wrap">
                                @if($order->bukti_dp)
                                    <a href="{{ asset($order->bukti_dp) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat Dokumen</span>
                                    </a>
                                    @if(!$valDp || $valDp->status_dokumen != 'disetujui')
                                        <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                            enctype="multipart/form-data" class="inline">
                                            @csrf
                                            <input type="hidden" name="jenis_bukti" value="bukti_dp">
                                            <label
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Ganti File</span>
                                                <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                    accept="image/*,.pdf" class="hidden" />
                                            </label>
                                        </form>
                                    @endif
                                @else
                                    <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                        enctype="multipart/form-data" class="w-full">
                                        @csrf
                                        <input type="hidden" name="jenis_bukti" value="bukti_dp">
                                        <label
                                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span>Upload Bukti DP</span>
                                            <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                accept="image/*,.pdf" class="hidden" />
                                        </label>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div
                            class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Bukti
                                            Pelunasan</span>
                                        <span class="text-[10px] text-slate-400">Rp
                                            {{ number_format($totalInklPpn - $totalDp, 0, ',', '.') }}</span>
                                    </div>
                                    @if($valLunas && $valLunas->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">Disetujui
                                            Admin</span>
                                    @elseif($valLunas && $valLunas->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full">Ditolak
                                            Admin</span>
                                    @elseif($order->bukti_lunas)
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full">Menunggu
                                            Konfirmasi Admin</span>
                                    @elseif(!$order->canUploadPelunasan())
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full">Belum
                                            Tersedia</span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full">Belum
                                            Upload</span>
                                    @endif
                                </div>
                            </div>
                            <div class="pt-1 flex items-center justify-between gap-2 flex-wrap">
                                @if($order->bukti_lunas)
                                    <a href="{{ asset($order->bukti_lunas) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat Dokumen</span>
                                    </a>
                                    @if(!$valLunas || $valLunas->status_dokumen != 'disetujui')
                                        <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                            enctype="multipart/form-data" class="inline">
                                            @csrf
                                            <input type="hidden" name="jenis_bukti" value="bukti_lunas">
                                            <label
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Ganti File</span>
                                                <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                    accept="image/*,.pdf" class="hidden" />
                                            </label>
                                        </form>
                                    @endif
                                @elseif(!$order->canUploadPelunasan())
                                    <div
                                        class="w-full py-2 px-3 rounded-lg bg-slate-100 dark:bg-slate-700/60 text-slate-400 text-center text-xs font-semibold">
                                        Belum Tersedia
                                    </div>
                                @else
                                    <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                        enctype="multipart/form-data" class="w-full">
                                        @csrf
                                        <input type="hidden" name="jenis_bukti" value="bukti_lunas">
                                        <label
                                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span>Upload Bukti Pelunasan</span>
                                            <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                accept="image/*,.pdf" class="hidden" />
                                        </label>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= KOLOM KANAN ================= -->
            <div class="space-y-6">

                <!-- 4. DOKUMEN PERSYARATAN LAYANAN -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">DOKUMEN PERSYARATAN
                                LAYANAN</h3>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Berkas persyaratan yang wajib diunggah
                                oleh pelanggan untuk layanan ini. Divalidasi oleh Direktur.</span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($statusPersyaratan === 'disetujui')
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 rounded-full">
                                    Disetujui Direktur
                                </span>
                            @elseif($statusPersyaratan === 'ditolak')
                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-0.5 rounded-full">
                                    Ditolak Direktur
                                </span>
                            @elseif($statusPersyaratan === 'menunggu')
                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-0.5 rounded-full">
                                    Menunggu Validasi Direktur
                                </span>
                            @endif
                            @if($dynamicDocs->isNotEmpty())
                                <span
                                    class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-0.5 rounded-full shrink-0">
                                    {{ $dynamicDocs->count() }} Berkas
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($statusPersyaratan === 'ditolak')
                        <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                            <span class="font-bold flex items-center gap-1.5 text-rose-700 dark:text-rose-300">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Dokumen Persyaratan Ditolak oleh Direktur
                            </span>
                            @if($valPersyaratan && $valPersyaratan->alasan)
                                <p class="text-[11px] pl-5 text-rose-600 dark:text-rose-400 font-medium">Alasan: {{ $valPersyaratan->alasan }}</p>
                            @endif
                            <p class="text-[10px] pl-5 text-slate-500 dark:text-slate-400">Silakan perbaiki atau unggah ulang berkas persyaratan yang belum sesuai.</p>
                        </div>
                    @endif

                    @if($dynamicDocs->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($dynamicDocs as $doc)
                                @php
                                    $filePath = $doc->file_path ?? null;
                                    $docName = $doc->nama_dokumen ?? 'Dokumen Persyaratan';
                                    $docId = $doc->id ?? null;
                                @endphp
                                <div
                                    class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $docName }}</span>
                                            @if($filePath)
                                                <span
                                                    class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">Terunggah</span>
                                            @else
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full">Belum
                                                    Upload</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="pt-1 flex items-center justify-between gap-2 flex-wrap">
                                        @if($filePath)
                                            <a href="{{ asset($filePath) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat Berkas</span>
                                            </a>

                                            @if($statusPersyaratan !== 'disetujui')
                                                <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                    enctype="multipart/form-data" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="jenis_bukti" value="dokumen_persyaratan">
                                                    @if($docId) <input type="hidden" name="pemesanan_dokumen_id" value="{{ $docId }}">
                                                    @endif
                                                    <input type="hidden" name="nama_dokumen" value="{{ $docName }}">
                                                    <label
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                        </svg>
                                                        <span>Ganti</span>
                                                        <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                            accept="image/*,.pdf" class="hidden" />
                                                    </label>
                                                </form>
                                            @endif
                                        @else
                                            <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                enctype="multipart/form-data" class="w-full">
                                                @csrf
                                                <input type="hidden" name="jenis_bukti" value="dokumen_persyaratan">
                                                @if($docId) <input type="hidden" name="pemesanan_dokumen_id" value="{{ $docId }}">
                                                @endif
                                                <input type="hidden" name="nama_dokumen" value="{{ $docName }}">
                                                <label
                                                    class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    <span>Upload</span>
                                                    <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                        accept="image/*,.pdf" class="hidden" />
                                                </label>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 text-center text-xs text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-600">
                            <p class="font-medium">Tidak ada dokumen persyaratan khusus untuk layanan ini.</p>
                        </div>
                    @endif
                </div>

                <!-- 5. DOKUMEN PROYEK (Kanan Tengah - di bawah Dokumen Persyaratan) -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">DOKUMEN PROYEK</h3>
                    </div>

                    <div class="space-y-4">
                        <!-- 1. SPK -->
                        <div
                            class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">1. Dokumen SPK
                                            (Surat Perjanjian Kerja)</span>
                                    </div>
                                    @if(!$canUploadSpk && !$order->bukti_surat_perjanjian_kerja)
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-0.5 rounded-full">
                                            Terkunci</span>
                                    @elseif($valSpk && $valSpk->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 rounded-full">
                                            Disetujui</span>
                                    @elseif($valSpk && $valSpk->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-0.5 rounded-full">Ditolak</span>
                                    @elseif($order->bukti_surat_perjanjian_kerja)
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-0.5 rounded-full">Menunggu
                                            Validasi</span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-0.5 rounded-full">Belum
                                            Upload</span>
                                    @endif
                                </div>

                                @if($valSpk && $valSpk->status_dokumen == 'ditolak' && $valSpk->alasan)
                                    <p class="text-[11px] text-red-500 italic">Alasan: {{ $valSpk->alasan }}</p>
                                @endif
                            </div>

                            @if(!$canUploadSpk && !$order->bukti_surat_perjanjian_kerja)
                                <div
                                    class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200/70 dark:border-amber-700/50 text-xs text-amber-800 dark:text-amber-300">
                                    Anda belum mengupload Bukti Pembayaran DP 50%, Silahkan Upload terlebih dahulu.
                                </div>
                            @else
                                <div class="pt-1 flex items-center justify-between gap-2 flex-wrap">
                                    @if($order->bukti_surat_perjanjian_kerja)
                                        <a href="{{ asset($order->bukti_surat_perjanjian_kerja) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Dokumen</span>
                                        </a>

                                        @if(!$valSpk || $valSpk->status_dokumen != 'disetujui')
                                            <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                enctype="multipart/form-data" class="inline">
                                                @csrf
                                                <input type="hidden" name="jenis_bukti" value="bukti_surat_perjanjian_kerja">
                                                <label
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    <span>Ganti File</span>
                                                    <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                        accept="image/*,.pdf" class="hidden" />
                                                </label>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                            enctype="multipart/form-data" class="w-full">
                                            @csrf
                                            <input type="hidden" name="jenis_bukti" value="bukti_surat_perjanjian_kerja">
                                            <label
                                                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Upload Dokumen SPK</span>
                                                <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                    accept="image/*,.pdf" class="hidden" />
                                            </label>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if(!$isPerizinan)
                            <!-- 2. Surat Jalan -->
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">2. Surat
                                            Jalan</span>
                                    </div>
                                    <div>
                                        @if(!$canShowSj)
                                            <span
                                                class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                                Belum Tersedia
                                            </span>
                                        @elseif($valSj && $valSj->status_dokumen == 'disetujui')
                                            <span
                                                class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                                Disetujui Direktur
                                            </span>
                                        @elseif($valSj && $valSj->status_dokumen == 'ditolak')
                                            <span
                                                class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-full">
                                                Ditolak: {{ $valSj->alasan }}
                                            </span>
                                        @elseif($valSj && $valSj->status_dokumen == 'menunggu')
                                            <span
                                                class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                                Menunggu Validasi
                                            </span>
                                        @else
                                            <span
                                                class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                                Belum Diajukan
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($order->canShowSuratJalan() && $order->isSuratJalanApproved())
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        <a href="{{ url('/cetak/surat-jalan/' . $order->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span>Unduh / Cetak Surat Jalan</span>
                                        </a>
                                    </div>
                                @else
                                    <p class="text-[11px] text-slate-400 italic pt-1">Surat Jalan belum tersedia.</p>
                                @endif
                            </div>
                        @endif

                        <!-- 3. Berita Acara (BAST) -->
                        <div
                            class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <span
                                        class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isPerizinan ? '2' : '3' }}.
                                        Berita Acara (BAST)</span>
                                </div>
                                <div>
                                    @if(!$order->isTeknisiReportApproved())
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                            Belum Tersedia
                                        </span>
                                    @elseif($valBa && $valBa->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                            Disetujui Direktur
                                        </span>
                                    @elseif($valBa && $valBa->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-full">
                                            Ditolak: {{ $valBa->alasan }}
                                        </span>
                                    @elseif($valBa && $valBa->status_dokumen == 'menunggu')
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                            Menunggu Validasi
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                            Belum Diajukan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($order->canShowBeritaAcara() && $order->isBeritaAcaraApproved())
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    <a href="{{ url('/cetak/berita-acara/' . $order->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Unduh / Cetak Berita Acara</span>
                                    </a>
                                </div>
                            @else
                                <p class="text-[11px] text-slate-400 italic pt-1">Berita Acara belum tersedia.</p>
                            @endif
                        </div>

                        <!-- 4. Invoice -->
                        <div
                            class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <span
                                        class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isPerizinan ? '3' : '4' }}.
                                        Invoice</span>
                                </div>
                                <div>
                                    @if($valInv && $valInv->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                            Disetujui Direktur
                                        </span>
                                    @elseif($valInv && $valInv->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-full">
                                            Sedang Direvisi
                                        </span>
                                    @elseif($valInv && $valInv->status_dokumen == 'menunggu')
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                            Menunggu Validasi Direktur
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                            Belum Tersedia
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($valInv && $valInv->status_dokumen == 'disetujui')
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    <a href="{{ url('/cetak/invoice/' . $order->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Unduh / Cetak Invoice</span>
                                    </a>
                                </div>
                            @elseif($valInv && $valInv->status_dokumen == 'menunggu')
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 italic pt-1">Invoice sedang diverifikasi oleh Direktur.</p>
                            @else
                                <p class="text-[11px] text-slate-400 italic pt-1">Invoice belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 6. LAPORAN PEKERJAAN TEKNISI (Kanan Bawah - di bawah Dokumen Proyek) -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">HASIL LAPORAN PEKERJAAN TEKNISI</h3>
                    </div>

                    <div
                        class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Laporan Pekerjaan Teknisi</span>
                            </div>
                            <div>
                                @if($order->isTeknisiReportApproved())
                                    <span
                                        class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                        Disetujui Direktur
                                    </span>
                                @elseif($order->hasTeknisiReport())
                                    <span
                                        class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                        Menunggu Validasi Direktur
                                    </span>
                                @else
                                    <span
                                        class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                        Belum Tersedia
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-1 flex flex-wrap items-center justify-between gap-2">
                            @if(!$order->hasTeknisiReport())
                                <p class="text-[11px] text-slate-400 italic">Laporan pekerjaan belum tersedia.</p>
                            @elseif($order->isTeknisiReportApproved())
                                <button type="button" onclick="openModal('modal-detail-laporan')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Lihat Detail Laporan Disini</span>
                                </button>
                            @else
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openModal('modal-detail-laporan')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 font-bold text-xs transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat Detail Laporan Disini</span>
                                    </button>
                                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Sedang
                                        diverifikasi Direktur</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Modal Detail Hasil Laporan Pekerjaan Teknisi -->
    <div id="modal-detail-laporan"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150 relative z-10">
            <!-- Modal Header -->
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
                <button type="button" onclick="closeModal('modal-detail-laporan')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content / List of Reports -->
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                @if($order->pelaporanTeknisi && $order->pelaporanTeknisi->count() > 0)
                    @foreach($order->pelaporanTeknisi as $laporan)
                        <div class="space-y-4 {{ !$loop->first ? 'pt-5 border-t border-slate-200 dark:border-slate-700' : '' }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div
                                    class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <span
                                        class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Nomor
                                        PO / ID Pesanan</span>
                                    <span class="text-sm font-black text-blue-600 dark:text-blue-400">{{ $order->id_pesanan }}</span>
                                </div>
                                <div
                                    class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <span
                                        class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Nama
                                        Pelanggan</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $order->nama_pelanggan }}</span>
                                </div>
                                <div
                                    class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <span
                                        class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Layanan</span>
                                    <div class="space-y-1">
                                        @if($order->pemesananLayanan && $order->pemesananLayanan->count() > 0)
                                            @foreach($order->pemesananLayanan as $item)
                                                <div class="flex items-start gap-1.5">
                                                    <span
                                                        class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 mt-1 shrink-0"></span>
                                                    <span
                                                        class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                                        {{ $item->nama_layanan }}
                                                        @if($item->jumlah > 1)
                                                            <span
                                                                class="text-[10px] text-blue-600 dark:text-blue-400 font-extrabold ml-0.5">(×{{ $item->jumlah }})</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span
                                                class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $laporan->nama_layanan ?? '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <span
                                        class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Tanggal
                                        Selesai Pengerjaan</span>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                        {{ $laporan->tanggal_selesai ? $laporan->tanggal_selesai->format('d/m/Y') : '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-700/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                                <span
                                    class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1.5">Alat
                                    yang Digunakan</span>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-relaxed">
                                    {{ $laporan->nama_alat ?: '-' }}
                                </p>
                            </div>

                            <div>
                                <span
                                    class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-2">Foto
                                    / Dokumentasi Hasil Pengerjaan</span>
                                <div>
                                    @if($laporan->gambar && file_exists(public_path($laporan->gambar)))
                                        <a href="{{ asset($laporan->gambar) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Dokumentasi Pekerjaan</span>
                                        </a>
                                    @else
                                        <div
                                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 border border-slate-100 dark:border-slate-700 text-xs text-slate-400 italic">
                                            Belum ada foto / dokumentasi pengerjaan yang diunggah.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-xs text-slate-400 italic text-center py-4">Belum ada laporan pengerjaan.</p>
                @endif
            </div>

            <!-- Modal Footer -->
            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-start bg-slate-50/50 dark:bg-slate-700/30">
                <button type="button" onclick="closeModal('modal-detail-laporan')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Close on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('modal-detail-laporan');
            }
        });
    </script>
@endsection