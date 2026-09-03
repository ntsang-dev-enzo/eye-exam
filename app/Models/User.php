<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'password',
        'avatar',
        'role',
        'department',
        'status',
        'face_registered',
        'face_embedding',
        'face_images',
        'face_registered_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'face_embedding',
        'face_images',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'face_registered' => 'boolean',
            'face_images' => 'array',
            'face_registered_at' => 'datetime',
        ];
    }

    /**
     * Get user avatar URL (explicitly uploaded by Admin/User, separate from Face ID).
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }
            return route('secure.media.avatar', $this->id);
        }
        return null;
    }

    public function getFrontalFaceUrlAttribute(): ?string
    {
        if (!empty($this->face_images) && !empty($this->face_images['frontal'])) {
            $path = $this->face_images['frontal'];
            if (str_starts_with($path, 'http')) {
                return $path;
            }
            return route('secure.media.face', ['targetUser' => $this->id, 'angle' => 'frontal']);
        }
        if (!empty($this->frontal_face_path)) {
            return route('secure.media.face', ['targetUser' => $this->id, 'angle' => 'frontal']);
        }
        return null;
    }

    public function proctorSnapshots()
    {
        return $this->hasMany(ExamProctorSnapshot::class, 'student_id');
    }

    /**
     * Get the subjects assigned to the teacher.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    public function antiCheatLogs()
    {
        return $this->hasMany(AntiCheatLog::class, 'student_id')->orderBy('occurred_at', 'desc');
    }
}
