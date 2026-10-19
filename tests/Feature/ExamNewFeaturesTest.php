<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\SchoolClass;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamNewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private $teacher;
    private $student;
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create([
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'face_registered' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'TEST999',
            'name' => 'Môn Kiểm Thử',
            'status' => true,
        ]);

        $this->teacher->subjects()->attach($this->subject->id);
    }

    /**
     * 1. Teacher can create exam with semester & allow_view_score
     */
    public function test_teacher_can_create_exam_with_semester_and_allow_view_score()
    {
        $question = Question::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'content' => 'Câu trắc nghiệm 1',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);

        $answer = Answer::create([
            'question_id' => $question->id,
            'label' => 'A',
            'content' => 'Đáp án A',
            'is_correct' => true,
        ]);

        $response = $this->actingAs($this->teacher)->post(route('teacher.exams.store'), [
            'subject_id' => $this->subject->id,
            'title' => 'Đề thi Học kỳ 1 Khóa 2026',
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'semester' => 'Học kỳ 1',
            'allow_view_score' => '1',
            'allow_review' => '1',
            'questions' => [$question->id],
            'points' => [$question->id => 10],
        ]);

        $response->assertRedirect(route('teacher.exams.index'));

        $this->assertDatabaseHas('exams', [
            'title' => 'Đề thi Học kỳ 1 Khóa 2026',
            'semester' => 'Học kỳ 1',
            'allow_view_score' => 1,
        ]);
    }

    /**
     * 2. Student semester filtering
     */
    public function test_student_can_filter_exams_by_semester()
    {
        $examHk1 = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Kỳ thi Học kỳ 1',
            'code' => 'EX_HK1',
            'duration_minutes' => 45,
            'total_questions' => 1,
            'max_attempts' => 1,
            'pass_score' => 5,
            'is_published' => true,
            'status' => 'published',
            'semester' => 'Học kỳ 1',
        ]);

        $examHk2 = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Kỳ thi Học kỳ 2',
            'code' => 'EX_HK2',
            'duration_minutes' => 45,
            'total_questions' => 1,
            'max_attempts' => 1,
            'pass_score' => 5,
            'is_published' => true,
            'status' => 'published',
            'semester' => 'Học kỳ 2',
        ]);

        // Assign both to student
        ExamAssignment::create([
            'exam_id' => $examHk1->id,
            'assigned_by' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        ExamAssignment::create([
            'exam_id' => $examHk2->id,
            'assigned_by' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        // Filter semester 1
        $resHk1 = $this->actingAs($this->student)->get(route('student.exams.index', ['semester' => 'Học kỳ 1']));
        $resHk1->assertStatus(200);
        $resHk1->assertSee('Kỳ thi Học kỳ 1');
        $resHk1->assertDontSee('Kỳ thi Học kỳ 2');

        // Filter semester 2
        $resHk2 = $this->actingAs($this->student)->get(route('student.exams.index', ['semester' => 'Học kỳ 2']));
        $resHk2->assertStatus(200);
        $resHk2->assertSee('Kỳ thi Học kỳ 2');
        $resHk2->assertDontSee('Kỳ thi Học kỳ 1');
    }

    /**
     * 3. Score visibility controlled by teacher
     */
    public function test_student_cannot_see_score_when_allow_view_score_is_false()
    {
        $exam = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Kỳ thi Giấu Điểm',
            'code' => 'EX_HIDE',
            'duration_minutes' => 30,
            'total_questions' => 1,
            'max_attempts' => 1,
            'pass_score' => 5,
            'is_published' => true,
            'status' => 'published',
            'allow_view_score' => false,
            'allow_review' => true,
        ]);

        ExamAssignment::create([
            'exam_id' => $exam->id,
            'assigned_by' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now()->subMinutes(5),
            'status' => 'submitted',
            'score_value' => 8.5,
            'correct_answers' => 1,
            'wrong_answers' => 0,
            'unanswered' => 0,
        ]);

        // Student exam list
        $indexRes = $this->actingAs($this->student)->get(route('student.exams.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Chờ công bố điểm');
        $indexRes->assertDontSee('8.5 đ');

        // Student review page
        $reviewRes = $this->actingAs($this->student)->get(route('student.exams.review', $exam));
        $reviewRes->assertStatus(200);
        $reviewRes->assertSee('Giảng viên chưa công bố điểm');
        $reviewRes->assertDontSee('8.5 / 10');
    }

    /**
     * 4. Multi-class SOLE assignment (Sole 1-2, 1-2-3, 1-2-3-4)
     */
    public function test_teacher_can_assign_classes_with_sole_distribution()
    {
        $class1 = SchoolClass::create(['name' => 'Lớp 12A1', 'code' => '12A1', 'teacher_id' => $this->teacher->id]);
        $class2 = SchoolClass::create(['name' => 'Lớp 12A2', 'code' => '12A2', 'teacher_id' => $this->teacher->id]);

        $s1 = User::factory()->create(['role' => 'student']);
        $s2 = User::factory()->create(['role' => 'student']);
        $s3 = User::factory()->create(['role' => 'student']);
        $s4 = User::factory()->create(['role' => 'student']);

        $class1->students()->attach([$s1->id, $s2->id]);
        $class2->students()->attach([$s3->id, $s4->id]);

        $examA = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Đề A (Mã 001)',
            'code' => 'EX_A',
            'duration_minutes' => 30,
            'total_questions' => 1,
            'is_published' => true,
            'status' => 'published',
        ]);

        $examB = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Đề B (Mã 002)',
            'code' => 'EX_B',
            'duration_minutes' => 30,
            'total_questions' => 1,
            'is_published' => true,
            'status' => 'published',
        ]);

        // Assign with distribution mode: sole_1_2
        $response = $this->actingAs($this->teacher)->post(route('teacher.assignments.store'), [
            'class_ids' => [$class1->id, $class2->id],
            'exams' => [$examA->id, $examB->id],
            'assign_type' => 'sole_1_2',
            'students' => [$s1->id, $s2->id, $s3->id, $s4->id],
        ]);

        $response->assertRedirect(route('teacher.exams.index'));

        // Check alternating assignment in ExamAssignment
        $examAStudentIds = ExamAssignment::where('exam_id', $examA->id)->pluck('student_id')->toArray();
        $examBStudentIds = ExamAssignment::where('exam_id', $examB->id)->pluck('student_id')->toArray();

        // 2 students in Exam A, 2 students in Exam B
        $this->assertCount(2, $examAStudentIds);
        $this->assertCount(2, $examBStudentIds);
        $this->assertEmpty(array_intersect($examAStudentIds, $examBStudentIds));
    }

    /**
     * 5. Essay question submission without SQL error
     */
    public function test_student_can_answer_and_submit_essay_question()
    {
        $essayQuestion = Question::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'content' => 'Hãy phân tích giải thuật sắp xếp nhanh QuickSort.',
            'type' => 'essay',
            'difficulty' => 'medium',
            'status' => true,
        ]);

        $exam = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Thi Tự Luận Thuật Toán',
            'code' => 'EX_ESSAY',
            'duration_minutes' => 60,
            'total_questions' => 1,
            'max_attempts' => 1,
            'pass_score' => 5,
            'is_published' => true,
            'status' => 'published',
            'allow_review' => true,
        ]);

        $exam->questions()->attach($essayQuestion->id, ['points' => 10, 'question_order' => 1]);

        ExamAssignment::create([
            'exam_id' => $exam->id,
            'assigned_by' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        // 1. Save essay answer via AJAX
        $saveRes = $this->actingAs($this->student)->postJson(route('student.exams.save-answer', $exam), [
            'attempt_id' => $attempt->id,
            'question_id' => $essayQuestion->id,
            'answer_id' => null,
            'text_answer' => 'QuickSort sử dụng phương pháp chia để trị với pivot.',
        ]);

        $saveRes->assertStatus(200);
        $saveRes->assertJson(['success' => true]);

        $this->assertDatabaseHas('exam_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $essayQuestion->id,
            'text_answer' => 'QuickSort sử dụng phương pháp chia để trị với pivot.',
        ]);

        // 2. Submit exam with essay answer in form
        $submitRes = $this->actingAs($this->student)->post(route('student.exams.submit', $exam), [
            'attempt_id' => $attempt->id,
            'answers' => [
                $essayQuestion->id => 'QuickSort hoạt động bằng cách chọn pivot và phân hoạch mảng.',
            ],
        ]);

        $submitRes->assertRedirect(route('student.dashboard'));

        $attempt->refresh();
        $this->assertEquals('submitted', $attempt->status);
        $this->assertEquals(0, $attempt->unanswered);

        $this->assertDatabaseHas('exam_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $essayQuestion->id,
            'text_answer' => 'QuickSort hoạt động bằng cách chọn pivot và phân hoạch mảng.',
        ]);
    }

    /**
     * 6. Student can review multiple attempts
     */
    public function test_student_can_review_any_attempt_when_allow_review_is_enabled()
    {
        $exam = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Kỳ thi Xem lại Đa Lần',
            'code' => 'EX_MULTI',
            'duration_minutes' => 30,
            'total_questions' => 1,
            'max_attempts' => 3,
            'pass_score' => 5,
            'is_published' => true,
            'status' => 'published',
            'allow_review' => true,
            'allow_view_score' => true,
        ]);

        ExamAssignment::create([
            'exam_id' => $exam->id,
            'assigned_by' => $this->teacher->id,
            'student_id' => $this->student->id,
            'assigned_at' => now(),
        ]);

        $att1 = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now()->subMinutes(60),
            'submitted_at' => now()->subMinutes(40),
            'status' => 'submitted',
            'score_value' => 6.0,
            'correct_answers' => 1,
            'wrong_answers' => 0,
            'unanswered' => 0,
        ]);

        $att2 = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'attempt_number' => 2,
            'started_at' => now()->subMinutes(30),
            'submitted_at' => now()->subMinutes(10),
            'status' => 'submitted',
            'score_value' => 9.0,
            'correct_answers' => 1,
            'wrong_answers' => 0,
            'unanswered' => 0,
        ]);

        // Review attempt 1
        $res1 = $this->actingAs($this->student)->get(route('student.exams.review', ['exam' => $exam->id, 'attempt_id' => $att1->id]));
        $res1->assertStatus(200);
        $res1->assertSee('6 / 10');

        // Review attempt 2
        $res2 = $this->actingAs($this->student)->get(route('student.exams.review', ['exam' => $exam->id, 'attempt_id' => $att2->id]));
        $res2->assertStatus(200);
        $res2->assertSee('9 / 10');
    }

    /**
     * 7. Teacher results sort by submitted_at desc
     */
    public function test_teacher_results_sorts_submitted_students_newest_first()
    {
        $s1 = User::factory()->create(['role' => 'student', 'name' => 'Nguyễn Văn A']);
        $s2 = User::factory()->create(['role' => 'student', 'name' => 'Trần Thị B']);

        $exam = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'title' => 'Đề thi Đối Soát Thứ Tự Nộp',
            'code' => 'EX_SORT',
            'duration_minutes' => 30,
            'total_questions' => 1,
            'is_published' => true,
            'status' => 'published',
        ]);

        // S1 submitted 30 minutes ago
        $attS1 = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $s1->id,
            'attempt_number' => 1,
            'started_at' => now()->subMinutes(50),
            'submitted_at' => now()->subMinutes(30),
            'status' => 'submitted',
            'score_value' => 7.0,
        ]);

        // S2 submitted 5 minutes ago (newer)
        $attS2 = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $s2->id,
            'attempt_number' => 1,
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now()->subMinutes(5),
            'status' => 'submitted',
            'score_value' => 8.0,
        ]);

        $response = $this->actingAs($this->teacher)->get(route('teacher.exams.results', $exam));
        $response->assertStatus(200);

        // Verify S2 is ordered before S1 in the attempts list passed to the view
        $attempts = $response->viewData('attempts');
        $this->assertEquals($attS2->id, $attempts->first()->id);
        $this->assertEquals($attS1->id, $attempts->last()->id);
    }
}
