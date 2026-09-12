<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->id_pesanan }}</title>
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
            <span>Cetak Invoice</span>
        </button>
    </div>

    {{-- Printable Paper --}}
    <div class="print-page max-w-4xl mx-auto bg-white p-10 sm:p-14 shadow-lg border border-slate-200">

        {{-- Kop Surat Perusahaan --}}
        @include('Cetak.kop-surat')

        {{-- Judul Invoice & Nomor --}}
        <div class="text-center mb-8">
            <h1 class="text-xl font-bold uppercase underline tracking-wider">INVOICE</h1>
            <p class="text-xs font-medium tracking-wide mt-1">{{ $noInvoice }}</p>
        </div>

        {{-- Metadata 2 Kolom (Pelanggan di Kiri, Pesanan di Ujung Kanan) --}}
        <div class="flex justify-between items-start text-xs sm:text-[13px] mb-6">
            {{-- Kiri: Data Pelanggan --}}
            <table class="text-left">
                <tr>
                    <td class="w-24 py-0.5 align-top">Kepada Yth.</td>
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

            {{-- Kanan: No Pesanan & Tanggal Terbit Sampai Ujung Kanan --}}
            <table class="text-left">
                <tr>
                    <td class="w-28 py-0.5 align-top">No. Pesanan</td>
                    <td class="w-3 py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top whitespace-nowrap">{{ $order->id_pesanan }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-top">Tanggal Terbit</td>
                    <td class="py-0.5 align-top">:</td>
                    <td class="py-0.5 align-top whitespace-nowrap">{{ $tglTerbit }}</td>
                </tr>
            </table>
        </div>

        {{-- Tabel Rincian Invoice Sesuai Draft --}}
        <div class="mb-6">
            <table class="w-full text-left text-xs sm:text-[13px] border border-black border-collapse">
                <thead>
                    <tr class="border-b border-black">
                        <th class="border-r border-black p-2 text-center w-12 font-bold">No.</th>
                        <th class="border-r border-black p-2 font-bold text-left w-36">Jenis Layanan</th>
                        <th class="border-r border-black p-2 font-bold text-left">Nama Layanan</th>
                        <th class="border-r border-black p-2 text-center w-16 font-bold">Qty</th>
                        <th class="border-r border-black p-2 text-right w-32 font-bold">Harga</th>
                        <th class="p-2 text-right w-36 font-bold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->pemesananLayanan as $idx => $item)
                        <tr class="border-b border-black">
                            <td class="border-r border-black p-2 text-center">{{ $idx + 1 }}</td>
                            <td class="border-r border-black p-2 text-left capitalize">{{ $item->jenis_layanan ?: ($item->layanan->kategori ?? 'Jasa') }}</td>
                            <td class="border-r border-black p-2 font-bold text-left">{{ $item->nama_layanan }}</td>
                            <td class="border-r border-black p-2 text-center">{{ $item->jumlah }}</td>
                            <td class="border-r border-black p-2 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="p-2 text-right">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- Baris Kalkulasi & Ringkasan (Tanpa sekat kosong di kiri) --}}
                    <tr class="border-b border-black">
                        <td colspan="5" class="border-r border-black p-2 text-right font-medium">Subtotal</td>
                        <td class="p-2 text-right font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td colspan="5" class="border-r border-black p-2 text-right italic">Discount</td>
                        <td class="p-2 text-right italic">{{ $diskon > 0 ? '- Rp ' . number_format($diskon, 0, ',', '.') : 'Rp 0' }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td colspan="5" class="border-r border-black p-2 text-right font-medium">PPN 11%</td>
                        <td class="p-2 text-right font-medium">+ Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td colspan="5" class="border-r border-black p-2 text-right font-medium">PPh 2%</td>
                        <td class="p-2 text-right font-medium">- Rp {{ number_format($pph, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td colspan="5" class="border-r border-black p-2 text-right font-bold">Total DP 50%</td>
                        <td class="p-2 text-right font-bold">Rp {{ number_format($dp, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-b border-black">
                        <td colspan="5" class="border-r border-black p-2 text-right font-bold">Total Pelunasan</td>
                        <td class="p-2 text-right font-bold">Rp {{ number_format($pelunasan, 0, ',', '.') }}</td>
                    </tr>

                    {{-- Baris Terbilang --}}
                    <tr>
                        <td colspan="6" class="p-2.5 text-center italic font-bold">
                            Terbilang: "{{ $terbilangTotal }} Rupiah"
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Bawah Tabel: Informasi Rekening & Tanda Tangan --}}
        <div class="grid grid-cols-2 gap-6 text-xs sm:text-[13px] pt-4">
            {{-- Kiri: Informasi Rekening Pembayaran --}}
            <div class="space-y-1">
                <p class="font-bold">Informasi Rekening Pembayaran:</p>
                <div class="space-y-0.5 text-slate-900 ml-1">
                    <p>- BCA : 1171121162 (a.n. SUTOMO)</p>
                    <p>- BNI : 0536364196 (a.n. SUTOMO)</p>
                    <p>- BRI : 009801002345532 (a.n. SUTOMO)</p>
                    <p>- Mandiri : 1140021345678 (a.n. SUTOMO)</p>
                </div>
            </div>

            {{-- Kanan: Tanda Tangan Direktur --}}
            <div class="text-center">
                <p>Metro, {{ $tglTerbit }}</p>
                <p class="font-bold mt-0.5">CV Tomo Teknik Mandiri</p>
                <div class="h-24 sm:h-28"></div>
                <p class="font-bold underline">Sutomo</p>
                <p class="text-slate-700">Direktur</p>
            </div>
        </div>

    </div>

</body>

</html>
