<style>
    .rich-text-content p {
        margin: 0;
    }

    .rich-text-content ul,
    .rich-text-content ol {
        margin-left: 1.25rem;
    }
</style>

<x-layouts.exam :title="$exam['title']">
    <form id="questionForm" method="POST" action="{{ route('student.exam.save-answer', $number) }}"
        class="min-h-screen bg-slate-100" data-remaining="{{ $remainingSeconds }}">
        @csrf

        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-extrabold text-blue-950">
                        {{ $exam->title }}
                    </p>
                    <p class="truncate text-xs text-slate-500">
                        {{ $student->name }} • Soal {{ $number }} dari {{ $totalQuestions }}
                    </p>
                </div>

                <div class="shrink-0 rounded-2xl bg-blue-950 px-4 py-2 text-center text-white">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-blue-200">Sisa Waktu</p>
                    <p id="timerText" class="text-lg font-extrabold tabular-nums">--:--</p>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-4 py-5 pb-28">
            @if (session('success'))
                <div
                    class="mb-5 rounded-2xl bg-green-50 p-4 text-sm font-semibold text-green-700 ring-1 ring-green-100">
                    {{ session('success') }}
                </div>
            @endif

            @error('submit')
                <div class="mb-5 rounded-2xl bg-red-50 p-4 text-sm font-semibold text-red-700 ring-1 ring-red-100">
                    {{ $message }}
                </div>
            @enderror

            @if ($attempt->tab_leave_count > 0)
                <div
                    class="mb-5 rounded-2xl bg-yellow-50 p-4 text-sm font-semibold leading-6 text-yellow-800 ring-1 ring-yellow-100">
                    Peringatan aktivitas: kamu sudah terdeteksi meninggalkan halaman ujian sebanyak
                    {{ $attempt->tab_leave_count }} kali.
                </div>
            @endif

            <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:p-7">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-yellow-600">Soal {{ $number }}</p>
                        <h1 class="mt-1 text-xl font-extrabold text-blue-950">
                            {{ $exam->schoolSubject?->name ?? '-' }}
                        </h1>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-center ring-1 ring-slate-200">
                        <p class="text-xs font-bold text-slate-500">Progress</p>
                        <p class="font-extrabold text-blue-950">{{ $number }}/{{ $totalQuestions }}</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-slate-50 p-5 leading-8 text-slate-800 ring-1 ring-slate-200">
                    @if ($question->image_path)
                        <div class="mt-5 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200">
                            <img src="{{ asset('storage/' . $question->image_path) }}"
                                alt="Gambar soal nomor {{ $number }}" class="w-full object-contain">
                        </div>
                    @endif

                    {{-- @php
                        $questionText = trim(strip_tags($question->question_text ?? ''));
                    @endphp --}}
                    <div class="rounded-3xl bg-slate-50 p-5 leading-8 text-slate-800 ring-1 ring-slate-200">
                        @if (filled($question->question_text))
                            <div class="question-math-text rich-text-content">
                                {!! $question->question_text !!}
                            </div>
                        @endif

                        @if ($question->image_path)
                            <div class="mt-5 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200">
                                <img src="{{ asset('storage/' . $question->image_path) }}"
                                    alt="Gambar soal nomor {{ $number }}" class="w-full object-contain">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($question->options as $option)
                        <label
                            class="flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50">
                            <input type="{{ $question->question_type === 'multiple_select' ? 'checkbox' : 'radio' }}"
                                name="question_option_ids[]" value="{{ $option->id }}" @checked(in_array($option->id, $selectedOptionIds ?? []))
                                class="mt-1 h-4 w-4 shrink-0 border-slate-300 text-blue-900 focus:ring-blue-900">

                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-extrabold text-blue-950">
                                {{ $option->option_label }}
                            </span>

                            @php
                                $optionText = trim(strip_tags($option->option_text ?? ''));
                            @endphp

                            <div class="flex-1 leading-7 text-slate-700">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if (filled($optionText))
                                        <span class="inline">
                                            {!! e($optionText) !!}
                                        </span>
                                    @endif
                                </div>

                                @if ($option->image_path)
                                    <div class="mt-3 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200">
                                        <img src="{{ asset('storage/' . $option->image_path) }}"
                                            alt="Gambar pilihan {{ $option->option_label }}"
                                            class="w-full max-h-64 object-contain">
                                    </div>
                                @endif
                            </div>
                        </label>
                    @endforeach

                    @error('question_option_id')
                        <p class="text-sm font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </section>

            <section class="mt-5 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-blue-950">Navigasi Soal</p>
                <div class="mt-4 grid grid-cols-8 gap-2 sm:grid-cols-10">
                    @for ($i = 1; $i <= $totalQuestions; $i++)
                        <a href="{{ route('student.exam.question', $i) }}" @class([
                            'flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold ring-1 ring-slate-200',
                            'bg-blue-950 text-white' => $i === $number,
                            'bg-green-100 text-green-700' =>
                                in_array($i, $answeredNumbers) && $i !== $number,
                            'bg-white text-blue-950' =>
                                !in_array($i, $answeredNumbers) && $i !== $number,
                        ])>
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            </section>
        </main>

        <div
            class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white p-4 shadow-[0_-8px_20px_rgba(15,23,42,0.08)]">
            <div class="mx-auto flex max-w-3xl gap-3">
                @if ($number > 1)
                    <button type="submit" name="action" value="previous"
                        class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm font-extrabold text-blue-950 cursor-pointer transition hover:bg-slate-100">
                        Sebelumnya
                    </button>
                @endif

                @if ($number < $totalQuestions)
                    <button type="submit" name="action" value="next"
                        class="flex-[1.4] rounded-2xl bg-blue-950 px-4 py-3 text-sm font-extrabold text-white cursor-pointer transition hover:bg-blue-900">
                        Simpan & Lanjut
                    </button>
                @else
                    <button type="submit" name="action" value="save"
                        class="flex-[1.2] rounded-2xl bg-blue-950 px-4 py-3 text-sm font-extrabold text-white cursor-pointer transition hover:bg-blue-900">
                        Simpan Jawaban
                    </button>

                    @if ($allQuestionsAnswered)
                        <button type="submit" name="action" value="finish"
                            onclick="return confirm('Yakin ingin mengumpulkan ujian sekarang? Setelah dikumpulkan, jawaban tidak bisa diubah lagi.')"
                            class="flex-[1.2] rounded-2xl bg-yellow-400 px-4 py-3 text-sm font-extrabold text-blue-950 cursor-pointer transition hover:bg-yellow-500">
                            Kumpulkan
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </form>

    <style>
        .question-math-text mjx-container {
            display: inline-block !important;
            margin: 0 !important;
            vertical-align: middle;
        }
    </style>

    <script>
        const questionForm = document.getElementById('questionForm');
        const timerText = document.getElementById('timerText');
        let remainingSeconds = Number(questionForm.dataset.remaining || 0);
        let violationAlreadyRecording = false;
        let lastViolationRecordedAt = 0;
        let isSafeNavigation = false;

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
                const finishInput = document.createElement('input');
                finishInput.type = 'hidden';
                finishInput.name = 'action';
                finishInput.value = 'finish';
                questionForm.appendChild(finishInput);
                questionForm.submit();
                return;
            }

            remainingSeconds--;
        }

        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        // menangani pelanggaran saat siswa meninggalkan tab atau aplikasi ujian
        if (questionForm) {
            questionForm.addEventListener('submit', function() {
                isSafeNavigation = true;
            });
        }

        document.querySelectorAll('a[href]').forEach(function(link) {
            link.addEventListener('click', function() {
                const href = link.getAttribute('href');

                if (!href) {
                    return;
                }

                if (href.startsWith('#')) {
                    return;
                }

                isSafeNavigation = true;
            });
        });

        async function recordExamViolation(type, description) {
            const now = Date.now();

            if (isSafeNavigation) {
                return;
            }

            if (violationAlreadyRecording || now - lastViolationRecordedAt < 5000) {
                return;
            }

            violationAlreadyRecording = true;
            lastViolationRecordedAt = now;

            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value;

                const response = await fetch("{{ route('student.exam.record-violation') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        violation_type: type,
                        description: description,
                        url: window.location.href,
                    }),
                });

                const result = await response.json();

                if (result.is_locked) {
                    alert(
                        "Ujian dikunci sementara karena kamu terdeteksi meninggalkan halaman ujian beberapa kali. Silakan hubungi pengawas."
                    );
                    window.location.href = "{{ route('student.exam.locked') }}";
                    return;
                }

                if (result.tab_leave_count) {
                    alert(
                        "Peringatan: kamu terdeteksi meninggalkan halaman ujian.\n\n" +
                        "Pelanggaran: " + result.tab_leave_count + " dari " + result.max_violations + ".\n" +
                        "Jika melewati batas, ujian akan dikunci sementara."
                    );
                }
            } catch (error) {
                console.error("Gagal mencatat pelanggaran ujian:", error);
            } finally {
                violationAlreadyRecording = false;
            }
        }

        document.addEventListener("visibilitychange", function() {
            if (document.hidden && !isSafeNavigation) {
                recordExamViolation(
                    "tab_hidden",
                    "Siswa meninggalkan tab atau aplikasi ujian."
                );
            }
        });
    </script>

</x-layouts.exam>
