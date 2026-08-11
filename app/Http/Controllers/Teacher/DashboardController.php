<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the teacher dashboard.
     */
    public function index()
    {
        return view('teacher.dashboard');
    }
}
