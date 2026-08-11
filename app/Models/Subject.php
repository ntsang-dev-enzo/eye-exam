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
        'description',
        'status',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
