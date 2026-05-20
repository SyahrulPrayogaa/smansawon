<?php

namespace App\Filament\Resources\SchoolSubjects\Pages;

use App\Filament\Resources\SchoolSubjects\SchoolSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolSubjects extends ListRecords
{
    protected static string $resource = SchoolSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Mata Pelajaran'),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Daftar Mata Pelajaran';
    }
}
