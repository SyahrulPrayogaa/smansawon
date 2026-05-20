<x-layouts.exam title="Masuk Ujian">
    <main class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
        <section class="w-full max-w-md rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 md:p-8">
            <div class="text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Logo sekolah" class="mx-auto h-16 w-16 object-contain">

                <h1 class="mt-5 text-2xl font-extrabold text-blue-950">
                    Masuk Ujian Siswa
                </h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Masukkan NISN untuk memverifikasi data siswa sebelum memulai ujian.
                </p>
            </div>

            <form method="POST" action="{{ route('student.exam.check-nisn') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="nisn" class="text-sm font-bold text-blue-950">
                        NISN
                    </label>
                    <input id="nisn" name="nisn" type="text" value="{{ old('nisn') }}" inputmode="numeric"
                        autocomplete="off" placeholder="Contoh: 1234567890"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-blue-900 focus:ring-4 focus:ring-blue-100">
                    @error('nisn')
                        <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    class="w-full rounded-2xl bg-blue-950 px-5 py-3.5 text-sm font-extrabold text-white cursor-pointer transition hover:bg-blue-900">
                    Cek Data Siswa
                </button>
            </form>
        </section>
    </main>
</x-layouts.exam>
