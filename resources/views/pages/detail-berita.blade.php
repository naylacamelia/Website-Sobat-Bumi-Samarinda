@extends('layouts.app')

@section('title', data_get($article ?? null, 'title', 'Detail Berita') . ' — Sobat Bumi Samarinda')

@section('content')

@php
    $articleTitle = data_get($article ?? null, 'title', 'Penanaman 500 Bibit Mangrove di Pesisir Sungai Mahakam');
    $articleAuthor = data_get($article ?? null, 'author', 'Admin SBS');
    $articleDate = data_get($article ?? null, 'date', '12 Desember 2024');
    $articleCategory = data_get($article ?? null, 'category', 'Kegiatan');

    $articleImage = data_get(
        $article ?? null,
        'image',
        data_get($article ?? null, 'img', asset('assets/images/news/news3.jpg'))
    );

    $articleCaption = data_get($article ?? null, 'caption', 'Kegiatan Sobat Bumi Samarinda.');
    $articleContentHtml = data_get($article ?? null, 'content_html', '<p>Konten artikel belum tersedia.</p>');
    $readTime = data_get($article ?? null, 'read_time', '5 menit baca');

    $tags = collect(data_get($article ?? null, 'tags', [
        'Lingkungan',
        'Relawan',
        'Samarinda',
        'Aksi',
    ]))
        ->filter()
        ->values()
        ->all();

    $relatedNews = collect($relatedNews ?? [
        [
            'id' => 'workshop-daur-ulang-sampah-plastik',
            'slug' => 'workshop-daur-ulang-sampah-plastik',
            'title' => 'Workshop Daur Ulang Sampah Plastik untuk Pelajar SMA',
            'date' => '5 Desember 2024',
            'category' => 'Edukasi',
            'img' => asset('assets/images/news/news1.JPG'),
        ],
        [
            'id' => 'kelas-lingkungan-di-sekolah',
            'slug' => 'kelas-lingkungan-di-sekolah',
            'title' => 'Kelas Lingkungan Hadir di 5 Sekolah Samarinda',
            'date' => '20 November 2024',
            'category' => 'Lingkungan',
            'img' => asset('assets/images/news/news2.JPG'),
        ],
        [
            'id' => 'panduan-hidup-zero-waste',
            'slug' => 'panduan-hidup-zero-waste',
            'title' => 'Panduan Hidup Zero Waste ala Pelajar Samarinda',
            'date' => '10 November 2024',
            'category' => 'Inspirasi',
            'img' => asset('assets/images/news/news3.jpg'),
        ],
    ])
        ->map(function ($news) {
            return [
                'id' => data_get($news, 'id'),
                'slug' => data_get($news, 'slug'),
                'title' => data_get($news, 'title', data_get($news, 'judul', 'Berita Sobat Bumi')),
                'date' => data_get($news, 'date', data_get($news, 'tanggal', '')),
                'category' => data_get($news, 'category', data_get($news, 'kategori', 'Berita')),
                'img' => data_get(
                    $news,
                    'img',
                    data_get($news, 'image', data_get($news, 'gambar_url', asset('assets/images/news/news3.jpg')))
                ),
            ];
        })
        ->filter(fn ($news) => filled(data_get($news, 'title')))
        ->take(3)
        ->values()
        ->all();
@endphp

<section class="sb-article-page">
    <div class="container">
        <div class="sb-article-layout">
            <main class="sb-article-main">
                <article class="sb-article">
                    <header class="sb-article-header">
                        <span class="sb-article-badge">
                            {{ $articleCategory }}
                        </span>

                        <h1>{{ $articleTitle }}</h1>

                        <div class="sb-article-meta">
                            <span>
                                <strong>Sobat Bumi Samarinda</strong>
                            </span>

                            <span class="sb-article-meta-dot" aria-hidden="true"></span>

                            <span>
                                Ditulis oleh {{ $articleAuthor }}
                            </span>

                            <span class="sb-article-meta-dot" aria-hidden="true"></span>

                            <span>
                                {{ $articleDate }}
                            </span>

                            <span class="sb-article-meta-dot" aria-hidden="true"></span>

                            <span>
                                {{ $readTime }}
                            </span>
                        </div>
                    </header>

                    <figure class="sb-article-cover">
                        <img
                            src="{{ $articleImage }}"
                            alt="{{ $articleTitle }}"
                            loading="lazy"
                            data-fallback="{{ asset('assets/images/news/news3.jpg') }}"
                            onerror="this.onerror=null;this.src=this.dataset.fallback;"
                        >

                        @if(filled($articleCaption))
                            <figcaption>
                                {{ $articleCaption }}
                            </figcaption>
                        @endif
                    </figure>

                    <div class="sb-article-body">
                        {!! $articleContentHtml !!}
                    </div>

                    <div class="sb-article-footer-actions">
                        @if(count($tags))
                            <div class="sb-article-tags">
                                <span>Tag</span>

                                @foreach($tags as $tag)
                                    <a href="{{ route('berita') }}?search={{ urlencode($tag) }}">
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="sb-article-share">
                            <span class="sb-article-share-label">
                                Bagikan artikel
                            </span>

                            <div class="sb-article-share-buttons">
                                <button type="button" data-share="facebook" aria-label="Bagikan ke Facebook">
                                    Facebook
                                </button>

                                <button type="button" data-share="twitter" aria-label="Bagikan ke X">
                                    X
                                </button>

                                <button type="button" data-share="whatsapp" aria-label="Bagikan ke WhatsApp">
                                    WhatsApp
                                </button>

                                <button type="button" data-share="copy" aria-label="Salin link artikel">
                                    Salin Link
                                </button>
                            </div>
                        </div>

                        <a href="{{ route('berita') }}" class="sb-article-back" aria-label="Kembali ke halaman berita">
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <path
            d="M15 6L9 12L15 18"
            fill="none"
            stroke="currentColor"
            stroke-width="2.4"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
      <span>Kembali</span>
</a>
                    </div>
                </article>
            </main>

            <aside class="sb-article-sidebar">
                <section class="sb-article-side-card">
                    <h2>Informasi Artikel</h2>

                    <div class="sb-article-info-list">
                        <div class="sb-article-info-item">
                            <small>Kategori</small>
                            <strong>{{ $articleCategory }}</strong>
                        </div>

                        <div class="sb-article-info-item">
                            <small>Penulis</small>
                            <strong>{{ $articleAuthor }}</strong>
                        </div>

                        <div class="sb-article-info-item">
                            <small>Diterbitkan</small>
                            <strong>{{ $articleDate }}</strong>
                        </div>

                        <div class="sb-article-info-item">
                            <small>Waktu Baca</small>
                            <strong>{{ $readTime }}</strong>
                        </div>
                    </div>
                </section>

                <section class="sb-article-side-card">
                    <div class="sb-article-side-head">
                        <h2>Berita Terkait</h2>
                        <a href="{{ route('berita') }}">Lihat Semua</a>
                    </div>

                    <div class="sb-article-related-list">
                        @foreach($relatedNews as $news)
                            @php
                                $relatedParam = data_get($news, 'slug', data_get($news, 'id'));
                                $relatedUrl = filled($relatedParam)
                                    ? route('berita.detail', $relatedParam)
                                    : route('berita');

                                $relatedTitle = data_get($news, 'title', 'Berita Sobat Bumi');
                                $relatedImage = data_get($news, 'img', asset('assets/images/news/news3.jpg'));
                            @endphp

                            <a href="{{ $relatedUrl }}" class="sb-article-related-item">
                                <img
                                    src="{{ $relatedImage }}"
                                    alt="{{ $relatedTitle }}"
                                    loading="lazy"
                                    data-fallback="{{ asset('assets/images/news/news3.jpg') }}"
                                    onerror="this.onerror=null;this.src=this.dataset.fallback;"
                                >

                                <span>
                                    <small>{{ data_get($news, 'category', 'Berita') }}</small>
                                    <strong>{{ $relatedTitle }}</strong>
                                    <em>{{ data_get($news, 'date') }}</em>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>

    </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection