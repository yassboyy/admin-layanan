<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemesananLayanan extends Model
{
    protected $table = 'pemesanan_layanan';

    protected $fillable = [
        'pemesanan_id',
        'layanan_id',
        'nama_layanan',
        'jenis_layanan',
        'jumlah',
        'harga',
        'diskon',
        'deskripsi',
        'gambar',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
