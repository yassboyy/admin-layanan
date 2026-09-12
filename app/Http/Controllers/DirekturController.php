<?php

namespace App\Http\Controllers;

use App\Models\PelaporanManagerTeknisi;
use App\Models\Pemesanan;
use App\Models\User;
use App\Models\ValidasiDokumen;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class DirekturController extends Controller
{
    /**
     * Dashboard Direktur.
     */
    public function dashboard()
    {
        $orders = Pemesanan::with('pemesananLayanan')->latest()->get();
        return view('Direktur.dashboard-direktur', compact('orders'));
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
                $q->where('user_id', $item->id)->orWhere('email', $item->email)->orWhere('nama_pelanggan', $item->name);
            })->latest()->first();
            $item->pemesanan_terakhir = $pemesanan;
            $item->total_pemesanan = Pemesanan::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)->orWhere('email', $item->email)->orWhere('nama_pelanggan', $item->name);
            })->count();
        }

        if ($sort === 'pesanan_terbanyak') {
            $pelanggan = $pelanggan->sortByDesc('total_pemesanan')->values();
        } elseif ($sort === 'pesanan_tersedikit') {
            $pelanggan = $pelanggan->sortBy('total_pemesanan')->values();
        }

        return view('Direktur.data-pelanggan-direktur', compact('pelanggan', 'sort'));
    }

    /**
     * Daftar validasi dokumen dengan filter pencarian.
     */
    public function validasiDokumen(Request $request)
    {
        $query = ValidasiDokumen::with(['pemesanan.pemesananLayanan.layanan', 'pemesanan.pemesananDokumen'])
            ->whereNotIn('tipe_dokumen', ['laporan_teknisi', 'bukti_dp', 'bukti_lunas']);

        // Filter Search (ID Pesanan, Pelanggan)
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->whereHas('pemesanan', function ($q) use ($search) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter Tipe Dokumen
        if ($request->filled('tipe_dokumen')) {
            $query->where('tipe_dokumen', $request->input('tipe_dokumen'));
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_dokumen', $request->input('status'));
        }

        // Filter Tanggal Diajukan
        $tglAjukanDari = $request->input('tanggal_diajukan_dari', $request->input('tanggal_diajukan_mulai'));
        $tglAjukanSampai = $request->input('tanggal_diajukan_sampai', $request->input('tanggal_diajukan_selesai'));
        if ($tglAjukanDari) {
            $query->whereDate('created_at', '>=', $tglAjukanDari);
        }
        if ($tglAjukanSampai) {
            $query->whereDate('created_at', '<=', $tglAjukanSampai);
        }

        // Filter Tanggal Divalidasi
        $tglValDari = $request->input('tanggal_validasi_dari', $request->input('tanggal_validasi_mulai'));
        $tglValSampai = $request->input('tanggal_validasi_sampai', $request->input('tanggal_validasi_selesai'));
        if ($tglValDari) {
            $query->where('status_dokumen', '!=', 'menunggu')
                  ->whereDate('updated_at', '>=', $tglValDari);
        }
        if ($tglValSampai) {
            $query->where('status_dokumen', '!=', 'menunggu')
                  ->whereDate('updated_at', '<=', $tglValSampai);
        }

        // Sort By
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $validasiList = $query->get();
        return view('Direktur.validasi-dokumen-direktur', compact('validasiList', 'sort'));
    }

    /**
     * Detail validasi dokumen berdasarkan ID.
     */
    public function detailValidasiDokumen($id)
    {
        $validasi = ValidasiDokumen::find($id);
        if ($validasi) {
            $order = Pemesanan::where('id', $validasi->pemesanan_id)
                ->with(['pemesananLayanan.layanan', 'pemesananDokumen', 'validasiDokumen', 'pelaporanTeknisi'])
                ->firstOrFail();
        } else {
            $order = Pemesanan::where('id', $id)
                ->with(['pemesananLayanan.layanan', 'pemesananDokumen', 'validasiDokumen', 'pelaporanTeknisi'])
                ->firstOrFail();
            $validasi = $order->validasiDokumen->first();
        }
        return view('Direktur.detail-validasi-dokumen-direktur', compact('order', 'validasi'));
    }

    /**
     * Setujui dokumen validasi.
     */
    public function setujuiDokumen(Request $request, $pemesanan_id)
    {
        $tipe = $request->input('tipe_dokumen');
        $existing = ValidasiDokumen::where('pemesanan_id', $pemesanan_id)->where('tipe_dokumen', $tipe)->first();
        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $pemesanan_id,
                'tipe_dokumen' => $tipe,
            ],
            [
                'status_dokumen' => 'disetujui',
                'alasan' => ($tipe === 'surat_jalan' && $existing) ? $existing->alasan : null,
            ]
        );

        $order = Pemesanan::with('pemesananLayanan')->find($pemesanan_id);
        if ($order) {
            if ($tipe == 'bukti_po') {
                $subtotalJasa = $order->pemesananLayanan->sum(fn($i) => $i->harga * $i->jumlah);
                $diskon = max(0, floatval($order->diskon_diajukan ?? 0));
                $ppnJasa = $subtotalJasa * 0.11;
                $pphJasa = $subtotalJasa * 0.02;
                $totalHarga = max(0, ($subtotalJasa + $ppnJasa - $pphJasa) - $diskon);

                $order->diskon = $diskon;
                $order->total_harga = $totalHarga;
                $order->save();
            } elseif ($tipe == 'bukti_surat_perjanjian_kerja') {
                // Ketika SPK sudah divalidasi/disetujui oleh Direktur, status menjadi Proses Pengerjaan (status_tipe = 2)
                if ($order->status_tipe < 2) {
                    $order->status_tipe = 2;
                    $order->save();
                }
            } elseif ($tipe == 'surat_jalan') {
                if ($order->status_tipe < 2) {
                    $order->status_tipe = 2; // Transisi ke Proses Pengerjaan
                    $order->save();
                }
            } elseif ($tipe == 'berita_acara') {
                // BAST disetujui, siap untuk pengajuan Invoice oleh Admin
            } elseif ($tipe == 'invoice') {
                $order->status_tipe = 3; // Transisi ke Menunggu Pelunasan
                $order->save();
            } elseif ($tipe == 'bukti_lunas') {
                $order->status_tipe = 4; // Transisi ke Selesai
                $order->save();
            }

            // Notifikasi: dokumen disetujui
            $namaDok = match ($tipe) {
                'bukti_po' => 'Dokumen PO',
                'dokumen_persyaratan' => 'Dokumen Persyaratan Layanan',
                'bukti_surat_perjanjian_kerja' => 'SPK',
                'surat_jalan' => 'Surat Jalan',
                'berita_acara' => 'Berita Acara (BAST)',
                'invoice' => 'Invoice',
                'bukti_lunas' => 'Bukti Pelunasan',
                default => 'Dokumen',
            };

            // Notifikasi ke Admin
            NotifikasiService::kirimKeRole('admin', $namaDok . ' Disetujui Direktur', $namaDok . ' untuk pesanan ' . $order->id_pesanan . ' telah disetujui oleh Direktur.', 'success', '/admin/detail-pesanan/' . $order->id);

            // Notifikasi ke Pelanggan
            if ($order->user_id) {
                $pesanPelanggan = ($tipe === 'invoice')
                    ? 'Invoice untuk pesanan ' . $order->id_pesanan . ' telah disetujui Direktur. Silakan unduh invoice dan lakukan pembayaran pelunasan.'
                    : $namaDok . ' untuk pesanan ' . $order->id_pesanan . ' telah disetujui.';
                NotifikasiService::kirim($order->user_id, $namaDok . ' Disetujui', $pesanPelanggan, 'success', '/pelanggan/detail-pesanan/' . $order->id);
            }

            // Jika SPK disetujui, beri tahu Manager Teknisi bahwa proyek siap dikerjakan
            if ($tipe === 'bukti_surat_perjanjian_kerja') {
                NotifikasiService::kirimKeRole('managerteknisi', 'Proyek Baru Siap Dikerjakan', 'SPK untuk pesanan ' . $order->id_pesanan . ' telah disetujui Direktur. Silakan mulai pengerjaan dan buat laporan.', 'info', '/manager/pelaporan');
            }
        }

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui!');
    }

    /**
     * Tolak dokumen validasi.
     */
    public function tolakDokumen(Request $request, $pemesanan_id)
    {
        $tipe = $request->input('tipe_dokumen');
        $alasan = $request->input('alasan', 'Dokumen tidak sesuai atau buram');
        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $pemesanan_id,
                'tipe_dokumen' => $tipe,
            ],
            [
                'status_dokumen' => 'ditolak',
                'alasan' => $alasan,
            ]
        );

        $order = Pemesanan::with('pemesananLayanan')->find($pemesanan_id);
        if ($order) {
            if ($tipe == 'bukti_po') {
                $subtotalJasa = $order->pemesananLayanan->sum(fn($i) => $i->harga * $i->jumlah);
                $ppnJasa = $subtotalJasa * 0.11;
                $pphJasa = $subtotalJasa * 0.02;
                $totalHarga = max(0, $subtotalJasa + $ppnJasa - $pphJasa);
                $order->diskon = 0;
                $order->total_harga = $totalHarga;
                $order->save();
            } elseif ($tipe == 'bukti_surat_perjanjian_kerja' && $order->status_tipe == 2) {
                $order->status_tipe = 1; // Kembalikan ke Proses Pemesanan
                $order->save();
            } elseif ($tipe == 'berita_acara') {
                if ($order->status_tipe == 3) {
                    $order->status_tipe = 2; // Kembalikan ke Proses Pengerjaan
                    $order->save();
                }
                // Hapus auto-approval invoice jika berita acara ditolak
                ValidasiDokumen::where('pemesanan_id', $pemesanan_id)
                    ->where('tipe_dokumen', 'invoice')
                    ->delete();
            }

            // Notifikasi: dokumen ditolak
            $namaDok = match ($tipe) {
                'bukti_po' => 'Dokumen PO',
                'dokumen_persyaratan' => 'Dokumen Persyaratan Layanan',
                'bukti_surat_perjanjian_kerja' => 'SPK',
                'surat_jalan' => 'Surat Jalan',
                'berita_acara' => 'Berita Acara (BAST)',
                'invoice' => 'Invoice',
                default => 'Dokumen',
            };

            // Notifikasi ke Admin
            NotifikasiService::kirimKeRole('admin', $namaDok . ' Ditolak Direktur', $namaDok . ' untuk pesanan ' . $order->id_pesanan . ' ditolak oleh Direktur. Alasan: ' . $alasan, 'danger', '/admin/detail-pesanan/' . $order->id);

            // Notifikasi ke Pelanggan
            if ($order->user_id) {
                NotifikasiService::kirim($order->user_id, $namaDok . ' Ditolak', $namaDok . ' untuk pesanan ' . $order->id_pesanan . ' ditolak. Alasan: ' . $alasan, 'danger', '/pelanggan/detail-pesanan/' . $order->id);
            }
        }

        return redirect()->back()->with('success', 'Dokumen berhasil ditolak.');
    }

    /**
     * Halaman Laporan Pekerjaan Teknisi — melihat dan mengonfirmasi pekerjaan dari Manager Teknisi.
     */
    public function laporanTeknisi(Request $request)
    {
        $query = PelaporanManagerTeknisi::with(['pemesanan.pemesananLayanan.layanan', 'pemesanan.validasiDokumen']);

        // Search (ID Pesanan, Pelanggan, Alat)
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                  ->orWhereHas('pemesanan', function ($pq) use ($search) {
                      $pq->where('id_pesanan', 'like', "%{$search}%")
                         ->orWhere('nama_pelanggan', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Layanan
        if ($request->filled('layanan_id')) {
            $layananId = $request->input('layanan_id');
            $query->whereHas('pemesanan.pemesananLayanan', function ($q) use ($layananId) {
                $q->where('layanan_id', $layananId);
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'menunggu') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('pemesanan.validasiDokumen', function ($vq) {
                        $vq->where('tipe_dokumen', 'laporan_teknisi');
                    })->orWhereHas('pemesanan.validasiDokumen', function ($vq) {
                        $vq->where('tipe_dokumen', 'laporan_teknisi')->where('status_dokumen', 'menunggu');
                    });
                });
            } elseif ($status === 'disetujui') {
                $query->whereHas('pemesanan.validasiDokumen', function ($vq) {
                    $vq->where('tipe_dokumen', 'laporan_teknisi')->where('status_dokumen', 'disetujui');
                });
            } elseif ($status === 'ditolak') {
                $query->whereHas('pemesanan.validasiDokumen', function ($vq) {
                    $vq->where('tipe_dokumen', 'laporan_teknisi')->where('status_dokumen', 'ditolak');
                });
            }
        }

        // Filter Tanggal Selesai
        $tglSelesaiDari = $request->input('tanggal_selesai_dari', $request->input('tanggal_selesai_mulai'));
        $tglSelesaiSampai = $request->input('tanggal_selesai_sampai', $request->input('tanggal_selesai_akhir'));
        if ($tglSelesaiDari) {
            $query->whereDate('tanggal_selesai', '>=', $tglSelesaiDari);
        }
        if ($tglSelesaiSampai) {
            $query->whereDate('tanggal_selesai', '<=', $tglSelesaiSampai);
        }

        // Sort By
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $reports = $query->get();

        $allReports = PelaporanManagerTeknisi::with('pemesanan.validasiDokumen')->get();
        $totalLaporan = $allReports->count();
        $menungguKonfirmasi = $allReports->filter(function ($item) {
            $val = $item->pemesanan ? $item->pemesanan->getValidasi('laporan_teknisi') : null;
            return !$val || $val->status_dokumen === 'menunggu';
        })->count();
        $dikonfirmasi = $allReports->filter(function ($item) {
            $val = $item->pemesanan ? $item->pemesanan->getValidasi('laporan_teknisi') : null;
            return $val && $val->status_dokumen === 'disetujui';
        })->count();
        $ditolak = $allReports->filter(function ($item) {
            $val = $item->pemesanan ? $item->pemesanan->getValidasi('laporan_teknisi') : null;
            return $val && $val->status_dokumen === 'ditolak';
        })->count();

        $layananList = \App\Models\Layanan::orderBy('nama_layanan')->get();

        return view('Direktur.laporan-teknisi-direktur', compact('reports', 'totalLaporan', 'menungguKonfirmasi', 'dikonfirmasi', 'ditolak', 'layananList', 'sort'));
    }

    /**
     * Setujui / Konfirmasi laporan pengerjaan teknisi.
     */
    public function konfirmasiLaporanTeknisi(Request $request, $id)
    {
        $laporan = PelaporanManagerTeknisi::with('pemesanan')->findOrFail($id);
        
        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $laporan->pemesanan_id,
                'tipe_dokumen' => 'laporan_teknisi',
            ],
            [
                'status_dokumen' => 'disetujui',
                'alasan' => null,
            ]
        );

        // Notifikasi ke Manager Teknisi: laporan disetujui
        NotifikasiService::kirimKeRole('managerteknisi', 'Laporan Teknisi Dikonfirmasi', 'Laporan pengerjaan untuk pesanan ' . ($laporan->pemesanan ? $laporan->pemesanan->id_pesanan : '') . ' telah dikonfirmasi dan disetujui oleh Direktur.', 'success', '/manager/pelaporan');

        // Notifikasi ke Pelanggan: laporan teknisi dikonfirmasi (pelunasan bisa diupload)
        if ($laporan->pemesanan && $laporan->pemesanan->user_id) {
            NotifikasiService::kirim($laporan->pemesanan->user_id, 'Laporan Teknisi Dikonfirmasi', 'Laporan pengerjaan untuk pesanan ' . $laporan->pemesanan->id_pesanan . ' telah dikonfirmasi. Anda sekarang bisa mengupload bukti pelunasan.', 'success', '/pelanggan/detail-pesanan/' . $laporan->pemesanan->id);
        }

        // Notifikasi ke Admin: laporan teknisi dikonfirmasi
        NotifikasiService::kirimKeRole('admin', 'Laporan Teknisi Dikonfirmasi', 'Laporan pengerjaan untuk pesanan ' . ($laporan->pemesanan ? $laporan->pemesanan->id_pesanan : '') . ' dikonfirmasi Direktur.', 'success', $laporan->pemesanan ? '/admin/detail-pesanan/' . $laporan->pemesanan->id : null);


        return redirect()->back()->with('success', 'Laporan pengerjaan teknisi untuk pesanan ' . ($laporan->pemesanan ? $laporan->pemesanan->id_pesanan : '') . ' berhasil dikonfirmasi dan disetujui!');
    }


    /**
     * Tolak laporan pengerjaan teknisi.
     */
    public function tolakLaporanTeknisi(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ], [
            'alasan.required' => 'Alasan penolakan laporan pengerjaan teknisi wajib diisi.',
        ]);

        $laporan = PelaporanManagerTeknisi::with('pemesanan')->findOrFail($id);

        ValidasiDokumen::updateOrCreate(
            [
                'pemesanan_id' => $laporan->pemesanan_id,
                'tipe_dokumen' => 'laporan_teknisi',
            ],
            [
                'status_dokumen' => 'ditolak',
                'alasan' => $request->alasan,
            ]
        );

        // Notifikasi ke Manager Teknisi: laporan ditolak
        NotifikasiService::kirimKeRole('managerteknisi', 'Laporan Teknisi Ditolak', 'Laporan pengerjaan untuk pesanan ' . ($laporan->pemesanan ? $laporan->pemesanan->id_pesanan : '') . ' ditolak oleh Direktur. Alasan: ' . $request->alasan, 'danger', '/manager/pelaporan');

        // Notifikasi ke Admin
        NotifikasiService::kirimKeRole('admin', 'Laporan Teknisi Ditolak', 'Laporan pengerjaan untuk pesanan ' . ($laporan->pemesanan ? $laporan->pemesanan->id_pesanan : '') . ' ditolak Direktur.', 'warning', $laporan->pemesanan ? '/admin/detail-pesanan/' . $laporan->pemesanan->id : null);


        return redirect()->back()->with('success', 'Laporan pengerjaan teknisi untuk pesanan ' . ($laporan->pemesanan ? $laporan->pemesanan->id_pesanan : '') . ' telah ditolak.');
    }
}
