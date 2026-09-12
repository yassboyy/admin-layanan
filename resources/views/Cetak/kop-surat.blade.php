@php
    $logoFile = public_path('images/logo-192.png');
    if (!file_exists($logoFile)) {
        $logoFile = public_path('images/logo.png');
    }
    $logoSrc = file_exists($logoFile)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile))
        : asset('images/logo.png');
@endphp

{{-- Kop Surat Resmi CV. TOMO TEKNIK MANDIRI --}}
<div class="kop-surat-container mb-6" style="font-family: Arial, Helvetica, 'Segoe UI', sans-serif;">
    <div style="display: flex; align-items: center; justify-content: flex-start; gap: 16px;">
        {{-- Logo Perusahaan --}}
        <div style="flex-shrink: 0;">
            <img src="{{ $logoSrc }}" alt="Logo CV. TOMO TEKNIK MANDIRI" 
                 style="width: 88px; height: 88px; object-fit: contain; display: block;">
        </div>

        {{-- Teks Kop Surat --}}
        <div style="flex: 1; min-width: 0;">
            <h1 style="margin: 0; padding: 0; font-size: 22px; font-weight: 800; color: #16335c; letter-spacing: 0.5px; line-height: 1.15; text-transform: uppercase;">
                CV. TOMO TEKNIK MANDIRI
            </h1>
            <div style="margin-top: 4px; font-size: 10.5px; font-weight: 700; color: #b5791a; line-height: 1.3; white-space: nowrap;">
                <span>Electrical</span>
                <span style="color: #b5791a; margin: 0 3px;">&middot;</span>
                <span>Service Genset</span>
                <span style="color: #b5791a; margin: 0 3px;">&middot;</span>
                <span>Service Electro Motor</span>
                <span style="color: #b5791a; margin: 0 3px;">&middot;</span>
                <span>Service Transformator</span>
                <span style="color: #b5791a; margin: 0 3px;">&middot;</span>
                <span>Perakitan Panel Listrik</span>
            </div>
            <div style="margin-top: 4px; font-size: 10px; color: #333333; line-height: 1.45;">
                <span>Jl. Madura No. 17, Hadimulyo Barat, Metro Pusat, Provinsi Lampung</span>
                <span style="color: #b5791a; font-weight: bold; margin: 0 4px;">|</span>
                <span>cvtomoteknikmandiri@gmail.com</span>
                <span style="color: #b5791a; font-weight: bold; margin: 0 4px;">|</span>
                <span style="white-space: nowrap;">0812-7960-316 / 0813-7988-7120</span>
            </div>
        </div>
    </div>

    {{-- Garis Pembatas Kop Surat: Garis Biru Tua Tebal + Garis Emas/Oranye --}}
    <div style="margin-top: 10px; margin-bottom: 22px;">
        <div style="border-top: 3.5px solid #16335c; width: 100%;"></div>
        <div style="border-top: 1.5px solid #b5791a; width: 100%; margin-top: 2px;"></div>
    </div>
</div>
