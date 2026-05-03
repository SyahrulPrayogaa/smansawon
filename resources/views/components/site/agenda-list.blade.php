@props([
    'items' => [],
])

<aside id="agenda" class="rounded-3xl bg-slate-50 p-6">
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-600">
            Agenda
        </p>

        <h2 class="mt-3 text-2xl font-extrabold text-blue-950">
            Agenda Sekolah
        </h2>
    </div>

    <div class="space-y-4">
        @forelse ($items as $item)
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-blue-900">
                    {{ $item->start_date->translatedFormat('d F Y') }}
                </p>

                <h3 class="mt-1 font-extrabold text-blue-950">
                    {{ $item->title }}
                </h3>

                @if ($item->location)
                    <p class="mt-2 text-sm text-slate-500">
                        {{ $item->location }}
                    </p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl bg-white p-5 text-sm text-slate-500 shadow-sm">
                Agenda belum tersedia.
            </div>
        @endforelse
    </div>
</aside>
