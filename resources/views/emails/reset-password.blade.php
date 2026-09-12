<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            max-width: 520px;
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
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .message-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .btn-reset {
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

        .security-alert {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 12px 16px;
            border-radius: 6px;
            text-align: left;
            font-size: 11px;
            color: #991b1b;
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
                <p class="header-subtitle">Cepat, Murah, dan Bertanggung Jawab</p>
            </div>

            <!-- Body -->
            <div class="body-content">
                <p class="greeting">Halo, {{ $name }}!</p>
                <p class="message-text">
                    Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda di <strong>CV Tomo Teknik
                        Mandiri</strong>.<br>
                    Silakan klik tombol di bawah ini untuk membuat password baru:
                </p>

                <!-- Action Button -->
                <div>
                    <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">
                        Atur Ulang Password Saya
                    </a>
                </div>

                <p class="expiry-note">
                    Link reset password ini hanya berlaku selama <strong>60 menit</strong>.
                </p>

                <!-- Fallback URL Link -->
                <div class="alt-link-box">
                    <strong>Jika tombol di atas tidak berfungsi, salin dan buka tautan berikut di browser:</strong><br>
                    <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
                </div>

                <!-- Security Alert -->
                <div class="security-alert">
                    ⚠️ <strong>Keamanan:</strong> Jika Anda tidak merasa meminta reset password, abaikan email ini dan
                    akun Anda akan tetap aman.
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p style="margin: 0 0 6px 0;">
                    Email ini dikirim otomatis oleh sistem CV Tomo Teknik Mandiri.
                </p>
                <p style="margin: 0; font-weight: 600;">
                    &copy; {{ date('Y') }} CV Tomo Teknik Mandiri. Seluruh Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>

</html>