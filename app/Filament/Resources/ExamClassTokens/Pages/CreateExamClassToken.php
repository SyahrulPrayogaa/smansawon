<?php

namespace App\Filament\Resources\ExamClassTokens\Pages;

use App\Filament\Resources\ExamClassTokens\ExamClassTokenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExamClassToken extends CreateRecord
{
    protected static string $resource = ExamClassTokenResource::class;

    public function getTitle(): string
    {
        return 'Tambah Token Ujian';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah Token Ujian';
    }
}
