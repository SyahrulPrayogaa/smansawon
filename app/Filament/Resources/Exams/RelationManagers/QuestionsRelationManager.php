<?php

namespace App\Filament\Resources\Exams\RelationManagers;

use App\Filament\Resources\Exams\ExamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;


class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $relatedResource = ExamResource::class;

    protected static ?string $title = 'Daftar Soal';

    protected static ?string $modelLabel = 'Soal';

    protected static ?string $pluralModelLabel = 'Daftar Soal';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('question_text')
                    ->label('Teks Soal')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->label('Gambar Soal')
                    ->image()
                    ->disk('public')
                    ->directory('exam-questions')
                    ->visibility('public')
                    ->imagePreviewHeight('200')
                    ->maxSize(2048)
                    ->helperText('Opsional. Gunakan jika soal membutuhkan gambar, grafik, tabel, atau ilustrasi.')
                    ->columnSpanFull(),

                Select::make('question_type')
                    ->label('Jenis Soal')
                    ->options([
                        'multiple_choice' => 'Pilihan Ganda',
                        'essay' => 'Esai',
                    ])
                    ->default('multiple_choice')
                    ->required(),

                TextInput::make('order_number')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->required()
                    ->default(1),

                TextInput::make('score')
                    ->label('Skor')
                    ->numeric()
                    ->required()
                    ->default(1),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Repeater::make('options')
                    ->label('Pilihan Jawaban')
                    ->relationship()
                    ->schema([
                        TextInput::make('option_label')
                            ->label('Label')
                            ->required()
                            ->maxLength(5)
                            ->placeholder('A'),

                        RichEditor::make('option_text')
                            ->label('Isi Pilihan')
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_correct')
                            ->label('Jawaban Benar'),
                    ])
                    ->columns(2)
                    ->defaultItems(5)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                TextColumn::make('order_number')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('question_text')
                    ->label('Soal')
                    ->html()
                    ->limit(80)
                    ->searchable(),

                IconColumn::make('image_path')
                    ->label('Gambar')
                    ->boolean()
                    ->getStateUsing(fn($record) => filled($record->image_path)),

                TextColumn::make('options_count')
                    ->label('Opsi')
                    ->counts('options'),

                TextColumn::make('score')
                    ->label('Skor')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Soal'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ]);
    }
}
