@extends('layouts.dashboard')

@section('title', 'Detail Validasi Dokumen')
@section('page_title', 'Detail Validasi Dokumen: ' . ($order->id_pesanan ?? ''))

@section('content')
    @php
        $subtotalJasa = $order->pemesananLayanan->sum(function ($i) {
            return $i->harga * $i->jumlah;
        });

        $targetTipe = $validasi->tipe_dokumen ?? request('tipe', 'bukti_po');
        $statusDokumenTarget = $validasi->status_dokumen ?? 'menunggu';

        // Cek jika sedang validasi Dokumen P.O dan ada diskon yang diajukan
        $isDiajukan = ($targetTipe === 'bukti_po' && $statusDokumenTarget !== 'disetujui' && ($order->diskon_diajukan ?? 0) > 0);
        $effectiveDiskon = $isDiajukan ? $order->diskon_diajukan : ($order->diskon ?? 0);
        $ppnJasa = $subtotalJasa * 0.11; // PPN 11%
        $pphJasa = $subtotalJasa * 0.02; // PPh Jasa 2%
        $totalInklPpn = max(0, ($subtotalJasa + $ppnJasa - $pphJasa) - $effectiveDiskon);
        $totalDp = $totalInklPpn * 0.50;

        $targetTipe = $validasi->tipe_dokumen ?? request('tipe', 'bukti_po');
        $namaDokumenTarget = match ($targetTipe) {
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

        $isSystemDoc = in_array($targetTipe, ['surat_jalan', 'berita_acara', 'invoice']);
        $isPersyaratan = ($targetTipe === 'dokumen_persyaratan');
        $systemDocUrl = match ($targetTipe) {
            'surat_jalan' => url('/cetak/surat-jalan/' . $order->id),
            'berita_acara' => url('/cetak/berita-acara/' . $order->id),
            'invoice' => url('/cetak/invoice/' . $order->id),
            default => null
        };

        $dynamicDocs = $order->pemesananDokumen ?? collect();
        $hasAnyPersyaratanFile = $dynamicDocs->whereNotNull('file_path')->isNotEmpty();

        if ($targetTipe === 'laporan_teknisi') {
            $lastReport = $order->pelaporanTeknisi ? $order->pelaporanTeknisi->last() : null;
            $filePathTarget = $lastReport ? $lastReport->gambar : null;
        } else {
            $filePathTarget = $order->{$targetTipe} ?? null;
        }

        $cleanPath = $filePathTarget ? ltrim($filePathTarget, '/\\') : null;
        $fileExists = $cleanPath && file_exists(public_path($cleanPath));

        $statusDokumenTarget = $validasi->status_dokumen ?? 'menunggu';
        $isPdf = $cleanPath ? (pathinfo($cleanPath, PATHINFO_EXTENSION) === 'pdf') : false;
        $canValidate = $isSystemDoc || ($isPersyaratan ? $hasAnyPersyaratanFile : $fileExists);
    @endphp

    <div class="space-y-6">
        <!-- Action Top Bar (Kembali & Detail Validasi Dokumen: id_pesanan) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                <a href="{{ url('/direktur/validasi-dokumen') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>

                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Detail Validasi Dokumen: {{ $order->id_pesanan }}
                </h2>
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

        <!-- LAYOUT 2 KOLOM (KIRI & KANAN) PRESISI SEJAJAR BAWAH -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            <!-- ================= KOLOM KIRI ================= -->
            <div class="flex flex-col gap-6">

                <!-- 1. DATA PELANGGAN (Atas Kiri) -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 space-y-4 shrink-0">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
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
                            <span class="text-slate-400 text-[10px] font-bold block uppercase">NO. HP / WA</span>
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
                            <span class="text-slate-400 text-[10px] font-bold block uppercase">ALAMAT PENGERJAAN</span>
                            <span
                                class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ $order->alamat ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. RINCIAN LAYANAN & PEMBAYARAN (Bawah Kiri) -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 flex-1 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
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
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                                            {{ $item->nama_layanan }}</h4>
                                        <p class="text-xs text-slate-400">Harga: Rp
                                            {{ number_format($item->harga, 0, ',', '.') }} × {{ $item->jumlah }} item</p>
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
                                <span class="font-bold block text-slate-800 dark:text-white">Catatan:</span>
                                <p class="mt-0.5">{{ $order->catatan }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Kalkulasi Ringkasan Pembayaran -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 space-y-3 text-xs sm:text-sm mt-4">
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

                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 py-1">
                            @if($isDiajukan)
                                <span class="text-red-500 font-bold">Diskon Pesanan Diajukan</span>
                                <span class="font-bold text-red-500">
                                    - Rp {{ number_format($effectiveDiskon, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-red-500 font-bold">Diskon Pesanan</span>
                                <span class="font-bold text-red-500">
                                    {{ $effectiveDiskon > 0 ? '- Rp ' . number_format($effectiveDiskon, 0, ',', '.') : '- Rp 0' }}
                                </span>
                            @endif
                        </div>

                        <!-- Total Akhir & Breakdown DP Pelunasan -->
                        <div class="pt-4 pb-1 border-t-2 border-slate-200 dark:border-slate-600 space-y-3">
                            <div
                                class="flex justify-between items-center text-slate-900 dark:text-white font-black text-sm sm:text-base py-1">
                                <span class="font-black">Total Akhir</span>
                                <span class="text-slate-900 dark:text-white font-extrabold text-base sm:text-lg">Rp
                                    {{ number_format($totalInklPpn, 0, ',', '.') }}</span>
                            </div>

                            <div class="pt-3 space-y-2 border-t border-dashed border-slate-200 dark:border-slate-700">
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

            </div>

            <!-- ================= KOLOM KANAN ================= -->
            <div class="flex flex-col gap-6">

                <!-- VALIDASI DOKUMEN (KOLOM KANAN) -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs p-6 flex-1 flex flex-col justify-between space-y-6">
                    <div
                        class="border-b border-slate-100 dark:border-slate-700 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                        <div>
                            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block">VALIDASI
                                DOKUMEN</span>
                            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mt-0.5">
                                {{ $namaDokumenTarget }}</h3>
                        </div>

                        <div>
                            @if(!$canValidate)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    Belum Ada File Terunggah
                                </span>
                            @elseif($statusDokumenTarget == 'disetujui')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Status: Disetujui Direktur
                                </span>
                            @elseif($statusDokumenTarget == 'ditolak')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                    Status: Ditolak ({{ $validasi->alasan ?? 'Tidak sesuai' }})
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    Status: Menunggu Validasi Direktur
                                </span>
                            @endif
                        </div>
                    </div>


                    <!-- PRATINJAU DOKUMEN (EXPANDS FULLY TO FILL COLUMN HEIGHT) -->
                    <div class="flex-1 flex flex-col justify-between space-y-4 my-auto">
                        @if($isPersyaratan)
                            <!-- BUNDLE SEMUA DOKUMEN PERSYARATAN LAYANAN -->
                            <div class="w-full flex-1 flex flex-col space-y-4">
                                <div
                                    class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-xs font-bold text-blue-900 dark:text-blue-200">Dokumen
                                            Persyaratan</span>
                                    </div>
                                    <span
                                        class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300">
                                        {{ $dynamicDocs->whereNotNull('file_path')->count() }} / {{ $dynamicDocs->count() }}
                                        Berkas Terunggah
                                    </span>
                                </div>

                                @if($dynamicDocs->isNotEmpty())
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-h-[520px] overflow-y-auto pr-1">
                                        @foreach($dynamicDocs as $doc)
                                            @php
                                                $docCleanPath = $doc->file_path ? ltrim($doc->file_path, '/\\') : null;
                                                $docExists = $docCleanPath && file_exists(public_path($docCleanPath));
                                                $docIsPdf = $docCleanPath ? (pathinfo($docCleanPath, PATHINFO_EXTENSION) === 'pdf') : false;
                                            @endphp
                                            <div
                                                class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40 flex flex-col justify-between space-y-3">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">
                                                            {{ $doc->nama_dokumen }}</h4>
                                                        <span class="text-[10px] text-slate-400">Berkas Persyaratan</span>
                                                    </div>
                                                    @if($docExists)
                                                        <span
                                                            class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                            Terunggah
                                                        </span>
                                                    @else
                                                        <span
                                                            class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-600 dark:bg-slate-600 dark:text-slate-300">
                                                            Belum Ada
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Preview thumbnail / box -->
                                                <div
                                                    class="rounded-lg overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600/70 p-2 flex items-center justify-center min-h-[120px]">
                                                    @if($docExists)
                                                        @if($docIsPdf)
                                                            <div class="text-center space-y-1 py-2">
                                                                <svg class="w-10 h-10 text-rose-500 mx-auto" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                                <span
                                                                    class="text-[11px] font-bold text-slate-700 dark:text-slate-200 block">Dokumen
                                                                    PDF</span>
                                                                <span
                                                                    class="text-[10px] text-slate-400 block truncate max-w-[140px]">{{ basename($docCleanPath) }}</span>
                                                            </div>
                                                        @else
                                                            <img src="{{ asset($docCleanPath) }}" alt="{{ $doc->nama_dokumen }}"
                                                                class="max-h-28 w-auto object-contain rounded">
                                                        @endif
                                                    @else
                                                        <div class="text-center text-slate-400 py-3 space-y-1">
                                                            <svg class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                            <span class="text-[10px] italic">Belum diunggah pelanggan</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if($docExists)
                                                    <a href="{{ asset($docCleanPath) }}" target="_blank"
                                                        class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        <span>Lihat Berkas</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div
                                        class="p-8 text-center text-slate-400 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <p class="text-xs">Tidak ada dokumen persyaratan khusus untuk pesanan ini.</p>
                                    </div>
                                @endif
                            </div>
                        @elseif($isSystemDoc)
                            <div class="w-full flex-1 flex flex-col space-y-3">
                                <iframe src="{{ $systemDocUrl }}" style="width: 100%; min-height: 480px;"
                                    class="w-full flex-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs"></iframe>
                                <div class="text-center shrink-0">
                                    <a href="{{ $systemDocUrl }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>Lihat Dokumen</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($fileExists)
                            @if($isPdf)
                                <div class="w-full flex-1 flex flex-col space-y-3">
                                    <iframe src="{{ asset($cleanPath) }}" style="width: 100%; min-height: 480px;"
                                        class="w-full flex-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs"></iframe>
                                    <div class="text-center shrink-0">
                                        <a href="{{ asset($cleanPath) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <span>Lihat Dokumen</span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="w-full flex-1 flex flex-col space-y-3 text-center justify-between">
                                    <img src="{{ asset($cleanPath) }}" alt="{{ $namaDokumenTarget }}" style="max-height: 480px;"
                                        class="w-auto max-w-full rounded-xl border border-slate-200 shadow-md object-contain mx-auto my-auto">
                                    <div class="text-center shrink-0">
                                        <a href="{{ asset($cleanPath) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <span>Lihat Dokumen</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div
                                class="p-12 text-center text-slate-400 space-y-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 my-auto">
                                <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-slate-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Berkas {{ $namaDokumenTarget }} belum diunggah atau tidak
                                    ditemukan.</p>
                            </div>
                        @endif
                    </div>

                    <!-- AKSI VALIDASI DIREKTUR (DI BAWAH SENDIRI SEJAJAR) -->
                    @if($canValidate)
                        <div
                            class="pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4 mt-auto shrink-0">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Validasi Direktur:</span>
                            <div class="flex items-center gap-3 w-full sm:w-auto">
                                @if($statusDokumenTarget == 'disetujui')
                                    <span
                                        class="px-5 py-2.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-extrabold text-xs rounded-xl flex items-center gap-2 border border-emerald-200 dark:border-emerald-800">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Di Setujui</span>
                                    </span>
                                @elseif($statusDokumenTarget == 'ditolak')
                                    <span
                                        class="px-5 py-2.5 bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400 font-extrabold text-xs rounded-xl flex items-center gap-2 border border-red-200 dark:border-red-800">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Di Tolak</span>
                                    </span>
                                @else
                                    <form action="{{ url('/direktur/validasi-dokumen/' . $order->id . '/setujui') }}" method="POST"
                                        class="shrink-0">
                                        @csrf
                                        <input type="hidden" name="tipe_dokumen" value="{{ $targetTipe }}">
                                        <button type="submit"
                                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition-all shadow-md flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Setujui Dokumen</span>
                                        </button>
                                    </form>

                                    <button onclick="toggleTolakFormTarget()"
                                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-xl transition-all shadow-md flex items-center gap-2 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Tolak Dokumen</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($statusDokumenTarget == 'menunggu')
                            <div id="tolak-form-target" class="hidden pt-3 border-t border-slate-100 dark:border-slate-700">
                                <form action="{{ url('/direktur/validasi-dokumen/' . $order->id . '/tolak') }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="tipe_dokumen" value="{{ $targetTipe }}">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200">Alasan Penolakan
                                        Dokumen:</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="alasan" required placeholder="Tuliskan alasan penolakan dokumen..."
                                            class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shrink-0 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            <span>Kirim Penolakan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif

                </div>

            </div>

        </div>
    </div>

    <script>
        function toggleTolakFormTarget() {
            const el = document.getElementById('tolak-form-target');
            if (el) {
                el.classList.toggle('hidden');
            }
        }
    </script>
@endsection