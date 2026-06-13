@extends('layouts.app')

@section('title', 'Beranda — Sobat Bumi Samarinda')
@section('meta_desc', 'Komunitas pelajar peduli lingkungan di Samarinda. Bergerak bersama untuk Bumi yang lebih baik.')

@section('content')

@php
$homeAboutImages = collect($homeAboutImages ?? [])
->map(function ($image) {
return [
'image' => data_get($image, 'image', data_get($image, 'gambar', asset('assets/images/gallery/foto1.jpeg'))),
'alt' => data_get($image, 'alt', data_get($image, 'alt_gambar', 'Kegiatan Sobat Bumi Samarinda')),
];
})
->filter(fn ($image) => filled(data_get($image, 'image')))
->values();

if ($homeAboutImages->isEmpty()) {
$homeAboutImages = collect([
[
'image' => asset('assets/images/gallery/foto1.jpeg'),
'alt' => 'Kegiatan Sobat Bumi Samarinda',
],
[
'image' => asset('assets/images/gallery/foto2.JPG'),
'alt' => 'Edukasi lingkungan Sobat Bumi',
],
]);
}

$homeAboutImages = $homeAboutImages->values()->all();

$homeStats = collect($homeStats ?? [])
->map(function ($stat) {
return [
'value' => data_get($stat, 'value', data_get($stat, 'nilai')),
'label' => data_get($stat, 'label'),
];
})
->filter(fn ($stat) => filled(data_get($stat, 'value')) && filled(data_get($stat, 'label')))
->take(2)
->values();

if ($homeStats->isEmpty()) {
$homeStats = collect([
[
'value' => '1000+ Kg CO₂',
'label' => 'Total reduksi emisi karbon dalam setahun',
],
[
'value' => '10 Program',
'label' => 'Total program aktif saat ini',
],
]);
}

$homeStats = $homeStats->values()->all();

$defaultPrograms = [
[
'number' => '01',
'label' => 'Aksi Sobat Bumi',
'title' => 'Aksi Sobat Bumi',
'desc' => 'Gerakan aksi lingkungan yang mengajak pelajar dan masyarakat untuk terlibat langsung dalam kegiatan bersih-bersih, penghijauan, kampanye lingkungan, dan aksi sosial berbasis kepedulian terhadap bumi.',
'image' => asset('assets/images/news/news3.jpg'),
'icon' => 'leaf',
],
[
'number' => '02',
'label' => 'Desa Energi Berdikari',
'title' => 'Desa Energi Berdikari',
'desc' => 'Program pemberdayaan desa melalui edukasi dan pemanfaatan energi bersih agar masyarakat dapat mengenal solusi energi yang lebih mandiri, ramah lingkungan, dan berkelanjutan.',
'image' => asset('assets/images/gallery/foto1.jpeg'),
'icon' => 'home',
],
[
'number' => '03',
'label' => 'Sekolah Energi Berdikari',
'title' => 'Sekolah Energi Berdikari',
'desc' => 'Program edukasi energi bersih di lingkungan sekolah untuk membangun kesadaran pelajar terhadap penghematan energi, energi terbarukan, dan kebiasaan ramah lingkungan sejak dini.',
'image' => asset('assets/images/gallery/foto2.JPG'),
'icon' => 'school',
],
];

$programs = collect($defaultPrograms)->values()->all();

$latestNews = collect($latestNews ?? [])
->map(function ($news) {
return [
'id' => data_get($news, 'id', data_get($news, 'slug', '#')),
'slug' => data_get($news, 'slug'),
'category' => data_get($news, 'category', data_get($news, 'kategori', 'Berita')),
'date' => data_get($news, 'date', data_get($news, 'tanggal', '')),
'title' => data_get($news, 'title', data_get($news, 'judul')),
'desc' => data_get($news, 'desc', data_get($news, 'ringkasan')),
'img' => data_get($news, 'img', data_get($news, 'image', data_get($news, 'gambar_url', asset('assets/images/news/news3.jpg')))),
];
})
->filter(fn ($news) => filled(data_get($news, 'title')))
->take(3)
->values();

if ($latestNews->isEmpty()) {
$latestNews = collect([
[
'id' => 1,
'slug' => null,
'category' => 'Kegiatan',
'date' => '20 Mei 2025',
'title' => 'Aksi Tanam 500 Pohon di Bantaran Sungai Karang Mumus',
'desc' => 'Sobat Bumi Samarinda bersama relawan dan warga menanam 500 pohon untuk ruang hijau kota.',
'img' => asset('assets/images/news/news1.JPG'),
],
[
'id' => 2,
'slug' => null,
'category' => 'Informasi',
'date' => '15 Mei 2025',
'title' => 'Kurangi Plastik Sekali Pakai, Mulai dari Hal Kecil',
'desc' => 'Mengurangi plastik sekali pakai bisa dimulai dari kebiasaan sederhana yang konsisten.',
'img' => asset('assets/images/news/news2.JPG'),
],
[
'id' => 3,
'slug' => null,
'category' => 'Kegiatan',
'date' => '10 Mei 2025',
'title' => 'Bersih-Bersih Tepi Sungai Bersama Komunitas',
'desc' => 'Kegiatan bersih-bersih sungai untuk membangun lingkungan yang lebih sehat dan nyaman.',
'img' => asset('assets/images/news/news3.jpg'),
],
]);
}

$latestNews = $latestNews->values()->all();
$defaultTestimonials = [
[
'name' => 'Sobat Bumi',
'role' => 'Relawan',
'text' => '“Sobat Bumi Samarinda adalah komunitas pelajar yang berdiri di Kota Samarinda dengan semangat peduli terhadap lingkungan dan masyarakat.”',
'image' => asset('assets/images/profile/Abdi.jpg'),
],
[
'name' => 'Sobat Bumi',
'role' => 'Relawan',
'text' => '“Kegiatannya membuat kami lebih sadar bahwa menjaga lingkungan bisa dimulai dari langkah kecil yang konsisten.”',
'image' => asset('assets/images/profile/Fadira.jpg'),
],
[
'name' => 'Sobat Bumi',
'role' => 'Relawan',
'text' => '“Sobat Bumi memberi ruang bagi pelajar untuk belajar, bergerak, dan berdampak secara nyata bagi sekitar.”',
'image' => asset('assets/images/profile/Ferdi.jpg'),
],
[
'name' => 'Sobat Bumi',
'role' => 'Relawan',
'text' => '“Programnya dekat dengan kehidupan sehari-hari, jadi mudah diikuti dan terasa manfaatnya untuk lingkungan.”',
'image' => asset('assets/images/profile/Tri.jpeg'),
],
[
'name' => 'Sobat Bumi',
'role' => 'Relawan',
'text' => '“Gerakan ini membuktikan bahwa anak muda bisa menjadi bagian penting dalam perubahan lingkungan.”',
'image' => asset('assets/images/profile/Sabira.jpg'),
],
];

$testimonials = collect($testimonials ?? [])
->map(function ($testimonial) {
$rawImage = data_get(
$testimonial,
'image',
data_get(
$testimonial,
'foto_url',
data_get($testimonial, 'foto')
)
);

if (blank($rawImage)) {
$imageUrl = asset('assets/images/avatar-placeholder.svg');
} elseif (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
$imageUrl = $rawImage;
} elseif (\Illuminate\Support\Str::startsWith($rawImage, ['/assets/', 'assets/'])) {
$imageUrl = asset(ltrim($rawImage, '/'));
} elseif (\Illuminate\Support\Str::startsWith($rawImage, ['/storage/', 'storage/'])) {
$imageUrl = asset(ltrim($rawImage, '/'));
} elseif (\Illuminate\Support\Str::startsWith($rawImage, 'public/')) {
$imageUrl = asset(ltrim(\Illuminate\Support\Str::after($rawImage, 'public/'), '/'));
} elseif (!\Illuminate\Support\Str::contains($rawImage, '/')) {
$imageUrl = asset('assets/images/profile/' . $rawImage);
} else {
// Path upload Laravel biasanya tersimpan seperti galeri/file.jpg atau testimoni/file.jpg.
$imageUrl = asset('storage/' . ltrim($rawImage, '/'));
}

return [
'name' => data_get($testimonial, 'name', data_get($testimonial, 'nama', 'Sobat Bumi')),
'role' => data_get($testimonial, 'role', data_get($testimonial, 'peran', 'Relawan')),
'text' => data_get($testimonial, 'text', data_get($testimonial, 'isi')),
'image' => $imageUrl,
];
})
->filter(fn ($testimonial) => filled(data_get($testimonial, 'text')))
->values();

if ($testimonials->isEmpty()) {
$testimonials = collect($defaultTestimonials);
}

$testimonials = $testimonials->values()->all();
@endphp

<section class="sb-hero">
    <div class="container sb-hero-content">
        <h1>Cintai Bumi,<br>Selamatkan Bumi</h1>
        <p>
            Sobat Bumi Samarinda adalah komunitas pelajar yang bergerak di bidang lingkungan,
            sosial, dan energi bersih.
        </p>

        <a href="{{ route('berita') }}" class="sb-hero-btn">
            Lihat Berita
            <svg class="sb-icon-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 17 17 7M9 7h8v8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </div>
</section>

<section class="sb-about">
    <div class="container">
        <div class="sb-section-title">
            <h2>Tentang <span>Kami</span></h2>
        </div>

        <div class="sb-about-grid">
            <div class="sb-about-left">
                <div class="sb-about-slider" id="aboutSlider">
                    <div class="sb-about-track">
                        @foreach($homeAboutImages as $image)
                        <img
                            src="{{ data_get($image, 'image') }}"
                            alt="{{ data_get($image, 'alt', 'Kegiatan Sobat Bumi Samarinda') }}"
                            loading="lazy"
                            data-fallback="{{ asset('assets/images/gallery/foto1.jpeg') }}"
                            onerror="this.onerror=null;this.src=this.dataset.fallback;">
                        @endforeach
                    </div>
                </div>

                <div class="sb-slider-dots" data-slider="about"></div>
            </div>

            <div class="sb-about-right">
                <h3>
                    Sobat Bumi Samarinda adalah komunitas pelajar yang berdiri di Kota Samarinda
                    dengan semangat peduli terhadap lingkungan dan masyarakat.
                </h3>

                <div class="sb-about-stats">
                    @foreach($homeStats as $index => $stat)
                    <div class="sb-about-stat-card">
                        <div class="sb-stat-icon" aria-hidden="true">
                            @if($index === 0)
                            <svg class="sb-icon-lg" viewBox="0 0 24 24" fill="none">
                                <path d="M7.5 7.4 9 4.8a2 2 0 0 1 3.5 0l.8 1.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="m14 5.8-.5 3.4 3.4-.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M16.8 9.3h3a2 2 0 0 1 1.7 3l-1.3 2.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="m21.1 14.1-3.2-1.2-1.2 3.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M14.8 18.5h-3.3a2 2 0 0 1-1.7-3l.8-1.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="m10.1 14.1-2.7 2.2 2.7 2.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            @else
                            <svg class="sb-icon-lg" viewBox="0 0 24 24" fill="none">
                                <path d="M4 7.5 12 3l8 4.5-8 4.5-8-4.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                <path d="m4 12 8 4.5 8-4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="m4 16.5 8 4.5 8-4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            @endif
                        </div>
                        <div>
                            <strong>{{ data_get($stat, 'value') }}</strong>
                            <p>{{ data_get($stat, 'label') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('tentang') }}" class="sb-pill-btn">
                    Selengkapnya Tentang Kami
                    <svg class="sb-icon-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="sb-programs" aria-labelledby="programsTitle">
    <div class="container">
        <div class="sb-programs-head">
            <div class="sb-programs-title">
                <h2 id="programsTitle">
                    Kegiatan <span>Kami</span>
                </h2>
            </div>
        </div>

        <div class="sb-programs-slider" id="homeProgramsSlider">
            @foreach($programs as $index => $program)
                @php
                    $programTitle = data_get($program, 'title', 'Program Sobat Bumi');
                    $programLabel = data_get($program, 'label', $programTitle);
                    $programDesc = data_get($program, 'desc', 'Program lingkungan Sobat Bumi Samarinda.');
                    $programNumber = data_get($program, 'number', str_pad($index + 1, 2, '0', STR_PAD_LEFT));
                    $programImage = data_get($program, 'image', asset('assets/images/news/news3.jpg'));
                @endphp

                <article
                    class="sb-program-card {{ $index === 0 ? 'active' : '' }}"
                    data-program-card
                    data-index="{{ $index }}"
                    data-program-image="{{ $programImage }}"
                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                    style="--program-image: url('{{ $programImage }}');">
                    <button
                        type="button"
                        class="sb-program-card-trigger"
                        data-program-trigger
                        aria-label="Tampilkan {{ $programTitle }}"
                        aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">
                        <span
                            class="sb-program-card-bg"
                            aria-hidden="true"
                            style="background-image: url('{{ $programImage }}');">
                        </span>

                        <span class="sb-program-card-overlay" aria-hidden="true"></span>

                        <span class="sb-program-card-content">
                            <span class="sb-program-card-top">
                                <span class="sb-program-label">
                                    {{ $programLabel }}
                                </span>
                            </span>

                            <span class="sb-program-card-main">
                                <span class="sb-program-number">{{ $programNumber }}</span>
                                <span class="sb-program-title">{{ $programTitle }}</span>
                                <span class="sb-program-desc">{{ $programDesc }}</span>
                            </span>
                        </span>

                        <span class="sb-program-vertical-title">
                            {{ $programTitle }}
                        </span>
                    </button>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="sb-news">
    <div class="container">
        <div class="sb-news-head">
            <h2>Berita <span>Terbaru</span></h2>
            <a href="{{ route('berita') }}">Lihat Semua Berita ›</a>
        </div>

        <div class="sb-news-grid">
            @foreach($latestNews as $news)
            @php
            $newsTitle = data_get($news, 'title', 'Berita Sobat Bumi');
            $newsImage = data_get($news, 'img', asset('assets/images/news/news3.jpg'));
            $newsSlug = data_get($news, 'slug');
            $newsId = data_get($news, 'id');
            $newsUrl = $newsSlug
            ? route('berita.detail', $newsSlug)
            : route('berita.detail', $newsId);
            @endphp

            <article class="sb-news-card">
                <div class="sb-news-img">
                    <img
                        src="{{ $newsImage }}"
                        alt="{{ $newsTitle }}"
                        loading="lazy"
                        data-fallback="{{ asset('assets/images/news/news3.jpg') }}"
                        onerror="this.onerror=null;this.src=this.dataset.fallback;">
                    <span>{{ data_get($news, 'category', 'Berita') }}</span>
                </div>
                <div class="sb-news-body">
                    <p class="sb-news-date">{{ data_get($news, 'date') }}</p>
                    <h3>{{ $newsTitle }}</h3>
                    <p>{{ data_get($news, 'desc') }}</p>
                    <a href="{{ $newsUrl }}">Baca Selengkapnya →</a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="sb-testimonial">
    <div class="container">
        <h2>Apa Kata Mereka?</h2>

        <div class="sb-testimonial-box">
            <div class="sb-testimonial-visual" aria-hidden="true">
                @foreach($testimonials as $index => $testimonial)
                @php
                $activeIndex = min(2, count($testimonials) - 1);
                @endphp

                <div
                    class="sb-person-card {{ $index === $activeIndex ? 'active' : '' }}"
                    data-testimonial="{{ data_get($testimonial, 'text') }}"
                    tabindex="0">
                    <img
                        src="{{ data_get($testimonial, 'image', asset('assets/images/profile/Abdi.jpg')) }}"
                        alt="Testimoni {{ data_get($testimonial, 'name', 'Sobat Bumi') }}"
                        loading="lazy"
                        data-fallback="{{ asset('assets/images/profile/Abdi.jpg') }}"
                        onerror="this.onerror=null;this.src=this.dataset.fallback;">
                </div>
                @endforeach
            </div>

            <div class="sb-testimonial-actions">
                <button type="button" id="testimonialPrev" aria-label="Testimoni sebelumnya">‹</button>
                <button type="button" id="testimonialNext" aria-label="Testimoni berikutnya">›</button>
            </div>

            <p id="testimonialText">
                {{ data_get($testimonials, '2.text', data_get($testimonials, '0.text', '“Sobat Bumi memberi ruang bagi pelajar untuk belajar, bergerak, dan berdampak secara nyata bagi sekitar.”')) }}
            </p>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection