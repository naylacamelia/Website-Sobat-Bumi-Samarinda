<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Testimoni extends Model
{
    protected $table = 'testimoni';

    protected $fillable = [
        'nama',
        'peran',
        'isi',
        'foto',
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

    public function getFotoUrlAttribute(): string
    {
        $placeholder = asset('assets/images/avatar-placeholder.svg');

        if (! $this->foto || ! trim((string) $this->foto)) {
            return $placeholder;
        }

        $foto = trim((string) $this->foto);

        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        if (Str::startsWith($foto, ['/storage/', 'storage/'])) {
            return asset(ltrim($foto, '/'));
        }

        if (Str::startsWith($foto, ['/assets/', 'assets/'])) {
            return asset(ltrim($foto, '/'));
        }

        if (Str::startsWith($foto, 'public/')) {
            $foto = Str::after($foto, 'public/');
        }

        /**
         * Kalau DB cuma berisi nama file seperti Abdi.jpg,
         * foto diarahkan ke public/assets/images/profile/Abdi.jpg.
         */
        if (! Str::contains($foto, '/')) {
            return asset('assets/images/profile/' . $foto);
        }

        return asset('storage/' . ltrim($foto, '/'));
    }
}
