<?php

namespace App\Filament\Imports;

use App\Models\Question;
use App\Models\QuestionOption;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Number;

class QuestionImporter extends Importer
{
    protected static ?string $model = Question::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('question_type')
                ->label('Jenis Soal')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:50'])
                ->castStateUsing(fn(?string $state): string => strtolower(trim($state ?: 'multiple_choice')))
                ->example('multiple_choice | multiple_select | true_false -> Pilih Salah Satu'),

            ImportColumn::make('order_number')
                ->label('Nomor Soal')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1'])
                ->example('1'),

            ImportColumn::make('question_text')
                ->label('Teks Soal')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->example('Hasil dari 12 x 8 adalah ...'),

            ImportColumn::make('score')
                ->label('Skor')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('10'),

            ImportColumn::make('option_a')
                ->label('Pilihan A')
                ->requiredMapping()
                ->rules(['nullable', 'string'])
                ->fillRecordUsing(function (Question $record, mixed $state): void {
                    // Kolom ini disimpan manual ke question_options.
                })
                ->example('86'),

            ImportColumn::make('option_b')
                ->label('Pilihan B')
                ->requiredMapping()
                ->rules(['nullable', 'string'])
                ->fillRecordUsing(function (Question $record, mixed $state): void {
                    // Kolom ini disimpan manual ke question_options.
                })
                ->example('94'),

            ImportColumn::make('option_c')
                ->label('Pilihan C')
                ->rules(['nullable', 'string'])
                ->fillRecordUsing(function (Question $record, mixed $state): void {
                    // Kolom ini disimpan manual ke question_options.
                })
                ->example('96'),

            ImportColumn::make('option_d')
                ->label('Pilihan D')
                ->rules(['nullable', 'string'])
                ->fillRecordUsing(function (Question $record, mixed $state): void {
                    // Kolom ini disimpan manual ke question_options.
                })
                ->example('108'),

            ImportColumn::make('option_e')
                ->label('Pilihan E')
                ->rules(['nullable', 'string'])
                ->fillRecordUsing(function (Question $record, mixed $state): void {
                    // Kolom ini disimpan manual ke question_options.
                })
                ->example('112'),

            ImportColumn::make('correct_answer')
                ->label('Jawaban Benar')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:50'])
                ->castStateUsing(fn(?string $state): string => strtoupper(trim($state ?: '')))
                ->fillRecordUsing(function (Question $record, mixed $state): void {
                    // Kolom ini dipakai untuk menentukan is_correct pada question_options.
                })
                ->example('C'),

            ImportColumn::make('image_path')
                ->label('Path Gambar Soal')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('exam-questions/grafik-no-1.png'),
        ];
    }

    public function resolveRecord(): ?Question
    {
        $examId = $this->options['exam_id'] ?? null;

        if (! $examId) {
            throw ValidationException::withMessages([
                'exam_id' => 'Paket soal tidak ditemukan. Import soal harus dilakukan dari halaman Bank Soal tertentu.',
            ]);
        }

        return Question::firstOrNew([
            'exam_id' => $examId,
            'order_number' => (int) $this->data['order_number'],
        ]);
    }

    protected function beforeSave(): void
    {
        $questionType = $this->getQuestionType();

        $this->validateQuestionData();

        $this->record->exam_id = (int) $this->options['exam_id'];
        $this->record->question_type = $questionType;
        $this->record->question_text = trim((string) $this->data['question_text']);
        $this->record->image_path = blank($this->data['image_path'] ?? null)
            ? null
            : trim((string) $this->data['image_path']);

        $this->record->score = filled($this->data['score'] ?? null)
            ? $this->data['score']
            : 1;

        $this->record->is_active = true;
    }

    protected function afterSave(): void
    {
        $optionTexts = $this->getOptionTexts();
        $correctLabels = $this->getCorrectLabels();

        // Hapus opsi lama yang tidak ada lagi pada CSV.
        QuestionOption::query()
            ->where('question_id', $this->record->id)
            ->whereNotIn('option_label', array_keys($optionTexts))
            ->delete();

        foreach ($optionTexts as $label => $text) {
            QuestionOption::updateOrCreate(
                [
                    'question_id' => $this->record->id,
                    'option_label' => $label,
                ],
                [
                    'option_text' => $text,
                    'is_correct' => in_array($label, $correctLabels, true),
                ]
            );
        }
    }

    private function getQuestionType(): string
    {
        $type = strtolower(trim((string) ($this->data['question_type'] ?? 'multiple_choice')));

        return match ($type) {
            'multiple_choice', 'pilihan_ganda', 'pg' => 'multiple_choice',
            'multiple_select', 'pilihan_ganda_kompleks', 'pg_kompleks', 'kompleks' => 'multiple_select',
            'true_false', 'benar_salah', 'bs' => 'true_false',
            default => $type,
        };
    }

    private function getOptionTexts(): array
    {
        $type = $this->getQuestionType();

        if ($type === 'true_false') {
            $optionA = trim((string) ($this->data['option_a'] ?? ''));
            $optionB = trim((string) ($this->data['option_b'] ?? ''));

            return [
                'A' => $optionA !== '' ? $optionA : 'Benar',
                'B' => $optionB !== '' ? $optionB : 'Salah',
            ];
        }

        $fields = [
            'A' => 'option_a',
            'B' => 'option_b',
            'C' => 'option_c',
            'D' => 'option_d',
            'E' => 'option_e',
        ];

        $options = [];

        foreach ($fields as $label => $field) {
            $value = trim((string) ($this->data[$field] ?? ''));

            if ($value !== '') {
                $options[$label] = $value;
            }
        }

        return $options;
    }

    private function getCorrectLabels(): array
    {
        $type = $this->getQuestionType();
        $raw = strtoupper(trim((string) ($this->data['correct_answer'] ?? '')));

        if ($type === 'true_false') {
            return match ($raw) {
                'A', 'BENAR', 'TRUE', 'YA', 'Y' => ['A'],
                'B', 'SALAH', 'FALSE', 'TIDAK', 'N' => ['B'],
                default => [$raw],
            };
        }

        $labels = preg_split('/[\|,;\/\s]+/', $raw);

        return collect($labels)
            ->map(fn($label) => strtoupper(trim((string) $label)))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    private function validateQuestionData(): void
    {
        $type = $this->getQuestionType();

        if (! in_array($type, ['multiple_choice', 'multiple_select', 'true_false'], true)) {
            throw ValidationException::withMessages([
                'question_type' => 'Jenis soal tidak valid. Gunakan multiple_choice, multiple_select, atau true_false.',
            ]);
        }

        $optionTexts = $this->getOptionTexts();
        $correctLabels = $this->getCorrectLabels();

        if (count($optionTexts) < 2) {
            throw ValidationException::withMessages([
                'option_a' => 'Minimal harus ada 2 pilihan jawaban.',
            ]);
        }

        if (empty($correctLabels)) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Jawaban benar wajib diisi.',
            ]);
        }

        $availableLabels = array_keys($optionTexts);
        $invalidCorrectLabels = array_values(array_diff($correctLabels, $availableLabels));

        if (! empty($invalidCorrectLabels)) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Jawaban benar tidak sesuai dengan opsi yang tersedia: ' . implode(', ', $invalidCorrectLabels),
            ]);
        }

        if (in_array($type, ['multiple_choice', 'true_false'], true) && count($correctLabels) !== 1) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Soal pilihan ganda biasa dan benar/salah hanya boleh memiliki 1 jawaban benar.',
            ]);
        }

        if ($type === 'multiple_select' && count($correctLabels) < 1) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Soal pilihan ganda kompleks harus memiliki minimal 1 jawaban benar.',
            ]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import soal selesai. '
            . number_format($import->successful_rows)
            . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '
                . number_format($failedRowsCount)
                . ' baris gagal diimport. Silakan unduh file baris gagal untuk diperbaiki.';
        }

        return $body;
    }
}
