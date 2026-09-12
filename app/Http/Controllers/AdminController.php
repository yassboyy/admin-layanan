<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Chat;
use App\Models\DokumenLayanan;
use App\Models\DokumentasiProyek;
use App\Models\Layanan;
use App\Models\Pemesanan;
use App\Models\PemesananDokumen;
use App\Models\TentangKami;
use App\Models\User;
use App\Models\ValidasiDokumen;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Dashboard Admin.
     */
    public function dashboard()
    {
        $orders = Pemesanan::with('pemesananLayanan')->latest()->get();
        return view('Admin.dashboard-admin', compact('orders'));
    }

    /**
     * Data Pelanggan — tampilkan semua pelanggan beserta info pemesanan terakhir.
     */
    public function dataPelanggan(Request $request)
    {
        $sort = $request->input('sort', 'terbaru');
        $query = User::where('role', 'pelanggan');

        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'nama_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $pelanggan = $query->get();

        foreach ($pelanggan as $item) {
            $pemesanan = Pemesanan::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)->orWhere('email', $item->email);
            })->latest()->first();
            $item->pemesanan_terakhir = $pemesanan;
            $item->total_pemesanan = Pemesanan::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)->orWhere('email', $item->email);
            })->count();
        }

        if ($sort === 'pesanan_terbanyak') {
            $pelanggan = $pelanggan->sortByDesc('total_pemesanan')->values();
        } elseif ($sort === 'pesanan_tersedikit') {
            $pelanggan = $pelanggan->sortBy('total_pemesanan')->values();
        }

        return view('Admin.data-pelanggan-admin', compact('pelanggan', 'sort'));
    }

    /**
     * Tambah data pelanggan baru oleh Admin.
     */
    public function storePelanggan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'role' => 'pelanggan',
            'password' => bcrypt($request->password),
            'email_verified_at' => now(), // Pelanggan yang ditambahkan admin langsung terverifikasi tanpa OTP email
        ]);

        // Buat sapaan chat awal dari Admin dengan timestamp pendaftaran
        $adminId = getUserId();
        Chat::create([
            'user_id' => $adminId,
            'pelanggan_id' => $user->id,
            'pesan' => "Halo {$user->name}! 👋\nTim Admin kami siap melayani Anda.",
            'created_at' => $user->created_at ?? now(),
            'updated_at' => $user->created_at ?? now(),
        ]);

        // Notifikasi selamat datang ke Pelanggan
        NotifikasiService::kirim($user->id, 'Akun Anda Telah Didaftarkan', 'Selamat datang di CV Tomo Teknik Mandiri, ' . $user->name . '. Akun Anda telah didaftarkan oleh tim Admin kami.', 'success', '/layanan');

        return redirect('/admin/data-pelanggan')->with('success', 'Data pelanggan baru berhasil ditambahkan dan langsung aktif.');
    }

    // ========================================================================
    // PESANAN
    // ========================================================================

    /**
     * Daftar semua pesanan dengan filter pencarian.
     */
    public function pesanan(Request $request)
    {
        $query = Pemesanan::with('pemesananLayanan.layanan');

        // Filter Search (ID Pesanan, Nama Pelanggan, No HP, Layanan)
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $cleanNumeric = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanNumeric) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhereHas('pemesananLayanan', function ($lq) use ($search) {
                      $lq->where('nama_layanan', 'like', "%{$search}%")
                         ->orWhereHas('layanan', function ($mlq) use ($search) {
                             $mlq->where('nama_layanan', 'like', "%{$search}%");
                         });
                  });
                if (!empty($cleanNumeric)) {
                    $q->orWhere('total_harga', 'like', "%{$cleanNumeric}%");
                }
            });
        }

        // Filter Layanan
        if ($request->filled('layanan_id')) {
            $layananId = $request->input('layanan_id');
            $query->whereHas('pemesananLayanan', function ($q) use ($layananId) {
                $q->where('layanan_id', $layananId);
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_tipe', $request->input('status'));
        }

        // Filter Tanggal Pesan (Rentang Tanggal)
        $tglDari = $request->input('tanggal_dari', $request->input('tanggal_mulai'));
        $tglSampai = $request->input('tanggal_sampai', $request->input('tanggal_selesai'));
        if ($tglDari) {
            $query->whereDate('created_at', '>=', $tglDari);
        }
        if ($tglSampai) {
            $query->whereDate('created_at', '<=', $tglSampai);
        }

        // Sort By
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'harga_tertinggi':
                $query->orderByDesc('total_harga');
                break;
            case 'harga_terendah':
                $query->orderBy('total_harga');
                break;
            case 'id_asc':
                $query->orderBy('id_pesanan');
                break;
            case 'id_desc':
                $query->orderByDesc('id_pesanan');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $orders = $query->get();
        $layananList = \App\Models\Layanan::orderBy('nama_layanan')->get();

        return view('Admin.pesanan-admin', compact('orders', 'layananList', 'sort'));
    }

    /**
     * Detail pesanan berdasarkan ID.
     */
    public function detailPesanan($id)
    {
        $order = Pemesanan::where('id', $id)
            ->with(['pemesananLayanan.layanan.dokumenLayanan', 'pemesananDokumen', 'validasiDokumen', 'pelaporanTeknisi'])
            ->firstOrFail();
        $alats = \App\Models\Alat::all();
        return view('Admin.detail-pesanan-admin', compact('order', 'alats'));
    }



    /**
     * Ajukan dokumen ke Direktur untuk validasi.
     */
    public function ajukanDokumen(Request $request, $id)
    {
        $tipe = $request->input('tipe_dokumen');
        $order = Pemesanan::with(['validasiDokumen', 'pelaporanTeknisi', 'pemesananLayanan.layanan'])->findOrFail($id);

        $alasanData = null;

        if ($tipe === 'dokumen_persyaratan') {
            $hasDocs = $order->pemesananDokumen()->whereNotNull('file_path')->exists();
            if (!$hasDocs) {
                return redirect()->back()->withErrors(['dokumen' => 'Belum ada berkas dokumen persyaratan yang diunggah untuk pesanan ini.']);
            }
        } elseif ($tipe === 'surat_jalan') {
            if ($order->isJasaPerizinan()) {
                return redirect()->back()->withErrors(['dokumen' => 'Layanan kategori jasa perizinan tidak memerlukan Surat Jalan.']);
            }
            if (!$order->isSpkApproved() && (int)$order->status_tipe < 2) {
                return redirect()->back()->withErrors(['dokumen' => 'Surat Jalan hanya dapat diajukan setelah SPK disetujui oleh Direktur.']);
            }

            // Simpan detail alat dan catatan surat jalan
            $alatNames = $request->input('alat_nama', []);
            $alatJumlahs = $request->input('alat_jumlah', []);
            $alatKeterangans = $request->input('alat_keterangan', []);
            $catatanSj = $request->input('catatan_surat_jalan', '');

            $items = [];
            if (is_array($alatNames)) {
                foreach ($alatNames as $idx => $nama) {
                    $nama = trim($nama);
                    if (!empty($nama)) {
                        $items[] = [
                            'nama' => $nama,
                            'jumlah' => isset($alatJumlahs[$idx]) ? max(1, (int)$alatJumlahs[$idx]) : 1,
                            'keterangan' => isset($alatKeterangans[$idx]) && !empty($alatKeterangans[$idx]) ? trim($alatKeterangans[$idx]) : 'Baik',
                        ];
                    }
                }
            }

            $alasanData = json_encode([
                'alat' => $items,
                'catatan' => $catatanSj,
            ]);
        } elseif ($tipe === 'berita_acara') {
            if (!$order->isTeknisiReportApproved()) {
                return redirect()->back()->withErrors(['dokumen' => 'Berita Acara (BAST) hanya dapat diajukan setelah Laporan Teknisi dikonfirmasi dan disetujui oleh Direktur.']);
            }
        } elseif ($tipe === 'invoice') {
            if (!$order->isBeritaAcaraApproved()) {
                return redirect()->back()->withErrors(['dokumen' => 'Invoice hanya dapat diajukan setelah Berita Acara (BAST) disetujui oleh Direktur.']);
            }
        }

        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $order->id,
                'tipe_dokumen' => $tipe,
            ],
            [
                'status_dokumen' => 'menunggu',
                'alasan' => $alasanData,
            ]
        );

        $namaDok = match ($tipe) {
            'dokumen_persyaratan' => 'Dokumen Persyaratan Layanan',
            'surat_jalan' => 'Surat Jalan',
            'berita_acara' => 'Berita Acara (BAST)',
            'invoice' => 'Invoice Tagihan',
            default => 'Dokumen'
        };

        // Notifikasi ke Direktur: dokumen menunggu validasi
        NotifikasiService::kirimKeRole('direktur', $namaDok . ' Menunggu Validasi', $namaDok . ' untuk pesanan ' . $order->id_pesanan . ' telah diajukan oleh Admin dan menunggu validasi.', 'warning', '/direktur/validasi-dokumen');

        return redirect()->back()->with('success', $namaDok . ' berhasil diajukan ke Direktur untuk divalidasi!');
    }

    /**
     * Konfirmasi / Setujui bukti transfer pembayaran (Bukti DP 50% atau Bukti Pelunasan) oleh Admin.
     */
    public function konfirmasiBuktiPembayaran(Request $request, $id)
    {
        $tipe = $request->input('tipe_dokumen'); // 'bukti_dp' or 'bukti_lunas'
        $order = Pemesanan::findOrFail($id);

        if (!in_array($tipe, ['bukti_dp', 'bukti_lunas'])) {
            return redirect()->back()->withErrors(['dokumen' => 'Tipe bukti pembayaran tidak valid.']);
        }

        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $order->id,
                'tipe_dokumen' => $tipe,
            ],
            [
                'status_dokumen' => 'disetujui',
                'alasan' => null,
            ]
        );

        if ($tipe === 'bukti_lunas') {
            $order->status_tipe = 4; // Transisi status ke Selesai
            $order->save();
        }

        $namaBukti = ($tipe === 'bukti_dp') ? 'Bukti Transfer DP (50%)' : 'Bukti Transfer Pelunasan';

        // Notifikasi ke Pelanggan: bukti pembayaran dikonfirmasi
        if ($order->user_id) {
            NotifikasiService::kirim($order->user_id, $namaBukti . ' Dikonfirmasi', $namaBukti . ' untuk pesanan ' . $order->id_pesanan . ' telah dikonfirmasi oleh Admin.', 'success', '/pelanggan/detail-pesanan/' . $order->id);
        }

        return redirect()->back()->with('success', $namaBukti . ' berhasil dikonfirmasi dan disetujui!');
    }

    /**
     * Tolak bukti transfer pembayaran (Bukti DP 50% atau Bukti Pelunasan) oleh Admin.
     */
    public function tolakBuktiPembayaran(Request $request, $id)
    {
        $tipe = $request->input('tipe_dokumen'); // 'bukti_dp' or 'bukti_lunas'
        $alasan = $request->input('alasan');

        $order = Pemesanan::findOrFail($id);

        if (!in_array($tipe, ['bukti_dp', 'bukti_lunas'])) {
            return redirect()->back()->withErrors(['dokumen' => 'Tipe bukti pembayaran tidak valid.']);
        }

        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $order->id,
                'tipe_dokumen' => $tipe,
            ],
            [
                'status_dokumen' => 'ditolak',
                'alasan' => $alasan,
            ]
        );

        $namaBukti = ($tipe === 'bukti_dp') ? 'Bukti Transfer DP (50%)' : 'Bukti Transfer Pelunasan';

        // Notifikasi ke Pelanggan: bukti pembayaran ditolak
        if ($order->user_id) {
            $pesanNotif = $alasan
                ? $namaBukti . ' untuk pesanan ' . $order->id_pesanan . ' ditolak oleh Admin. Alasan: ' . $alasan
                : $namaBukti . ' untuk pesanan ' . $order->id_pesanan . ' ditolak oleh Admin. Silakan unggah kembali bukti pembayaran yang valid.';
            NotifikasiService::kirim($order->user_id, $namaBukti . ' Ditolak', $pesanNotif, 'danger', '/pelanggan/detail-pesanan/' . $order->id);
        }

        return redirect()->back()->with('success', $namaBukti . ' telah ditolak.');
    }


    // ========================================================================
    // DATA LAYANAN (CRUD)
    // ========================================================================

    /**
     * Daftar semua layanan.
     */
    public function dataLayanan(Request $request)
    {
        $sort = $request->input('sort', 'terbaru');
        $query = Layanan::with('dokumenLayanan');

        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'nama_asc':
                $query->orderBy('nama_layanan', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_layanan', 'desc');
                break;
            case 'harga_tertinggi':
                $query->orderByDesc('harga');
                break;
            case 'harga_terendah':
                $query->orderBy('harga', 'asc');
                break;
            case 'kategori_asc':
                $query->orderBy('kategori_layanan', 'asc')->orderBy('nama_layanan', 'asc');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $layanan = $query->get();
        return view('Admin.data-layanan-admin', compact('layanan', 'sort'));
    }

    /**
     * Tambah layanan baru.
     */
    public function storeLayanan(Request $request)
    {
        $request->validate([
            'kategori_layanan' => 'required|in:jasa_service,jasa_perizinan',
            'nama_layanan' => 'required|string|max:255',
            'jenis_layanan' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5048',
            'dokumen_persyaratan' => 'nullable|array',
            'dokumen_persyaratan.*' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['kategori_layanan', 'nama_layanan', 'jenis_layanan', 'harga', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/layanan'), $filename);
            $data['gambar'] = 'uploads/layanan/' . $filename;
        }

        $layanan = Layanan::create($data);

        // Simpan dokumen persyaratan jika ada
        if ($request->has('dokumen_persyaratan') && is_array($request->dokumen_persyaratan)) {
            foreach ($request->dokumen_persyaratan as $namaDokumen) {
                $nama = trim($namaDokumen);
                if (!empty($nama)) {
                    DokumenLayanan::create([
                        'layanan_id' => $layanan->id,
                        'nama_dokumen' => $nama,
                    ]);
                }
            }
        }

        return redirect('/admin/data-layanan')->with('success', 'Layanan dan dokumen persyaratan berhasil ditambahkan.');
    }

    /**
     * Update layanan.
     */
    public function updateLayanan(Request $request, $id)
    {
        $request->validate([
            'kategori_layanan' => 'required|in:jasa_service,jasa_perizinan',
            'nama_layanan' => 'required|string|max:255',
            'jenis_layanan' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5048',
            'dokumen_persyaratan' => 'nullable|array',
            'dokumen_persyaratan.*' => 'nullable|string|max:255',
        ]);

        $layanan = Layanan::findOrFail($id);
        $data = $request->only(['kategori_layanan', 'nama_layanan', 'jenis_layanan', 'harga', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            if ($layanan->gambar && file_exists(public_path($layanan->gambar))) {
                @unlink(public_path($layanan->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/layanan'), $filename);
            $data['gambar'] = 'uploads/layanan/' . $filename;
        }

        $layanan->update($data);

        // Sinkronisasi dokumen persyaratan
        DokumenLayanan::where('layanan_id', $layanan->id)->delete();
        if ($request->has('dokumen_persyaratan') && is_array($request->dokumen_persyaratan)) {
            foreach ($request->dokumen_persyaratan as $namaDokumen) {
                $nama = trim($namaDokumen);
                if (!empty($nama)) {
                    DokumenLayanan::create([
                        'layanan_id' => $layanan->id,
                        'nama_dokumen' => $nama,
                    ]);
                }
            }
        }

        return redirect('/admin/data-layanan')->with('success', 'Layanan dan dokumen persyaratan berhasil diperbarui.');
    }

    /**
     * Hapus layanan.
     */
    public function destroyLayanan($id)
    {
        $layanan = Layanan::findOrFail($id);
        if ($layanan->gambar && file_exists(public_path($layanan->gambar))) {
            @unlink(public_path($layanan->gambar));
        }
        $layanan->delete();
        return redirect('/admin/data-layanan')->with('success', 'Layanan berhasil dihapus.');
    }

    // ========================================================================
    // DATA STAFF (CRUD)
    // ========================================================================

    /**
     * Daftar semua staff (non-pelanggan).
     */
    public function dataStaff(Request $request)
    {
        $sort = $request->input('sort', 'terbaru');
        $query = User::where('role', '!=', 'pelanggan');

        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'nama_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'role_asc':
                $query->orderBy('role', 'asc')->orderBy('name', 'asc');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $staffs = $query->get();
        return view('Admin.data-satff-admin', compact('staffs', 'sort'));
    }

    /**
     * Tambah staff baru.
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,direktur,managerteknisi',
            'password' => 'required|string|min:6',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);
        return redirect('/admin/data-staff')->with('success', 'Data staff berhasil ditambahkan.');
    }

    /**
     * Update data staff.
     */
    public function updateStaff(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,direktur,managerteknisi',
            'password' => 'nullable|string|min:6',
        ]);
        $staff = User::findOrFail($id);
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        if ($request->filled('password')) {
            $staff->update(['password' => bcrypt($request->password)]);
        }
        return redirect('/admin/data-staff')->with('success', 'Data staff berhasil diperbarui.');
    }

    /**
     * Hapus data staff.
     */
    public function destroyStaff($id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();
        return redirect('/admin/data-staff')->with('success', 'Data staff berhasil dihapus.');
    }

    // ========================================================================
    // DATA ALAT (CRUD)
    // ========================================================================

    /**
     * Daftar semua alat.
     */
    public function dataAlat(Request $request)
    {
        $sort = $request->input('sort', 'terbaru');
        $query = Alat::query();

        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'nama_asc':
                $query->orderBy('nama_alat', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_alat', 'desc');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $alats = $query->get();
        return view('Admin.data-alat-admin', compact('alats', 'sort'));
    }

    /**
     * Tambah alat baru.
     */
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5048',
        ]);
        $data = $request->only(['nama_alat', 'deskripsi']);
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/alat'), $filename);
            $data['gambar'] = 'uploads/alat/' . $filename;
        }
        Alat::create($data);
        return redirect('/admin/data-alat')->with('success', 'Data alat berhasil ditambahkan.');
    }

    /**
     * Update data alat.
     */
    public function updateAlat(Request $request, $id)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5048',
        ]);
        $alat = Alat::findOrFail($id);
        $data = $request->only(['nama_alat', 'deskripsi']);
        if ($request->hasFile('gambar')) {
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                @unlink(public_path($alat->gambar));
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/alat'), $filename);
            $data['gambar'] = 'uploads/alat/' . $filename;
        }
        $alat->update($data);
        return redirect('/admin/data-alat')->with('success', 'Data alat berhasil diperbarui.');
    }

    /**
     * Hapus data alat.
     */
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);
        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            @unlink(public_path($alat->gambar));
        }
        $alat->delete();
        return redirect('/admin/data-alat')->with('success', 'Data alat berhasil dihapus.');
    }

    // ========================================================================
    // DOKUMENTASI PROYEK (CRUD)
    // ========================================================================

    /**
     * Daftar dokumentasi proyek.
     */
    public function dokumentasiProyek(Request $request)
    {
        $sort = $request->input('sort', 'terbaru');
        $query = DokumentasiProyek::query();

        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'nama_asc':
                $query->orderBy('nama_layanan', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_layanan', 'desc');
                break;
            case 'jenis_asc':
                $query->orderBy('jenis_layanan', 'asc')->orderBy('nama_layanan', 'asc');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $dokumentasis = $query->get();
        return view('Admin.dokumentasi-proyek-admin', compact('dokumentasis', 'sort'));
    }

    /**
     * Tambah dokumentasi proyek baru.
     */
    public function storeDokumentasi(Request $request)
    {
        $request->validate([
            'jenis_layanan' => 'required|string|max:255',
            'nama_layanan' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5048',
        ]);
        $data = $request->only(['jenis_layanan', 'nama_layanan']);
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/dokumentasi'), $filename);
            $data['gambar'] = 'uploads/dokumentasi/' . $filename;
        }
        DokumentasiProyek::create($data);
        return redirect('/admin/dokumentasi-proyek')->with('success', 'Dokumentasi proyek berhasil ditambahkan.');
    }

    /**
     * Update dokumentasi proyek.
     */
    public function updateDokumentasi(Request $request, $id)
    {
        $request->validate([
            'jenis_layanan' => 'required|string|max:255',
            'nama_layanan' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5048',
        ]);
        $dok = DokumentasiProyek::findOrFail($id);
        $data = $request->only(['jenis_layanan', 'nama_layanan']);
        if ($request->hasFile('gambar')) {
            if ($dok->gambar && file_exists(public_path($dok->gambar))) {
                @unlink(public_path($dok->gambar));
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/dokumentasi'), $filename);
            $data['gambar'] = 'uploads/dokumentasi/' . $filename;
        }
        $dok->update($data);
        return redirect('/admin/dokumentasi-proyek')->with('success', 'Dokumentasi proyek berhasil diperbarui.');
    }

    /**
     * Hapus dokumentasi proyek.
     */
    public function destroyDokumentasi($id)
    {
        $dok = DokumentasiProyek::findOrFail($id);
        if ($dok->gambar && file_exists(public_path($dok->gambar))) {
            @unlink(public_path($dok->gambar));
        }
        $dok->delete();
        return redirect('/admin/dokumentasi-proyek')->with('success', 'Dokumentasi proyek berhasil dihapus.');
    }

    // ========================================================================
    // CHAT ADMIN & PELANGGAN
    // ========================================================================

    /**
     * Halaman chat admin terhubung dengan pelanggan.
     */
    public function chat(Request $request)
    {
        $pelanggans = User::where('role', 'pelanggan')->get();
        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : getUserId();

        foreach ($pelanggans as $p) {
            $lastChat = Chat::where('pelanggan_id', $p->id)->latest()->first();
            if (!$lastChat) {
                $lastChat = Chat::create([
                    'user_id' => $adminId,
                    'pelanggan_id' => $p->id,
                    'pesan' => "Halo {$p->name}! 👋\nTim Admin kami siap melayani Anda.",
                    'created_at' => $p->created_at ?? now(),
                    'updated_at' => $p->created_at ?? now(),
                ]);
            }
            $p->last_chat = $lastChat;
        }

        // Urutkan pelanggan yang memiliki chat terbaru di atas
        $pelanggans = $pelanggans->sortByDesc(function ($p) {
            return $p->last_chat ? $p->last_chat->created_at : Carbon::createFromTimestamp(0);
        })->values();

        $selectedPelangganId = $request->query('pelanggan_id');
        $selectedPelanggan = $selectedPelangganId ? User::find($selectedPelangganId) : null;
        
        // Jika pelanggan dipilih dan belum ada pesan, buat pesan greeting otomatis dengan waktu registrasi pelanggan
        if ($selectedPelanggan) {
            $hasChat = Chat::where('pelanggan_id', $selectedPelanggan->id)->exists();
            if (!$hasChat) {
                Chat::create([
                    'user_id' => $adminId,
                    'pelanggan_id' => $selectedPelanggan->id,
                    'pesan' => "Halo {$selectedPelanggan->name}! 👋\nTim Admin kami siap melayani Anda.",
                    'created_at' => $selectedPelanggan->created_at ?? now(),
                    'updated_at' => $selectedPelanggan->created_at ?? now(),
                ]);
            }
        }

        $messages = $selectedPelanggan
            ? Chat::where('pelanggan_id', $selectedPelanggan->id)->with('user')->orderBy('created_at', 'asc')->get()
            : collect();

        $latestOrder = $selectedPelanggan
            ? Pemesanan::where('user_id', $selectedPelanggan->id)->latest()->first()
            : null;

        return view('Admin.chat-admin', compact('pelanggans', 'selectedPelanggan', 'messages', 'latestOrder'));
    }

    /**
     * Kirim pesan balasan dari Admin ke pelanggan.
     */
    public function sendChat(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|integer',
            'pesan' => 'required|string|max:3000',
        ]);

        $pelangganId = $request->input('pelanggan_id');
        $currentUserId = getUserId();

        $chat = Chat::create([
            'user_id' => $currentUserId,
            'pelanggan_id' => $pelangganId,
            'pesan' => trim($request->input('pesan')),
        ]);

        // Notifikasi ke Pelanggan: ada balasan chat dari Admin
        NotifikasiService::kirim($pelangganId, 'Pesan Baru dari Admin', 'Admin Support: "' . Str::limit($chat->pesan, 50) . '"', 'info', '/pelanggan/chat');

        if ($request->expectsJson() || $request->ajax()) {
            $created = Carbon::parse($chat->created_at);
            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->id,
                    'pesan' => $chat->pesan,
                    'sender_id' => $chat->user_id,
                    'sender_name' => 'Admin Support',
                    'is_me' => true,
                    'time' => $created->format('H:i'),
                    'date' => $created->format('d M Y'),
                ],
            ]);
        }

        return redirect('/admin/chat?pelanggan_id=' . $pelangganId);
    }

    /**
     * Dapatkan daftar pesan (AJAX polling) untuk admin.
     */
    public function getChatMessages(Request $request)
    {
        $pelangganId = $request->query('pelanggan_id');
        if (!$pelangganId) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $currentUserId = getUserId();
        
        $hasChat = Chat::where('pelanggan_id', $pelangganId)->exists();
        if (!$hasChat) {
            $adminUser = User::where('role', 'admin')->first();
            $adminId = $adminUser ? $adminUser->id : $currentUserId;
            $pelanggan = User::find($pelangganId);
            $pelangganName = $pelanggan ? $pelanggan->name : 'Pelanggan';

            Chat::create([
                'user_id' => $adminId,
                'pelanggan_id' => $pelangganId,
                'pesan' => "Halo {$pelangganName}! 👋\nTim Admin kami siap melayani Anda.",
                'created_at' => $pelanggan ? ($pelanggan->created_at ?? now()) : now(),
                'updated_at' => $pelanggan ? ($pelanggan->created_at ?? now()) : now(),
            ]);
        }

        $chats = Chat::where('pelanggan_id', $pelangganId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = [];
        foreach ($chats as $c) {
            $created = Carbon::parse($c->created_at);
            $formatted[] = [
                'id' => $c->id,
                'pesan' => $c->pesan,
                'sender_id' => $c->user_id,
                'sender_name' => ($c->user && $c->user->role === 'admin') ? 'Admin Support' : ($c->user ? $c->user->name : 'Pelanggan'),
                'is_me' => ($c->user_id == $currentUserId),
                'time' => $created->format('H:i'),
                'date' => $created->format('d M Y'),
            ];
        }

        return response()->json([
            'success' => true,
            'messages' => $formatted,
        ]);
    }

    /**
     * Halaman tentang kami (admin).
     */
    public function tentangKami()
    {
        $tentangKami = TentangKami::first();
        if (!$tentangKami) {
            $tentangKami = TentangKami::create([
                'gambar' => 'images/logo.png',
                'deskripsi_profil' => "CV Tomo Teknik Mandiri didirikan dengan tekad kuat untuk menghadirkan layanan teknik profesional yang aman, handal, dan berorientasi pada kepuasan pelanggan. Kami memiliki tim spesialis di bidang kelistrikan panel, pengeboran sumur dalam (deep well), dan pemeliharaan mesin-mesin industri/genset.\n\nKami melayani klien dari kalangan perorangan, komersial, maupun pabrik-pabrik berskala besar yang membutuhkan penanganan presisi tinggi dan pemeliharaan alat secara berkala.",
                'visi' => "Menjadi perusahaan penyedia jasa teknik terintegrasi yang paling terpercaya, unggul dalam pelayanan, dan berkomitmen kuat terhadap kualitas kerja guna memajukan industri dan infrastruktur nasional.",
                'misi' => "Menyediakan layanan teknik dengan standar mutu kerja, keamanan, dan efisiensi waktu yang tinggi.\nMengembangkan kompetensi SDM teknisi secara berkelanjutan agar mampu beradaptasi dengan perkembangan teknologi industri terbaru.\nMembangun kemitraan jangka panjang yang transparan dan saling menguntungkan dengan seluruh klien kami.",
            ]);
        }
        return view('Admin.tentang-kami-admin', compact('tentangKami'));
    }

    /**
     * Simpan perubahan data Tentang Kami.
     */
    public function updateTentangKami(Request $request)
    {
        $request->validate([
            'deskripsi_profil' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ], [
            'deskripsi_profil.required' => 'Deskripsi profil perusahaan wajib diisi.',
            'visi.required' => 'Visi perusahaan wajib diisi.',
            'misi.required' => 'Misi perusahaan wajib diisi.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp, svg.',
            'gambar.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $tentangKami = TentangKami::first();
        if (!$tentangKami) {
            $tentangKami = new TentangKami();
            $tentangKami->gambar = 'images/logo.png';
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/tentang-kami');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $tentangKami->gambar = 'uploads/tentang-kami/' . $filename;
        }

        $tentangKami->deskripsi_profil = $request->deskripsi_profil;
        $tentangKami->visi = $request->visi;
        $tentangKami->misi = $request->misi;
        $tentangKami->save();

        return redirect()->back()->with('success', 'Data Tentang Kami berhasil diperbarui.');
    }
}
