<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nisn')
                ->label('NISN')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:20'])
                ->example('1234567890'),

            ImportColumn::make('name')
                ->label('Nama Siswa')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Budi Santoso'),

            ImportColumn::make('classRoom')
                ->label('Kelas')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required'])
                ->example('XI IPA 1'),

            ImportColumn::make('gender')
                ->label('Jenis Kelamin')
                ->rules(['nullable', 'in:male,female,L,P,l,p,Laki-laki,Perempuan,laki-laki,perempuan'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }

                    $state = trim($state);

                    return match (strtolower($state)) {
                        'l', 'male', 'laki-laki', 'laki laki' => 'male',
                        'p', 'female', 'perempuan' => 'female',
                        default => $state,
                    };
                })
                ->example('L'),

            ImportColumn::make('is_active')
                ->label('Aktif')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->example('1'),
        ];
    }

    public function resolveRecord(): Student
    {
        return Student::firstOrNew([
            'nisn' => $this->data['nisn'],
        ]);
    }

    protected function beforeSave(): void
    {
        $this->record->is_active = $this->record->is_active ?? true;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data siswa selesai. '
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
