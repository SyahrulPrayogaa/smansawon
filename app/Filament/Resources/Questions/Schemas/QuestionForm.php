<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuestionForm
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

                RichEditor::make('question_text')
                    ->label('Teks Soal')
                    ->required()
                    ->columnSpanFull(),

                Select::make('question_type')
                    ->label('Jenis Soal')
                    ->options([
                        'multiple_choice' => 'Pilihan Ganda',
                        'essay' => 'Esai',
                    ])
                    ->default('multiple_choice')
                    ->required(),

                TextInput::make('order_number')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->required()
                    ->default(1),

                TextInput::make('score')
                    ->label('Skor')
                    ->numeric()
                    ->required()
                    ->default(1),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Repeater::make('options')
                    ->label('Pilihan Jawaban')
                    ->relationship()
                    ->schema([
                        TextInput::make('option_label')
                            ->label('Label')
                            ->required()
                            ->maxLength(5)
                            ->placeholder('A'),

                        TextInput::make('option_text')
                            ->label('Isi Pilihan')
                            ->required(),

                        Toggle::make('is_correct')
                            ->label('Jawaban Benar'),
                    ])
                    ->columns(3)
                    ->defaultItems(5)
                    ->columnSpanFull(),
            ]);
    }
}
