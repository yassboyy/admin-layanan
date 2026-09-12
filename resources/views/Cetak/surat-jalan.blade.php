<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $order->id_pesanan }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.15;
        }

        p, table, td, th, div, span {
            line-height: 1.15;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
            }

            .print-page {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 20px 40px !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen p-4 sm:p-8">

    {{-- Action Bar (Hidden on Print) --}}
    <div class="max-w-4xl mx-auto mb-4 no-print flex items-center justify-between font-sans">
        <button onclick="window.history.back()"
            class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-300 transition-all flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali</span>
        </button>
        <button onclick="window.print()"
            class="px-5 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 shadow-md transition-all flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Surat Jalan</span>
        </button>
    </div>

    {{-- Printable Paper --}}
    <div class="print-page max-w-4xl mx-auto bg-white p-10 sm:p-14 shadow-lg border border-slate-200">
        
        {{-- Kop Surat Perusahaan --}}
        @include('Cetak.kop-surat')

        {{-- Judul Surat Jalan --}}
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold uppercase underline tracking-wider">SURAT JALAN</h1>
            <p class="text-xs font-medium tracking-wide mt-1">{{ $noSuratJalan }}</p>
        </div>

        {{-- Metadata 2 Kolom (Pelanggan di Kiri, Pesanan di Ujung Kanan) --}}
        <div class="flex justify-between items-start text-xs sm:text-[13px] mb-6">
            {{-- Kolom Kiri: Pelanggan --}}
            <table class="text-left">
                <tr>
                    <td class="w-28 py-0.5 align-top">Kepada Yth.</td>
                    <td class="w-3 py-0.5 align-top">:</td>
                    <td class="py-0.5 font-bold align-top">{{ $order->nama_pelanggan }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-top">Alamat</td>
                    <td class="py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top">{{ $order->alamat ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-top">No. HP</td>
                    <td class="py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top">{{ $order->no_hp ?: '-' }}</td>
                </tr>
            </table>

            {{-- Kolom Kanan: Pesanan & Tanggal Sampai Ujung Kanan --}}
            <table class="text-left">
                <tr>
                    <td class="w-28 py-0.5 align-top">No. Pesanan</td>
                    <td class="w-3 py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top whitespace-nowrap">{{ $order->id_pesanan }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-top">Tanggal</td>
                    <td class="py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top whitespace-nowrap">
                        @php
                            $tglSj = $order->created_at ? $order->created_at : now();
                            $bulanIndo = [
                                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ];
                        @endphp
                        {{ $tglSj->format('d') }} {{ $bulanIndo[(int)$tglSj->format('m')] }} {{ $tglSj->format('Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="py-0.5 align-top">Untuk Pekerjaan</td>
                    <td class="py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top">{{ $namaLayananList }}</td>
                </tr>
            </table>
        </div>

        {{-- Paragraf Pengantar / Penjelasan (Di Bawah Data Pelanggan) --}}
        <p class="text-xs sm:text-[13px] leading-relaxed text-justify mb-6">
            Sehubungan pengantaran barang ke <span class="font-bold">{{ $order->nama_pelanggan }}</span> yang akan membawa barang milik <span class="font-bold">{{ $order->nama_pelanggan }}</span>, maka kami CV. Tomo Teknik Mandiri mengajukan permohonan izin masuk ke area <span class="font-bold">{{ $order->nama_pelanggan }}</span> untuk dapat mengantarkan barang dengan rincian sebagai berikut:
        </p>

        {{-- Tabel Daftar Barang / Alat --}}
        <div class="mb-6">
            <table class="w-full text-left text-xs sm:text-[13px] border border-black border-collapse">
                <thead>
                    <tr class="border-b border-black">
                        <th class="border-r border-black p-2 text-center w-12 font-bold">No</th>
                        <th class="border-r border-black p-2 font-bold text-center">Nama Barang / Alat</th>
                        <th class="border-r border-black p-2 text-center w-24 font-bold">Jumlah</th>
                        <th class="p-2 text-center w-36 font-bold">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alatList as $idx => $item)
                        <tr class="border-b border-black">
                            <td class="border-r border-black p-2 text-center">{{ $idx + 1 }}</td>
                            <td class="border-r border-black p-2 font-normal">{{ $item['nama'] ?? '-' }}</td>
                            <td class="border-r border-black p-2 text-center">{{ $item['jumlah'] ?? 1 }}</td>
                            <td class="p-2 text-center">{{ $item['keterangan'] ?? 'Baik' }}</td>
                        </tr>
                    @empty
                        <tr class="border-b border-black">
                            <td class="border-r border-black p-2 text-center">1</td>
                            <td class="border-r border-black p-2">{{ $namaLayananList }}</td>
                            <td class="border-r border-black p-2 text-center">1</td>
                            <td class="p-2 text-center">Baik</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Catatan --}}
        <div class="text-xs sm:text-[13px] mb-12">
            <p><span class="font-bold">Catatan:</span> {{ $catatanSj ?: ($order->catatan ?: '-') }}</p>
        </div>

        {{-- Tanda Tangan 3 Kolom Sesuai Permintaan --}}
        <div class="grid grid-cols-3 gap-4 text-center text-xs sm:text-[13px]">
            {{-- Kiri: Direktur (Sutomo) --}}
            <div>
                <p>Hormat Kami,</p>
                <p class="font-bold">Direktur</p>
                <div class="h-24 sm:h-28"></div>
                <p class="font-bold underline">Sutomo</p>
            </div>

            {{-- Tengah: Manager Teknisi --}}
            <div>
                <p>Yang Membawa,</p>
                <p class="font-bold">Manager Teknisi</p>
                <div class="h-24 sm:h-28"></div>
                <p>( .................................... )</p>
            </div>

            {{-- Kanan: Pelanggan --}}
            <div>
                <p>Yang Menerima,</p>
                <p class="font-bold">Pelanggan</p>
                <div class="h-24 sm:h-28"></div>
                <p class="font-bold">( {{ $order->nama_pelanggan }} )</p>
            </div>
        </div>

    </div>

</body>

</html>
