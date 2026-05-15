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
            <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:p-7">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-yellow-600">Soal {{ $number }}</p>
                        <h1 class="mt-1 text-xl font-extrabold text-blue-950">
                            {{ $exam->subject }}
                        </h1>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-center ring-1 ring-slate-200">
                        <p class="text-xs font-bold text-slate-500">Progress</p>
                        <p class="font-extrabold text-blue-950">{{ $number }}/{{ $totalQuestions }}</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-slate-50 p-5 leading-8 text-slate-800 ring-1 ring-slate-200">
                    {!! $question->question_text !!}
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($question->options as $option)
                        <label
                            class="flex cursor-pointer gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50">
                            <input type="radio" name="question_option_id" value="{{ $option->id }}"
                                @checked($selectedAnswer?->question_option_id === $option->id)
                                class="mt-1 h-4 w-4 shrink-0 border-slate-300 text-blue-900 focus:ring-blue-900">

                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-extrabold text-blue-950">
                                {{ $option->option_label }}
                            </span>

                            <span class="leading-7 text-slate-700">
                                {!! $option->option_text !!}
                            </span>
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
                    <button type="submit" name="action" value="finish"
                        onclick="return confirm('Yakin ingin mengumpulkan ujian sekarang?')"
                        class="flex-[1.4] rounded-2xl bg-yellow-400 px-4 py-3 text-sm font-extrabold text-blue-950 cursor-pointer transition hover:bg-yellow-300">
                        Kumpulkan
                    </button>
                @endif
            </div>
        </div>
    </form>

    <script>
        const questionForm = document.getElementById('questionForm');
        const timerText = document.getElementById('timerText');
        let remainingSeconds = Number(questionForm.dataset.remaining || 0);

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
    </script>
</x-layouts.exam>
