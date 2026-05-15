<?php

namespace App\Filament\Resources\ExamClassTokens;

use App\Filament\Resources\ExamClassTokens\Pages\CreateExamClassToken;
use App\Filament\Resources\ExamClassTokens\Pages\EditExamClassToken;
use App\Filament\Resources\ExamClassTokens\Pages\ListExamClassTokens;
use App\Filament\Resources\ExamClassTokens\Schemas\ExamClassTokenForm;
use App\Filament\Resources\ExamClassTokens\Tables\ExamClassTokensTable;
use App\Models\ExamClassToken;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExamClassTokenResource extends Resource
{
    protected static ?string $modelLabel = 'Token Ujian';

    protected static ?string $pluralModelLabel = 'Token Ujian';

    protected static ?string $navigationLabel = 'Token Ujian';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 5;

    protected static ?string $model = ExamClassToken::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';
    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'token';

    public static function form(Schema $schema): Schema
    {
        return ExamClassTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamClassTokensTable::configure($table);
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
            'index' => ListExamClassTokens::route('/'),
            'create' => CreateExamClassToken::route('/create'),
            'edit' => EditExamClassToken::route('/{record}/edit'),
        ];
    }
}
