<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
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
                    ->label('Judul Ujian')
                    ->required()
                    ->maxLength(255),

                TextInput::make('subject')
                    ->label('Mata Pelajaran')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),

                TextInput::make('duration_minutes')
                    ->label('Durasi Menit')
                    ->numeric()
                    ->required()
                    ->default(60),

                DateTimePicker::make('starts_at')
                    ->label('Mulai'),

                DateTimePicker::make('ends_at')
                    ->label('Selesai'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(false),
            ]);
    }
}
