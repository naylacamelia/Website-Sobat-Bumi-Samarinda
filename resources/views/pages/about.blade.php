@extends('layouts.app')

@section('title', 'Tentang Kami — Sobat Bumi Samarinda')

@section('content')

@php
    $heroHighlights = [
        [
            'title' => 'Bersama untuk Bumi yang Lebih Baik',
            'icon' => 'leaf',
        ],
        [
            'title' => 'Kolaborasi Aksi Dampak Nyata',
            'icon' => 'group',
        ],
    ];

    $aboutImages = collect($aboutImages ?? [
        [
            'image' => asset('assets/images/gallery/foto3.JPG'),
            'alt' => 'Drone Pertanian',
        ],
        [
            'image' => asset('assets/images/gallery/foto4.jpg'),
            'alt' => 'Aksi Penanaman',
        ],
        [
            'image' => asset('assets/images/gallery/about-hero.jpeg'),
            'alt' => 'Tim Sobat Bumi Samarinda',
        ],
    ])->values()->all();

    $missions = collect($missions ?? [
        'Meningkatkan kesadaran dan kepedulian lingkungan di kalangan pelajar.',
        'Menggerakkan aksi nyata untuk pelestarian lingkungan dan energi bersih.',
        'Memberdayakan masyarakat melalui program sosial dan ekonomi kreatif.',
        'Membangun kolaborasi dengan berbagai pihak untuk dampak yang lebih luas.',
        'Mendidik dan menginspirasi generasi muda untuk masa depan berkelanjutan.',
    ])->values()->all();

    $journeys = collect($journeys ?? [
        [
            'year' => '2019',
            'title' => 'Awal Perjalanan',
            'desc' => 'Sejumlah pelajar mulai bergerak melalui aksi lingkungan pertama.',
            'icon' => 'leaf',
            'active' => false,
        ],
        [
            'year' => '2020',
            'title' => 'Komunitas Terbentuk',
            'desc' => 'Sobat Bumi Samarinda resmi terbentuk dan menjalankan program rutin.',
            'icon' => 'recycle',
            'active' => false,
        ],
        [
            'year' => '2022',
            'title' => 'Fokus Energi Bersih',
            'desc' => 'Mulai mengembangkan edukasi dan kampanye energi terbarukan.',
            'icon' => 'bolt',
            'active' => false,
        ],
        [
            'year' => '2024+',
            'title' => 'Masa Depan',
            'desc' => 'Terus tumbuh, berinovasi, dan bergerak untuk bumi yang lebih baik.',
            'icon' => 'earth',
            'active' => true,
        ],
    ])->values()->all();

    $heroLead = $heroLead ?? 'Sobat Bumi Samarinda adalah komunitas pelajar yang bergerak di bidang lingkungan, sosial, dan energi bersih untuk menciptakan perubahan positif bagi bumi dan masyarakat.';
    $introText = $introText ?? 'Kami adalah ruang belajar, beraksi, dan berkolaborasi bagi pelajar yang ingin berkontribusi nyata melalui kegiatan positif di bidang lingkungan, pemberdayaan masyarakat, dan energi bersih.';
    $visionText = $visionText ?? 'Menjadi komunitas pelajar terdepan di Samarinda yang menginspirasi dan menggerakkan aksi nyata untuk lingkungan yang lestari dan masyarakat yang berdaya.';

    $icons = [
        'leaf' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.5 4.5c-5.8.2-10 1.8-12.6 4.4-2.1 2.1-2.7 5-1.5 7.2L3.6 18l1.4 1.4 1.9-1.9c2.2 1.2 5.1.6 7.2-1.5 2.6-2.6 4.2-6.8 4.4-12.6ZM12.7 14.6c-1.3 1.3-3 1.7-4.3 1l4.8-4.8-1.4-1.4L7 14.2c-.7-1.3-.3-3 1-4.3 1.7-1.7 4.4-2.8 8.2-3.2-.4 3.8-1.5 6.5-3.5 7.9Z" fill="currentColor"/></svg>',
        'group' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm8 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6ZM3.5 18.5c.4-3 2.1-5 4.5-5s4.1 2 4.5 5h-9Zm8 0c.2-1.5.8-2.8 1.8-3.7.9-.8 1.9-1.3 2.7-1.3 2.4 0 4.1 2 4.5 5h-9Z" fill="currentColor"/></svg>',
        'eye' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c5.4 0 8.7 5.1 9 7-.3 1.9-3.6 7-9 7s-8.7-5.1-9-7c.3-1.9 3.6-7 9-7Zm0 11.5a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm0-2a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z" fill="currentColor"/></svg>',
        'target' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21a9 9 0 1 1 9-9 9 9 0 0 1-9 9Zm0-2a7 7 0 1 0 0-14 7 7 0 0 0 0 14Zm0-3.2a3.8 3.8 0 1 1 0-7.6 3.8 3.8 0 0 1 0 7.6Zm0-2a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6Z" fill="currentColor"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m10 15.2 6.6-6.6L18 10l-8 8-4-4 1.4-1.4 2.6 2.6Z" fill="currentColor"/></svg>',
        'recycle' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8.3 4.4a2 2 0 0 1 3.4 0l.7 1.2 1.5-.8-.4 4.4-3.9-2.1 1.4-.8-1.1-1.9a.3.3 0 0 0-.5 0L7.5 7.8 5.8 6.8l2.5-2.4Zm9.9 8.1 2.4 4.2a2 2 0 0 1-1.7 3h-1.5v1.6l-3.7-2.3 3.7-2.3v1.5h1.5a.3.3 0 0 0 .2-.5l-1.8-3 1.7-1Zm-12.4.8.8 1.4-1.6 1 .1.2h3.7v-1.6l3.7 2.3-3.7 2.3v-1.5H5.1a2 2 0 0 1-1.7-3l.7-1.2-1.4-.8 3.9-2.1-.4 4.4-.4-1.4Z" fill="currentColor"/></svg>',
        'bolt' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.2 2 4.8 13.4h6.3L10.2 22l9-12.4h-6.1L13.2 2Z" fill="currentColor"/></svg>',
        'earth' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Zm-1-2.2A10.6 10.6 0 0 1 9.2 15H6.6a7.1 7.1 0 0 0 4.4 3.8ZM17.4 15h-2.6a10.6 10.6 0 0 1-1.8 3.8 7.1 7.1 0 0 0 4.4-3.8ZM8.8 13a12.6 12.6 0 0 1 0-2H5.3a7 7 0 0 0 0 2h3.5Zm5.9 0a12.6 12.6 0 0 0 0-2H9.3a12.6 12.6 0 0 0 0 2h5.4Zm4 0a7 7 0 0 0 0-2h-3.5a12.6 12.6 0 0 1 0 2h3.5ZM9.2 9c.4-1.6 1-2.9 1.8-3.8A7.1 7.1 0 0 0 6.6 9h2.6Zm5.6 0h2.6A7.1 7.1 0 0 0 13 5.2c.8.9 1.4 2.2 1.8 3.8Zm-.5 0C13.8 6.7 13 5 12 5s-1.8 1.7-2.3 4h4.6Zm0 6H9.7c.5 2.3 1.3 4 2.3 4s1.8-1.7 2.3-4Z" fill="currentColor"/></svg>',
    ];
@endphp

<section class="sb-about-page-clean">

    <section class="sb-about-hero">
        <div class="container">
            <div class="sb-about-hero-grid">
                <div class="sb-about-copy">
                    <p class="sb-about-kicker">Tentang Kami</p>

                    <h1>
                        Tentang
                        <span>Sobat Bumi Samarinda</span>
                    </h1>

                    <p class="sb-about-lead">
                        {{ $heroLead }}
                    </p>

                    <div class="sb-about-values">
                        @foreach($heroHighlights as $item)
                            <div class="sb-about-value">
                                <span>{!! $icons[$item['icon']] !!}</span>
                                <strong>{{ $item['title'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

<div class="sb-about-visual">
    <div class="sb-hero-image-group">
        <div class="sb-hero-glow"></div>
        
        <div class="sb-img-circle img-main">
            <img src="{{ data_get($aboutImages, '0.image', asset('assets/images/about-hero-1.jpg')) }}" alt="{{ data_get($aboutImages, '0.alt', 'Drone Pertanian') }}">
        </div>
        
        <div class="sb-img-circle img-sub">
            <img src="{{ data_get($aboutImages, '1.image', asset('assets/images/about-hero-2.jpg')) }}" alt="{{ data_get($aboutImages, '1.alt', 'Aksi Penanaman') }}">
        </div>
        
        <div class="sb-hero-leaf-decor">
            {!! $icons['leaf'] !!}
        </div>
    </div>
</div>
            </div>
        </div>
    </section>

    <section class="sb-about-intro">
        <div class="container">
            <div class="sb-about-intro-grid">
                <figure class="sb-about-photo">
                    <img
                        src="{{ data_get($aboutImages, '2.image', asset('assets/images/gallery/about-hero.jpeg')) }}"
                        alt="{{ data_get($aboutImages, '2.alt', 'Tim Sobat Bumi Samarinda') }}"
                    >
                </figure>

                <div class="sb-about-intro-copy">
                    <h2>Siapa Kami?</h2>

                    <p>
                        {{ $introText }}
                    </p>

                    <a href="{{ route('berita') }}">
                        Lihat Kegiatan Kami
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="sb-about-vm">
        <div class="container">
            <div class="sb-about-vm-grid">
                <article class="sb-about-card">
                    <div class="sb-about-card-head">
                        <span>{!! $icons['eye'] !!}</span>
                        <h2>Visi</h2>
                    </div>

                    <p>
                        {{ $visionText }}
                    </p>

                    <span class="sb-about-decor decor-leaf" aria-hidden="true"></span>
                    <span class="sb-about-decor decor-circle" aria-hidden="true"></span>
                </article>

                <article class="sb-about-card">
                    <div class="sb-about-card-head">
                        <span>{!! $icons['target'] !!}</span>
                        <h2>Misi</h2>
                    </div>

                    <ul class="sb-about-mission">
                        @foreach($missions as $mission)
                            <li>
                                <span>{!! $icons['check'] !!}</span>
                                <p>{{ $mission }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <span class="sb-about-decor decor-circle" aria-hidden="true"></span>
                </article>
            </div>
        </div>
    </section>

    <section class="sb-about-journey">
        <div class="container">
            <h2>Perjalanan Kami</h2>

            <div class="sb-about-timeline">
                @foreach($journeys as $journey)
                    <article class="sb-about-step {{ $journey['active'] ? 'active' : '' }}">
                        <span class="sb-about-step-icon">
                            {!! $icons[$journey['icon']] !!}
                        </span>

                        <div class="sb-about-step-card">
                            <strong>{{ $journey['year'] }}</strong>
                            <h3>{{ $journey['title'] }}</h3>
                            <p>{{ $journey['desc'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

</section>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection