<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'kategori_id',
        'pengguna_id',
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'gambar',
        'alt_gambar',
        'penulis',
        'meta_deskripsi',
        'tag',
        'unggulan',
        'status',
        'tanggal_terbit',
        'jumlah_dilihat',
    ];

    protected $casts = [
        'tag' => 'array',
        'unggulan' => 'boolean',
        'tanggal_terbit' => 'datetime',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
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

    public function getImageUrlAttribute(): string
    {
        return $this->gambar_url;
    }
}
