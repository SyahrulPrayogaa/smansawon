<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamClassToken;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Student;
use App\Models\ExamViolation;

class ExamController extends Controller
{
    // maksimal jumlah pelanggaran meninggalkan tab yang diperbolehkan sebelum akun terkunci
    private int $maxTabLeaveViolations = 1;

    public function login(): View
    {
        return view('exams.login');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'exam_student_id',
            'exam_attempt_id',
        ]);

        $request->session()->regenerateToken();

        return redirect()
            ->route('student.exam.login')
            ->with('success', 'Kamu sudah keluar dari halaman ujian.');
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

        if ($existingAttempt && in_array($existingAttempt->status, ['submitted', 'expired'])) {
            session([
                'exam_attempt_id' => $existingAttempt->id,
            ]);

            return redirect()->route('student.exam.result');
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

        if ($attempt->is_locked) {
            return redirect()->route('student.exam.locked');
        }

        if (in_array($attempt->status, ['submitted', 'expired'])) {
            return redirect()->route('student.exam.result');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exam.profile');
        }

        if ($this->remainingSeconds($attempt) <= 0) {
            $this->expireAttempt($attempt);

            return redirect()
                ->route('student.exam.result')
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

        $selectedOptionIds = $selectedAnswer
            ? $selectedAnswer->options()->pluck('question_options.id')->toArray()
            : [];

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

        $answeredCount = count($answeredQuestionIds);
        $unansweredCount = $totalQuestions - $answeredCount;
        $allQuestionsAnswered = $unansweredCount === 0;

        return view('exams.question', [
            'student' => $student,
            'attempt' => $attempt,
            'exam' => $attempt->exam,
            'question' => $question,
            'number' => $number,
            'totalQuestions' => $totalQuestions,
            'selectedAnswer' => $selectedAnswer,
            'selectedOptionIds' => $selectedOptionIds,
            'remainingSeconds' => $this->remainingSeconds($attempt),
            'answeredNumbers' => $answeredNumbers,
            'answeredCount' => $answeredCount,
            'unansweredCount' => $unansweredCount,
            'allQuestionsAnswered' => $allQuestionsAnswered,
        ]);
    }

    public function saveAnswer(Request $request, int $number): RedirectResponse
    {
        $student = $this->currentStudent();
        $attempt = $this->currentAttempt();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! $attempt) {
            return redirect()->route('student.exam.result');
        }

        if ($attempt->is_locked) {
            return redirect()->route('student.exam.locked');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exam.profile');
        }

        if ($this->remainingSeconds($attempt) <= 0) {
            $this->expireAttempt($attempt);

            return redirect()
                ->route('student.exam.result')
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
            'question_option_ids' => ['nullable', 'array'],
            'question_option_ids.*' => ['integer', 'exists:question_options,id'],
            'action' => ['required', 'string', 'in:previous,next,save,finish'],
        ]);

        $selectedOptionIds = $validated['question_option_ids'] ?? [];

        $selectedOptionIds = collect($selectedOptionIds)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        if (! empty($selectedOptionIds)) {
            $validSelectedOptionIds = QuestionOption::query()
                ->where('question_id', $question->id)
                ->whereIn('id', $selectedOptionIds)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->sort()
                ->values()
                ->toArray();

            if (count($validSelectedOptionIds) !== count($selectedOptionIds)) {
                return back()
                    ->withErrors([
                        'question_option_ids' => 'Pilihan jawaban tidak valid.',
                    ]);
            }

            $correctOptionIds = QuestionOption::query()
                ->where('question_id', $question->id)
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->sort()
                ->values()
                ->toArray();

            $isCorrect = $validSelectedOptionIds === $correctOptionIds;
            $score = $isCorrect ? 1 : 0;

            $answer = ExamAnswer::updateOrCreate(
                [
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_option_id' => $validSelectedOptionIds[0] ?? null,
                    'answer_text' => null,
                    'is_correct' => $isCorrect,
                    'score' => $score,
                    'answered_at' => now(),
                ]
            );

            $answer->options()->sync($validSelectedOptionIds);
        } else {
            $existingAnswer = ExamAnswer::query()
                ->where('exam_attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->first();

            if ($existingAnswer) {
                $existingAnswer->options()->sync([]);
                $existingAnswer->delete();
            }
        }

        if ($validated['action'] === 'finish') {
            $answeredCount = ExamAnswer::query()
                ->where('exam_attempt_id', $attempt->id)
                ->count();

            if ($answeredCount < $totalQuestions) {
                return redirect()
                    ->route('student.exam.question', $number)
                    ->withErrors([
                        'submit' => 'Masih ada soal yang belum dijawab. Lengkapi semua jawaban sebelum mengumpulkan ujian.',
                    ]);
            }

            $this->submitAttempt($attempt);

            return redirect()
                ->route('student.exam.result')
                ->with('success', 'Jawaban ujian berhasil dikumpulkan.');
        }

        if ($validated['action'] === 'save') {
            return redirect()
                ->route('student.exam.question', $number)
                ->with('success', 'Jawaban berhasil disimpan.');
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

        if ($attempt->is_locked) {
            return redirect()->route('student.exam.locked');
        }

        $this->submitAttempt($attempt);

        return redirect()
            ->route('student.exam.result')
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

        $score = $this->calculateFinalScore($attempt);

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

        $score = $this->calculateFinalScore($attempt);

        $attempt->update([
            'status' => 'expired',
            'submitted_at' => now(),
            'score' => $score,
        ]);
    }

    private function calculateFinalScore(ExamAttempt $attempt): float
    {
        $totalQuestions = Question::query()
            ->where('exam_id', $attempt->exam_id)
            ->where('is_active', true)
            ->count();

        if ($totalQuestions === 0) {
            return 0;
        }

        $correctAnswers = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->where('is_correct', true)
            ->count();

        return round(($correctAnswers / $totalQuestions) * 100, 2);
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

    // ? fungsi untuk mencatat pelanggaran yang dilakukan siswa selama ujian, seperti meninggalkan tab ujian
    public function locked(): View|RedirectResponse
    {
        $student = $this->currentStudent();
        $attempt = $this->currentAttempt();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! $attempt) {
            return redirect()->route('student.exam.profile');
        }

        if (! $attempt->is_locked) {
            return redirect()->route('student.exam.question', 1);
        }

        return view('exams.locked', [
            'student' => $student,
            'attempt' => $attempt,
            'exam' => $attempt->exam,
        ]);
    }

    public function recordViolation(Request $request): JsonResponse
    {
        $attempt = $this->currentAttempt();

        if (! $attempt) {
            return response()->json([
                'message' => 'Attempt tidak ditemukan.',
            ], 404);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'message' => 'Ujian sudah tidak berjalan.',
            ]);
        }

        if ($attempt->is_locked) {
            return response()->json([
                'message' => 'Ujian sudah terkunci.',
                'is_locked' => true,
                'tab_leave_count' => $attempt->tab_leave_count,
                'max_violations' => $this->maxTabLeaveViolations,
            ]);
        }

        $validated = $request->validate([
            'violation_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        ExamViolation::create([
            'exam_attempt_id' => $attempt->id,
            'violation_type' => $validated['violation_type'],
            'description' => $validated['description'] ?? null,
            'metadata' => [
                'url' => $request->input('url'),
                'user_agent' => $request->userAgent(),
            ],
            'occurred_at' => now(),
        ]);

        $attempt->increment('tab_leave_count');
        $attempt->refresh();

        if ($attempt->tab_leave_count >= $this->maxTabLeaveViolations) {
            $attempt->update([
                'is_locked' => true,
                'locked_at' => now(),
                'lock_reason' => 'Siswa terdeteksi meninggalkan halaman ujian beberapa kali.',
            ]);

            return response()->json([
                'message' => 'Ujian dikunci karena melewati batas pelanggaran.',
                'is_locked' => true,
                'tab_leave_count' => $attempt->tab_leave_count,
                'max_violations' => $this->maxTabLeaveViolations,
            ]);
        }

        return response()->json([
            'message' => 'Pelanggaran tercatat.',
            'is_locked' => false,
            'tab_leave_count' => $attempt->tab_leave_count,
            'max_violations' => $this->maxTabLeaveViolations,
        ]);
    }

    public function result(): View|RedirectResponse
    {
        $student = $this->currentStudent();
        $attempt = $this->currentAttempt();

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! $attempt) {
            return redirect()->route('student.exam.profile');
        }

        if ($attempt->status === 'in_progress') {
            return redirect()->route('student.exam.question', 1);
        }

        $totalQuestions = Question::query()
            ->where('exam_id', $attempt->exam_id)
            ->where('is_active', true)
            ->count();

        $correctCount = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->where('is_correct', true)
            ->count();

        $answeredCount = ExamAnswer::query()
            ->where('exam_attempt_id', $attempt->id)
            ->count();

        $unansweredCount = max(0, $totalQuestions - $answeredCount);

        // Salah dihitung dari semua soal yang tidak benar.
        // Jadi termasuk soal salah dan soal kosong.
        $wrongCount = max(0, $totalQuestions - $correctCount);

        return view('exams.result', [
            'student' => $student,
            'attempt' => $attempt,
            'exam' => $attempt->exam,
            'totalQuestions' => $totalQuestions,
            'correctCount' => $correctCount,
            'wrongCount' => $wrongCount,
            'answeredCount' => $answeredCount,
            'unansweredCount' => $unansweredCount,
        ]);
    }
}
