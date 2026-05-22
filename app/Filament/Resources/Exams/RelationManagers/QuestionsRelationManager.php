<?php

namespace App\Filament\Resources\Exams\RelationManagers;

use App\Filament\Resources\Exams\ExamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

use App\Filament\Imports\QuestionImporter;
use Filament\Actions\ImportAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;

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
                Textarea::make('question_text')
                    ->label('Teks Soal')
                    ->required()
                    ->rows(5)
                    ->helperText('Untuk notasi matematika, gunakan LaTeX. Contoh: \\(x^2 + 2x + 1\\)')
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->label('Gambar Soal')
                    ->image()
                    ->disk('public')
                    ->directory('exam-questions')
                    ->visibility('public')
                    ->imagePreviewHeight('200')
                    ->maxSize(2048)
                    ->downloadable()
                    ->openable()
                    ->previewable(true)
                    ->helperText('Opsional. Gunakan jika soal membutuhkan gambar, grafik, tabel, atau ilustrasi.')
                    ->columnSpanFull(),

                Select::make('question_type')
                    ->label('Jenis Soal')
                    ->options([
                        'multiple_choice' => 'Pilihan Ganda',
                        'multiple_select' => 'Pilihan Ganda Kompleks',
                        'true_false' => 'Benar / Salah',
                    ])
                    ->default('multiple_choice')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set, $state): void {
                        $set('options', self::defaultOptions($state ?? 'multiple_choice'));
                    }),

                Hidden::make('order_number')
                    ->default(function () {
                        return ((int) $this->getOwnerRecord()
                            ->questions()
                            ->max('order_number')) + 1;
                    })
                    ->dehydrated(true),

                Hidden::make('score')
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
                            ->disabled()
                            ->dehydrated(true)
                            ->maxLength(5),

                        Textarea::make('option_text')
                            ->label('Isi Pilihan')
                            ->rows(2)
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->label('Gambar Pilihan')
                            ->image()
                            ->disk('public')
                            ->directory('exam-option-images')
                            ->visibility('public')
                            ->imagePreviewHeight('160')
                            ->maxSize(2048)
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->helperText('Opsional. Gunakan jika opsi jawaban membutuhkan gambar, grafik, rumus, atau ilustrasi.')
                            ->columnSpanFull(),

                        Toggle::make('is_correct')
                            ->label('Jawaban Benar'),
                    ])
                    ->columns(2)
                    ->default(self::defaultOptions('multiple_choice'))
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
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
                ImportAction::make('importQuestions')
                    ->label('Import Soal')
                    ->importer(QuestionImporter::class)
                    ->options([
                        'exam_id' => $this->getOwnerRecord()->id,
                    ])
                    ->csvDelimiter(',')
                    ->chunkSize(50),
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

    private static function defaultOptions(string $questionType = 'multiple_choice'): array
    {
        if ($questionType === 'true_false') {
            return [
                [
                    'option_label' => 'A',
                    'option_text' => 'Benar',
                    'is_correct' => false,
                ],
                [
                    'option_label' => 'B',
                    'option_text' => 'Salah',
                    'is_correct' => false,
                ],
            ];
        }

        return [
            [
                'option_label' => 'A',
                'option_text' => '',
                'is_correct' => false,
            ],
            [
                'option_label' => 'B',
                'option_text' => '',
                'is_correct' => false,
            ],
            [
                'option_label' => 'C',
                'option_text' => '',
                'is_correct' => false,
            ],
            [
                'option_label' => 'D',
                'option_text' => '',
                'is_correct' => false,
            ],
            [
                'option_label' => 'E',
                'option_text' => '',
                'is_correct' => false,
            ],
        ];
    }
}
