<?php

namespace App\Filament\Resources\SchoolSubjects\Pages;

use App\Filament\Resources\SchoolSubjects\SchoolSubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolSubject extends CreateRecord
{
    protected static string $resource = SchoolSubjectResource::class;

    public function getTitle(): string
    {
        return 'Tambah Mata Pelajaran';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah Mata Pelajaran';
    }
}
