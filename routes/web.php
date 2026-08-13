<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login']);
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Admin routes
    Route::prefix('quan-tri')->name('admin.')->group(function () {
        Route::get('/tong-quan', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Subjects
        Route::get('/mon-hoc', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/mon-hoc/tao-moi', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/mon-hoc', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/mon-hoc/{subject}/sua', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/mon-hoc/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('subjects.update');

        // Classes
        Route::get('/lop-hoc', [\App\Http\Controllers\Admin\ClassController::class, 'index'])->name('classes.index');
        Route::get('/lop-hoc/tao-moi', [\App\Http\Controllers\Admin\ClassController::class, 'create'])->name('classes.create');
        Route::post('/lop-hoc', [\App\Http\Controllers\Admin\ClassController::class, 'store'])->name('classes.store');
        Route::get('/lop-hoc/{class}/sua', [\App\Http\Controllers\Admin\ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/lop-hoc/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'update'])->name('classes.update');
        Route::get('/lop-hoc/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'show'])->name('classes.show');
        Route::post('/lop-hoc/{class}/assign', [\App\Http\Controllers\Admin\ClassController::class, 'assign'])->name('classes.assign');
        Route::delete('/lop-hoc/{class}/assign/{subject_id}', [\App\Http\Controllers\Admin\ClassController::class, 'removeAssign'])->name('classes.remove-assign');

        // Exams
        Route::get('/de-thi', [\App\Http\Controllers\Admin\ExamController::class, 'index'])->name('exams.index');
        Route::get('/de-thi/{exam}/giam-sat', [\App\Http\Controllers\Admin\ExamController::class, 'monitor'])->name('exams.monitor');
        Route::get('/de-thi/{exam}/api-monitor', [\App\Http\Controllers\Admin\ExamController::class, 'apiMonitor'])->name('exams.api-monitor');
    });

    // Teacher routes
    Route::prefix('giang-vien')->name('teacher.')->group(function () {
        Route::get('/tong-quan', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        
        // Questions API
        Route::get('/api/cau-hoi', [\App\Http\Controllers\Teacher\QuestionController::class, 'apiIndex'])->name('api.questions.index');
        Route::post('/api/cau-hoi', [\App\Http\Controllers\Teacher\QuestionController::class, 'apiStore'])->name('api.questions.store');
        
        // Questions
        Route::get('/cau-hoi', [\App\Http\Controllers\Teacher\QuestionController::class, 'index'])->name('questions.index');
        Route::get('/cau-hoi/tao-moi', [\App\Http\Controllers\Teacher\QuestionController::class, 'create'])->name('questions.create');
        Route::post('/cau-hoi', [\App\Http\Controllers\Teacher\QuestionController::class, 'store'])->name('questions.store');
        
        // Exams
        Route::get('/de-thi', [\App\Http\Controllers\Teacher\ExamController::class, 'index'])->name('exams.index');
        Route::get('/de-thi/tao-moi', [\App\Http\Controllers\Teacher\ExamController::class, 'create'])->name('exams.create');
        Route::post('/de-thi', [\App\Http\Controllers\Teacher\ExamController::class, 'store'])->name('exams.store');
        Route::get('/de-thi/{exam}/sua', [\App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/de-thi/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'update'])->name('exams.update');
        Route::patch('/de-thi/{exam}/trang-thai', [\App\Http\Controllers\Teacher\ExamController::class, 'updateStatus'])->name('exams.update-status');
        Route::get('/de-thi/{exam}/ket-qua', [\App\Http\Controllers\Teacher\ExamController::class, 'results'])->name('exams.results');
        Route::get('/de-thi/{exam}/giam-sat', [\App\Http\Controllers\Teacher\ExamController::class, 'monitor'])->name('exams.monitor');
        Route::get('/de-thi/{exam}/api-monitor', [\App\Http\Controllers\Teacher\ExamController::class, 'apiMonitor'])->name('exams.api-monitor');
        
        // Classes
        Route::get('/lop-hoc', [\App\Http\Controllers\Teacher\ClassController::class, 'index'])->name('classes.index');
        Route::get('/lop-hoc/{class}', [\App\Http\Controllers\Teacher\ClassController::class, 'show'])->name('classes.show');

        // Assignments
        Route::get('/giao-de-thi', [\App\Http\Controllers\Teacher\AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/giao-de-thi', [\App\Http\Controllers\Teacher\AssignmentController::class, 'store'])->name('assignments.store');
    });

    // Student routes
    Route::prefix('sinh-vien')->name('student.')->middleware([\App\Http\Middleware\CheckActiveExam::class])->group(function () {
        Route::get('/tong-quan', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/ky-thi-cua-toi', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
        Route::post('/de-thi/tham-gia', [\App\Http\Controllers\Student\ExamController::class, 'join'])->name('exams.join');
        Route::get('/de-thi/{exam}/lam-bai', [\App\Http\Controllers\Student\ExamController::class, 'take'])->name('exams.take');
        Route::post('/de-thi/{exam}/nop-bai', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('exams.submit');
        Route::post('/attempt/{attempt}/cheat', [\App\Http\Controllers\Student\ExamController::class, 'cheat'])->name('exams.cheat');
        Route::get('/de-thi/{exam}/xem-lai', [\App\Http\Controllers\Student\ExamController::class, 'review'])->name('exams.review');
    });
});
//Homepage

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

