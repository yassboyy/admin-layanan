<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Staff;
use App\Models\Alat;
use App\Models\DokumentasiProyek;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sample Staff
        if (Staff::count() === 0) {
            Staff::create([
                'nama' => 'Supriadi Santoso',
                'jabatan' => 'Teknisi Lapangan Utama',
                'spesialisasi' => 'Kelistrikan & Panel Kontrol',
                'no_hp' => '0852-1111-2222',
            ]);
            Staff::create([
                'nama' => 'Heri Setiawan',
                'jabatan' => 'Asisten Teknisi',
                'spesialisasi' => 'Instalasi Kabel & Wiring',
                'no_hp' => '0852-3333-4444',
            ]);
        }

        // Sample Alat
        if (Alat::count() === 0) {
            Alat::create([
                'nama_alat' => 'Panel Box Steel 40x60',
                'gambar' => 'images/alat/panel_box.jpg',
                'deskripsi' => 'Panel box besi powder coating 40x60cm Schneider Electric',
            ]);
            Alat::create([
                'nama_alat' => 'Kabel NYY 4x16mm Supreme',
                'gambar' => 'images/alat/kabel_nyy.jpg',
                'deskripsi' => 'Kabel tanah Supreme 4x16mm SNI per rol 100m',
            ]);
        }

        // Sample Dokumentasi Proyek
        if (DokumentasiProyek::count() === 0) {
            DokumentasiProyek::create([
                'jenis_layanan' => 'Jasa Service',
                'nama_layanan' => 'Pemasangan Panel Listrik Industri 3 Phase',
                'gambar' => 'images/dokumentasi/panel_3phase.jpg',
            ]);
            DokumentasiProyek::create([
                'jenis_layanan' => 'Jasa Perizinan',
                'nama_layanan' => 'Surat Izin Pengambilan Air Tanah (SIPA)',
                'gambar' => 'images/dokumentasi/sipa_bor.jpg',
            ]);
        }
    }
}
