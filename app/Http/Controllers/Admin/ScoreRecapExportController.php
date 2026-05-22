<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\SchoolSubject;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ScoreRecapExportController extends Controller
{
    public function pdf(Request $request)
    {
        $validated = $request->validate([
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'school_subject_id' => ['required', 'integer', 'exists:school_subjects,id'],
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
        ]);

        $classRoom = ClassRoom::findOrFail($validated['class_room_id']);
        $schoolSubject = SchoolSubject::findOrFail($validated['school_subject_id']);

        $exam = Exam::query()
            ->with('schoolSubject')
            ->where('school_subject_id', $schoolSubject->id)
            ->findOrFail($validated['exam_id']);

        $students = Student::query()
            ->with('classRoom')
            ->where('class_room_id', $classRoom->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $attempts = ExamAttempt::query()
            ->with(['answers'])
            ->where('exam_id', $exam->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $totalQuestions = Question::query()
            ->where('exam_id', $exam->id)
            ->where('is_active', true)
            ->count();

        $rows = $students->map(function (Student $student) use ($attempts, $totalQuestions) {
            $attempt = $attempts->get($student->id);

            if (! $attempt) {
                return [
                    'student' => $student,
                    'attempt' => null,
                    'status' => 'not_started',
                    'correct_count' => 0,
                    'wrong_count' => 0,
                    'unanswered_count' => $totalQuestions,
                    'score' => null,
                ];
            }

            $correctCount = $attempt->answers
                ->where('is_correct', true)
                ->count();

            $answeredCount = $attempt->answers->count();

            return [
                'student' => $student,
                'attempt' => $attempt,
                'status' => $attempt->status,
                'correct_count' => $correctCount,
                'wrong_count' => max(0, $totalQuestions - $correctCount),
                'unanswered_count' => max(0, $totalQuestions - $answeredCount),
                'score' => $attempt->score,
            ];
        });

        $pdf = Pdf::loadView('exports.score-recap-pdf', [
            'classRoom' => $classRoom,
            'schoolSubject' => $schoolSubject,
            'exam' => $exam,
            'rows' => $rows,
            'totalQuestions' => $totalQuestions,
        ])->setPaper('a4', 'portrait');

        $filename = 'rekap-nilai-'
            . str($classRoom->name)->slug()
            . '-'
            . str($schoolSubject->name)->slug()
            . '-'
            . str($exam->title)->slug()
            . '.pdf';

        return $pdf->stream($filename);
    }
}
