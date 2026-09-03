<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'category_id',
        'created_by',
        'code',
        'title',
        'description',
        'duration_minutes',
        'total_questions',
        'start_at',
        'end_at',
        'status',
        'max_attempts',
        'allow_review',
        'shuffle_questions',
        'shuffle_answers',
        'enable_anti_cheat',
        'require_fullscreen',
        'prevent_tab_switch',
        'prevent_copy_paste',
        'prevent_right_click',
        'prevent_screen_capture',
        'require_face_verification',
        'enable_proctor_camera',
        'proctor_interval_seconds',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'allow_review' => 'boolean',
        'enable_anti_cheat' => 'boolean',
        'require_fullscreen' => 'boolean',
        'prevent_tab_switch' => 'boolean',
        'prevent_copy_paste' => 'boolean',
        'prevent_right_click' => 'boolean',
        'prevent_screen_capture' => 'boolean',
        'require_face_verification' => 'boolean',
        'enable_proctor_camera' => 'boolean',
        'proctor_interval_seconds' => 'integer',
    ];

    public function isUnlimitedAttempts(): bool
    {
        return empty($this->max_attempts) || $this->max_attempts <= 0;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(ExamAssignment::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot('question_order', 'points')
            ->withTimestamps()
            ->orderBy('pivot_question_order');
    }
}
