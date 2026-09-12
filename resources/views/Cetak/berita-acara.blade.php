<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Serah Terima - {{ $order->id_pesanan }}</title>
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
            <span>Cetak Berita Acara</span>
        </button>
    </div>

    {{-- Printable Paper --}}
    <div class="print-page max-w-4xl mx-auto bg-white p-10 sm:p-14 shadow-lg border border-slate-200">

        {{-- Kop Surat Perusahaan --}}
        @include('Cetak.kop-surat')

        {{-- Judul Berita Acara --}}
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold uppercase underline tracking-wider">BERITA ACARA SERAH TERIMA PEKERJAAN</h1>
            <p class="text-xs font-medium tracking-wide mt-1">{{ $noBeritaAcara }}</p>
        </div>

        {{-- Paragraf Pembuka Tanggal --}}
        <p class="text-xs sm:text-[13px] leading-relaxed text-justify mb-5">
            Pada hari ini, <span class="font-bold">{{ $namaHari }}</span>, tanggal <span
                class="font-bold">{{ $terbilangTanggal }}</span> bulan <span class="font-bold">{{ $namaBulan }}</span>
            tahun <span class="font-bold">{{ $terbilangTahun }}</span> ({{ $tglNumeric }}), bertempat di
            {{ $order->alamat ?: 'Metro, Lampung' }},
            telah dilaksanakan serah terima penyelesaian pekerjaan oleh dan antara pihak-pihak di bawah ini:
        </p>

        {{-- Identitas Para Pihak --}}
        <div class="space-y-4 mb-6 text-xs sm:text-[13px]">
            {{-- 1. Pihak Pertama --}}
            <div>
                <h3 class="font-bold mb-1.5">1. PIHAK PERTAMA (Penyedia Jasa):</h3>
                <table class="w-full text-left ml-4">
                    <tr>
                        <td class="w-48 py-0.5 align-top">Nama Perusahaan</td>
                        <td class="w-4 py-0.5 align-top">:</td>
                        <td class="py-0.5 font-bold align-top">CV. TOMO TEKNIK MANDIRI</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top">Nama Penanggung Jawab</td>
                        <td class="py-0.5 align-top">:</td>
                        <td class="py-0.5 align-top">Sutomo (Direktur)</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top">Alamat Kantor</td>
                        <td class="py-0.5 align-top">:</td>
                        <td class="py-0.5 align-top">Jl. Madura No. 17, Kelurahan Hadimulyo Barat, Metro Pusat, Kota
                            Metro, Lampung</td>
                    </tr>
                </table>
            </div>

            {{-- 2. Pihak Kedua --}}
            <div>
                <h3 class="font-bold mb-1.5">2. PIHAK KEDUA (Pelanggan / Pengguna Jasa):</h3>
                <table class="w-full text-left ml-4">
                    <tr>
                        <td class="w-48 py-0.5 align-top">Nama Pelanggan</td>
                        <td class="w-4 py-0.5 align-top">:</td>
                        <td class="py-0.5 font-bold align-top">{{ strtoupper($order->nama_pelanggan) }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top">No. Kontak / HP</td>
                        <td class="py-0.5 align-top">:</td>
                        <td class="py-0.5 align-top">{{ $order->no_hp ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top">Email</td>
                        <td class="py-0.5 align-top">:</td>
                        <td class="py-0.5 align-top">{{ $order->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top">Alamat</td>
                        <td class="py-0.5 align-top">:</td>
                        <td class="py-0.5 align-top">{{ $order->alamat ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Sub-header Tabel --}}
        <p class="text-xs sm:text-[13px] font-bold mb-2.5">
            PIHAK PERTAMA telah menyelesaikan seluruh ruang lingkup pekerjaan dengan rincian sebagai berikut:
        </p>

        {{-- Tabel Layanan Sesuai Format Surat Jalan --}}
        <div class="mb-6">
            <table class="w-full text-left text-xs sm:text-[13px] border border-black border-collapse">
                <thead>
                    <tr class="border-b border-black">
                        <th class="border-r border-black p-2 text-center w-12 font-bold">No</th>
                        <th class="border-r border-black p-2 font-bold text-left w-44">Jenis Layanan</th>
                        <th class="border-r border-black p-2 font-bold text-left">Nama Layanan</th>
                        <th class="border-r border-black p-2 text-center w-16 font-bold">Qty</th>
                        <th class="p-2 text-center w-24 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->pemesananLayanan as $idx => $item)
                        <tr class="border-b border-black">
                            <td class="border-r border-black p-2 text-center">{{ $idx + 1 }}</td>
                            <td class="border-r border-black p-2 text-left capitalize">
                                {{ $item->jenis_layanan ?: ($item->layanan->kategori ?? 'Jasa') }}
                            </td>
                            <td class="border-r border-black p-2 text-left font-bold">{{ $item->nama_layanan }}</td>
                            <td class="border-r border-black p-2 text-center">{{ $item->jumlah }}</td>
                            <td class="p-2 text-center">Selesai</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Butir-butir Pernyataan Serah Terima --}}
        <div class="space-y-2 text-xs sm:text-[13px] leading-relaxed mb-6">
            <div class="flex items-start gap-2 text-justify">
                <span class="font-bold shrink-0">1.</span>
                <div>
                    <span class="font-bold">Pemeriksaan Hasil Kerja:</span> PIHAK KEDUA telah melakukan pemeriksaan,
                    verifikasi fisik, serta pengecekan kelengkapan dokumen pengerjaan yang diserahkan oleh PIHAK PERTAMA
                    dan menyatakan bahwa hasil pekerjaan telah diterima dalam keadaan <span class="font-bold">BAIK,
                        LENGKAP, DAN SESUAI SPESIFIKASI</span>.
                </div>
            </div>
            <div class="flex items-start gap-2 text-justify">
                <span class="font-bold shrink-0">2.</span>
                <div>
                    <span class="font-bold">Peralihan Tanggung Jawab:</span> Sejak ditandatanganinya Berita Acara ini,
                    hasil pekerjaan resmi diserahterimakan kepada PIHAK KEDUA.
                </div>
            </div>
            <div class="flex items-start gap-2 text-justify">
                <span class="font-bold shrink-0">3.</span>
                <div>
                    <span class="font-bold">Administrasi & Pelunasan:</span> Berita Acara Serah Terima (BAST) ini
                    menjadi dasar resmi penerbitan Invoice Final dan proses administrasi pelunasan pembayaran bagi PIHAK
                    KEDUA sesuai dengan ketentuan sistem pemesanan.
                </div>
            </div>
        </div>

        {{-- Kalimat Penutup --}}
        <p class="text-xs sm:text-[13px] leading-relaxed text-justify mb-8">
            Demikian Berita Acara Serah Terima Pekerjaan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana
            mestinya.
        </p>

        {{-- Tanda Tangan 2 Kolom Sesuai Draft --}}
        <div class="grid grid-cols-2 gap-8 text-center text-xs sm:text-[13px]">
            {{-- Kiri: Pihak Kedua (Pelanggan) --}}
            <div>
                <p class="font-bold">PIHAK KEDUA (Pelanggan)</p>
                <p class="font-bold">{{ strtoupper($order->nama_pelanggan) }}</p>
                <div class="h-28"></div>
                <p class="font-bold">( .................................................... )</p>
                <p class="text-slate-700 mt-1">Pelanggan</p>
            </div>

            {{-- Kanan: Pihak Pertama (CV. Tomo Teknik Mandiri) --}}
            <div>
                <p class="font-bold">PIHAK PERTAMA</p>
                <p class="font-bold">CV. TOMO TEKNIK MANDIRI</p>
                <div class="h-28"></div>
                <p class="font-bold underline">Sutomo</p>
                <p class="text-slate-700 mt-1">Direktur</p>
            </div>
        </div>

    </div>

</body>

</html>