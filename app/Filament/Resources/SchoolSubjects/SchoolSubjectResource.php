<?php

namespace App\Filament\Resources\SchoolSubjects;

use App\Filament\Resources\SchoolSubjects\Pages\CreateSchoolSubject;
use App\Filament\Resources\SchoolSubjects\Pages\EditSchoolSubject;
use App\Filament\Resources\SchoolSubjects\Pages\ListSchoolSubjects;
use App\Filament\Resources\SchoolSubjects\Schemas\SchoolSubjectForm;
use App\Filament\Resources\SchoolSubjects\Tables\SchoolSubjectsTable;
use App\Models\SchoolSubject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SchoolSubjectResource extends Resource
{
    protected static ?string $model = SchoolSubject::class;

    protected static ?string $modelLabel = 'Mata Pelajaran';

    protected static ?string $pluralModelLabel = 'Daftar Mata Pelajaran';

    protected static ?string $navigationLabel = 'Daftar Mata Pelajaran';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SchoolSubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolSubjectsTable::configure($table);
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
            'index' => ListSchoolSubjects::route('/'),
            'create' => CreateSchoolSubject::route('/create'),
            'edit' => EditSchoolSubject::route('/{record}/edit'),
        ];
    }
}
