<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuestionOption extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    protected static function booted(): void
    {
        static::updating(function (QuestionOption $option): void {
            if ($option->isDirty('image_path')) {
                $oldImagePath = $option->getOriginal('image_path');
                $newImagePath = $option->image_path;

                if (
                    filled($oldImagePath) &&
                    $oldImagePath !== $newImagePath
                ) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
        });

        static::deleting(function (QuestionOption $option): void {
            if (filled($option->image_path)) {
                Storage::disk('public')->delete($option->image_path);
            }
        });
    }
}
