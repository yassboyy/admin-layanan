<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - CV Tomo Teknik Mandiri</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 15px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .header {
            padding: 32px 36px 24px 36px;
            border-bottom: 1px solid #f1f5f9;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .company-tagline {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        .content {
            padding: 32px 36px;
        }

        .greeting {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .paragraph {
            font-size: 14px;
            color: #475569;
            margin-bottom: 24px;
        }

        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .meta-label {
            color: #64748b;
            width: 38%;
        }

        .meta-value {
            color: #0f172a;
            font-weight: 600;
            width: 62%;
        }

        .section-heading {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 28px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .data-table th {
            text-align: left;
            padding: 10px 12px;
            background-color: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .data-table .service-name {
            font-weight: 600;
            color: #0f172a;
        }

        .data-table .service-type {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 12px;
            margin-bottom: 24px;
        }

        .summary-table td {
            padding: 5px 12px;
        }

        .summary-label {
            text-align: right;
            color: #64748b;
        }

        .summary-value {
            text-align: right;
            color: #0f172a;
            font-weight: 600;
            width: 32%;
        }

        .summary-total td {
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .summary-dp td {
            padding-top: 8px;
            padding-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #1e40af;
            background-color: #f8fafc;
        }

        .bank-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .bank-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .bank-table td {
            padding: 6px 0;
            vertical-align: middle;
        }

        .bank-tag {
            font-weight: 700;
            color: #0f172a;
            width: 30%;
        }

        .bank-acc {
            font-family: 'Consolas', 'Courier New', Courier, monospace;
            font-weight: 700;
            color: #1e40af;
            width: 40%;
        }

        .bank-owner {
            color: #64748b;
            font-size: 12px;
            text-align: right;
            width: 30%;
        }

        .instructions {
            font-size: 13px;
            color: #475569;
            margin-bottom: 28px;
        }

        .instructions ol {
            margin: 8px 0 0 0;
            padding-left: 18px;
        }

        .instructions li {
            margin-bottom: 6px;
        }

        .button-wrap {
            text-align: center;
            margin: 32px 0 24px 0;
        }

        .btn-primary {
            display: inline-block;
            background-color: #1e3a8a;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 6px;
            letter-spacing: 0.3px;
        }

        .closing {
            font-size: 13px;
            color: #475569;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .footer {
            background-color: #f8fafc;
            padding: 24px 36px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        .footer a {
            color: #64748b;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    @php
        $subtotalJasa = $order->pemesananLayanan->sum(function ($i) {
            return $i->harga * $i->jumlah;
        });
        $diskon = $order->diskon ?? 0;
        $ppnJasa = $subtotalJasa * 0.11;
        $pphJasa = $subtotalJasa * 0.02;
        $totalAkhir = max(0, ($subtotalJasa + $ppnJasa - $pphJasa) - $diskon);
        $totalDp = $totalAkhir * 0.50;
        $sisaPelunasan = $totalAkhir - $totalDp;
    @endphp

    <div class="wrapper">
        <div class="container">
            <!-- Header Formal -->
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="logo-cell" style="width: 48px;">
                            <img src="{{ url('images/logo.png') }}" alt="Logo" width="42" height="42"
                                style="display: block; border-radius: 4px;">
                        </td>
                        <td style="padding-left: 12px; vertical-align: middle;">
                            <p class="company-name">CV TOMO TEKNIK MANDIRI</p>
                            <p class="company-tagline">Layanan Pengadaan & Perbaikan Peralatan Teknik</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Isi Surat Formal -->
            <div class="content">
                <p class="greeting">Yth. Bapak/Ibu {{ $order->nama_pelanggan }},</p>
                <p class="paragraph">
                    Terima kasih telah mempercayakan kebutuhan Anda kepada CV Tomo Teknik Mandiri. Pesanan Anda telah
                    berhasil tercatat dalam sistem kami dengan nomor pemesanan
                    <strong>#{{ $order->id_pesanan }}</strong>.
                </p>

                <!-- Informasi Pesanan -->
                <div class="meta-box">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Nomor Pesanan</td>
                            <td class="meta-value">#{{ $order->id_pesanan }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tanggal Pemesanan</td>
                            <td class="meta-value">
                                {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : date('d/m/Y H:i') }}
                                WIB</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Status Saat Ini</td>
                            <td class="meta-value" style="color: #d97706;">Proses Pemesanan</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Alamat Pengerjaan</td>
                            <td class="meta-value">{{ $order->alamat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Kontak / No. Telp</td>
                            <td class="meta-value">{{ $order->no_hp ?: '-' }}</td>
                        </tr>
                        @if($order->catatan)
                            <tr>
                                <td class="meta-label">Catatan Tambahan</td>
                                <td class="meta-value" style="font-weight: normal; color: #475569;">{{ $order->catatan }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>

                <!-- Rincian Layanan -->
                <div class="section-heading">Rincian Layanan</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th style="text-align: center; width: 15%;">Qty</th>
                            <th style="text-align: right; width: 25%;">Harga Satuan</th>
                            <th style="text-align: right; width: 25%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->pemesananLayanan as $item)
                            <tr>
                                <td>
                                    <div class="service-name">{{ $item->nama_layanan }}</div>
                                    <div class="service-type">{{ str_replace('_', ' ', $item->jenis_layanan) }}</div>
                                </td>
                                <td style="text-align: center;">{{ $item->jumlah }}</td>
                                <td style="text-align: right; color: #64748b;">Rp
                                    {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 600; color: #0f172a;">Rp
                                    {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Ringkasan Biaya -->
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Subtotal Jasa</td>
                        <td class="summary-value">Rp {{ number_format($subtotalJasa, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">PPN 11%</td>
                        <td class="summary-value">+ Rp {{ number_format($ppnJasa, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">PPh Jasa 2%</td>
                        <td class="summary-value" style="color: #dc2626;">- Rp
                            {{ number_format($pphJasa, 0, ',', '.') }}</td>
                    </tr>
                    @if($diskon > 0)
                        <tr>
                            <td class="summary-label">Diskon</td>
                            <td class="summary-value" style="color: #dc2626;">- Rp {{ number_format($diskon, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                    <tr class="summary-total">
                        <td class="summary-label" style="font-weight: 700; color: #0f172a;">Total Biaya</td>
                        <td class="summary-value">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="summary-dp">
                        <td class="summary-label" style="color: #1e40af; font-weight: 700;">DP 50% yang Harus Dibayar
                        </td>
                        <td class="summary-value" style="color: #1e40af;">Rp {{ number_format($totalDp, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>

                <!-- Rekening Pembayaran -->
                <div class="section-heading">Informasi Pembayaran Transfer</div>
                <div class="bank-box">
                    <table class="bank-table">
                        <tr>
                            <td class="bank-tag">Bank BCA</td>
                            <td class="bank-acc">1171121162</td>
                            <td class="bank-owner">a.n. SUTOMO</td>
                        </tr>
                        <tr>
                            <td class="bank-tag">Bank BNI</td>
                            <td class="bank-acc">0536364196</td>
                            <td class="bank-owner">a.n. SUTOMO</td>
                        </tr>
                        <tr>
                            <td class="bank-tag">Bank BRI</td>
                            <td class="bank-acc">569901012636530</td>
                            <td class="bank-owner">a.n. SUTOMO</td>
                        </tr>
                        <tr>
                            <td class="bank-tag">Bank Mandiri</td>
                            <td class="bank-acc">1140026428576</td>
                            <td class="bank-owner">a.n. SUTOMO</td>
                        </tr>
                    </table>
                </div>

                <!-- Petunjuk Selanjutnya -->
                <div class="instructions">
                    <strong>Petunjuk Pembayaran & Validasi:</strong>
                    <ol>
                        <li>Lakukan pembayaran DP 50% sebesar <strong>Rp
                                {{ number_format($totalDp, 0, ',', '.') }}</strong> ke salah satu rekening di atas.</li>
                        <li>Unggah berkas bukti pembayaran DP melalui menu <strong>Detail Pesanan</strong> di akun Anda.
                        </li>
                        <li>Tim Admin kami akan mengonfirmasi pembayaran Anda untuk melanjutkan ke proses pengerjaan.
                        </li>
                    </ol>
                </div>

                <!-- Tombol CTA -->
                <div class="button-wrap">
                    <a href="{{ url('/detail-pesanan/' . $order->id) }}" class="btn-primary">
                        Buka Halaman Detail Pesanan
                    </a>
                </div>

                <!-- Penutup Formal -->
                <div class="closing">
                    <p style="margin: 0 0 4px 0;">Hormat kami,</p>
                    <p style="margin: 0; font-weight: 700; color: #0f172a;">CV TOMO TEKNIK MANDIRI</p>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">Tim Layanan Pelanggan & Administrasi
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p style="margin: 0 0 6px 0;">
                    Email ini dikirimkan secara otomatis oleh sistem kami. Jika ada pertanyaan, hubungi <a
                        href="mailto:teknikmandiricvtomo@gmail.com">teknikmandiricvtomo@gmail.com</a>.
                </p>
                <p style="margin: 0;">
                    &copy; {{ date('Y') }} CV Tomo Teknik Mandiri. Seluruh hak cipta dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>

</html>