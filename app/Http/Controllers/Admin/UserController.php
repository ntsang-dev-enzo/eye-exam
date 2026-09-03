<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of teacher and student accounts with search and filter capabilities.
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['teacher', 'student']);

        // Search by keyword (name, email, code)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by role (teacher, student)
        if ($request->filled('role') && in_array($request->input('role'), ['teacher', 'student'])) {
            $query->where('role', $request->input('role'));
        }

        // Filter by status (active, inactive, locked)
        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive', 'locked'])) {
            $query->where('status', $request->input('status'));
        }

        // Order newest accounts first and paginate with query params preserved
        $users = $query->latest('id')->paginate(12)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Reset Face ID registration for a student to allow re-registration if errors occur.
     */
    public function resetFaceId(User $user)
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Chỉ có thể đặt lại Face ID cho tài khoản sinh viên.');
        }

        // Remove old photos from storage if present
        if (!empty($user->face_images) && is_array($user->face_images)) {
            foreach ($user->face_images as $p) {
                \App\Services\SecureMediaService::delete($p);
            }
        }
        if ($user->frontal_face_path) {
            \App\Services\SecureMediaService::delete($user->frontal_face_path);
        }
        if ($user->left_face_path) {
            \App\Services\SecureMediaService::delete($user->left_face_path);
        }
        if ($user->right_face_path) {
            \App\Services\SecureMediaService::delete($user->right_face_path);
        }

        $user->update([
            'face_registered' => false,
            'face_registered_at' => null,
            'face_embedding' => null,
            'face_images' => null,
            'frontal_face_path' => null,
            'left_face_path' => null,
            'right_face_path' => null,
        ]);

        return back()->with('success', "Đã đặt lại Face ID cho sinh viên {$user->name} ({$user->code}). Sinh viên đã được cấp quyền chụp lại khuôn mặt mới.");
    }
}
