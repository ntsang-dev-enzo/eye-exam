<?php

namespace App\Http\Controllers\Teacher;

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
     * Whitelist allowed extensions and maximum file sizes (in bytes).
     */
    private const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'zip'];
    private const MAX_SIZE_DOCUMENT = 20 * 1024 * 1024; // 20MB for PDF, DOC, DOCX
    private const MAX_SIZE_ZIP = 100 * 1024 * 1024;      // 100MB for ZIP

    /**
     * Check if the authenticated teacher is assigned to this Class + Subject.
     */
    private function authorizeClassSubject(SchoolClass $class, Subject $subject): void
    {
        $teacherId = auth()->id();
        $isAssigned = DB::table('class_subject_teacher')
            ->where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$isAssigned && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không được phân công giảng dạy môn học này cho lớp.');
        }
    }

    /**
     * Display categories and documents for a Class + Subject.
     */
    public function index(SchoolClass $class, Subject $subject)
    {
        $this->authorizeClassSubject($class, $subject);

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

        return view('teacher.documents.index', compact('class', 'subject', 'categories', 'totalDocuments'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request, SchoolClass $class, Subject $subject)
    {
        $this->authorizeClassSubject($class, $subject);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DocumentCategory::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Tạo danh mục tài liệu thành công!');
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Request $request, DocumentCategory $category)
    {
        $class = $category->schoolClass;
        $subject = $category->subject;
        $this->authorizeClassSubject($class, $subject);

        if ($category->teacher_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền chỉnh sửa danh mục này.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(DocumentCategory $category)
    {
        $class = $category->schoolClass;
        $subject = $category->subject;
        $this->authorizeClassSubject($class, $subject);

        if ($category->teacher_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xóa danh mục này.');
        }

        // Check if category has documents
        if ($category->documents()->count() > 0) {
            return back()->withErrors(['category' => 'Không thể xóa danh mục vì vẫn còn tài liệu. Vui lòng xóa hoặc di chuyển các tài liệu trước.']);
        }

        $category->delete();

        return back()->with('success', 'Đã xóa danh mục tài liệu thành công!');
    }

    /**
     * Store a new document.
     */
    public function store(Request $request, SchoolClass $class, Subject $subject)
    {
        $this->authorizeClassSubject($class, $subject);

        $request->validate([
            'category_id' => 'required|exists:document_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file',
        ]);

        // Verify category belongs to this class + subject
        $category = DocumentCategory::where('id', $request->category_id)
            ->where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->firstOrFail();

        $file = $request->file('file');
        $this->validateDocumentFile($file);

        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // Store file securely in private disk
        $path = $file->store("documents/{$class->id}/{$subject->id}", 'local');

        Document::create([
            'category_id' => $category->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'original_filename' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
            'file_size' => $fileSize,
        ]);

        return back()->with('success', 'Tải lên tài liệu thành công!');
    }

    /**
     * Update an existing document.
     */
    public function update(Request $request, Document $document)
    {
        $class = $document->schoolClass;
        $subject = $document->subject;
        $this->authorizeClassSubject($class, $subject);

        if ($document->teacher_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền chỉnh sửa tài liệu này.');
        }

        $request->validate([
            'category_id' => 'required|exists:document_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file',
        ]);

        // Verify target category belongs to the same class + subject
        $category = DocumentCategory::where('id', $request->category_id)
            ->where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->firstOrFail();

        $updateData = [
            'category_id' => $category->id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Handle file replacement if a new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $this->validateDocumentFile($file);

            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            $newPath = $file->store("documents/{$class->id}/{$subject->id}", 'local');
            $oldPath = $document->file_path;

            $updateData['original_filename'] = $originalName;
            $updateData['file_path'] = $newPath;
            $updateData['file_type'] = $extension;
            $updateData['file_size'] = $fileSize;

            // Delete old file from storage
            if ($oldPath && Storage::disk('local')->exists($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
        }

        $document->update($updateData);

        return back()->with('success', 'Cập nhật tài liệu thành công!');
    }

    /**
     * Delete a document and its stored file.
     */
    public function destroy(Document $document)
    {
        $class = $document->schoolClass;
        $subject = $document->subject;
        $this->authorizeClassSubject($class, $subject);

        if ($document->teacher_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xóa tài liệu này.');
        }

        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Đã xóa tài liệu thành công!');
    }

    /**
     * View PDF inline in browser.
     */
    public function view(Document $document): Response
    {
        $class = $document->schoolClass;
        $subject = $document->subject;
        $this->authorizeClassSubject($class, $subject);

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
     * Download document file.
     */
    public function download(Document $document): Response
    {
        $class = $document->schoolClass;
        $subject = $document->subject;
        $this->authorizeClassSubject($class, $subject);

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Tệp tài liệu không tồn tại trên hệ thống lưu trữ.');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }

    /**
     * Validate file type and file size strictly against whitelist.
     */
    private function validateDocumentFile($file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            abort(422, 'Loại tệp không được hỗ trợ. Chỉ cho phép các định dạng: PDF, DOC, DOCX, ZIP.');
        }

        $size = $file->getSize();

        if ($extension === 'zip') {
            if ($size > self::MAX_SIZE_ZIP) {
                abort(422, 'Dung lượng tệp ZIP vượt quá giới hạn cho phép (Tối đa 100MB).');
            }
        } else {
            if ($size > self::MAX_SIZE_DOCUMENT) {
                abort(422, 'Dung lượng tệp PDF/DOC/DOCX vượt quá giới hạn cho phép (Tối đa 20MB).');
            }
        }
    }
}
