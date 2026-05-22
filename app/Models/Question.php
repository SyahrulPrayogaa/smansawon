<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::updating(function (Question $question): void {
            if ($question->isDirty('image_path')) {
                $oldImagePath = $question->getOriginal('image_path');
                $newImagePath = $question->image_path;

                if (
                    filled($oldImagePath) &&
                    $oldImagePath !== $newImagePath
                ) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
        });

        static::deleting(function (Question $question): void {
            if (filled($question->image_path)) {
                Storage::disk('public')->delete($question->image_path);
            }

            foreach ($question->options as $option) {
                if (filled($option->image_path)) {
                    Storage::disk('public')->delete($option->image_path);
                }
            }
        });
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
