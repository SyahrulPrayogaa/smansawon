@props([
    'items' => [],
])

<section class="mx-auto max-w-7xl px-6 py-20">
    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-600">
                Fasilitas
            </p>

            <h2 class="mt-3 text-3xl font-extrabold text-blue-950 md:text-4xl">
                Lingkungan Belajar yang Mendukung
            </h2>

            <p class="mt-4 leading-7 text-slate-600">
                Sekolah menyediakan berbagai fasilitas untuk menunjang proses belajar, kegiatan organisasi, pengembangan
                bakat, dan aktivitas siswa.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
            @forelse ($items as $item)
                <div class="rounded-2xl bg-white p-5 font-bold text-blue-950 shadow">
                    {{ $item->name }}
                </div>
            @empty
                <div class="col-span-full rounded-2xl bg-white p-5 text-slate-500 shadow">
                    Data fasilitas belum tersedia.
                </div>
            @endforelse
        </div>
    </div>
</section>
