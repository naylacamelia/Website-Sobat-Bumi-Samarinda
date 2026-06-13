@php
    $isEdit = $isEdit ?? false;
    $formAction = $isEdit ? route('admin.update-testimoni', $testimoni) : route('admin.simpan-testimoni');
@endphp

<div class="page-shell article-editor-page cms-form-page">
    <header class="page-header article-editor-header">
        <div>
            <h1>{{ $isEdit ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h1>
            <p>{{ $isEdit ? 'Perbarui nama, foto, isi, dan status testimoni.' : 'Tambahkan testimoni baru untuk slider di halaman beranda.' }}</p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('admin.testimoni') }}" class="admin-btn admin-btn-secondary">Kembali</a>
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
                <div class="form-grid-2">
                    <div class="form-field">
                        <label for="nama" class="form-label">Nama *</label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            class="form-input"
                            value="{{ old('nama', $testimoni->nama) }}"
                            placeholder="Nama pemberi testimoni"
                            required
                        >
                    </div>

                    <div class="form-field">
                        <label for="peran" class="form-label">Peran</label>
                        <input
                            type="text"
                            id="peran"
                            name="peran"
                            class="form-input"
                            value="{{ old('peran', $testimoni->peran) }}"
                            placeholder="Contoh: Anggota Sobat Bumi"
                        >
                    </div>
                </div>

                <div class="form-field">
                    <label for="isi" class="form-label">Isi Testimoni *</label>
                    <textarea
                        id="isi"
                        name="isi"
                        class="form-input article-summary-input"
                        maxlength="500"
                        placeholder="Tulis testimoni singkat dan natural."
                        required
                    >{{ old('isi', $testimoni->isi) }}</textarea>
                    <p class="form-hint">Maksimal 500 karakter. Teks yang terlalu panjang bikin slider terasa sesak, seperti rapat tanpa agenda.</p>
                </div>

                <div class="form-field no-margin">
                    <label for="foto" class="form-label">Foto <small>(opsional)</small></label>

                    <label class="article-upload-box cms-upload-box testimonial-upload-box" for="foto">
                        <input type="file" id="foto" name="foto" accept="image/*" data-preview-input data-preview-target="testimoniPreview">

                        <div class="upload-placeholder" id="testimoniUploadPlaceholder" @if($isEdit && $testimoni->foto) hidden @endif>
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="9" r="3" stroke="currentColor" stroke-width="2"/></svg>
                            </span>
                            <strong>Klik untuk unggah foto</strong>
                            <small>JPG, PNG, JPEG, atau WEBP maksimal 5MB.</small>
                        </div>

                        <div class="upload-preview" id="testimoniPreview" @if(!($isEdit && $testimoni->foto)) hidden @endif>
                            <img src="{{ $isEdit && $testimoni->foto ? $testimoni->foto_url : '' }}" alt="Preview foto testimoni">
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
                        <label for="urutan" class="form-label">Urutan Tampil *</label>
                        <input type="number" id="urutan" name="urutan" min="1" max="255" class="form-input" value="{{ old('urutan', $testimoni->urutan ?: 1) }}" required>
                    </div>

                    <label class="featured-toggle cms-toggle">
                        <input type="hidden" name="aktif" value="0">
                        <input type="checkbox" name="aktif" value="1" @checked((bool) old('aktif', $testimoni->aktif ?? true))>
                        <span aria-hidden="true"></span>
                        <strong>Aktifkan testimoni di beranda</strong>
                    </label>

                    <div class="settings-meta">
                        <div>
                            <span>Status</span>
                            <strong>{{ old('aktif', $testimoni->aktif ?? true) ? 'Aktif' : 'Nonaktif' }}</strong>
                        </div>
                        <div>
                            <span>Karakter</span>
                            <strong>{{ mb_strlen(old('isi', $testimoni->isi ?? '')) }}/500</strong>
                        </div>
                    </div>

                    <div class="article-form-actions">
                        <button type="submit" class="admin-btn admin-btn-primary full">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Testimoni' }}
                        </button>
                        <a href="{{ route('admin.testimoni') }}" class="admin-btn admin-btn-secondary full">Batal</a>
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
        const placeholder = document.getElementById('testimoniUploadPlaceholder');
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
