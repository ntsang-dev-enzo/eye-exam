<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExamProctorSnapshot extends Model
{
    protected $fillable = [
        'attempt_id',
        'student_id',
        'image_path',
        'status',
        'violations',
        'detections',
        'face_similarity',
        'details',
        'captured_at',
    ];

    protected $casts = [
        'violations' => 'array',
        'detections' => 'array',
        'face_similarity' => 'decimal:2',
        'captured_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return '';
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return route('secure.media.snapshot', $this->id);
    }
}
