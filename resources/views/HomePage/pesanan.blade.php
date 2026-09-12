@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Pesanan Saya</h1>
        </div>
    </section>

    <!-- Orders History Section -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div
                    class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <span class="font-bold">Sukses:</span> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(!session()->has('user'))
                <!-- Not logged in state -->
                <div class="text-center py-16 bg-white border border-slate-100 rounded-3xl shadow-sm max-w-xl mx-auto px-6">
                    <div
                        class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Pesanan</h2>
                    <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto leading-relaxed">
                        Silakan masuk terlebih dahulu untuk melihat daftar pemesanan anda.
                    </p>
                    <a href="{{ url('/login') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                        Login Sekarang
                    </a>
                </div>
            @elseif($orders->isEmpty())
                <!-- Empty orders state -->
                <div class="text-center py-16 bg-white border border-slate-100 rounded-3xl shadow-sm max-w-xl mx-auto px-6">
                    <div
                        class="w-20 h-20 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Belum Ada Pesanan</h2>
                    <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto leading-relaxed">
                        Anda belum pernah melakukan booking layanan apa pun. Temukan layanan kami dan buat pesanan
                        pertama anda.
                    </p>
                    <a href="{{ url('/layanan') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                        Pesan Layanan Sekarang
                    </a>
                </div>
            @else
                <!-- Orders Table / List Card -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div
                        class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Daftar Pesanan Saya</h3>
                            <p class="text-xs text-slate-500">Menampilkan seluruh pesanan anda</p>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50/50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                    <th class="p-6">ID Pesanan / Tanggal</th>
                                    <th class="p-6">Layanan</th>
                                    <th class="p-6">Pelanggan</th>
                                    <th class="p-6">Nomor Handphone</th>
                                    <th class="p-6">Total Harga</th>
                                    <th class="p-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100 text-slate-700">
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="p-6 align-middle">
                                            <span class="font-extrabold text-slate-900 block">{{ $order->id_pesanan }}</span>
                                            <span class="text-xs text-slate-400 mt-1 block">
                                                {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                            </span>
                                        </td>
                                        <td class="p-6 align-middle">
                                            <div class="space-y-1.5 min-w-[200px] max-w-sm">
                                                @foreach($order->pemesananLayanan as $pLayanan)
                                                    <div class="flex items-start gap-2">
                                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                                                        <div class="leading-tight">
                                                            <span class="font-bold text-slate-800 text-xs sm:text-sm">
                                                                {{ $pLayanan->nama_layanan }}
                                                            </span>
                                                            @if($pLayanan->jumlah > 1)
                                                                <span class="text-[11px] font-extrabold text-blue-600 ml-1">
                                                                    (×{{ $pLayanan->jumlah }})
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="p-6 align-middle">
                                            <span class="font-bold text-slate-900 block">{{ $order->nama_pelanggan }}</span>
                                        </td>
                                        <td class="p-6 align-middle">
                                            <span class="font-bold text-slate-900 block">{{ $order->no_hp }}</span>
                                        </td>
                                        <td class="p-6 align-middle">
                                            <span class="font-extrabold text-slate-900">Rp
                                                {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="p-6 text-center align-middle">
                                            <a href="{{ url('/detail-pesanan/' . $order->id) }}"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection