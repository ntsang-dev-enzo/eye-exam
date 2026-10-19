<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class, 'faculty_id');
    }

    public function subjects()
    {
        return $this->hasManyThrough(Subject::class, Department::class, 'faculty_id', 'department_id');
    }
}
