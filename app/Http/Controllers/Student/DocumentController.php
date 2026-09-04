<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Check if the authenticated student belongs to this Class.
     */
    private function authorizeStudentClass(SchoolClass $class): void
    {
        $studentId = auth()->id();
        $belongsToClass = DB::table('class_students')
            ->where('class_id', $class->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$belongsToClass) {
            abort(403, 'Bạn không thuộc lớp học này.');
        }
    }

    /**
     * Display categories and documents for a Class + Subject for Student.
     */
    public function index(SchoolClass $class, Subject $subject)
    {
        $this->authorizeStudentClass($class);

        // Verify that the subject is assigned to this class
        $subjectAssigned = DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->first();

        if (!$subjectAssigned) {
            abort(404, 'Môn học không thuộc chương trình của lớp này.');
        }

        // Get teacher info
        $teacher = \App\Models\User::find($subjectAssigned->teacher_id);

        $categories = DocumentCategory::where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->with(['documents' => function ($q) {
                $q->with('teacher')->latest();
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalDocuments = Document::where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->count();

        return view('student.documents.index', compact('class', 'subject', 'teacher', 'categories', 'totalDocuments'));
    }

    /**
     * View PDF inline in browser for Student.
     */
    public function view(Document $document): Response
    {
        $class = $document->schoolClass;
        $this->authorizeStudentClass($class);

        if ($document->file_type !== 'pdf') {
            abort(400, 'Chỉ hỗ trợ xem trực tuyến đối với tệp PDF.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Tệp tài liệu không tồn tại trên hệ thống lưu trữ.');
        }

        $filePath = Storage::disk('local')->path($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($document->original_filename) . '"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Download document file for Student.
     */
    public function download(Document $document): Response
    {
        $class = $document->schoolClass;
        $this->authorizeStudentClass($class);

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Tệp tài liệu không tồn tại trên hệ thống lưu trữ.');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }
}
