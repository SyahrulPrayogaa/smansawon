<?php

namespace App\Filament\Resources\ExamAttempts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExamAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('exam_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                TextInput::make('exam_class_token_id')
                    ->numeric(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('submitted_at'),
                Select::make('status')
                    ->options(['in_progress' => 'In progress', 'submitted' => 'Submitted', 'expired' => 'Expired'])
                    ->default('in_progress')
                    ->required(),
                TextInput::make('score')
                    ->numeric(),
            ]);
    }
}
