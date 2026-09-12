@extends('layouts.dashboard')

@section('title', 'Dashboard Manager Teknisi')
@section('page_title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Dashboard</h1>

        <!-- Welcome Card -->
        <div
            class="bg-white dark:bg-slate-800 p-6 sm:p-7 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors space-y-2">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">Selamat Datang..</h2>
            <p class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight">
                Halo {{ session('user.name', 'Manager Teknisi') }}, Anda Login sebagai Manager Teknisi
            </p>
        </div>
    </div>
@endsection