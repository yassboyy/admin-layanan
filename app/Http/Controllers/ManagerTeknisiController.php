<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\PelaporanManagerTeknisi;
use App\Models\Pemesanan;
use App\Models\ValidasiDokumen;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class ManagerTeknisiController extends Controller
{
    /**
     * Dashboard Manager Teknisi.
     */
    public function dashboard()
    {
        $ordersReady = Pemesanan::where(function ($query) {
            $query->whereHas('validasiDokumen', function ($q) {
                $q->where('tipe_dokumen', 'bukti_surat_perjanjian_kerja')->where('status_dokumen', 'disetujui');
            })->orWhere('status_tipe', '>=', 2);
        })->with(['validasiDokumen', 'pelaporanTeknisi'])->get();

        $totalProyek = $ordersReady->count();
        $menungguLapor = $ordersReady->filter(fn($o) => $o->pelaporanTeknisi->isEmpty())->count();
        $selesaiLapor = $ordersReady->filter(fn($o) => $o->isTeknisiReportApproved())->count();

        $reports = PelaporanManagerTeknisi::with('pemesanan')->latest()->take(5)->get();

        return view('ManagerTeknisi.dashboard-managerteknisi', compact('totalProyek', 'menungguLapor', 'selesaiLapor', 'reports'));
    }

    /**
     * Halaman pelaporan — daftar pesanan yang SPK-nya telah disetujui direktur dengan filter pencarian.
     */
    public function pelaporan(Request $request)
    {
        // Hanya pesanan yang SPK-nya sudah disetujui oleh Direktur (atau status >= 2)
        $query = Pemesanan::where(function ($query) {
            $query->whereHas('validasiDokumen', function ($q) {
                $q->where('tipe_dokumen', 'bukti_surat_perjanjian_kerja')->where('status_dokumen', 'disetujui');
            })->orWhere('status_tipe', '>=', 2);
        })
        ->with(['pemesananLayanan.layanan', 'pelaporanTeknisi', 'validasiDokumen']);

        // Filter Search (ID Pesanan, Pelanggan)
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter Layanan
        if ($request->filled('layanan_id')) {
            $layananId = $request->input('layanan_id');
            $query->whereHas('pemesananLayanan', function ($q) use ($layananId) {
                $q->where('layanan_id', $layananId);
            });
        }

        // Filter Status Laporan
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'belum_lapor') {
                $query->whereDoesntHave('pelaporanTeknisi');
            } elseif ($status === 'menunggu') {
                $query->whereHas('pelaporanTeknisi')->where(function ($q) {
                    $q->whereDoesntHave('validasiDokumen', function ($vq) {
                        $vq->where('tipe_dokumen', 'laporan_teknisi');
                    })->orWhereHas('validasiDokumen', function ($vq) {
                        $vq->where('tipe_dokumen', 'laporan_teknisi')->where('status_dokumen', 'menunggu');
                    });
                });
            } elseif ($status === 'disetujui') {
                $query->whereHas('validasiDokumen', function ($vq) {
                    $vq->where('tipe_dokumen', 'laporan_teknisi')->where('status_dokumen', 'disetujui');
                });
            } elseif ($status === 'ditolak') {
                $query->whereHas('validasiDokumen', function ($vq) {
                    $vq->where('tipe_dokumen', 'laporan_teknisi')->where('status_dokumen', 'ditolak');
                });
            }
        }

        // Filter Tanggal Pesan
        $tglPesanDari = $request->input('tanggal_pesan_dari', $request->input('tanggal_pesan_mulai'));
        $tglPesanSampai = $request->input('tanggal_pesan_sampai', $request->input('tanggal_pesan_selesai'));
        if ($tglPesanDari) {
            $query->whereDate('created_at', '>=', $tglPesanDari);
        }
        if ($tglPesanSampai) {
            $query->whereDate('created_at', '<=', $tglPesanSampai);
        }

        // Filter Tanggal Selesai Laporan
        $tglSelesaiDari = $request->input('tanggal_selesai_dari', $request->input('tanggal_selesai_mulai'));
        $tglSelesaiSampai = $request->input('tanggal_selesai_sampai', $request->input('tanggal_selesai_akhir'));
        if ($tglSelesaiDari) {
            $query->whereHas('pelaporanTeknisi', function ($q) use ($tglSelesaiDari) {
                $q->whereDate('tanggal_selesai', '>=', $tglSelesaiDari);
            });
        }
        if ($tglSelesaiSampai) {
            $query->whereHas('pelaporanTeknisi', function ($q) use ($tglSelesaiSampai) {
                $q->whereDate('tanggal_selesai', '<=', $tglSelesaiSampai);
            });
        }

        // Sort By
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
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
        $alats = Alat::all();
        $layananList = \App\Models\Layanan::orderBy('nama_layanan')->get();

        return view('ManagerTeknisi.pelaporan-managerteknisi', compact('orders', 'alats', 'layananList', 'sort'));
    }

    /**
     * Simpan laporan pengerjaan teknisi.
     */
    public function storePelaporan(Request $request)
    {
        $request->validate([
            'pemesanan_id' => 'required|exists:pemesanan,id',
            'nama_alat' => 'required|string|max:500',
            'tanggal_selesai' => 'required|date',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:5048',
        ], [
            'pemesanan_id.required' => 'Pemesanan ID wajib dipilih.',
            'nama_alat.required' => 'Alat yang digunakan wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai pengerjaan wajib diisi.',
            'gambar.required' => 'Foto bukti pengerjaan lapangan wajib diunggah.',
            'gambar.image' => 'File bukti harus berupa gambar (JPG, PNG, WEBP).',
        ]);

        $order = Pemesanan::with(['pemesananLayanan.layanan', 'pelaporanTeknisi', 'validasiDokumen'])->findOrFail($request->pemesanan_id);

        if (!$order->isSpkApproved() && (int)$order->status_tipe < 2) {
            return redirect()->back()->withErrors(['spk' => 'Laporan pengerjaan hanya dapat dikirim jika SPK telah disetujui oleh Direktur.']);
        }

        $namaLayanan = $order->pemesananLayanan->map(function ($pl) {
            return $pl->layanan ? $pl->layanan->nama_layanan : $pl->nama_layanan;
        })->filter()->implode(', ');

        if (empty($namaLayanan)) {
            $namaLayanan = 'Layanan Proyek';
        }

        $jenisLayanan = $order->pemesananLayanan->map(function ($pl) {
            return $pl->layanan ? $pl->layanan->jenis_layanan : $pl->jenis_layanan;
        })->filter()->implode(', ');

        if (empty($jenisLayanan)) {
            $jenisLayanan = 'Teknik';
        }

        $gambarPath = 'uploads/laporan/default.jpg';
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/laporan'), $filename);
            $gambarPath = 'uploads/laporan/' . $filename;
        }

        // Update or create pelaporan
        PelaporanManagerTeknisi::updateOrCreate(
            ['pemesanan_id' => $order->id],
            [
                'nama_layanan' => $namaLayanan,
                'jenis_layanan' => $jenisLayanan,
                'no_surat_jalan' => $order->id_pesanan ?? ('SJ-' . $order->id),
                'nama_alat' => $request->nama_alat,
                'gambar' => $gambarPath,
                'tanggal_selesai' => $request->tanggal_selesai,
            ]
        );

        // Buat pengajuan validasi dokumen 'laporan_teknisi' dengan status 'menunggu'
        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $order->id,
                'tipe_dokumen' => 'laporan_teknisi',
            ],
            [
                'status_dokumen' => 'menunggu',
                'alasan' => null,
            ]
        );

        // Notifikasi ke Direktur: laporan teknisi baru menunggu konfirmasi
        NotifikasiService::kirimKeRole('direktur', 'Laporan Teknisi Baru', 'Laporan pengerjaan teknisi untuk pesanan ' . $order->id_pesanan . ' telah dikirim dan menunggu konfirmasi.', 'info', '/direktur/laporan-teknisi');

        // Notifikasi ke Admin: laporan teknisi dikirim
        NotifikasiService::kirimKeRole('admin', 'Laporan Teknisi Dikirim', 'Manager Teknisi mengirim laporan pengerjaan untuk pesanan ' . $order->id_pesanan . '.', 'info', '/admin/detail-pesanan/' . $order->id);

        return redirect()->back()->with('success', 'Laporan pengerjaan teknisi untuk pesanan ' . $order->id_pesanan . ' berhasil dikirim dan menunggu konfirmasi!');
    }
}
