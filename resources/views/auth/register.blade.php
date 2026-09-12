<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CV Tomo Teknik Mandiri</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <span class="text-lg font-extrabold text-slate-900 tracking-tight block leading-tight">TOMO TEKNIK</span>
                    <span class="text-xs font-semibold text-blue-600 tracking-wider block">MANDIRI</span>
                </div>
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-xl shadow-slate-100/50">
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-xl font-bold text-slate-955 tracking-tight">Daftar Akun Baru</h2>
                <p class="text-xs text-slate-500 mt-1">Lengkapi formulir untuk membuat akun pelanggan</p>
            </div>

            <!-- Form -->
            <form action="{{ url('/register') }}" method="POST" class="space-y-5">
                @csrf
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Nama
                        Lengkap</label>
                    <input id="name" type="text" name="name" required autofocus placeholder="Masukkan nama lengkap anda"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email"
                        class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Email</label>
                    <input id="email" type="email" name="email" required placeholder="Email"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                </div>

                <!-- Password -->
                <div>
                    <label for="password"
                        class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required placeholder="Password"
                            class="w-full pl-4 pr-10 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="h-5 w-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation"
                        class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Konfirmasi
                        Password</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            placeholder="Password"
                            class="w-full pl-4 pr-10 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="h-5 w-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-lg shadow-md hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Daftar Akun</span>
                </button>
            </form>

            <!-- Footer / Login Link -->
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun? <a href="{{ url('/login') }}"
                        class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Masuk</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.eye-icon');
            const eyeOffIcon = button.querySelector('.eye-off-icon');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>

</html>