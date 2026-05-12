{{--
    File: resources/views/exams/show.blade.php
    Halaman ujian siswa - mobile friendly + timer

    Catatan tahap awal:
    - Data soal masih dummy dari controller.
    - Timer berjalan di sisi browser.
    - Saat waktu habis, form otomatis submit.
    - Nanti bisa dikembangkan dengan database, autosave, dan validasi backend.
--}}

<x-layouts.exam :title="$exam['title'] ?? 'Ujian Siswa'">
    <form id="examForm" method="POST" action="{{ route('student.exam.submit') }}" class="min-h-screen bg-slate-100"
        data-duration="{{ $exam['duration_minutes'] * 60 }}">
        @csrf

        {{-- Header ujian sticky --}}
        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-4 py-3 md:px-6">
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-blue-950 md:text-base">
                        {{ $exam['title'] }}
                    </p>
                    <p class="truncate text-xs text-slate-500">
                        {{ $exam['subject'] }} • {{ count($questions) }} Soal
                    </p>
                </div>

                <div class="shrink-0 rounded-2xl bg-blue-950 px-4 py-2 text-center text-white shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-blue-200">
                        Sisa Waktu
                    </p>
                    <p id="timerText" class="text-lg font-extrabold leading-tight tabular-nums md:text-xl">
                        --:--
                    </p>
                </div>
            </div>
        </header>

        {{-- Konten utama --}}
        <main class="mx-auto grid max-w-5xl gap-5 px-4 py-5 pb-28 md:grid-cols-[1fr_280px] md:px-6 md:py-8 md:pb-8">
            {{-- Kolom soal --}}
            <section class="space-y-5">
                {{-- Info ujian --}}
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-xl font-extrabold text-blue-950 md:text-2xl">
                                {{ $exam['title'] }}
                            </h1>
                            <p class="mt-1 text-sm text-slate-500">
                                Kerjakan soal dengan teliti. Pastikan semua jawaban sudah terisi sebelum mengumpulkan.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <p>
                                Nama: <span class="font-bold text-blue-950">{{ $student['name'] }}</span>
                            </p>
                            <p>
                                Kelas: <span class="font-bold text-blue-950">{{ $student['class'] }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Daftar soal --}}
                @foreach ($questions as $index => $question)
                    <article id="question-{{ $index + 1 }}"
                        class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:p-6">
                        <div class="mb-4 flex items-start gap-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-950 text-sm font-extrabold text-white">
                                {{ $index + 1 }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="leading-8 text-slate-800">
                                    {{ $question['question'] }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @foreach ($question['options'] as $optionKey => $optionText)
                                <label
                                    class="flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50">
                                    <input type="radio" name="answers[{{ $question['id'] }}]"
                                        value="{{ $optionKey }}"
                                        class="mt-1 h-4 w-4 shrink-0 border-slate-300 text-blue-900 focus:ring-blue-900">

                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-extrabold text-blue-950">
                                        {{ $optionKey }}
                                    </span>

                                    <span class="leading-7 text-slate-700">
                                        {{ $optionText }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </section>

            {{-- Sidebar navigasi soal desktop --}}
            <aside class="hidden md:block">
                <div class="sticky top-24 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <h2 class="font-extrabold text-blue-950">
                        Navigasi Soal
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Klik nomor untuk menuju soal.
                    </p>

                    <div class="mt-5 grid grid-cols-5 gap-2">
                        @foreach ($questions as $index => $question)
                            <a href="#question-{{ $index + 1 }}"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-sm font-bold text-blue-950 hover:border-blue-900 hover:bg-blue-900 hover:text-white">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>

                    <button type="button" onclick="confirmSubmitExam()"
                        class="mt-6 w-full rounded-2xl bg-yellow-400 px-5 py-3 text-sm font-extrabold text-blue-950 transition hover:bg-yellow-300">
                        Kumpulkan Jawaban
                    </button>
                </div>
            </aside>
        </main>

        {{-- Bottom action khusus mobile --}}
        <div
            class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white p-4 shadow-[0_-8px_20px_rgba(15,23,42,0.08)] md:hidden">
            <div class="mx-auto flex max-w-5xl items-center gap-3">
                <a href="#question-1"
                    class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-blue-950">
                    Soal 1
                </a>

                <button type="button" onclick="confirmSubmitExam()"
                    class="flex-[1.4] rounded-2xl bg-yellow-400 px-4 py-3 text-sm font-extrabold text-blue-950 shadow-sm">
                    Kumpulkan
                </button>
            </div>
        </div>
    </form>

    {{-- Modal peringatan waktu habis --}}
    <div id="timeUpModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/70 px-4">
        <div class="max-w-sm rounded-3xl bg-white p-6 text-center shadow-xl">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-2xl">
                ⏰
            </div>
            <h2 class="mt-4 text-xl font-extrabold text-blue-950">
                Waktu Habis
            </h2>
            <p class="mt-2 leading-7 text-slate-600">
                Waktu pengerjaan telah selesai. Jawaban akan dikumpulkan otomatis.
            </p>
        </div>
    </div>

    <script>
        const examForm = document.getElementById('examForm');
        const timerText = document.getElementById('timerText');
        const timeUpModal = document.getElementById('timeUpModal');

        let remainingSeconds = Number(examForm.dataset.duration || 0);
        let isSubmitting = false;

        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            if (hours > 0) {
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        function updateTimer() {
            timerText.textContent = formatTime(remainingSeconds);

            if (remainingSeconds <= 300) {
                timerText.parentElement.classList.remove('bg-blue-950');
                timerText.parentElement.classList.add('bg-red-600');
            }

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                submitExamAutomatically();
                return;
            }

            remainingSeconds--;
        }

        function confirmSubmitExam() {
            const confirmation = confirm('Apakah kamu yakin ingin mengumpulkan jawaban sekarang?');

            if (confirmation) {
                isSubmitting = true;
                examForm.submit();
            }
        }

        function submitExamAutomatically() {
            isSubmitting = true;
            timeUpModal.classList.remove('hidden');
            timeUpModal.classList.add('flex');

            setTimeout(() => {
                examForm.submit();
            }, 1500);
        }

        window.addEventListener('beforeunload', function(event) {
            if (!isSubmitting && remainingSeconds > 0) {
                event.preventDefault();
                event.returnValue = '';
            }
        });

        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);
    </script>
</x-layouts.exam>
