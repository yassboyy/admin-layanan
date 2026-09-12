@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
    <!-- Page Header (Exact style from HomePage/pesanan.blade.php) -->
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Keranjang</h1>
        </div>
    </section>

    <!-- Cart Content Section -->
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

            @if($errors->any())
                <div
                    class="mb-8 p-4 rounded-xl bg-red-50 border border-red-100 text-red-800 text-sm flex flex-col gap-1.5 shadow-sm">
                    <div class="flex items-start gap-3 font-bold">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Terjadi Kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside pl-8 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!session()->has('user'))
                <!-- Not logged in state (Exact style from HomePage/pesanan.blade.php) -->
                <div class="text-center py-16 bg-white border border-slate-100 rounded-3xl shadow-sm max-w-xl mx-auto px-6">
                    <div
                        class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Keranjang Belanja</h2>
                    <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto leading-relaxed">
                        Silakan masuk terlebih dahulu untuk melihat daftar keranjang anda.
                    </p>
                    <a href="{{ url('/login') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                        Login Sekarang
                    </a>
                </div>
            @elseif($items->isEmpty())
                <!-- Empty cart state (Exact style from HomePage/pesanan.blade.php) -->
                <div class="text-center py-16 bg-white border border-slate-100 rounded-3xl shadow-sm max-w-xl mx-auto px-6">
                    <div
                        class="w-20 h-20 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Keranjang Kosong</h2>
                    <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto leading-relaxed">
                        Anda belum menambahkan layanan apa pun ke dalam keranjang. Temukan layanan kami dan booking sekarang.
                    </p>
                    <a href="{{ url('/layanan') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-lg shadow-blue-200 transition-all">
                        Pesan Layanan Sekarang
                    </a>
                </div>
            @else
                @php
                    $subtotal = $items->sum(function ($i) {
                        return $i->harga * $i->jumlah; });
                    $ppn = $subtotal * 0.11;
                    $pph = $subtotal * 0.02;
                    $totalAkhir = max(0, $subtotal + $ppn - $pph);
                @endphp

                <!-- Grid Sejajar Kiri & Kanan: Daftar Item (Kiri) & Rincian Biaya (Kanan) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- ================= SEBELAH KIRI: DAFTAR ITEM KERANJANG ================= -->
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

                            <!-- Card Header -->
                            <div
                                class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Daftar Item Keranjang</h3>
                                    <p class="text-xs text-slate-500">Menampilkan seluruh layanan di keranjang anda</p>
                                </div>
                                <a href="{{ url('/layanan') }}"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Layanan</span>
                                </a>
                            </div>

                            <!-- Table: Styled exactly like HomePage/pesanan.blade.php (Without Image) -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50/50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                            <th class="p-6 text-center w-16">No</th>
                                            <th class="p-6">Layanan</th>
                                            <th class="p-6">Harga Satuan</th>
                                            <th class="p-6 text-center">Jumlah</th>
                                            <th class="p-6">Subtotal</th>
                                            <th class="p-6 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-slate-100 text-slate-700">
                                        @foreach($items as $item)
                                            <tr>
                                                <!-- No -->
                                                <td class="p-6 text-center font-bold text-slate-400">
                                                    {{ $loop->iteration }}
                                                </td>

                                                <!-- Layanan (Nama & Kategori, Tanpa Gambar) -->
                                                <td class="p-6">
                                                    <div class="space-y-1">
                                                        <span class="font-bold text-slate-900 block text-sm sm:text-base">
                                                            {{ $item->layanan->nama_layanan }}
                                                        </span>
                                                        <span class="text-xs text-slate-400 block font-medium">
                                                            {{ $item->layanan->jenis_layanan }}
                                                        </span>
                                                    </div>
                                                </td>

                                                <!-- Harga Satuan -->
                                                <td class="p-6 whitespace-nowrap">
                                                    <span class="font-bold text-slate-800">
                                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                                    </span>
                                                </td>

                                                <!-- Jumlah (Stepper) -->
                                                <td class="p-6 text-center whitespace-nowrap">
                                                    <div
                                                        class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50/80 overflow-hidden shadow-2xs">
                                                        <button type="button"
                                                            onclick="submitCartQty('{{ url('/keranjang/update/' . $item->id) }}', 'decrease')"
                                                            class="px-3 py-1.5 text-slate-600 hover:bg-slate-200 hover:text-slate-900 font-extrabold transition-colors cursor-pointer text-xs"
                                                            title="Kurangi Kuantiti">−</button>

                                                        <span
                                                            class="px-3.5 py-1.5 text-xs font-black text-slate-900 bg-white border-x border-slate-200 min-w-[2.2rem] text-center">
                                                            {{ $item->jumlah }}
                                                        </span>

                                                        <button type="button"
                                                            onclick="submitCartQty('{{ url('/keranjang/update/' . $item->id) }}', 'increase')"
                                                            class="px-3 py-1.5 text-slate-600 hover:bg-slate-200 hover:text-slate-900 font-extrabold transition-colors cursor-pointer text-xs"
                                                            title="Tambah Kuantiti">+</button>
                                                    </div>
                                                </td>

                                                <!-- Subtotal -->
                                                <td class="p-6 whitespace-nowrap">
                                                    <span class="font-extrabold text-slate-900">
                                                        Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
                                                    </span>
                                                </td>

                                                <!-- Aksi (Hapus) -->
                                                <td class="p-6 text-center whitespace-nowrap">
                                                    <button type="button"
                                                        onclick="openDeleteModal('{{ url('/keranjang/hapus/' . $item->id) }}', '{{ addslashes($item->layanan->nama_layanan) }}')"
                                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-xs cursor-pointer"
                                                        title="Hapus Layanan">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ================= SEBELAH KANAN: RINCIAN BIAYA ================= -->
                    <div class="lg:col-span-4">
                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8 sticky top-28 space-y-6">

                            <!-- Card Header -->
                            <div class="border-b border-slate-100 pb-4">
                                <h3 class="text-lg font-bold text-slate-900">Rincian Biaya</h3>
                                <p class="text-xs text-slate-500">Ringkasan total pembayaran</p>
                            </div>

                            <!-- Financial Breakdown (Without DP & Without Pelunasan) -->
                            <div class="space-y-4 text-sm divide-y divide-slate-100">
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-slate-500 font-medium">Subtotal ({{ $items->sum('jumlah') }} unit)</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-slate-500 font-medium">PPN 11%</span>
                                    <span class="font-bold text-slate-900">+ Rp {{ number_format($ppn, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-slate-500 font-medium">PPh Jasa 2%</span>
                                    <span class="font-bold text-red-600">- Rp {{ number_format($pph, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-4">
                                    <span class="text-slate-700 font-bold text-base">Total Biaya</span>
                                    <span class="text-xl font-extrabold text-blue-600">Rp
                                        {{ number_format($totalAkhir, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Form Checkout -->
                            <form action="{{ url('/checkout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-xl shadow-lg shadow-blue-200 transition-all cursor-pointer">
                                    <span>Buat Pesanan Sekarang</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            @endif
        </div>
    </section>

    <!-- Separate Hidden Form for Qty Updating -->
    <form id="qty-update-form" action="" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="action" id="qty-action" value="">
    </form>

    <!-- Custom Centered Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="delete-modal-backdrop"></div>

        <div
            class="relative z-10 w-full max-w-md rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-100 transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900" id="modal-title">Hapus Layanan</h3>
                <button type="button" onclick="closeDeleteModal()"
                    class="text-slate-400 hover:text-slate-600 transition-colors p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer -mr-1"
                    aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="py-5">
                <p class="text-sm text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus layanan <span id="delete-item-name"
                        class="font-bold text-slate-900"></span> dari keranjang belanja?
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-50">
                <button type="button" onclick="closeDeleteModal()"
                    class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer">
                    <span>Batal</span>
                </button>
                <form id="delete-form" action="" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-200 transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Ya, Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function submitCartQty(actionUrl, actionType) {
            const form = document.getElementById('qty-update-form');
            form.action = actionUrl;
            document.getElementById('qty-action').value = actionType;
            form.submit();
        }

        function openDeleteModal(actionUrl, itemName) {
            document.getElementById('delete-form').action = actionUrl;
            document.getElementById('delete-item-name').textContent = itemName;
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const backdrop = document.getElementById('delete-modal-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', closeDeleteModal);
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });
        });
    </script>
@endsection