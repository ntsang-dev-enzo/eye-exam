<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\AiProctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaceAuthController extends Controller
{
    protected AiProctorService $aiProctor;

    public function __construct(AiProctorService $aiProctor)
    {
        $this->aiProctor = $aiProctor;
    }

    /**
     * Show the multi-angle face registration page.
     */
    public function showRegister()
    {
        $user = Auth::user();
        if ($user->face_registered) {
            return redirect()->route('student.dashboard')
                ->with('info', 'Hồ sơ khuôn mặt (Face ID) của bạn đã được đăng ký và bảo mật an toàn. Nếu cần chụp lại do lỗi hoặc thay đổi ngoại hình, vui lòng liên hệ Quản trị viên (Admin) để được cấp quyền.');
        }
        return view('student.face.register', compact('user'));
    }

    /**
     * Store registered face photos & ArcFace embedding.
     */
    public function storeRegister(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'frontal' => 'required|string',
            'left' => 'required|string',
            'right' => 'required|string',
        ], [
            'frontal.required' => 'Vui lòng chụp ảnh khuôn mặt nhìn thẳng.',
            'left.required' => 'Vui lòng chụp ảnh khuôn mặt nghiêng trái.',
            'right.required' => 'Vui lòng chụp ảnh khuôn mặt nghiêng phải.',
        ]);

        $images = [
            'frontal' => $request->input('frontal'),
            'left' => $request->input('left'),
            'right' => $request->input('right'),
        ];

        // Call AI service to extract combined 512D ArcFace embedding
        $result = $this->aiProctor->extractFaceEmbedding(array_values($images));

        if (!$result['success'] || empty($result['embedding'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Không thể trích xuất đặc trưng khuôn mặt. Vui lòng căn chỉnh ánh sáng và chụp lại.',
            ], 422);
        }

        // Save encrypted image files to private storage: storage/app/private/faces/{user_id}/
        $folder = "faces/{$user->id}";

        $savedPaths = [];
        foreach ($images as $angle => $b64) {
            $data = $b64;
            if (str_contains($data, ',')) {
                $data = explode(',', $data)[1];
            }
            $binary = base64_decode($data);
            $fileName = "{$folder}/{$angle}_" . time() . ".enc";
            \App\Services\SecureMediaService::storeEncrypted($fileName, $binary, 'local');
            $savedPaths[$angle] = $fileName;
        }

        // Update student record
        $user->update([
            'face_registered' => true,
            'face_embedding' => json_encode($result['embedding']),
            'face_images' => $savedPaths,
            'face_registered_at' => now(),
        ]);

        session()->flash('success', 'Đăng ký nhận diện khuôn mặt thành công! Bạn đã đủ điều kiện tham gia các kỳ thi.');

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký khuôn mặt thành công! Danh tính của bạn đã được lưu trữ an toàn.',
            'redirect_url' => route('student.dashboard'),
        ]);
    }

    /**
     * Verify student face before starting an exam (AJAX modal verification).
     * Requirements:
     * - Circular progress
     * - Threshold at least 70%
     * - Infinite retry attempts
     */
    public function verifyFace(Request $request, Exam $exam)
    {
        $user = Auth::user();

        if (!$user->face_registered || empty($user->face_embedding)) {
            return response()->json([
                'success' => false,
                'need_registration' => true,
                'message' => 'Bạn chưa cập nhật khuôn mặt trên hệ thống. Bắt buộc phải đăng ký khuôn mặt mới được tham gia thi.',
                'register_url' => route('student.face.register'),
            ], 403);
        }

        $request->validate([
            'image' => 'required|string',
        ]);

        $probeImage = $request->input('image');
        $enrolledEmbedding = is_string($user->face_embedding) ? json_decode($user->face_embedding, true) : $user->face_embedding;

        // Provide enrolled frontal face image for direct InsightFace ArcFace photo comparison
        $enrolledImageBase64 = null;
        if (!empty($user->face_images)) {
            $images = is_string($user->face_images) ? json_decode($user->face_images, true) : $user->face_images;
            $frontalPath = $images['frontal'] ?? null;
            if ($frontalPath) {
                $decrypted = \App\Services\SecureMediaService::getDecrypted($frontalPath);
                if ($decrypted) {
                    $enrolledImageBase64 = 'data:image/jpeg;base64,' . base64_encode($decrypted);
                }
            }
        }

        // Yêu cầu tỉ lệ xác minh khuôn mặt trên 65%
        $threshold = (float) config('services.ai_proctor.threshold', 65.0);
        $result = $this->aiProctor->verifyFace($probeImage, $enrolledEmbedding, $threshold, $enrolledImageBase64);

        if (!$result['success'] || !$result['matched'] || ($result['similarity'] ?? 0) <= 65.0) {
            $sim = $result['similarity'] ?? 0;
            return response()->json([
                'success' => false,
                'matched' => false,
                'similarity' => $sim,
                'threshold' => $threshold,
                'message' => $result['message'] ?: "Khuôn mặt không trùng khớp với ảnh đăng ký ({$sim}% < {$threshold}%). Yêu cầu đúng thí sinh và đạt tỉ lệ trên 65%!",
            ]);
        }

        // Find or create in_progress attempt
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if (!$attempt) {
            // Check max attempts
            if ($exam->max_attempts && $exam->max_attempts > 0) {
                $submittedCount = ExamAttempt::where('exam_id', $exam->id)
                    ->where('student_id', $user->id)
                    ->where('status', 'submitted')
                    ->count();

                if ($submittedCount >= $exam->max_attempts) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã hoàn thành tối đa ' . $exam->max_attempts . ' lần làm bài cho phép.',
                    ], 403);
                }
            }

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $user->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Save encrypted verification photo to private storage: storage/app/private/verification/{user_id}/
        $data = $probeImage;
        if (str_contains($data, ',')) {
            $data = explode(',', $data)[1];
        }
        $binary = base64_decode($data);
        $folder = "verification/{$user->id}";
        $verPath = "{$folder}/ver_attempt_{$attempt->id}_" . time() . ".enc";
        \App\Services\SecureMediaService::storeEncrypted($verPath, $binary, 'local');

        $sessionToken = Str::random(40);

        $attempt->update([
            'face_verified_at' => now(),
            'face_similarity' => $result['similarity'],
            'verification_image' => $verPath,
            'exam_session_token' => $sessionToken,
        ]);

        return response()->json([
            'success' => true,
            'matched' => true,
            'similarity' => $result['similarity'],
            'threshold' => $threshold,
            'message' => "Xác thực danh tính thành công! Độ tương đồng: {$result['similarity']}%",
            'redirect_url' => route('student.exams.take', $exam),
        ]);
    }
}
