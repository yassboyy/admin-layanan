<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'kategori_layanan',
        'nama_layanan',
        'jenis_layanan',
        'harga',
        'deskripsi',
        'gambar',
    ];

    public function dokumenLayanan()
    {
        return $this->hasMany(DokumenLayanan::class, 'layanan_id');
    }
}
