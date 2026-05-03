@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'left',
])

<div @class(['max-w-2xl', 'mx-auto text-center' => $align === 'center'])>
    @if ($eyebrow)
        <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-600">
            {{ $eyebrow }}
        </p>
    @endif

    <h2 class="mt-3 text-3xl font-extrabold text-blue-950 md:text-4xl">
        {{ $title }}
    </h2>

    @if ($description)
        <p class="mt-4 leading-7 text-slate-600">
            {{ $description }}
        </p>
    @endif
</div>
