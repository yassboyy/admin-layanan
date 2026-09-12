@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Hubungi Kami</h1>
        </div>
    </section>

    <!-- Contact Form and Details -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contact Details -->
                <div class="lg:col-span-1 space-y-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Informasi Kontak</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Silakan hubungi kami melalui salah satu saluran berikut, atau kirimkan pesan langsung melalui
                            formulir di samping.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Address -->
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Alamat Kantor
                                </h4>
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    Jl. Madura No. 17, Kelurahan Hadimulyo Barat, Metro Pusat, Kota Metro, Lampung
                                </p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Telepon &
                                    WhatsApp</h4>
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    <span class="block">+62 812-7960-316</span>
                                    <span class="block mt-0.5">+62 813-7988-7120</span>
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Email Resmi</h4>
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    cvtomoteknikmandiri@gmail.com
                                </p>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Jam Operasional
                                </h4>
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    Senin - Sabtu: 08:00 - 17:00 WIB<br>
                                    Minggu & Hari Libur: Tutup
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Kirim Pesan Langsung</h3>
                    <form action="#" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                                <input type="text" placeholder="Masukkan nama Anda"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon</label>
                                <input type="text" placeholder="Masukkan nomor telepon"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                            <input type="email" placeholder="Masukkan email Anda"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pesan</label>
                            <textarea rows="5" placeholder="Tuliskan pesan atau kebutuhan teknik Anda..."
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white rounded-lg shadow-md transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Kirim Pesan</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection