<x-layouts.app :title="$post->title" :description="$post->excerpt ?? 'Detail berita sekolah.'">
    <article>
        <section class="bg-blue-950 py-20 text-white">
            <div class="mx-auto max-w-4xl px-6 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-yellow-300">
                    {{ $post->category?->name ?? 'Berita' }}
                </p>

                <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-5xl">
                    {{ $post->title }}
                </h1>

                @if ($post->published_at)
                    <p class="mt-5 text-blue-100">
                        Dipublikasikan pada {{ $post->published_at->translatedFormat('d F Y') }}
                    </p>
                @endif
            </div>
        </section>

        <section class="mx-auto max-w-4xl px-6 py-12">
            @if ($post->thumbnail)
                <section class="mx-auto px-6 pt-8" style="max-width: 920px;">
                    <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}"
                        class="h-auto w-full rounded-2xl object-cover shadow-sm" style="max-height: 360px;">
                </section>
            @endif

            {{-- @if ($post->excerpt)
                <section class="mx-auto px-6 py-10 md:py-12" style="max-width: 760px;">
                    <p class="mb-8 rounded-3xl bg-blue-50 p-6 text-lg leading-8 text-blue-950">
                        {{ $post->excerpt }}
                    </p>
                </section>
            @endif --}}

            <section class="mx-auto px-6 mt-3 py-10 md:py-12" style="max-width: 760px;">
                <div class="leading-8 text-slate-700">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </section>
        </section>
    </article>

    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <x-site.section-heading eyebrow="Berita Lainnya" title="Baca Juga" />

                <a href="{{ route('posts.index') }}" class="font-bold text-blue-900 hover:text-blue-700">
                    Semua Berita
                </a>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                @forelse ($relatedPosts as $relatedPost)
                    <x-site.content-card :category="$relatedPost->category?->name ?? 'Berita'" :title="$relatedPost->title" :description="$relatedPost->excerpt" :image="$relatedPost->thumbnail"
                        :href="route('posts.show', $relatedPost->slug)" />
                @empty
                    <div class="col-span-full rounded-3xl bg-white p-8 text-center text-slate-500 shadow">
                        Belum ada berita lainnya.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
