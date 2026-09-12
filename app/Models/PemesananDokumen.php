<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemesananDokumen extends Model
{
    protected $table = 'pemesanan_dokumens';

    protected $fillable = [
        'pemesanan_id',
        'dokumen_layanan_id',
        'nama_dokumen',
        'file_path',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function dokumenLayanan()
    {
        return $this->belongsTo(DokumenLayanan::class, 'dokumen_layanan_id');
    }
}
