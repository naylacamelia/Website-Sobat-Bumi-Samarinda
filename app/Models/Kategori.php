<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = [
        'nama',
        'slug',
        'tipe',
    ];

    public function berita()
    {
        return $this->hasMany(Berita::class, 'kategori_id');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'kategori_id');
    }
}
