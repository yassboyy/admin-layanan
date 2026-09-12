@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    @php
        $subtotalJasa = $order->pemesananLayanan->sum(function ($i) {
            return $i->harga * $i->jumlah;
        });
        $diskon = $order->diskon ?? 0;
        $ppnJasa = $subtotalJasa * 0.11; // PPN 11%
        $pphJasa = $subtotalJasa * 0.02; // PPh Jasa 2%
        $totalInklPpn = max(0, ($subtotalJasa + $ppnJasa - $pphJasa) - $diskon);
        $totalDp = $totalInklPpn * 0.50; // 50% DP
        $isPaymentValidated = ($order->getValidasiStatus('bukti_dp') === 'disetujui') || ($order->status_tipe >= 2);

        $backUrl = url('/pesanan');
    @endphp

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Detail Pesanan: {{ $order->id_pesanan }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1">Dibuat pada
                        {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                <div>
                    @if($order->status_tipe == 1)
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 backdrop-blur-md">
                            Status: Proses Pemesanan
                        </span>
                    @elseif($order->status_tipe == 2)
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 backdrop-blur-md">
                            Status: Proses Pengerjaan
                        </span>
                    @elseif($order->status_tipe == 3)
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 backdrop-blur-md">
                            Status: Menunggu Pelunasan
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-xs sm:text-sm font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 backdrop-blur-md">
                            Status: Selesai
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Detail Content Section -->
    <section class="py-12 bg-slate-50/70 min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div
                    class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm flex items-start gap-3 shadow-sm">
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
                <div class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-sm flex flex-col gap-1 shadow-sm">
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

            <!-- MASTER UNIFIED CONTAINER WITH SOFT SHADOW -->
            <div class="bg-white rounded-3xl shadow-sm p-6 sm:p-10 space-y-8">

                <!-- ROW 1: Layanan Dipesan (Paling Atas, Tanpa Gambar) -->
                <div class="p-6 sm:p-8 rounded-2xl bg-slate-50/60 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-1">
                        <h3 class="text-base font-extrabold text-slate-900">
                            Layanan Dipesan
                        </h3>
                        <span class="text-xs text-slate-400 font-semibold">{{ $order->pemesananLayanan->count() }} Item
                            Layanan</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($order->pemesananLayanan as $item)
                            <div
                                class="p-4 sm:p-5 rounded-2xl bg-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-slate-100 shadow-2xs">
                                <div class="space-y-1">
                                    <span
                                        class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                        {{ $item->jenis_layanan }}
                                    </span>
                                    <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mt-1">
                                        {{ $item->nama_layanan }}
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Harga Satuan: Rp {{ number_format($item->harga, 0, ',', '.') }} × {{ $item->jumlah }}
                                        item
                                    </p>
                                </div>
                                <div class="text-right sm:self-center">
                                    <span
                                        class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Subtotal</span>
                                    <span class="text-sm sm:text-base font-extrabold text-slate-900">
                                        Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ROW 2: Data Pelanggan & Catatan (Kiri) SEJAJAR DENGAN Rincian Pembayaran (Kanan) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                    <!-- Left: Data Pelanggan & Catatan Pesanan (Merged, Tanpa Garis Hitam) -->
                    <div class="lg:col-span-2">
                        <div class="p-6 sm:p-8 rounded-2xl bg-slate-50/60 shadow-xs h-full space-y-4">
                            <h3 class="text-base font-extrabold text-slate-900 pb-1">
                                Data Pelanggan
                            </h3>

                            <div class="bg-white p-5 rounded-2xl border border-slate-100 space-y-4 text-xs sm:text-sm">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <span
                                            class="text-slate-400 text-[11px] font-bold block uppercase tracking-wider">Nama
                                            Pelanggan</span>
                                        <span class="font-bold text-slate-800">{{ $order->nama_pelanggan }}</span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-slate-400 text-[11px] font-bold block uppercase tracking-wider">Nomor
                                            HP/WA</span>
                                        <span class="font-bold text-slate-800">{{ $order->no_hp ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-slate-400 text-[11px] font-bold block uppercase tracking-wider">Email</span>
                                        <span class="font-bold text-slate-800">{{ $order->email ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-slate-400 text-[11px] font-bold block uppercase tracking-wider">Alamat
                                            Lengkap Pengerjaan</span>
                                        <span
                                            class="font-bold text-slate-800 leading-relaxed block mt-0.5">{{ $order->alamat ?: '-' }}</span>
                                    </div>
                                </div>

                                <!-- Catatan Pesanan (Tanpa Border / Garis) -->
                                <div class="pt-2">
                                    <span
                                        class="text-slate-400 text-[11px] font-bold block uppercase tracking-wider mb-1.5">Catatan
                                        Pesanan</span>
                                    <p class="font-medium text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl">
                                        {{ $order->catatan ?: 'Tidak ada catatan khusus.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Rincian Pembayaran -->
                    <div class="lg:col-span-1">
                        <div class="p-6 sm:p-8 rounded-2xl bg-slate-50/60 shadow-xs h-full space-y-4">
                            <h3 class="text-base font-extrabold text-slate-900 pb-1">
                                Rincian Pembayaran
                            </h3>

                            <div class="space-y-3 text-xs sm:text-sm">
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500 font-medium">Subtotal Jasa</span>
                                    <span class="font-bold text-slate-900">Rp
                                        {{ number_format($subtotalJasa, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500 font-medium">PPN 11%</span>
                                    <span class="font-bold text-slate-900">+ Rp
                                        {{ number_format($ppnJasa, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500 font-medium">PPh Jasa 2%</span>
                                    <span class="font-bold text-red-600">- Rp
                                        {{ number_format($pphJasa, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500 font-medium">Diskon</span>
                                    <span class="font-bold {{ $diskon > 0 ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $diskon > 0 ? '- Rp ' . number_format($diskon, 0, ',', '.') : 'Rp 0' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-slate-800 font-bold text-sm">Total Biaya</span>
                                    <span class="text-xl font-extrabold text-blue-600">Rp
                                        {{ number_format($totalInklPpn, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-slate-200/60">
                                    <span class="text-slate-800 font-bold text-sm">Total DP (50%)</span>
                                    <span class="text-lg font-extrabold text-indigo-600">Rp
                                        {{ number_format($totalDp, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROW 3: Metode Pembayaran (Kiri) SEJAJAR DENGAN Status Dokumen (Kanan) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                    <!-- Left: Metode Pembayaran -->
                    <div class="lg:col-span-2">
                        <div class="p-6 rounded-2xl bg-slate-50/60 shadow-xs h-full space-y-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">METODE PEMBAYARAN</h4>
                                <p class="text-sm text-slate-600 mt-1">Pembayaran dapat ditransfer melalui rekening berikut:
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <!-- BCA -->
                                <div
                                    class="p-5 rounded-2xl bg-white flex items-center gap-4 hover:bg-blue-50/40 transition-all">
                                    <div class="w-16 h-12 shrink-0 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('images/banks/bca.svg') }}" alt="Bank BCA"
                                            class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-[#005CAA] tracking-wide block">BANK BCA</span>
                                        <span
                                            class="text-base font-extrabold text-slate-900 tracking-tight block">1171121162</span>
                                        <span class="text-[11px] text-slate-400 font-bold block">a.n. SUTOMO</span>
                                    </div>
                                </div>

                                <!-- BNI -->
                                <div
                                    class="p-5 rounded-2xl bg-white flex items-center gap-4 hover:bg-orange-50/40 transition-all">
                                    <div class="w-16 h-12 shrink-0 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('images/banks/bni.svg') }}" alt="Bank BNI"
                                            class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-[#F15A24] tracking-wide block">BANK BNI</span>
                                        <span
                                            class="text-base font-extrabold text-slate-900 tracking-tight block">0536364196</span>
                                        <span class="text-[11px] text-slate-400 font-bold block">a.n. SUTOMO</span>
                                    </div>
                                </div>

                                <!-- BRI -->
                                <div
                                    class="p-5 rounded-2xl bg-white flex items-center gap-4 hover:bg-blue-50/40 transition-all">
                                    <div class="w-16 h-12 shrink-0 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('images/banks/bri.svg') }}" alt="Bank BRI"
                                            class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-[#00529B] tracking-wide block">BANK BRI</span>
                                        <span
                                            class="text-base font-extrabold text-slate-900 tracking-tight block">569901012636530</span>
                                        <span class="text-[11px] text-slate-400 font-bold block">a.n. SUTOMO</span>
                                    </div>
                                </div>

                                <!-- MANDIRI -->
                                <div
                                    class="p-5 rounded-2xl bg-white flex items-center gap-4 hover:bg-amber-50/40 transition-all">
                                    <div class="w-16 h-12 shrink-0 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('images/banks/mandiri.svg') }}" alt="Bank Mandiri"
                                            class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-[#003D79] tracking-wide block">BANK
                                            MANDIRI</span>
                                        <span
                                            class="text-base font-extrabold text-slate-900 tracking-tight block">1140026428576</span>
                                        <span class="text-[11px] text-slate-400 font-bold block">a.n. SUTOMO</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Status Dokumen -->
                    <div class="lg:col-span-1">
                        <div
                            class="p-6 rounded-2xl bg-slate-50/60 shadow-xs h-full space-y-4 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">STATUS DOKUMEN
                                </h4>
                                <p class="text-xs text-slate-500 mb-4">Status pengunggahan dokumen</p>

                                <div class="space-y-3.5 text-xs sm:text-sm">
                                    <!-- 1. Dokumen P.O -->
                                    <div
                                        class="flex items-center justify-between gap-3 p-3.5 rounded-xl bg-white border border-slate-100 shadow-2xs">
                                        <span class="font-bold text-slate-700 text-xs sm:text-sm">Dokumen P.O</span>
                                        @if(empty($order->bukti_po))
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-slate-100 text-slate-600 border border-slate-200">
                                                Belum Upload
                                            </span>
                                        @elseif($order->getValidasiStatus('bukti_po') === 'disetujui')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Diterima
                                            </span>
                                        @elseif($order->getValidasiStatus('bukti_po') === 'ditolak')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-rose-50 text-rose-700 border border-rose-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu Validasi
                                            </span>
                                        @endif
                                    </div>

                                    <!-- 2. Bukti DP 50% -->
                                    <div
                                        class="flex items-center justify-between gap-3 p-3.5 rounded-xl bg-white border border-slate-100 shadow-2xs">
                                        <span class="font-bold text-slate-700 text-xs sm:text-sm">Bukti DP 50%</span>
                                        @if(empty($order->bukti_dp))
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-slate-100 text-slate-600 border border-slate-200">
                                                Belum Upload
                                            </span>
                                        @elseif($order->getValidasiStatus('bukti_dp') === 'disetujui')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Diterima
                                            </span>
                                        @elseif($order->getValidasiStatus('bukti_dp') === 'ditolak')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-rose-50 text-rose-700 border border-rose-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 whitespace-nowrap bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu Validasi
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM ROW (FULL WIDTH CENTERED): Upload Dokumen & Bukti Pembayaran -->
                <div class="pt-6 space-y-6">
                    <div class="text-center max-w-xl mx-auto">
                        <h3 class="text-base font-extrabold text-slate-900">Upload Dokumen PO & Bukti Pembayaran DP 50%</h3>
                        <p class="text-xs text-slate-500 mt-1">Silahkan Unggah Dokumen PO dan Bukti DP 50%
                            untuk melanjutkan proses pemesanan.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">

                        <!-- 1. Upload Dokumen P.O -->
                        @php
                            $valPermohonan = $order->getValidasi('bukti_po');
                            $statusPermohonan = $order->getValidasiStatus('bukti_po');
                        @endphp
                        <div class="space-y-4 flex flex-col justify-between p-5 rounded-2xl bg-white shadow-2xs">
                            <div class="text-center">
                                <h4 class="text-sm font-extrabold text-slate-900 mb-1">
                                    Pengajuan Diskon & Dokumen P.O (Purchase Order)
                                </h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Ajukan diskon pesanan (opsional) dan unggah Dokumen PO.
                                </p>
                            </div>

                            @if($statusPermohonan === 'ditolak')
                                <div class="py-2.5 px-3 rounded-xl bg-rose-50 text-rose-800 text-xs space-y-0.5 text-center">
                                    <div class="font-bold text-rose-700">
                                        Pengajuan Ditolak Direktur
                                    </div>
                                    <p class="text-[11px] text-rose-600">Alasan:
                                        {{ $valPermohonan->alasan ?? 'Dokumen / diskon tidak sesuai' }}
                                    </p>
                                    <p class="text-[10px] text-slate-500 pt-0.5">Silakan upload kembali berkas dan masukkan
                                        nominal diskon yang sesuai.</p>
                                </div>
                            @endif

                            @if($statusPermohonan === 'menunggu')
                                <!-- Tampilan saat Menunggu Validasi Direktur -->
                                <div
                                    class="p-4 rounded-2xl bg-amber-50/60 flex flex-col items-center justify-center space-y-2 text-center">
                                    @if(($order->diskon_diajukan ?? 0) > 0)
                                        <div class="flex items-center justify-center gap-2 text-xs">
                                            <span class="text-slate-500 font-medium">Diskon Diajukan:</span>
                                            <span class="font-extrabold text-amber-700 text-sm">Rp
                                                {{ number_format($order->diskon_diajukan, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-center gap-2 text-xs">
                                        <span class="text-slate-500 font-medium">Dokumen Terunggah:</span>
                                        @if($order->bukti_po)
                                            <a href="{{ asset($order->bukti_po) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                                <span>Lihat Berkas</span>
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="pt-0.5">
                                        <span class="inline-block text-[11px] text-amber-700 font-semibold">Menunggu peninjauan
                                            & persetujuan Direktur</span>
                                    </div>
                                </div>
                            @elseif($statusPermohonan === 'disetujui')
                                <!-- Tampilan saat Disetujui Direktur -->
                                <div
                                    class="p-4 rounded-2xl bg-emerald-50/60 flex flex-col items-center justify-center space-y-2 text-center">
                                    @if(($order->diskon ?? 0) > 0)
                                        <div class="flex items-center justify-center gap-2 text-xs">
                                            <span class="text-slate-500 font-medium">Diskon Disetujui:</span>
                                            <span class="font-extrabold text-emerald-700 text-sm">Rp
                                                {{ number_format($order->diskon, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-center gap-2 text-xs">
                                        <span class="text-slate-500 font-medium">Dokumen PO:</span>
                                        @if($order->bukti_po)
                                            <a href="{{ asset($order->bukti_po) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                                <span>Lihat Berkas</span>
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="pt-0.5">
                                        <span class="inline-block text-[11px] text-emerald-700 font-bold">
                                            {{ ($order->diskon ?? 0) > 0 ? 'Diskon dan Dokumen PO disetujui Direktur, Silahkan lanjutkan ke pembayaran' : 'Dokumen P.O telah disetujui Direktur, Silahkan lanjutkan ke pembayaran' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <!-- Form Input & Upload (Belum Upload atau Ditolak) -->
                                <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-3.5">
                                    @csrf
                                    <input type="hidden" name="jenis_bukti" value="bukti_po">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                                        <!-- Kiri: Input Nominal Diskon (Opsional) -->
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-600 mb-1.5 h-4 leading-none truncate">
                                                Nominal Diskon (Rp) <span
                                                    class="text-[10px] text-slate-400 font-normal">(Opsional)</span>:
                                            </label>
                                            <div class="relative h-11">
                                                <span
                                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                                <input type="number" name="nominal_diskon" min="0" step="1000"
                                                    placeholder="0 (Opsional)"
                                                    value="{{ old('nominal_diskon', $order->diskon_diajukan ?: '') }}"
                                                    class="w-full h-11 pl-10 pr-3.5 text-xs font-bold border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50 text-slate-800 transition-all box-border">
                                            </div>
                                        </div>

                                        <!-- Kanan: File Picker (Wajib) -->
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-600 mb-1.5 h-4 leading-none truncate">
                                                Berkas Dokumen P.O (PDF/Gambar) <span class="text-rose-500 font-bold">*</span>:
                                            </label>
                                            <label
                                                class="flex items-center gap-2.5 px-3.5 h-11 rounded-xl bg-slate-50/50 hover:bg-slate-100 text-slate-700 text-xs font-bold cursor-pointer transition-colors border border-slate-200 box-border">
                                                <span
                                                    class="px-2.5 py-1 rounded-lg bg-blue-600 text-white text-[11px] font-extrabold shrink-0">Choose
                                                    File</span>
                                                <span id="file-name-permohonan"
                                                    class="text-[11px] text-slate-400 font-medium truncate">Pilih PDF /
                                                    Gambar</span>
                                                <input type="file" name="file_bukti" required accept="image/*,.pdf"
                                                    class="hidden"
                                                    onchange="document.getElementById('file-name-permohonan').textContent = this.files[0] ? this.files[0].name : 'Pilih PDF / Gambar'" />
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full mt-2 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold transition-all shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span>Ajukan Diskon & Dokumen P.O</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- 2. Upload Bukti DP (50%) -->
                        @php
                            $valDpHome = $order->getValidasi('bukti_dp');
                            $statusDpHome = $valDpHome ? $valDpHome->status_dokumen : null;
                        @endphp
                        <div class="space-y-3 flex flex-col justify-between p-5 rounded-2xl bg-white shadow-2xs">
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <h4 class="text-sm font-extrabold text-slate-900">
                                        Bukti DP 50%
                                    </h4>
                                    <span
                                        class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Rp
                                        {{ number_format($totalDp, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Unggah bukti transfer DP 50%.
                                </p>
                            </div>

                            @if($statusDpHome === 'ditolak')
                                <div class="py-2.5 px-3 rounded-xl bg-rose-50 text-rose-800 text-xs space-y-0.5 text-center">
                                    <div class="font-bold text-rose-700">
                                        Bukti DP ditolak, silakan upload kembali bukti DP.
                                    </div>
                                    @if($valDpHome && $valDpHome->alasan)
                                        <p class="text-[11px] text-rose-600">Alasan: {{ $valDpHome->alasan }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($statusDpHome === 'disetujui')
                                <div
                                    class="p-4 rounded-2xl bg-emerald-50/60 flex flex-col items-center justify-center space-y-2 text-center">
                                    <div class="flex items-center justify-center gap-2 text-xs">
                                        <span class="text-slate-500 font-medium">Dokumen Berkas:</span>
                                        @if($order->bukti_dp)
                                            <a href="{{ asset($order->bukti_dp) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                                <span>Lihat Berkas</span>
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="pt-0.5">
                                        <span class="inline-block text-[11px] text-emerald-700 font-bold">Bukti DP telah
                                            dikonfirmasi Admin</span>
                                    </div>
                                </div>
                            @else
                                <form action="{{ url('/pesanan/upload-bukti/' . $order->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="jenis_bukti" value="bukti_dp">
                                    <label
                                        class="flex items-center justify-center gap-2.5 px-3.5 h-11 rounded-xl bg-slate-100/80 hover:bg-slate-200/80 text-slate-700 text-xs font-bold cursor-pointer transition-colors border-0">
                                        <span
                                            class="px-2.5 py-1 rounded-lg bg-blue-600 text-white text-[11px] font-extrabold shrink-0">Choose
                                            File</span>
                                        <span class="text-[11px] text-slate-400 font-medium truncate">Pilih PDF / Gambar</span>
                                        <input type="file" name="file_bukti" onchange="this.form.submit()" accept="image/*,.pdf"
                                            class="hidden" />
                                    </label>

                                    @if($order->bukti_dp)
                                        <div class="pt-0.5 text-center">
                                            <a href="{{ asset($order->bukti_dp) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                                <span>Lihat Dokumen</span>
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </form>
                            @endif
                        </div>


                    </div>

                    @php
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
                    @endphp
                </div>

                <!-- Footer Navigation Bar -->
                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Tombol Kembali: Background Putih, Border Grey Halus, Teks Gelap -->
                    <a href="{{ $backUrl }}"
                        class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white border border-slate-200/90 text-slate-800 font-bold text-xs sm:text-sm hover:bg-slate-50 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali</span>
                    </a>

                    @if($isPaymentValidated)
                        <!-- Tombol Lanjut Aktif (Telah Dikonfirmasi Admin) -> Direct ke Dashboard Pelanggan -->
                        <form action="{{ url('/pesanan/lanjut-proses/' . $order->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm transition-all shadow-2xs cursor-pointer">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                                <span>Lanjut ke Detail Pesanan</span>
                            </button>
                        </form>
                    @else
                        <!-- Teks Peringatan & Tombol Non-Aktif (Tanpa Pop-up Alert) -->
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <span
                                class="text-xs font-bold text-amber-700 bg-amber-50 px-3.5 py-2.5 rounded-xl border border-amber-200/70 flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Upload Bukti DP 50% dan menunggu konfirmasi Admin</span>
                            </span>

                            <button type="button" disabled
                                class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs sm:text-sm cursor-not-allowed shadow-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                                <span>Lanjut Proses Pengerjaan</span>
                            </button>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </section>
@endsection