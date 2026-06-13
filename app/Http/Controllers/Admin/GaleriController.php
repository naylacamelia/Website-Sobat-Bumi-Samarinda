<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'lokasi' => $request->query('lokasi', 'semua'),
            'status' => $request->query('status', 'semua'),
        ];

        $stats = [
            'total' => Galeri::count(),
            'aktif' => Galeri::where('aktif', true)->count(),
            'beranda' => Galeri::where('lokasi', 'beranda')->count(),
            'tentang' => Galeri::where('lokasi', 'tentang')->count(),
            'testimoni' => Galeri::where('lokasi', 'testimoni')->count(),
        ];

        $galeri = Galeri::query()
            ->when($filters['q'], function ($query, $keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('judul', 'like', "%{$keyword}%")
                        ->orWhere('alt_gambar', 'like', "%{$keyword}%")
                        ->orWhere('lokasi', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['lokasi'] !== 'semua', fn ($query) => $query->where('lokasi', $filters['lokasi']))
            ->when($filters['status'] === 'aktif', fn ($query) => $query->where('aktif', true))
            ->when($filters['status'] === 'nonaktif', fn ($query) => $query->where('aktif', false))
            ->orderBy('lokasi')
            ->orderBy('urutan')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.galeri.index', compact('galeri', 'stats', 'filters'));
    }

    public function create()
    {
        $nextOrder = (int) Galeri::max('urutan') + 1;

        return view('admin.galeri.create', [
            'galeri' => new Galeri([
                'lokasi' => 'beranda',
                'urutan' => $nextOrder ?: 1,
                'aktif' => true,
            ]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateGaleri($request, true);

        $data['gambar'] = $request->file('gambar')->store('galeri', 'public');
        $data['aktif'] = $request->boolean('aktif');

        Galeri::create($data);

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function edit(Galeri $galeri)
    {
        return view('admin.galeri.edit', [
            'galeri' => $galeri,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Galeri $galeri)
    {
        $data = $this->validateGaleri($request, false);
        $data['aktif'] = $request->boolean('aktif');

        if ($request->hasFile('gambar')) {
            $this->deletePublicFile($galeri->gambar);
            $data['gambar'] = $request->file('gambar')->store('galeri', 'public');
        }

        $galeri->update($data);

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Foto galeri berhasil diperbarui.');
    }

    public function toggle(Galeri $galeri)
    {
        $galeri->update([
            'aktif' => ! $galeri->aktif,
        ]);

        return back()->with('success', 'Status foto galeri berhasil diperbarui.');
    }

    public function destroy(Galeri $galeri)
    {
        $this->deletePublicFile($galeri->gambar);
        $galeri->delete();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Foto galeri berhasil dihapus.');
    }

    private function validateGaleri(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'alt_gambar' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['required', 'in:beranda,tentang,kegiatan,testimoni'],
            'urutan' => ['required', 'integer', 'min:1', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
            'gambar' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'gambar.required' => 'Gambar galeri wajib diunggah.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'gambar.max' => 'Ukuran gambar maksimal 5MB.',
            'lokasi.required' => 'Lokasi tampil wajib dipilih.',
            'lokasi.in' => 'Lokasi tampil tidak valid.',
            'urutan.required' => 'Urutan tampil wajib diisi.',
        ]);
    }

    private function deletePublicFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, ['http://', 'https://', 'assets/'])) {
            return;
        }

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
