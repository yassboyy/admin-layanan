<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumentasiProyek extends Model
{
    protected $table = 'dokumentasi_proyek';

    protected $fillable = [
        'jenis_layanan',
        'nama_layanan',
        'gambar',
    ];
}
