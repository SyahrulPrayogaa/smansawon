<?php

namespace App\Filament\Imports;

use App\Models\Question;
use App\Models\QuestionOption;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class QuestionImporter extends Importer
{
    protected static ?string $model = Question::class;

    public static function getColumns(): array
    {
        return [
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

            ImportColumn::make('image_path')
                ->label('Path Gambar Soal')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('exam-questions/grafik-no-1.png')
                ->helperText('Opsional. Isi dengan path file di storage, misalnya exam-questions/gambar.png.'),

            ImportColumn::make('score')
                ->label('Skor')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('10'),

            ImportColumn::make('option_a')
                ->label('Pilihan A')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (Question $record, ?string $state): void {
                    // Kolom ini tidak disimpan langsung ke tabel questions.
                })
                ->example('86'),

            ImportColumn::make('option_b')
                ->label('Pilihan B')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (Question $record, ?string $state): void {
                    // Kolom ini tidak disimpan langsung ke tabel questions.
                })
                ->example('94'),

            ImportColumn::make('option_c')
                ->label('Pilihan C')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (Question $record, ?string $state): void {
                    // Kolom ini tidak disimpan langsung ke tabel questions.
                })
                ->example('96'),

            ImportColumn::make('option_d')
                ->label('Pilihan D')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (Question $record, ?string $state): void {
                    // Kolom ini tidak disimpan langsung ke tabel questions.
                })
                ->example('108'),

            ImportColumn::make('option_e')
                ->label('Pilihan E')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (Question $record, ?string $state): void {
                    // Kolom ini tidak disimpan langsung ke tabel questions.
                })
                ->example('112'),

            ImportColumn::make('correct_answer')
                ->label('Jawaban Benar')
                ->requiredMapping()
                ->rules(['required', 'string', 'in:A,B,C,D,E,a,b,c,d,e'])
                ->castStateUsing(fn(?string $state): ?string => $state ? strtoupper(trim($state)) : null)
                ->fillRecordUsing(function (Question $record, ?string $state): void {
                    // Kolom ini tidak disimpan langsung ke tabel questions.
                })
                ->example('C'),
        ];
    }

    public function resolveRecord(): ?Question
    {
        $examId = $this->options['exam_id'] ?? null;

        if (! $examId) {
            return null;
        }

        return Question::firstOrNew([
            'exam_id' => $examId,
            'order_number' => $this->data['order_number'],
        ]);
    }

    protected function beforeSave(): void
    {
        $this->record->exam_id = $this->options['exam_id'];
        $this->record->question_type = 'multiple_choice';
        $this->record->is_active = true;

        if (blank($this->record->score)) {
            $this->record->score = 1;
        }
    }

    protected function afterSave(): void
    {
        $correctAnswer = strtoupper($this->data['correct_answer']);

        $options = [
            'A' => $this->data['option_a'],
            'B' => $this->data['option_b'],
            'C' => $this->data['option_c'],
            'D' => $this->data['option_d'],
            'E' => $this->data['option_e'],
        ];

        foreach ($options as $label => $text) {
            QuestionOption::updateOrCreate(
                [
                    'question_id' => $this->record->id,
                    'option_label' => $label,
                ],
                [
                    'option_text' => $text,
                    'is_correct' => $label === $correctAnswer,
                ]
            );
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
