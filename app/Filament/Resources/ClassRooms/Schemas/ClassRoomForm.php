<?php

namespace App\Filament\Resources\ClassRooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClassRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: XI IPA 1')
                    ->required()
                    ->maxLength(255),

                TextInput::make('grade')
                    ->label('Tingkat')
                    ->placeholder('Contoh: XI')
                    ->maxLength(50),

                TextInput::make('major')
                    ->label('Jurusan')
                    ->placeholder('Contoh: IPA / IPS / Umum')
                    ->maxLength(100),

                TextInput::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->placeholder('Contoh: 2025/2026')
                    ->default(fn() => now()->year . '/' . (now()->year + 1))
                    ->maxLength(20),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
