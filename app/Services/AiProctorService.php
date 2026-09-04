<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiProctorService
{
    protected string $baseUrl;
    protected float $defaultThreshold;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ai_proctor.url', 'http://127.0.0.1:5001'), '/');
        $this->defaultThreshold = (float) config('services.ai_proctor.threshold', 65.0);
    }

    /**
     * Check if AI Microservice is healthy and ready.
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/health");
            return $response->successful() && ($response->json('status') === 'healthy');
        } catch (\Throwable $e) {
            Log::warning("AI Proctor Service health check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract 512-D ArcFace embedding from single or multiple angle images.
     *
     * @param array $images Base64 encoded images [front, left, right]
     * @return array [ 'success' => bool, 'embedding' => array|null, 'message' => string ]
     */
    public function extractFaceEmbedding(array $images): array
    {
        try {
            $response = Http::timeout(15)->post("{$this->baseUrl}/api/face/extract-embedding", [
                'images' => $images,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'embedding' => $response->json('embedding'),
                    'angles' => $response->json('angles', []),
                    'message' => 'Trích xuất đặc trưng khuôn mặt thành công!',
                ];
            }

            return [
                'success' => false,
                'embedding' => null,
                'message' => $response->json('message') ?? 'Không tìm thấy khuôn mặt hợp lệ. Vui lòng căn chỉnh lại camera.',
            ];
        } catch (\Throwable $e) {
            Log::error("Face embedding extraction error: " . $e->getMessage());
            return [
                'success' => false,
                'embedding' => null,
                'message' => 'Lỗi kết nối dịch vụ AI. Vui lòng thử lại sau giây lát.',
            ];
        }
    }

    /**
     * Verify pre-exam face scan against registered student ArcFace embedding.
     *
     * @param string $probeImage Base64 encoded probe scan
     * @param array $enrolledEmbedding 512-D float array
     * @param float|null $threshold Minimum similarity percentage (default 70.0)
     * @return array [ 'success' => bool, 'matched' => bool, 'similarity' => float, 'message' => string ]
     */
    public function verifyFace(string $probeImage, array $enrolledEmbedding, ?float $threshold = null, ?string $enrolledImage = null): array
    {
        $threshold = $threshold ?? $this->defaultThreshold;

        try {
            $payload = [
                'image' => $probeImage,
                'enrolled_embedding' => $enrolledEmbedding,
                'threshold' => $threshold,
            ];
            if (!empty($enrolledImage)) {
                $payload['enrolled_image'] = $enrolledImage;
            }

            $response = Http::timeout(10)->post("{$this->baseUrl}/api/face/verify", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'matched' => (bool) ($data['matched'] ?? false),
                    'similarity' => (float) ($data['similarity'] ?? 0),
                    'threshold' => $threshold,
                    'message' => $data['message'] ?? '',
                    'bbox' => $data['bbox'] ?? null,
                ];
            }

            return [
                'success' => false,
                'matched' => false,
                'similarity' => 0.0,
                'threshold' => $threshold,
                'message' => $response->json('message') ?? $response->json('error') ?? 'Không thể phân tích khuôn mặt.',
            ];
        } catch (\Throwable $e) {
            Log::error("Face verification error: " . $e->getMessage());
            return [
                'success' => false,
                'matched' => false,
                'similarity' => 0.0,
                'threshold' => $threshold,
                'message' => 'Lỗi kết nối dịch vụ AI xác minh. Vui lòng thử lại.',
            ];
        }
    }

    /**
     * Directly compare two face images using InsightFace ArcFace model.
     */
    public function compareFaces(string $image1, string $image2, ?float $threshold = 70.0): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/api/face/compare", [
                'image1' => $image1,
                'image2' => $image2,
                'threshold' => $threshold,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'matched' => false,
                'similarity' => 0.0,
                'message' => $response->json('message') ?? 'Không thể so sánh 2 ảnh.',
            ];
        } catch (\Throwable $e) {
            Log::error("Direct face comparison error: " . $e->getMessage());
            return [
                'success' => false,
                'matched' => false,
                'similarity' => 0.0,
                'message' => 'Lỗi kết nối dịch vụ AI so sánh khuôn mặt.',
            ];
        }
    }

    /**
     * Analyze in-exam proctoring snapshot with YOLOv8 & ArcFace.
     *
     * @param string $snapshotImage Base64 encoded webcam image
     * @param array|null $enrolledEmbedding Registered face embedding (optional for face match)
     * @param float|null $threshold
     * @return array
     */
    public function analyzeProctorSnapshot(string $snapshotImage, ?array $enrolledEmbedding = null, ?float $threshold = null, ?string $verificationImage = null): array
    {
        $threshold = $threshold ?? $this->defaultThreshold;

        try {
            $payload = [
                'image' => $snapshotImage,
                'enrolled_embedding' => $enrolledEmbedding,
                'threshold' => $threshold,
            ];
            if (!empty($verificationImage)) {
                $payload['verification_image'] = $verificationImage;
            }

            $response = Http::timeout(10)->post("{$this->baseUrl}/api/proctor/analyze", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("Proctor analyze error response: " . $response->body());
        } catch (\Throwable $e) {
            Log::error("Proctor analyze connection error: " . $e->getMessage());
        }

        // Return fallback normal status on temporary network glitch so exam continues smoothly
        return [
            'status' => 'normal',
            'violations' => [],
            'detections' => [],
            'person_count' => 1,
            'face_similarity' => null,
            'face_matched' => true,
            'summary' => 'Ảnh giám sát đã được ghi nhận.',
            'fallback' => true,
        ];
    }
}
