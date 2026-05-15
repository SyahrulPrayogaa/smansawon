<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamClassToken extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
