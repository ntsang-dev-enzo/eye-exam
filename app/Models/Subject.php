<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
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

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'subject_id');
    }

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
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'teacher_id')
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_subject')->withTimestamps();
    }

    public function documentCategories()
    {
        return $this->hasMany(DocumentCategory::class, 'subject_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'subject_id');
    }
}
