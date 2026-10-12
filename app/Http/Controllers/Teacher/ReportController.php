<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the teacher reports and statistics dashboard.
     */
    public function index(Request $request)
    {
        $teacherId = auth()->id();

        // 1. Get classes managed or taught by this teacher
        $homeroomClassIds = SchoolClass::where('teacher_id', $teacherId)->pluck('id')->toArray();
        $subjectClassIds = DB::table('class_subject_teacher')->where('teacher_id', $teacherId)->pluck('class_id')->toArray();
        $allClassIds = array_values(array_unique(array_merge($homeroomClassIds, $subjectClassIds)));

        $classes = SchoolClass::whereIn('id', $allClassIds)
            ->with(['course'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        // Selected class (default to first class if available)
        $selectedClassId = $request->input('class_id');
        $selectedClass = null;
        if ($selectedClassId && $classes->contains('id', $selectedClassId)) {
            $selectedClass = $classes->firstWhere('id', $selectedClassId);
        } elseif ($classes->isNotEmpty()) {
            $selectedClass = $classes->first();
            $selectedClassId = $selectedClass->id;
        }

        // Students in the selected class
        $classStudents = $selectedClass ? $selectedClass->students()->orderBy('name')->get() : collect();
        $classStudentIds = $classStudents->pluck('id')->toArray();

        // 2. Get exams related to this teacher or assigned to this class
        $assignedExamIds = [];
        if ($selectedClassId) {
            $assignedExamIds = ExamAssignment::where('class_id', $selectedClassId)->pluck('exam_id')->toArray();
        } elseif (!empty($allClassIds)) {
            $assignedExamIds = ExamAssignment::whereIn('class_id', $allClassIds)->pluck('exam_id')->toArray();
        }

        $allTeacherExamIds = Exam::where('created_by', $teacherId)
            ->orWhereIn('id', $assignedExamIds)
            ->pluck('id')
            ->toArray();

        $exams = Exam::whereIn('id', $allTeacherExamIds)
            ->with(['subject', 'category'])
            ->orderBy('start_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Selected Exam for score distribution (default to latest or first)
        $selectedExamId = $request->input('exam_id');
        $selectedExam = null;
        if ($selectedExamId && $exams->contains('id', $selectedExamId)) {
            $selectedExam = $exams->firstWhere('id', $selectedExamId);
        } elseif ($exams->isNotEmpty()) {
            $selectedExam = $exams->last(); // prefer the most recent exam
            $selectedExamId = $selectedExam->id;
        }

        // Selected Student for individual progress
        $selectedStudentId = $request->input('student_id');
        if ($selectedStudentId && !$classStudents->contains('id', $selectedStudentId)) {
            $selectedStudentId = null;
        }
        if (!$selectedStudentId && $classStudents->isNotEmpty()) {
            $selectedStudentId = $classStudents->first()->id;
        }
        $selectedStudent = $classStudents->firstWhere('id', $selectedStudentId);

        // =========================================================================
        // SECTION 1: THỐNG KÊ SỐ LƯỢNG SINH VIÊN THI THEO BÀI (Attendance & Performance)
        // =========================================================================
        $examStats = [];
        $examComparisonChart = [
            'labels' => [],
            'attended' => [],
            'assigned' => [],
            'average_scores' => [],
            'pass_rates' => [],
        ];

        foreach ($exams as $exam) {
            // Count assigned students
            $assignedCount = 0;
            if ($selectedClassId) {
                $assignedCount = ExamAssignment::where('exam_id', $exam->id)
                    ->where('class_id', $selectedClassId)
                    ->count();
                if ($assignedCount === 0) {
                    $assignedCount = $classStudents->count();
                }
            } else {
                $assignedCount = ExamAssignment::where('exam_id', $exam->id)->count();
                if ($assignedCount === 0) {
                    $assignedCount = $classes->sum('students_count');
                }
            }

            // Get submitted attempts
            $attemptsQuery = ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'submitted');
            if ($selectedClassId && !empty($classStudentIds)) {
                $attemptsQuery->whereIn('student_id', $classStudentIds);
            }
            $submittedAttempts = $attemptsQuery->get();

            // Best score per student
            $studentBestScores = $submittedAttempts->groupBy('student_id')
                ->map(fn($group) => (float) $group->max('score_value'));

            $attendedCount = $studentBestScores->count();
            $unattendedCount = max(0, $assignedCount - $attendedCount);
            $turnoutRate = $assignedCount > 0 ? round(($attendedCount / $assignedCount) * 100, 1) : 0;
            $avgScore = $attendedCount > 0 ? round($studentBestScores->avg(), 2) : 0;
            $maxScore = $attendedCount > 0 ? round($studentBestScores->max(), 2) : 0;
            $minScore = $attendedCount > 0 ? round($studentBestScores->min(), 2) : 0;
            $passedCount = $studentBestScores->filter(fn($score) => $score >= 5.0)->count();
            $passRate = $attendedCount > 0 ? round(($passedCount / $attendedCount) * 100, 1) : 0;

            $examStats[] = [
                'exam' => $exam,
                'assigned_count' => $assignedCount,
                'attended_count' => $attendedCount,
                'unattended_count' => $unattendedCount,
                'turnout_rate' => $turnoutRate,
                'avg_score' => $avgScore,
                'max_score' => $maxScore,
                'min_score' => $minScore,
                'passed_count' => $passedCount,
                'pass_rate' => $passRate,
                'total_attempts' => $submittedAttempts->count(),
            ];

            // Chart data
            $examComparisonChart['labels'][] = $exam->title;
            $examComparisonChart['attended'][] = $attendedCount;
            $examComparisonChart['assigned'][] = $assignedCount;
            $examComparisonChart['average_scores'][] = $avgScore;
            $examComparisonChart['pass_rates'][] = $passRate;
        }

        // =========================================================================
        // SECTION 2: SƠ ĐỒ ĐẠT ĐƯỢC ĐIỂM TỪ THẤP ĐẾN CAO CỦA CẢ LỚP (Ascending Score Curve)
        // =========================================================================
        $classAscendingRanking = [];
        $ascendingChart = [
            'labels' => [],
            'scores' => [],
            'student_names' => [],
            'average_line' => 0,
            'pass_mark' => 5.0,
        ];

        // Distribution brackets
        $scoreDistribution = [
            'kem' => 0,       // 0 - < 3.5
            'yeu' => 0,       // 3.5 - < 5.0
            'trung_binh' => 0,// 5.0 - < 6.5
            'kha' => 0,       // 6.5 - < 8.0
            'gioi' => 0,      // 8.0 - < 9.0
            'xuat_sac' => 0,  // 9.0 - 10.0
        ];

        if ($selectedExam) {
            $studentScoreItems = [];

            foreach ($classStudents as $student) {
                $bestAttempt = ExamAttempt::where('exam_id', $selectedExam->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'submitted')
                    ->orderByDesc('score_value')
                    ->first();

                if ($bestAttempt) {
                    $score = (float) $bestAttempt->score_value;
                    $studentScoreItems[] = [
                        'student' => $student,
                        'score' => $score,
                        'correct_answers' => $bestAttempt->correct_answers ?? 0,
                        'wrong_answers' => $bestAttempt->wrong_answers ?? 0,
                        'unanswered' => $bestAttempt->unanswered ?? 0,
                        'submitted_at' => $bestAttempt->submitted_at,
                        'has_taken' => true,
                    ];

                    // Classify into brackets
                    if ($score < 3.5) {
                        $scoreDistribution['kem']++;
                    } elseif ($score < 5.0) {
                        $scoreDistribution['yeu']++;
                    } elseif ($score < 6.5) {
                        $scoreDistribution['trung_binh']++;
                    } elseif ($score < 8.0) {
                        $scoreDistribution['kha']++;
                    } elseif ($score < 9.0) {
                        $scoreDistribution['gioi']++;
                    } else {
                        $scoreDistribution['xuat_sac']++;
                    }
                } else {
                    $studentScoreItems[] = [
                        'student' => $student,
                        'score' => 0.0,
                        'correct_answers' => 0,
                        'wrong_answers' => 0,
                        'unanswered' => 0,
                        'submitted_at' => null,
                        'has_taken' => false,
                    ];
                }
            }

            // Sort ascending: TỪ THẤP ĐẾN CAO
            usort($studentScoreItems, function ($a, $b) {
                if ($a['has_taken'] !== $b['has_taken']) {
                    return $a['has_taken'] ? 1 : -1;
                }
                return $a['score'] <=> $b['score'];
            });

            $classAscendingRanking = $studentScoreItems;

            // Compute taken scores average
            $takenScores = array_filter(array_column($studentScoreItems, 'score'), function ($k) use ($studentScoreItems) {
                return $studentScoreItems[$k]['has_taken'] ?? false;
            }, ARRAY_FILTER_USE_KEY);

            $classAvg = count($takenScores) > 0 ? round(array_sum($takenScores) / count($takenScores), 2) : 0;
            $ascendingChart['average_line'] = $classAvg;

            $rankIndex = 1;
            foreach ($studentScoreItems as $item) {
                if ($item['has_taken']) {
                    $ascendingChart['labels'][] = "Top #{$rankIndex}";
                    $ascendingChart['scores'][] = $item['score'];
                    $ascendingChart['student_names'][] = $item['student']->name . " (" . ($item['student']->code ?? 'SV') . ")";
                    $rankIndex++;
                }
            }
        }

        // =========================================================================
        // SECTION 3: THỐNG KÊ ĐIỂM TIẾN BỘ CỦA SINH VIÊN THEO TỪNG BÀI THI (Progress Tracking)
        // =========================================================================
        // Exams in chronological order
        $chronologicalExams = $exams->sortBy('start_at')->values();

        // Class Progress Matrix: Each student with their scores across all chronological exams
        $studentsProgressMatrix = [];
        $classAveragesPerExam = [];

        foreach ($chronologicalExams as $exam) {
            $examAttempts = ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'submitted')
                ->whereIn('student_id', $classStudentIds)
                ->get()
                ->groupBy('student_id');

            $scoresList = [];
            foreach ($examAttempts as $sId => $group) {
                $scoresList[] = (float) $group->max('score_value');
            }
            $classAveragesPerExam[$exam->id] = count($scoresList) > 0 ? round(array_sum($scoresList) / count($scoresList), 2) : 0;
        }

        foreach ($classStudents as $student) {
            $scoresByExam = [];
            $scoresSequence = [];

            foreach ($chronologicalExams as $exam) {
                $bestAttempt = ExamAttempt::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'submitted')
                    ->orderByDesc('score_value')
                    ->first();

                if ($bestAttempt) {
                    $sc = (float) $bestAttempt->score_value;
                    $scoresByExam[$exam->id] = $sc;
                    $scoresSequence[] = $sc;
                } else {
                    $scoresByExam[$exam->id] = null;
                }
            }

            // Calculate progress metrics
            $initialScore = null;
            $latestScore = null;
            $scoreDelta = 0;
            $statusText = 'Chưa thi';
            $statusType = 'neutral'; // success, info, neutral, warning, danger

            if (!empty($scoresSequence)) {
                $initialScore = $scoresSequence[0];
                $latestScore = end($scoresSequence);
                $scoreDelta = round($latestScore - $initialScore, 2);

                if (count($scoresSequence) === 1) {
                    $statusText = 'Mới hoàn thành 1 bài';
                    $statusType = 'info';
                } elseif ($scoreDelta >= 2.0) {
                    $statusText = 'Bứt phá vượt bậc (+' . $scoreDelta . ')';
                    $statusType = 'success';
                } elseif ($scoreDelta >= 0.5) {
                    $statusText = 'Có tiến bộ (+' . $scoreDelta . ')';
                    $statusType = 'primary';
                } elseif ($scoreDelta >= -0.5) {
                    $statusText = 'Giữ vững phong độ';
                    $statusType = 'neutral';
                } else {
                    $statusText = 'Cần chú ý (' . $scoreDelta . ')';
                    $statusType = 'danger';
                }
            }

            $studentsProgressMatrix[] = [
                'student' => $student,
                'scores_by_exam' => $scoresByExam,
                'initial_score' => $initialScore,
                'latest_score' => $latestScore,
                'highest_score' => !empty($scoresSequence) ? max($scoresSequence) : null,
                'average_score' => !empty($scoresSequence) ? round(array_sum($scoresSequence) / count($scoresSequence), 2) : null,
                'score_delta' => $scoreDelta,
                'status_text' => $statusText,
                'status_type' => $statusType,
            ];
        }

        // Selected student progress chart data
        $studentProgressChart = [
            'labels' => [],
            'student_scores' => [],
            'class_averages' => [],
            'student_name' => $selectedStudent ? $selectedStudent->name : '',
        ];

        if ($selectedStudent) {
            foreach ($chronologicalExams as $exam) {
                $studentProgressChart['labels'][] = $exam->title;

                $bestAttempt = ExamAttempt::where('exam_id', $exam->id)
                    ->where('student_id', $selectedStudent->id)
                    ->where('status', 'submitted')
                    ->orderByDesc('score_value')
                    ->first();

                $studentProgressChart['student_scores'][] = $bestAttempt ? (float) $bestAttempt->score_value : null;
                $studentProgressChart['class_averages'][] = $classAveragesPerExam[$exam->id] ?? 0;
            }
        }

        // =========================================================================
        // SECTION 4: KPI SUMMARY CARDS
        // =========================================================================
        $kpi = [
            'total_exams' => $exams->count(),
            'total_assigned' => array_sum(array_column($examStats, 'assigned_count')),
            'total_attended' => array_sum(array_column($examStats, 'attended_count')),
            'total_submissions' => array_sum(array_column($examStats, 'total_attempts')),
            'average_turnout' => count($examStats) > 0 ? round(array_sum(array_column($examStats, 'turnout_rate')) / count($examStats), 1) : 0,
            'average_class_score' => count($examStats) > 0 ? round(array_sum(array_column($examStats, 'avg_score')) / count($examStats), 2) : 0,
            'average_pass_rate' => count($examStats) > 0 ? round(array_sum(array_column($examStats, 'pass_rate')) / count($examStats), 1) : 0,
        ];

        return view('teacher.reports.index', compact(
            'classes',
            'selectedClass',
            'selectedClassId',
            'exams',
            'selectedExam',
            'selectedExamId',
            'classStudents',
            'selectedStudent',
            'selectedStudentId',
            'examStats',
            'examComparisonChart',
            'classAscendingRanking',
            'ascendingChart',
            'scoreDistribution',
            'chronologicalExams',
            'studentsProgressMatrix',
            'studentProgressChart',
            'classAveragesPerExam',
            'kpi'
        ));
    }

    /**
     * Export student reports and progress data to CSV.
     */
    public function exportCsv(Request $request)
    {
        $classId = $request->input('class_id');
        $class = SchoolClass::with('students')->findOrFail($classId);

        $exams = Exam::whereHas('assignments', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        })
        ->orderBy('start_at')
        ->get();

        $students = $class->students()->orderBy('name')->get();

        $filename = "bao_cao_lop_" . \Str::slug($class->name) . "_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($class, $exams, $students) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for proper Vietnamese display in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Class Header
            fputcsv($file, ['BÁO CÁO KẾT QUẢ VÀ TIẾN BỘ HỌC TẬP']);
            fputcsv($file, ['Lớp học:', $class->name . " ({$class->code})"]);
            fputcsv($file, ['Ngày xuất báo cáo:', date('d/m/Y H:i')]);
            fputcsv($file, []);

            // Table Header
            $headerRow = ['STT', 'Mã Sinh Viên', 'Họ và Tên'];
            foreach ($exams as $exam) {
                $headerRow[] = $exam->title;
            }
            $headerRow[] = 'Điểm Ban Đầu';
            $headerRow[] = 'Điểm Gần Nhất';
            $headerRow[] = 'Mức Tiến Bộ (Δ)';
            $headerRow[] = 'Đánh Giá Phong Độ';

            fputcsv($file, $headerRow);

            $index = 1;
            foreach ($students as $student) {
                $row = [
                    $index++,
                    $student->code ?? 'SV' . $student->id,
                    $student->name,
                ];

                $scoresSequence = [];
                foreach ($exams as $exam) {
                    $att = ExamAttempt::where('exam_id', $exam->id)
                        ->where('student_id', $student->id)
                        ->where('status', 'submitted')
                        ->orderByDesc('score_value')
                        ->first();

                    if ($att) {
                        $sc = (float) $att->score_value;
                        $row[] = number_format($sc, 1);
                        $scoresSequence[] = $sc;
                    } else {
                        $row[] = 'Chưa thi';
                    }
                }

                if (!empty($scoresSequence)) {
                    $initial = $scoresSequence[0];
                    $latest = end($scoresSequence);
                    $delta = round($latest - $initial, 2);

                    $row[] = number_format($initial, 1);
                    $row[] = number_format($latest, 1);
                    $row[] = ($delta > 0 ? "+{$delta}" : "{$delta}");

                    if (count($scoresSequence) === 1) {
                        $eval = 'Mới làm 1 bài';
                    } elseif ($delta >= 2.0) {
                        $eval = 'Tiến bộ vượt bậc';
                    } elseif ($delta >= 0.5) {
                        $eval = 'Có tiến bộ';
                    } elseif ($delta >= -0.5) {
                        $eval = 'Ổn định';
                    } else {
                        $eval = 'Cần chú ý / Giảm sút';
                    }
                    $row[] = $eval;
                } else {
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = 'Chưa tham gia thi';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
