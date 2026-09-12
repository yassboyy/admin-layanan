<?php

namespace App\Http\Controllers;

use App\Models\DokumentasiProyek;
use App\Models\Layanan;
use App\Models\TentangKami;

class HomeController extends Controller
{
    /**
     * Halaman utama (HomePage) dengan 3 layanan & 3 dokumentasi proyek terbaru.
     */
    public function index()
    {
        $layanan = Layanan::take(3)->get();
        $dokumentasi = DokumentasiProyek::latest()->take(3)->get();
        return view('HomePage.home', compact('layanan', 'dokumentasi'));
    }

    /**
     * Halaman Tentang Kami.
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
        return view('HomePage.tentang-kami', compact('tentangKami'));
    }

    /**
     * Halaman daftar semua Layanan.
     */
    public function layanan()
    {
        $layanan = Layanan::all();
        return view('HomePage.layanan', compact('layanan'));
    }

    /**
     * Halaman detail layanan berdasarkan ID.
     */
    public function detailLayanan($id)
    {
        $layanan = Layanan::with('dokumenLayanan')->findOrFail($id);
        return view('HomePage.detail-layanan', compact('layanan'));
    }

    /**
     * Halaman Dokumentasi Proyek.
     */
    public function dokumentasi()
    {
        $dokumentasi = DokumentasiProyek::latest()->get();
        return view('HomePage.dokumentasi', compact('dokumentasi'));
    }

    /**
     * Halaman Kontak.
     */
    public function kontak()
    {
        return view('HomePage.kontak');
    }
}
