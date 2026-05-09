<x-layouts.app title="Agenda Sekolah" description="Daftar agenda dan kegiatan sekolah.">
    <section class="border-b border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-4xl px-6 py-12 md:py-16">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-600">
                Agenda Sekolah
            </p>

            <h1 class="mt-3 text-3xl font-extrabold leading-tight text-blue-950 md:text-5xl">
                Jadwal Kegiatan Sekolah
            </h1>

            <p class="mt-5 max-w-2xl leading-8 text-slate-600">
                Informasi agenda kegiatan akademik, kesiswaan, rapat, pertemuan orang tua, dan aktivitas sekolah
                lainnya.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-6 py-12">
        <div class="space-y-5">
            @forelse ($agendas as $agenda)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-sm font-bold text-blue-900">
                                {{ $agenda->start_date->translatedFormat('d F Y') }}
                                @if ($agenda->end_date)
                                    - {{ $agenda->end_date->translatedFormat('d F Y') }}
                                @endif
                            </p>

                            <h2 class="mt-2 text-xl font-extrabold text-blue-950">
                                {{ $agenda->title }}
                            </h2>

                            @if ($agenda->location)
                                <p class="mt-2 text-sm font-semibold text-slate-500">
                                    Lokasi: {{ $agenda->location }}
                                </p>
                            @endif

                            @if ($agenda->description)
                                <p class="mt-4 leading-7 text-slate-600">
                                    {{ $agenda->description }}
                                </p>
                            @endif
                        </div>

                        <div class="shrink-0 rounded-2xl bg-blue-50 px-4 py-3 text-center text-blue-950">
                            <p class="text-2xl font-extrabold">
                                {{ $agenda->start_date->translatedFormat('d') }}
                            </p>
                            <p class="text-xs font-bold uppercase">
                                {{ $agenda->start_date->translatedFormat('M') }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl bg-white p-8 text-center text-slate-500 shadow-sm">
                    Agenda belum tersedia.
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $agendas->links() }}
        </div>
    </section>
</x-layouts.app>
