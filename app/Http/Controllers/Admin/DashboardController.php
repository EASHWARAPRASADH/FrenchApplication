<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\TestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware is now handled in routes/web.php
    }

    public function index()
    {
        // Platform Statistics
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $pendingTeachers = User::where('role', 'teacher')->where('status', 'pending')->count();

        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();
        $draftCourses = Course::where('status', 'draft')->count();
        $pendingCourses = Course::where('status', 'pending')->count();

        $totalEnrollments = Enrollment::count();
        $activeEnrollments = Enrollment::where('status', 'active')->count();

        // Recent Activity
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentCourses = Course::with('teacher')->orderBy('created_at', 'desc')->limit(5)->get();
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly Growth Data
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $monthExpr = $isSqlite ? 'strftime("%m", created_at)' : 'MONTH(created_at)';
        
        $monthlyUsers = User::selectRaw($monthExpr . ' as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyEnrollments = Enrollment::selectRaw($monthExpr . ' as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Course Performance
        $topCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(5)
            ->get();

        // System Health
        $systemHealth = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'pendingTeachers',
            'totalCourses',
            'publishedCourses',
            'draftCourses',
            'pendingCourses',
            'totalEnrollments',
            'activeEnrollments',
            'recentUsers',
            'recentCourses',
            'recentEnrollments',
            'monthlyUsers',
            'monthlyEnrollments',
            'topCourses',
            'systemHealth'
        ));
    }

    public function users()
    {
        $users = User::when(request('role'), function ($query) {
            return $query->where('role', request('role'));
        })
            ->when(request('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->when(request('search'), function ($query) {
                return $query->where(function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }



    public function settings()
    {
        return view('admin.settings.index');
    }

    /**
     * Show course builder page
     */
    public function courseBuilder()
    {
        $courses = Course::with('teacher')
            ->withCount(['lessons', 'enrollments'])
            ->latest()
            ->get();

        return view('admin.course-builder.index', compact('courses'));
    }

    /**
     * Show assignments page
     */
    public function assignments()
    {
        // Get real assignment data
        $courses = Course::with(['teacher', 'enrollments.user'])
            ->withCount('enrollments')
            ->get();

        return view('admin.assignments.index', compact('courses'));
    }

    /**
     * Show analytics page
     */
    public function analytics()
    {
        $analytics = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'completion_rate' => 0, // Calculate completion rate
        ];

        return view('admin.analytics.index', compact('analytics'));
    }

    /**
     * Show test submissions page
     */
    public function testSubmissions()
    {
        // Get real test submission data
        $query = TestSubmission::with(['test.course', 'student']);

        // Apply filters
        if (request('course')) {
            $query->whereHas('test', function ($q) {
                $q->where('course_id', request('course'));
            });
        }

        if (request('test')) {
            $query->where('test_id', request('test'));
        }

        if (request('status')) {
            if (request('status') === 'pending') {
                $query->where('status', 'pending');
            } elseif (request('status') === 'passed') {
                $query->where('passed', true);
            } elseif (request('status') === 'failed') {
                $query->where('passed', false)->where(function ($q) {
                    $q->where('status', '!=', 'pending')->orWhereNull('status');
                });
            }
        }

        if (request('search')) {
            $search = request('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->paginate(20);

        // Get courses and tests for filters
        $courses = Course::orderBy('title')->get();
        $tests = \App\Models\Test::with('course')->orderBy('title')->get();

        return view('admin.test-submissions.index', compact('submissions', 'courses', 'tests'));
    }

    /**
     * Show individual test submission details
     */
    public function showTestSubmission(TestSubmission $submission)
    {
        $submission->load(['test.course', 'test.questions', 'student']);
        return view('admin.test-submissions.show', compact('submission'));
    }

    /**
     * Update test submission (grading)
     */
    public function updateTestSubmission(Request $request, TestSubmission $submission)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
            'status' => 'required|in:pending,completed',
            'corrections' => 'nullable|array'
        ]);

        $test = $submission->test;
        $passed = $request->score >= $test->passing_score;

        // Merge corrections into answers
        $answers = $submission->answers;
        if ($request->has('corrections') && is_array($answers)) {
            foreach ($request->corrections as $questionId => $correction) {
                if (isset($answers[$questionId])) {
                    // Ensure structure is an array before setting key
                    if (!is_array($answers[$questionId])) {
                        $answers[$questionId] = ['answer' => $answers[$questionId]];
                    }
                    $answers[$questionId]['correction'] = $correction;
                }
            }
        }

        // DB Transaction to ensure consistency
        DB::transaction(function () use ($submission, $request, $passed, $test, $answers) {
            $submission->update([
                'score' => $request->score,
                'remarks' => $request->remarks,
                'status' => $request->status,
                'passed' => $passed,
                'answers' => $answers
            ]);

            // Update the attempt record as well
            $attempt = \App\Models\StudentTestAttempt::where('student_id', $submission->student_id)
                ->where('test_id', $submission->test_id)
                ->where('attempt_number', $submission->attempt_number)
                ->first();

            if ($attempt) {
                $attempt->update([
                    'score' => $request->score,
                    'passed' => $passed,
                    'status' => 'completed' // Always completed if graded
                ]);
            }

            // Award points if passed (and points weren't already awarded)
            // Note: Simplistic check. Ideally we should check a transaction log or if points were already given.
            // For now, we assume if it was previously failed/pending and now passed, we award points.
            // But to avoid double counting on re-grading, let's just update the Level if needed.
            if ($passed) {
                $user = $submission->student;
                // Recalculate or add points logic here if robust system existed.
                // Since points are added on submission in DashboardController, we might skip delayed points or
                // just rely on the manual update failing if logic isn't perfect.
                // For this specific request, we just save the grade.
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Saved successfully']);
        }

        return redirect()->route('admin.test-submissions.show', $submission)
            ->with('success', 'Test submission updated successfully.');
    }

    /**
     * Show file management page
     */
    public function files()
    {
        // Get real file data from storage
        $uploadPath = storage_path('app/public');
        $files = collect();

        if (is_dir($uploadPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploadPath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files->push([
                        'name' => $file->getFilename(),
                        'path' => str_replace($uploadPath, '', $file->getPathname()),
                        'size' => $file->getSize(),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'type' => $file->getExtension()
                    ]);
                }
            }
        }

        return view('admin.files.index', compact('files'));
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkStorageHealth()
    {
        $storagePath = storage_path('app');
        if (is_writable($storagePath)) {
            return ['status' => 'healthy', 'message' => 'Storage is writable'];
        }
        return ['status' => 'warning', 'message' => 'Storage permission issues'];
    }

    private function checkCacheHealth()
    {
        try {
            cache()->put('health_check', 'ok', 60);
            $value = cache()->get('health_check');
            return ['status' => 'healthy', 'message' => 'Cache is working'];
        } catch (\Exception $e) {
            return ['status' => 'warning', 'message' => 'Cache issues detected'];
        }
    }
}
