<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// File serving route (bypass symlink issues on LiteSpeed)
// Using /files/ instead of /storage/ to avoid Hostinger security blocks
Route::get('/files/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    // Security: prevent directory traversal
    if (strpos($path, '..') !== false || strpos($path, '//') !== false) {
        abort(403, 'Forbidden');
    }

    // Check if file exists
    if (!file_exists($fullPath) || !is_file($fullPath)) {
        abort(404, 'File not found');
    }

    // Get file extension
    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

    // Set appropriate content type
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'wav' => 'audio/wav',
        'webm' => 'video/webm',
        'ogg' => 'audio/ogg',
    ];

    $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

    // Return file with appropriate headers
    return response()->file($fullPath, [
        'Content-Type' => $contentType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('serve-storage');

// Redirect dashboard based on user role
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    switch ($user->role) {
        case 'student':
            return redirect()->route('student.dashboard');
        case 'teacher':
            return redirect()->route('teacher.dashboard');
        case 'admin':
            return redirect()->route('admin.dashboard');
        default:
            return redirect()->route('home');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Student Routes
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [StudentDashboardController::class, 'courses'])->name('courses');
    Route::get('/progress', [StudentDashboardController::class, 'progress'])->name('progress');

    // Course specific routes
    Route::get('/course/{course}', [StudentDashboardController::class, 'showCourse'])->name('course.show');
    Route::post('/course/{course}/enroll', [StudentDashboardController::class, 'enroll'])->name('course.enroll');
    Route::get('/lesson/{lesson}', [StudentDashboardController::class, 'showLesson'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [StudentDashboardController::class, 'completeLesson'])->name('lesson.complete');

    // Test routes
    Route::get('/test/{test}', [StudentDashboardController::class, 'showTest'])->name('test.show');
    Route::post('/test/{test}/start', [StudentDashboardController::class, 'startTest'])->name('test.start');
    Route::post('/test/{test}/submit', [StudentDashboardController::class, 'submitTest'])->name('test.submit');
    Route::get('/test/{test}/results/{attempt}', [StudentDashboardController::class, 'showTestResults'])->name('test.results');

    // Preview routes (accessible to admins and teachers)
    Route::get('/test/{test}/preview', [StudentDashboardController::class, 'previewTest'])->name('test.preview');
    Route::get('/lesson/{lesson}/preview', [StudentDashboardController::class, 'previewLesson'])->name('lesson.preview');
});

// Teacher Routes
Route::middleware(['auth', 'verified'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [TeacherDashboardController::class, 'courses'])->name('courses');
    Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');

    // Course Management
    Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [\App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [\App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/courses/{course}/builder', [\App\Http\Controllers\Admin\CourseController::class, 'builder'])->name('courses.builder');
    Route::get('/courses/{course}/folder/{folder}/contents', [\App\Http\Controllers\Admin\CourseController::class, 'getFolderContents'])->name('courses.folder.contents');

    // Course Builder AJAX Routes
    Route::post('/folders', [\App\Http\Controllers\Admin\FolderController::class, 'store'])->name('folders.store');
    Route::delete('/folders/{folder}', [\App\Http\Controllers\Admin\FolderController::class, 'destroy'])->name('folders.destroy');
    Route::post('/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [\App\Http\Controllers\Admin\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('lessons.destroy');

    // Lesson Content Block Routes
    Route::post('/lessons/{lesson}/content-blocks', [\App\Http\Controllers\Admin\LessonController::class, 'addContentBlock'])->name('lessons.content-blocks.store');
    Route::put('/lessons/{lesson}/content-blocks/{block}', [\App\Http\Controllers\Admin\LessonController::class, 'updateContentBlock'])->name('lessons.content-blocks.update');
    Route::delete('/lessons/{lesson}/content-blocks/{block}', [\App\Http\Controllers\Admin\LessonController::class, 'deleteContentBlock'])->name('lessons.content-blocks.destroy');
    Route::post('/lessons/{lesson}/content-blocks/reorder', [\App\Http\Controllers\Admin\LessonController::class, 'reorderContentBlocks'])->name('lessons.content-blocks.reorder');
    Route::post('/tests', [\App\Http\Controllers\Admin\TestController::class, 'store'])->name('tests.store');
    Route::get('/tests/{test}/edit', [\App\Http\Controllers\Admin\TestController::class, 'edit'])->name('tests.edit');
    Route::put('/tests/{test}', [\App\Http\Controllers\Admin\TestController::class, 'update'])->name('tests.update');
    Route::delete('/tests/{test}', [\App\Http\Controllers\Admin\TestController::class, 'destroy'])->name('tests.destroy');

    // Test Question Routes
    Route::post('/tests/{test}/questions', [\App\Http\Controllers\Admin\TestController::class, 'addQuestion'])->name('tests.questions.store');
    Route::put('/tests/{test}/questions/{question}', [\App\Http\Controllers\Admin\TestController::class, 'updateQuestion'])->name('tests.questions.update');
    Route::post('/tests/{test}/questions/{question}/update-basic', [\App\Http\Controllers\Admin\TestController::class, 'updateQuestionBasic'])->name('tests.questions.update-basic');
    Route::delete('/tests/{test}/questions/{question}', [\App\Http\Controllers\Admin\TestController::class, 'deleteQuestion'])->name('tests.questions.destroy');
    Route::post('/tests/{test}/questions/reorder', [\App\Http\Controllers\Admin\TestController::class, 'reorderQuestions'])->name('tests.questions.reorder');

    // Debug Routes
    Route::get('/debug/upload-limits', [\App\Http\Controllers\Admin\DebugController::class, 'checkUploadLimits'])->name('debug.upload-limits');

    // File Upload Routes
    Route::post('/files/upload', [\App\Http\Controllers\Admin\FileController::class, 'upload'])->name('files.upload');
    Route::post('/files/upload-content', [\App\Http\Controllers\Admin\FileController::class, 'uploadContent'])->name('files.upload-content');
    Route::delete('/files/{file}', [\App\Http\Controllers\Admin\FileController::class, 'destroy'])->name('files.destroy');
    Route::patch('/files/{file}/settings', [\App\Http\Controllers\Admin\FileController::class, 'updateSettings'])->name('files.settings');

    // Course Builder
    Route::get('/course-builder', [AdminDashboardController::class, 'courseBuilder'])->name('course-builder.index');

    // Course Assignments
    Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/course/{course}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.course.show');
    Route::post('/assignments/course/{course}/assign', [\App\Http\Controllers\Admin\AssignmentController::class, 'assign'])->name('assignments.course.assign');
    Route::delete('/assignments/course/{course}/unassign', [\App\Http\Controllers\Admin\AssignmentController::class, 'unassign'])->name('assignments.course.unassign');
    Route::get('/assignments/course/{course}/data', [\App\Http\Controllers\Admin\AssignmentController::class, 'getCourseData'])->name('assignments.course.data');

    // Analytics
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics.index');

    // Test Submissions
    Route::get('/test-submissions', [AdminDashboardController::class, 'testSubmissions'])->name('test-submissions.index');
    Route::get('/test-submissions/{submission}', [AdminDashboardController::class, 'showTestSubmission'])->name('test-submissions.show');

    // File Management
    Route::get('/files', [AdminDashboardController::class, 'files'])->name('files.index');

    // Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
