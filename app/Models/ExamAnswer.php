<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }

    /**
     * Mengambil baris dari tabel exam_answer_options.
     */
    public function selectedOptions()
    {
        return $this->hasMany(ExamAnswerOption::class);
    }

    /**
     * Mengambil langsung opsi jawaban yang dipilih siswa.
     * Ini yang dipakai untuk multiple choice, multiple select, dan true/false.
     */
    public function options()
    {
        return $this->belongsToMany(
            QuestionOption::class,
            'exam_answer_options',
            'exam_answer_id',
            'question_option_id'
        );
    }
}
