<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamClassToken;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SchoolSubject;
use App\Models\Student;

class ExamDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Kelas
        |--------------------------------------------------------------------------
        */

        $classXiIpa1 = ClassRoom::updateOrCreate(
            [
                'name' => 'XI IPA 1',
                'academic_year' => '2025/2026',
            ],
            [
                'grade' => 'XI',
                'major' => 'IPA',
                'is_active' => true,
            ]
        );

        $classXiIpa2 = ClassRoom::updateOrCreate(
            [
                'name' => 'XI IPA 2',
                'academic_year' => '2025/2026',
            ],
            [
                'grade' => 'XI',
                'major' => 'IPA',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Siswa
        |--------------------------------------------------------------------------
        */

        $students = [
            [
                'nisn' => '1234567890',
                'name' => 'Budi Santoso',
                'gender' => 'male',
                'class_room_id' => $classXiIpa1->id,
            ],
            [
                'nisn' => '1234567891',
                'name' => 'Siti Aminah',
                'gender' => 'female',
                'class_room_id' => $classXiIpa1->id,
            ],
            [
                'nisn' => '1234567892',
                'name' => 'Ahmad Fauzan',
                'gender' => 'male',
                'class_room_id' => $classXiIpa1->id,
            ],
            [
                'nisn' => '2234567890',
                'name' => 'Dewi Lestari',
                'gender' => 'female',
                'class_room_id' => $classXiIpa2->id,
            ],
            [
                'nisn' => '2234567891',
                'name' => 'Rizky Pratama',
                'gender' => 'male',
                'class_room_id' => $classXiIpa2->id,
            ],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                [
                    'nisn' => $student['nisn'],
                ],
                [
                    'name' => $student['name'],
                    'gender' => $student['gender'],
                    'class_room_id' => $student['class_room_id'],
                    'is_active' => true,
                ]
            );
        }
        /*
        |--------------------------------------------------------------------------
        | Mata Pelajaran
        |--------------------------------------------------------------------------
        */
        $subjects = [
            [
                'name' => 'Matematika',
                'code' => 'MTK',
                'description' => 'Mata pelajaran Matematika.',
            ],
            [
                'name' => 'Fisika',
                'code' => 'FIS',
                'description' => 'Mata pelajaran Fisika.',
            ],
            [
                'name' => 'Kimia',
                'code' => 'KIM',
                'description' => 'Mata pelajaran Kimia.',
            ],
            [
                'name' => 'Biologi',
                'code' => 'BIO',
                'description' => 'Mata pelajaran Biologi.',
            ],
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BIN',
                'description' => 'Mata pelajaran Bahasa Indonesia.',
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BIG',
                'description' => 'Mata pelajaran Bahasa Inggris.',
            ],
            [
                'name' => 'Sejarah',
                'code' => 'SEJ',
                'description' => 'Mata pelajaran Sejarah.',
            ],
            [
                'name' => 'Geografi',
                'code' => 'GEO',
                'description' => 'Mata pelajaran Geografi.',
            ],
            [
                'name' => 'Ekonomi',
                'code' => 'EKO',
                'description' => 'Mata pelajaran Ekonomi.',
            ],
            [
                'name' => 'Sosiologi',
                'code' => 'SOS',
                'description' => 'Mata pelajaran Sosiologi.',
            ],
            [
                'name' => 'Pendidikan Pancasila',
                'code' => 'PP',
                'description' => 'Mata pelajaran Pendidikan Pancasila.',
            ],
            [
                'name' => 'Pendidikan Agama Islam',
                'code' => 'PAI',
                'description' => 'Mata pelajaran Pendidikan Agama Islam.',
            ],
            [
                'name' => 'Informatika',
                'code' => 'INF',
                'description' => 'Mata pelajaran Informatika.',
            ],
            [
                'name' => 'Seni Budaya',
                'code' => 'SB',
                'description' => 'Mata pelajaran Seni Budaya.',
            ],
            [
                'name' => 'PJOK',
                'code' => 'PJOK',
                'description' => 'Mata pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan.',
            ],
        ];

        foreach ($subjects as $subject) {
            SchoolSubject::updateOrCreate(
                [
                    'name' => $subject['name'],
                ],
                [
                    'code' => $subject['code'],
                    'description' => $subject['description'],
                    'is_active' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Ujian
        |--------------------------------------------------------------------------
        */

        $mathSubject = SchoolSubject::where('name', 'Matematika')->first();

        $mathExam = Exam::updateOrCreate(
            [
                'title' => 'Ujian Matematika Kelas XI',
            ],
            [
                'school_subject_id' => $mathSubject?->id,
                'description' => 'Ujian latihan matematika kelas XI berbasis pilihan ganda.',
                'duration_minutes' => 60,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Token Ujian per Kelas
        |--------------------------------------------------------------------------
        */

        ExamClassToken::updateOrCreate(
            [
                'exam_id' => $mathExam->id,
                'class_room_id' => $classXiIpa1->id,
            ],
            [
                'token' => 'MATHXIIPA1',
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(7),
            ]
        );

        ExamClassToken::updateOrCreate(
            [
                'exam_id' => $mathExam->id,
                'class_room_id' => $classXiIpa2->id,
            ],
            [
                'token' => 'MATHXIIPA2',
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(7),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Bank Soal dan Pilihan Jawaban
        |--------------------------------------------------------------------------
        */

        $questions = [
            [
                'order_number' => 1,
                'question_text' => 'Hasil dari 12 × 8 adalah ...',
                'score' => 10,
                'options' => [
                    ['label' => 'A', 'text' => '86', 'is_correct' => false],
                    ['label' => 'B', 'text' => '94', 'is_correct' => false],
                    ['label' => 'C', 'text' => '96', 'is_correct' => true],
                    ['label' => 'D', 'text' => '108', 'is_correct' => false],
                    ['label' => 'E', 'text' => '112', 'is_correct' => false],
                ],
            ],
            [
                'order_number' => 2,
                'question_text' => 'Jika x + 5 = 12, maka nilai x adalah ...',
                'score' => 10,
                'options' => [
                    ['label' => 'A', 'text' => '5', 'is_correct' => false],
                    ['label' => 'B', 'text' => '6', 'is_correct' => false],
                    ['label' => 'C', 'text' => '7', 'is_correct' => true],
                    ['label' => 'D', 'text' => '8', 'is_correct' => false],
                    ['label' => 'E', 'text' => '9', 'is_correct' => false],
                ],
            ],
            [
                'order_number' => 3,
                'question_text' => 'Luas persegi dengan panjang sisi 9 cm adalah ...',
                'score' => 10,
                'options' => [
                    ['label' => 'A', 'text' => '18 cm²', 'is_correct' => false],
                    ['label' => 'B', 'text' => '27 cm²', 'is_correct' => false],
                    ['label' => 'C', 'text' => '72 cm²', 'is_correct' => false],
                    ['label' => 'D', 'text' => '81 cm²', 'is_correct' => true],
                    ['label' => 'E', 'text' => '90 cm²', 'is_correct' => false],
                ],
            ],
            [
                'order_number' => 4,
                'question_text' => 'Nilai dari 3² + 4² adalah ...',
                'score' => 10,
                'options' => [
                    ['label' => 'A', 'text' => '7', 'is_correct' => false],
                    ['label' => 'B', 'text' => '12', 'is_correct' => false],
                    ['label' => 'C', 'text' => '25', 'is_correct' => true],
                    ['label' => 'D', 'text' => '49', 'is_correct' => false],
                    ['label' => 'E', 'text' => '81', 'is_correct' => false],
                ],
            ],
            [
                'order_number' => 5,
                'question_text' => 'Jika 2x = 18, maka nilai x adalah ...',
                'score' => 10,
                'options' => [
                    ['label' => 'A', 'text' => '6', 'is_correct' => false],
                    ['label' => 'B', 'text' => '7', 'is_correct' => false],
                    ['label' => 'C', 'text' => '8', 'is_correct' => false],
                    ['label' => 'D', 'text' => '9', 'is_correct' => true],
                    ['label' => 'E', 'text' => '10', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $questionData) {
            $question = Question::updateOrCreate(
                [
                    'exam_id' => $mathExam->id,
                    'order_number' => $questionData['order_number'],
                ],
                [
                    'question_text' => $questionData['question_text'],
                    'question_type' => 'multiple_choice',
                    'score' => $questionData['score'],
                    'is_active' => true,
                ]
            );

            foreach ($questionData['options'] as $optionData) {
                QuestionOption::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'option_label' => $optionData['label'],
                    ],
                    [
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'],
                    ]
                );
            }
        }
    }
}
