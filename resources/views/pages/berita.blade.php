@extends('layouts.app')

@section('title', 'Berita & Media — Sobat Bumi Samarinda')
@section('meta_desc', 'Kumpulan berita, kegiatan, edukasi lingkungan, dan kabar terbaru Sobat Bumi Samarinda.')

@section('content')

@php
$newsList = collect($newsList ?? [])->values()->all();
$featuredNews = $featuredNews ?? ($newsList[0] ?? null);
$popularNews = collect($popularNews ?? [])->values()->all();
$latestArticles = collect($latestArticles ?? [])->values()->all();
$categoryMeta = $categoryMeta ?? ['all' => 'Semua'];
@endphp
<section class="sb-news-page">
    <div class="container">

        <section class="sb-news-hero-centered">
            <div class="sb-news-hero-glow"></div>

            <div class="sb-news-hero-copy-centered">
                <div class="sb-news-hero-copy">
                    <span class="sb-news-category-badge">Kabar Sobat Bumi</span>

                    <h1>
                        Berita & <span>Informasi</span>
                    </h1>

                    <p class="sb-news-hero-subtitle">
                        Ikuti kabar terbaru seputar aksi lingkungan, edukasi energi bersih,
                        kolaborasi komunitas, dan inspirasi hijau dari Sobat Bumi Samarinda.
                    </p>
                </div>
            </div>
        </section>
        @if($featuredNews)
        <article class="sb-featured-news">
            <img src="{{ $featuredNews['img'] }}" alt="{{ $featuredNews['title'] }}">
            <div class="sb-featured-news-overlay"></div>

            <div class="sb-featured-news-content">
                <div class="sb-featured-meta">
                    <span class="badge">{{ $featuredNews['category'] }}</span>
                    <p class="date">{{ $featuredNews['date'] }}</p>
                </div>

                <h2>{{ $featuredNews['title'] }}</h2>
                <p class="desc">{{ $featuredNews['desc'] }}</p>

                <a href="{{ route('berita.detail', $featuredNews['id']) }}" class="btn-readmore">
                    Baca Selengkapnya
                    <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18">
                        <path d="M13.5 4.5l8 8-8 8M21.5 12.5H3" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </svg>
                </a>
            </div>

            <div class="sb-featured-dots" aria-label="Slider berita pilihan">
                <button type="button" class="active" aria-label="Berita pilihan 1"></button>
                <button type="button" aria-label="Berita pilihan 2"></button>
                <button type="button" aria-label="Berita pilihan 3"></button>
            </div>
        </article>
        @endif
<section class="sb-news-toolbar">
    <div class="sb-news-search" role="search" aria-label="Cari artikel berita">
        <input
            type="search"
            id="newsSearchInput"
            placeholder="Cari artikel, kegiatan, atau informasi..."
            aria-label="Cari artikel berita"
            autocomplete="off">

        <button type="button" data-news-search-submit>
            Search
        </button>
    </div>

    <div class="sb-news-category-select">
        <label for="newsCategorySelect"></label>

        <div class="sb-news-select-wrap">
            <select id="newsCategorySelect" aria-label="Filter kategori berita">
                <option value="all">Semua Berita</option>

                @foreach($categoryMeta as $key => $label)
                    @continue($key === 'all')

                    <option value="{{ $key }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <span class="sb-news-select-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M7 10l5 5 5-5"
                        stroke="currentColor"
                        stroke-width="2.3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </span>
        </div>
    </div>
</section>

        <section class="sb-news-content-layout">

            <main class="sb-news-list">
                <div class="sb-news-list-grid" id="newsListGrid">
                    @foreach($newsList as $news)
                    <article
                        class="sb-news-item-card"
                        data-category="{{ $news['key'] }}"
                        data-title="{{ strtolower($news['title']) }}"
                        data-content="{{ strtolower($news['title'] . ' ' . $news['desc'] . ' ' . $news['category']) }}">
                        <div class="sb-news-item-image">
                            <img src="{{ $news['img'] }}" alt="{{ $news['title'] }}">

                            <span class="sb-news-category">
                                {{ $news['category'] }}
                            </span>
                        </div>

                        <div class="sb-news-item-body">
                            <p class="sb-news-item-date">{{ $news['date'] }}</p>

                            <h3>{{ $news['title'] }}</h3>

                            <p>{{ $news['desc'] }}</p>

                            <a href="{{ route('berita.detail', $news['id']) }}">
                                Baca Selengkapnya
                                <span>→</span>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>

                <p class="sb-news-empty" id="newsEmptyState" hidden>
                    Tidak ada berita yang sesuai dengan pencarian atau kategori tersebut.
                </p>

                <nav
                    class="sb-news-pagination"
                    id="newsPagination"
                    aria-label="Navigasi halaman berita"></nav>
            </main>

            <aside class="sb-news-sidebar">
                <section class="sb-news-side-card">
                    <h2>Berita Populer</h2>

                    <div class="sb-popular-list">
                        @foreach($popularNews as $item)
                        <a href="{{ route('berita.detail', $item['id']) }}" class="sb-popular-item">
                            <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}">

                            <span>
                                <strong>{{ $item['title'] }}</strong>
                                <small>{{ $item['date'] }}</small>
                            </span>
                        </a>
                        @endforeach
                    </div>
                </section>

                <section class="sb-news-side-card">
                    <div class="sb-side-head">
                        <h2>Artikel Terbaru</h2>
                        <a href="{{ route('berita') }}">Lihat Semua</a>
                    </div>

                    <div class="sb-latest-list">
                        @foreach($latestArticles as $item)
                        <a href="{{ route('berita.detail', $item['id']) }}" class="sb-latest-item">
                            <div class="sb-latest-item-copy">
                                <span class="sb-latest-item-badge">{{ $item['category'] }}</span>
                                <strong>{{ $item['title'] }}</strong>
                                <small>{{ $item['date'] }}</small>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </section>

        <section class="sb-newsletter-card">
            <div class="sb-newsletter-visual" aria-hidden="true">
                <span></span>
            </div>

            <div class="sb-newsletter-copy">
                <h2>Dapatkan update berita lewat email</h2>
                <p>
                    Masukkan email kamu agar kami bisa mengirimkan informasi terbaru
                    seputar kegiatan, edukasi lingkungan, dan kabar Sobat Bumi Samarinda.
                </p>
            </div>

            <form class="sb-newsletter-form" id="newsEmailForm">
                <input
                    type="email"
                    id="newsEmailInput"
                    placeholder="Masukkan email Anda"
                    aria-label="Masukkan email Anda"
                    required>

                <button type="submit">
                    Kirim Email
                    <span>→</span>
                </button>

                <p class="sb-newsletter-message" id="newsEmailMessage" hidden></p>
            </form>
        </section>
    </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection