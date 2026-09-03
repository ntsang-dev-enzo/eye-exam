<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'expired_at',
        'status',
        'score',
        'score_value',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'cheat_warnings',
        'out_of_screen_time',
        'total_out_seconds',
        'violation_count',
        'face_verified_at',
        'face_similarity',
        'verification_image',
        'exam_session_token',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'expired_at' => 'datetime',
        'face_verified_at' => 'datetime',
        'face_similarity' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    public function antiCheatLogs()
    {
        return $this->hasMany(AntiCheatLog::class, 'attempt_id')->orderBy('occurred_at', 'desc');
    }

    public function proctorSnapshots()
    {
        return $this->hasMany(ExamProctorSnapshot::class, 'attempt_id')->orderBy('captured_at', 'desc');
    }

    public function getVerificationImageUrlAttribute(): ?string
    {
        if (empty($this->verification_image)) {
            return null;
        }
        if (str_starts_with($this->verification_image, 'http')) {
            return $this->verification_image;
        }
        return route('secure.media.verification', $this->id);
    }
}
