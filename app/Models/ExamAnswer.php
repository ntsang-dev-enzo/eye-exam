<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_id',
        'text_answer',
        'is_correct',
        'points_earned',
        'answered_at'
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'is_correct' => 'boolean',
    ];

    public function getAnswerTextAttribute()
    {
        return $this->text_answer;
    }

    public function setAnswerTextAttribute($value)
    {
        $this->attributes['text_answer'] = $value;
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedAnswer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
