<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:admin,teacher,student'],
        ]);

        // We attempt to log in using only email and password
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            
            $user = Auth::user();
            
            // Validate that the chosen role matches the user's role in DB
            if ($user->role !== $credentials['role']) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'role' => 'The selected role does not match your account.',
                ])->onlyInput('email', 'role');
            }

            // Authentication and role validation passed
            $request->session()->regenerate();

            if ($user->role === 'teacher') {
                return redirect()->intended(route('teacher.dashboard'));
            } elseif ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'student') {
                if (!$user->face_registered) {
                    return redirect()->route('student.face.register')
                        ->with('info', 'Chào mừng bạn! Vui lòng hoàn tất đăng ký khuôn mặt để kích hoạt tài khoản thi.');
                }
                return redirect()->intended(route('student.dashboard'));
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email', 'role');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
