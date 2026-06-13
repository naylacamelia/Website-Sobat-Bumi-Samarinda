@php
    $lokasiLabels = [
        'beranda' => 'Beranda',
        'tentang' => 'Tentang Kami',
        'kegiatan' => 'Kegiatan',
        'testimoni' => 'Testimoni',
    ];

    $isEdit = $isEdit ?? false;
    $formAction = $isEdit ? route('admin.update-galeri', $galeri) : route('admin.simpan-galeri');
@endphp

<div class="page-shell article-editor-page cms-form-page">
    <header class="page-header article-editor-header">
        <div>
            <h1>{{ $isEdit ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' }}</h1>
            <p>{{ $isEdit ? 'Perbarui foto, lokasi tampil, dan status gambar.' : 'Tambahkan gambar baru untuk ditampilkan di halaman publik.' }}</p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('admin.galeri') }}" class="admin-btn admin-btn-secondary">Kembali</a>
        </div>
    </header>

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

    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="article-editor-grid cms-editor-grid">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <article class="admin-card article-compose-card">
            <div class="card-body">
                <div class="form-field">
                    <label for="judul" class="form-label">Judul Foto <small>(opsional)</small></label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        class="form-input large"
                        value="{{ old('judul', $galeri->judul) }}"
                        placeholder="Contoh: Aksi Penanaman Pohon"
                    >
                </div>

                <div class="form-field">
                    <label for="alt_gambar" class="form-label">Alt Text Gambar</label>
                    <input
                        type="text"
                        id="alt_gambar"
                        name="alt_gambar"
                        class="form-input"
                        value="{{ old('alt_gambar', $galeri->alt_gambar) }}"
                        placeholder="Deskripsi singkat gambar untuk aksesibilitas"
                    >
                    <p class="form-hint">Alt text membantu aksesibilitas dan SEO. Jangan dibiarkan kosong kalau gambarnya punya makna.</p>
                </div>

                <div class="form-field no-margin">
                    <label for="gambar" class="form-label">Gambar {{ $isEdit ? '' : '*' }}</label>

                    <label class="article-upload-box cms-upload-box" for="gambar">
                        <input type="file" id="gambar" name="gambar" accept="image/*" data-preview-input data-preview-target="galeriPreview">

                        <div class="upload-placeholder" id="galeriUploadPlaceholder" @if($isEdit && $galeri->gambar) hidden @endif>
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="m4 16 4.5-4.5 3.5 3.5 2-2L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <strong>Klik untuk unggah gambar</strong>
                            <small>JPG, PNG, JPEG, atau WEBP maksimal 5MB.</small>
                        </div>

                        <div class="upload-preview" id="galeriPreview" @if(!($isEdit && $galeri->gambar)) hidden @endif>
                            <img src="{{ $isEdit && $galeri->gambar ? $galeri->gambar_url : '' }}" alt="Preview gambar galeri">
                        </div>
                    </label>
                </div>
            </div>
        </article>

        <aside class="article-settings-column">
            <article class="admin-card article-settings-card">
                <div class="card-body">
                    <h2>Pengaturan</h2>

                    <div class="form-field">
                        <label for="lokasi" class="form-label">Lokasi Tampil *</label>
                        <select id="lokasi" name="lokasi" class="form-input article-category-select" required>
                            @foreach($lokasiLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('lokasi', $galeri->lokasi) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="urutan" class="form-label">Urutan Tampil *</label>
                        <input type="number" id="urutan" name="urutan" min="1" max="255" class="form-input" value="{{ old('urutan', $galeri->urutan ?: 1) }}" required>
                    </div>

                    <label class="featured-toggle cms-toggle">
                        <input type="hidden" name="aktif" value="0">
                        <input type="checkbox" name="aktif" value="1" @checked((bool) old('aktif', $galeri->aktif ?? true))>
                        <span aria-hidden="true"></span>
                        <strong>Aktifkan foto di halaman publik</strong>
                    </label>

                    <div class="settings-meta">
                        <div>
                            <span>Status</span>
                            <strong>{{ old('aktif', $galeri->aktif ?? true) ? 'Aktif' : 'Nonaktif' }}</strong>
                        </div>
                        <div>
                            <span>Lokasi</span>
                            <strong>{{ $lokasiLabels[old('lokasi', $galeri->lokasi ?? 'beranda')] ?? 'Beranda' }}</strong>
                        </div>
                    </div>

                    <div class="article-form-actions">
                        <button type="submit" class="admin-btn admin-btn-primary full">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Foto' }}
                        </button>
                        <a href="{{ route('admin.galeri') }}" class="admin-btn admin-btn-secondary full">Batal</a>
                    </div>
                </div>
            </article>
        </aside>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-preview-input]').forEach((input) => {
    input.addEventListener('change', () => {
        const target = document.getElementById(input.dataset.previewTarget);
        const placeholder = document.getElementById('galeriUploadPlaceholder');
        const file = input.files && input.files[0];

        if (!target || !file) return;

        const image = target.querySelector('img');
        image.src = URL.createObjectURL(file);
        target.hidden = false;
        if (placeholder) placeholder.hidden = true;
    });
});
</script>
@endpush
