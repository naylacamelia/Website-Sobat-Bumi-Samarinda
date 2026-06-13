@extends('layouts.admin')

@section('title', 'Testimoni')
@section('page_title', 'Testimoni')

@section('content')
<div class="page-shell admin-cms-page testimonial-manager-page">
    <header class="page-header compact">
        <div>
            <h1>Testimoni</h1>
            <p>Kelola cerita dan kutipan anggota yang tampil pada slider testimoni di halaman beranda.</p>
        </div>

    </header>

    @if(session('success'))
        <div class="admin-alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="admin-alert danger">
            <strong>Data belum bisa disimpan.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="cms-stat-grid cms-stat-grid-three" aria-label="Statistik testimoni">
        <article class="cms-stat-card">
            <span>Total Testimoni</span>
            <strong>{{ $stats['total'] ?? 0 }}</strong>
        </article>
        <article class="cms-stat-card">
            <span>Aktif</span>
            <strong>{{ $stats['aktif'] ?? 0 }}</strong>
        </article>
        <article class="cms-stat-card">
            <span>Nonaktif</span>
            <strong>{{ $stats['nonaktif'] ?? 0 }}</strong>
        </article>
    </section>

    <article class="admin-card cms-list-card">
        <div class="card-header clean cms-filter-header">
            <div>
                <h2>Daftar Testimoni</h2>
                <p>Atur urutan dan status testimoni yang ingin ditampilkan di website.</p>
            </div>

            <form method="GET" action="{{ route('admin.testimoni') }}" class="cms-filter-form cms-filter-form-compact" data-auto-filter>
                <label class="dashboard-search">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m16.5 16.5 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari testimoni..." autocomplete="off">
                </label>

                <select name="status" class="admin-select admin-select-sm" aria-label="Filter status">
                    <option value="semua" @selected(($filters['status'] ?? 'semua') === 'semua')>Semua Status</option>
                    <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(($filters['status'] ?? '') === 'nonaktif')>Nonaktif</option>
                </select>

            </form>
        </div>

        <div class="card-body">
            @if($testimoni->count())
                <div class="cms-card-grid testimonial-grid">
                    @foreach($testimoni as $item)
                        <article class="cms-testimonial-card">
                            <div class="cms-testimonial-head">
                                <img src="{{ $item->foto_url }}" alt="Foto {{ $item->nama ?: 'pemberi testimoni' }}" loading="lazy">

                                <div>
                                    <span class="cms-status-badge {{ $item->aktif ? 'is-active' : 'is-muted' }}">
                                        {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <h3>{{ $item->nama }}</h3>
                                    <small>{{ $item->peran ?: 'Tanpa peran' }} · Urutan {{ $item->urutan }}</small>
                                </div>
                            </div>

                            <p>“{{ $item->isi }}”</p>

                            <div class="cms-card-actions">
                                <a href="{{ route('admin.edit-testimoni', $item) }}" class="admin-btn admin-btn-secondary admin-btn-sm">Edit</a>

                                <form method="POST" action="{{ route('admin.toggle-testimoni', $item) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.hapus-testimoni', $item) }}" onsubmit="return confirm('Hapus testimoni ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-empty-state">
                    <strong>Belum ada testimoni yang cocok.</strong>
                    <p>Tambah testimoni baru atau ubah filter pencarian yang sedang digunakan.</p>
                    <a href="{{ route('admin.tambah-testimoni') }}" class="admin-btn admin-btn-primary">Tambah Testimoni</a>
                </div>
            @endif
        </div>

        @if($testimoni->hasPages())
            <footer class="table-footer cms-list-footer">
                <span>Menampilkan {{ $testimoni->firstItem() ?? 0 }}–{{ $testimoni->lastItem() ?? 0 }} dari {{ $testimoni->total() }} testimoni</span>

                <div class="pagination-mini" aria-label="Pagination testimoni">
                    @if($testimoni->onFirstPage())
                        <button type="button" class="pagination-arrow" disabled aria-label="Sebelumnya"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.5 6.5 9 12l5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    @else
                        <a href="{{ $testimoni->previousPageUrl() }}" class="pagination-link pagination-arrow" aria-label="Halaman sebelumnya"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14.5 6.5 9 12l5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    @endif

                    <span class="pagination-current">{{ $testimoni->currentPage() }} / {{ $testimoni->lastPage() }}</span>

                    @if($testimoni->hasMorePages())
                        <a href="{{ $testimoni->nextPageUrl() }}" class="pagination-link pagination-arrow" aria-label="Halaman berikutnya"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9.5 6.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    @else
                        <button type="button" class="pagination-arrow" disabled aria-label="Berikutnya"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9.5 6.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                    @endif
                </div>
            </footer>
        @endif
    </article>

    @if(($stats['total'] ?? 0) > 0)
        <a href="{{ route('admin.tambah-testimoni') }}" class="cms-floating-add" aria-label="Tambah testimoni">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/>
            </svg>
            <span>Tambah</span>
        </a>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const form = document.querySelector('[data-auto-filter]');
    if (!form) return;

    let timer = null;
    const submit = () => form.requestSubmit ? form.requestSubmit() : form.submit();

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', submit);
    });

    form.querySelectorAll('input[type="search"]').forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(submit, 520);
        });
    });
})();
</script>
@endpush
@endsection
