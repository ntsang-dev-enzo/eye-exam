<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'semester',
        'academic_year',
        'description',
        'status',
    ];

    /**
     * Get the subjects linked to this course.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'course_subject')->withTimestamps();
    }
}
