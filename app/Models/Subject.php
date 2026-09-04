<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'credits',
        'description',
        'status',
    ];

    protected $casts = [
        'credits' => 'integer',
        'status' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_subject')->withTimestamps();
    }
}
