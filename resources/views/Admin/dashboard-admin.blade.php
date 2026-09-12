@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Dashboard</h1>

    <!-- Welcome Card -->
    <div class="bg-white dark:bg-slate-800 p-6 sm:p-7 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors space-y-2">
        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">Selamat Datang..</h2>
        <p class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight">
            Halo {{ session('user.name', 'Admin') }}, Anda Login sebagai Admin
        </p>
    </div>

    <!-- Quick Stats Grid (Hanya 3 Card: Total Layanan, Total Pesanan, Total Pelanggan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Stat card 1: Total Layanan -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors">
            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Total Layanan</span>
            <span class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ \App\Models\Layanan::count() }}</span>
        </div>
        <!-- Stat card 2: Total Pesanan -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors">
            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Total Pesanan</span>
            <span class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ \App\Models\Pemesanan::count() }}</span>
        </div>
        <!-- Stat card 3: Total Pelanggan -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors">
            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Total Pelanggan</span>
            <span class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ \App\Models\User::where('role', 'pelanggan')->count() }}</span>
        </div>
    </div>
</div>
@endsection
