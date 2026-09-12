<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DirekturController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagerTeknisiController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PesananController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// NOTIFIKASI ROUTES (Semua role yang login)
// ============================================================================
Route::get('/notifikasi/data', [NotifikasiController::class, 'getData']);
Route::post('/notifikasi/baca/{id}', [NotifikasiController::class, 'tandaiBaca']);
Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'tandaiBacaSemua']);

// ============================================================================
// CHAT REDIRECT ROUTE (Global)
// ============================================================================
Route::get('/chat', function () {
    if (!session()->has('user')) {
        return redirect('/login')->withErrors(['login' => 'Silakan login terlebih dahulu untuk mengakses layanan chat.']);
    }
    $role = session('user.role');
    if ($role === 'admin') {
        return redirect('/admin/chat');
    }
    if ($role === 'pelanggan') {
        return redirect('/pelanggan/chat');
    }
    return redirect('/dashboard');
});

// ============================================================================
// HALAMAN PUBLIK (HomePage)
// ============================================================================
Route::get('/', [HomeController::class, 'index']);
Route::get('/tentang-kami', [HomeController::class, 'tentangKami']);
Route::get('/layanan', [HomeController::class, 'layanan']);
Route::get('/layanan/{id}', [HomeController::class, 'detailLayanan']);
Route::get('/dokumentasi', [HomeController::class, 'dokumentasi']);
Route::get('/kontak', [HomeController::class, 'kontak']);

// ============================================================================
// PESANAN, KERANJANG & CHECKOUT (Front-end Pelanggan)
// ============================================================================
Route::get('/pesanan', [PesananController::class, 'index']);
Route::get('/detail-pesanan/{id}', [PesananController::class, 'show']);
Route::post('/pesanan/upload-bukti/{id}', [PesananController::class, 'uploadBukti']);
Route::post('/pesanan/lanjut-proses/{id}', [PesananController::class, 'lanjutProses']);
Route::get('/keranjang', [PesananController::class, 'keranjang']);
Route::post('/keranjang/tambah/{id}', [PesananController::class, 'tambahKeranjang']);
Route::post('/keranjang/update/{id}', [PesananController::class, 'updateQuantity']);
Route::delete('/keranjang/hapus/{id}', [PesananController::class, 'hapusKeranjang']);
Route::post('/checkout', [PesananController::class, 'checkout']);
Route::get('/cetak/{tipe}/{id}', [PesananController::class, 'cetakDokumen']);

// ============================================================================
// AUTENTIKASI
// ============================================================================
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token?}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/verify-email/resend', [AuthController::class, 'resendVerificationCode']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/dashboard', [AuthController::class, 'dashboard']);

// ============================================================================
// ADMIN ROUTES
// ============================================================================
Route::middleware('role:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/data-pelanggan', [AdminController::class, 'dataPelanggan']);
    Route::post('/admin/data-pelanggan', [AdminController::class, 'storePelanggan']);

    // Pesanan
    Route::get('/admin/pesanan', [AdminController::class, 'pesanan']);
    Route::get('/admin/detail-pesanan/{id}', [AdminController::class, 'detailPesanan']);
    Route::post('/admin/pesanan/{id}/ajukan-dokumen', [AdminController::class, 'ajukanDokumen']);
    Route::post('/admin/pesanan/{id}/konfirmasi-bukti', [AdminController::class, 'konfirmasiBuktiPembayaran']);
    Route::post('/admin/pesanan/{id}/tolak-bukti', [AdminController::class, 'tolakBuktiPembayaran']);
    Route::redirect('/admin/pengajuan-edit-pesanan', '/admin/pesanan');

    // Data Layanan (CRUD)
    Route::get('/admin/data-layanan', [AdminController::class, 'dataLayanan']);
    Route::post('/admin/data-layanan', [AdminController::class, 'storeLayanan']);
    Route::put('/admin/data-layanan/{id}', [AdminController::class, 'updateLayanan']);
    Route::delete('/admin/data-layanan/{id}', [AdminController::class, 'destroyLayanan']);

    // Data Staff (CRUD)
    Route::get('/admin/data-staff', [AdminController::class, 'dataStaff']);
    Route::post('/admin/data-staff', [AdminController::class, 'storeStaff']);
    Route::put('/admin/data-staff/{id}', [AdminController::class, 'updateStaff']);
    Route::delete('/admin/data-staff/{id}', [AdminController::class, 'destroyStaff']);

    // Data Alat (CRUD)
    Route::get('/admin/data-alat', [AdminController::class, 'dataAlat']);
    Route::post('/admin/data-alat', [AdminController::class, 'storeAlat']);
    Route::put('/admin/data-alat/{id}', [AdminController::class, 'updateAlat']);
    Route::delete('/admin/data-alat/{id}', [AdminController::class, 'destroyAlat']);

    // Dokumentasi Proyek (CRUD)
    Route::get('/admin/dokumentasi-proyek', [AdminController::class, 'dokumentasiProyek']);
    Route::post('/admin/dokumentasi-proyek', [AdminController::class, 'storeDokumentasi']);
    Route::put('/admin/dokumentasi-proyek/{id}', [AdminController::class, 'updateDokumentasi']);
    Route::delete('/admin/dokumentasi-proyek/{id}', [AdminController::class, 'destroyDokumentasi']);

    // Chat Admin & Pesan
    Route::get('/admin/chat', [AdminController::class, 'chat']);
    Route::post('/admin/chat', [AdminController::class, 'sendChat']);
    Route::get('/admin/chat/messages', [AdminController::class, 'getChatMessages']);
    Route::get('/admin/tentang-kami', [AdminController::class, 'tentangKami']);
    Route::post('/admin/tentang-kami', [AdminController::class, 'updateTentangKami']);
});

// ============================================================================
// DIREKTUR ROUTES
// ============================================================================
Route::middleware('role:direktur')->group(function () {
    Route::get('/direktur/dashboard', [DirekturController::class, 'dashboard']);
    Route::get('/direktur/data-pelanggan', [DirekturController::class, 'dataPelanggan']);
    Route::get('/direktur/validasi-dokumen', [DirekturController::class, 'validasiDokumen']);
    Route::get('/direktur/validasi-dokumen/detail/{id}', [DirekturController::class, 'detailValidasiDokumen']);
    Route::post('/direktur/validasi-dokumen/{pemesanan_id}/setujui', [DirekturController::class, 'setujuiDokumen']);
    Route::post('/direktur/validasi-dokumen/{pemesanan_id}/tolak', [DirekturController::class, 'tolakDokumen']);
    Route::get('/direktur/laporan-teknisi', [DirekturController::class, 'laporanTeknisi']);
    Route::post('/direktur/laporan-teknisi/{id}/konfirmasi', [DirekturController::class, 'konfirmasiLaporanTeknisi']);
    Route::post('/direktur/laporan-teknisi/{id}/tolak', [DirekturController::class, 'tolakLaporanTeknisi']);
});

// ============================================================================
// MANAGER TEKNISI ROUTES
// ============================================================================
Route::middleware('role:managerteknisi')->group(function () {
    Route::get('/manager/dashboard', [ManagerTeknisiController::class, 'dashboard']);
    Route::get('/manager/pelaporan', [ManagerTeknisiController::class, 'pelaporan']);
    Route::post('/manager/pelaporan', [ManagerTeknisiController::class, 'storePelaporan']);
});

// ============================================================================
// PELANGGAN ROUTES
// ============================================================================
Route::middleware('role:pelanggan')->group(function () {
    Route::get('/pelanggan/dashboard', [PelangganController::class, 'dashboard']);
    Route::get('/pelanggan/pesanan', [PelangganController::class, 'pesanan']);
    Route::get('/pelanggan/detail-pesanan/{id}', [PelangganController::class, 'detailPesanan']);
    Route::get('/pelanggan/chat', [PelangganController::class, 'chat']);
    Route::post('/pelanggan/chat', [PelangganController::class, 'sendChat']);
    Route::get('/pelanggan/chat/messages', [PelangganController::class, 'getChatMessages']);
});
