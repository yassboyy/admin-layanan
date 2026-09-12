<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiDokumen extends Model
{
    protected $table = 'validasi_dokumen';

    protected $fillable = [
        'pemesanan_id',
        'tipe_dokumen',
        'status_dokumen',
        'alasan',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
