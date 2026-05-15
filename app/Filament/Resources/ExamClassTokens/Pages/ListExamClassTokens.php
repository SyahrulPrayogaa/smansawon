<?php

namespace App\Filament\Resources\ExamClassTokens\Pages;

use App\Filament\Resources\ExamClassTokens\ExamClassTokenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamClassTokens extends ListRecords
{
    protected static string $resource = ExamClassTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Token Ujian'),
        ];
    }
}
