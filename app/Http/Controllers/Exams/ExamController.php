<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamClassToken;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Student;

class ExamController extends Controller
{
    public function login(): View
    {
        return view('exams.login');
    }

    public function checkNisn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string', 'max:20'],
        ]);

        $student = Student::query()
            ->with('classRoom')
            ->where('nisn', $validated['nisn'])
            ->where('is_active', true)
            ->first();

        if (! $student) {
            return back()
                ->withInput()
                ->withErrors([
                    'nisn' => 'NISN tidak ditemukan atau siswa tidak aktif.',
                ]);
        }

        session([
            'exam_student_id' => $student->id,
            'exam_attempt_id' => null,
        ]);

        return redirect()->route('student.exam.profile');
    }

    public function profile(): View|RedirectResponse
    {
        $student = $this->currentStudent();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        $attempt = $this->currentAttempt();

        return view('exams.profile', [
            'student' => $student,
            'attempt' => $attempt,
        ]);
    }

    public function checkToken(Request $request): RedirectResponse
    {
        $student = $this->currentStudent();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        $validated = $request->validate([
            'token' => ['required', 'string', 'max:50'],
        ]);

        $token = ExamClassToken::query()
            ->with('exam')
            ->where('class_room_id', $student->class_room_id)
            ->where('token', strtoupper(trim($validated['token'])))
            ->where('is_active', true)
            ->whereHas('exam', function ($query) {
                $query->where('is_active', true);
            })
            ->first();

        if (! $token) {
            return back()
                ->withInput()
                ->withErrors([
                    'token' => 'Token tidak valid untuk kelas kamu.',
                ]);
        }

        if (! $this->isTokenCurrentlyValid($token)) {
            return back()
                ->withInput()
                ->withErrors([
                    'token' => 'Token ujian belum aktif atau sudah melewati batas waktu.',
                ]);
        }

        $existingAttempt = ExamAttempt::query()
            ->where('exam_id', $token->exam_id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingAttempt && $existingAttempt->status === 'submitted') {
            return back()
                ->withErrors([
                    'token' => 'Kamu sudah mengumpulkan ujian ini.',
                ]);
        }

        if ($existingAttempt && $existingAttempt->status === 'expired') {
            return back()
                ->withErrors([
                    'token' => 'Waktu pengerjaan ujian ini sudah habis.',
                ]);
        }

        $attempt = $existingAttempt ?: ExamAttempt::create([
            'exam_id' => $token->exam_id,
            'student_id' => $student->id,
            'exam_class_token_id' => $token->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        if (! $attempt->started_at) {
            $attempt->update([
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        session([
            'exam_attempt_id' => $attempt->id,
        ]);

        return redirect()->route('student.exam.question', 1);
    }

    public function question(int $number): View|RedirectResponse
    {
        $student = $this->currentStudent();
        $attempt = $this->currentAttempt();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! $attempt) {
            return redirect()->route('student.exam.profile');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()
                ->route('student.exam.profile')
                ->with('success', 'Ujian sudah selesai.');
        }

        if ($this->remainingSeconds($attempt) <= 0) {
            $this->expireAttempt($attempt);

            return redirect()
                ->route('student.exam.profile')
                ->with('success', 'Waktu ujian sudah habis. Jawaban yang tersimpan telah dikumpulkan.');
        }

        $questions = Question::query()
            ->with('options')
            ->where('exam_id', $attempt->exam_id)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()
                ->route('student.exam.profile')
                ->withErrors([
                    'token' => 'Soal ujian belum tersedia.',
                ]);
        }

        $totalQuestions = $questions->count();

        abort_if($number < 1 || $number > $totalQuestions, 404);

        $question = $questions[$number - 1];

        $selectedAnswer = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first();

        $answeredQuestionIds = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->pluck('question_id')
            ->toArray();

        $answeredNumbers = $questions
            ->values()
            ->filter(fn($item) => in_array($item->id, $answeredQuestionIds))
            ->map(fn($item, $index) => $questions->search(fn($q) => $q->id === $item->id) + 1)
            ->values()
            ->toArray();

        return view('exams.question', [
            'student' => $student,
            'attempt' => $attempt,
            'exam' => $attempt->exam,
            'question' => $question,
            'number' => $number,
            'totalQuestions' => $totalQuestions,
            'selectedAnswer' => $selectedAnswer,
            'remainingSeconds' => $this->remainingSeconds($attempt),
            'answeredNumbers' => $answeredNumbers,
        ]);
    }

    public function saveAnswer(Request $request, int $number): RedirectResponse
    {
        // dd($request->all());
        $student = $this->currentStudent();
        $attempt = $this->currentAttempt();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! $attempt) {
            return redirect()->route('student.exam.profile');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exam.profile');
        }

        if ($this->remainingSeconds($attempt) <= 0) {
            $this->expireAttempt($attempt);

            return redirect()
                ->route('student.exam.profile')
                ->with('success', 'Waktu ujian sudah habis. Jawaban yang tersimpan telah dikumpulkan.');
        }

        $questions = Question::query()
            ->where('exam_id', $attempt->exam_id)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->orderBy('id')
            ->get();

        $totalQuestions = $questions->count();

        abort_if($number < 1 || $number > $totalQuestions, 404);

        $question = $questions[$number - 1];

        $validated = $request->validate([
            'question_option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'action' => ['required', 'string', 'in:previous,next,finish'],
        ]);

        if (! empty($validated['question_option_id'])) {
            $selectedOption = QuestionOption::query()
                ->where('id', $validated['question_option_id'])
                ->where('question_id', $question->id)
                ->first();

            if (! $selectedOption) {
                return back()
                    ->withErrors([
                        'question_option_id' => 'Pilihan jawaban tidak valid.',
                    ]);
            }

            $isCorrect = $selectedOption->is_correct;
            $score = $isCorrect ? $question->score : 0;

            ExamAnswer::updateOrCreate(
                [
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_option_id' => $selectedOption->id,
                    'answer_text' => null,
                    'is_correct' => $isCorrect,
                    'score' => $score,
                    'answered_at' => now(),
                ]
            );
        }

        if ($validated['action'] === 'finish') {
            $this->submitAttempt($attempt);

            return redirect()
                ->route('student.exam.profile')
                ->with('success', 'Jawaban ujian berhasil dikumpulkan.');
        }

        if ($validated['action'] === 'previous') {
            return redirect()->route('student.exam.question', max(1, $number - 1));
        }

        return redirect()->route('student.exam.question', min($totalQuestions, $number + 1));
    }

    public function finish(Request $request): RedirectResponse
    {
        $attempt = $this->currentAttempt();

        if (! $attempt) {
            return redirect()->route('student.exam.login');
        }

        $this->submitAttempt($attempt);

        return redirect()
            ->route('student.exam.profile')
            ->with('success', 'Jawaban ujian berhasil dikumpulkan.');
    }

    private function currentStudent(): ?Student
    {
        $studentId = session('exam_student_id');

        if (! $studentId) {
            return null;
        }

        return Student::query()
            ->with('classRoom')
            ->find($studentId);
    }

    private function currentAttempt(): ?ExamAttempt
    {
        $attemptId = session('exam_attempt_id');

        if (! $attemptId) {
            return null;
        }

        return ExamAttempt::query()
            ->with(['exam', 'student.classRoom', 'token'])
            ->find($attemptId);
    }

    private function remainingSeconds(ExamAttempt $attempt): int
    {
        if (! $attempt->started_at) {
            return 0;
        }

        $durationSeconds = $attempt->exam->duration_minutes * 60;
        $endTime = $attempt->started_at->copy()->addSeconds($durationSeconds);

        return max(0, now()->diffInSeconds($endTime, false));
    }

    private function submitAttempt(ExamAttempt $attempt): void
    {
        if ($attempt->status !== 'in_progress') {
            return;
        }

        $score = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->sum('score');

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'score' => $score,
        ]);
    }

    private function expireAttempt(ExamAttempt $attempt): void
    {
        if ($attempt->status !== 'in_progress') {
            return;
        }

        $score = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->sum('score');

        $attempt->update([
            'status' => 'expired',
            'submitted_at' => now(),
            'score' => $score,
        ]);
    }

    private function isTokenCurrentlyValid(ExamClassToken $token): bool
    {
        $now = now();

        if ($token->starts_at && $now->lt($token->starts_at)) {
            return false;
        }

        if ($token->ends_at && $now->gt($token->ends_at)) {
            return false;
        }

        if ($token->exam->starts_at && $now->lt($token->exam->starts_at)) {
            return false;
        }

        if ($token->exam->ends_at && $now->gt($token->exam->ends_at)) {
            return false;
        }

        return true;
    }
}
