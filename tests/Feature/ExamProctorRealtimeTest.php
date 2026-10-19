<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\AntiCheatLog;
use App\Models\ExamProctorSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamProctorRealtimeTest extends TestCase
{
    use RefreshDatabase;

    private $teacher;
    private $student;
    private $subject;
    private $exam;
    private $attempt;

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

        $this->exam = Exam::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->teacher->id,
            'code' => 'EXAM_TEST_PROC',
            'title' => 'Đề thi kiểm tra Realtime Proctoring',
            'duration_minutes' => 60,
            'total_questions' => 1,
            'max_attempts' => 1,
            'pass_score' => 5,
            'is_published' => true,
            'status' => 'published',
            'enable_proctor_camera' => true,
            'require_face_verification' => false,
        ]);

        $this->attempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);
    }

    /**
     * 1. Test proctor snapshot with client-side YOLO phone detection
     */
    public function test_proctor_snapshot_with_client_phone_detection()
    {
        $dummyImage = 'data:image/jpeg;base64,' . base64_encode('fake-image-content');

        $response = $this->actingAs($this->student)->postJson(
            route('student.exams.proctor-snapshot', $this->attempt->id),
            [
                'image' => $dummyImage,
                'trigger_source' => 'browser_realtime_detection',
                'anomaly_type' => 'phone_detected',
                'face_count' => 1,
                'details' => 'Phát hiện điện thoại di động trước camera (88%)',
                'detections' => [
                    [
                        'label' => 'cell phone',
                        'confidence' => 88.0,
                        'box' => [100, 100, 200, 300],
                    ],
                ],
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'violation',
        ]);

        // Check snapshot record
        $this->assertDatabaseHas('exam_proctor_snapshots', [
            'attempt_id' => $this->attempt->id,
            'student_id' => $this->student->id,
            'status' => 'violation',
        ]);

        // Check AntiCheatLog record
        $this->assertDatabaseHas('anti_cheat_logs', [
            'attempt_id' => $this->attempt->id,
            'student_id' => $this->student->id,
            'event_type' => 'phone_detected',
        ]);

        // Verify cheat warning was incremented
        $this->attempt->refresh();
        $this->assertGreaterThan(0, $this->attempt->cheat_warnings);
    }

    /**
     * 2. Test proctor snapshot with MediaPipe sustained head turn (> 10s)
     */
    public function test_proctor_snapshot_with_sustained_head_turn_over_10s()
    {
        $dummyImage = 'data:image/jpeg;base64,' . base64_encode('fake-image-content');

        $response = $this->actingAs($this->student)->postJson(
            route('student.exams.proctor-snapshot', $this->attempt->id),
            [
                'image' => $dummyImage,
                'trigger_source' => 'browser_realtime_detection',
                'anomaly_type' => 'looking_away',
                'face_count' => 1,
                'duration_seconds' => 11,
                'details' => 'AI phát hiện thí sinh quay trái liên tục quá 10s (11s), không nhìn trực diện màn hình.',
                'detections' => [],
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'violation',
        ]);

        // Check AntiCheatLog recorded duration
        $this->assertDatabaseHas('anti_cheat_logs', [
            'attempt_id' => $this->attempt->id,
            'event_type' => 'looking_away',
            'duration_seconds' => 11,
        ]);
    }

    /**
     * 3. Test proctor snapshot with multiple persons (>1)
     */
    public function test_proctor_snapshot_with_multiple_persons()
    {
        $dummyImage = 'data:image/jpeg;base64,' . base64_encode('fake-image-content');

        $response = $this->actingAs($this->student)->postJson(
            route('student.exams.proctor-snapshot', $this->attempt->id),
            [
                'image' => $dummyImage,
                'trigger_source' => 'browser_realtime_detection',
                'anomaly_type' => 'multiple_persons',
                'face_count' => 2,
                'details' => 'Phát hiện 2 người cùng xuất hiện trước camera (Nghi vấn có người trợ giúp)',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'violation',
        ]);

        $this->assertDatabaseHas('anti_cheat_logs', [
            'attempt_id' => $this->attempt->id,
            'event_type' => 'multiple_persons',
        ]);
    }
}
