<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\TestimoniController;
use App\Http\Controllers\HalamanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HalamanController::class, 'home'])
    ->name('home');

Route::get('/tentang-kami', [HalamanController::class, 'tentang'])
    ->name('tentang');

Route::get('/berita', [HalamanController::class, 'berita'])
    ->name('berita');

Route::get('/berita/{identifier}', [HalamanController::class, 'detailBerita'])
    ->name('berita.detail');

Route::get('/kegiatan', [HalamanController::class, 'kegiatan'])
    ->name('kegiatan');

Route::get('/kontak', [HalamanController::class, 'kontak'])
    ->name('kontak');

Route::post('/kontak', [HalamanController::class, 'kirimKontak'])
    ->name('kontak.kirim');


/*
|--------------------------------------------------------------------------
| LOGIN FALLBACK
|--------------------------------------------------------------------------
| Laravel kadang butuh route bernama "login" saat middleware auth gagal.
| Jadi route /login diarahkan ke halaman login admin.
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| ADMIN AUTH ROUTES
|--------------------------------------------------------------------------
| Route login admin.
| Tidak perlu dibuat dobel.
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->name('admin.login.post');

/*
|--------------------------------------------------------------------------
| ADMIN PANEL ROUTES
|--------------------------------------------------------------------------
| Semua route di bawah ini hanya bisa diakses setelah login sebagai admin.
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        })->name('index');

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | ADMIN BERITA ROUTES
        |--------------------------------------------------------------------------
        */

        Route::get('/berita', [BeritaController::class, 'index'])
            ->name('berita');

        Route::get('/berita/tambah', [BeritaController::class, 'create'])
            ->name('tambah-berita');

        Route::post('/berita', [BeritaController::class, 'store'])
            ->name('simpan-berita');

        Route::get('/berita/{id}/edit', [BeritaController::class, 'edit'])
            ->name('edit-berita');

        Route::put('/berita/{id}', [BeritaController::class, 'update'])
            ->name('update-berita');

        Route::delete('/berita/{id}', [BeritaController::class, 'destroy'])
            ->name('hapus-berita');

        /*
        |--------------------------------------------------------------------------
        | ADMIN GALERI ROUTES
        |--------------------------------------------------------------------------
        */

        Route::get('/galeri', [GaleriController::class, 'index'])
            ->name('galeri');

        Route::get('/galeri/tambah', [GaleriController::class, 'create'])
            ->name('tambah-galeri');

        Route::post('/galeri', [GaleriController::class, 'store'])
            ->name('simpan-galeri');

        Route::get('/galeri/{galeri}/edit', [GaleriController::class, 'edit'])
            ->name('edit-galeri');

        Route::put('/galeri/{galeri}', [GaleriController::class, 'update'])
            ->name('update-galeri');

        Route::patch('/galeri/{galeri}/toggle', [GaleriController::class, 'toggle'])
            ->name('toggle-galeri');

        Route::delete('/galeri/{galeri}', [GaleriController::class, 'destroy'])
            ->name('hapus-galeri');

        /*
        |--------------------------------------------------------------------------
        | ADMIN TESTIMONI ROUTES
        |--------------------------------------------------------------------------
        */

        Route::get('/testimoni', [TestimoniController::class, 'index'])
            ->name('testimoni');

        Route::get('/testimoni/tambah', [TestimoniController::class, 'create'])
            ->name('tambah-testimoni');

        Route::post('/testimoni', [TestimoniController::class, 'store'])
            ->name('simpan-testimoni');

        Route::get('/testimoni/{testimoni}/edit', [TestimoniController::class, 'edit'])
            ->name('edit-testimoni');

        Route::put('/testimoni/{testimoni}', [TestimoniController::class, 'update'])
            ->name('update-testimoni');

        Route::patch('/testimoni/{testimoni}/toggle', [TestimoniController::class, 'toggle'])
            ->name('toggle-testimoni');

        Route::delete('/testimoni/{testimoni}', [TestimoniController::class, 'destroy'])
            ->name('hapus-testimoni');
    });
