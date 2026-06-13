<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class TestimoniController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'status' => $request->query('status', 'semua'),
        ];

        $stats = [
            'total' => Testimoni::count(),
            'aktif' => Testimoni::where('aktif', true)->count(),
            'nonaktif' => Testimoni::where('aktif', false)->count(),
        ];

        $testimoni = Testimoni::query()
            ->when($filters['q'], function ($query, $keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('peran', 'like', "%{$keyword}%")
                        ->orWhere('isi', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['status'] === 'aktif', fn ($query) => $query->where('aktif', true))
            ->when($filters['status'] === 'nonaktif', fn ($query) => $query->where('aktif', false))
            ->orderBy('urutan')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.testimoni.index', compact('testimoni', 'stats', 'filters'));
    }

    public function create()
    {
        $nextOrder = (int) Testimoni::max('urutan') + 1;

        return view('admin.testimoni.create', [
            'testimoni' => new Testimoni([
                'urutan' => $nextOrder ?: 1,
                'aktif' => true,
            ]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateTestimoni($request);
        $fotoPath = null;

        try {
            $data['aktif'] = $request->boolean('aktif');

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('testimoni', 'public');
                $data['foto'] = $fotoPath;
            }

            Testimoni::create($data);

            return redirect()
                ->route('admin.testimoni')
                ->with('success', 'Testimoni berhasil ditambahkan.');
        } catch (Throwable $exception) {
            $this->deletePublicFile($fotoPath);
            $this->logCrudError('store', $exception);

            return back()
                ->withInput()
                ->with('error', 'Testimoni gagal ditambahkan. Periksa data lalu coba lagi.');
        }
    }

    public function edit(Testimoni $testimoni)
    {
        return view('admin.testimoni.edit', [
            'testimoni' => $testimoni,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Testimoni $testimoni)
    {
        $data = $this->validateTestimoni($request);
        $oldPhoto = $testimoni->foto;
        $newPhoto = null;

        try {
            $data['aktif'] = $request->boolean('aktif');

            if ($request->hasFile('foto')) {
                $newPhoto = $request->file('foto')->store('testimoni', 'public');
                $data['foto'] = $newPhoto;
            }

            $testimoni->update($data);

            if ($newPhoto) {
                $this->deletePublicFile($oldPhoto);
            }

            return redirect()
                ->route('admin.testimoni')
                ->with('success', 'Testimoni berhasil diperbarui.');
        } catch (Throwable $exception) {
            $this->deletePublicFile($newPhoto);
            $this->logCrudError('update', $exception);

            return back()
                ->withInput()
                ->with('error', 'Testimoni gagal diperbarui. Periksa data lalu coba lagi.');
        }
    }

    public function toggle(Testimoni $testimoni)
    {
        try {
            $testimoni->update([
                'aktif' => ! $testimoni->aktif,
            ]);

            return back()->with('success', 'Status testimoni berhasil diperbarui.');
        } catch (Throwable $exception) {
            $this->logCrudError('toggle', $exception);

            return back()->with('error', 'Status testimoni gagal diperbarui.');
        }
    }

    public function destroy(Testimoni $testimoni)
    {
        try {
            $fotoPath = $testimoni->foto;

            $testimoni->delete();
            $this->deletePublicFile($fotoPath);

            return redirect()
                ->route('admin.testimoni')
                ->with('success', 'Testimoni berhasil dihapus.');
        } catch (Throwable $exception) {
            $this->logCrudError('destroy', $exception);

            return back()->with('error', 'Testimoni gagal dihapus.');
        }
    }

    private function validateTestimoni(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'peran' => ['nullable', 'string', 'max:255'],
            'isi' => ['required', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'urutan' => ['required', 'integer', 'min:1', 'max:255'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama pemberi testimoni wajib diisi.',
            'nama.max' => 'Nama pemberi testimoni maksimal 255 karakter.',
            'isi.required' => 'Isi testimoni wajib diisi.',
            'isi.max' => 'Isi testimoni maksimal 500 karakter.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
            'urutan.required' => 'Urutan tampil wajib diisi.',
            'urutan.integer' => 'Urutan tampil harus berupa angka.',
            'urutan.min' => 'Urutan tampil minimal 1.',
            'urutan.max' => 'Urutan tampil maksimal 255.',
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

    private function logCrudError(string $action, Throwable $exception): void
    {
        Log::error("Testimoni {$action} failed", [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
