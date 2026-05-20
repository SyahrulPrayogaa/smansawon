<?php

namespace App\Filament\Resources\ExamClassTokens\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ExamClassTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('exam_id')
                    ->label('Ujian')
                    ->relationship('exam', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('class_room_id')
                    ->label('Kelas')
                    ->relationship('classRoom', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('token')
                    ->label('Token')
                    ->required()
                    ->maxLength(50)
                    ->default(fn() => strtoupper(Str::random(8))),

                DateTimePicker::make('starts_at')
                    ->label('Token Berlaku Mulai'),

                DateTimePicker::make('ends_at')
                    ->label('Token Berlaku Sampai'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
