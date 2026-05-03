<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Achievement;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $category = PostCategory::create([
            'name' => 'Kegiatan Sekolah',
            'slug' => 'kegiatan-sekolah',
        ]);

        Post::create([
            'post_category_id' => $category->id,
            'title' => 'Kegiatan Projek Penguatan Profil Pelajar',
            'slug' => Str::slug('Kegiatan Projek Penguatan Profil Pelajar'),
            'thumbnail' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=900&q=80',
            'excerpt' => 'Siswa mengikuti kegiatan pembelajaran berbasis proyek untuk menguatkan karakter dan kolaborasi.',
            'content' => 'Isi lengkap berita kegiatan projek penguatan profil pelajar.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Post::create([
            'post_category_id' => $category->id,
            'title' => 'Workshop Literasi Digital untuk Siswa',
            'slug' => Str::slug('Workshop Literasi Digital untuk Siswa'),
            'thumbnail' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?auto=format&fit=crop&w=900&q=80',
            'excerpt' => 'Program literasi digital membantu siswa menggunakan teknologi secara produktif dan bertanggung jawab.',
            'content' => 'Isi lengkap berita workshop literasi digital.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Agenda::create([
            'title' => 'Rapat Orang Tua Siswa',
            'location' => 'Aula Sekolah',
            'description' => 'Pertemuan orang tua siswa bersama pihak sekolah.',
            'start_date' => '2026-06-12 08:00:00',
            'end_date' => '2026-06-12 10:00:00',
            'is_published' => true,
        ]);

        Agenda::create([
            'title' => 'Kegiatan Class Meeting',
            'location' => 'Lapangan Sekolah',
            'description' => 'Kegiatan class meeting setelah pelaksanaan ujian.',
            'start_date' => '2026-06-20 08:00:00',
            'end_date' => '2026-06-20 12:00:00',
            'is_published' => true,
        ]);

        Achievement::create([
            'title' => 'Juara Olimpiade Sains Tingkat Kabupaten',
            'category' => 'Akademik',
            'student_name' => 'Nama Siswa',
            'level' => 'Kabupaten',
            'year' => '2026',
            'image' => 'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?auto=format&fit=crop&w=900&q=80',
            'description' => 'Siswa berhasil meraih prestasi membanggakan dalam ajang kompetisi sains.',
            'is_published' => true,
        ]);

        Achievement::create([
            'title' => 'Tim Sekolah Raih Juara Turnamen Pelajar',
            'category' => 'Olahraga',
            'student_name' => null,
            'level' => 'Kabupaten',
            'year' => '2026',
            'image' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=900&q=80',
            'description' => 'Pembinaan ekstrakurikuler terus mendorong siswa berprestasi di berbagai bidang.',
            'is_published' => true,
        ]);

        foreach (['Laboratorium', 'Perpustakaan', 'Ruang Kelas', 'Aula', 'Lapangan', 'Masjid'] as $facility) {
            Facility::create([
                'name' => $facility,
                'description' => 'Fasilitas sekolah untuk menunjang kegiatan belajar dan pengembangan siswa.',
                'is_published' => true,
            ]);
        }

        foreach (
            [
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=700&q=80',
                'https://images.unsplash.com/photo-1577896852618-3db4e042d86f?auto=format&fit=crop&w=700&q=80',
                'https://images.unsplash.com/photo-1577896852264-91c0d9a047af?auto=format&fit=crop&w=700&q=80',
                'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=700&q=80',
            ] as $index => $image
        ) {
            Gallery::create([
                'title' => 'Galeri Kegiatan ' . ($index + 1),
                'image' => $image,
                'description' => 'Dokumentasi kegiatan sekolah.',
                'is_published' => true,
            ]);
        }
    }
}
