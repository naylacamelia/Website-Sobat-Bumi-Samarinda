<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Kategori;

class DashboardController extends Controller
{
    public function index()
    {
        $berita = Berita::with(['kategori', 'pengguna'])
            ->latest()
            ->take(10)
            ->get();

        $articles = $berita->map(function (Berita $item) {
            return [
                'id' => $item->id,
                'slug' => $item->slug,
                'title' => $item->judul,
                'summary' => $item->ringkasan,
                'category' => $item->kategori->nama ?? '-',
                'status' => $item->status,
                'featured' => $item->unggulan,
                'date' => optional($item->tanggal_terbit ?? $item->created_at)->translatedFormat('d M Y'),
                'author' => $item->penulis ?? $item->pengguna->nama ?? 'Admin SBS',
                'image' => $item->gambar_url,
                'views' => $item->jumlah_dilihat,
            ];
        });

        $totalArticles = Berita::count();
        $published = Berita::where('status', 'published')->count();
        $drafts = Berita::where('status', 'draft')->count();
        $totalViews = Berita::sum('jumlah_dilihat');
        $totalKategori = Kategori::where('tipe', 'berita')->count();

        return view('admin.dashboard', [
            'articles' => $articles,
            'recentArticles' => $articles,

            'totalArticles' => $totalArticles,
            'totalArtikel' => $totalArticles,

            'published' => $published,
            'totalPublished' => $published,

            'drafts' => $drafts,
            'totalDraft' => $drafts,

            'totalViews' => $totalViews,
            'totalKategori' => $totalKategori,
        ]);
    }
}