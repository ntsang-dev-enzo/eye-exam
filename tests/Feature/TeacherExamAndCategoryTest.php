<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeacherExamAndCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_exam_with_category_and_delete_exam()
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $subject = Subject::create([
            'code' => 'TEST101',
            'name' => 'Test Subject',
            'status' => true,
        ]);

        $teacher->subjects()->attach($subject->id);

        $category = Category::create([
            'name' => 'Chương 1: Test Category',
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
        ]);

        $question = Question::create([
            'subject_id' => $subject->id,
            'category_id' => $category->id,
            'created_by' => $teacher->id,
            'content' => 'Sample test question?',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);

        Answer::create([
            'question_id' => $question->id,
            'label' => 'A',
            'content' => 'Correct Answer',
            'is_correct' => true,
        ]);

        // 1. Teacher stores exam
        $response = $this->actingAs($teacher)->post(route('teacher.exams.store'), [
            'subject_id' => $subject->id,
            'category_id' => $category->id,
            'title' => 'Đề thi thử nghiệm',
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'start_at' => now()->format('Y-m-d H:i:s'),
            'end_at' => now()->addMinutes(60)->format('Y-m-d H:i:s'),
            'questions' => [$question->id],
            'points' => [$question->id => 10],
        ]);

        $response->assertRedirect(route('teacher.exams.index'));
        $this->assertDatabaseHas('exams', [
            'title' => 'Đề thi thử nghiệm',
            'category_id' => $category->id,
            'duration_minutes' => 60,
        ]);

        $exam = Exam::where('title', 'Đề thi thử nghiệm')->first();

        // 2. Teacher deletes exam
        $delResponse = $this->actingAs($teacher)->delete(route('teacher.exams.destroy', $exam));
        $delResponse->assertRedirect(route('teacher.exams.index'));
        $this->assertDatabaseMissing('exams', [
            'id' => $exam->id,
        ]);
    }

    public function test_teacher_can_create_and_filter_questions_by_category()
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $subject = Subject::create([
            'code' => 'TEST102',
            'name' => 'Test Subject 2',
            'status' => true,
        ]);

        $teacher->subjects()->attach($subject->id);

        $response = $this->actingAs($teacher)->post(route('teacher.questions.store'), [
            'subject_id' => $subject->id,
            'new_category_name' => 'Chương mới tạo inline',
            'content' => 'Nội dung câu hỏi tạo mới kèm danh mục inline?',
            'type' => 'multiple_choice',
            'difficulty' => 'medium',
            'answers' => ['Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D'],
            'correct_answer' => 0,
        ]);

        $response->assertRedirect(route('teacher.questions.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Chương mới tạo inline',
            'subject_id' => $subject->id,
        ]);

        $this->assertDatabaseHas('questions', [
            'content' => 'Nội dung câu hỏi tạo mới kèm danh mục inline?',
            'created_by' => $teacher->id,
        ]);
    }

    public function test_teacher_can_manage_subject_categories_and_bulk_assign_questions()
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $subject = Subject::create([
            'code' => 'TEST103',
            'name' => 'Test Subject 3',
            'status' => true,
        ]);

        $teacher->subjects()->attach($subject->id);

        // 1. Create Category via CategoryController
        $catResponse = $this->actingAs($teacher)->post(route('teacher.categories.store'), [
            'name' => 'Chương 1: Mở đầu',
            'subject_id' => $subject->id,
            'description' => 'Mô tả chương 1',
        ]);

        $catResponse->assertRedirect(route('teacher.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Chương 1: Mở đầu',
            'subject_id' => $subject->id,
        ]);

        $category = Category::where('name', 'Chương 1: Mở đầu')->first();

        // 2. Create questions without category
        $q1 = Question::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'content' => 'Bulk test question 1',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);

        $q2 = Question::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'content' => 'Bulk test question 2',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);

        // 3. Bulk assign category to questions
        $bulkResponse = $this->actingAs($teacher)->post(route('teacher.questions.bulk-category'), [
            'question_ids' => [$q1->id, $q2->id],
            'category_id' => $category->id,
        ]);

        $bulkResponse->assertSessionHas('success');
        $this->assertEquals($category->id, $q1->fresh()->category_id);
        $this->assertEquals($category->id, $q2->fresh()->category_id);

        // 4. Update category
        $updateResponse = $this->actingAs($teacher)->put(route('teacher.categories.update', $category), [
            'name' => 'Chương 1: Đã đổi tên',
            'subject_id' => $subject->id,
            'description' => 'Mô tả mới',
        ]);

        $updateResponse->assertRedirect(route('teacher.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Chương 1: Đã đổi tên',
        ]);

        // 5. Delete category
        $deleteResponse = $this->actingAs($teacher)->delete(route('teacher.categories.destroy', $category));
        $deleteResponse->assertRedirect(route('teacher.categories.index'));
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
        // Check that questions still exist but category_id is now null
        $this->assertNull($q1->fresh()->category_id);
    }

    public function test_student_cheat_logging_and_teacher_behavior_monitoring()
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $subject = Subject::create(['code' => 'TEST104', 'name' => 'Test Subject 4', 'status' => true]);
        $teacher->subjects()->attach($subject->id);

        $exam = Exam::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'code' => 'CHEAT01',
            'title' => 'Đề thi giám sát vi phạm',
            'duration_minutes' => 45,
            'total_questions' => 1,
            'status' => 'published',
        ]);

        $attempt = \App\Models\ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'cheat_warnings' => 0,
            'out_of_screen_time' => 0,
        ]);

        // 1. Student triggers anti-cheat events
        $this->actingAs($student)->postJson(route('student.exams.cheat', $attempt->id), [
            'event_type' => 'fullscreen_exit',
            'event_data' => ['screen_state' => 'windowed'],
        ])->assertJson(['success' => true]);

        $this->actingAs($student)->postJson(route('student.exams.cheat', $attempt->id), [
            'event_type' => 'tab_switch',
            'duration_seconds' => 12,
        ])->assertJson(['success' => true]);

        $this->actingAs($student)->postJson(route('student.exams.cheat', $attempt->id), [
            'event_type' => 'copy',
            'event_data' => ['action' => 'copy_text'],
        ])->assertJson(['success' => true]);

        $this->actingAs($student)->postJson(route('student.exams.cheat', $attempt->id), [
            'event_type' => 'paste',
            'event_data' => ['action' => 'paste_text'],
        ])->assertJson(['success' => true]);

        $this->actingAs($student)->postJson(route('student.exams.cheat', $attempt->id), [
            'event_type' => 'right_click',
            'event_data' => ['action' => 'context_menu'],
        ])->assertJson(['success' => true]);

        $attempt->refresh();
        $this->assertEquals(5, $attempt->cheat_warnings);
        $this->assertEquals(12, $attempt->out_of_screen_time);
        $this->assertDatabaseCount('anti_cheat_logs', 5);

        // 2. Teacher monitors live API
        $monitorResponse = $this->actingAs($teacher)->getJson(route('teacher.exams.api-monitor', $exam));
        $monitorResponse->assertStatus(200);
        $monitorResponse->assertJsonStructure(['attempts' => [['id', 'student_name', 'out_of_screen_time', 'cheat_warnings', 'last_event']]]);

        // 3. Teacher fetches full student behavior timeline
        $behaviorResponse = $this->actingAs($teacher)->getJson(route('teacher.exams.student-behavior', [$exam, $attempt]));
        $behaviorResponse->assertStatus(200);
        $behaviorResponse->assertJsonStructure([
            'student' => ['id', 'name', 'code', 'email'],
            'attempt' => ['id', 'status', 'cheat_warnings', 'out_of_screen_time'],
            'stats' => ['total_logs', 'cheat_warnings', 'out_of_screen_time', 'fullscreen_exits', 'tab_switches', 'copy_pastes', 'right_clicks'],
            'logs' => [['id', 'event_type', 'title', 'badge', 'severity']],
        ]);

        $this->assertEquals(5, $behaviorResponse->json('stats.total_logs'));
        $this->assertEquals(1, $behaviorResponse->json('stats.fullscreen_exits'));
        $this->assertEquals(1, $behaviorResponse->json('stats.tab_switches'));
        $this->assertEquals(2, $behaviorResponse->json('stats.copy_pastes'));
        $this->assertEquals(1, $behaviorResponse->json('stats.right_clicks'));
    }

    public function test_realtime_answer_saving_and_offline_sync()
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $subject = Subject::create(['code' => 'TEST105', 'name' => 'Test Subject 5', 'status' => true]);
        $teacher->subjects()->attach($subject->id);

        $exam = Exam::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'code' => 'AUTOSAVE1',
            'title' => 'Đề thi Auto-save & Offline Sync',
            'duration_minutes' => 60,
            'total_questions' => 2,
            'status' => 'published',
        ]);

        $q1 = Question::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'content' => 'Câu 1?',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);
        $a1 = Answer::create(['question_id' => $q1->id, 'label' => 'A', 'content' => 'Đáp án đúng Q1', 'is_correct' => true]);
        $a2 = Answer::create(['question_id' => $q1->id, 'label' => 'B', 'content' => 'Đáp án sai Q1', 'is_correct' => false]);

        $q2 = Question::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'content' => 'Câu 2?',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);
        $a3 = Answer::create(['question_id' => $q2->id, 'label' => 'A', 'content' => 'Đáp án đúng Q2', 'is_correct' => true]);

        $exam->questions()->attach($q1->id, ['question_order' => 1, 'points' => 5]);
        $exam->questions()->attach($q2->id, ['question_order' => 2, 'points' => 5]);

        $attempt = \App\Models\ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'cheat_warnings' => 0,
            'out_of_screen_time' => 0,
        ]);

        // 1. Test Single Real-time Answer Save
        $saveResp = $this->actingAs($student)->postJson(route('student.exams.save-answer', $attempt->id), [
            'question_id' => $q1->id,
            'answer_id' => $a1->id,
        ]);
        $saveResp->assertStatus(200);
        $saveResp->assertJson(['success' => true]);

        $this->assertDatabaseHas('exam_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'answer_id' => $a1->id,
            'is_correct' => true,
            'points_earned' => 5,
        ]);

        // 2. Test Offline Sync with answer and logs
        $syncResp = $this->actingAs($student)->postJson(route('student.exams.sync-offline', $attempt->id), [
            'answers' => [
                ['question_id' => $q2->id, 'answer_id' => $a3->id],
            ],
            'logs' => [
                ['event_type' => 'tab_switch', 'duration_seconds' => 5],
                ['event_type' => 'right_click'],
            ],
        ]);

        $syncResp->assertStatus(200);
        $syncResp->assertJson(['success' => true, 'synced_answers' => 1, 'synced_logs' => 2]);

        $this->assertDatabaseHas('exam_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $q2->id,
            'answer_id' => $a3->id,
            'is_correct' => true,
            'points_earned' => 5,
        ]);

        $attempt->refresh();
        $this->assertEquals(2, $attempt->cheat_warnings);
        $this->assertEquals(5, $attempt->out_of_screen_time);

        // 3. Test Submit Exam (which should calculate score from all pre-saved answers)
        $submitResp = $this->actingAs($student)->post(route('student.exams.submit', $exam), []);
        $submitResp->assertRedirect(route('student.dashboard'));

        $attempt->refresh();
        $this->assertEquals('submitted', $attempt->status);
        $this->assertEquals(10, $attempt->score_value);
        $this->assertEquals(2, $attempt->correct_answers);
        $this->assertEquals(0, $attempt->wrong_answers);
        $this->assertEquals(0, $attempt->unanswered);
    }

    public function test_teacher_can_configure_anti_cheat_rules_and_unlimited_attempts()
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $subject = Subject::create(['code' => 'TEST106', 'name' => 'Test Subject 6', 'status' => true]);
        $teacher->subjects()->attach($subject->id);

        $q1 = Question::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'content' => 'Câu hỏi tùy chọn chống gian lận?',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);
        Answer::create(['question_id' => $q1->id, 'label' => 'A', 'content' => 'Đúng', 'is_correct' => true]);

        // 1. Teacher creates an exam with unlimited attempts and selective anti-cheat options
        $createResp = $this->actingAs($teacher)->post(route('teacher.exams.store'), [
            'subject_id' => $subject->id,
            'title' => 'Đề thi Vô Hạn Lần & Tùy Chọn Bảo Vệ',
            'duration_minutes' => 30,
            'unlimited_attempts' => '1',
            'enable_anti_cheat' => '1',
            'require_fullscreen' => '1',
            'prevent_tab_switch' => '1',
            // Turn OFF copy/paste and right click restrictions
            'questions' => [$q1->id],
            'points' => [$q1->id => 10],
        ]);
        $createResp->assertRedirect(route('teacher.exams.index'));

        $exam = Exam::where('title', 'Đề thi Vô Hạn Lần & Tùy Chọn Bảo Vệ')->first();
        $this->assertNotNull($exam);
        $this->assertEquals(0, $exam->max_attempts);
        $this->assertTrue($exam->isUnlimitedAttempts());
        $this->assertTrue($exam->enable_anti_cheat);
        $this->assertTrue($exam->require_fullscreen);
        $this->assertTrue($exam->prevent_tab_switch);
        $this->assertFalse($exam->prevent_copy_paste);
        $this->assertFalse($exam->prevent_right_click);

        // 2. Open the exam
        $exam->update(['status' => 'published']);

        // 3. Student takes the exam multiple times without attempt limitation
        // Attempt 1:
        $joinResp1 = $this->actingAs($student)->post(route('student.exams.join'), ['code' => $exam->code]);
        $joinResp1->assertRedirect(route('student.exams.take', $exam));

        $attempt1 = \App\Models\ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->latest('id')->first();
        $this->assertNotNull($attempt1);

        // 3a. Submitting with empty answers should be blocked and redirected back to take
        $emptySubmitResp = $this->actingAs($student)->post(route('student.exams.submit', $exam), []);
        $emptySubmitResp->assertRedirect(route('student.exams.take', $exam));
        $emptySubmitResp->assertSessionHas('error');

        // 3b. Answer the question and submit successfully
        $this->actingAs($student)->postJson(route('student.exams.save-answer', $attempt1->id), [
            'question_id' => $q1->id,
            'answer_id' => $q1->answers->first()->id,
        ]);

        $submitResp1 = $this->actingAs($student)->post(route('student.exams.submit', $exam), []);
        $submitResp1->assertRedirect(route('student.dashboard'));
        $this->assertEquals('submitted', $attempt1->fresh()->status);

        // Attempt 2 (allowed because it's unlimited):
        $joinResp2 = $this->actingAs($student)->post(route('student.exams.join'), ['code' => $exam->code]);
        $joinResp2->assertRedirect(route('student.exams.take', $exam));

        $attempt2 = \App\Models\ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->latest('id')->first();
        $this->assertNotEquals($attempt1->id, $attempt2->id);
    }

    public function test_student_can_clear_answer_and_view_categorized_dashboard()
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $subject = Subject::create(['code' => 'TEST107', 'name' => 'Test Subject 7', 'status' => true]);
        $category = Category::create(['subject_id' => $subject->id, 'created_by' => $teacher->id, 'name' => 'Chương 1: Cơ bản']);
        $teacher->subjects()->attach($subject->id);

        $q1 = Question::create([
            'subject_id' => $subject->id,
            'category_id' => $category->id,
            'created_by' => $teacher->id,
            'content' => 'Câu hỏi test clear?',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'status' => true,
        ]);
        $a1 = Answer::create(['question_id' => $q1->id, 'label' => 'A', 'content' => 'Đáp án A', 'is_correct' => true]);

        $exam = Exam::create([
            'subject_id' => $subject->id,
            'category_id' => $category->id,
            'created_by' => $teacher->id,
            'code' => 'CLREXM01',
            'title' => 'Đề thi kiểm tra nút Clear',
            'duration_minutes' => 30,
            'total_questions' => 1,
            'max_attempts' => 1,
            'status' => 'published',
            'allow_review' => true,
            'require_face_verification' => false,
        ]);
        $exam->questions()->attach($q1->id, ['question_order' => 1, 'points' => 10]);

        // Student joins exam
        $this->actingAs($student)->post(route('student.exams.join'), ['code' => $exam->code]);
        $attempt = \App\Models\ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->first();

        // 1. Select answer
        $saveResp = $this->actingAs($student)->postJson(route('student.exams.save-answer', $attempt->id), [
            'question_id' => $q1->id,
            'answer_id' => $a1->id,
        ]);
        $saveResp->assertStatus(200);
        $this->assertDatabaseHas('exam_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'answer_id' => $a1->id,
        ]);

        // 2. Clear answer (answer_id = null)
        $clearResp = $this->actingAs($student)->postJson(route('student.exams.save-answer', $attempt->id), [
            'question_id' => $q1->id,
            'answer_id' => null,
        ]);
        $clearResp->assertStatus(200);
        $clearResp->assertJson(['success' => true, 'cleared' => true]);
        $this->assertDatabaseMissing('exam_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
        ]);

        // 3. Select answer again and submit
        $this->actingAs($student)->postJson(route('student.exams.save-answer', $attempt->id), [
            'question_id' => $q1->id,
            'answer_id' => $a1->id,
        ]);
        $this->actingAs($student)->post(route('student.exams.submit', $exam), []);

        // 4. Test Student Dashboard view with categorized stats
        \App\Models\ExamAssignment::create(['exam_id' => $exam->id, 'student_id' => $student->id, 'assigned_by' => $teacher->id]);

        $dashResp = $this->actingAs($student)->get(route('student.dashboard'));
        $dashResp->assertStatus(200);
        $dashResp->assertSee('Chương 1: Cơ bản');
        $dashResp->assertSee('Đề thi kiểm tra nút Clear');
        $dashResp->assertSee('10 đ');
    }
}
