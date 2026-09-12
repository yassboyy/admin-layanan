<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TentangKami extends Model
{
    protected $table = 'tentang_kami';

    protected $fillable = [
        'gambar',
        'deskripsi_profil',
        'visi',
        'misi',
    ];
}
