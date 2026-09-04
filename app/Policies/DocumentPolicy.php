<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return DB::table('class_subject_teacher')
                ->where('class_id', $document->class_id)
                ->where('subject_id', $document->subject_id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        if ($user->role === 'student') {
            return DB::table('class_students')
                ->where('class_id', $document->class_id)
                ->where('student_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the teacher can create documents for a class & subject.
     */
    public function create(User $user, SchoolClass $class, Subject $subject): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return DB::table('class_subject_teacher')
                ->where('class_id', $class->id)
                ->where('subject_id', $subject->id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the document.
     */
    public function update(User $user, Document $document): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return $document->teacher_id === $user->id && DB::table('class_subject_teacher')
                ->where('class_id', $document->class_id)
                ->where('subject_id', $document->subject_id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return $document->teacher_id === $user->id && DB::table('class_subject_teacher')
                ->where('class_id', $document->class_id)
                ->where('subject_id', $document->subject_id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}
