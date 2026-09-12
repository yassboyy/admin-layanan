<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | CV Tomo Teknik Mandiri</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .otp-input {
            width: 46px;
            height: 54px;
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .otp-input:focus {
            outline: none;
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .otp-input.filled {
            border-color: #3b82f6;
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        @media (max-width: 380px) {
            .otp-input {
                width: 38px;
                height: 48px;
                font-size: 18px;
            }
        }
    </style>
</head>

<body
    class="font-sans bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <!-- Logo Outside Card -->
        <div class="text-center mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CV Tomo Teknik Mandiri"
                    class="w-12 h-12 object-contain drop-shadow transition-transform duration-200 group-hover:scale-105">
                <div class="text-left">
                    <span class="text-lg font-extrabold text-slate-900 tracking-tight block leading-tight">TOMO
                        TEKNIK</span>
                    <span class="text-xs font-semibold text-blue-600 tracking-wider block">MANDIRI</span>
                </div>
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-100/50">

            <!-- Header -->
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-slate-950 tracking-tight">Verifikasi Email Anda</h2>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Kode verifikasi 6 digit telah dikirimkan ke:
                </p>
                <div
                    class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 rounded-full text-xs font-bold text-slate-700">
                    <span class="text-blue-600">✉️</span>
                    <span class="truncate max-w-[220px]">{{ $email ?? session('user.email', 'pelanggan@email.com')
                        }}</span>
                </div>
            </div>

            <!-- Flash Alert Status Success -->
            @if (session('status'))
                <div
                    class="mb-5 p-3.5 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <!-- Flash Error Status -->
            @if ($errors->any())
                <div
                    class="mb-5 p-3.5 rounded-lg bg-red-50 border border-red-100 text-red-800 text-xs flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ $errors->first('code') ?: $errors->first() }}</div>
                </div>
            @endif

            <!-- OTP Form -->
            <form id="form-verify-otp" action="{{ url('/verify-email') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="code" id="hidden-otp-code" value="">

                {{-- 6 Digit OTP Inputs --}}
                <div class="flex justify-center items-center gap-2 sm:gap-2.5">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input" id="otp-1"
                        data-index="1" autofocus autocomplete="off">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input" id="otp-2"
                        data-index="2" autocomplete="off">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input" id="otp-3"
                        data-index="3" autocomplete="off">
                    <span class="text-slate-300 font-bold text-base select-none">&ndash;</span>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input" id="otp-4"
                        data-index="4" autocomplete="off">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input" id="otp-5"
                        data-index="5" autocomplete="off">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-input" id="otp-6"
                        data-index="6" autocomplete="off">
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="btn-submit-verify"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-lg shadow-md hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-200 flex items-center justify-center gap-2">
                    <span>Verifikasi & Masuk</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Resend Section -->
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col items-center gap-2 text-center">
                <p class="text-xs text-slate-500">
                    Tidak menerima kode verifikasi?
                </p>

                <form id="form-resend-otp" action="{{ url('/verify-email/resend') }}" method="POST">
                    @csrf
                    <button type="submit" id="btn-resend-otp"
                        class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors disabled:text-slate-400 disabled:cursor-not-allowed">
                        Kirim Ulang Kode (<span id="resend-timer">60</span>s)
                    </button>
                </form>

                <!-- Back to Login -->
                <a href="{{ url('/login') }}"
                    class="text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors uppercase tracking-wider mt-3">
                    Kembali ke Login
                </a>
            </div>

        </div>

    </div>

    <!-- OTP Interactive Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenCodeInput = document.getElementById('hidden-otp-code');
            const formVerify = document.getElementById('form-verify-otp');
            const btnResend = document.getElementById('btn-resend-otp');
            const timerEl = document.getElementById('resend-timer');

            if (inputs.length > 0) {
                inputs[0].focus();
            }

            function updateHiddenCode() {
                let code = '';
                inputs.forEach(input => {
                    code += input.value;
                    if (input.value) {
                        input.classList.add('filled');
                    } else {
                        input.classList.remove('filled');
                    }
                });
                hiddenCodeInput.value = code;
            }

            inputs.forEach((input, index) => {
                input.addEventListener('input', function () {
                    const val = this.value.replace(/[^0-9]/g, '');
                    this.value = val ? val.charAt(val.length - 1) : '';

                    updateHiddenCode();

                    if (this.value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace') {
                        if (!this.value && index > 0) {
                            inputs[index - 1].focus();
                            inputs[index - 1].value = '';
                            updateHiddenCode();
                        }
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        inputs[index - 1].focus();
                    } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                // Paste Support
                input.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    const digits = pastedData.replace(/[^0-9]/g, '').slice(0, 6);

                    if (digits) {
                        digits.split('').forEach((d, i) => {
                            if (inputs[i]) {
                                inputs[i].value = d;
                            }
                        });
                        updateHiddenCode();
                        const nextIdx = Math.min(digits.length, inputs.length - 1);
                        inputs[nextIdx].focus();

                        if (digits.length === 6) {
                            formVerify.submit();
                        }
                    }
                });
            });

            formVerify.addEventListener('submit', function (e) {
                updateHiddenCode();
                if (hiddenCodeInput.value.length !== 6) {
                    e.preventDefault();
                    alert('Silakan masukkan lengkap 6 digit kode verifikasi.');
                    const firstEmpty = Array.from(inputs).find(inp => !inp.value);
                    if (firstEmpty) firstEmpty.focus();
                }
            });

            // Resend Countdown Timer (60s)
            let countdown = 60;
            btnResend.disabled = true;

            const timerInterval = setInterval(function () {
                countdown--;
                if (countdown > 0) {
                    timerEl.innerText = countdown;
                } else {
                    clearInterval(timerInterval);
                    btnResend.disabled = false;
                    btnResend.innerHTML = 'Kirim Ulang Kode';
                }
            }, 1000);
        });
    </script>
</body>

</html>