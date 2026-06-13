<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Linimasa;
use App\Models\PengaturanSitus;
use App\Models\PesanKontak;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class HalamanController extends Controller
{
    public function home()
    {
        $homeAboutImages = $this->galleryImages('beranda', $this->defaultHomeAboutImages());

        // Statistik homepage dibuat hardcode agar tidak perlu dikelola dari database.
        $homeStats = $this->defaultHomeStats();

        $programs = Kegiatan::with('kategori')
            ->where('status', 'aktif')
            ->orderByRaw('tanggal IS NULL, tanggal asc')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Kegiatan $item, int $index) => $this->mapKegiatanProgram($item, $index))
            ->filter(fn ($item) => filled($item['title']))
            ->values()
            ->all();

        if (count($programs) === 0) {
            $programs = $this->defaultPrograms();
        }

        $latestNews = Berita::with('kategori')
            ->published()
            ->latest('tanggal_terbit')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Berita $item) => $this->mapNews($item))
            ->filter(fn ($item) => filled($item['title']))
            ->values()
            ->all();

        if (count($latestNews) === 0) {
            $latestNews = collect($this->defaultNews())
                ->take(3)
                ->values()
                ->all();
        }

        $testimonialGalleryImages = collect($this->galleryImages('testimoni', []));

        $defaultTestimonials = collect($this->defaultTestimonials())
            ->map(function (array $item, int $index) use ($testimonialGalleryImages) {
                if ($testimonialGalleryImages->isNotEmpty()) {
                    $item['image'] = data_get(
                        $testimonialGalleryImages->get($index % $testimonialGalleryImages->count()),
                        'image',
                        $item['image']
                    );
                }

                return $item;
            });

        $dbTestimonials = Testimoni::where('aktif', true)
            ->orderBy('urutan')
            ->take(7)
            ->get()
            ->map(function (Testimoni $item, int $index) use ($defaultTestimonials, $testimonialGalleryImages) {
                $fallback = $defaultTestimonials->get($index % $defaultTestimonials->count());
                $galleryImage = $testimonialGalleryImages->isNotEmpty()
                    ? data_get($testimonialGalleryImages->get($index % $testimonialGalleryImages->count()), 'image')
                    : null;

                return [
                    'name' => $item->nama ?: data_get($fallback, 'name', 'Sobat Bumi'),
                    'role' => $item->peran ?: data_get($fallback, 'role', 'Relawan'),
                    'text' => $item->isi ?: data_get($fallback, 'text'),
                    // Prioritas foto: Galeri lokasi testimoni, foto dari tabel testimoni, lalu fallback lokal.
                    'image' => $galleryImage ?: $this->resolveTestimonialImage(
                        $item->foto,
                        data_get($fallback, 'image', asset('assets/images/profile/Abdi.jpg'))
                    ),
                ];
            })
            ->filter(fn ($item) => filled(data_get($item, 'text')))
            ->values();

        $minimumTestimonials = 5;

        if ($dbTestimonials->count() === 0) {
            $testimonials = $defaultTestimonials->values()->all();
        } elseif ($dbTestimonials->count() < $minimumTestimonials) {
            $usedCount = $dbTestimonials->count();

            $testimonials = $dbTestimonials
                ->concat(
                    $defaultTestimonials
                        ->slice($usedCount)
                        ->take($minimumTestimonials - $usedCount)
                        ->values()
                )
                ->values()
                ->all();
        } else {
            $testimonials = $dbTestimonials->values()->all();
        }

        return $this->renderUserView(['pages.home', 'home'], compact(
            'homeAboutImages',
            'homeStats',
            'programs',
            'latestNews',
            'testimonials'
        ));
    }

    public function tentang()
    {
        $aboutImages = $this->galleryImages('tentang', $this->defaultAboutImages());

        $missions = $this->settingLines('tentang_misi', $this->defaultMissions());

        $journeys = Linimasa::where('aktif', true)
            ->orderBy('urutan')
            ->get()
            ->map(function (Linimasa $item, int $index) {
                $icons = ['leaf', 'recycle', 'bolt', 'earth'];

                return [
                    'year' => $item->tahun,
                    'title' => $item->judul,
                    'desc' => $item->deskripsi,
                    'icon' => $icons[$index % count($icons)],
                    'active' => false,
                ];
            })
            ->filter(fn ($item) => filled($item['year']) && filled($item['title']))
            ->values()
            ->all();

        if (count($journeys) === 0) {
            $journeys = $this->defaultJourneys();
        }

        if (count($journeys) > 0) {
            $journeys[array_key_last($journeys)]['active'] = true;
        }

        $heroLead = $this->setting(
            'tentang_hero_lead',
            'Sobat Bumi Samarinda adalah komunitas pelajar yang bergerak di bidang lingkungan, sosial, dan energi bersih untuk menciptakan perubahan positif bagi bumi dan masyarakat.'
        );

        $introText = $this->setting(
            'tentang_intro',
            'Kami adalah ruang belajar, beraksi, dan berkolaborasi bagi pelajar yang ingin berkontribusi nyata melalui kegiatan positif di bidang lingkungan, pemberdayaan masyarakat, dan energi bersih.'
        );

        $visionText = $this->setting(
            'tentang_visi',
            'Menjadi komunitas pelajar terdepan di Samarinda yang menginspirasi dan menggerakkan aksi nyata untuk lingkungan yang lestari dan masyarakat yang berdaya.'
        );

        return $this->renderUserView(['pages.about', 'about', 'tentang'], compact(
            'aboutImages',
            'missions',
            'journeys',
            'heroLead',
            'introText',
            'visionText'
        ));
    }

    public function berita()
    {
        $newsCollection = Berita::with('kategori')
            ->published()
            ->latest('tanggal_terbit')
            ->latest()
            ->get();

        if ($newsCollection->count() > 0) {
            $newsList = $newsCollection
                ->map(fn (Berita $item) => $this->mapNews($item))
                ->values()
                ->all();

            $featured = $newsCollection->firstWhere('unggulan', true) ?: $newsCollection->first();
            $featuredNews = $featured ? $this->mapNews($featured) : null;

            $popularNews = Berita::with('kategori')
                ->published()
                ->orderByDesc('jumlah_dilihat')
                ->latest('tanggal_terbit')
                ->take(3)
                ->get()
                ->map(fn (Berita $item) => $this->mapNews($item))
                ->values()
                ->all();

            if (count($popularNews) === 0) {
                $popularNews = collect($newsList)->take(3)->values()->all();
            }

            $latestArticles = collect($newsList)
                ->when($featuredNews, fn (Collection $items) => $items->reject(fn ($item) => $item['id'] === $featuredNews['id']))
                ->take(4)
                ->values()
                ->all();

            $categoryMeta = ['all' => 'Semua'];

            Kategori::where('tipe', 'berita')
                ->orderBy('nama')
                ->get()
                ->each(function (Kategori $kategori) use (&$categoryMeta) {
                    $categoryMeta[$kategori->slug] = $kategori->nama;
                });
        } else {
            $newsList = $this->defaultNews();
            $featuredNews = $newsList[0] ?? null;
            $popularNews = collect($newsList)->take(3)->values()->all();

            $latestArticles = collect($newsList)
                ->when($featuredNews, fn (Collection $items) => $items->reject(fn ($item) => $item['id'] === $featuredNews['id']))
                ->take(4)
                ->values()
                ->all();

            $categoryMeta = collect($newsList)
                ->pluck('category', 'key')
                ->prepend('Semua', 'all')
                ->toArray();
        }

        return $this->renderUserView(['pages.berita', 'berita'], compact(
            'featuredNews',
            'newsList',
            'popularNews',
            'latestArticles',
            'categoryMeta'
        ));
    }

    public function detailBerita($identifier)
    {
        $articleModel = Berita::with(['kategori', 'pengguna'])
            ->published()
            ->where(function ($query) use ($identifier) {
                $query->where('slug', $identifier);

                if (is_numeric($identifier)) {
                    $query->orWhere('id', (int) $identifier);
                }
            })
            ->first();

        if ($articleModel) {
            $articleModel->increment('jumlah_dilihat');

            $article = $this->mapNews($articleModel);
            $article['content'] = $articleModel->isi;
            $article['content_html'] = $this->formatArticleContent($articleModel->isi);
            $article['caption'] = $articleModel->alt_gambar ?: $articleModel->judul;
            $article['read_time'] = $this->readTime($articleModel->isi);
            $article['tags'] = $this->normalizeTags($articleModel->tag);

            $relatedNews = Berita::with('kategori')
                ->published()
                ->where('id', '!=', $articleModel->id)
                ->when($articleModel->kategori_id, fn ($query) => $query->where('kategori_id', $articleModel->kategori_id))
                ->latest('tanggal_terbit')
                ->take(3)
                ->get();

            if ($relatedNews->count() < 3) {
                $extra = Berita::with('kategori')
                    ->published()
                    ->where('id', '!=', $articleModel->id)
                    ->whereNotIn('id', $relatedNews->pluck('id'))
                    ->latest('tanggal_terbit')
                    ->take(3 - $relatedNews->count())
                    ->get();

                $relatedNews = $relatedNews->concat($extra);
            }

            $relatedNews = $relatedNews
                ->map(fn (Berita $item) => $this->mapNews($item))
                ->values()
                ->all();
        } else {
            $localNews = collect($this->defaultNews());

            $article = $localNews->first(function ($item) use ($identifier) {
                return (string) data_get($item, 'id') === (string) $identifier
                    || (string) data_get($item, 'slug') === (string) $identifier;
            });

            if (!$article) {
                abort(404);
            }

            $article['content'] = data_get($article, 'content', data_get($article, 'desc'));
            $article['content_html'] = $this->formatArticleContent(data_get($article, 'content'));
            $article['caption'] = data_get($article, 'title');
            $article['read_time'] = $this->readTime(data_get($article, 'content'));
            $article['tags'] = data_get($article, 'tags', []);

            $relatedNews = $localNews
                ->reject(fn ($item) => data_get($item, 'id') === data_get($article, 'id'))
                ->take(3)
                ->values()
                ->all();
        }

        return $this->renderUserView(['pages.detail-berita', 'detail-berita', 'berita.detail'], compact(
            'article',
            'relatedNews'
        ));
    }

    public function kegiatan()
    {
        $kegiatan = Kegiatan::with('kategori')
            ->latest()
            ->get();

        if ($kegiatan->count() === 0) {
            $kegiatan = collect($this->defaultPrograms())
                ->map(function ($item, $index) {
                    return (object) [
                        'id' => $index + 1,
                        'judul' => $item['title'],
                        'slug' => Str::slug($item['title']),
                        'deskripsi' => $item['desc'],
                        'gambar' => $item['image'],
                        'gambar_url' => $item['image'],
                        'tanggal' => null,
                        'lokasi' => 'Samarinda',
                        'status' => 'aktif',
                        'kategori' => (object) [
                            'nama' => $item['label'],
                            'slug' => Str::slug($item['label']),
                        ],
                    ];
                });
        }

        return $this->renderUserView(['pages.kegiatan', 'kegiatan'], compact('kegiatan'));
    }

    public function kontak()
    {
        return $this->renderUserView(['pages.kontak', 'kontak']);
    }

    public function kirimKontak(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'pesan' => ['required', 'string'],
        ]);

        PesanKontak::create($data);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    private function mapNews(Berita $item): array
    {
        $categoryName = $item->kategori->nama ?? 'Berita';
        $categorySlug = $item->kategori->slug ?? Str::slug($categoryName);

        return [
            'id' => $item->slug ?: $item->id,
            'db_id' => $item->id,
            'slug' => $item->slug,
            'key' => $categorySlug,
            'category' => $categoryName,
            'date' => optional($item->tanggal_terbit ?? $item->created_at)->translatedFormat('d M Y'),
            'title' => $item->judul,
            'desc' => $item->ringkasan,
            'img' => $item->gambar_url,
            'image' => $item->gambar_url,
            'author' => $item->penulis ?: $item->pengguna?->nama ?: 'Admin SBS',
            'views' => $item->jumlah_dilihat,
        ];
    }

    private function mapKegiatanProgram(Kegiatan $item, int $index): array
    {
        $icons = ['leaf', 'home', 'school'];

        return [
            'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'label' => $item->kategori->nama ?? $item->judul,
            'title' => $item->judul,
            'desc' => $item->deskripsi ?: 'Informasi kegiatan Sobat Bumi Samarinda.',
            'image' => $this->resolvePublicImage($item->gambar, 'assets/images/news/news3.jpg'),
            'icon' => $icons[$index % count($icons)],
        ];
    }

    private function galleryImages(string $location, array $fallback): array
    {
        $images = Galeri::where('lokasi', $location)
            ->where('aktif', true)
            ->orderBy('urutan')
            ->get()
            ->map(fn (Galeri $item) => [
                'image' => $this->resolvePublicImage($item->gambar, data_get($fallback, '0.image', 'assets/images/gallery/foto1.jpeg')),
                'alt' => $item->alt_gambar ?: $item->judul ?: 'Galeri Sobat Bumi Samarinda',
            ])
            ->filter(fn ($item) => filled($item['image']))
            ->values()
            ->all();

        return count($images) ? $images : $fallback;
    }

    private function setting(string $key, ?string $fallback = null): ?string
    {
        $value = PengaturanSitus::where('kunci', $key)->value('nilai');

        return filled($value) ? $value : $fallback;
    }

    private function settingLines(string $key, array $fallback = []): array
    {
        $value = $this->setting($key);

        if (!$value) {
            return $fallback;
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            $lines = collect($decoded)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();

            return count($lines) ? $lines : $fallback;
        }

        $lines = collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        return count($lines) ? $lines : $fallback;
    }

    private function normalizeTags($tags): array
    {
        if (is_array($tags)) {
            return collect($tags)
                ->map(fn ($tag) => trim((string) $tag))
                ->filter()
                ->values()
                ->all();
        }

        if (is_string($tags) && filled($tags)) {
            return collect(explode(',', $tags))
                ->map(fn ($tag) => trim($tag))
                ->filter()
                ->values()
                ->all();
        }

        return [];
    }

    private function readTime(?string $content): string
    {
        $words = str_word_count(strip_tags((string) $content));
        $minutes = max(1, (int) ceil($words / 200));

        return $minutes . ' menit baca';
    }

    private function formatArticleContent(?string $content): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '<p>Konten artikel belum tersedia.</p>';
        }

        $paragraphs = preg_split('/\n\s*\n/', $content) ?: [$content];

        return collect($paragraphs)
            ->map(function ($paragraph) {
                $paragraph = trim($paragraph);

                if ($paragraph === '') {
                    return null;
                }

                if (Str::startsWith($paragraph, ['<h2', '<h3', '<p', '<ul', '<ol', '<blockquote', '<figure'])) {
                    return $paragraph;
                }

                return '<p>' . nl2br(e($paragraph)) . '</p>';
            })
            ->filter()
            ->implode("\n");
    }

    private function renderUserView(array $views, array $data = [])
    {
        foreach ($views as $view) {
            if (View::exists($view)) {
                return view($view, $data);
            }
        }

        abort(404, 'View halaman tidak ditemukan.');
    }

    private function resolvePublicImage(?string $path, string $fallback, ?string $defaultFolder = null): string
    {
        $fallbackUrl = Str::startsWith($fallback, ['http://', 'https://'])
            ? $fallback
            : asset(ltrim($fallback, '/'));

        if (!$path || !trim($path)) {
            return $fallbackUrl;
        }

        $path = trim($path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        }

        if (Str::startsWith($path, ['/assets/', 'assets/', '/storage/', 'storage/'])) {
            $relativePath = ltrim($path, '/');
        } elseif (!Str::contains($path, '/') && $defaultFolder) {
            $relativePath = trim($defaultFolder, '/') . '/' . $path;
        } else {
            // Path hasil upload Laravel biasanya tersimpan seperti "galeri/file.jpg".
            // Di public harus diakses lewat symbolic link /storage/....
            $relativePath = 'storage/' . ltrim($path, '/');
        }

        return asset($relativePath);
    }

    private function resolveTestimonialImage(?string $path, string $fallbackUrl): string
    {
        if (!$path || !trim($path)) {
            return $fallbackUrl;
        }

        $path = trim($path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'public/')) {
            $path = Str::after($path, 'public/');
        }

        if (Str::startsWith($path, ['/assets/', 'assets/', '/storage/', 'storage/'])) {
            $relativePath = ltrim($path, '/');
        } elseif (!Str::contains($path, '/')) {
            $relativePath = 'assets/images/profile/' . $path;
        } else {
            // Path hasil upload Laravel biasanya tersimpan seperti "testimoni/file.jpg" atau "galeri/file.jpg".
            $relativePath = 'storage/' . ltrim($path, '/');
        }

        if (!file_exists(public_path($relativePath))) {
            return $fallbackUrl;
        }

        return asset($relativePath);
    }

    private function defaultHomeAboutImages(): array
    {
        return [
            [
                'image' => asset('assets/images/gallery/foto1.jpeg'),
                'alt' => 'Kegiatan Sobat Bumi Samarinda',
            ],
            [
                'image' => asset('assets/images/gallery/foto2.JPG'),
                'alt' => 'Edukasi lingkungan Sobat Bumi',
            ],
        ];
    }

    private function defaultAboutImages(): array
    {
        return [
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
        ];
    }

    private function defaultHomeStats(): array
    {
        return [
            [
                'value' => '1000+ Kg CO₂',
                'label' => 'Total reduksi emisi karbon dalam setahun',
            ],
            [
                'value' => '10 Program',
                'label' => 'Total program aktif saat ini',
            ],
        ];
    }

    private function defaultPrograms(): array
    {
        return [
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
    }

    private function defaultMissions(): array
    {
        return [
            'Meningkatkan kesadaran dan kepedulian lingkungan di kalangan pelajar.',
            'Menggerakkan aksi nyata untuk pelestarian lingkungan dan energi bersih.',
            'Memberdayakan masyarakat melalui program sosial dan ekonomi kreatif.',
            'Membangun kolaborasi dengan berbagai pihak untuk dampak yang lebih luas.',
            'Mendidik dan menginspirasi generasi muda untuk masa depan berkelanjutan.',
        ];
    }

    private function defaultJourneys(): array
    {
        return [
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
        ];
    }

    private function defaultTestimonials(): array
    {
        return [
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
    }

    private function defaultNews(): array
    {
        return [
            [
                'id' => 'aksi-tanam-500-pohon',
                'db_id' => null,
                'slug' => 'aksi-tanam-500-pohon',
                'key' => 'kegiatan',
                'category' => 'Kegiatan',
                'date' => '20 Mei 2025',
                'title' => 'Aksi Tanam 500 Pohon di Bantaran Sungai Karang Mumus',
                'desc' => 'Sobat Bumi Samarinda bersama relawan dan warga menanam 500 pohon untuk ruang hijau kota.',
                'img' => asset('assets/images/news/news1.JPG'),
                'image' => asset('assets/images/news/news1.JPG'),
                'author' => 'Admin SBS',
                'views' => 0,
                'content' => "Sobat Bumi Samarinda bersama relawan dan warga melaksanakan aksi tanam pohon di kawasan Bantaran Sungai Karang Mumus.\n\nKegiatan ini menjadi bagian dari upaya membangun ruang hijau kota sekaligus mengajak pelajar untuk terlibat dalam aksi lingkungan yang nyata.",
                'tags' => ['Lingkungan', 'Aksi', 'Samarinda'],
            ],
            [
                'id' => 'kurangi-plastik-sekali-pakai',
                'db_id' => null,
                'slug' => 'kurangi-plastik-sekali-pakai',
                'key' => 'informasi',
                'category' => 'Informasi',
                'date' => '15 Mei 2025',
                'title' => 'Kurangi Plastik Sekali Pakai, Mulai dari Hal Kecil',
                'desc' => 'Mengurangi plastik sekali pakai bisa dimulai dari kebiasaan sederhana yang konsisten.',
                'img' => asset('assets/images/news/news2.JPG'),
                'image' => asset('assets/images/news/news2.JPG'),
                'author' => 'Admin SBS',
                'views' => 0,
                'content' => "Mengurangi plastik sekali pakai dapat dimulai dari kebiasaan kecil seperti membawa botol minum, menggunakan tas belanja, dan memilih wadah pakai ulang.\n\nLangkah sederhana yang dilakukan secara konsisten dapat memberi dampak besar bagi lingkungan.",
                'tags' => ['Plastik', 'Edukasi', 'Lingkungan'],
            ],
            [
                'id' => 'bersih-bersih-tepi-sungai',
                'db_id' => null,
                'slug' => 'bersih-bersih-tepi-sungai',
                'key' => 'kegiatan',
                'category' => 'Kegiatan',
                'date' => '10 Mei 2025',
                'title' => 'Bersih-Bersih Tepi Sungai Bersama Komunitas',
                'desc' => 'Kegiatan bersih-bersih sungai untuk membangun lingkungan yang lebih sehat dan nyaman.',
                'img' => asset('assets/images/news/news3.jpg'),
                'image' => asset('assets/images/news/news3.jpg'),
                'author' => 'Admin SBS',
                'views' => 0,
                'content' => "Sobat Bumi Samarinda mengadakan kegiatan bersih-bersih tepi sungai bersama komunitas dan warga sekitar.\n\nKegiatan ini bertujuan membangun kesadaran bahwa sungai yang bersih adalah tanggung jawab bersama.",
                'tags' => ['Sungai', 'Komunitas', 'Aksi'],
            ],
        ];
    }
}   