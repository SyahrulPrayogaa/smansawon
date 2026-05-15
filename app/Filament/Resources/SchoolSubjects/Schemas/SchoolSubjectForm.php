<?php

namespace App\Filament\Resources\SchoolSubjects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SchoolSubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->placeholder('Contoh: Matematika')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Kode Mapel')
                    ->placeholder('Contoh: MTK')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
