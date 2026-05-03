@props([
    'images' => [],
])

<section class="mx-auto max-w-7xl px-6 py-20">
    <div class="mx-auto mb-10 max-w-2xl text-center">
        <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-600">
            Galeri
        </p>

        <h2 class="mt-3 text-3xl font-extrabold text-blue-950 md:text-4xl">
            Dokumentasi Kegiatan
        </h2>

        <p class="mt-4 leading-7 text-slate-600">
            Sekilas kegiatan akademik, kesiswaan, prestasi, dan budaya sekolah.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        @forelse ($images as $item)
            <img src="{{ $item->image }}" alt="{{ $item->title ?? 'Galeri kegiatan sekolah' }}"
                class="aspect-square rounded-3xl object-cover shadow">
        @empty
            <div class="col-span-full rounded-2xl bg-white p-6 text-center text-slate-500 shadow">
                Galeri belum tersedia.
            </div>
        @endforelse
    </div>
</section>
