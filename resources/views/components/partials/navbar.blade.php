{{-- <div class="hidden bg-blue-950 text-white md:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-2 text-sm">
        <div class="flex items-center gap-6">
            <span>Email: info@sman1wonosari.sch.id</span>
            <span>Telepon: (0274) 000000</span>
        </div>

        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-yellow-300">Instagram</a>
            <a href="#" class="hover:text-yellow-300">YouTube</a>
            <a href="#" class="hover:text-yellow-300">Facebook</a>
        </div>
    </div>
</div> --}}

<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-white p-1 shadow-sm ring-1 ring-slate-200">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SMA Negeri 1 Wonosari"
                    class="h-full w-full object-contain">
            </div>

            <div>
                <p class="text-sm font-bold leading-tight text-blue-950 md:text-base">
                    SMA Negeri 1 Wonosari
                </p>
                <p class="text-xs text-slate-500">
                    Berprestasi, Berkarakter, Berbudaya
                </p>
            </div>
        </a>

        <div class="hidden items-center gap-7 text-sm font-semibold text-slate-700 lg:flex">
            <a href="{{ route('home') }}" class="hover:text-blue-800">Beranda</a>
            <a href="#profil" class="hover:text-blue-800">Profil</a>
            <a href="#program" class="hover:text-blue-800">Program</a>
            <a href="#prestasi" class="hover:text-blue-800">Prestasi</a>
            <a href="{{ route('posts.index') }}" class="hover:text-blue-800">Berita</a>
            <a href="{{ route('agendas.index') }}" class="hover:text-blue-800">Agenda</a>
            <a href="#kontak" class="hover:text-blue-800">Kontak</a>
        </div>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="#ppdb"
                class="rounded-full bg-yellow-400 px-5 py-2.5 text-sm font-bold text-blue-950 transition hover:bg-yellow-300">
                Info PPDB
            </a>

            <a href="#"
                class="rounded-full border border-blue-900 px-5 py-2.5 text-sm font-bold text-blue-900 transition hover:bg-blue-900 hover:text-white">
                Login
            </a>
        </div>

        <button class="rounded-xl border border-slate-200 p-2 lg:hidden" aria-label="Buka menu navigasi">
            ☰
        </button>
    </nav>
</header>
