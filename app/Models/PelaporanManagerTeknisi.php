<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaporanManagerTeknisi extends Model
{
    protected $table = 'pelaporan_manager_teknisi';

    protected $fillable = [
        'pemesanan_id',
        'nama_layanan',
        'jenis_layanan',
        'no_surat_jalan',
        'nama_alat',
        'gambar',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_selesai' => 'datetime',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
