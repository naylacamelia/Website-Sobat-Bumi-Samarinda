@extends('layouts.admin')

@section('title', 'Galeri')
@section('page_title', 'Galeri')

@section('content')
@php
    $items = $galeri ?? collect();
    $filters = $filters ?? [
        'q' => '',
        'lokasi' => 'semua',
        'status' => 'semua',
    ];

    $hasCreateRoute = \Illuminate\Support\Facades\Route::has('admin.tambah-galeri');
    $hasEditRoute = \Illuminate\Support\Facades\Route::has('admin.edit-galeri');
    $hasToggleRoute = \Illuminate\Support\Facades\Route::has('admin.toggle-galeri');
    $hasDeleteRoute = \Illuminate\Support\Facades\Route::has('admin.hapus-galeri');

    $createUrl = $hasCreateRoute ? route('admin.tambah-galeri') : '#';

    $lokasiOptions = [
        'semua' => 'Semua Lokasi',
        'beranda' => 'Beranda',
        'tentang' => 'Tentang',
        'kegiatan' => 'Kegiatan',
        'testimoni' => 'Testimoni',
    ];

    function galeri_image_url($path) {
        if (!$path) {
            return asset('assets/img/placeholder.jpg');
        }

        $path = ltrim($path, '/');

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['storage/', 'assets/'])) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
@endphp

<div class="page-shell admin-cms-page gallery-manager-page">
    <header class="page-header compact">
        <div>
            <h1>Galeri</h1>
            <p>Kelola gambar yang tampil di halaman beranda, tentang, kegiatan, dan area visual lainnya.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="admin-alert success" data-toast="success" data-toast-title="Berhasil">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="admin-alert danger" data-toast="danger" data-toast-title="Gagal">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-alert danger" data-toast="danger" data-toast-title="Data belum valid">
            <strong>Data belum bisa disimpan.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="cms-stat-grid" aria-label="Statistik galeri">
        <article class="cms-stat-card">
            <span>Total Gambar</span>
            <strong>{{ $stats['total'] ?? 0 }}</strong>
        </article>

        <article class="cms-stat-card">
            <span>Aktif</span>
            <strong>{{ $stats['aktif'] ?? 0 }}</strong>
        </article>

        <article class="cms-stat-card">
            <span>Beranda</span>
            <strong>{{ $stats['beranda'] ?? 0 }}</strong>
        </article>

        <article class="cms-stat-card">
            <span>Tentang</span>
            <strong>{{ $stats['tentang'] ?? 0 }}</strong>
        </article>
    </section>

    <article class="admin-card cms-list-card">
        <div class="card-header clean cms-filter-header">
            <div>
                <h2>Daftar Galeri</h2>
                <p>Atur gambar berdasarkan lokasi tampil, urutan, dan status aktif.</p>
            </div>

            <form method="GET" action="{{ route('admin.galeri') }}" class="cms-filter-form" data-auto-filter>
                <label class="dashboard-search">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <path d="m16.5 16.5 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="Cari judul, alt, lokasi..."
                        autocomplete="off"
                    >
                </label>

                <select name="lokasi" class="admin-select admin-select-sm" aria-label="Filter lokasi">
                    @foreach($lokasiOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['lokasi'] ?? 'semua') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="admin-select admin-select-sm" aria-label="Filter status">
                    <option value="semua" @selected(($filters['status'] ?? 'semua') === 'semua')>Semua Status</option>
                    <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(($filters['status'] ?? '') === 'nonaktif')>Nonaktif</option>
                </select>
            </form>
        </div>

        <div class="card-body">
            @if($items->count())
                <div class="cms-card-grid gallery-grid">
                    @foreach($items as $item)
                        @php
                            $imageUrl = galeri_image_url($item->gambar ?? null);
                            $editUrl = $hasEditRoute ? route('admin.edit-galeri', $item) : '#';
                            $toggleUrl = $hasToggleRoute ? route('admin.toggle-galeri', $item) : '#';
                            $deleteUrl = $hasDeleteRoute ? route('admin.hapus-galeri', $item) : '#';
                            $lokasiLabel = $lokasiOptions[$item->lokasi ?? ''] ?? ucfirst((string) ($item->lokasi ?? 'Tanpa lokasi'));
                        @endphp

                        <article class="cms-media-card">
                            <div class="cms-media-thumb">
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $item->alt_gambar ?: ($item->judul ?: 'Gambar galeri') }}"
                                    loading="lazy"
                                >

                                <span class="cms-status-badge {{ $item->aktif ? 'is-active' : 'is-muted' }}">
                                    {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <div class="cms-media-body">
                                <div class="cms-media-meta">
                                    <span>{{ $lokasiLabel }}</span>
                                    <small>Urutan {{ $item->urutan ?? '-' }}</small>
                                </div>

                                <h3>{{ $item->judul ?: 'Tanpa judul' }}</h3>

                                <p>
                                    {{ $item->alt_gambar ?: 'Belum ada teks alternatif untuk gambar ini.' }}
                                </p>

                                <div class="cms-card-actions">
                                    <a href="{{ $editUrl }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ $toggleUrl }}"
                                        data-confirm
                                        data-confirm-title="{{ $item->aktif ? 'Nonaktifkan gambar?' : 'Aktifkan gambar?' }}"
                                        data-confirm-message="Status gambar ini akan diperbarui pada halaman publik."
                                        data-confirm-ok="{{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ $deleteUrl }}"
                                        data-confirm
                                        data-confirm-title="Hapus gambar?"
                                        data-confirm-message="Gambar yang dihapus tidak bisa dikembalikan."
                                        data-confirm-ok="Hapus"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-empty-state">
                    <strong>Belum ada gambar yang cocok.</strong>
                    <p>Tambah gambar baru atau ubah filter pencarian yang sedang digunakan.</p>
                    <a href="{{ $createUrl }}" class="admin-btn admin-btn-primary">
                        Tambah Gambar
                    </a>
                </div>
            @endif
        </div>

        @if(method_exists($items, 'hasPages') && $items->hasPages())
            <footer class="table-footer cms-list-footer">
                <span>
                    Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }}
                    dari {{ $items->total() }} gambar
                </span>

                <div class="pagination-mini" aria-label="Pagination galeri">
                    @if($items->onFirstPage())
                        <button type="button" class="pagination-arrow" disabled aria-label="Sebelumnya">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M14.5 6.5 9 12l5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="pagination-link pagination-arrow" aria-label="Halaman sebelumnya">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M14.5 6.5 9 12l5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif

                    <span class="pagination-current">
                        {{ $items->currentPage() }} / {{ $items->lastPage() }}
                    </span>

                    @if($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="pagination-link pagination-arrow" aria-label="Halaman berikutnya">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m9.5 6.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @else
                        <button type="button" class="pagination-arrow" disabled aria-label="Berikutnya">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m9.5 6.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </footer>
        @endif
    </article>

@if($items->count() > 0)
    <a href="{{ $createUrl }}" class="cms-floating-add" aria-label="Tambah gambar galeri">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/>
        </svg>
        <span>Tambah</span>
    </a>
@endif
</div>
@endsection