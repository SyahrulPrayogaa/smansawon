<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function login(): View
    {
        return view('exams.login');
    }

    public function checkNisn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string', 'min:8', 'max:20'],
        ]);

        // Tahap awal: data siswa masih dummy.
        // Nanti bagian ini diganti query ke tabel students berdasarkan NISN.
        $students = $this->dummyStudents();
        $student = collect($students)->firstWhere('nisn', $validated['nisn']);

        if (! $student) {
            return back()
                ->withInput()
                ->withErrors([
                    'nisn' => 'NISN tidak ditemukan. Periksa kembali NISN kamu.',
                ]);
        }

        session([
            'exam_student' => $student,
        ]);

        return redirect()->route('student.exam.profile');
    }

    public function profile(): View|RedirectResponse
    {
        $student = session('exam_student');

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        return view('exams.profile', [
            'student' => $student,
            'exam' => $this->dummyExam(),
        ]);
    }

    public function checkToken(Request $request): RedirectResponse
    {
        $student = session('exam_student');

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        $validated = $request->validate([
            'token' => ['required', 'string', 'max:30'],
        ]);

        $exam = $this->dummyExam();

        // Token dibuat seragam untuk satu kelas.
        // Tahap database nanti: token diambil dari tabel exam_sessions/class_exam_tokens.
        if (strtoupper($validated['token']) !== $exam['token']) {
            return back()
                ->withInput()
                ->withErrors([
                    'token' => 'Token ujian tidak sesuai. Silakan periksa kembali token dari pengawas.',
                ]);
        }

        session([
            'exam_started_at' => now()->timestamp,
            'exam_duration_seconds' => $exam['duration_minutes'] * 60,
            'exam_answers' => session('exam_answers', []),
            'exam_is_running' => true,
        ]);

        return redirect()->route('student.exam.question', 1);
    }

    public function question(int $number): View|RedirectResponse
    {
        $student = session('exam_student');

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! session('exam_is_running')) {
            return redirect()->route('student.exam.profile');
        }

        if ($this->remainingSeconds() <= 0) {
            return redirect()->route('student.exam.question', $number)
                ->with('time_up', true);
        }

        $questions = $this->dummyQuestions();
        $totalQuestions = count($questions);

        abort_if($number < 1 || $number > $totalQuestions, 404);

        $question = $questions[$number - 1];
        $answers = session('exam_answers', []);
        $selectedAnswer = $answers[$question['id']] ?? null;

        return view('exams.question', [
            'student' => $student,
            'exam' => $this->dummyExam(),
            'question' => $question,
            'number' => $number,
            'totalQuestions' => $totalQuestions,
            'selectedAnswer' => $selectedAnswer,
            'remainingSeconds' => $this->remainingSeconds(),
            'answeredNumbers' => $this->answeredNumbers($questions, $answers),
        ]);
    }

    public function saveAnswer(Request $request, int $number): RedirectResponse
    {
        $student = session('exam_student');

        if (! $student) {
            return redirect()->route('student.exam.login');
        }

        if (! session('exam_is_running')) {
            return redirect()->route('student.exam.profile');
        }

        if ($this->remainingSeconds() <= 0) {
            return $this->finish($request);
        }

        $questions = $this->dummyQuestions();
        $totalQuestions = count($questions);

        abort_if($number < 1 || $number > $totalQuestions, 404);

        $question = $questions[$number - 1];

        $validated = $request->validate([
            'answer' => ['nullable', 'string', 'in:A,B,C,D,E'],
            'action' => ['required', 'string', 'in:previous,next,finish'],
        ]);

        $answers = session('exam_answers', []);

        // Inilah inti autosave per soal.
        // Nanti bagian ini diganti create/update ke tabel exam_answers.
        if (! empty($validated['answer'])) {
            $answers[$question['id']] = $validated['answer'];
            session(['exam_answers' => $answers]);
        }

        if ($validated['action'] === 'finish') {
            return $this->finish($request);
        }

        if ($validated['action'] === 'previous') {
            return redirect()->route('student.exam.question', max(1, $number - 1));
        }

        return redirect()->route('student.exam.question', min($totalQuestions, $number + 1));
    }

    public function finish(Request $request): RedirectResponse
    {
        // Tahap awal: jawaban masih tersimpan di session.
        // Tahap database nanti: simpan status attempt = submitted.
        session([
            'exam_is_running' => false,
            'exam_submitted_at' => now()->timestamp,
        ]);

        return redirect()
            ->route('student.exam.profile')
            ->with('success', 'Jawaban ujian berhasil dikumpulkan.');
    }

    private function remainingSeconds(): int
    {
        $startedAt = session('exam_started_at');
        $duration = session('exam_duration_seconds');

        if (! $startedAt || ! $duration) {
            return 0;
        }

        $elapsed = now()->timestamp - $startedAt;

        return max(0, $duration - $elapsed);
    }

    private function answeredNumbers(array $questions, array $answers): array
    {
        $answeredNumbers = [];

        foreach ($questions as $index => $question) {
            if (array_key_exists($question['id'], $answers)) {
                $answeredNumbers[] = $index + 1;
            }
        }

        return $answeredNumbers;
    }

    private function dummyStudents(): array
    {
        return [
            [
                'nisn' => '1234567890',
                'name' => 'Budi Santoso',
                'class' => 'XI IPA 1',
            ],
            [
                'nisn' => '0987654321',
                'name' => 'Siti Aminah',
                'class' => 'XI IPA 1',
            ],
        ];
    }

    private function dummyExam(): array
    {
        return [
            'title' => 'Ujian Matematika',
            'subject' => 'Matematika',
            'class' => 'XI IPA 1',
            'duration_minutes' => 60,
            'token' => 'MATHXIIPA1',
        ];
    }

    private function dummyQuestions(): array
    {
        return [
            [
                'id' => 1,
                'question' => 'Hasil dari 12 × 8 adalah ...',
                'options' => [
                    'A' => '86',
                    'B' => '94',
                    'C' => '96',
                    'D' => '108',
                    'E' => '112',
                ],
            ],
            [
                'id' => 2,
                'question' => 'Jika x + 5 = 12, maka nilai x adalah ...',
                'options' => [
                    'A' => '5',
                    'B' => '6',
                    'C' => '7',
                    'D' => '8',
                    'E' => '9',
                ],
            ],
            [
                'id' => 3,
                'question' => 'Bangun datar yang memiliki empat sisi sama panjang adalah ...',
                'options' => [
                    'A' => 'Segitiga',
                    'B' => 'Persegi',
                    'C' => 'Lingkaran',
                    'D' => 'Trapesium',
                    'E' => 'Jajar genjang',
                ],
            ],
        ];
    }
}
