@props([
    'category' => 'Informasi',
    'title',
    'description' => null,
    'image' => null,
    'href' => null,
])

<article
    class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <img src="{{ $image ?: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=900&q=80' }}"
        alt="{{ $title }}" class="aspect-video w-full object-cover">

    <div class="p-6">
        <p class="text-xs font-bold uppercase tracking-wide text-yellow-600">
            {{ $category }}
        </p>

        <h3 class="mt-2 text-lg font-extrabold text-blue-950">
            @if ($href)
                <a href="{{ $href }}" class="hover:text-blue-700">
                    {{ $title }}
                </a>
            @else
                {{ $title }}
            @endif
        </h3>

        @if ($description)
            <p class="mt-3 text-sm leading-6 text-slate-600">
                {{ $description }}
            </p>
        @endif

        @if ($href)
            <a href="{{ $href }}" class="mt-5 inline-flex text-sm font-bold text-blue-900 hover:text-blue-700">
                Baca Selengkapnya
            </a>
        @endif
    </div>
</article>
