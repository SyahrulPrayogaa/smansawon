{{--
    File: resources/views/pages/home.blade.php
    Tahap: componentized landing page

    Catatan:
    - File ini sekarang hanya bertugas menyusun section.
    - Detail markup utama dipindahkan ke layout dan components.
    - Untuk sementara data masih statis memakai array.
    - Nanti array ini bisa diganti dengan data dari controller/database.
--}}

<x-layouts.app title="SMA Negeri 1 Wonosari - Sekolah Berprestasi dan Berkarakter"
    description="Website resmi sekolah untuk informasi profil, prestasi, PPDB, berita, agenda, dan layanan sekolah.">

    <x-site.hero />

    <x-site.stats :items="$stats" />

    <x-site.principal-message />

    <section id="program" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-6">
            <x-site.section-heading eyebrow="Program Sekolah" title="Program Unggulan"
                description="Program sekolah dirancang untuk mendukung prestasi akademik, pembentukan karakter, literasi digital, dan pengembangan minat bakat siswa."
                align="center" />

            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($programs as $program)
                    <x-site.program-card :number="$program['number']" :title="$program['title']" :description="$program['description']" />
                @endforeach
            </div>
        </div>
    </section>

    <x-site.facilities :items="$facilities" />

    <x-site.ppdb />

    <section id="prestasi" class="mx-auto max-w-7xl px-6 py-20">
        <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <x-site.section-heading eyebrow="Prestasi" title="Prestasi Terbaru" />
            <a href="#" class="font-bold text-blue-900 hover:text-blue-700">Lihat Semua Prestasi</a>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($achievements as $achievement)
                <x-site.content-card :category="$achievement->category ?? 'Prestasi'" :title="$achievement->title" :description="$achievement->description" :image="$achievement->image" />
            @endforeach
        </div>
    </section>

    <section id="berita" class="bg-white py-20">
        <div class="mx-auto grid max-w-7xl gap-10 px-6 lg:grid-cols-[1.4fr_0.8fr]">
            <div>
                <div class="mb-10 flex items-end justify-between gap-4">
                    <x-site.section-heading eyebrow="Berita" title="Berita Terbaru" />
                    <a href="{{ route('posts.index') }}"
                        class="hidden font-bold text-blue-900 hover:text-blue-700 md:block">Semua Berita</a>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($posts as $post)
                        <x-site.content-card :category="$post->category?->name ?? 'Berita'" :title="$post->title" :description="$post->excerpt" :image="$post->thumbnail"
                            :href="route('posts.show', $post->slug)" />
                    @endforeach
                </div>
            </div>

            <x-site.agenda-list :items="$agendas" />
        </div>
    </section>


    <x-site.gallery :images="$galleryImages" />

    <x-site.cta />
</x-layouts.app>
