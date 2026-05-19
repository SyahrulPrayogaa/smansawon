<x-layouts.exam title="Hasil Ujian">
    <main class="min-h-screen bg-slate-100 px-4 py-8">
        <section class="mx-auto max-w-2xl rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 md:p-8">
            <div class="text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Logo sekolah" class="mx-auto h-16 w-16 object-contain">

                <h1 class="mt-5 text-2xl font-extrabold text-blue-950">
                    Ujian Selesai
                </h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Berikut adalah ringkasan hasil ujian kamu.
                </p>
            </div>

            @if (session('success'))
                <div
                    class="mt-6 rounded-2xl bg-green-50 p-4 text-sm font-semibold leading-6 text-green-700 ring-1 ring-green-100">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-8 rounded-3xl bg-blue-950 p-6 text-center text-white">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-200">
                    Nilai Akhir
                </p>

                <p class="mt-3 text-6xl font-black tabular-nums">
                    {{ number_format((float) ($attempt->score ?? 0), 2) }}
                </p>

                {{-- <p class="mt-2 text-sm font-semibold text-blue-100">
                    Skala 100
                </p> --}}
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-green-50 p-5 text-center ring-1 ring-green-100">
                    <p class="text-xs font-bold uppercase tracking-wide text-green-700">
                        Benar
                    </p>
                    <p class="mt-2 text-3xl font-black text-green-700">
                        {{ $correctCount }}
                    </p>
                </div>

                <div class="rounded-2xl bg-red-50 p-5 text-center ring-1 ring-red-100">
                    <p class="text-xs font-bold uppercase tracking-wide text-red-700">
                        Salah
                    </p>
                    <p class="mt-2 text-3xl font-black text-red-700">
                        {{ $wrongCount }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5 text-center ring-1 ring-slate-200">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        Total Soal
                    </p>
                    <p class="mt-2 text-3xl font-black text-blue-950">
                        {{ $totalQuestions }}
                    </p>
                </div>
            </div>

            @if ($unansweredCount > 0)
                <div
                    class="mt-5 rounded-2xl bg-yellow-50 p-4 text-sm font-semibold leading-6 text-yellow-800 ring-1 ring-yellow-100">
                    Ada {{ $unansweredCount }} soal yang tidak terjawab. Soal kosong dihitung sebagai salah.
                </div>
            @endif

            <div class="mt-6 rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                <h2 class="text-lg font-extrabold text-blue-950">
                    Rincian Peserta
                </h2>

                <div class="mt-5 space-y-4">
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
                            NISN
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $student->nisn }}
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
                </div>
            </div>

            <div class="mt-6 rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                <h2 class="text-lg font-extrabold text-blue-950">
                    Rincian Ujian
                </h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Nama Ujian
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $exam->title }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Mata Pelajaran
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $exam->schoolSubject?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Status
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $attempt->status === 'submitted' ? 'Dikumpulkan' : 'Waktu Habis' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Waktu Mulai
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $attempt->started_at?->translatedFormat('d F Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Waktu Selesai
                        </p>
                        <p class="mt-1 font-extrabold text-blue-950">
                            {{ $attempt->submitted_at?->translatedFormat('d F Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('student.exam.logout') }}" class="mt-6">
                @csrf

                <button type="submit"
                    class="w-full rounded-2xl bg-yellow-400 px-5 py-3.5 text-sm font-extrabold text-blue-950 transition hover:bg-yellow-300">
                    Keluar dari Halaman Ujian
                </button>
            </form>
        </section>
    </main>
</x-layouts.exam>
