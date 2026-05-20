<?php

namespace App\Filament\Resources\SchoolSubjects\Pages;

use App\Filament\Resources\SchoolSubjects\SchoolSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchoolSubject extends EditRecord
{
    protected static string $resource = SchoolSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
