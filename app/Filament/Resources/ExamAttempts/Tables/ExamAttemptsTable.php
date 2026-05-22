<?php

namespace App\Filament\Resources\ExamAttempts\Tables;

use App\Models\ExamAttempt;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;

class ExamAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No')
                    ->rowIndex()
                    ->width('60px'),

                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable(),

                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.classRoom.name')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('exam.title')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('submitted_at')
                    ->label('Submit')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('tab_leave_count')
                    ->label('Pelanggaran')
                    ->sortable(),

                IconColumn::make('is_locked')
                    ->label('Terkunci')
                    ->boolean(),

                TextColumn::make('score')
                    ->label('Nilai')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Lihat Jawaban'),
                Action::make('unlockExam')
                    ->label('Buka Kunci')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Buka Kunci Ujian')
                    ->modalDescription('Siswa akan bisa melanjutkan ujian setelah kunci dibuka.')
                    ->visible(fn(ExamAttempt $record): bool => $record->is_locked && $record->status === 'in_progress')
                    ->action(function (ExamAttempt $record): void {
                        $record->update([
                            'is_locked' => false,
                            'tab_leave_count' => 0,
                            'unlocked_at' => now(),
                            'lock_reason' => null,
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
