<?php

namespace App\Filament\Resources\ExamAttempts;

use App\Filament\Resources\ExamAttempts\Pages\CreateExamAttempt;
use App\Filament\Resources\ExamAttempts\Pages\EditExamAttempt;
use App\Filament\Resources\ExamAttempts\Pages\ListExamAttempts;
use App\Filament\Resources\ExamAttempts\Pages\ViewExamAttempt;
use App\Filament\Resources\ExamAttempts\Schemas\ExamAttemptForm;
use App\Filament\Resources\ExamAttempts\Schemas\ExamAttemptInfolist;
use App\Filament\Resources\ExamAttempts\Tables\ExamAttemptsTable;
use App\Models\ExamAttempt;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExamAttemptResource extends Resource
{
    protected static ?string $modelLabel = 'Hasil Ujian Siswa';

    protected static ?string $pluralModelLabel = 'Hasil Ujian Siswa';

    protected static ?string $navigationLabel = 'Hasil Ujian Siswa';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 6;

    protected static ?string $model = ExamAttempt::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ExamAttemptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamAttemptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamAttemptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamAttempts::route('/'),
            // 'create' => CreateExamAttempt::route('/create'),
            'view' => ViewExamAttempt::route('/{record}'),
            // 'edit' => EditExamAttempt::route('/{record}/edit'),
        ];
    }
}
