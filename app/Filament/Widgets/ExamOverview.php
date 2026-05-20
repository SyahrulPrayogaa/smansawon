<?php

namespace App\Filament\Widgets;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExamOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Ringkasan Sistem Ujian';

    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Kelas', ClassRoom::query()->count())
                ->description('Total kelas terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Jumlah Siswa', Student::query()->count())
                ->description('Total siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Ujian Aktif', Exam::query()->where('is_active', true)->count())
                ->description('Ujian yang sedang aktif')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Peserta Submit', ExamAttempt::query()->where('status', 'submitted')->count())
                ->description('Peserta sudah mengumpulkan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
        ];
    }
}
