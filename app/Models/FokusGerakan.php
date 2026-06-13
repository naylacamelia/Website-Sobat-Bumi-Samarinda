<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FokusGerakan extends Model
{
    protected $table = 'fokus_gerakan';

    protected $fillable = [
        'tipe',
        'nomor',
        'judul',
        'isi',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
