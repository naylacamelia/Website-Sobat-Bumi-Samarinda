<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BeritaController extends Controller
{
    private const DEFAULT_CATEGORIES = [
        'Kegiatan',
        'Informasi',
        'Edukasi',
        'Pengumuman',
    ];

    public function index()
    {
        $articles = Berita::with(['kategori', 'pengguna'])
            ->latest()
            ->get()
            ->map(fn (Berita $item) => $this->mapArticleForAdmin($item));

        return view('admin.berita', compact('articles'));
    }

    public function create()
    {
        return view('admin.tambah-berita', [
            'isEdit' => false,
            'article' => null,
            'categories' => $this->getCategoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateArticle($request);
        $gambarPath = null;

        try {
            $kategori = $this->resolveCategory($request);
            $gambarPath = $this->storeUploadedImage($request);

            Berita::create([
                'kategori_id' => $kategori?->id,
                'pengguna_id' => Auth::guard('admin')->id(),
                'judul' => $data['title'],
                'slug' => $this->makeUniqueSlug($request->input('slug') ?: $data['title']),
                'ringkasan' => $data['summary'],
                'isi' => $data['content'],
                'gambar' => $gambarPath,
                'alt_gambar' => $request->input('image_alt'),
                'penulis' => $request->input('author') ?: Auth::guard('admin')->user()?->nama ?: 'Admin SBS',
                'meta_deskripsi' => $request->input('meta_description'),
                'tag' => $this->parseTags($request->input('tags')),
                'unggulan' => $request->boolean('featured'),
                'status' => $this->resolveStatus($request),
                'tanggal_terbit' => $request->input('publish_date') ?: now(),
                'jumlah_dilihat' => 0,
            ]);

            return redirect()
                ->route('admin.berita')
                ->with('success', 'Berita berhasil disimpan.');
        } catch (Throwable $exception) {
            $this->deleteStoredImage($gambarPath);
            $this->logCrudError('store', $exception);

            return back()
                ->withInput()
                ->with('error', 'Berita gagal disimpan. Periksa koneksi/database lalu coba lagi.');
        }
    }

    public function edit($id)
    {
        $berita = Berita::with(['kategori', 'pengguna'])->findOrFail($id);

        return view('admin.tambah-berita', [
            'isEdit' => true,
            'article' => $this->mapArticleForForm($berita),
            'categories' => $this->getCategoryOptions(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        $data = $this->validateArticle($request, $berita->id);

        $oldImage = $berita->gambar;
        $newImage = null;

        try {
            $kategori = $this->resolveCategory($request);
            $gambarPath = $oldImage;
            $shouldDeleteOldImage = false;

            if ($request->boolean('remove_image') && ! $request->hasFile('image')) {
                $gambarPath = null;
                $shouldDeleteOldImage = filled($oldImage);
            }

            if ($request->hasFile('image')) {
                $newImage = $this->storeUploadedImage($request);
                $gambarPath = $newImage;
                $shouldDeleteOldImage = filled($oldImage);
            }

            $berita->update([
                'kategori_id' => $kategori?->id,
                'judul' => $data['title'],
                'slug' => $this->makeUniqueSlug($request->input('slug') ?: $data['title'], $berita->id),
                'ringkasan' => $data['summary'],
                'isi' => $data['content'],
                'gambar' => $gambarPath,
                'alt_gambar' => $request->input('image_alt'),
                'penulis' => $request->input('author') ?: Auth::guard('admin')->user()?->nama ?: 'Admin SBS',
                'meta_deskripsi' => $request->input('meta_description'),
                'tag' => $this->parseTags($request->input('tags')),
                'unggulan' => $request->boolean('featured'),
                'status' => $this->resolveStatus($request),
                'tanggal_terbit' => $request->input('publish_date') ?: $berita->tanggal_terbit,
            ]);

            if ($shouldDeleteOldImage) {
                $this->deleteStoredImage($oldImage);
            }

            return redirect()
                ->route('admin.berita')
                ->with('success', 'Berita berhasil diperbarui.');
        } catch (Throwable $exception) {
            $this->deleteStoredImage($newImage);
            $this->logCrudError('update', $exception);

            return back()
                ->withInput()
                ->with('error', 'Berita gagal diperbarui. Periksa data dan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $berita = Berita::findOrFail($id);
            $gambarPath = $berita->gambar;

            $berita->delete();
            $this->deleteStoredImage($gambarPath);

            return redirect()
                ->route('admin.berita')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (Throwable $exception) {
            $this->logCrudError('destroy', $exception);

            return back()
                ->with('error', 'Berita gagal dihapus. Data mungkin masih dipakai atau koneksi bermasalah.');
        }
    }

    private function validateArticle(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'category' => ['required_without:custom_category', 'nullable', 'string', 'max:255'],
            'custom_category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['nullable', 'in:draft,published'],
            'publish_date' => ['nullable', 'date'],
            'author' => ['nullable', 'string', 'max:255'],
            'featured' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
            'action' => ['nullable', 'in:save,draft'],
        ], [
            'title.required' => 'Judul berita wajib diisi.',
            'title.max' => 'Judul berita maksimal 255 karakter.',
            'summary.required' => 'Ringkasan singkat wajib diisi.',
            'summary.max' => 'Ringkasan singkat maksimal 200 karakter.',
            'content.required' => 'Isi berita wajib diisi.',
            'category.required_without' => 'Kategori berita wajib dipilih atau dibuat baru.',
            'custom_category.max' => 'Nama kategori baru maksimal 255 karakter.',
            'image.image' => 'File sampul harus berupa gambar.',
            'image.mimes' => 'Format foto sampul harus jpg, jpeg, png, atau webp.',
            'image.max' => 'Ukuran foto sampul maksimal 5MB.',
            'status.in' => 'Status berita tidak valid.',
            'publish_date.date' => 'Tanggal terbit tidak valid.',
            'author.max' => 'Nama penulis maksimal 255 karakter.',
            'action.in' => 'Aksi penyimpanan tidak valid.',
        ]);
    }

    private function resolveCategory(Request $request): ?Kategori
    {
        $namaKategori = trim((string) ($request->input('custom_category') ?: $request->input('category')));

        if (! $namaKategori) {
            return null;
        }

        return Kategori::firstOrCreate(
            [
                'slug' => Str::slug($namaKategori),
                'tipe' => 'berita',
            ],
            [
                'nama' => $namaKategori,
            ]
        );
    }

    private function resolveStatus(Request $request): string
    {
        if ($request->input('action') === 'draft') {
            return 'draft';
        }

        if ($request->input('action') === 'save') {
            return 'published';
        }

        return $request->input('status') === 'published' ? 'published' : 'draft';
    }

    private function storeUploadedImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('berita', 'public');
    }

    private function deleteStoredImage(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '/assets/', 'assets/'])) {
            return;
        }

        $path = Str::startsWith($path, 'public/')
            ? Str::after($path, 'public/')
            : ltrim($path, '/');

        $path = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'berita';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Berita::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function parseTags(?string $tags): ?array
    {
        if (! $tags) {
            return null;
        }

        return collect(explode(',', $tags))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->toArray();
    }

    private function getCategoryOptions(): array
    {
        $categories = Kategori::where('tipe', 'berita')
            ->orderBy('nama')
            ->pluck('nama')
            ->filter()
            ->values()
            ->all();

        return count($categories) ? $categories : self::DEFAULT_CATEGORIES;
    }

    private function mapArticleForAdmin(Berita $item): array
    {
        return [
            'id' => $item->id,
            'slug' => $item->slug,
            'title' => $item->judul,
            'summary' => $item->ringkasan,
            'category' => $item->kategori->nama ?? '-',
            'status' => $item->status,
            'featured' => $item->unggulan,
            'date' => optional($item->tanggal_terbit ?? $item->created_at)->translatedFormat('d M Y'),
            'author' => $item->penulis ?? $item->pengguna->nama ?? 'Admin SBS',
            'image' => $item->gambar_url,
            'raw_image' => $item->gambar,
            'views' => $item->jumlah_dilihat,
        ];
    }

    private function mapArticleForForm(Berita $item): array
    {
        return [
            'id' => $item->id,
            'slug' => $item->slug,
            'title' => $item->judul,
            'summary' => $item->ringkasan,
            'content' => $item->isi,
            'category' => $item->kategori->nama ?? '',
            'status' => $item->status,
            'featured' => $item->unggulan,
            'publish_date' => optional($item->tanggal_terbit)->format('Y-m-d'),
            'author' => $item->penulis ?? $item->pengguna->nama ?? 'Admin SBS',
            'image' => $item->gambar_url,
            'raw_image' => $item->gambar,
            'image_alt' => $item->alt_gambar,
            'meta_description' => $item->meta_deskripsi,
            'tags' => is_array($item->tag) ? implode(', ', $item->tag) : '',
        ];
    }

    private function logCrudError(string $action, Throwable $exception): void
    {
        Log::error("Berita {$action} failed", [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
