<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\ExamAssignment;
use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\Answer;
use App\Models\AntiCheatLog;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $assignments = ExamAssignment::with(['exam.subject', 'exam.category'])
            ->where('student_id', auth()->id())
            ->latest('id')
            ->get();
            
        // Calculate status and available actions for each assigned exam
        $exams = $assignments->map(function ($assignment) {
            $exam = $assignment->exam;
            $now = now();
            
            $attempts = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', auth()->id())
                ->latest('id')
                ->get();
                
            $latestAttempt = $attempts->first();
            $inProgressAttempt = $attempts->where('status', 'in_progress')->first();
            $submittedAttempts = $attempts->where('status', 'submitted');
            $submittedCount = $submittedAttempts->count();
            $highestScore = $submittedAttempts->max('score_value');
                
            $isTimeEnded = $exam->end_at && $now->gt($exam->end_at);
            $isTimeNotStarted = $exam->start_at && $now->lt($exam->start_at);
            $isPublished = in_array($exam->status, ['published', 'ongoing']);
            $isUnlimited = $exam->isUnlimitedAttempts();
            $hasRetakeAvailable = ($isUnlimited || $submittedCount < ($exam->max_attempts ?? 1));

            $exam->attempt_status = $latestAttempt ? $latestAttempt->status : null;
            $exam->submitted_count = $submittedCount;
            $exam->score = $highestScore !== null ? $highestScore : ($latestAttempt ? $latestAttempt->score_value : null);
            $exam->latest_score = $latestAttempt ? $latestAttempt->score_value : null;
            $exam->has_in_progress = (bool) $inProgressAttempt;
            $exam->can_review = ($submittedCount > 0 && (bool) $exam->allow_review);
            $exam->can_take = $isPublished && !$isTimeEnded && !$isTimeNotStarted && ($inProgressAttempt || $hasRetakeAvailable);
            
            if ($isTimeNotStarted) {
                $exam->calculated_status = 'Sắp thi';
                $exam->status_color = 'amber';
            } elseif ($isTimeEnded) {
                $exam->calculated_status = 'Đã hết hạn';
                $exam->status_color = 'rose';
            } elseif (!$isPublished) {
                $exam->calculated_status = 'Đang khóa';
                $exam->status_color = 'gray';
            } elseif ($inProgressAttempt) {
                $exam->calculated_status = 'Đang làm dở';
                $exam->status_color = 'blue';
            } elseif ($submittedCount > 0) {
                if ($hasRetakeAvailable) {
                    $exam->calculated_status = 'Có thể thi lại (' . $submittedCount . ($isUnlimited ? '/∞' : '/' . $exam->max_attempts) . ')';
                    $exam->status_color = 'indigo';
                } else {
                    $exam->calculated_status = 'Đã nộp (' . $submittedCount . '/' . $exam->max_attempts . ')';
                    $exam->status_color = 'emerald';
                }
            } else {
                $exam->calculated_status = 'Chưa làm';
                $exam->status_color = 'blue';
            }
            
            return $exam;
        });

        $categories = $exams->map(function ($exam) {
            return $exam->category ? [
                'id' => 'cat_' . $exam->category->id,
                'name' => $exam->category->name,
                'type' => 'category'
            ] : ($exam->subject ? [
                'id' => 'subj_' . $exam->subject->id,
                'name' => $exam->subject->name,
                'type' => 'subject'
            ] : null);
        })->filter()->unique('id')->values();
        
        return view('student.exams.index', compact('exams', 'categories'));
    }

    public function join(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $exam = Exam::where('code', strtoupper($request->code))->first();
        
        if (!$exam) {
            return back()->withErrors(['code' => 'Mã đề thi không tồn tại.']);
        }
        
        if ($exam->status !== 'published' && $exam->status !== 'ongoing') {
            return back()->withErrors(['code' => 'Kỳ thi này hiện không mở.']);
        }
        
        if ($exam->start_at && now()->lt($exam->start_at)) {
            return back()->withErrors(['code' => 'Kỳ thi chưa bắt đầu.']);
        }
        
        if ($exam->end_at && now()->gt($exam->end_at)) {
            return back()->withErrors(['code' => 'Kỳ thi đã kết thúc.']);
        }

        // Check if there is an in_progress attempt
        $inProgress = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if ($inProgress) {
            return redirect()->route('student.exams.take', $exam);
        }

        // Check max attempts (if configured and > 0)
        if ($exam->max_attempts && $exam->max_attempts > 0) {
            $submittedCount = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', auth()->id())
                ->where('status', 'submitted')
                ->count();

            if ($submittedCount >= $exam->max_attempts) {
                return back()->withErrors(['code' => 'Bạn đã hoàn thành tối đa ' . $exam->max_attempts . ' lần làm bài cho phép.']);
            }
        }

        // Check if exam requires face verification and student has not registered yet
        if ($exam->require_face_verification && !auth()->user()->face_registered) {
            return redirect()->route('student.face.register')
                ->with('error', 'Kỳ thi này yêu cầu xác thực khuôn mặt. Vui lòng đăng ký Face ID trước khi tham gia.');
        }

        // Create new attempt
        ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => auth()->id(),
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('student.exams.take', $exam);
    }

    public function take(Exam $exam)
    {
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if (!$attempt) {
            return redirect()->route('student.dashboard')->with('error', 'Bạn không thể truy cập bài thi này.');
        }
        
        // Time check
        if ($exam->end_at && now()->gt($exam->end_at)) {
            return redirect()->route('student.exams.submit', $exam); // auto submit if late
        }

        $timeLimit = $exam->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($attempt->started_at);
        $timeRemaining = max(0, $timeLimit - $elapsed);
        
        if ($timeRemaining <= 0) {
            return redirect()->route('student.exams.submit', $exam); // time's up
        }

        $exam->load(['questions.answers', 'subject']);

        $savedAnswers = ExamAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        return view('student.exams.take', compact('exam', 'attempt', 'timeRemaining', 'savedAnswers'));
    }

    public function saveAnswer(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Phiên làm bài không tồn tại hoặc đã kết thúc.'], 404);
        }

        $questionId = $request->input('question_id');
        $answerId = $request->input('answer_id');

        if (empty($answerId)) {
            ExamAnswer::where('attempt_id', $attempt->id)->where('question_id', $questionId)->delete();
            return response()->json([
                'success' => true,
                'cleared' => true,
                'question_id' => $questionId,
                'saved_at' => now()->format('H:i:s')
            ]);
        }

        $question = Question::find($questionId);
        if (!$question) {
            return response()->json(['error' => 'Câu hỏi không tồn tại.'], 404);
        }

        $isCorrect = null;
        $pointsEarned = 0;

        if ($question->type === 'multiple_choice' && $answerId) {
            $correctAnswer = Answer::where('question_id', $questionId)->where('is_correct', true)->first();
            $examQuestion = ExamQuestion::where('exam_id', $attempt->exam_id)->where('question_id', $questionId)->first();
            $maxPoints = $examQuestion->points ?? 1;

            if ($correctAnswer && $correctAnswer->id == $answerId) {
                $isCorrect = true;
                $pointsEarned = $maxPoints;
            } else {
                $isCorrect = false;
                $pointsEarned = 0;
            }
        }

        ExamAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $questionId],
            [
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'answered_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'question_id' => $questionId,
            'saved_at' => now()->format('H:i:s')
        ]);
    }

    public function syncOffline(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Phiên làm bài không tồn tại hoặc đã kết thúc.'], 404);
        }

        $answers = $request->input('answers', []);
        $logs = $request->input('logs', []);

        // Sync answers
        $syncedAnswersCount = 0;
        foreach ($answers as $item) {
            $qId = $item['question_id'] ?? null;
            $aId = $item['answer_id'] ?? null;
            if (!$qId) continue;

            if (empty($aId)) {
                ExamAnswer::where('attempt_id', $attempt->id)->where('question_id', $qId)->delete();
                $syncedAnswersCount++;
                continue;
            }

            $question = Question::find($qId);
            if (!$question) continue;

            $isCorrect = null;
            $pointsEarned = 0;

            if ($question->type === 'multiple_choice' && $aId) {
                $correctAnswer = Answer::where('question_id', $qId)->where('is_correct', true)->first();
                $examQuestion = ExamQuestion::where('exam_id', $attempt->exam_id)->where('question_id', $qId)->first();
                $maxPoints = $examQuestion->points ?? 1;

                if ($correctAnswer && $correctAnswer->id == $aId) {
                    $isCorrect = true;
                    $pointsEarned = $maxPoints;
                } else {
                    $isCorrect = false;
                    $pointsEarned = 0;
                }
            }

            ExamAnswer::updateOrCreate(
                ['attempt_id' => $attempt->id, 'question_id' => $qId],
                [
                    'answer_id' => $aId,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'answered_at' => isset($item['timestamp']) ? \Carbon\Carbon::parse($item['timestamp']) : now(),
                ]
            );
            $syncedAnswersCount++;
        }

        // Sync offline anti cheat logs
        $validEvents = [
            'tab_switch', 'window_blur', 'window_focus',
            'fullscreen_exit', 'fullscreen_enter',
            'copy', 'paste', 'cut', 'select_all', 'right_click',
            'page_reload', 'connection_lost', 'connection_restored'
        ];

        $syncedLogsCount = 0;
        $totalDuration = 0;
        $violationCount = 0;

        foreach ($logs as $logItem) {
            $rawEvent = $logItem['event_type'] ?? 'tab_switch';
            $savedEvent = in_array($rawEvent, $validEvents) ? $rawEvent : 'tab_switch';
            $duration = (int) ($logItem['duration_seconds'] ?? 0);

            if (in_array($rawEvent, ['fullscreen_exit', 'tab_switch', 'window_blur', 'copy', 'paste', 'cut', 'right_click', 'page_reload', 'print_screen'])) {
                $violationCount++;
            }
            if ($duration > 0) {
                $totalDuration += $duration;
            }

            AntiCheatLog::create([
                'attempt_id' => $attempt->id,
                'student_id' => auth()->id(),
                'event_type' => $savedEvent,
                'event_data' => $logItem['event_data'] ?? ($rawEvent !== $savedEvent ? ['raw_event' => $rawEvent] : null),
                'duration_seconds' => $duration > 0 ? $duration : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'occurred_at' => isset($logItem['timestamp']) ? \Carbon\Carbon::parse($logItem['timestamp']) : now(),
            ]);
            $syncedLogsCount++;
        }

        if ($violationCount > 0) {
            $attempt->increment('cheat_warnings', $violationCount);
        }
        if ($totalDuration > 0) {
            $attempt->increment('out_of_screen_time', $totalDuration);
        }

        return response()->json([
            'success' => true,
            'synced_answers' => $syncedAnswersCount,
            'synced_logs' => $syncedLogsCount,
            'synced_at' => now()->format('H:i:s')
        ]);
    }

    public function submit(Request $request, Exam $exam)
    {
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if (!$attempt) {
            return redirect()->route('student.dashboard');
        }

        $inputAnswers = $request->input('answers', []);

        // Process any answers submitted with form
        foreach ($exam->questions as $question) {
            $studentAnswerId = $inputAnswers[$question->id] ?? null;

            if ($studentAnswerId) {
                $isCorrect = null;
                $pointsEarned = 0;

                if ($question->type === 'multiple_choice') {
                    $correctAnswer = $question->answers()->where('is_correct', true)->first();
                    $examQuestion = ExamQuestion::where('exam_id', $exam->id)->where('question_id', $question->id)->first();
                    $maxPoints = $examQuestion->points ?? 1;

                    if ($correctAnswer && $correctAnswer->id == $studentAnswerId) {
                        $isCorrect = true;
                        $pointsEarned = $maxPoints;
                    } else {
                        $isCorrect = false;
                        $pointsEarned = 0;
                    }
                }

                ExamAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                    [
                        'answer_id' => $studentAnswerId,
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsEarned,
                        'answered_at' => now(),
                    ]
                );
            }
        }

        // Aggregate statistics from all ExamAnswers for this attempt
        $allAnswers = ExamAnswer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');
        $totalScore = 0;
        $correct = 0;
        $wrong = 0;
        $unanswered = 0;

        foreach ($exam->questions as $question) {
            if ($allAnswers->has($question->id) && $allAnswers[$question->id]->answer_id) {
                $ans = $allAnswers[$question->id];
                $totalScore += $ans->points_earned;
                if ($ans->is_correct === true) {
                    $correct++;
                } elseif ($ans->is_correct === false) {
                    $wrong++;
                }
            } else {
                $unanswered++;
            }
        }

        // Enforce all questions answered unless time has expired
        $timeLimit = $exam->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($attempt->started_at);
        $isTimeExpired = ($exam->end_at && now()->gt($exam->end_at)) || ($elapsed >= $timeLimit);

        if ($unanswered > 0 && !$isTimeExpired && !$request->boolean('force_timeout')) {
            return redirect()->route('student.exams.take', $exam)->with('error', "Không thể nộp bài! Bạn còn {$unanswered} câu hỏi chưa làm. Vui lòng hoàn thành 100% câu hỏi trước khi nộp.");
        }

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'score_value' => $totalScore,
            'correct_answers' => $correct,
            'wrong_answers' => $wrong,
            'unanswered' => $unanswered,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Nộp bài thi thành công!');
    }

    public function cheat(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('student_id', auth()->id())
            ->first();
            
        if ($attempt) {
            $eventType = $request->input('event_type', 'tab_switch');
            $duration = (int) $request->input('duration_seconds', $request->input('out_time', 0));
            $eventData = $request->input('event_data', null);

            // Valid event types from database schema
            $validEvents = [
                'tab_switch', 'window_blur', 'window_focus',
                'fullscreen_exit', 'fullscreen_enter',
                'copy', 'paste', 'cut', 'select_all', 'right_click',
                'page_reload', 'connection_lost', 'connection_restored'
            ];

            $savedEventType = in_array($eventType, $validEvents) ? $eventType : 'tab_switch';

            // Increment violation warning counters
            $isViolation = in_array($eventType, ['fullscreen_exit', 'tab_switch', 'window_blur', 'copy', 'paste', 'cut', 'right_click', 'page_reload', 'print_screen', 'select_all']);
            
            if ($isViolation) {
                $attempt->increment('cheat_warnings');
            }
            
            if ($duration > 0) {
                $attempt->increment('out_of_screen_time', $duration);
            }

            // Create anti cheat log
            AntiCheatLog::create([
                'attempt_id' => $attempt->id,
                'student_id' => auth()->id(),
                'event_type' => $savedEventType,
                'event_data' => $eventData ?: ($eventType !== $savedEventType ? ['raw_event' => $eventType] : null),
                'duration_seconds' => $duration > 0 ? $duration : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'occurred_at' => now(),
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    public function review(Exam $exam)
    {
        if (!$exam->allow_review) {
            return redirect()->route('student.exams.index')->with('error', 'Kỳ thi này không cho phép xem lại bài.');
        }

        $attempt = ExamAttempt::with('answers')->where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'submitted')
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()->route('student.exams.index')->with('error', 'Không tìm thấy bài thi.');
        }

        $exam->load(['questions.answers']);
        $studentAnswers = $attempt->answers->keyBy('question_id');

        return view('student.exams.review', compact('exam', 'attempt', 'studentAnswers'));
    }

    public function captureProctorSnapshot(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::with(['exam', 'student'])->where('id', $attemptId)
            ->where('student_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Phiên thi không hợp lệ.'], 404);
        }

        $imageB64 = $request->input('image');
        if (empty($imageB64)) {
            return response()->json(['error' => 'Không có dữ liệu ảnh.'], 400);
        }

        $student = $attempt->student;
        $enrolledEmbedding = $student && !empty($student->face_embedding)
            ? (is_string($student->face_embedding) ? json_decode($student->face_embedding, true) : $student->face_embedding)
            : null;

        // Call AI Service
        $aiService = app(\App\Services\AiProctorService::class);
        $result = $aiService->analyzeProctorSnapshot($imageB64, $enrolledEmbedding, 70.0);

        // Save encrypted image to private storage: storage/app/private/proctor/{attempt_id}/
        $folder = "proctor/{$attempt->id}";
        $data = $imageB64;
        if (str_contains($data, ',')) {
            $data = explode(',', $data)[1];
        }
        $binary = base64_decode($data);
        $fileName = "{$folder}/snap_" . time() . "_" . \Illuminate\Support\Str::random(5) . ".enc";
        \App\Services\SecureMediaService::storeEncrypted($fileName, $binary, 'local');

        $status = $result['status'] ?? 'normal';
        $violations = $result['violations'] ?? [];
        $detections = $result['detections'] ?? [];
        $faceSimilarity = $result['face_similarity'] ?? null;
        $summary = $result['summary'] ?? '';
        $personCount = $result['person_count'] ?? 1;

        // Guaranteed log if 2 or more persons are detected
        if ($personCount > 1) {
            $hasMultiple = false;
            foreach ($violations as $v) {
                if (($v['type'] ?? '') === 'multiple_persons') {
                    $hasMultiple = true;
                    break;
                }
            }
            if (!$hasMultiple) {
                $violations[] = [
                    'type' => 'multiple_persons',
                    'severity' => 'high',
                    'message' => "Phát hiện {$personCount} người trong khung hình camera (Có người trợ giúp)",
                ];
                $status = 'violation';
            }
        }

        // Save ExamProctorSnapshot record
        $snapshot = \App\Models\ExamProctorSnapshot::create([
            'attempt_id' => $attempt->id,
            'student_id' => $student->id,
            'image_path' => $fileName,
            'status' => $status,
            'violations' => $violations,
            'detections' => $detections,
            'face_similarity' => $faceSimilarity,
            'details' => $summary,
            'captured_at' => now(),
        ]);

        // If violations detected, record in AntiCheatLog and increment warning counters
        if ($status === 'violation' || !empty($violations)) {
            $count = count($violations) > 0 ? count($violations) : 1;
            $attempt->increment('cheat_warnings', $count);

            foreach ($violations as $v) {
                $rawType = $v['type'] ?? 'proctor_violation';
                AntiCheatLog::create([
                    'attempt_id' => $attempt->id,
                    'student_id' => $student->id,
                    'event_type' => $rawType,
                    'event_data' => [
                        'violation_type' => $rawType,
                        'summary' => $v['message'] ?? $summary,
                        'detections' => $detections,
                        'similarity' => $faceSimilarity,
                        'person_count' => $personCount,
                        'snapshot_id' => $snapshot->id,
                        'snapshot_url' => route('secure.media.snapshot', $snapshot->id),
                    ],
                    'snapshot_path' => $fileName,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'occurred_at' => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'violations' => $violations,
            'summary' => $summary,
            'cheat_warnings' => $attempt->cheat_warnings,
            'captured_at' => now()->format('H:i:s'),
        ]);
    }
}
