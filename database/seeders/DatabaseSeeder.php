<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::updateOrCreate(
            ['email' => 'admin@sobatbumi.id'],
            [
                'nama' => 'Admin Sobat Bumi',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        $kategoriBerita = [
            'Aksi Lingkungan',
            'Kolaborasi',
            'Program',
            'Edukasi',
            'Komunitas',
        ];

        foreach ($kategoriBerita as $nama) {
            DB::table('kategori')->updateOrInsert(
                ['slug' => Str::slug($nama)],
                [
                    'nama' => $nama,
                    'tipe' => 'berita',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $kategoriKegiatan = [
            'Aksi Sobat Bumi',
            'Desa Energi Berdikari',
            'Sekolah Energi Berdikari',
        ];

        foreach ($kategoriKegiatan as $nama) {
            DB::table('kategori')->updateOrInsert(
                ['slug' => Str::slug($nama)],
                [
                    'nama' => $nama,
                    'tipe' => 'kegiatan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

$fokus = [
    [
        'tipe' => 'individu',
        'nomor' => 1,
        'judul' => 'Mulai dari Kebiasaan Kecil',
        'isi' => 'Mendorong perubahan perilaku ramah lingkungan dari kebiasaan sehari-hari.',
        'urutan' => 1,
    ],
    [
        'tipe' => 'individu',
        'nomor' => 2,
        'judul' => 'Berani Bergerak',
        'isi' => 'Mengajak anak muda untuk terlibat langsung dalam aksi lingkungan.',
        'urutan' => 2,
    ],
    [
        'tipe' => 'lingkungan',
        'nomor' => 1,
        'judul' => 'Pemulihan Ekosistem',
        'isi' => 'Mendukung kegiatan penghijauan, penanaman pohon, dan pelestarian kawasan pesisir.',
        'urutan' => 1,
    ],
    [
        'tipe' => 'lingkungan',
        'nomor' => 2,
        'judul' => 'Edukasi Keberlanjutan',
        'isi' => 'Menghadirkan ruang belajar tentang energi bersih, sampah, dan perubahan iklim.',
        'urutan' => 2,
    ],
];

foreach ($fokus as $item) {
    DB::table('fokus_gerakan')->updateOrInsert(
        [
            'tipe' => $item['tipe'],
            'nomor' => $item['nomor'],
        ],
        [
            'judul' => $item['judul'],
            'isi' => $item['isi'],
            'urutan' => $item['urutan'],
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
}

        $linimasa = [
            [
                'tahun' => '2021',
                'judul' => 'Awal Gerakan',
                'deskripsi' => 'Sobat Bumi Samarinda mulai bergerak sebagai ruang kolaborasi pelajar peduli lingkungan.',
                'urutan' => 1,
            ],
            [
                'tahun' => '2023',
                'judul' => 'Kolaborasi Komunitas',
                'deskripsi' => 'Gerakan diperluas melalui aksi lingkungan, edukasi, dan kerja sama lintas pihak.',
                'urutan' => 2,
            ],
            [
                'tahun' => '2025',
                'judul' => 'Penguatan Program',
                'deskripsi' => 'Program rutin dikembangkan melalui Aksi Sobat Bumi, Desa Energi Berdikari, dan Sekolah Energi Berdikari.',
                'urutan' => 3,
            ],
            [
                'tahun' => '2026',
                'judul' => 'Digitalisasi Program',
                'deskripsi' => 'Implementasi teknologi digital dalam mengelola dan mempromosikan program lingkungan.',
                'urutan' => 3,
            ],
        ];

        foreach ($linimasa as $item) {
            DB::table('linimasa')->updateOrInsert(
                [
                    'tahun' => $item['tahun'],
                    'judul' => $item['judul'],
                ],
                [
                    'deskripsi' => $item['deskripsi'],
                    'urutan' => $item['urutan'],
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $kegiatan = [
            [
                'kategori_slug' => 'aksi-sobat-bumi',
                'judul' => 'Aksi Sobat Bumi',
                'slug' => 'aksi-sobat-bumi',
                'deskripsi' => 'Program aksi lingkungan yang berfokus pada penghijauan, bersih lingkungan, dan kolaborasi relawan muda.',
                'gambar' => 'assets/images/hero.JPG',
                'status' => 'aktif',
                'urutan' => 1,
            ],
            [
                'kategori_slug' => 'desa-energi-berdikari',
                'judul' => 'Desa Energi Berdikari',
                'slug' => 'desa-energi-berdikari',
                'deskripsi' => 'Program pemberdayaan masyarakat berbasis energi bersih dan keberlanjutan lingkungan.',
                'gambar' => 'assets/images/hero.JPG',
                'status' => 'aktif',
                'urutan' => 2,
            ],
            [
                'kategori_slug' => 'sekolah-energi-berdikari',
                'judul' => 'Sekolah Energi Berdikari',
                'slug' => 'sekolah-energi-berdikari',
                'deskripsi' => 'Program edukasi energi bersih untuk pelajar agar isu keberlanjutan lebih mudah dipahami sejak dini.',
                'gambar' => 'assets/images/hero.JPG',
                'status' => 'aktif',
                'urutan' => 3,
            ],
        ];

        foreach ($kegiatan as $item) {
            $kategoriId = DB::table('kategori')
                ->where('slug', $item['kategori_slug'])
                ->value('id');

            DB::table('kegiatan')->updateOrInsert(
                ['slug' => $item['slug']],
                [
                    'kategori_id' => $kategoriId,
                    'judul' => $item['judul'],
                    'deskripsi' => $item['deskripsi'],
                    'gambar' => $item['gambar'],
                    'alt_gambar' => $item['judul'],
                    'tanggal' => null,
                    'lokasi' => 'Samarinda',
                    'status' => $item['status'],
                    'urutan' => $item['urutan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}