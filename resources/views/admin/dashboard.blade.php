@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@php
    $articleData = collect($articles ?? $recentArticles ?? [
        [
            'id' => 1,
            'title' => 'Restorasi Kawasan Hijau di Bantaran Sungai Karang Mumus',
            'author' => 'Admin',
            'category' => 'Lingkungan',
            'status' => 'published',
            'date' => '22 Okt 2024',
            'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=200&q=80',
        ],
        [
            'id' => 2,
            'title' => 'Bank Sampah Digital untuk Komunitas Sekolah',
            'author' => 'Siti Aminah',
            'category' => 'Teknologi',
            'status' => 'draft',
            'date' => '21 Okt 2024',
            'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=200&q=80',
        ],
        [
            'id' => 3,
            'title' => 'Edukasi Lingkungan untuk Generasi Muda',
            'author' => 'Admin SBS',
            'category' => 'Edukasi',
            'status' => 'published',
            'date' => '18 Okt 2024',
            'image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=200&q=80',
        ],
    ]);

    $categories = $articleData
        ->pluck('category')
        ->filter()
        ->unique()
        ->values();

    $publishedCount = $articleData
        ->filter(fn ($article) => strtolower(data_get($article, 'status', '')) === 'published')
        ->count();

    $draftCount = $articleData
        ->filter(fn ($article) => strtolower(data_get($article, 'status', '')) === 'draft')
        ->count();

    $stats = [
        [
            'label' => 'Total Artikel',
            'value' => $totalArticles ?? $articleData->count(),
            'desc' => '+12% bulan ini',
            'tone' => 'lime',
            'icon' => 'article',
        ],
        [
            'label' => 'Published',
            'value' => $published ?? $publishedCount,
            'desc' => 'Sesuai jadwal publikasi',
            'tone' => 'green',
            'icon' => 'check',
        ],
        [
            'label' => 'Draft',
            'value' => $drafts ?? $draftCount,
            'desc' => 'Perlu direview',
            'tone' => 'yellow',
            'icon' => 'draft',
        ],
        [
            'label' => 'Total Views',
            'value' => $totalViews ?? '12.5k',
            'desc' => '+8% hari ini',
            'tone' => 'lime',
            'icon' => 'eye',
        ],
    ];

    $icons = [
        'article' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'check' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="m8.5 12.2 2.3 2.3 4.8-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        'draft' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 20h14M7 17l8.8-8.8a2 2 0 0 1 2.8 2.8L9.8 19H7v-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        'eye' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>',

        'search' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m16.5 16.5 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'plus' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/></svg>',

        'media' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="m4 16 4.5-4.5 3.5 3.5 2-2L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        'users' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="9" r="3" stroke="currentColor" stroke-width="2"/><path d="M20 18c0-1.7-1.1-3.1-2.7-3.7M17.5 7.2a2.5 2.5 0 0 1 0 4.6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'reports' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 19V5M5 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="m8 15 3-3 2.2 2.2L18 9.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        'external' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 5h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="m13 11 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 14v3.5A1.5 1.5 0 0 1 17.5 19h-11A1.5 1.5 0 0 1 5 17.5v-11A1.5 1.5 0 0 1 6.5 5H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'kebab' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="5" r="1.8" fill="currentColor"/><circle cx="12" cy="12" r="1.8" fill="currentColor"/><circle cx="12" cy="19" r="1.8" fill="currentColor"/></svg>',
    ];
@endphp

<div class="page-shell dashboard-page">
    <header class="page-header compact">
        <div>
            <h1>Dashboard</h1>
            <p>Selamat datang kembali, Admin. Berikut ringkasan performa konten hari ini.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="admin-alert success" data-toast="success" data-toast-title="Berhasil">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert danger" data-toast="danger" data-toast-title="Aksi gagal">{{ session('error') }}</div>
    @endif

    <section class="stat-grid" aria-label="Statistik konten">
        @foreach($stats as $stat)
            <article class="stat-card tone-{{ $stat['tone'] }}">
                <div class="stat-icon">
                    {!! $icons[$stat['icon']] !!}
                </div>

                <div class="stat-copy">
                    <span>{{ $stat['label'] }}</span>
                    <strong>{{ $stat['value'] }}</strong>
                    <p>{{ $stat['desc'] }}</p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="dashboard-grid">
        <article class="admin-card table-card">
            <div class="card-header clean">
                <div>
                    <h2>Artikel Terbaru</h2>
                    <p>Konten terakhir yang dikelola admin.</p>
                </div>

                <div class="table-filters" aria-label="Filter artikel">
                    <label class="dashboard-search">
                        {!! $icons['search'] !!}
                        <input
                            type="search"
                            id="articleSearch"
                            placeholder="Cari artikel..."
                            autocomplete="off"
                        >
                    </label>

                    <select id="categoryFilter" class="admin-select admin-select-sm" aria-label="Filter kategori">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ strtolower($category) }}">{{ $category }}</option>
                        @endforeach
                    </select>

                    <select id="statusFilter" class="admin-select admin-select-sm" aria-label="Filter status">
                        <option value="all">Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

<div class="admin-table-wrap">
    <table class="admin-table" id="dashboardArticleTable" data-per-page="10">
        <colgroup>
            <col class="col-thumb">
            <col class="col-title">
            <col class="col-category">
            <col class="col-status">
            <col class="col-date">
            <col class="col-action">
        </colgroup>

        <thead>
            <tr>
                <th>Thumbnail</th>
                <th>Judul Artikel</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($articleData as $article)
                @php
                    $status = strtolower(data_get($article, 'status', 'draft'));
                    $articleId = data_get($article, 'id');
                    $title = data_get($article, 'title', '-');
                    $category = data_get($article, 'category', '-');
                    $author = data_get($article, 'author', 'Admin');
                    $date = data_get($article, 'date', '-');
                @endphp

                <tr
                    data-title="{{ strtolower($title) }}"
                    data-category="{{ strtolower($category) }}"
                    data-status="{{ strtolower($status) }}"
                    data-author="{{ strtolower($author) }}"
                >
                    <td>
                        <img
                            class="table-thumb"
                            src="{{ data_get($article, 'image') }}"
                            alt="{{ $title }}"
                        >
                    </td>

                    <td>
                        <div class="article-title-cell">
                            <strong>{{ $title }}</strong>
                        </div>
                    </td>

                    <td>
                        <span class="category-pill">
                            {{ $category }}
                        </span>
                    </td>

                    <td>
                        <span class="status-pill {{ $status === 'published' ? 'published' : 'draft' }}">
                            {{ $status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </td>

                    <td>
                        <span class="date-text">{{ $date }}</span>
                    </td>

<td class="text-right">
    <div class="table-action-menu" data-action-menu>
        <button
            type="button"
            class="table-kebab-btn"
            data-action-menu-trigger
            aria-label="Buka opsi artikel {{ $title }}"
            aria-haspopup="true"
            aria-expanded="false"
        >
            {!! $icons['kebab'] !!}
        </button>

        <div class="table-action-panel" data-action-menu-panel>
            <a
                href="{{ route('admin.edit-berita', $articleId) }}"
                class="table-action-item"
            >
                Edit
            </a>

            <form
                method="POST"
                action="{{ route('admin.hapus-berita', $articleId) }}"
                data-confirm
                data-confirm-title="Hapus berita?"
                data-confirm-message="Data berita yang dihapus tidak bisa dikembalikan."
                data-confirm-ok="Hapus"
            >
                @csrf
                @method('DELETE')

                <button type="submit" class="table-action-item danger">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</td>
                </tr>
            @endforeach

            <tr id="emptyRow" class="empty-row" hidden>
                <td colspan="6">Tidak ada artikel yang cocok dengan filter.</td>
            </tr>
        </tbody>
    </table>
</div>

            <footer class="table-footer">
                <span id="tableInfo">Menampilkan 0–0 dari 0 artikel</span>
                <div class="pagination-mini" id="articlePagination" aria-label="Pagination artikel"></div>
            </footer>
        </article>

        <aside class="dashboard-side">
            <article class="admin-card action-card">
                <h2>Quick Actions</h2>

                <div class="action-grid">
                    <a href="{{ route('admin.tambah-berita') }}" class="quick-action">
                        <span>{!! $icons['plus'] !!}</span>
                        <strong>Tambah Berita</strong>
                    </a>

                    <a href="{{ route('admin.galeri') }}" class="quick-action">
                        <span>{!! $icons['media'] !!}</span>
                        <strong>Kelola Galeri</strong>
                    </a>

                    <a href="{{ route('admin.testimoni') }}" class="quick-action">
                        <span>{!! $icons['users'] !!}</span>
                        <strong>Kelola Testimoni</strong>
                    </a>

<a href="{{ route('home') }}" class="quick-action" target="_self">
                        <span>{!! $icons['external'] !!}</span>
                        <strong>Lihat Website</strong>
                    </a>
                </div>
            </article>
        </aside>
    </section>
</div>
@endsection
