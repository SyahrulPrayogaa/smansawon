<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function schoolSubject()
    {
        return $this->belongsTo(SchoolSubject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_number');
    }

    public function classTokens()
    {
        return $this->hasMany(ExamClassToken::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
