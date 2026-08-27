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
}
