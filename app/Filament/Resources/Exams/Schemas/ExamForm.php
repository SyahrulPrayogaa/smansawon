<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama Paket Soal / Ujian')
                    ->placeholder('Contoh: Ujian Matematika Kelas XI')
                    ->required()
                    ->maxLength(255),

                Select::make('school_subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('schoolSubject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),

                TextInput::make('duration_minutes')
                    ->label('Durasi Pengerjaan')
                    ->suffix('menit')
                    ->numeric()
                    ->required()
                    ->default(60),

                // DateTimePicker::make('starts_at')
                //     ->label('Mulai Aktif'),

                // DateTimePicker::make('ends_at')
                //     ->label('Berakhir'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(false),
            ]);
    }
}
