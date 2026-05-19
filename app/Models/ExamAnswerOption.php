<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswerOption extends Model
{
    protected $guarded = ['id'];

    public function answer()
    {
        return $this->belongsTo(ExamAnswer::class, 'exam_answer_id');
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
