<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $teacherA;
    private User $teacherB;
    private User $student1;
    private User $studentOther;
    private SchoolClass $class1;
    private SchoolClass $class2;
    private Subject $subject1;
    private Subject $subject2;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        // Create Teachers
        $this->teacherA = User::factory()->create(['role' => 'teacher', 'status' => 'active', 'code' => 'GV01']);
        $this->teacherB = User::factory()->create(['role' => 'teacher', 'status' => 'active', 'code' => 'GV02']);

        // Create Students
        $this->student1 = User::factory()->create(['role' => 'student', 'status' => 'active', 'code' => 'SV01']);
        $this->studentOther = User::factory()->create(['role' => 'student', 'status' => 'active', 'code' => 'SV02']);

        // Create Subjects
        $this->subject1 = Subject::create(['code' => 'CS101', 'name' => 'Lập trình C++', 'credits' => 3, 'status' => true]);
        $this->subject2 = Subject::create(['code' => 'CS102', 'name' => 'Cơ sở dữ liệu', 'credits' => 3, 'status' => true]);

        // Create Classes
        $this->class1 = SchoolClass::create([
            'code' => 'CNTT01',
            'name' => 'Lớp CNTT K15A',
            'teacher_id' => $this->teacherA->id,
            'status' => 'active'
        ]);

        $this->class2 = SchoolClass::create([
            'code' => 'CNTT02',
            'name' => 'Lớp CNTT K15B',
            'teacher_id' => $this->teacherB->id,
            'status' => 'active'
        ]);

        // Assign teacherA to class1 + subject1
        DB::table('class_subject_teacher')->insert([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign teacherB to class2 + subject2
        DB::table('class_subject_teacher')->insert([
            'class_id' => $this->class2->id,
            'subject_id' => $this->subject2->id,
            'teacher_id' => $this->teacherB->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign student1 to class1
        DB::table('class_students')->insert([
            'class_id' => $this->class1->id,
            'student_id' => $this->student1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign studentOther to class2
        DB::table('class_students')->insert([
            'class_id' => $this->class2->id,
            'student_id' => $this->studentOther->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** TC01: Teacher được phân công class + subject -> tạo category -> SUCCESS */
    public function test_tc01_assigned_teacher_can_create_category(): void
    {
        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.categories.store', [$this->class1, $this->subject1]),
            [
                'name' => 'Chương 1: Mở đầu',
                'description' => 'Tài liệu chương 1',
                'sort_order' => 1,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('document_categories', [
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1: Mở đầu',
        ]);
    }

    /** TC02: Teacher không được phân công -> tạo category -> DENY */
    public function test_tc02_unassigned_teacher_cannot_create_category(): void
    {
        $response = $this->actingAs($this->teacherB)->post(
            route('teacher.classes.subjects.categories.store', [$this->class1, $this->subject1]),
            ['name' => 'Hack Category']
        );

        $response->assertStatus(403);
    }

    /** TC03: Teacher được phân công -> upload PDF -> SUCCESS */
    public function test_tc03_assigned_teacher_can_upload_pdf(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        $file = UploadedFile::fake()->create('slide.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.documents.store', [$this->class1, $this->subject1]),
            [
                'category_id' => $category->id,
                'title' => 'Slide chương 1',
                'file' => $file,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'category_id' => $category->id,
            'title' => 'Slide chương 1',
            'file_type' => 'pdf',
        ]);
    }

    /** TC04: Teacher được phân công -> upload DOCX -> SUCCESS */
    public function test_tc04_assigned_teacher_can_upload_docx(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        $file = UploadedFile::fake()->create('baitap.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.documents.store', [$this->class1, $this->subject1]),
            [
                'category_id' => $category->id,
                'title' => 'Bài tập chương 1',
                'file' => $file,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'category_id' => $category->id,
            'title' => 'Bài tập chương 1',
            'file_type' => 'docx',
        ]);
    }

    /** TC05: Teacher được phân công -> upload ZIP -> SUCCESS */
    public function test_tc05_assigned_teacher_can_upload_zip(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        $file = UploadedFile::fake()->create('code_mau.zip', 5000, 'application/zip');

        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.documents.store', [$this->class1, $this->subject1]),
            [
                'category_id' => $category->id,
                'title' => 'Code mẫu thực hành',
                'file' => $file,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'category_id' => $category->id,
            'title' => 'Code mẫu thực hành',
            'file_type' => 'zip',
        ]);
    }

    /** TC06: Upload EXE -> REJECT */
    public function test_tc06_upload_exe_rejected(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        $file = UploadedFile::fake()->create('virus.exe', 100);

        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.documents.store', [$this->class1, $this->subject1]),
            [
                'category_id' => $category->id,
                'title' => 'Malware',
                'file' => $file,
            ]
        );

        $response->assertStatus(422);
    }

    /** TC07: Upload file vượt giới hạn -> REJECT */
    public function test_tc07_upload_oversized_file_rejected(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        // PDF over 20MB (25MB = 25600 KB)
        $file = UploadedFile::fake()->create('large.pdf', 25600, 'application/pdf');

        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.documents.store', [$this->class1, $this->subject1]),
            [
                'category_id' => $category->id,
                'title' => 'Large file',
                'file' => $file,
            ]
        );

        $response->assertStatus(422);
    }

    /** TC08 & TC09: Teacher cố upload vào category của lớp khác -> DENY */
    public function test_tc08_tc09_teacher_cannot_upload_to_another_class_category(): void
    {
        // Category in class2
        $categoryClass2 = DocumentCategory::create([
            'class_id' => $this->class2->id,
            'subject_id' => $this->subject2->id,
            'teacher_id' => $this->teacherB->id,
            'name' => 'Chương 1 Lớp 2',
        ]);

        $file = UploadedFile::fake()->create('slide.pdf', 1024, 'application/pdf');

        // Teacher A attempts to post to class1 but passes category of class2
        $response = $this->actingAs($this->teacherA)->post(
            route('teacher.classes.subjects.documents.store', [$this->class1, $this->subject1]),
            [
                'category_id' => $categoryClass2->id,
                'title' => 'Slide',
                'file' => $file,
            ]
        );

        $response->assertStatus(404);
    }

    /** TC10: Teacher A cố xóa tài liệu của Teacher B -> DENY */
    public function test_tc10_teacher_cannot_delete_another_teachers_document(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class2->id,
            'subject_id' => $this->subject2->id,
            'teacher_id' => $this->teacherB->id,
            'name' => 'Category B',
        ]);

        $document = Document::create([
            'category_id' => $category->id,
            'class_id' => $this->class2->id,
            'subject_id' => $this->subject2->id,
            'teacher_id' => $this->teacherB->id,
            'title' => 'Doc B',
            'original_filename' => 'doc.pdf',
            'file_path' => 'documents/test.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->teacherA)->delete(
            route('teacher.documents.destroy', $document)
        );

        $response->assertStatus(403);
    }

    /** TC11: Student thuộc class -> xem tài liệu -> SUCCESS */
    public function test_tc11_student_in_class_can_view_documents(): void
    {
        $response = $this->actingAs($this->student1)->get(
            route('student.classes.subjects.documents.index', [$this->class1, $this->subject1])
        );

        $response->assertStatus(200);
        $response->assertSee('Tài liệu học tập');
    }

    /** TC12: Student không thuộc class -> xem tài liệu -> DENY */
    public function test_tc12_student_not_in_class_cannot_view_documents(): void
    {
        $response = $this->actingAs($this->studentOther)->get(
            route('student.classes.subjects.documents.index', [$this->class1, $this->subject1])
        );

        $response->assertStatus(403);
    }

    /** TC13: Student xem PDF trực tuyến -> SUCCESS */
    public function test_tc13_student_can_view_pdf_inline(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        Storage::disk('local')->put('documents/test.pdf', '%PDF-1.4 test file');

        $document = Document::create([
            'category_id' => $category->id,
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'title' => 'Slide C++',
            'original_filename' => 'slide.pdf',
            'file_path' => 'documents/test.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->student1)->get(
            route('student.documents.view', $document)
        );

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** TC14, TC15, TC16: Student download PDF, DOCX, ZIP -> SUCCESS */
    public function test_tc14_15_16_student_can_download_documents(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        Storage::disk('local')->put('documents/code.zip', 'PK ZIP content');

        $document = Document::create([
            'category_id' => $category->id,
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'title' => 'Code ZIP',
            'original_filename' => 'code.zip',
            'file_path' => 'documents/code.zip',
            'file_type' => 'zip',
            'file_size' => 2048,
        ]);

        $response = $this->actingAs($this->student1)->get(
            route('student.documents.download', $document)
        );

        $response->assertStatus(200);
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    /** TC19: Student cố tải tài liệu của lớp khác -> DENY */
    public function test_tc19_student_cannot_download_document_from_other_class(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class2->id,
            'subject_id' => $this->subject2->id,
            'teacher_id' => $this->teacherB->id,
            'name' => 'Chương 1 Lớp 2',
        ]);

        Storage::disk('local')->put('documents/secret.pdf', '%PDF secret');

        $document = Document::create([
            'category_id' => $category->id,
            'class_id' => $this->class2->id,
            'subject_id' => $this->subject2->id,
            'teacher_id' => $this->teacherB->id,
            'title' => 'Secret Doc',
            'original_filename' => 'secret.pdf',
            'file_path' => 'documents/secret.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->student1)->get(
            route('student.documents.download', $document)
        );

        $response->assertStatus(403);
    }

    /** TC20: File database tồn tại nhưng storage không tồn tại -> 404 graceful */
    public function test_tc20_missing_storage_file_returns_graceful_404(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        $document = Document::create([
            'category_id' => $category->id,
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'title' => 'Missing File',
            'original_filename' => 'missing.pdf',
            'file_path' => 'documents/nonexistent.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->student1)->get(
            route('student.documents.download', $document)
        );

        $response->assertStatus(404);
    }

    /** Cannot delete category if documents still exist */
    public function test_cannot_delete_category_with_documents(): void
    {
        $category = DocumentCategory::create([
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'name' => 'Chương 1',
        ]);

        Document::create([
            'category_id' => $category->id,
            'class_id' => $this->class1->id,
            'subject_id' => $this->subject1->id,
            'teacher_id' => $this->teacherA->id,
            'title' => 'Doc 1',
            'original_filename' => 'doc.pdf',
            'file_path' => 'documents/doc.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->teacherA)->delete(
            route('teacher.document-categories.destroy', $category)
        );

        $response->assertSessionHasErrors('category');
        $this->assertDatabaseHas('document_categories', ['id' => $category->id]);
    }
}
