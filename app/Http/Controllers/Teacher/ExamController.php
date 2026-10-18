<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $classes = \App\Models\SchoolClass::where('teacher_id', auth()->id())->get();
        $categories = \App\Models\Category::where('created_by', auth()->id())
            ->orWhereNull('created_by')
            ->get();
        
        $query = Exam::with(['subject', 'category'])->where('created_by', auth()->id());
        
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('class_id') && $request->class_id != '') {
            $classId = $request->class_id;
            $query->whereHas('assignments', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        
        $exams = $query->latest()->paginate(15);
        
        return view('teacher.exams.index', compact('exams', 'subjects', 'classes', 'categories'));
    }

    public function create()
    {
        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $categories = \App\Models\Category::where('created_by', auth()->id())
            ->orWhereNull('created_by')
            ->get();
        return view('teacher.exams.create', compact('subjects', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'nullable|integer|min:0',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'questions' => 'required|array|min:1', // Array of question IDs
            'points' => 'required|array', // Array of points per question
            'proctor_interval_seconds' => 'nullable|integer|min:15|max:1800',
        ]);

        $maxAttempts = $request->has('unlimited_attempts') || $request->max_attempts === '0' || empty($request->max_attempts) ? 0 : (int) $request->max_attempts;
        $enableAntiCheat = $request->has('enable_anti_cheat');

        $exam = Exam::create([
            'subject_id' => $request->subject_id,
            'category_id' => $request->category_id,
            'created_by' => auth()->id(),
            'code' => strtoupper(Str::random(8)),
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_attempts' => $maxAttempts,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'total_questions' => count($request->questions),
            'status' => 'closed',
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_answers' => $request->has('shuffle_answers'),
            'allow_review' => $request->has('allow_review'),
            'allow_view_score' => $request->has('allow_view_score'),
            'semester' => $request->semester,
            'enable_anti_cheat' => $enableAntiCheat,
            'require_fullscreen' => $enableAntiCheat && $request->has('require_fullscreen'),
            'prevent_tab_switch' => $enableAntiCheat && $request->has('prevent_tab_switch'),
            'prevent_copy_paste' => $enableAntiCheat && $request->has('prevent_copy_paste'),
            'prevent_right_click' => $enableAntiCheat && $request->has('prevent_right_click'),
            'prevent_screen_capture' => $enableAntiCheat && $request->has('prevent_screen_capture'),
            'require_face_verification' => $enableAntiCheat && $request->has('require_face_verification'),
            'enable_proctor_camera' => $enableAntiCheat && $request->has('enable_proctor_camera'),
            'proctor_interval_seconds' => (int) ($request->proctor_interval_seconds ?? 180),
        ]);

        foreach ($request->questions as $index => $questionId) {
            $exam->questions()->attach($questionId, [
                'question_order' => $index + 1,
                'points' => $request->points[$questionId] ?? 1,
            ]);
        }

        return redirect()->route('teacher.exams.index')->with('success', 'Tạo đề thi thành công!');
    }

    public function edit(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $subjects = auth()->user()->subjects()->where('status', true)->get();
        $categories = \App\Models\Category::where('created_by', auth()->id())
            ->orWhereNull('created_by')
            ->get();
        $exam->load(['questions' => function($q) {
            $q->orderBy('exam_questions.question_order');
        }]);
        
        return view('teacher.exams.edit', compact('exam', 'subjects', 'categories'));
    }

    public function update(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'nullable|integer|min:0',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|in:published,closed',
            'questions' => 'required|array|min:1', // Array of question IDs
            'points' => 'required|array', // Array of points per question
            'proctor_interval_seconds' => 'nullable|integer|min:15|max:1800',
        ]);

        $maxAttempts = $request->has('unlimited_attempts') || $request->max_attempts === '0' || empty($request->max_attempts) ? 0 : (int) $request->max_attempts;
        $enableAntiCheat = $request->has('enable_anti_cheat');

        $exam->update([
            'subject_id' => $request->subject_id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'max_attempts' => $maxAttempts,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'status' => $request->status,
            'total_questions' => count($request->questions),
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_answers' => $request->has('shuffle_answers'),
            'allow_review' => $request->has('allow_review'),
            'allow_view_score' => $request->has('allow_view_score'),
            'semester' => $request->semester,
            'enable_anti_cheat' => $enableAntiCheat,
            'require_fullscreen' => $enableAntiCheat && $request->has('require_fullscreen'),
            'prevent_tab_switch' => $enableAntiCheat && $request->has('prevent_tab_switch'),
            'prevent_copy_paste' => $enableAntiCheat && $request->has('prevent_copy_paste'),
            'prevent_right_click' => $enableAntiCheat && $request->has('prevent_right_click'),
            'prevent_screen_capture' => $enableAntiCheat && $request->has('prevent_screen_capture'),
            'require_face_verification' => $enableAntiCheat && $request->has('require_face_verification'),
            'enable_proctor_camera' => $enableAntiCheat && $request->has('enable_proctor_camera'),
            'proctor_interval_seconds' => (int) ($request->proctor_interval_seconds ?? 180),
        ]);

        $syncData = [];
        foreach ($request->questions as $index => $questionId) {
            $syncData[$questionId] = [
                'question_order' => $index + 1,
                'points' => $request->points[$questionId] ?? 1,
            ];
        }
        $exam->questions()->sync($syncData);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật đề thi thành công!');
    }

    public function destroy(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa đề thi này.');
        }

        $exam->delete();

        return redirect()->route('teacher.exams.index')->with('success', 'Đã xóa đề thi thành công!');
    }

    public function updateStatus(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $request->validate([
            'status' => 'required|in:published,closed'
        ]);

        $exam->update(['status' => $request->status]);

        return redirect()->route('teacher.exams.index')->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function updateQuickSettings(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Bạn không có quyền sửa đề thi này.'], 403);
            }
            abort(403, 'Bạn không có quyền sửa đề thi này.');
        }

        $data = [];
        if ($request->has('enable_proctor_camera')) {
            $data['enable_proctor_camera'] = filter_var($request->input('enable_proctor_camera'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('require_face_verification')) {
            $data['require_face_verification'] = filter_var($request->input('require_face_verification'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('allow_review')) {
            $data['allow_review'] = filter_var($request->input('allow_review'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('allow_view_score')) {
            $data['allow_view_score'] = filter_var($request->input('allow_view_score'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('proctor_interval_seconds')) {
            $interval = (int) $request->input('proctor_interval_seconds');
            if ($interval >= 15 && $interval <= 1800) {
                $data['proctor_interval_seconds'] = $interval;
            }
        }

        if (!empty($data)) {
            $exam->update($data);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cài đặt đề thi thành công!',
                'exam' => [
                    'id' => $exam->id,
                    'enable_proctor_camera' => (bool) $exam->enable_proctor_camera,
                    'require_face_verification' => (bool) $exam->require_face_verification,
                    'allow_review' => (bool) $exam->allow_review,
                    'allow_view_score' => (bool) $exam->allow_view_score,
                    'proctor_interval_seconds' => (int) ($exam->proctor_interval_seconds ?? 120),
                ]
            ]);
        }

        return back()->with('success', 'Cập nhật cài đặt đề thi thành công!');
    }

    public function results(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem kết quả đề thi này.');
        }

        $attempts = ExamAttempt::with([
                'student',
                'proctorSnapshots' => function($q) {
                    $q->orderBy('captured_at', 'desc');
                }
            ])
            ->where('exam_id', $exam->id)
            ->orderByRaw("CASE WHEN status = 'submitted' THEN 0 ELSE 1 END")
            ->orderBy('submitted_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        $submittedAttempts = $attempts->where('status', 'submitted');
        $stats = [
            'total' => $submittedAttempts->count(),
            'average' => $submittedAttempts->avg('score_value') ? round($submittedAttempts->avg('score_value'), 1) : 0,
            'max' => $submittedAttempts->max('score_value') ?? 0,
            'min' => $submittedAttempts->min('score_value') ?? 0,
            'passed' => $submittedAttempts->where('score_value', '>=', 5)->count(),
        ];
        
        $stats['pass_rate'] = $stats['total'] > 0 ? round(($stats['passed'] / $stats['total']) * 100) : 0;

        return view('teacher.exams.results', compact('exam', 'attempts', 'stats'));
    }

    public function monitor(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền giám sát đề thi này.');
        }
        return view('teacher.exams.monitor', compact('exam'));
    }

    public function apiMonitor(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attempts = ExamAttempt::with([
                'student',
                'antiCheatLogs' => function($q) {
                    $q->latest('occurred_at')->limit(1);
                },
                'proctorSnapshots' => function($q) {
                    $q->latest('captured_at')->limit(1);
                }
            ])
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->get()
            ->map(function ($attempt) {
                $lastLog = $attempt->antiCheatLogs->first();
                $lastSnap = $attempt->proctorSnapshots->first();

                return [
                    'id' => $attempt->id,
                    'student_name' => $attempt->student->name ?? 'N/A',
                    'student_code' => $attempt->student->code ?? 'N/A',
                    'student_email' => $attempt->student->email ?? '',
                    'out_of_screen_time' => $attempt->out_of_screen_time,
                    'cheat_warnings' => $attempt->cheat_warnings,
                    'started_at' => $attempt->started_at ? $attempt->started_at->format('H:i:s') : 'N/A',
                    'last_event' => $lastLog ? $lastLog->event_info['title'] : null,
                    'last_event_time' => $lastLog && $lastLog->occurred_at ? $lastLog->occurred_at->format('H:i:s') : null,
                    'face_verified' => (bool) $attempt->face_verified_at,
                    'face_similarity' => $attempt->face_similarity,
                    'verification_image_url' => $attempt->verification_image_url,
                    'enrolled_image_url' => $attempt->student ? $attempt->student->frontal_face_url : null,
                    'latest_snapshot_url' => $lastSnap ? $lastSnap->image_url : null,
                    'latest_snapshot_status' => $lastSnap ? $lastSnap->status : 'normal',
                ];
            });

        return response()->json(['attempts' => $attempts]);
    }

    public function studentBehavior(Exam $exam, ExamAttempt $attempt)
    {
        if ($exam->created_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->exam_id !== $exam->id) {
            return response()->json(['error' => 'Invalid attempt for this exam'], 404);
        }

        $attempt->load([
            'student',
            'antiCheatLogs' => function($q) {
                $q->orderBy('occurred_at', 'desc');
            },
            'proctorSnapshots' => function($q) {
                $q->orderBy('captured_at', 'desc');
            }
        ]);

        $student = $attempt->student;
        $logs = $attempt->antiCheatLogs->map(function ($log) {
            $info = $log->event_info;
            return [
                'id' => $log->id,
                'event_type' => $log->event_type,
                'title' => $info['title'],
                'description' => $info['description'],
                'badge' => $info['badge'],
                'icon' => $info['icon'],
                'severity' => $info['severity'],
                'duration_seconds' => $log->duration_seconds,
                'ip_address' => $log->ip_address,
                'snapshot_url' => $log->snapshot_url,
                'occurred_at' => $log->occurred_at ? $log->occurred_at->format('H:i:s d/m/Y') : null,
                'occurred_time' => $log->occurred_at ? $log->occurred_at->format('H:i:s') : null,
                'time_diff' => $log->occurred_at ? $log->occurred_at->diffForHumans() : null,
                'event_data' => $log->event_data,
            ];
        });

        $snapshots = $attempt->proctorSnapshots->map(function ($snap) {
            return [
                'id' => $snap->id,
                'image_url' => $snap->image_url,
                'status' => $snap->status,
                'violations' => $snap->violations ?? [],
                'detections' => $snap->detections ?? [],
                'face_similarity' => $snap->face_similarity,
                'details' => $snap->details,
                'captured_at' => $snap->captured_at ? $snap->captured_at->format('H:i:s d/m/Y') : null,
                'captured_time' => $snap->captured_at ? $snap->captured_at->format('H:i:s') : null,
            ];
        });

        $stats = [
            'total_logs' => $logs->count(),
            'cheat_warnings' => $attempt->cheat_warnings,
            'out_of_screen_time' => $attempt->out_of_screen_time,
            'fullscreen_exits' => $logs->where('event_type', 'fullscreen_exit')->count(),
            'tab_switches' => $logs->whereIn('event_type', ['tab_switch', 'window_blur'])->count(),
            'copy_pastes' => $logs->whereIn('event_type', ['copy', 'paste', 'cut'])->count(),
            'right_clicks' => $logs->where('event_type', 'right_click')->count(),
            'phone_violations' => $logs->where('event_type', 'phone_detected')->count(),
            'multiple_persons' => $logs->where('event_type', 'multiple_persons')->count(),
            'face_absent' => $logs->where('event_type', 'face_absent')->count(),
            'face_mismatches' => $logs->where('event_type', 'face_mismatch')->count(),
            'total_snapshots' => $snapshots->count(),
            'avg_snapshot_similarity' => round($attempt->proctorSnapshots->whereNotNull('face_similarity')->avg('face_similarity') ?? 0, 1),
            'latest_snapshot_similarity' => $attempt->proctorSnapshots->whereNotNull('face_similarity')->first()?->face_similarity,
        ];

        return response()->json([
            'student' => [
                'id' => $student->id ?? null,
                'name' => $student->name ?? 'N/A',
                'code' => $student->code ?? 'N/A',
                'email' => $student->email ?? 'N/A',
                'initial' => mb_substr($student->name ?? '?', 0, 1, 'UTF-8'),
                'enrolled_image_url' => $student ? $student->frontal_face_url : null,
                'face_registered' => $student ? $student->face_registered : false,
            ],
            'attempt' => [
                'id' => $attempt->id,
                'status' => $attempt->status,
                'status_text' => $attempt->status === 'submitted' ? 'Đã nộp bài' : ($attempt->status === 'in_progress' ? 'Đang làm bài' : 'Chưa hoàn thành'),
                'score' => $attempt->score_value,
                'started_at' => $attempt->started_at ? $attempt->started_at->format('H:i:s d/m/Y') : 'N/A',
                'submitted_at' => $attempt->submitted_at ? $attempt->submitted_at->format('H:i:s d/m/Y') : null,
                'correct_answers' => $attempt->correct_answers,
                'wrong_answers' => $attempt->wrong_answers,
                'unanswered' => $attempt->unanswered,
                'cheat_warnings' => $attempt->cheat_warnings,
                'out_of_screen_time' => $attempt->out_of_screen_time,
                'face_verified_at' => $attempt->face_verified_at ? $attempt->face_verified_at->format('H:i:s d/m/Y') : null,
                'face_similarity' => $attempt->face_similarity,
                'verification_image_url' => $attempt->verification_image_url,
            ],
            'stats' => $stats,
            'logs' => $logs,
            'snapshots' => $snapshots,
        ]);
    }

    /**
     * Export exam results and statistics to Excel (.xls formatted table).
     */
    public function exportExcel(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xuất báo cáo đề thi này.');
        }

        $data = $this->getExamReportData($exam);

        $sanitizedSubject = Str::slug($exam->subject->name ?? 'mon_hoc', '_');
        $sanitizedTitle = Str::slug($exam->title, '_');
        $filename = "Bang_diem_{$sanitizedSubject}_{$sanitizedTitle}_" . date('Ymd_His') . ".xls";

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->view('teacher.exams.export-excel', $data, 200, $headers);
    }

    /**
     * Display printable PDF report of an exam for a subject.
     */
    public function exportPdf(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xuất báo cáo đề thi này.');
        }

        $data = $this->getExamReportData($exam);

        return view('teacher.exams.export-pdf', $data);
    }

    /**
     * Helper to prepare statistics and attempts data for a single exam of a subject.
     */
    protected function getExamReportData(Exam $exam): array
    {
        $exam->load([
            'subject',
            'category',
            'creator',
            'assignments.schoolClass',
        ]);

        $attempts = ExamAttempt::with([
                'student.schoolClasses',
                'antiCheatLogs',
            ])
            ->where('exam_id', $exam->id)
            ->where('status', 'submitted')
            ->orderByDesc('score_value')
            ->get();

        $totalAttempts = $attempts->count();
        $averageScore = $totalAttempts > 0 ? round((float) $attempts->avg('score_value'), 2) : 0;
        $maxScore = $totalAttempts > 0 ? (float) $attempts->max('score_value') : 0;
        $minScore = $totalAttempts > 0 ? (float) $attempts->min('score_value') : 0;
        $passedCount = $attempts->where('score_value', '>=', 5.0)->count();
        $failedCount = $totalAttempts - $passedCount;
        $passRate = $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100, 1) : 0;

        // Phân loại dải điểm
        $brackets = [
            'excellent' => $attempts->where('score_value', '>=', 9.0)->count(),
            'good' => $attempts->filter(fn($a) => $a->score_value >= 8.0 && $a->score_value < 9.0)->count(),
            'fair' => $attempts->filter(fn($a) => $a->score_value >= 6.5 && $a->score_value < 8.0)->count(),
            'average' => $attempts->filter(fn($a) => $a->score_value >= 5.0 && $a->score_value < 6.5)->count(),
            'weak' => $attempts->where('score_value', '<', 5.0)->count(),
        ];

        $assignedClasses = $exam->assignments->map(fn($a) => $a->schoolClass->name ?? null)->filter()->unique()->values()->implode(', ');
        if (empty($assignedClasses)) {
            $assignedClasses = 'Tất cả sinh viên theo danh sách';
        }

        $formattedAttempts = $attempts->map(function ($attempt, $index) use ($exam) {
            $score = (float) $attempt->score_value;
            $classification = match (true) {
                $score >= 9.0 => 'Xuất sắc',
                $score >= 8.0 => 'Giỏi',
                $score >= 6.5 => 'Khá',
                $score >= 5.0 => 'Trung bình',
                default => 'Chưa đạt / Yếu',
            };

            $durationMinutes = 0;
            if ($attempt->started_at && $attempt->submitted_at) {
                $durationMinutes = round($attempt->started_at->diffInSeconds($attempt->submitted_at) / 60, 1);
            }

            $className = $attempt->student?->schoolClasses?->first()?->name ?? 'N/A';

            return [
                'index' => $index + 1,
                'student_code' => $attempt->student->code ?? ('SV' . str_pad($attempt->student_id, 4, '0', STR_PAD_LEFT)),
                'student_name' => $attempt->student->name ?? 'Sinh viên chưa cập nhật tên',
                'student_email' => $attempt->student->email ?? '',
                'class_name' => $className,
                'score' => number_format($score, 2),
                'score_raw' => $score,
                'correct_answers' => $attempt->correct_answers ?? 0,
                'wrong_answers' => $attempt->wrong_answers ?? 0,
                'unanswered' => $attempt->unanswered ?? 0,
                'total_questions' => $exam->total_questions,
                'started_at' => $attempt->started_at ? $attempt->started_at->format('d/m/Y H:i:s') : 'N/A',
                'submitted_at' => $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i:s') : 'N/A',
                'duration_text' => $durationMinutes > 0 ? "{$durationMinutes} phút" : 'N/A',
                'cheat_warnings' => $attempt->cheat_warnings ?? 0,
                'out_of_screen_time' => $attempt->out_of_screen_time ?? 0,
                'face_similarity' => $attempt->face_similarity ? number_format($attempt->face_similarity, 1) . '%' : ($attempt->face_verified_at ? 'Hợp lệ' : 'Không có'),
                'classification' => $classification,
                'status_result' => $score >= 5.0 ? 'Đạt' : 'Không đạt',
            ];
        });

        return [
            'exam' => $exam,
            'attempts' => $formattedAttempts,
            'assignedClasses' => $assignedClasses,
            'stats' => [
                'total' => $totalAttempts,
                'average' => $averageScore,
                'max' => $maxScore,
                'min' => $minScore,
                'passed' => $passedCount,
                'failed' => $failedCount,
                'pass_rate' => $passRate,
                'fail_rate' => $totalAttempts > 0 ? round(100 - $passRate, 1) : 0,
                'brackets' => $brackets,
            ],
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'teacher_name' => auth()->user()->name ?? ($exam->creator->name ?? 'Giảng viên'),
        ];
    }
}

