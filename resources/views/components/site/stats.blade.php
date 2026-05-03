@props([
    'items' => [],
])

<section class="relative z-10 -mt-10 px-6">
    <div class="mx-auto grid max-w-6xl grid-cols-2 gap-4 rounded-3xl bg-white p-5 shadow-xl md:grid-cols-4">
        @foreach ($items as $item)
            <div class="rounded-2xl bg-slate-50 p-5 text-center">
                <p class="text-3xl font-extrabold text-blue-900">
                    {{ $item['value'] }}
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-500">
                    {{ $item['label'] }}
                </p>
            </div>
        @endforeach
    </div>
</section>
