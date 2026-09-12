<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenLayanan extends Model
{
    protected $table = 'dokumen_layanans';

    protected $fillable = [
        'layanan_id',
        'nama_dokumen',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}
