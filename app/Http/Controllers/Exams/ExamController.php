<?php

namespace App\Http\Controllers\Exams;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function show(): View
    {
        $student = [
            'name' => 'Budi Santoso',
            'class' => 'XI IPA 1',
        ];

        $exam = [
            'title' => 'Ujian Matematika',
            'subject' => 'Matematika',
            'duration_minutes' => 60,
        ];

        $questions = [
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

        return view('exams.show', compact(
            'student',
            'exam',
            'questions',
        ));
    }

    public function submit()
    {
        // Tahap awal: nanti kita isi untuk menyimpan jawaban siswa.
        return redirect()
            ->route('student.exam.show')
            ->with('success', 'Jawaban berhasil dikumpulkan.');
    }
}
