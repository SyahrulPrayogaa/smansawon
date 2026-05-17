<x-layouts.exam title="Ujian Terkunci">
    <main class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
        <section class="w-full max-w-md rounded-3xl bg-white p-6 text-center shadow-sm ring-1 ring-slate-200 md:p-8">
            <img src="{{ asset('img/logo.png') }}" alt="Logo sekolah" class="mx-auto h-16 w-16 object-contain">

            <div class="mx-auto mt-6 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-3xl">
                ⚠️
            </div>

            <h1 class="mt-5 text-2xl font-extrabold text-blue-950">
                Ujian Dikunci Sementara
            </h1>

            <p class="mt-3 text-sm leading-7 text-slate-600">
                Sistem mendeteksi kamu meninggalkan halaman ujian beberapa kali.
                Untuk melanjutkan ujian, silakan hubungi pengawas.
            </p>

            <div class="mt-6 rounded-2xl bg-slate-50 p-5 text-left ring-1 ring-slate-200">
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Nama
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $student->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Kelas
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $student->classRoom?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Ujian
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $exam->title }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Jumlah Pelanggaran
                        </p>
                        <p class="mt-1 font-extrabold text-red-600">
                            {{ $attempt->tab_leave_count }}
                        </p>
                    </div>
                </div>
            </div>

            <a href="{{ route('student.exam.question', 1) }}"
                class="mt-6 inline-flex w-full justify-center rounded-2xl bg-blue-950 px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-blue-900">
                Lanjutkan Ujian
            </a>

            <p class="mt-4 text-xs leading-5 text-slate-500">
                Jika pengawas sudah membuka kunci, tekan tombol di atas untuk melanjutkan.
            </p>
        </section>
    </main>
</x-layouts.exam>
