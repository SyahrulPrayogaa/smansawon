@props(['number', 'title', 'description'])

<div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 transition hover:-translate-y-1 hover:shadow-lg">
    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-900 text-white">
        {{ $number }}
    </div>

    <h3 class="text-lg font-extrabold text-blue-950">
        {{ $title }}
    </h3>

    <p class="mt-3 leading-7 text-slate-600">
        {{ $description }}
    </p>
</div>
