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
                <a href="{{ url('/admin/pesanan') }}"
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
            $valPoAdmin = $order->getValidasi('bukti_po');
            $statusPoAdmin = $order->getValidasiStatus('bukti_po');
            $valPersyaratanAdmin = $order->getValidasi('dokumen_persyaratan');
            $statusPersyaratanAdmin = $order->getValidasiStatus('dokumen_persyaratan');
            $hasUploadedPersyaratanAdmin = $dynamicDocs->whereNotNull('file_path')->isNotEmpty();
            $valDpAdmin = $order->getValidasi('bukti_dp');
            $valLunasAdmin = $order->getValidasi('bukti_lunas');
            $valSpkAdmin = $order->getValidasi('bukti_surat_perjanjian_kerja');
            $isDpApprovedAdmin = ($valDpAdmin && $valDpAdmin->status_dokumen == 'disetujui') || (int) $order->status_tipe >= 2;
            $hasUploadedDpAdmin = !empty($order->bukti_dp);
            $canUploadSpkAdmin = $hasUploadedDpAdmin && $isDpApprovedAdmin;
            $valSjAdmin = $order->getValidasi('surat_jalan');
            $valBaAdmin = $order->getValidasi('berita_acara');
            $valInvAdmin = $order->getValidasi('invoice');
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

                    <!-- Item Layanan Dipesan -->
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

                    <!-- Kalkulasi Ringkasan Pembayaran -->
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

                        <!-- Total Akhir & Breakdown DP Pelunasan -->
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

                <!-- 3. PO DAN PEMBAYARAN -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">PO DAN PEMBAYARAN</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">

                        <!-- 1. Dokumen PO (Purchase Order) -->
                        <div
                            class="p-3.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-1 flex-wrap">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Dokumen
                                            P.O</span>
                                        <span class="text-[10px] text-slate-400">Purchase Order</span>
                                    </div>
                                    <div>
                                        @if($statusPoAdmin === 'disetujui')
                                            <span
                                                class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Disetujui
                                                Direktur</span>
                                        @elseif($statusPoAdmin === 'ditolak')
                                            <span
                                                class="text-[9px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Ditolak
                                                Direktur</span>
                                        @elseif($order->bukti_po)
                                            <span
                                                class="text-[9px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Menunggu
                                                Validasi</span>
                                        @else
                                            <span
                                                class="text-[9px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full whitespace-nowrap">Belum
                                                Upload</span>
                                        @endif
                                    </div>
                                </div>

                                @if($statusPoAdmin === 'ditolak')
                                    <div
                                        class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 text-[11px] text-rose-800 dark:text-rose-300 space-y-0.5">
                                        <span class="font-bold block text-rose-700 dark:text-rose-300">Ditolak Direktur</span>
                                        @if($valPoAdmin && $valPoAdmin->alasan)
                                            <span
                                                class="text-[10px] text-rose-600 dark:text-rose-400 block">{{ $valPoAdmin->alasan }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="pt-1 flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-1.5 flex-wrap">
                                    @if($order->bukti_po)
                                        <a href="{{ asset($order->bukti_po) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat File</span>
                                        </a>

                                        @if($statusPoAdmin !== 'disetujui')
                                            <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                enctype="multipart/form-data" class="inline">
                                                @csrf
                                                <input type="hidden" name="jenis_bukti" value="bukti_po">
                                                <label
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                    <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor"
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
                                            <input type="hidden" name="jenis_bukti" value="bukti_po">
                                            <label
                                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Upload PO</span>
                                                <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                    accept="image/*,.pdf" class="hidden" />
                                            </label>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 2. Bukti DP (50%) -->
                        <div
                            class="p-3.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-1 flex-wrap">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Bukti DP
                                            (50%)</span>
                                        <span class="text-[10px] text-slate-400">Rp
                                            {{ number_format($totalDp, 0, ',', '.') }}</span>
                                    </div>
                                    <div>
                                        @if($valDpAdmin && $valDpAdmin->status_dokumen == 'disetujui')
                                            <span
                                                class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Disetujui
                                                Admin</span>
                                        @elseif($valDpAdmin && $valDpAdmin->status_dokumen == 'ditolak')
                                            <span
                                                class="text-[9px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Ditolak</span>
                                        @elseif($order->bukti_dp)
                                            <span
                                                class="text-[9px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Menunggu
                                                Konfirmasi</span>
                                        @else
                                            <span
                                                class="text-[9px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full whitespace-nowrap">Belum
                                                Upload</span>
                                        @endif
                                    </div>
                                </div>

                                @if($valDpAdmin && $valDpAdmin->status_dokumen == 'ditolak')
                                    <div
                                        class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 text-[11px] text-rose-800 dark:text-rose-300 space-y-0.5">
                                        <span class="font-bold block text-rose-700 dark:text-rose-300">Bukti DP ditolak</span>
                                        @if($valDpAdmin->alasan)
                                            <span
                                                class="text-[10px] text-rose-600 dark:text-rose-400 block">{{ $valDpAdmin->alasan }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="pt-1 flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-1.5 flex-wrap">
                                    @if($order->bukti_dp)
                                        <a href="{{ asset($order->bukti_dp) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat File</span>
                                        </a>

                                        @if(!$valDpAdmin || $valDpAdmin->status_dokumen != 'disetujui')
                                            <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                enctype="multipart/form-data" class="inline">
                                                @csrf
                                                <input type="hidden" name="jenis_bukti" value="bukti_dp">
                                                <label
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                    <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor"
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
                                            <input type="hidden" name="jenis_bukti" value="bukti_dp">
                                            <label
                                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Upload DP</span>
                                                <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                    accept="image/*,.pdf" class="hidden" />
                                            </label>
                                        </form>
                                    @endif
                                </div>

                                <!-- Aksi Konfirmasi Admin untuk Bukti DP -->
                                @if($order->bukti_dp && (!$valDpAdmin || $valDpAdmin->status_dokumen != 'disetujui'))
                                    <div
                                        class="pt-2 border-t border-slate-200/70 dark:border-slate-600/70 flex flex-wrap items-center gap-1.5">
                                        <button type="button" onclick="konfirmasiBukti('bukti_dp', 'Bukti DP (50%)')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Konfirmasi</span>
                                        </button>

                                        <button type="button" onclick="tolakBukti('bukti_dp', 'Bukti DP (50%)')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span>Tolak</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- 3. Bukti Pelunasan -->
                        <div
                            class="p-3.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-600/70 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-1 flex-wrap">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Bukti
                                            Pelunasan</span>
                                        <span class="text-[10px] text-slate-400">Rp
                                            {{ number_format($totalInklPpn - $totalDp, 0, ',', '.') }}</span>
                                    </div>
                                    <div>
                                        @if($valLunasAdmin && $valLunasAdmin->status_dokumen == 'disetujui')
                                            <span
                                                class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Disetujui
                                                Admin</span>
                                        @elseif($valLunasAdmin && $valLunasAdmin->status_dokumen == 'ditolak')
                                            <span
                                                class="text-[9px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Ditolak</span>
                                        @elseif($order->bukti_lunas)
                                            <span
                                                class="text-[9px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-full whitespace-nowrap">Menunggu
                                                Konfirmasi</span>
                                        @elseif(!$order->canUploadPelunasan())
                                            <span
                                                class="text-[9px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full whitespace-nowrap">Belum
                                                Tersedia</span>
                                        @else
                                            <span
                                                class="text-[9px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full whitespace-nowrap">Belum
                                                Upload</span>
                                        @endif
                                    </div>
                                </div>

                                @if($valLunasAdmin && $valLunasAdmin->status_dokumen == 'ditolak')
                                    <div
                                        class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 text-[11px] text-rose-800 dark:text-rose-300 space-y-0.5">
                                        <span class="font-bold block text-rose-700 dark:text-rose-300">Bukti Pelunasan
                                            ditolak</span>
                                        @if($valLunasAdmin->alasan)
                                            <span
                                                class="text-[10px] text-rose-600 dark:text-rose-400 block">{{ $valLunasAdmin->alasan }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="pt-1 flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-1.5 flex-wrap">
                                    @if($order->bukti_lunas)
                                        <a href="{{ asset($order->bukti_lunas) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat File</span>
                                        </a>

                                        @if(!$valLunasAdmin || $valLunasAdmin->status_dokumen != 'disetujui')
                                            <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                enctype="multipart/form-data" class="inline">
                                                @csrf
                                                <input type="hidden" name="jenis_bukti" value="bukti_lunas">
                                                <label
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                                    <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor"
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
                                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span>Upload Lunas</span>
                                                <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                    accept="image/*,.pdf" class="hidden" />
                                            </label>
                                        </form>
                                    @endif
                                </div>

                                <!-- Aksi Konfirmasi Admin untuk Bukti Pelunasan -->
                                @if($order->bukti_lunas && (!$valLunasAdmin || $valLunasAdmin->status_dokumen != 'disetujui'))
                                    <div
                                        class="pt-2 border-t border-slate-200/70 dark:border-slate-600/70 flex flex-wrap items-center gap-1.5">
                                        <button type="button" onclick="konfirmasiBukti('bukti_lunas', 'Bukti Pelunasan')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Konfirmasi</span>
                                        </button>

                                        <button type="button" onclick="tolakBukti('bukti_lunas', 'Bukti Pelunasan')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span>Tolak</span>
                                        </button>
                                    </div>
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
                                oleh pelanggan untuk layanan ini. Divalidasi langsung oleh Direktur.</span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($statusPersyaratanAdmin === 'disetujui')
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 rounded-full">
                                    Disetujui Direktur
                                </span>
                            @elseif($statusPersyaratanAdmin === 'ditolak')
                                <span class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-0.5 rounded-full">
                                    Ditolak Direktur
                                </span>
                            @elseif($statusPersyaratanAdmin === 'menunggu')
                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-0.5 rounded-full">
                                    Menunggu Validasi Direktur
                                </span>
                            @elseif($hasUploadedPersyaratanAdmin)
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-full">
                                    Belum Diajukan
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

                    @if($statusPersyaratanAdmin === 'ditolak')
                        <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 text-xs text-rose-800 dark:text-rose-300 space-y-1">
                            <span class="font-bold flex items-center gap-1.5 text-rose-700 dark:text-rose-300">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Dokumen Persyaratan Ditolak Direktur
                            </span>
                            @if($valPersyaratanAdmin && $valPersyaratanAdmin->alasan)
                                <p class="text-[11px] pl-5 text-rose-600 dark:text-rose-400">Alasan: {{ $valPersyaratanAdmin->alasan }}</p>
                            @endif
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
                                                    class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                                                    Terunggah</span>
                                            @else
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2 py-0.5 rounded-full">
                                                    Belum Upload</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400">Berkas persyaratan pesanan.</p>
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

                                            @if($statusPersyaratanAdmin !== 'disetujui')
                                                <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                                    enctype="multipart/form-data" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="jenis_bukti" value="dokumen_persyaratan">
                                                    @if($docId)
                                                        <input type="hidden" name="pemesanan_dokumen_id" value="{{ $docId }}">
                                                    @endif
                                                    <input type="hidden" name="nama_dokumen" value="{{ $docName }}">
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
                                                <input type="hidden" name="jenis_bukti" value="dokumen_persyaratan">
                                                @if($docId)
                                                    <input type="hidden" name="pemesanan_dokumen_id" value="{{ $docId }}">
                                                @endif
                                                <input type="hidden" name="nama_dokumen" value="{{ $docName }}">
                                                <label
                                                    class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer transition-all shadow-xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    <span>Upload Berkas</span>
                                                    <input type="file" name="file_bukti" onchange="this.form.submit()"
                                                        accept="image/*,.pdf" class="hidden" />
                                                </label>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($hasUploadedPersyaratanAdmin)
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <span class="text-xs text-slate-500">Ajukan semua dokumen persyaratan layanan ke Direktur.</span>
                                @if(!$valPersyaratanAdmin || $statusPersyaratanAdmin === 'ditolak')
                                    <form action="{{ url('/admin/pesanan/' . $order->id . '/ajukan-dokumen') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tipe_dokumen" value="dokumen_persyaratan">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>Ajukan ke Direktur</span>
                                        </button>
                                    </form>
                                @elseif($statusPersyaratanAdmin === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400">
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Menunggu Validasi Direktur
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Tervalidasi Direktur
                                    </span>
                                @endif
                            </div>
                        @endif
                    @else
                        <div
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 text-center text-xs text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-600">
                            <p class="font-medium">Tidak ada dokumen persyaratan khusus untuk layanan ini.</p>
                        </div>
                    @endif
                </div>

                <!-- 5. DOKUMEN PROYEK -->
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
                                    @if(!$canUploadSpkAdmin && !$order->bukti_surat_perjanjian_kerja)
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-0.5 rounded-full">
                                            Terkunci</span>
                                    @elseif($valSpkAdmin && $valSpkAdmin->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 rounded-full">
                                            Disetujui</span>
                                    @elseif($valSpkAdmin && $valSpkAdmin->status_dokumen == 'ditolak')
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

                                @if($valSpkAdmin && $valSpkAdmin->status_dokumen == 'ditolak' && $valSpkAdmin->alasan)
                                    <p class="text-[11px] text-red-500 italic">Alasan: {{ $valSpkAdmin->alasan }}</p>
                                @endif
                            </div>

                            @if(!$canUploadSpkAdmin && !$order->bukti_surat_perjanjian_kerja)
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

                                        @if(!$valSpkAdmin || $valSpkAdmin->status_dokumen != 'disetujui')
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
                                        @elseif($valSjAdmin && $valSjAdmin->status_dokumen == 'disetujui')
                                            <span
                                                class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                                Disetujui Direktur
                                            </span>
                                        @elseif($valSjAdmin && $valSjAdmin->status_dokumen == 'ditolak')
                                            <span
                                                class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-full">
                                                Ditolak
                                            </span>
                                        @elseif($valSjAdmin && $valSjAdmin->status_dokumen == 'menunggu')
                                            <span
                                                class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                                Menunggu Validasi Direktur
                                            </span>
                                        @else
                                            <span
                                                class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">
                                                Siap Diajukan
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($valSjAdmin && $valSjAdmin->status_dokumen == 'ditolak' && $valSjAdmin->alasan)
                                    <p class="text-[11px] text-red-500 italic">Alasan: {{ $valSjAdmin->alasan }}</p>
                                @endif

                                <div class="pt-1 flex flex-wrap items-center justify-between gap-2">
                                    @if(!$canShowSj)
                                        <p class="text-[11px] text-slate-400 italic">Surat Jalan dapat diajukan setelah SPK
                                            disetujui oleh Direktur.</p>
                                    @elseif($valSjAdmin && $valSjAdmin->status_dokumen == 'disetujui')
                                        <a href="{{ url('/cetak/surat-jalan/' . $order->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span>Unduh / Cetak Surat Jalan</span>
                                        </a>
                                    @elseif($valSjAdmin && $valSjAdmin->status_dokumen == 'menunggu')
                                        <div class="flex items-center gap-2">
                                            <a href="{{ url('/cetak/surat-jalan/' . $order->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 font-bold text-xs transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Pratinjau Surat Jalan</span>
                                            </a>
                                            <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Sedang
                                                diverifikasi Direktur</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 w-full justify-between flex-wrap">
                                            <a href="{{ url('/cetak/surat-jalan/' . $order->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 font-bold text-xs transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Pratinjau Dokumen</span>
                                            </a>
                                            <button type="button" onclick="openModal('modal-ajukan-surat-jalan')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span>{{ ($valSjAdmin && $valSjAdmin->status_dokumen == 'ditolak') ? 'Ajukan Ulang Surat Jalan' : 'Ajukan Surat Jalan ke Direktur' }}</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
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
                                    @elseif($valBaAdmin && $valBaAdmin->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                            Disetujui Direktur
                                        </span>
                                    @elseif($valBaAdmin && $valBaAdmin->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-full">
                                            Ditolak
                                        </span>
                                    @elseif($valBaAdmin && $valBaAdmin->status_dokumen == 'menunggu')
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                            Menunggu Validasi Direktur
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">
                                            Siap Diajukan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($valBaAdmin && $valBaAdmin->status_dokumen == 'ditolak' && $valBaAdmin->alasan)
                                <p class="text-[11px] text-red-500 italic">Alasan: {{ $valBaAdmin->alasan }}</p>
                            @endif

                            <div class="pt-1 flex flex-wrap items-center justify-between gap-2">
                                @if(!$order->isTeknisiReportApproved())
                                    <p class="text-[11px] text-slate-400 italic">Berita Acara dapat diajukan setelah Manager
                                        Teknisi melaporkan pekerjaan dan dikonfirmasi Direktur.</p>
                                @elseif($valBaAdmin && $valBaAdmin->status_dokumen == 'disetujui')
                                    <a href="{{ url('/cetak/berita-acara/' . $order->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Unduh / Cetak Berita Acara</span>
                                    </a>
                                @elseif($valBaAdmin && $valBaAdmin->status_dokumen == 'menunggu')
                                    <div class="flex items-center gap-2">
                                        <a href="{{ url('/cetak/berita-acara/' . $order->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 font-bold text-xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Pratinjau Berita Acara</span>
                                        </a>
                                        <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Sedang
                                            diverifikasi Direktur</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 w-full justify-between flex-wrap">
                                        <a href="{{ url('/cetak/berita-acara/' . $order->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 font-bold text-xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Pratinjau Dokumen</span>
                                        </a>
                                        <form action="{{ url('/admin/pesanan/' . $order->id . '/ajukan-dokumen') }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="tipe_dokumen" value="berita_acara">
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span>{{ ($valBaAdmin && $valBaAdmin->status_dokumen == 'ditolak') ? 'Ajukan Ulang Berita Acara' : 'Ajukan Berita Acara ke Direktur' }}</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
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
                                    @if(!$order->isBeritaAcaraApproved())
                                        <span
                                            class="text-[10px] font-bold text-slate-400 bg-slate-200/70 dark:bg-slate-600 px-2.5 py-1 rounded-full">
                                             Belum Tersedia
                                        </span>
                                    @elseif($valInvAdmin && $valInvAdmin->status_dokumen == 'disetujui')
                                        <span
                                            class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">
                                            Disetujui Direktur
                                        </span>
                                    @elseif($valInvAdmin && $valInvAdmin->status_dokumen == 'ditolak')
                                        <span
                                            class="text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-full">
                                            Ditolak
                                        </span>
                                    @elseif($valInvAdmin && $valInvAdmin->status_dokumen == 'menunggu')
                                        <span
                                            class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">
                                            Menunggu Validasi Direktur
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">
                                            Belum Diajukan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($valInvAdmin && $valInvAdmin->status_dokumen == 'ditolak' && $valInvAdmin->alasan)
                                <p class="text-[11px] text-red-500 italic">Alasan: {{ $valInvAdmin->alasan }}</p>
                            @endif

                            <div class="pt-1 flex flex-wrap items-center justify-between gap-2">
                                @if(!$order->isBeritaAcaraApproved())
                                    <p class="text-[11px] text-slate-400 italic">Invoice dapat diajukan setelah Berita Acara (BAST) disetujui Direktur.</p>
                                @elseif($valInvAdmin && $valInvAdmin->status_dokumen == 'disetujui')
                                    <a href="{{ url('/cetak/invoice/' . $order->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Unduh / Cetak Invoice</span>
                                    </a>
                                @elseif($valInvAdmin && $valInvAdmin->status_dokumen == 'menunggu')
                                    <div class="flex items-center gap-2">
                                        <a href="{{ url('/cetak/invoice/' . $order->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 font-bold text-xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Pratinjau Invoice</span>
                                        </a>
                                        <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Sedang
                                            diverifikasi Direktur</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 w-full justify-between flex-wrap">
                                        <a href="{{ url('/cetak/invoice/' . $order->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 font-bold text-xs transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Pratinjau Dokumen</span>
                                        </a>
                                        <form action="{{ url('/admin/pesanan/' . $order->id . '/ajukan-dokumen') }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="tipe_dokumen" value="invoice">
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span>{{ ($valInvAdmin && $valInvAdmin->status_dokumen == 'ditolak') ? 'Ajukan Ulang Invoice' : 'Ajukan Invoice ke Direktur' }}</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
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
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Bukti Pembayaran -->
    <div id="modal-konfirmasi-bukti" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-konfirmasi-bukti')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-sm p-6 text-center relative z-10">
                <div
                    class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/30 flex items-center justify-center mx-auto mb-4 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-2" id="konfirmasi-judul">Konfirmasi
                    Bukti Pembayaran?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6" id="konfirmasi-pesan">
                    Anda yakin ingin mengonfirmasi dan menyetujui <strong id="konfirmasi-nama"
                        class="text-slate-800 dark:text-slate-200">Bukti Pembayaran</strong>? Status pesanan akan
                    diperbarui.
                </p>
                <form id="form-konfirmasi-bukti" method="POST"
                    action="{{ url('/admin/pesanan/' . $order->id . '/konfirmasi-bukti') }}"
                    class="flex justify-center gap-2">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" id="konfirmasi-tipe" value="">
                    <button type="button" onclick="closeModal('modal-konfirmasi-bukti')"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-600/20 transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Ya, Konfirmasi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tolak Bukti Pembayaran -->
    <div id="modal-tolak-bukti" class="fixed inset-0 z-[99] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-tolak-bukti')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-sm p-6 text-center relative z-10">
                <div
                    class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-950/30 flex items-center justify-center mx-auto mb-4 text-red-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-2" id="tolak-judul">Tolak Bukti
                    Pembayaran?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6" id="tolak-pesan">
                    Anda yakin ingin menolak <strong id="tolak-nama" class="text-slate-800 dark:text-slate-200">Bukti
                        Pembayaran</strong>? Pelanggan akan diminta mengunggah ulang bukti pembayaran.
                </p>
                <form id="form-tolak-bukti" method="POST"
                    action="{{ url('/admin/pesanan/' . $order->id . '/tolak-bukti') }}" class="flex justify-center gap-2">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" id="tolak-tipe" value="">
                    <button type="button" onclick="closeModal('modal-tolak-bukti')"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm shadow-red-600/20 transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Ya, Tolak</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL AJUKAN SURAT JALAN --}}
    <div id="modal-ajukan-surat-jalan"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150 relative z-10 flex flex-col max-h-[90vh]">
            
            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/30 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Form Pengajuan Surat Jalan</h3>
                        <p class="text-[11px] text-slate-400">Tentukan rincian barang/alat yang akan dibawa teknisi ke lokasi</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-ajukan-surat-jalan')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form Body --}}
            <form action="{{ url('/admin/pesanan/' . $order->id . '/ajukan-dokumen') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="tipe_dokumen" value="surat_jalan">

                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    {{-- Info Pesanan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-700/40 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-0.5">ID Pesanan</span>
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400">{{ $order->id_pesanan }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-0.5">Nama Pelanggan</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $order->nama_pelanggan }}</span>
                        </div>
                    </div>

                    {{-- Daftar Master Alat (Datalist) --}}
                    <datalist id="master-alat-options">
                        @if(isset($alats))
                            @foreach($alats as $a)
                                <option value="{{ $a->nama_alat }}">
                            @endforeach
                        @endif
                        @foreach($order->pemesananLayanan as $pl)
                            <option value="{{ $pl->nama_layanan }}">
                        @endforeach
                    </datalist>

                    {{-- Section Rincian Alat yang Dibawa --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1">
                                <span>Daftar Barang / Alat yang Dibawa</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="tambahBarisAlat()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg text-[11px] font-bold transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Alat</span>
                            </button>
                        </div>

                        <div id="container-alat-rows" class="space-y-2.5">
                            {{-- Baris Default 1 --}}
                            @php
                                $firstLayanan = $order->pemesananLayanan->first();
                                $defaultNama = $firstLayanan ? $firstLayanan->nama_layanan : '';
                            @endphp
                            <div class="alat-row flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-200 dark:border-slate-600">
                                <div class="flex-1 min-w-0">
                                    <input type="text" name="alat_nama[]" list="master-alat-options" required
                                        value="{{ $defaultNama }}"
                                        placeholder="Pilih dari master alat atau ketik nama..."
                                        class="w-full px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-20 shrink-0">
                                    <input type="number" name="alat_jumlah[]" min="1" value="1" required
                                        placeholder="Jml"
                                        class="w-full px-2 py-1.5 text-xs font-bold text-center rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-28 shrink-0">
                                    <input type="text" name="alat_keterangan[]" value="Baik" required
                                        placeholder="Ket"
                                        class="w-full px-2.5 py-1.5 text-xs font-medium text-center rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <button type="button" onclick="hapusBarisAlat(this)"
                                    class="p-1.5 text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors cursor-pointer rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Pengantaran --}}
                    <div>
                        <label for="catatan-sj" class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                            Catatan Pengantaran (Opsional)
                        </label>
                        <textarea name="catatan_surat_jalan" id="catatan-sj" rows="2"
                            placeholder="Contoh: Pengantaran ke workshop / gedung utama area perakitan..."
                            class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">{{ $order->catatan }}</textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/30 shrink-0">
                    <button type="button" onclick="closeModal('modal-ajukan-surat-jalan')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span>Ajukan Surat Jalan</span>
                    </button>
                </div>
            </form>
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

        function konfirmasiBukti(tipe, namaBukti) {
            document.getElementById('konfirmasi-tipe').value = tipe;
            document.getElementById('konfirmasi-nama').textContent = namaBukti;
            document.getElementById('konfirmasi-judul').textContent = 'Konfirmasi ' + namaBukti + '?';
            openModal('modal-konfirmasi-bukti');
        }

        function tolakBukti(tipe, namaBukti) {
            document.getElementById('tolak-tipe').value = tipe;
            document.getElementById('tolak-nama').textContent = namaBukti;
            document.getElementById('tolak-judul').textContent = 'Tolak ' + namaBukti + '?';
            openModal('modal-tolak-bukti');
        }

        function tambahBarisAlat() {
            const container = document.getElementById('container-alat-rows');
            const newRow = document.createElement('div');
            newRow.className = 'alat-row flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-200 dark:border-slate-600';
            newRow.innerHTML = `
                <div class="flex-1 min-w-0">
                    <input type="text" name="alat_nama[]" list="master-alat-options" required
                        placeholder="Pilih dari master alat atau ketik nama..."
                        class="w-full px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-20 shrink-0">
                    <input type="number" name="alat_jumlah[]" min="1" value="1" required
                        placeholder="Jml"
                        class="w-full px-2 py-1.5 text-xs font-bold text-center rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-28 shrink-0">
                    <input type="text" name="alat_keterangan[]" value="Baik" required
                        placeholder="Ket"
                        class="w-full px-2.5 py-1.5 text-xs font-medium text-center rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="button" onclick="hapusBarisAlat(this)"
                    class="p-1.5 text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors cursor-pointer rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            container.appendChild(newRow);
        }

        function hapusBarisAlat(btn) {
            const rows = document.querySelectorAll('.alat-row');
            if (rows.length > 1) {
                btn.closest('.alat-row').remove();
            } else {
                alert('Minimal 1 barang / alat harus diisi.');
            }
        }

        // Close on ESC key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal('modal-konfirmasi-bukti');
                closeModal('modal-tolak-bukti');
                closeModal('modal-ajukan-surat-jalan');
            }
        });
    </script>
@endsection