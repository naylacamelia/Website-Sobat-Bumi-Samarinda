<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'kategori_id',
        'judul',
        'slug',
        'deskripsi',
        'gambar',
        'tanggal',
        'lokasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function getGambarUrlAttribute(): string
    {
        $placeholder = asset('assets/images/placeholder-news.svg');

        if (!$this->gambar || !trim((string) $this->gambar)) {
            return $placeholder;
        }

        $gambar = trim((string) $this->gambar);

        if (Str::startsWith($gambar, ['http://', 'https://'])) {
            return $gambar;
        }

        if (Str::startsWith($gambar, ['/storage/', 'storage/'])) {
            return asset(ltrim($gambar, '/'));
        }

        if (Str::startsWith($gambar, ['/assets/', 'assets/'])) {
            return asset(ltrim($gambar, '/'));
        }

        if (Str::startsWith($gambar, 'public/')) {
            $gambar = Str::after($gambar, 'public/');
        }

        return asset('storage/' . ltrim($gambar, '/'));
    }
}
