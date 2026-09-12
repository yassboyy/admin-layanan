@extends('layouts.app')

@section('title', 'Layanan')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Layanan Kami</h1>
        </div>
    </section>

    <!-- Filter & Search Section -->
    <section class="py-8 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Categories -->
                <div class="flex flex-wrap gap-2">
                    <button
                        class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-blue-600 text-white">Semua</button>
                    @foreach($layanan->pluck('jenis_layanan')->unique() as $jenis)
                        <button
                            class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200">{{ $jenis }}</button>
                    @endforeach
                </div>
                <!-- Search bar -->
                <div class="w-full sm:w-72">
                    <input type="text" placeholder="Cari layanan..."
                        class="w-full px-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>
    </section>

    <!-- Layout Layanan (Placeholder Tampilan) -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Notice banner explaining it is a template -->

            <!-- Grid of Services -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($layanan as $item)
                    <div class="service-card bg-white rounded-2xl overflow-hidden border border-slate-100 hover:border-blue-100 hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300"
                        data-nama="{{ $item->nama_layanan }}" data-jenis="{{ $item->jenis_layanan }}">
                        <div class="h-48 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                            @if($item->gambar && file_exists(public_path($item->gambar)))
                                <img src="{{ asset($item->gambar) }}" alt="{{ $item->nama_layanan }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            @endif
                            <div
                                class="absolute top-4 left-4 px-3 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">
                                {{ $item->jenis_layanan }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-2 truncate">{{ $item->nama_layanan }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mb-6 line-clamp-3 h-15">
                                {{ $item->deskripsi }}
                            </p>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <div>
                                    <span class="block text-[10px] text-slate-400 font-medium">Estimasi Biaya</span>
                                    <span class="text-base font-extrabold text-blue-600">Rp
                                        {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ url('/layanan/' . $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Booking Layanan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Belum ada data layanan tersedia.</p>
                        </div>
                    </div>
                @endforelse
                {{-- Empty search result row --}}
                <div id="no-services-found" class="col-span-3 text-center py-12 hidden">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="h-12 w-12 text-slate-350" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="text-sm font-semibold text-slate-400">Tidak ada layanan yang cocok dengan pencarian.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[placeholder="Cari layanan..."]');
            const cards = document.querySelectorAll('.service-card');
            const emptyMsg = document.getElementById('no-services-found');
            const filterButtons = document.querySelectorAll('button[class*="rounded-lg"]');

            let activeCategory = 'Semua';
            let searchQuery = '';

            function filterLayanan() {
                let visibleCount = 0;
                cards.forEach(card => {
                    const nama = card.getAttribute('data-nama').toLowerCase();
                    const jenis = card.getAttribute('data-jenis').toLowerCase();
                    const matchCategory = activeCategory === 'Semua' || jenis.includes(activeCategory.toLowerCase());
                    const matchSearch = nama.includes(searchQuery) || jenis.includes(searchQuery);

                    if (matchCategory && matchSearch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (emptyMsg) {
                    emptyMsg.classList.toggle('hidden', visibleCount > 0);
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    searchQuery = this.value.toLowerCase().trim();
                    filterLayanan();
                });
            }

            filterButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Update active classes
                    filterButtons.forEach(b => {
                        b.className = 'px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200';
                    });
                    this.className = 'px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-blue-600 text-white';

                    activeCategory = this.textContent.trim();
                    filterLayanan();
                });
            });
        });
    </script>
@endsection