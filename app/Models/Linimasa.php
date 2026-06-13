<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linimasa extends Model
{
    protected $table = 'linimasa';

    protected $fillable = [
        'tahun',
        'judul',
        'deskripsi',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
