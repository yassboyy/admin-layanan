<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';

    protected $fillable = [
        'user_id',
        'nama_pelanggan',
        'no_hp',
        'email',
        'alamat',
        'id_pesanan',
        'catatan',
        'diskon',
        'diskon_diajukan',
        'total_harga',
        'status_tipe',
        'bukti_po',
        'bukti_surat_perjanjian_kerja',
        'bukti_dp',
        'bukti_lunas',
    ];

    public static function generateIdPesanan(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $code = 'TTM-' . substr(str_shuffle($letters), 0, 4);
        } while (self::where('id_pesanan', $code)->exists());

        return $code;
    }

    protected static function booted()
    {
        static::creating(function ($pemesanan) {
            if (empty($pemesanan->id_pesanan)) {
                $pemesanan->id_pesanan = self::generateIdPesanan();
            }
        });
    }

    public function getNoPoAttribute()
    {
        return $this->id_pesanan;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pemesananLayanan()
    {
        return $this->hasMany(PemesananLayanan::class, 'pemesanan_id');
    }

    public function validasiDokumen()
    {
        return $this->hasMany(ValidasiDokumen::class, 'pemesanan_id');
    }

    public function pelaporanTeknisi()
    {
        return $this->hasMany(PelaporanManagerTeknisi::class, 'pemesanan_id');
    }

    public function pemesananDokumen()
    {
        return $this->hasMany(PemesananDokumen::class, 'pemesanan_id');
    }

    public function getValidasiStatus($tipe)
    {
        $v = $this->validasiDokumen->firstWhere('tipe_dokumen', $tipe);
        return $v ? $v->status_dokumen : null;
    }

    public function getValidasi($tipe)
    {
        return $this->validasiDokumen->firstWhere('tipe_dokumen', $tipe);
    }

    public function isJasaPerizinan()
    {
        if ($this->pemesananLayanan->isEmpty()) {
            return false;
        }

        $hasService = $this->pemesananLayanan->contains(function ($item) {
            return $item->layanan && $item->layanan->kategori_layanan === 'jasa_service';
        });

        $hasPerizinan = $this->pemesananLayanan->contains(function ($item) {
            return $item->layanan && $item->layanan->kategori_layanan === 'jasa_perizinan';
        });

        if ($hasPerizinan && !$hasService) {
            return true;
        }

        if (!$hasService) {
            return $this->pemesananLayanan->every(function ($item) {
                $name = strtolower($item->nama_layanan . ' ' . $item->jenis_layanan);
                return str_contains($name, 'izin') || str_contains($name, 'perizinan') || str_contains($name, 'sipa') || str_contains($name, 'amdal') || str_contains($name, 'dokumen');
            });
        }

        return false;
    }

    public function isDokumenPersyaratanApproved()
    {
        return $this->getValidasiStatus('dokumen_persyaratan') === 'disetujui';
    }

    public function hasUploadedAllPersyaratan()
    {
        if (!$this->pemesananDokumen || $this->pemesananDokumen->isEmpty()) {
            return false;
        }
        return $this->pemesananDokumen->every(fn($d) => !empty($d->file_path));
    }

    public function isSpkApproved()
    {
        return $this->getValidasiStatus('bukti_surat_perjanjian_kerja') === 'disetujui';
    }

    public function canShowSuratJalan()
    {
        if ($this->isJasaPerizinan()) {
            return false;
        }
        return $this->isSpkApproved();
    }

    public function isSuratJalanApproved()
    {
        return $this->getValidasiStatus('surat_jalan') === 'disetujui';
    }

    public function isTeknisiReported()
    {
        return $this->pelaporanTeknisi()->exists();
    }

    public function hasTeknisiReport()
    {
        return $this->pelaporanTeknisi()->exists();
    }

    public function isTeknisiReportApproved()
    {
        $v = $this->getValidasiStatus('laporan_teknisi');
        return $v === 'disetujui';
    }

    public function canShowBeritaAcara()
    {
        if ($this->isJasaPerizinan()) {
            return $this->isSpkApproved() && $this->isTeknisiReportApproved();
        }
        return $this->canShowSuratJalan() && $this->isTeknisiReportApproved();
    }

    public function isBeritaAcaraApproved()
    {
        return $this->getValidasiStatus('berita_acara') === 'disetujui';
    }

    public function isInvoiceApproved()
    {
        return $this->getValidasiStatus('invoice') === 'disetujui';
    }

    public function isPelunasanApproved()
    {
        return $this->getValidasiStatus('bukti_lunas') === 'disetujui';
    }

    public function canUploadPelunasan()
    {
        return $this->isInvoiceApproved();
    }

    public function getStatusLabelAttribute()
    {
        return match ((int)$this->status_tipe) {
            1 => 'Proses Pemesanan',
            2 => 'Proses Pengerjaan',
            3 => 'Menunggu Pelunasan',
            4 => 'Selesai',
            default => 'Proses Pemesanan',
        };
    }
}
