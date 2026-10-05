<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    if (file_exists(public_path('index.html'))) {
        return file_get_contents(public_path('index.html'));
    }
    if (file_exists(public_path('index-next.html'))) {
        return file_get_contents(public_path('index-next.html'));
    }
    return view('home');
})->name('home');

// Fix nav links from Next.js landing page
Route::get('/media-library', function () {
    return redirect('/courses');
});
Route::get('/media-library/', function () {
    return redirect('/courses');
});

Route::get('/certification', function () {
    return redirect('/courses');
});
Route::get('/certification/', function () {
    return redirect('/courses');
});

Route::get('/membership', function () {
    return redirect('/join-now');
});
Route::get('/membership/', function () {
    return redirect('/join-now');
});

Route::get('/contact', function () {
    return redirect('/about');
});
Route::get('/contact/', function () {
    return redirect('/about');
});

Route::get('/courses/french', function () {
    return redirect('/courses');
});
Route::get('/courses/french/', function () {
    return redirect('/courses');
});

Route::get('/exams/orientation-test', function () {
    return redirect('/test');
});
Route::get('/exams/orientation-test/', function () {
    return redirect('/test');
});

Route::get('/auth/sign-in', function () {
    return redirect('/login');
});
Route::get('/auth/sign-in/', function () {
    return redirect('/login');
});

Route::get('/auth/register', function () {
    return redirect('/register');
});
Route::get('/auth/register/', function () {
    return redirect('/register');
});

// Additional static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/testimonials', function () {
    return view('testimonials');
})->name('testimonials');

Route::get('/join-now', function () {
    return redirect()->route('login');
})->name('join-now');

Route::get('/test', function () {
    return view('test');
})->name('test');

Route::get('/courses', function () {
    return view('courses');
})->name('courses.index');
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
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'csv' => 'text/csv',
        'txt' => 'text/plain',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'wav' => 'audio/wav',
        'webm' => 'video/webm',
        'ogg' => 'audio/ogg',
        'm4a' => 'audio/m4a',
    ];

    $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

    // Return file with appropriate headers
    return response()->file($fullPath, [
        'Content-Type' => $contentType,
        'Accept-Ranges' => 'bytes',
        'Cache-Control' => 'public, max-age=86400',
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
    Route::get('/courses/level/{level}', [StudentDashboardController::class, 'coursesByLevel'])->name('courses.level');


    // Course specific routes
    Route::get('/course/{course}', [StudentDashboardController::class, 'showCourse'])->name('course.show');
    Route::post('/course/{course}/enroll', [StudentDashboardController::class, 'enroll'])->name('course.enroll');
    Route::get('/course/{course}/folder/{folder}', [StudentDashboardController::class, 'showFolder'])->name('course.folder');

    Route::get('/lesson/{lesson}', [StudentDashboardController::class, 'showLesson'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [StudentDashboardController::class, 'completeLesson'])->name('lesson.complete');

    // Test routes
    Route::get('/test/{test}', [StudentDashboardController::class, 'showTest'])->name('test.show');
    Route::post('/test/{test}/start', [StudentDashboardController::class, 'startTest'])->name('test.start');
    Route::post('/test/{test}/submit', [StudentDashboardController::class, 'submitTest'])->name('test.submit');
    Route::get('/test/{test}/results/{attempt}', [StudentDashboardController::class, 'showTestResults'])->name('test.results');

    // File download and preview
    Route::get('/file/download/{file}', [StudentDashboardController::class, 'downloadFile'])->name('file.download');
    Route::get('/file/preview/{file}', [StudentDashboardController::class, 'previewFile'])->name('file.preview');

    // Preview routes (accessible to admins and teachers)
    Route::get('/test/{test}/preview', [StudentDashboardController::class, 'previewTest'])->name('test.preview');
    Route::get('/lesson/{lesson}/preview', [StudentDashboardController::class, 'previewLesson'])->name('lesson.preview');

    // Student Analytics (view own performance)
    Route::get('/analytics', [\App\Http\Controllers\Admin\StudentAnalyticsController::class, 'showOwn'])->name('analytics');
    Route::get('/analytics/chart-data', [\App\Http\Controllers\Admin\StudentAnalyticsController::class, 'getOwnChartData'])->name('analytics.chart-data');

    // Status Tracker
    Route::get('/status', [\App\Http\Controllers\Student\StudentStatusController::class, 'index'])->name('status.index');
    Route::post('/status/update', [\App\Http\Controllers\Student\StudentStatusController::class, 'update'])->name('status.update');
    Route::post('/status/topic/store', [\App\Http\Controllers\Student\StudentStatusController::class, 'storeTopic'])->name('status.topic.store');
    Route::post('/status/topic/update', [\App\Http\Controllers\Student\StudentStatusController::class, 'updateTopic'])->name('status.topic.update');
    Route::delete('/status/topic/delete', [\App\Http\Controllers\Student\StudentStatusController::class, 'deleteTopic'])->name('status.topic.delete');

    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [\App\Http\Controllers\Student\AttendanceController::class, 'store'])->name('attendance.store');
});

// File routes aliases with exact 'file.*' names (outside the 'student.' name prefix group)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/file/download/{file}', [StudentDashboardController::class, 'downloadFile'])->name('file.download');
    Route::get('/file/preview/{file}', [StudentDashboardController::class, 'previewFile'])->name('file.preview');
});

// Teacher Routes
Route::middleware(['auth', 'verified', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
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
    Route::get('/courses/{course}/folder/{folder}/items', [\App\Http\Controllers\Admin\CourseController::class, 'getFolderContents'])->name('courses.folder.contents');

    // Course Builder AJAX Routes
    Route::post('/folders', [\App\Http\Controllers\Admin\FolderController::class, 'store'])->name('folders.store');
    Route::delete('/folders/{folder}', [\App\Http\Controllers\Admin\FolderController::class, 'destroy'])->name('folders.destroy');
    Route::get('/folders/{folder}', [\App\Http\Controllers\Admin\FolderController::class, 'show'])->name('folders.show');
    Route::put('/folders/{folder}', [\App\Http\Controllers\Admin\FolderController::class, 'update'])->name('folders.update');
    Route::get('/courses/{course}/folders/{folder}/move-options', [\App\Http\Controllers\Admin\FolderController::class, 'moveOptions'])->name('folders.move-options');
    Route::put('/folders/{folder}/move', [\App\Http\Controllers\Admin\FolderController::class, 'move'])->name('folders.move');
    Route::post('/folders/{folder}/duplicate', [\App\Http\Controllers\Admin\FolderController::class, 'duplicate'])->name('folders.duplicate');


    Route::post('/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [\App\Http\Controllers\Admin\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::put('/lessons/{lesson}/move', [\App\Http\Controllers\Admin\LessonController::class, 'move'])->name('lessons.move');
    Route::post('/lessons/{lesson}/duplicate', [\App\Http\Controllers\Admin\LessonController::class, 'duplicate'])->name('lessons.duplicate');

    // Lesson Content Block Routes
    Route::post('/lessons/{lesson}/content-blocks', [\App\Http\Controllers\Admin\LessonController::class, 'addContentBlock'])->name('lessons.content-blocks.store');
    Route::put('/lessons/{lesson}/content-blocks/{block}', [\App\Http\Controllers\Admin\LessonController::class, 'updateContentBlock'])->name('lessons.content-blocks.update');
    Route::delete('/lessons/{lesson}/content-blocks/{block}', [\App\Http\Controllers\Admin\LessonController::class, 'deleteContentBlock'])->name('lessons.content-blocks.destroy');
    Route::post('/lessons/{lesson}/content-blocks/reorder', [\App\Http\Controllers\Admin\LessonController::class, 'reorderContentBlocks'])->name('lessons.content-blocks.reorder');
    Route::post('/tests', [\App\Http\Controllers\Admin\TestController::class, 'store'])->name('tests.store');
    Route::get('/tests/{test}/edit', [\App\Http\Controllers\Admin\TestController::class, 'edit'])->name('tests.edit');
    Route::put('/tests/{test}', [\App\Http\Controllers\Admin\TestController::class, 'update'])->name('tests.update');
    Route::delete('/tests/{test}', [\App\Http\Controllers\Admin\TestController::class, 'destroy'])->name('tests.destroy');
    Route::get('/tests/{test}/move-options', [\App\Http\Controllers\Admin\TestController::class, 'moveOptions'])->name('tests.move-options');
    Route::put('/tests/{test}/move', [\App\Http\Controllers\Admin\TestController::class, 'move'])->name('tests.move');
    Route::post('/tests/{test}/duplicate', [\App\Http\Controllers\Admin\TestController::class, 'duplicate'])->name('tests.duplicate');

    // Test Question Routes
    Route::post('/tests/{test}/questions', [\App\Http\Controllers\Admin\TestController::class, 'addQuestion'])->name('tests.questions.store');
    Route::put('/tests/{test}/questions/{question}', [\App\Http\Controllers\Admin\TestController::class, 'updateQuestion'])->name('tests.questions.update');
    Route::post('/tests/{test}/questions/{question}/update-basic', [\App\Http\Controllers\Admin\TestController::class, 'updateQuestionBasic'])->name('tests.questions.update-basic');
    Route::delete('/tests/{test}/questions/{question}', [\App\Http\Controllers\Admin\TestController::class, 'deleteQuestion'])->name('tests.questions.destroy');
    Route::post('/tests/{test}/questions/reorder', [\App\Http\Controllers\Admin\TestController::class, 'reorderQuestions'])->name('tests.questions.reorder');


    // Passage MCQ Bulk Upload (CSV)
    Route::get('/tests/passage-mcq/bulk-template', [\App\Http\Controllers\Admin\TestController::class, 'downloadPassageMcqTemplate'])->name('tests.questions.passage-mcq.template');
    Route::post('/tests/{test}/questions/passage-mcq/bulk-parse', [\App\Http\Controllers\Admin\TestController::class, 'bulkParsePassageMcq'])->name('tests.questions.passage-mcq.bulk-parse');
    Route::post('/tests/{test}/questions/passage-mcq/bulk-import', [\App\Http\Controllers\Admin\TestController::class, 'bulkImportPassageMcq'])->name('tests.questions.passage-mcq.bulk-import');

    // Debug Routes
    Route::get('/debug/upload-limits', [\App\Http\Controllers\Admin\DebugController::class, 'checkUploadLimits'])->name('debug.upload-limits');

    // File Upload Routes - MUST come before {file} routes to avoid route conflicts
    Route::post('/files/upload', [\App\Http\Controllers\Admin\FileController::class, 'upload'])
        ->withoutMiddleware(\Illuminate\Http\Middleware\ValidatePostSize::class)
        ->name('files.upload');
    Route::post('/files/upload-content', [\App\Http\Controllers\Admin\FileController::class, 'uploadContent'])
        ->withoutMiddleware(\Illuminate\Http\Middleware\ValidatePostSize::class)
        ->name('files.upload-content');
    Route::post('/files/upload-chunk', [\App\Http\Controllers\Admin\FileController::class, 'uploadChunk'])
        ->withoutMiddleware(\Illuminate\Http\Middleware\ValidatePostSize::class)
        ->name('files.upload-chunk');

    // Generic file routes - MUST come after specific upload routes


    // Course Collections (folders for organizing courses)
    Route::get('/course-collections/options', [\App\Http\Controllers\Admin\CourseCollectionController::class, 'options'])->name('course-collections.options');
    Route::post('/course-collections', [\App\Http\Controllers\Admin\CourseCollectionController::class, 'store'])->name('course-collections.store');

    // Move course into a collection
    Route::put('/courses/{course}/move-collection', [\App\Http\Controllers\Admin\CourseController::class, 'moveToCollection'])->name('courses.move-collection');


    // Merge Course endpoints
    Route::get('/courses/{course}/merge-options', [\App\Http\Controllers\Admin\CourseController::class, 'mergeOptions'])->name('courses.merge-options');
    Route::get('/courses/{course}/merge-stats', [\App\Http\Controllers\Admin\CourseController::class, 'mergeStats'])->name('courses.merge-stats');
    Route::post('/courses/{course}/merge', [\App\Http\Controllers\Admin\CourseController::class, 'merge'])->name('courses.merge');

    // Course Builder
    Route::get('/course-builder', [AdminDashboardController::class, 'courseBuilder'])->name('course-builder.index');

    // Course Assignments
    Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/course/{course}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.course.show');
    Route::post('/assignments/course/{course}/assign', [\App\Http\Controllers\Admin\AssignmentController::class, 'assign'])->name('assignments.course.assign');
    Route::delete('/assignments/course/{course}/unassign', [\App\Http\Controllers\Admin\AssignmentController::class, 'unassign'])->name('assignments.course.unassign');
    // Student Content Permissions
    // Student Content Permissions
    Route::get('/assignments/course/{course}/permissions/{student_id}', [\App\Http\Controllers\Admin\AssignmentController::class, 'permissions'])->name('assignments.course.permissions');
    Route::post('/assignments/course/{course}/permissions/{student_id}', [\App\Http\Controllers\Admin\AssignmentController::class, 'savePermissions'])->name('assignments.course.permissions.save');

    Route::get('/assignments/course/{course}/data', [\App\Http\Controllers\Admin\AssignmentController::class, 'getCourseData'])->name('assignments.course.data');

    // Student Analytics
    Route::get('/students/{student}/analytics', [\App\Http\Controllers\Admin\StudentAnalyticsController::class, 'show'])->name('students.analytics');
    Route::get('/students/{student}/analytics/chart-data', [\App\Http\Controllers\Admin\StudentAnalyticsController::class, 'getChartData'])->name('students.analytics.chart-data');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    // Test Submissions
    Route::get('/test-submissions', [AdminDashboardController::class, 'testSubmissions'])->name('test-submissions.index');
    Route::get('/test-submissions/{submission}', [AdminDashboardController::class, 'showTestSubmission'])->name('test-submissions.show');
    Route::post('/test-submissions/{submission}', [AdminDashboardController::class, 'updateTestSubmission'])->name('test-submissions.update');

    // File Management
    // File Management
    Route::get('/files', [\App\Http\Controllers\Admin\FileController::class, 'index'])->name('files.index');
    Route::post('/files', [\App\Http\Controllers\Admin\FileController::class, 'store'])->name('files.store');
    Route::post('/files/create-folder', [\App\Http\Controllers\Admin\FileController::class, 'createFolder'])->name('files.create-folder');
    Route::delete('/files/{file}', [\App\Http\Controllers\Admin\FileController::class, 'destroyCourseFile'])->name('files.destroy-course-file');
    Route::delete('/files', [\App\Http\Controllers\Admin\FileController::class, 'destroy'])->name('files.destroy');
    Route::patch('/files/{file}/settings', [\App\Http\Controllers\Admin\FileController::class, 'updateSettings'])->name('files.update-settings');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');

    // Status Returns
    Route::get('/status', [\App\Http\Controllers\Admin\AdminStatusController::class, 'index'])->name('status.index');
    Route::get('/status/{student}', [\App\Http\Controllers\Admin\AdminStatusController::class, 'show'])->name('status.show');
    Route::post('/status/topic/store', [\App\Http\Controllers\Admin\AdminStatusController::class, 'storeTopic'])->name('status.topic.store');
    Route::post('/status/topic/update', [\App\Http\Controllers\Admin\AdminStatusController::class, 'updateTopic'])->name('status.topic.update');
    Route::delete('/status/topic/delete', [\App\Http\Controllers\Admin\AdminStatusController::class, 'deleteTopic'])->name('status.topic.delete');

    // Attendance Management
    Route::get('/attendance/sessions', [\App\Http\Controllers\Admin\AttendanceSessionController::class, 'index'])->name('attendance.sessions.index');
    Route::post('/attendance/sessions', [\App\Http\Controllers\Admin\AttendanceSessionController::class, 'store'])->name('attendance.sessions.store');
    Route::delete('/attendance/sessions/{id}', [\App\Http\Controllers\Admin\AttendanceSessionController::class, 'destroy'])->name('attendance.sessions.destroy');
    
    Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/{id}/status', [\App\Http\Controllers\Admin\AttendanceController::class, 'updateStatus'])->name('attendance.status');
    Route::post('/attendance/bulk-approve', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkApprove'])->name('attendance.bulk-approve');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Public route to serve uploaded files from storage (secured against path traversal)
Route::get('/storage/uploads/{type}/{filename}', function ($type, $filename) {
    // Prevent directory traversal attacks
    $cleanFilename = basename($filename);
    if ($cleanFilename !== $filename || str_contains($filename, '..')) {
        abort(403, 'Forbidden');
    }

    $baseUploadDir = base_path('storage/uploads/' . $type);
    if (!is_dir($baseUploadDir)) {
        abort(404, 'Category directory not found');
    }

    $allowedDir = realpath($baseUploadDir);
    $fullPath = $baseUploadDir . '/' . $cleanFilename;

    if (!file_exists($fullPath)) {
        abort(404, 'File not found');
    }

    $path = realpath($fullPath);
    if (!$path || !$allowedDir || !str_starts_with($path, $allowedDir)) {
        abort(403, 'Access denied');
    }

    return response()->file($path);
})->where('type', 'audio|video|image|file')->name('storage.uploads');

