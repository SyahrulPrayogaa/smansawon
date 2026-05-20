<?php

namespace App\Filament\Resources\ExamClassTokens\Pages;

use App\Filament\Resources\ExamClassTokens\ExamClassTokenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamClassToken extends EditRecord
{
    protected static string $resource = ExamClassTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
