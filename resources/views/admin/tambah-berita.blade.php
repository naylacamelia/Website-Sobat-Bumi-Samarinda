@extends('layouts.admin')

@section('title', ($isEdit ?? false ? 'Edit' : 'Tambah') . ' Berita')
@section('page_title', ($isEdit ?? false ? 'Edit' : 'Tambah') . ' Berita')

@section('content')
@php
    $articleData = $article ?? null;
    $articleId = data_get($articleData, 'id', request()->route('id'));
    $isEditMode = $isEdit ?? false;

    $categoryOptions = collect($categories ?? ['Kegiatan', 'Informasi', 'Edukasi', 'Pengumuman'])
        ->filter()
        ->values()
        ->all();

    $adminName = auth('admin')->user()?->nama ?? 'Admin SBS';

    $currentStatus = old('status', data_get($articleData, 'status', 'draft'));
    $currentCategory = old('category', data_get($articleData, 'category', $categoryOptions[0] ?? ''));
    $currentFeatured = old('featured', data_get($articleData, 'featured', false));

    $existingImage = data_get($articleData, 'image', data_get($articleData, 'cover_url', ''));
    $hasExistingImage = filled($existingImage);

    $icons = [
        'save' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 4h10l2 2v14H6V4Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 4v6h6V4M9 17h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'send' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 12 16-7-7 16-2-7-7-2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="m11 13 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'preview' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 12s3.3-6 9-6 9 6 9 6-3.3 6-9 6-9-6-9-6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>',

        'upload' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 16V5M8 9l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 16v2.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'close' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',

        'plus' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',

        'image' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="m4 16 4.5-4.5 3.5 3.5 2-2L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

        'link' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l1.4-1.4a5 5 0 0 0-7.1-7.1L10.5 5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11a5 5 0 0 0-7.1 0l-1.4 1.4a5 5 0 0 0 7.1 7.1l.9-.9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

        'list' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',

        'quote' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 10h4v8H6v-6a6 6 0 0 1 6-6M18 10h2v8h-6v-6a6 6 0 0 1 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];
@endphp

<div class="page-shell article-editor-page">
    <header class="page-header compact article-editor-header">
        <div>
            <h1>{{ $isEditMode ? 'Edit Berita' : 'Tambah Berita' }}</h1>
            <p>{{ $isEditMode ? 'Perbarui berita yang sudah tersimpan.' : 'Tulis berita baru untuk ditampilkan di halaman publik.' }}</p>
        </div>
    </header>

    @if(session('error'))
        <div class="admin-alert danger" data-toast="danger" data-toast-title="Aksi gagal">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-alert danger" data-toast="danger" data-toast-title="Form belum bisa disimpan">
            <strong>Form belum bisa disimpan.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ $isEditMode ? route('admin.update-berita', $articleId) : route('admin.simpan-berita') }}"
        enctype="multipart/form-data"
        id="articleForm"
        class="article-form"
        data-validate-form
        novalidate
    >
        @csrf

        @if($isEditMode)
            @method('PUT')
        @endif

        <input type="hidden" name="remove_image" id="removeImageFlag" value="0">

        <div class="article-editor-grid">
            <section class="admin-card article-compose-card">
                <div class="card-body">
                    <div class="form-field">
                        <label class="form-label" for="title">Judul Berita <span>*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-input article-title-input"
                            placeholder="Contoh: Aksi Bersih Sungai Bersama Sobat Bumi"
                            value="{{ old('title', data_get($articleData, 'title', '')) }}"
                            required
                            autocomplete="off"
                        >
                    </div>

                    <div class="form-grid-2">
                        <div class="form-field">
                            <label class="form-label" for="slug">Link Berita</label>
                            <div class="input-prefix article-slug-input">
                                <span>/berita/</span>
                                <input
                                    type="text"
                                    id="slug"
                                    name="slug"
                                    placeholder="judul-berita"
                                    value="{{ old('slug', data_get($articleData, 'slug', '')) }}"
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="tags">Kata Kunci <small>opsional</small></label>
                            <input
                                type="text"
                                id="tags"
                                name="tags"
                                class="form-input"
                                placeholder="Contoh: lingkungan, aksi, samarinda"
                                value="{{ old('tags', data_get($articleData, 'tags', '')) }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="summary">Ringkasan Singkat <span>*</span></label>
                        <textarea
                            id="summary"
                            name="summary"
                            class="form-input article-summary-input"
                            rows="4"
                            maxlength="200"
                            placeholder="Tulis gambaran singkat isi berita..."
                            required
                        >{{ old('summary', data_get($articleData, 'summary', '')) }}</textarea>

                        <div class="form-hint-row">
                            <p class="form-hint">Ringkasan ini akan tampil di card berita.</p>
                            <p class="form-hint"><span id="summaryCounter">0</span>/200</p>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="content">Isi Berita <span>*</span></label>

                        <div class="editor-box article-editor-box">
                            <div class="editor-toolbar" aria-label="Toolbar editor artikel">
                                <button type="button" class="editor-tool" data-editor-action="bold" title="Tebal"><strong>B</strong></button>
                                <button type="button" class="editor-tool" data-editor-action="italic" title="Miring"><em>I</em></button>
                                <button type="button" class="editor-tool" data-editor-action="underline" title="Garis bawah"><u>U</u></button>
                                <button type="button" class="editor-tool" data-editor-action="ul" title="Daftar poin">{!! $icons['list'] !!}</button>
                                <button type="button" class="editor-tool" data-editor-action="ol" title="Daftar angka">1.</button>
                                <button type="button" class="editor-tool" data-editor-action="quote" title="Kutipan">{!! $icons['quote'] !!}</button>
                                <button type="button" class="editor-tool" data-editor-action="image" title="Tambah gambar">{!! $icons['image'] !!}</button>
                                <button type="button" class="editor-tool" data-editor-action="link" title="Tambah link">{!! $icons['link'] !!}</button>
                                <button type="button" class="editor-tool" data-editor-action="code" title="Kode">&lt;/&gt;</button>
                            </div>

                            <textarea
                                id="content"
                                name="content"
                                rows="16"
                                placeholder="Tulis isi berita lengkap di sini..."
                                required
                            >{{ old('content', data_get($articleData, 'content', '')) }}</textarea>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-field">
                            <label class="form-label" for="meta_description">Ringkasan untuk Google <small>opsional</small></label>
                            <input
                                type="text"
                                id="meta_description"
                                name="meta_description"
                                class="form-input"
                                placeholder="Ringkasan pendek agar berita mudah ditemukan"
                                value="{{ old('meta_description', data_get($articleData, 'meta_description', '')) }}"
                                autocomplete="off"
                            >
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="image_alt">Keterangan Gambar <small>opsional</small></label>
                            <input
                                type="text"
                                id="image_alt"
                                name="image_alt"
                                class="form-input"
                                placeholder="Contoh: Relawan menanam pohon"
                                value="{{ old('image_alt', data_get($articleData, 'image_alt', '')) }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="form-field no-margin">
                        <label class="form-label" for="imageInput">Foto Sampul</label>

                        <div class="upload-box article-upload-box {{ $hasExistingImage ? 'has-preview' : '' }}" id="uploadBox" role="button" tabindex="0" aria-label="Pilih gambar artikel">
                            <input
                                type="file"
                                id="imageInput"
                                name="image"
                                accept="image/png,image/jpeg,image/webp"
                            >

                            <div id="uploadPreview" class="upload-preview" {{ $hasExistingImage ? '' : 'hidden' }}>
                                <img
                                    id="previewImg"
                                    src="{{ $existingImage }}"
                                    alt="Preview gambar artikel"
                                    data-fallback="{{ asset('assets/images/placeholder-news.svg') }}"
                                    onerror="this.onerror=null;this.src=this.dataset.fallback;"
                                >

                                <button
                                    type="button"
                                    class="upload-remove-btn"
                                    id="removeImageBtn"
                                    aria-label="Hapus gambar"
                                >
                                    {!! $icons['close'] !!}
                                </button>
                            </div>

                            <div id="uploadPlaceholder" class="upload-placeholder" {{ $hasExistingImage ? 'hidden' : '' }}>
                                <span>{!! $icons['upload'] !!}</span>
                                <strong>Klik untuk unggah atau seret file ke sini</strong>
                                <small>PNG, JPG, WebP. Maksimal 5MB. Rekomendasi 16:9.</small>
                            </div>

                            <div class="upload-change-hint" {{ $hasExistingImage ? '' : 'hidden' }}>Klik area gambar untuk mengganti foto sampul</div>
                        </div>

                        <p class="form-hint" id="uploadHint">
                            {{ $hasExistingImage ? 'Foto sampul sudah tersedia.' : 'Belum ada file baru dipilih.' }}
                        </p>
                    </div>
                </div>
            </section>

            <aside class="article-settings-column">
                <section class="admin-card article-settings-card">
                    <div class="card-body">
                        <h2>Pengaturan</h2>

                        <div class="form-field">
                            <label class="form-label" for="status">Status Berita</label>
                            <select id="status" name="status" class="form-input article-status-select" required>
                                <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ $currentStatus === 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="category">Kategori Berita <span>*</span></label>

                            <select id="category" name="category" class="form-input article-category-select" required>
                                <option value="" disabled {{ $currentCategory ? '' : 'selected' }}>Pilih kategori berita</option>

                                @foreach($categoryOptions as $category)
                                    <option value="{{ $category }}" {{ $currentCategory === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach

                                @if($currentCategory && !in_array($currentCategory, $categoryOptions))
                                    <option value="{{ $currentCategory }}" selected>{{ $currentCategory }}</option>
                                @endif
                            </select>

                            <button type="button" class="add-category-btn" id="addCategoryBtn">
                                {!! $icons['plus'] !!}
                                Tambah kategori baru
                            </button>

                            <div class="custom-category-box" id="customCategoryBox" hidden>
                                <label class="sr-only" for="customCategoryInput">Nama kategori baru</label>

                                <input
                                    type="text"
                                    id="customCategoryInput"
                                    name="custom_category"
                                    class="form-input custom-category-input"
                                    placeholder="Contoh: Kegiatan Sekolah"
                                    autocomplete="off"
                                >

                                <button type="button" class="admin-btn admin-btn-secondary admin-btn-sm" id="saveCategoryBtn">
                                    Tambahkan
                                </button>
                            </div>
                        </div>

                        <label class="featured-toggle" for="featured">
                            <input
                                type="checkbox"
                                id="featured"
                                name="featured"
                                value="1"
                                {{ $currentFeatured ? 'checked' : '' }}
                            >
                            <span></span>
                            <strong>Jadikan artikel unggulan</strong>
                        </label>

                        <div class="settings-meta">
                            <div>
                                <span>Penulis</span>
                                <strong id="authorPreviewText">{{ old('author', data_get($articleData, 'author', $adminName)) }}</strong>
                            </div>

                            <div>
                                <span>Terakhir diubah</span>
                                <strong>Hari ini</strong>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="publish_date">Tanggal Terbit</label>
                            <input
                                type="date"
                                id="publish_date"
                                name="publish_date"
                                class="form-input"
                                value="{{ old('publish_date', data_get($articleData, 'publish_date', date('Y-m-d'))) }}"
                            >
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="author">Nama Penulis</label>
                            <input
                                type="text"
                                id="author"
                                name="author"
                                class="form-input"
                                value="{{ old('author', data_get($articleData, 'author', $adminName)) }}"
                                autocomplete="name"
                            >
                        </div>

                        <div class="form-actions article-form-actions">
                            <button type="submit" name="action" value="save" class="admin-btn admin-btn-primary full" id="publishBtn">
                                {!! $icons['send'] !!}
                                Terbitkan Berita
                            </button>

                            <button type="submit" name="action" value="draft" class="admin-btn admin-btn-secondary full" id="draftBtn">
                                {!! $icons['save'] !!}
                                Simpan Draft
                            </button>

                            <button type="button" class="admin-btn admin-btn-secondary full preview-action-btn" id="previewBtn" hidden>
                                {!! $icons['preview'] !!}
                                Preview Berita
                            </button>

                            @if($isEditMode)
                                <button type="button" class="text-danger-link as-button" id="deleteArticleBtn" data-confirm-title="Hapus berita?" data-confirm-message="Data berita yang dihapus tidak bisa dikembalikan." data-confirm-ok="Hapus">
                                    Hapus
                                </button>
                            @else
                                <a href="{{ route('admin.berita') }}" class="text-danger-link">Batal</a>
                            @endif
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </form>

    @if($isEditMode)
        <form id="deleteForm" method="POST" action="{{ route('admin.hapus-berita', $articleId) }}" hidden>
            @csrf
            @method('DELETE')
        </form>
    @endif

    <div class="article-preview-modal" id="previewModal" hidden>
        <div class="article-preview-backdrop" data-preview-close></div>

        <section class="article-preview-dialog" role="dialog" aria-modal="true" aria-labelledby="previewTitle">
            <header>
                <div>
                    <span>Preview Berita</span>
                    <h2 id="previewTitle">Judul berita</h2>
                </div>

                <button type="button" class="preview-close-btn" data-preview-close aria-label="Tutup preview">
                    {!! $icons['close'] !!}
                </button>
            </header>

            <div class="preview-cover" id="previewCover" hidden>
                <img id="previewCoverImg" src="" alt="Preview foto sampul">
            </div>

            <div class="preview-meta">
                <span id="previewCategory">Kategori</span>
                <span id="previewStatus">Status</span>
                <span id="previewAuthor">Penulis</span>
            </div>

            <p id="previewSummary" class="preview-summary">Ringkasan berita akan tampil di sini.</p>
            <article id="previewContent" class="preview-content">Isi berita akan tampil di sini.</article>
        </section>
    </div>
</div>
@endsection
