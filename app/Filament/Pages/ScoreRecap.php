<?php

namespace App\Filament\Pages;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\SchoolSubject;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ScoreRecap extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Rekap Nilai';

    protected static ?string $title = 'Rekap Nilai';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Ujian';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.score-recap.view-score-recap';

    public ?int $class_room_id = null;

    public ?int $school_subject_id = null;

    public ?int $exam_id = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('class_room_id')
                    ->label('Kelas')
                    ->options(
                        ClassRoom::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->resetTable();
                    }),

                Select::make('school_subject_id')
                    ->label('Mata Pelajaran')
                    ->options(
                        SchoolSubject::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->exam_id = null;
                        $this->resetTable();
                    }),

                Select::make('exam_id')
                    ->label('Paket Ujian')
                    ->options(function (): array {
                        if (! $this->school_subject_id) {
                            return [];
                        }

                        return Exam::query()
                            ->where('school_subject_id', $this->school_subject_id)
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->disabled(fn(): bool => ! filled($this->school_subject_id))
                    ->afterStateUpdated(function (): void {
                        $this->resetTable();
                    }),
            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('classRoom.name')
                    ->label('Kelas'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Student $record): string {
                        $attempt = $this->getStudentAttempt($record);

                        return match ($attempt?->status) {
                            'in_progress' => 'Sedang Mengerjakan',
                            'submitted' => 'Selesai',
                            'expired' => 'Waktu Habis',
                            default => 'Belum Mengerjakan',
                        };
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'Selesai' => 'success',
                            'Waktu Habis' => 'danger',
                            'Sedang Mengerjakan' => 'warning',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('correct_count')
                    ->label('Benar')
                    ->alignCenter()
                    ->getStateUsing(function (Student $record): int {
                        $attempt = $this->getStudentAttempt($record);

                        if (! $attempt) {
                            return 0;
                        }

                        return $attempt->answers
                            ->where('is_correct', true)
                            ->count();
                    }),

                TextColumn::make('wrong_count')
                    ->label('Salah')
                    ->alignCenter()
                    ->getStateUsing(function (Student $record): int {
                        $attempt = $this->getStudentAttempt($record);

                        if (! $attempt) {
                            return 0;
                        }

                        $totalQuestions = $this->getTotalQuestions();
                        $correctCount = $attempt->answers
                            ->where('is_correct', true)
                            ->count();

                        return max(0, $totalQuestions - $correctCount);
                    }),

                TextColumn::make('unanswered_count')
                    ->label('Kosong')
                    ->alignCenter()
                    ->getStateUsing(function (Student $record): int {
                        $attempt = $this->getStudentAttempt($record);

                        if (! $attempt) {
                            return $this->getTotalQuestions();
                        }

                        $totalQuestions = $this->getTotalQuestions();
                        $answeredCount = $attempt->answers->count();

                        return max(0, $totalQuestions - $answeredCount);
                    }),

                TextColumn::make('score')
                    ->label('Nilai')
                    ->alignEnd()
                    ->getStateUsing(function (Student $record): string {
                        $attempt = $this->getStudentAttempt($record);

                        return is_null($attempt?->score)
                            ? '-'
                            : number_format((float) $attempt->score, 2);
                    }),
            ])
            ->emptyStateHeading('Pilih filter rekap nilai')
            ->emptyStateDescription('Pilih kelas, mata pelajaran, dan paket ujian terlebih dahulu.')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getTableQuery(): Builder
    {
        $query = Student::query()
            ->with([
                'classRoom',
                'examAttempts' => function ($query) {
                    $query
                        ->with(['exam.schoolSubject', 'answers'])
                        ->when($this->exam_id, function ($query) {
                            $query->where('exam_id', $this->exam_id);
                        });
                },
            ])
            ->where('is_active', true)
            ->orderBy('name');

        if (! $this->class_room_id || ! $this->school_subject_id || ! $this->exam_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('class_room_id', $this->class_room_id);
    }

    protected function getStudentAttempt(Student $student): ?ExamAttempt
    {
        if (! $this->exam_id) {
            return null;
        }

        return $student->examAttempts
            ->firstWhere('exam_id', $this->exam_id);
    }

    protected function getTotalQuestions(): int
    {
        if (! $this->exam_id) {
            return 0;
        }

        return Question::query()
            ->where('exam_id', $this->exam_id)
            ->where('is_active', true)
            ->count();
    }

    protected function getSelectedExam(): ?Exam
    {
        if (! $this->exam_id) {
            return null;
        }

        return Exam::query()
            ->with('schoolSubject')
            ->find($this->exam_id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->url(fn(): string => route('admin.score-recap.pdf', [
                    'class_room_id' => $this->class_room_id,
                    'school_subject_id' => $this->school_subject_id,
                    'exam_id' => $this->exam_id,
                ]))
                ->openUrlInNewTab()
                ->disabled(fn(): bool => ! $this->class_room_id || ! $this->school_subject_id || ! $this->exam_id),
        ];
    }
}
