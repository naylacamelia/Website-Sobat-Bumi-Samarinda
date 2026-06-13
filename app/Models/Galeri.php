<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Galeri extends Model
{
    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'gambar',
        'alt_gambar',
        'lokasi',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function getGambarUrlAttribute(): string
    {
        $placeholder = asset('assets/images/placeholder-news.svg');

        if (! $this->gambar || ! trim((string) $this->gambar)) {
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
