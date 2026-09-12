<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Anda Telah Dibuat di SIADO</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }

        .container {
            max-width: 540px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .header {
            background-color: #2563eb;
            padding: 28px 24px;
            text-align: center;
            color: #ffffff;
        }

        .logo-img {
            display: inline-block;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background-color: #ffffff;
            padding: 2px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
        }

        .header-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            margin: 4px 0 0 0;
            font-size: 12px;
            opacity: 0.9;
        }

        .body-content {
            padding: 32px 28px;
            text-align: center;
        }

        .greeting {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .info-card {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }

        .info-card-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e40af;
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-card-text {
            font-size: 12px;
            color: #1e3a8a;
            line-height: 1.6;
            margin: 0;
        }

        .message-text {
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 24px;
            text-align: left;
        }

        .btn-set-password {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 10px;
            margin: 8px 0 24px 0;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .expiry-note {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        .alt-link-box {
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 11px;
            color: #64748b;
            word-break: break-all;
            margin-bottom: 20px;
            text-align: left;
        }

        .security-notice {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: left;
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <img src="{{ url('images/logo.png') }}" alt="Logo CV Tomo Teknik Mandiri" class="logo-img" width="54" height="54">
                <h1 class="header-title">CV TOMO TEKNIK MANDIRI</h1>
                <p class="header-subtitle">SIADO - Sistem Informasi Administrasi Layanan Operasional</p>
            </div>

            <!-- Body -->
            <div class="body-content">
                <p class="greeting">Halo, {{ $name }}! 👋</p>

                <div class="info-card">
                    <div class="info-card-title">
                        🎉 Akun Anda Berhasil Dibuat Otomatis
                    </div>
                    <p class="info-card-text">
                        Selamat! Akun Anda telah berhasil terdaftar secara otomatis di sistem <strong>SIADO</strong> karena Anda baru saja melakukan pemesanan layanan teknik kami{{ !empty($serviceName) ? ' (' . $serviceName . ')' : '' }}.
                    </p>
                </div>

                <p class="message-text">
                    Demi keamanan data dan transaksi Anda, sistem telah meng-generate kata sandi acak sementara. Agar Anda dapat kembali masuk (login) ke SIADO di kemudian hari dan memantau status pesanan Anda, silakan buat kata sandi pribadi Anda melalui tombol di bawah ini:
                </p>

                <!-- Action Button -->
                <div>
                    <a href="{{ $resetUrl }}" class="btn-set-password" target="_blank">
                        Atur Password Akun Saya &rarr;
                    </a>
                </div>

                <p class="expiry-note">
                    Tautan ini berlaku selama <strong>60 menit</strong> sejak email ini dikirimkan.
                </p>

                <!-- Fallback URL Link -->
                <div class="alt-link-box">
                    <strong>Jika tombol di atas tidak dapat diklik, silakan salin dan buka tautan berikut di peramban (browser) Anda:</strong><br>
                    <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
                </div>

                <!-- Security Notice -->
                <div class="security-notice">
                    🔒 <strong>Detail Akun Terdaftar:</strong><br>
                    • Email Login: <strong>{{ $email }}</strong><br>
                    • Nama: <strong>{{ $name }}</strong><br>
                    Setelah mengatur password baru, Anda dapat login kapan saja melalui halaman masuk SIADO.
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p style="margin: 0 0 6px 0;">
                    Email pemberitahuan ini dikirim otomatis oleh sistem SIADO - CV Tomo Teknik Mandiri.
                </p>
                <p style="margin: 0; font-weight: 600;">
                    &copy; {{ date('Y') }} CV Tomo Teknik Mandiri. Seluruh Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
