<x-filament-panels::page>
    @php
        $attempt = $this->record->loadMissing([
            'student.classRoom',
            'exam.schoolSubject',
            'exam.questions.options',
            'answers.selectedOption',
            'violations',
        ]);

        $questions = $attempt->exam->questions;
        $answers = $attempt->answers->keyBy('question_id');

        $totalQuestions = $questions->count();
        $answeredCount = $attempt->answers->count();
        $correctCount = $attempt->answers->where('is_correct', true)->count();
        $wrongCount = $attempt->answers->where('is_correct', false)->count();
    @endphp

    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Ringkasan Hasil Ujian
            </x-slot>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
                <div>
                    <div style="font-size: 13px; color: #6b7280;">Nilai Akhir</div>
                    <div style="font-size: 28px; font-weight: 800;">
                        {{ $attempt->score ?? 0 }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Total Soal</div>
                    <div style="font-size: 28px; font-weight: 800;">
                        {{ $totalQuestions }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Terjawab</div>
                    <div style="font-size: 28px; font-weight: 800;">
                        {{ $answeredCount }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Benar / Salah</div>
                    <div style="font-size: 28px; font-weight: 800;">
                        {{ $correctCount }} / {{ $wrongCount }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section style="margin-top: 1rem">
            <x-slot name="heading">
                Data Peserta
            </x-slot>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div>
                    <div style="font-size: 13px; color: #6b7280;">Nama Siswa</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->student->name }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">NISN</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->student->nisn }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Kelas</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->student->classRoom?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Status</div>
                    <div style="font-weight: 700;">
                        @if ($attempt->status === 'submitted')
                            <x-filament::badge color="success">
                                Sudah Dikumpulkan
                            </x-filament::badge>
                        @elseif ($attempt->status === 'expired')
                            <x-filament::badge color="danger">
                                Waktu Habis
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="warning">
                                Sedang Dikerjakan
                            </x-filament::badge>
                        @endif
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Ujian</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->exam->title }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Mata Pelajaran</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->exam->schoolSubject?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Mulai</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->started_at?->translatedFormat('d F Y H:i') ?? '-' }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 13px; color: #6b7280;">Submit</div>
                    <div style="font-weight: 700;">
                        {{ $attempt->submitted_at?->translatedFormat('d F Y H:i') ?? '-' }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section style="margin-top: 1rem">
            <x-slot name="heading">
                Riwayat Aktivitas Mencurigakan
            </x-slot>

            <x-slot name="description">
                Catatan ketika siswa terdeteksi meninggalkan halaman ujian.
            </x-slot>

            @if ($attempt->violations->isEmpty())
                <p style="font-size: 14px; color: #6b7280;">
                    Tidak ada pelanggaran tercatat.
                </p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 12px; text-align: left;">Waktu</th>
                                <th style="padding: 12px; text-align: left;">Jenis</th>
                                <th style="padding: 12px; text-align: left;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attempt->violations->sortByDesc('occurred_at') as $violation)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 12px; white-space: nowrap;">
                                        {{ $violation->occurred_at?->translatedFormat('d F Y H:i:s') ?? '-' }}
                                    </td>
                                    <td style="padding: 12px;">
                                        {{ $violation->violation_type }}
                                    </td>
                                    <td style="padding: 12px;">
                                        {{ $violation->description ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>

        <x-filament::section style="margin-top: 1rem">
            <x-slot name="heading">
                Tabel Jawaban Siswa
            </x-slot>

            <x-slot name="description">
                Tabel ini menampilkan jawaban siswa, jawaban benar, status benar/salah, dan skor setiap soal.
            </x-slot>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left; font-weight: 700; white-space: nowrap;">
                                No
                            </th>
                            <th style="padding: 12px; text-align: left; font-weight: 700; min-width: 220px;">
                                Jawaban Siswa
                            </th>
                            <th style="padding: 12px; text-align: left; font-weight: 700; min-width: 220px;">
                                Jawaban Benar
                            </th>
                            <th style="padding: 12px; text-align: left; font-weight: 700; white-space: nowrap;">
                                Status
                            </th>
                            <th style="padding: 12px; text-align: right; font-weight: 700; white-space: nowrap;">
                                Skor
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($questions as $index => $question)
                            @php
                                $answer = $answers->get($question->id);
                                $selectedOption = $answer?->selectedOption;
                                $correctOption = $question->options->firstWhere('is_correct', true);
                            @endphp

                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 12px; vertical-align: top; font-weight: 700;">
                                    {{ $index + 1 }}
                                </td>

                                <td style="padding: 12px; vertical-align: top;">
                                    @if ($selectedOption)
                                        <strong>{{ $selectedOption->option_label }}.</strong>
                                        {{ strip_tags($selectedOption->option_text) }}
                                    @else
                                        <span style="color: #9ca3af;">Tidak dijawab</span>
                                    @endif
                                </td>

                                <td style="padding: 12px; vertical-align: top;">
                                    @if ($correctOption)
                                        <strong>{{ $correctOption->option_label }}.</strong>
                                        {{ strip_tags($correctOption->option_text) }}
                                    @else
                                        <span style="color: #9ca3af;">Belum ditentukan</span>
                                    @endif
                                </td>

                                <td style="padding: 12px; vertical-align: top; white-space: nowrap;">
                                    @if (!$answer)
                                        <x-filament::badge color="gray">
                                            Kosong
                                        </x-filament::badge>
                                    @elseif ($answer->is_correct)
                                        <x-filament::badge color="success">
                                            Benar
                                        </x-filament::badge>
                                    @else
                                        <x-filament::badge color="danger">
                                            Salah
                                        </x-filament::badge>
                                    @endif
                                </td>

                                <td style="padding: 12px; text-align: right; vertical-align: top; font-weight: 700;">
                                    {{ $answer?->score ?? 0 }}
                                    <span style="color: #9ca3af; font-weight: 400;">
                                        / {{ $question->score }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
