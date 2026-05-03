<footer id="kontak" class="bg-blue-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 md:grid-cols-4">
        <div class="md:col-span-2">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white p-1 shadow-sm">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo SMA Negeri 1 Wonosari"
                        class="h-full w-full object-contain">
                </div>
                <div>
                    <p class="font-extrabold">SMA Negeri 1 Wonosari</p>
                    <p class="text-sm text-blue-100">Berprestasi, Berkarakter, Berbudaya</p>
                </div>
            </div>

            <p class="mt-5 max-w-md leading-7 text-blue-100">
                Website resmi sekolah sebagai pusat informasi profil, berita, agenda, pengumuman, prestasi, PPDB, dan
                layanan sekolah.
            </p>
        </div>

        <div>
            <h3 class="font-extrabold">Link Cepat</h3>
            <div class="mt-4 space-y-3 text-sm text-blue-100">
                <a href="#profil" class="block hover:text-yellow-300">Profil Sekolah</a>
                <a href="#program" class="block hover:text-yellow-300">Program</a>
                <a href="#ppdb" class="block hover:text-yellow-300">PPDB</a>
                <a href="#berita" class="block hover:text-yellow-300">Berita</a>
            </div>
        </div>

        <div>
            <h3 class="font-extrabold">Kontak</h3>
            <div class="mt-4 space-y-3 text-sm leading-6 text-blue-100">
                <p>Jl. Contoh Alamat Sekolah, Wonosari</p>
                <p>Email: info@sman1wonosari.sch.id</p>
                <p>Telepon: (0274) 000000</p>
                <p>WhatsApp: 08xx-xxxx-xxxx</p>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10 px-6 py-5 text-center text-sm text-blue-100">
        © {{ date('Y') }} SMA Negeri 1 Wonosari. All rights reserved.
    </div>
</footer>
