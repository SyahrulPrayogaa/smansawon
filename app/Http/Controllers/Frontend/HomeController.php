<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Agenda;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $stats = [
            ['value' => '766', 'label' => 'Siswa'],
            ['value' => '94', 'label' => 'Guru & Tendik'],
            ['value' => '22', 'label' => 'Rombel'],
            ['value' => '100+', 'label' => 'Prestasi'],
        ];

        $programs = [
            [
                'number' => '01',
                'title' => 'Akademik Unggul',
                'description' => 'Pembelajaran terarah, literasi, numerasi, riset, dan penguatan kompetensi siswa.',
            ],
            [
                'number' => '02',
                'title' => 'Karakter & Budaya',
                'description' => 'Pembinaan kedisiplinan, kepedulian sosial, religiusitas, dan budaya positif sekolah.',
            ],
            [
                'number' => '03',
                'title' => 'Digital School',
                'description' => 'Informasi sekolah, pengumuman, presensi, bank soal, dan layanan digital terpadu.',
            ],
            [
                'number' => '04',
                'title' => 'Ekstrakurikuler',
                'description' => 'Wadah pengembangan minat, bakat, kepemimpinan, seni, olahraga, dan organisasi.',
            ],
        ];

        $posts = Post::query()
            ->with('category')
            ->where('is_published', true)
            ->latest('published_at')
            ->take(2)
            ->get();

        $agendas = Agenda::query()
            ->where('is_published', true)
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $achievements = Achievement::query()
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $facilities = Facility::query()
            ->where('is_published', true)
            ->take(6)
            ->get();

        $galleryImages = Gallery::query()
            ->where('is_published', true)
            ->latest()
            ->take(4)
            ->get();

        return view('pages.home', compact(
            'stats',
            'programs',
            'facilities',
            'achievements',
            'posts',
            'agendas',
            'galleryImages',
        ));
    }
}
