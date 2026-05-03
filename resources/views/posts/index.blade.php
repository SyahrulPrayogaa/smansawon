<x-layouts.app title="Berita Sekolah" description="Informasi berita terbaru dari sekolah.">
    <section class="bg-blue-950 py-20 text-white">
        <div class="mx-auto max-w-7xl px-6">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-300">
                Berita Sekolah
            </p>

            <h1 class="mt-3 text-4xl font-extrabold md:text-5xl">
                Informasi dan Kegiatan Terbaru
            </h1>

            <p class="mt-5 max-w-2xl leading-8 text-blue-100">
                Ikuti kabar terbaru seputar kegiatan akademik, kesiswaan, prestasi, pengumuman, dan aktivitas sekolah.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-site.content-card :category="$post->category?->name ?? 'Berita'" :title="$post->title" :description="$post->excerpt" :image="$post->thumbnail"
                    :href="route('posts.show', $post->slug)" />
            @empty
                <div class="col-span-full rounded-3xl bg-white p-8 text-center text-slate-500 shadow">
                    Berita belum tersedia.
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    </section>
</x-layouts.app>
