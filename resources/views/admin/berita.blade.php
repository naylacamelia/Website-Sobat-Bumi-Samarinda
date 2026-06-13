@extends('layouts.admin')

@section('title', 'Kelola Berita')
@section('page_title', 'Kelola Berita')

@section('content')
@php
    $articles = collect($articles ?? []);

    $categories = $articles
        ->pluck('category')
        ->filter()
        ->unique()
        ->values();

    $icons = [
        'search' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m16.5 16.5 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'kebab' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="5" r="1.8" fill="currentColor"/><circle cx="12" cy="12" r="1.8" fill="currentColor"/><circle cx="12" cy="19" r="1.8" fill="currentColor"/></svg>',
    ];
@endphp

<div class="page-shell manage-news-page">
    <header class="page-header compact">
        <div>
            <h1>Kelola Berita</h1>
            <p>Atur artikel yang tampil di halaman berita, detail artikel, dan berita terbaru.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="admin-alert success" data-toast="success" data-toast-title="Berhasil">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert danger" data-toast="danger" data-toast-title="Aksi gagal">{{ session('error') }}</div>
    @endif

    <section class="admin-card table-card manage-news-card">
        <div class="card-header clean">
            <div>
                <h2>Daftar Artikel</h2>
                
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
                    <option value="all">Semua Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table manage-news-table" id="dashboardArticleTable" data-per-page="10">
                <colgroup>
                    <col class="col-thumb">
                    <col class="col-title">
                    <col class="col-category">
                    <col class="col-status">
                    <col class="col-featured">
                    <col class="col-date">
                    <col class="col-action">
                </colgroup>

                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Artikel</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($articles as $article)
                        @php
                            $articleId = data_get($article, 'id');
                            $title = data_get($article, 'title', '-');
                            $summary = data_get($article, 'summary', '-');
                            $category = data_get($article, 'category', '-');
                            $status = strtolower(data_get($article, 'status', 'draft'));
                            $featured = (bool) data_get($article, 'featured', false);
                            $date = data_get($article, 'date', '-');
                            $author = data_get($article, 'author', 'Admin');
                            $searchText = strtolower($title . ' ' . $summary . ' ' . $author . ' ' . $category);
                            $editUrl = route('admin.edit-berita', $articleId);
                            $imageUrl = data_get($article, 'image') ?: asset('assets/images/placeholder-news.svg');
                        @endphp

                        <tr
                            class="clickable-row"
                            data-row-link="{{ $editUrl }}"
                            data-title="{{ $searchText }}"
                            data-category="{{ strtolower($category) }}"
                            data-status="{{ strtolower($status) }}"
                            data-author="{{ strtolower($author) }}"
                            tabindex="0"
                            aria-label="Edit artikel {{ $title }}"
                        >
                            <td>
                                <img
                                    class="table-thumb"
                                    src="{{ $imageUrl }}"
                                    alt="{{ $title }}"
                                    loading="lazy"
                                    data-fallback="{{ asset('assets/images/placeholder-news.svg') }}"
                                    onerror="this.onerror=null;this.src=this.dataset.fallback;"
                                >
                            </td>

                            <td>
                                <div class="article-title-cell">
                                    <strong>{{ $title }}</strong>
                                    <span>{{ $summary }}</span>
                                </div>
                            </td>

                            <td>
                                <span class="category-pill">{{ $category }}</span>
                            </td>

                            <td>
                                <span class="status-pill {{ $status === 'published' ? 'published' : 'draft' }}">
                                    {{ $status === 'published' ? 'Published' : 'Draft' }}
                                </span>
                            </td>

                            <td>
                                <span class="featured-pill {{ $featured ? 'is-featured' : '' }}">
                                    {{ $featured ? 'Unggulan' : 'Biasa' }}
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
                        <td colspan="7">Tidak ada artikel yang cocok dengan filter.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer class="table-footer">
            <span id="tableInfo">Menampilkan 0–0 dari 0 artikel</span>
            <div class="pagination-mini" id="articlePagination" aria-label="Pagination artikel"></div>
        </footer>
    </section>
</div>
@endsection