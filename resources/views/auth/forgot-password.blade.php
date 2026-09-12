<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | CV Tomo Teknik Mandiri</title>
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
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-slate-950 tracking-tight">Lupa Password?</h2>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang kata sandi.
                </p>
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
                    <div>{{ $errors->first('email') ?: $errors->first() }}</div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf
                <!-- Email Address -->
                <div>
                    <label for="email"
                        class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="nama@email.com"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-lg shadow-md hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Kirim Link Reset Password</span>
                </button>
            </form>

            <!-- Footer / Back to Login -->
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Ingat password Anda? <a href="{{ url('/login') }}"
                        class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>