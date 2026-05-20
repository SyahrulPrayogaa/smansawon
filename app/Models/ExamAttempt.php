<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function token()
    {
        return $this->belongsTo(ExamClassToken::class, 'exam_class_token_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class);
    }
}
