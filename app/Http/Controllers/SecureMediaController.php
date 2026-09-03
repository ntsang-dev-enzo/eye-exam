<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExamAttempt;
use App\Models\ExamProctorSnapshot;
use App\Models\AntiCheatLog;
use App\Services\SecureMediaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureMediaController extends Controller
{
    /**
     * Stream an encrypted exam proctor snapshot to an authorized viewer.
     */
    public function streamSnapshot(Request $request, ExamProctorSnapshot $snapshot): Response
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Chưa đăng nhập.');
        }

        // Authorization check: Admin, Student owner, or Exam Creator (Teacher)
        $isAdmin = $user->role === 'admin';
        $isStudentOwner = $user->id === $snapshot->student_id;
        $isExamTeacher = $snapshot->attempt && $snapshot->attempt->exam && $snapshot->attempt->exam->created_by === $user->id;

        if (!$isAdmin && !$isStudentOwner && !$isExamTeacher) {
            abort(403, 'Bạn không có quyền xem ảnh giám sát này.');
        }

        $binary = SecureMediaService::getDecrypted($snapshot->image_path);
        if (!$binary) {
            abort(404, 'Không tìm thấy dữ liệu ảnh giám sát.');
        }

        return response($binary, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="snapshot_' . $snapshot->id . '.jpg"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream the pre-exam face verification photo.
     */
    public function streamVerification(Request $request, ExamAttempt $attempt): Response
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Chưa đăng nhập.');
        }

        $isAdmin = $user->role === 'admin';
        $isStudentOwner = $user->id === $attempt->student_id;
        $isExamTeacher = $attempt->exam && $attempt->exam->created_by === $user->id;

        if (!$isAdmin && !$isStudentOwner && !$isExamTeacher) {
            abort(403, 'Bạn không có quyền xem ảnh xác thực này.');
        }

        $binary = SecureMediaService::getDecrypted($attempt->verification_image);
        if (!$binary) {
            abort(404, 'Không tìm thấy dữ liệu ảnh xác thực.');
        }

        return response($binary, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="verify_' . $attempt->id . '.jpg"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream user avatar uploaded via Admin (separate from Face ID verification photo).
     */
    public function streamAvatar(Request $request, User $targetUser): Response
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(401, 'Chưa đăng nhập.');
        }

        if (empty($targetUser->avatar)) {
            abort(404, 'Người dùng chưa có ảnh đại diện.');
        }

        $binary = SecureMediaService::getDecrypted($targetUser->avatar);
        if (!$binary) {
            abort(404, 'Không tìm thấy tệp ảnh đại diện.');
        }

        return response($binary, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="avatar_' . $targetUser->id . '.jpg"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream the enrolled Face ID profile photo of a user.
     */
    public function streamFace(Request $request, User $targetUser): Response
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            abort(401, 'Chưa đăng nhập.');
        }

        // Authorization: Admin, Self, or Authorized Teacher (who teaches this student's exam or class)
        $isAdmin = $currentUser->role === 'admin';
        $isSelf = $currentUser->id === $targetUser->id;
        $isAuthorizedTeacher = false;

        if ($currentUser->role === 'teacher') {
            $hasExam = \App\Models\ExamAttempt::where('student_id', $targetUser->id)
                ->whereHas('exam', function ($q) use ($currentUser) {
                    $q->where('created_by', $currentUser->id);
                })->exists();

            $hasClass = \App\Models\SchoolClass::where('teacher_id', $currentUser->id)
                ->whereHas('students', function ($q) use ($targetUser) {
                    $q->where('users.id', $targetUser->id);
                })->exists();

            $isAuthorizedTeacher = $hasExam || $hasClass;
        }

        if (!$isAdmin && !$isSelf && !$isAuthorizedTeacher) {
            abort(403, 'Bạn không có quyền xem ảnh chân dung của tài khoản này.');
        }

        $angle = $request->query('angle', 'frontal');
        $path = null;
        if (!empty($targetUser->face_images)) {
            $path = $targetUser->face_images[$angle] ?? $targetUser->face_images['frontal'] ?? null;
        }
        if (!$path) {
            $path = $targetUser->frontal_face_path;
        }

        if (!$path) {
            abort(404, 'Người dùng chưa có dữ liệu ảnh Face ID.');
        }

        $binary = SecureMediaService::getDecrypted($path);
        if (!$binary) {
            abort(404, 'Không tìm thấy tệp ảnh Face ID.');
        }

        return response($binary, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="face_' . $targetUser->id . '_' . $angle . '.jpg"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream an evidence snapshot attached to an anti-cheat log.
     */
    public function streamLogSnapshot(Request $request, AntiCheatLog $log): Response
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Chưa đăng nhập.');
        }

        $isAdmin = $user->role === 'admin';
        $isStudentOwner = $user->id === $log->student_id;
        $isExamTeacher = $log->attempt && $log->attempt->exam && $log->attempt->exam->created_by === $user->id;

        if (!$isAdmin && !$isStudentOwner && !$isExamTeacher) {
            abort(403, 'Bạn không có quyền xem ảnh bằng chứng này.');
        }

        $binary = SecureMediaService::getDecrypted($log->snapshot_path);
        if (!$binary) {
            abort(404, 'Không tìm thấy tệp ảnh bằng chứng.');
        }

        return response($binary, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="evidence_' . $log->id . '.jpg"',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
