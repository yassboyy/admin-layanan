<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pesanan Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 30px 12px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header {
            padding: 24px 30px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #ffffff;
        }

        .header p {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
        }

        .badge-notice {
            display: inline-block;
            background-color: #0284c7;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 24px 30px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 10px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .info-label {
            color: #64748b;
            width: 35%;
        }

        .info-value {
            color: #0f172a;
            font-weight: 600;
            width: 65%;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        .order-table th {
            text-align: left;
            padding: 10px 12px;
            background-color: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .order-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: top;
        }

        .service-name {
            font-weight: 600;
            color: #0f172a;
        }

        .service-badge {
            display: inline-block;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 3px;
            background-color: #f1f5f9;
            color: #475569;
            margin-top: 3px;
        }

        .total-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            margin-top: 15px;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .total-table td {
            padding: 4px 0;
        }

        .total-label {
            color: #64748b;
        }

        .total-val {
            text-align: right;
            color: #334155;
            font-weight: 500;
        }

        .grand-total td {
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
        }

        .footer {
            padding: 18px 30px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="badge-notice">Notifikasi Sistem</div>
                <h1>Pesanan Baru Masuk</h1>
                <p>Pemberitahuan pemesanan layanan baru dari pelanggan</p>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Informasi Utama -->
                <div class="section-title">Informasi Pesanan</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Kode Pesanan</td>
                        <td class="info-value"><span style="color: #0284c7; font-weight: 700;">#{{ $order->id_pesanan }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-label">Nama Pelanggan</td>
                        <td class="info-value">{{ $order->nama_pelanggan }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">No. Telepon / WhatsApp</td>
                        <td class="info-value">{{ $order->no_hp ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Email Pelanggan</td>
                        <td class="info-value">{{ $order->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat Pelanggan</td>
                        <td class="info-value">{{ $order->alamat ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Pesanan</td>
                        <td class="info-value">{{ $order->created_at ? $order->created_at->translatedFormat('d F Y, H:i') . ' WIB' : now()->translatedFormat('d F Y, H:i') . ' WIB' }}</td>
                    </tr>
                    @if(!empty($order->catatan))
                    <tr>
                        <td class="info-label">Catatan Tambahan</td>
                        <td class="info-value" style="font-weight: normal; color: #475569; font-style: italic;">"{{ $order->catatan }}"</td>
                    </tr>
                    @endif
                </table>

                <!-- Daftar & Jenis Layanan -->
                <div class="section-title">Rincian Layanan</div>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Layanan & Jenis</th>
                            <th style="text-align: center; width: 50px;">Qty</th>
                            <th style="text-align: right; width: 110px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->pemesananLayanan as $item)
                        <tr>
                            <td>
                                <div class="service-name">{{ $item->nama_layanan }}</div>
                                @if(!empty($item->jenis_layanan))
                                <div class="service-badge">Jenis: {{ $item->jenis_layanan }}</div>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $item->jumlah }}</td>
                            <td style="text-align: right; font-weight: 600;">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8; padding: 16px;">Tidak ada rincian layanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Ringkasan Biaya -->
                <div class="total-box">
                    <table class="total-table">
                        @php
                            $subtotalLayanan = $order->pemesananLayanan->sum(function($item) {
                                return $item->harga * $item->jumlah;
                            });
                            $ppn = $subtotalLayanan * 0.11;
                            $pph = $subtotalLayanan * 0.02;
                        @endphp
                        <tr>
                            <td class="total-label">Subtotal Layanan</td>
                            <td class="total-val">Rp {{ number_format($subtotalLayanan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">PPN 11%</td>
                            <td class="total-val">+ Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">PPh Jasa 2%</td>
                            <td class="total-val" style="color: #dc2626;">- Rp {{ number_format($pph, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->diskon > 0)
                        <tr>
                            <td class="total-label">Diskon</td>
                            <td class="total-val" style="color: #dc2626;">- Rp {{ number_format($order->diskon, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td>Total Biaya</td>
                            <td style="text-align: right; color: #0f172a;">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p style="margin: 0;">Email ini dikirim secara otomatis oleh sistem aplikasi CV Tomo Teknik Mandiri.</p>
            </div>
        </div>
    </div>
</body>

</html>
