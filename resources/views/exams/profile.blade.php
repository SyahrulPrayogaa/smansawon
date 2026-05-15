<x-layouts.exam title="Profil Siswa">
    <main class="min-h-screen bg-slate-100 px-4 py-8">
        <section class="mx-auto max-w-lg rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 md:p-8">
            <div class="text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Logo sekolah" class="mx-auto h-16 w-16 object-contain">
                <h1 class="mt-5 text-2xl font-extrabold text-blue-950">
                    Verifikasi Profil Siswa
                </h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Pastikan data kamu benar, lalu masukkan token ujian dari pengawas.
                </p>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-2xl bg-green-50 p-4 text-sm font-semibold leading-6 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-8 rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama</p>
                        <p class="mt-1 font-extrabold text-blue-950">{{ $student['name'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">NISN</p>
                        <p class="mt-1 font-extrabold text-blue-950">{{ $student['nisn'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kelas</p>
                        <p class="mt-1 font-extrabold text-blue-950">{{ $student['class'] }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-3xl bg-blue-50 p-5 ring-1 ring-blue-100">
                <p class="text-sm font-bold text-blue-950">{{ $exam['title'] }}</p>
                <p class="mt-1 text-sm text-slate-600">Mata pelajaran: {{ $exam['subject'] }}</p>
                <p class="mt-1 text-sm text-slate-600">Durasi: {{ $exam['duration_minutes'] }} menit</p>
            </div>

            <form method="POST" action="{{ route('student.exam.check-token') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="token" class="text-sm font-bold text-blue-950">
                        Token Ujian
                    </label>
                    <input id="token" name="token" type="text" value="{{ old('token') }}" autocomplete="off"
                        placeholder="Masukkan token dari pengawas"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-center text-base font-extrabold uppercase tracking-widest outline-none focus:border-blue-900 focus:ring-4 focus:ring-blue-100">
                    @error('token')
                        <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    class="w-full rounded-2xl bg-yellow-400 px-5 py-3.5 text-sm font-extrabold text-blue-950 transition hover:bg-yellow-300">
                    Mulai Ujian
                </button>
            </form>
        </section>
    </main>
</x-layouts.exam>
