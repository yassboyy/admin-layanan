<?php

namespace App\Http\Controllers;

use App\Jobs\SendNewOrderNotification;
use App\Mail\NotifikasiPesananBaruMail;
use App\Models\Chat;
use App\Models\Keranjang;
use App\Models\Layanan;
use App\Models\Pemesanan;
use App\Models\PemesananDokumen;
use App\Models\PemesananLayanan;
use App\Models\User;
use App\Models\ValidasiDokumen;
use App\Notifications\AccountAutoCreatedNotification;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    /**
     * Field bukti dokumen yang tersimpan langsung di tabel pemesanan.
     */
    private const FIELD_BUKTI_UMUM = [
        'bukti_po',
        'bukti_surat_perjanjian_kerja',
        'bukti_dp',
        'bukti_lunas',
    ];

    /**
     * Helper: Dapatkan user_id dari session.
     */
    private function getUserId()
    {
        if (!session()->has('user')) {
            return null;
        }
        if (empty(session('user.id')) && session()->has('user.email')) {
            $user = \App\Models\User::where('email', session('user.email'))->first();
            if ($user) {
                $userSession = session('user');
                $userSession['id'] = $user->id;
                session(['user' => $userSession]);
                return $user->id;
            }
        }
        return session('user.id');
    }

    /**
     * Halaman daftar pesanan pelanggan (front-end).
     */
    public function index()
    {
        if (!session()->has('user')) {
            return view('HomePage.pesanan', ['orders' => collect()]);
        }

        if (session('user.role') !== 'pelanggan') {
            return redirect('/dashboard');
        }

        $orders = Pemesanan::where('user_id', $this->getUserId())
            ->with('pemesananLayanan.layanan')
            ->latest()
            ->get();

        return view('HomePage.pesanan', compact('orders'));
    }

    /**
     * Halaman detail pesanan.
     */
    public function show($id)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        $role = session('user.role');

        if ($role === 'pelanggan') {
            $order = Pemesanan::where('user_id', $this->getUserId())
                ->where('id', $id)
                ->with(['pemesananLayanan.layanan.dokumenLayanan', 'pemesananDokumen', 'validasiDokumen', 'pelaporanTeknisi'])
                ->firstOrFail();
        } else {
            $order = Pemesanan::where('id', $id)
                ->with(['pemesananLayanan.layanan.dokumenLayanan', 'pemesananDokumen', 'validasiDokumen', 'pelaporanTeknisi'])
                ->firstOrFail();
        }

        return view('HomePage.detail-pesanan', compact('order'));
    }

    /**
     * Upload bukti dokumen pelanggan dan dokumen persyaratan pesanan.
     */
    public function uploadBukti(Request $request, $id)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        if (!in_array(session('user.role'), ['pelanggan', 'admin'])) {
            return redirect('/dashboard');
        }

        $order = (session('user.role') === 'admin')
            ? Pemesanan::findOrFail($id)
            : Pemesanan::where('user_id', $this->getUserId())->where('id', $id)->firstOrFail();

        // 1. Cek apakah yang diunggah adalah Dokumen Persyaratan Layanan Dinamis
        if ($request->input('jenis_bukti') === 'dokumen_persyaratan' || $request->has('pemesanan_dokumen_id')) {
            $valPersyaratan = ValidasiDokumen::where('pemesanan_id', $order->id)
                ->where('tipe_dokumen', 'dokumen_persyaratan')
                ->first();

            if ($valPersyaratan && $valPersyaratan->status_dokumen === 'disetujui') {
                return redirect()->back()->withErrors([
                    'upload' => 'Dokumen persyaratan layanan telah disetujui oleh Direktur dan tidak dapat diubah lagi.'
                ]);
            }

            $request->validate([
                'file_bukti' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
                'pemesanan_dokumen_id' => 'nullable|integer',
                'nama_dokumen' => 'nullable|string',
            ], [
                'file_bukti.required' => 'Berkas dokumen wajib dipilih.',
                'file_bukti.mimes' => 'Format berkas harus berupa JPG, PNG, atau PDF.',
                'file_bukti.max' => 'Ukuran berkas maksimal 5MB.',
            ]);

            $docId = $request->input('pemesanan_dokumen_id');
            $namaDok = $request->input('nama_dokumen', 'Dokumen Persyaratan');

            if ($docId) {
                $dokumenPesanan = PemesananDokumen::where('pemesanan_id', $order->id)->where('id', $docId)->first();
            } else {
                $dokumenPesanan = PemesananDokumen::where('pemesanan_id', $order->id)->where('nama_dokumen', $namaDok)->first();
            }

            if (!$dokumenPesanan) {
                $dokumenPesanan = new PemesananDokumen();
                $dokumenPesanan->pemesanan_id = $order->id;
                $dokumenPesanan->nama_dokumen = $namaDok;
            }

            if ($request->hasFile('file_bukti')) {
                // Hapus file lama jika ada
                if ($dokumenPesanan->file_path && file_exists(public_path($dokumenPesanan->file_path))) {
                    @unlink(public_path($dokumenPesanan->file_path));
                }

                $file = $request->file('file_bukti');
                $filename = time() . '_dokumen_' . $order->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $dest = public_path('uploads/dokumen_pesanan');
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                $file->move($dest, $filename);
                $dokumenPesanan->file_path = 'uploads/dokumen_pesanan/' . $filename;
                $dokumenPesanan->save();
            }

            // Otomatis buat / perbarui ValidasiDokumen untuk Direktur
            ValidasiDokumen::updateOrCreate(
                [
                    'pemesanan_id' => $order->id,
                    'tipe_dokumen' => 'dokumen_persyaratan',
                ],
                [
                    'status_dokumen' => 'menunggu',
                    'alasan' => null,
                ]
            );

            // Notifikasi ke Direktur & Admin
            NotifikasiService::kirimKeRole('direktur', 'Dokumen Persyaratan Layanan Menunggu Validasi', 'Dokumen persyaratan layanan untuk pesanan ' . $order->id_pesanan . ' telah diunggah dan menunggu validasi.', 'warning', '/direktur/validasi-dokumen');
            NotifikasiService::kirimKeRole('admin', 'Dokumen Persyaratan Diupload', 'Pelanggan mengupload dokumen persyaratan "' . $dokumenPesanan->nama_dokumen . '" untuk pesanan ' . $order->id_pesanan . '.', 'info', '/admin/detail-pesanan/' . $order->id);

            return redirect()->back()->with('success', 'Dokumen ' . $dokumenPesanan->nama_dokumen . ' berhasil diunggah!');
        }

        // 2. Upload Dokumen Umum (Penawaran, SPK, DP, Lunas)
        $rules = [
            'jenis_bukti' => 'required|in:' . implode(',', self::FIELD_BUKTI_UMUM),
            'file_bukti' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
        ];

        if ($request->input('jenis_bukti') === 'bukti_po') {
            $rules['nominal_diskon'] = 'nullable|numeric|min:0';
        }

        $request->validate($rules, [
            'nominal_diskon.numeric' => 'Nominal diskon harus berupa angka.',
            'nominal_diskon.min' => 'Nominal diskon tidak boleh kurang dari 0.',
            'file_bukti.required' => 'Berkas dokumen P.O wajib diunggah.',
        ]);

        $field = $request->input('jenis_bukti');

        // Cegah penggantian dokumen jika sudah disetujui
        $validasi = ValidasiDokumen::where('pemesanan_id', $order->id)
            ->where('tipe_dokumen', $field)
            ->first();

        if ($validasi && $validasi->status_dokumen === 'disetujui') {
            $approvedBy = in_array($field, ['bukti_dp', 'bukti_lunas']) ? 'Admin' : 'Direktur';
            return redirect()->back()->withErrors([
                'upload' => "Dokumen ini telah disetujui oleh {$approvedBy} dan tidak dapat diganti atau diunggah ulang."
            ]);
        }

        // Cegah upload SPK jika Bukti DP belum diupload atau belum dikonfirmasi Admin
        if ($field === 'bukti_surat_perjanjian_kerja') {
            $valDp = ValidasiDokumen::where('pemesanan_id', $order->id)
                ->where('tipe_dokumen', 'bukti_dp')
                ->first();

            $isDpApproved = ($valDp && $valDp->status_dokumen === 'disetujui') || (int)$order->status_tipe >= 2;

            if (empty($order->bukti_dp) || !$isDpApproved) {
                return redirect()->back()->withErrors([
                    'upload' => 'Anda belum mengupload Bukti DP 50% atau Bukti DP belum dikonfirmasi oleh Admin.'
                ]);
            }
        }

        // Cegah upload Bukti Pelunasan jika Invoice belum disetujui Direktur
        if ($field === 'bukti_lunas') {
            if (!$order->canUploadPelunasan()) {
                return redirect()->back()->withErrors([
                    'upload' => 'Upload bukti pelunasan belum tersedia. Bukti pelunasan dapat diunggah setelah Invoice disetujui Direktur.'
                ]);
            }
        }

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filename = time() . '_' . $field . '_' . $order->id . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/bukti');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            $order->$field = 'uploads/bukti/' . $filename;

            if ($field === 'bukti_po') {
                $nominalDiskon = max(0, floatval($request->input('nominal_diskon', 0)));
                $order->diskon_diajukan = $nominalDiskon;
            }

            $order->save();

            ValidasiDokumen::updateOrCreate(
                [
                    'pemesanan_id' => $order->id,
                    'tipe_dokumen' => $field,
                ],
                [
                    'status_dokumen' => 'menunggu',
                    'alasan' => null,
                ]
            );
        }

        // Notifikasi berdasarkan jenis dokumen yang diupload
        $labelDokumen = match($field) {
            'bukti_po' => 'Dokumen PO',
            'bukti_surat_perjanjian_kerja' => 'Bukti SPK',
            'bukti_dp' => 'Bukti Transfer DP',
            'bukti_lunas' => 'Bukti Transfer Pelunasan',
            default => 'Dokumen',
        };

        if (in_array($field, ['bukti_dp', 'bukti_lunas'])) {
            // Bukti pembayaran → notif ke Admin
            NotifikasiService::kirimKeRole('admin', $labelDokumen . ' Diupload', 'Pelanggan mengupload ' . $labelDokumen . ' untuk pesanan ' . $order->id_pesanan . '. Menunggu konfirmasi.', 'info', '/admin/detail-pesanan/' . $order->id);
        } else {
            // Dokumen PO/SPK → notif ke Admin & Direktur
            NotifikasiService::kirimKeRole('admin', $labelDokumen . ' Diupload', 'Pelanggan mengupload ' . $labelDokumen . ' untuk pesanan ' . $order->id_pesanan . '.', 'info', '/admin/detail-pesanan/' . $order->id);
            if ($field === 'bukti_po') {
                NotifikasiService::kirimKeRole('direktur', $labelDokumen . ' Menunggu Validasi', $labelDokumen . ' untuk pesanan ' . $order->id_pesanan . ' menunggu validasi.', 'warning', '/direktur/validasi-dokumen');
            }
        }

        if ($field === 'bukti_po') {
            if ($order->diskon_diajukan > 0) {
                $msg = 'Dokumen P.O dan pengajuan diskon sebesar Rp ' . number_format($order->diskon_diajukan, 0, ',', '.') . ' berhasil diajukan ke Direktur untuk divalidasi!';
            } else {
                $msg = 'Dokumen P.O berhasil diajukan ke Direktur untuk divalidasi!';
            }
        } elseif (in_array($field, ['bukti_dp', 'bukti_lunas'])) {
            $msg = 'Dokumen bukti pembayaran berhasil diunggah dan menunggu konfirmasi dari Admin!';
        } else {
            $msg = 'Dokumen bukti berhasil diunggah dan dikirim ke Direktur untuk divalidasi!';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Lanjutkan proses pesanan ke tahap Pengerjaan.
     */
    public function lanjutProses($id)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        $order = Pemesanan::where('user_id', $this->getUserId())
            ->where('id', $id)
            ->firstOrFail();

        $isDpApproved = $order->getValidasiStatus('bukti_dp') === 'disetujui';
        $isLunasApproved = $order->getValidasiStatus('bukti_lunas') === 'disetujui';

        if (!$isDpApproved && !$isLunasApproved) {
            return redirect()->back()->withErrors(['upload' => 'Dokumen Bukti DP (50%) atau Bukti Pelunasan belum dikonfirmasi/disetujui oleh Admin. Silakan tunggu hingga pembayaran dikonfirmasi.']);
        }

        return redirect('/pelanggan/detail-pesanan/' . $order->id)->with('success', 'Bukti DP telah dikonfirmasi Admin. Silakan lengkapi dokumen persyaratan dan SPK.');
    }

    /**
     * Halaman keranjang belanja.
     */
    public function keranjang()
    {
        if (!session()->has('user')) {
            return view('HomePage.keranjang', ['items' => collect()]);
        }

        if (session('user.role') !== 'pelanggan') {
            return redirect('/dashboard');
        }

        $items = Keranjang::where('user_id', $this->getUserId())
            ->with('layanan')
            ->get();

        return view('HomePage.keranjang', compact('items'));
    }

    /**
     * Tambah item ke keranjang.
     * Mendukung booking langsung untuk pelanggan baru / belum login (auto-register & set session).
     */
    public function tambahKeranjang(Request $request, $id)
    {
        $isNewUser = false;
        $newUserInstance = null;

        if (!session()->has('user')) {
            $email = strtolower(trim($request->input('email', '')));
            $nama = trim($request->input('nama_pelanggan', ''));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()->withErrors(['email' => 'Silakan masukkan alamat email yang valid untuk melakukan pendaftaran booking.'])->withInput();
            }

            if (empty($nama)) {
                $nama = 'Pelanggan';
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                $isNewUser = true;
                $randomPassword = Str::random(16);
                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => bcrypt($randomPassword),
                    'role' => 'pelanggan',
                    'email_verified_at' => now(),
                ]);

                // Buat sapaan chat awal dari Admin
                $adminUser = User::where('role', 'admin')->first();
                $adminId = $adminUser ? $adminUser->id : 1;
                Chat::create([
                    'user_id' => $adminId,
                    'pelanggan_id' => $user->id,
                    'pesan' => "Halo {$user->name}! 👋\nTim Admin kami siap melayani Anda.",
                    'created_at' => $user->created_at ?? now(),
                    'updated_at' => $user->created_at ?? now(),
                ]);

                NotifikasiService::kirim($user->id, 'Selamat Datang!', 'Selamat datang di CV Tomo Teknik Mandiri, ' . $user->name . '. Layanan Anda telah berhasil ditambahkan ke keranjang.', 'success', '/keranjang');
                NotifikasiService::kirimKeRole('admin', 'Pelanggan Baru Terdaftar', 'Pelanggan baru terdaftar melalui booking layanan: ' . $user->name . ' (' . $user->email . ').', 'info', '/admin/data-pelanggan');
            }

            // Login-kan user ke Auth guard dan session
            Auth::login($user);
            session([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'pelanggan',
                ]
            ]);

            $newUserInstance = $user;
        }

        if (session('user.role') !== 'pelanggan') {
            return redirect('/')->withErrors(['role' => 'Hanya pelanggan yang dapat memesan layanan.']);
        }

        $layanan = Layanan::findOrFail($id);
        $qty = max(1, (int) $request->input('jumlah', 1));
        $userId = $this->getUserId();

        // Kirim email notifikasi set password ke akun yang baru dibuat otomatis
        if ($isNewUser && $newUserInstance) {
            try {
                $token = Password::createToken($newUserInstance);
                $newUserInstance->notify(new AccountAutoCreatedNotification($token, $layanan->nama_layanan));
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim AccountAutoCreatedNotification: ' . $e->getMessage());
            }
        }

        // Simpan detail booking ke session jika dikirim
        session(['booking_details' => [
            'nama_pelanggan' => $request->input('nama_pelanggan', session('user.name')),
            'alamat_lengkap' => $request->input('alamat_lengkap', ''),
            'nomor_hp' => $request->input('nomor_hp', ''),
            'email' => $request->input('email', session('user.email')),
            'catatan_tambahan' => $request->input('catatan_tambahan', ''),
        ]]);

        // Kalau item sudah ada di keranjang, tambah jumlahnya
        $existing = Keranjang::where('user_id', $userId)
            ->where('layanan_id', $id)
            ->first();

        if ($existing) {
            $existing->update([
                'jumlah' => $existing->jumlah + $qty,
            ]);
        } else {
            Keranjang::create([
                'user_id' => $userId,
                'layanan_id' => $id,
                'jumlah' => $qty,
                'harga' => $layanan->harga,
            ]);
        }

        return redirect('/keranjang')->with('success', 'Layanan "' . $layanan->nama_layanan . '" berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update jumlah / kuantiti item di keranjang.
     */
    public function updateQuantity(Request $request, $id)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        $item = Keranjang::where('user_id', $this->getUserId())
            ->where('id', $id)
            ->firstOrFail();

        $action = $request->input('action');

        if ($action === 'increase') {
            $item->increment('jumlah');
        } elseif ($action === 'decrease') {
            if ($item->jumlah > 1) {
                $item->decrement('jumlah');
            } else {
                $item->delete();
                return redirect('/keranjang')->with('success', 'Item dihapus dari keranjang.');
            }
        } elseif ($request->has('jumlah')) {
            $qty = max(1, (int) $request->input('jumlah'));
            $item->update(['jumlah' => $qty]);
        }

        return redirect('/keranjang')->with('success', 'Jumlah item berhasil diperbarui.');
    }

    /**
     * Hapus item dari keranjang.
     */
    public function hapusKeranjang($id)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        $item = Keranjang::where('user_id', $this->getUserId())
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();
        return redirect('/keranjang')->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Proses checkout keranjang menjadi pesanan.
     * Menggabungkan seluruh item layanan dan inisialisasi dokumen persyaratan.
     */
    public function checkout(Request $request)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        if (session('user.role') !== 'pelanggan') {
            return redirect('/')->withErrors(['role' => 'Hanya pelanggan yang dapat memesan layanan.']);
        }

        $cartItems = Keranjang::where('user_id', $this->getUserId())
            ->with('layanan.dokumenLayanan')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect('/keranjang')->withErrors(['cart' => 'Keranjang anda kosong.']);
        }

        $saved = session('booking_details', []);
        $namaPelanggan = $request->input('nama_pelanggan', $saved['nama_pelanggan'] ?? session('user.name') ?? 'Pelanggan');
        $nomorHp = $request->input('nomor_hp', $saved['nomor_hp'] ?? '-');
        $email = $request->input('email', $saved['email'] ?? session('user.email') ?? '-');
        $alamat = $request->input('alamat_lengkap', $saved['alamat_lengkap'] ?? '-');
        $catatan = $request->input('catatan_tambahan', $saved['catatan_tambahan'] ?? null);

        $subtotal = $cartItems->sum(function ($item) {
            return $item->harga * $item->jumlah;
        });
        $ppn = $subtotal * 0.11; // PPN 11%
        $pph = $subtotal * 0.02; // PPh Jasa 2%
        $totalHarga = max(0, $subtotal + $ppn - $pph); // Subtotal Jasa + PPN 11% - PPh Jasa 2%

        $idPesanan = Pemesanan::generateIdPesanan();

        $pemesanan = Pemesanan::create([
            'user_id' => $this->getUserId(),
            'nama_pelanggan' => $namaPelanggan,
            'no_hp' => $nomorHp,
            'email' => $email,
            'alamat' => $alamat,
            'id_pesanan' => $idPesanan,
            'catatan' => $catatan,
            'diskon' => 0,
            'total_harga' => $totalHarga,
            'status_tipe' => 1, // Proses Pemesanan
        ]);

        $createdDocNames = [];

        foreach ($cartItems as $item) {
            $layanan = $item->layanan;
            PemesananLayanan::create([
                'pemesanan_id' => $pemesanan->id,
                'layanan_id' => $item->layanan_id,
                'nama_layanan' => $layanan->nama_layanan,
                'jenis_layanan' => $layanan->jenis_layanan,
                'jumlah' => $item->jumlah,
                'harga' => $item->harga,
                'diskon' => 0,
                'deskripsi' => $layanan->deskripsi ?: '',
                'gambar' => $layanan->gambar ?: '',
            ]);

            // Inisialisasi dokumen persyaratan pesanan dari dokumen layanan (tanpa duplikasi)
            if ($layanan && $layanan->dokumenLayanan) {
                foreach ($layanan->dokumenLayanan as $dok) {
                    $docKey = strtolower(trim($dok->nama_dokumen));
                    if (!in_array($docKey, $createdDocNames)) {
                        PemesananDokumen::create([
                            'pemesanan_id' => $pemesanan->id,
                            'dokumen_layanan_id' => $dok->id,
                            'nama_dokumen' => $dok->nama_dokumen,
                            'file_path' => null,
                        ]);
                        $createdDocNames[] = $docKey;
                    }
                }
            }
        }

        // Bersihkan keranjang dan session booking
        Keranjang::where('user_id', $this->getUserId())->delete();
        session()->forget('booking_details');

        // Kirim notifikasi konfirmasi pesanan ke email pelanggan
        $customerEmail = !empty($pemesanan->email) ? trim($pemesanan->email) : (session('user.email') ?? null);
        if ($customerEmail && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                $pemesanan->load('pemesananLayanan.layanan');
                Mail::to($customerEmail)->send(new NotifikasiPesananBaruMail($pemesanan));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email konfirmasi pesanan ID ' . $pemesanan->id_pesanan . ': ' . $e->getMessage());
            }
        }

        // Kirim email notifikasi pesanan baru ke Admin via Queue Job
        try {
            SendNewOrderNotification::dispatch($pemesanan);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim notifikasi email admin untuk pesanan ID ' . $pemesanan->id_pesanan . ': ' . $e->getMessage());
        }

        // Notifikasi ke Pelanggan: pesanan berhasil dibuat
        NotifikasiService::kirim($this->getUserId(), 'Pesanan Berhasil Dibuat', 'Pesanan ' . $idPesanan . ' telah berhasil dibuat. Silakan upload dokumen PO dan bukti pembayaran DP.', 'success', '/pelanggan/detail-pesanan/' . $pemesanan->id);

        // Notifikasi ke semua Admin: ada pesanan baru
        NotifikasiService::kirimKeRole('admin', 'Pesanan Baru Masuk', 'Pesanan baru ' . $idPesanan . ' dari ' . $namaPelanggan . ' dengan ' . $cartItems->count() . ' layanan.', 'info', '/admin/detail-pesanan/' . $pemesanan->id);

        return redirect('/pesanan')->with('success', 'Pesanan ' . $idPesanan . ' berhasil dibuat dengan ' . $cartItems->count() . ' jenis layanan!');
    }

    /**
     * Cetak dokumen proyek (SPK, Surat Jalan, Berita Acara, Invoice).
     */
    public function cetakDokumen($tipe, $id)
    {
        if (!session()->has('user')) {
            return redirect('/login');
        }

        $order = Pemesanan::where('id', $id)
            ->with(['pemesananLayanan.layanan', 'validasiDokumen', 'pelaporanTeknisi'])
            ->firstOrFail();

        $namaLayananList = $order->pemesananLayanan->map(function ($pl) {
            return $pl->layanan ? $pl->layanan->nama_layanan : $pl->nama_layanan;
        })->filter()->implode(', ');

        if ($tipe === 'surat-jalan') {
            $valSj = $order->getValidasi('surat_jalan');
            $alatList = [];
            $catatanSj = '';

            if ($valSj && !empty($valSj->alasan)) {
                $decoded = json_decode($valSj->alasan, true);
                if (is_array($decoded)) {
                    $alatList = $decoded['alat'] ?? [];
                    $catatanSj = $decoded['catatan'] ?? '';
                }
            }

            // Fallback jika belum ada daftar alat di validasi dokumen
            if (empty($alatList)) {
                if ($order->pelaporanTeknisi->isNotEmpty() && !empty($order->pelaporanTeknisi->first()->nama_alat)) {
                    $alatNames = explode(',', $order->pelaporanTeknisi->first()->nama_alat);
                    foreach ($alatNames as $aName) {
                        $aName = trim($aName);
                        if (!empty($aName)) {
                            $alatList[] = [
                                'nama' => $aName,
                                'jumlah' => 1,
                                'keterangan' => 'Baik',
                            ];
                        }
                    }
                } else {
                    foreach ($order->pemesananLayanan as $pl) {
                        $alatList[] = [
                            'nama' => $pl->nama_layanan,
                            'jumlah' => $pl->jumlah,
                            'keterangan' => 'Baik',
                        ];
                    }
                }
            }

            // Helper bulan romawi
            $romanMonths = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $tglSj = $order->created_at ? $order->created_at : now();
            $bulanRomawi = $romanMonths[(int)$tglSj->format('m')] ?? 'VIII';
            $noSuratJalan = 'NO. ' . $order->id . '/CV.TTM/SJ/' . $bulanRomawi . '/' . $tglSj->format('Y');

            return view('Cetak.surat-jalan', compact('order', 'alatList', 'catatanSj', 'noSuratJalan', 'namaLayananList'));
        }

        if ($tipe === 'berita-acara') {
            $tglBa = $order->created_at ? $order->created_at : now();

            $hariIndo = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $bulanIndo = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            $namaHari = $hariIndo[$tglBa->format('l')] ?? 'Jumat';
            $namaBulan = $bulanIndo[(int)$tglBa->format('m')] ?? 'Agustus';

            // Terbilang function
            $terbilang = function ($angka) use (&$terbilang) {
                $bilang = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
                if ($angka < 12) {
                    return $bilang[$angka];
                } elseif ($angka < 20) {
                    return $terbilang($angka - 10) . ' Belas';
                } elseif ($angka < 100) {
                    return $terbilang((int)($angka / 10)) . ' Puluh' . (($angka % 10 != 0) ? ' ' . $terbilang($angka % 10) : '');
                } elseif ($angka < 200) {
                    return 'Seratus' . (($angka - 100 != 0) ? ' ' . $terbilang($angka - 100) : '');
                } elseif ($angka < 1000) {
                    return $terbilang((int)($angka / 100)) . ' Ratus' . (($angka % 100 != 0) ? ' ' . $terbilang($angka % 100) : '');
                } elseif ($angka < 2000) {
                    return 'Seribu' . (($angka - 1000 != 0) ? ' ' . $terbilang($angka - 1000) : '');
                } elseif ($angka < 1000000) {
                    return $terbilang((int)($angka / 1000)) . ' Ribu' . (($angka % 1000 != 0) ? ' ' . $terbilang($angka % 1000) : '');
                }
                return '';
            };

            $terbilangTanggal = $terbilang((int)$tglBa->format('d'));
            $terbilangTahun = $terbilang((int)$tglBa->format('Y'));
            $tglNumeric = $tglBa->format('d-m-Y');

            // Nomor BAST
            $romanMonths = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $bulanRomawi = $romanMonths[(int)$tglBa->format('m')] ?? 'VIII';
            $noBeritaAcara = 'NO. ' . $order->id . '/CV.TTM/BAST/' . $bulanRomawi . '/' . $tglBa->format('Y');

            return view('Cetak.berita-acara', compact(
                'order', 'namaHari', 'namaBulan', 'terbilangTanggal',
                'terbilangTahun', 'tglNumeric', 'noBeritaAcara'
            ));
        }

        if ($tipe === 'invoice') {
            $subtotal = $order->pemesananLayanan->sum(fn($i) => $i->harga * $i->jumlah);
            $diskon = floatval($order->diskon ?? 0);
            $ppn = $subtotal * 0.11; // PPN 11%
            $pph = $subtotal * 0.02; // PPh 2%
            $totalHarga = max(0, ($subtotal + $ppn - $pph) - $diskon);
            $dp = $totalHarga * 0.50;
            $pelunasan = $totalHarga - $dp;

            // Terbilang function
            $terbilang = function ($angka) use (&$terbilang) {
                $angka = (int)abs($angka);
                $bilang = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
                if ($angka < 12) {
                    return $bilang[$angka];
                } elseif ($angka < 20) {
                    return $terbilang($angka - 10) . ' Belas';
                } elseif ($angka < 100) {
                    return $terbilang((int)($angka / 10)) . ' Puluh' . (($angka % 10 != 0) ? ' ' . $terbilang($angka % 10) : '');
                } elseif ($angka < 200) {
                    return 'Seratus' . (($angka - 100 != 0) ? ' ' . $terbilang($angka - 100) : '');
                } elseif ($angka < 1000) {
                    return $terbilang((int)($angka / 100)) . ' Ratus' . (($angka % 100 != 0) ? ' ' . $terbilang($angka % 100) : '');
                } elseif ($angka < 2000) {
                    return 'Seribu' . (($angka - 1000 != 0) ? ' ' . $terbilang($angka - 1000) : '');
                } elseif ($angka < 1000000) {
                    return $terbilang((int)($angka / 1000)) . ' Ribu' . (($angka % 1000 != 0) ? ' ' . $terbilang($angka % 1000) : '');
                } elseif ($angka < 1000000000) {
                    return $terbilang((int)($angka / 1000000)) . ' Juta' . (($angka % 1000000 != 0) ? ' ' . $terbilang($angka % 1000000) : '');
                } elseif ($angka < 1000000000000) {
                    return $terbilang((int)($angka / 1000000000)) . ' Miliar' . (($angka % 1000000000 != 0) ? ' ' . $terbilang($angka % 1000000000) : '');
                }
                return '';
            };

            $terbilangTotal = trim($terbilang($pelunasan));

            $tglInv = $order->created_at ? $order->created_at : now();
            $bulanIndo = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $tglTerbit = $tglInv->format('d') . ' ' . ($bulanIndo[(int)$tglInv->format('m')] ?? '') . ' ' . $tglInv->format('Y');

            $romanMonths = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $bulanRomawi = $romanMonths[(int)$tglInv->format('m')] ?? 'VIII';
            $noInvoice = 'NO. ' . $order->id . '/CV.TTM/INV/' . $bulanRomawi . '/' . $tglInv->format('Y');

            return view('Cetak.invoice', compact(
                'order', 'subtotal', 'diskon', 'ppn', 'pph', 'totalHarga',
                'dp', 'pelunasan', 'terbilangTotal', 'tglTerbit', 'noInvoice'
            ));
        }

        abort(404, 'Jenis dokumen tidak ditemukan.');
    }
}