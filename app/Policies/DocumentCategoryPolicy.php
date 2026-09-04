<?php

namespace App\Policies;

use App\Models\DocumentCategory;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class DocumentCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the document category.
     */
    public function view(User $user, DocumentCategory $category): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return DB::table('class_subject_teacher')
                ->where('class_id', $category->class_id)
                ->where('subject_id', $category->subject_id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        if ($user->role === 'student') {
            return DB::table('class_students')
                ->where('class_id', $category->class_id)
                ->where('student_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the teacher can create categories for a class & subject.
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
     * Determine whether the user can update the document category.
     */
    public function update(User $user, DocumentCategory $category): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return $category->teacher_id === $user->id && DB::table('class_subject_teacher')
                ->where('class_id', $category->class_id)
                ->where('subject_id', $category->subject_id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the document category.
     */
    public function delete(User $user, DocumentCategory $category): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teacher') {
            return $category->teacher_id === $user->id && DB::table('class_subject_teacher')
                ->where('class_id', $category->class_id)
                ->where('subject_id', $category->subject_id)
                ->where('teacher_id', $user->id)
                ->exists();
        }

        return false;
    }
}
