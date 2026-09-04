<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\QuestionController as TeacherQuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\ClassController as TeacherClassController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\CategoryController as TeacherCategoryController;
use App\Http\Controllers\Teacher\DocumentController as TeacherDocumentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DocumentController as StudentDocumentController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login']);
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Admin routes (Protected by role:admin)
    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/tong-quan', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Account Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/reset-face', [AdminUserController::class, 'resetFaceId'])->name('users.reset-face');
        Route::post('/users/{user}/avatar', [AdminUserController::class, 'updateAvatar'])->name('users.update-avatar');
        Route::delete('/users/{user}/avatar', [AdminUserController::class, 'deleteAvatar'])->name('users.delete-avatar');
        
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
        Route::post('/lop-hoc/{class}/sinh-vien', [\App\Http\Controllers\Admin\ClassController::class, 'addStudent'])->name('classes.add-student');
        Route::delete('/lop-hoc/{class}/sinh-vien/{student}', [\App\Http\Controllers\Admin\ClassController::class, 'removeStudent'])->name('classes.remove-student');

        // Exams
        Route::get('/de-thi', [\App\Http\Controllers\Admin\ExamController::class, 'index'])->name('exams.index');
        Route::get('/de-thi/{exam}/giam-sat', [\App\Http\Controllers\Admin\ExamController::class, 'monitor'])->name('exams.monitor');
        Route::get('/de-thi/{exam}/api-monitor', [\App\Http\Controllers\Admin\ExamController::class, 'apiMonitor'])->name('exams.api-monitor');

        // Course Management (Khóa học) - Exact Vietnamese URLs
        Route::get('/khoa-hoc', [AdminCourseController::class, 'index'])->name('khoa-hoc.index');
        Route::get('/khoa-hoc/them-moi', [AdminCourseController::class, 'create'])->name('khoa-hoc.create');
        Route::post('/khoa-hoc', [AdminCourseController::class, 'store'])->name('khoa-hoc.store');
        Route::get('/khoa-hoc/{khoaHoc}', [AdminCourseController::class, 'show'])->name('khoa-hoc.show');
        Route::get('/khoa-hoc/{khoaHoc}/chinh-sua', [AdminCourseController::class, 'edit'])->name('khoa-hoc.edit');
        Route::put('/khoa-hoc/{khoaHoc}', [AdminCourseController::class, 'update'])->name('khoa-hoc.update');
        Route::delete('/khoa-hoc/{khoaHoc}', [AdminCourseController::class, 'destroy'])->name('khoa-hoc.destroy');
    });

    // Teacher routes (Protected by role:teacher)
    Route::prefix('giang-vien')->middleware('role:teacher')->name('teacher.')->group(function () {
        Route::get('/tong-quan', [TeacherDashboardController::class, 'index'])->name('dashboard');
        
        // Questions API
        Route::get('/api/cau-hoi', [TeacherQuestionController::class, 'apiIndex'])->name('api.questions.index');
        Route::post('/api/cau-hoi', [TeacherQuestionController::class, 'apiStore'])->name('api.questions.store');
        Route::get('/api/danh-muc', [TeacherQuestionController::class, 'apiCategories'])->name('api.categories.index');
        Route::post('/api/danh-muc', [TeacherQuestionController::class, 'apiCategoryStore'])->name('api.categories.store');
        
        // Categories (Danh mục môn học)
        Route::get('/danh-muc-mon-hoc', [TeacherCategoryController::class, 'index'])->name('categories.index');
        Route::post('/danh-muc-mon-hoc', [TeacherCategoryController::class, 'store'])->name('categories.store');
        Route::put('/danh-muc-mon-hoc/{category}', [TeacherCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/danh-muc-mon-hoc/{category}', [TeacherCategoryController::class, 'destroy'])->name('categories.destroy');

        // Questions
        Route::get('/cau-hoi', [TeacherQuestionController::class, 'index'])->name('questions.index');
        Route::get('/cau-hoi/tao-moi', [TeacherQuestionController::class, 'create'])->name('questions.create');
        Route::post('/cau-hoi', [TeacherQuestionController::class, 'store'])->name('questions.store');
        Route::post('/cau-hoi/chuyen-danh-muc', [TeacherQuestionController::class, 'bulkAssignCategory'])->name('questions.bulk-category');
        Route::get('/cau-hoi/{question}/sua', [TeacherQuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/cau-hoi/{question}', [TeacherQuestionController::class, 'update'])->name('questions.update');
        Route::delete('/cau-hoi/{question}', [TeacherQuestionController::class, 'destroy'])->name('questions.destroy');
        
        // Exams
        Route::get('/de-thi', [\App\Http\Controllers\Teacher\ExamController::class, 'index'])->name('exams.index');
        Route::get('/de-thi/tao-moi', [\App\Http\Controllers\Teacher\ExamController::class, 'create'])->name('exams.create');
        Route::post('/de-thi', [\App\Http\Controllers\Teacher\ExamController::class, 'store'])->name('exams.store');
        Route::get('/de-thi/{exam}/sua', [\App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/de-thi/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'update'])->name('exams.update');
        Route::delete('/de-thi/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'destroy'])->name('exams.destroy');
        Route::patch('/de-thi/{exam}/trang-thai', [\App\Http\Controllers\Teacher\ExamController::class, 'updateStatus'])->name('exams.update-status');
        Route::patch('/de-thi/{exam}/cai-dat-nhanh', [\App\Http\Controllers\Teacher\ExamController::class, 'updateQuickSettings'])->name('exams.update-quick-settings');
        Route::get('/de-thi/{exam}/ket-qua', [\App\Http\Controllers\Teacher\ExamController::class, 'results'])->name('exams.results');
        Route::get('/de-thi/{exam}/giam-sat', [\App\Http\Controllers\Teacher\ExamController::class, 'monitor'])->name('exams.monitor');
        Route::get('/de-thi/{exam}/api-monitor', [\App\Http\Controllers\Teacher\ExamController::class, 'apiMonitor'])->name('exams.api-monitor');
        Route::get('/de-thi/{exam}/sinh-vien/{attempt}/hanh-vi', [\App\Http\Controllers\Teacher\ExamController::class, 'studentBehavior'])->name('exams.student-behavior');

        // Classes
        Route::get('/lop-hoc', [TeacherClassController::class, 'index'])->name('classes.index');
        Route::get('/lop-hoc/{class}', [TeacherClassController::class, 'show'])->name('classes.show');

        // Assignments
        Route::get('/giao-de-thi', [TeacherAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/giao-de-thi', [TeacherAssignmentController::class, 'store'])->name('assignments.store');

        // Teacher Course View (Khóa học của tôi)
        Route::get('/khoa-hoc-cua-toi', [TeacherCourseController::class, 'index'])->name('khoa-hoc.index');

        // Study Materials (Tài liệu học tập)
        Route::get('/lop-hoc/{class}/mon-hoc/{subject}/tai-lieu', [TeacherDocumentController::class, 'index'])->name('classes.subjects.documents.index');
        Route::post('/lop-hoc/{class}/mon-hoc/{subject}/danh-muc', [TeacherDocumentController::class, 'storeCategory'])->name('classes.subjects.categories.store');
        Route::put('/danh-muc-tai-lieu/{category}', [TeacherDocumentController::class, 'updateCategory'])->name('document-categories.update');
        Route::delete('/danh-muc-tai-lieu/{category}', [TeacherDocumentController::class, 'destroyCategory'])->name('document-categories.destroy');

        Route::post('/lop-hoc/{class}/mon-hoc/{subject}/tai-lieu', [TeacherDocumentController::class, 'store'])->name('classes.subjects.documents.store');
        Route::put('/tai-lieu/{document}', [TeacherDocumentController::class, 'update'])->name('documents.update');
        Route::delete('/tai-lieu/{document}', [TeacherDocumentController::class, 'destroy'])->name('documents.destroy');
        Route::get('/tai-lieu/{document}/xem', [TeacherDocumentController::class, 'view'])->name('documents.view');
        Route::get('/tai-lieu/{document}/tai-ve', [TeacherDocumentController::class, 'download'])->name('documents.download');
    });

    // Student routes (Protected by role:student)
    Route::prefix('sinh-vien')->name('student.')->middleware(['role:student', \App\Http\Middleware\CheckActiveExam::class])->group(function () {
        Route::get('/tong-quan', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/ky-thi-cua-toi', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
        Route::post('/de-thi/tham-gia', [\App\Http\Controllers\Student\ExamController::class, 'join'])->name('exams.join');
        Route::get('/de-thi/{exam}/lam-bai', [\App\Http\Controllers\Student\ExamController::class, 'take'])->name('exams.take');
        Route::post('/de-thi/{exam}/nop-bai', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('exams.submit');
        Route::post('/de-thi/{exam}/roi-phong', [\App\Http\Controllers\Student\ExamController::class, 'leave'])->name('exams.leave');
        Route::post('/attempt/{attempt}/cheat', [\App\Http\Controllers\Student\ExamController::class, 'cheat'])->name('exams.cheat');
        Route::post('/attempt/{attempt}/save-answer', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('exams.save-answer');
        Route::post('/attempt/{attempt}/sync-offline', [\App\Http\Controllers\Student\ExamController::class, 'syncOffline'])->name('exams.sync-offline');
        Route::get('/de-thi/{exam}/xem-lai', [\App\Http\Controllers\Student\ExamController::class, 'review'])->name('exams.review');

        // Face ID & AI Proctoring routes
        Route::get('/dang-ky-khuon-mat', [\App\Http\Controllers\Student\FaceAuthController::class, 'showRegister'])->name('face.register');
        Route::post('/dang-ky-khuon-mat', [\App\Http\Controllers\Student\FaceAuthController::class, 'storeRegister'])->name('face.register.store');
        Route::post('/de-thi/{exam}/xac-thuc-khuon-mat', [\App\Http\Controllers\Student\FaceAuthController::class, 'verifyFace'])->name('exams.verify-face');
        Route::post('/attempt/{attempt}/proctor-snapshot', [\App\Http\Controllers\Student\ExamController::class, 'captureProctorSnapshot'])->name('exams.proctor-snapshot');

        // Student Classes View
        Route::get('/lop-hoc-cua-toi', [\App\Http\Controllers\Student\ClassController::class, 'index'])->name('classes.index');
        Route::get('/lop-hoc-cua-toi/{class}', [\App\Http\Controllers\Student\ClassController::class, 'show'])->name('classes.show');

        // Study Materials (Tài liệu học tập)
        Route::get('/lop-hoc-cua-toi/{class}/mon-hoc/{subject}/tai-lieu', [StudentDocumentController::class, 'index'])->name('classes.subjects.documents.index');
        Route::get('/tai-lieu/{document}/xem', [StudentDocumentController::class, 'view'])->name('documents.view');
        Route::get('/tai-lieu/{document}/tai-ve', [StudentDocumentController::class, 'download'])->name('documents.download');

        // Student Course View (Khóa học của tôi)
        Route::get('/khoa-hoc-cua-toi', [StudentCourseController::class, 'index'])->name('khoa-hoc.index');

        // Student Profile (Thông tin sinh viên)
        Route::get('/thong-tin', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
    });

    // =========================================================
    // SECURE ENCRYPTED MEDIA STREAMING (ROLE-BASED AUTHORIZATION)
    // =========================================================
    Route::prefix('secure-media')->name('secure.media.')->group(function () {
        Route::get('/snapshot/{snapshot}', [\App\Http\Controllers\SecureMediaController::class, 'streamSnapshot'])->name('snapshot');
        Route::get('/verification/{attempt}', [\App\Http\Controllers\SecureMediaController::class, 'streamVerification'])->name('verification');
        Route::get('/face/{targetUser}', [\App\Http\Controllers\SecureMediaController::class, 'streamFace'])->name('face');
        Route::get('/avatar/{targetUser}', [\App\Http\Controllers\SecureMediaController::class, 'streamAvatar'])->name('avatar');
        Route::get('/log/{log}', [\App\Http\Controllers\SecureMediaController::class, 'streamLogSnapshot'])->name('log');
    });
});
