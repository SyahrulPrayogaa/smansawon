<?php

namespace App\Filament\Resources\ExamAttempts\Pages;

use App\Filament\Resources\ExamAttempts\ExamAttemptResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExamAttempt extends ViewRecord
{
    protected static string $resource = ExamAttemptResource::class;

    protected string $view = 'admin.filament.resources.exam-results.pages.view-exam-results';

    public function getTitle(): string
    {
        return 'Detail Hasil Ujian';
    }

    public function getBreadcrumb(): string
    {
        return 'Detail Hasil Ujian';
    }

    public function getRecordTitle(): string
    {
        return $this->record->student?->name ?? 'Peserta Ujian';
    }
}
