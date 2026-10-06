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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    /**
     * Permanently delete a user account and cascade delete all related data.
     */
    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own admin account.');
        }

        if ($user->courses()->exists()) {
            $count = $user->courses()->count();
            return redirect()->route('admin.users.index')->with('error', "Cannot delete user {$user->name}: they are currently assigned as instructor to {$count} course(s). Please reassign or delete those courses first.");
        }

        $userName = $user->name;
        $userEmail = $user->email;

        try {
            DB::beginTransaction();

            // 1. Remove all content permissions
            if (\Illuminate\Support\Facades\Schema::hasTable('student_content_permissions')) {
                \App\Models\StudentContentPermission::where('student_id', $user->id)->delete();
                \App\Models\StudentContentPermission::where('granted_by', $user->id)->update(['granted_by' => null]);
            }

            // 2. Remove test submissions and attempts
            if (\Illuminate\Support\Facades\Schema::hasTable('test_submissions')) {
                \App\Models\TestSubmission::where('student_id', $user->id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('student_test_attempts')) {
                \App\Models\StudentTestAttempt::where('student_id', $user->id)->delete();
            }

            // 3. Remove lesson progress and bookmarks
            if (\Illuminate\Support\Facades\Schema::hasTable('lesson_progress')) {
                \App\Models\LessonProgress::where('student_id', $user->id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('lesson_bookmarks')) {
                \App\Models\LessonBookmark::where('student_id', $user->id)->delete();
            }

            // 4. Remove attendance and daily statuses
            if (\Illuminate\Support\Facades\Schema::hasTable('student_attendances')) {
                \App\Models\StudentAttendance::where('user_id', $user->id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('student_daily_statuses')) {
                \App\Models\StudentDailyStatus::where('user_id', $user->id)->delete();
            }

            // 5. Remove achievements and enrollments
            if (\Illuminate\Support\Facades\Schema::hasTable('user_achievements')) {
                \DB::table('user_achievements')->where('user_id', $user->id)->delete();
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('enrollments')) {
                \App\Models\Enrollment::where('user_id', $user->id)->delete();
            }

            // 6. Remove active sessions if sessions table exists
            if (\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                \DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            // 7. Delete user record
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', "User {$userName} ({$userEmail}) has been permanently deleted.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Directly update/reset a user's password from the admin panel.
     */
    public function resetPasswordDirect(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Password for {$user->name} ({$user->email}) has been updated successfully.");
    }

    /**
     * Generate a one-click password reset link for a user.
     */
    public function generateResetLink(User $user)
    {
        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'reset_url' => $resetUrl,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'message' => "Reset link generated for {$user->name}.",
        ]);
    }

    /**
     * Get comprehensive details, enrollments, test history, and attendance for a user (AJAX).
     */
    public function userDetails(User $user)
    {
        $user->load(['enrollments.course']);

        $courses = $user->enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->course->id ?? null,
                'title' => $enrollment->course->title ?? 'Untitled Course',
                'level' => $enrollment->course->level ?? 'N/A',
                'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : ($enrollment->created_at ? $enrollment->created_at->format('M d, Y') : 'N/A'),
                'progress_percentage' => (float) ($enrollment->progress_percentage ?? 0),
                'status' => $enrollment->status ?? 'active',
            ];
        });

        // Test submissions
        $submissions = TestSubmission::where('student_id', $user->id)
            ->with(['test.course'])
            ->orderBy('submitted_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'test_title' => $sub->test->title ?? 'Untitled Test',
                    'course_title' => $sub->test->course->title ?? 'N/A',
                    'score' => $sub->score,
                    'passed' => (bool) $sub->passed,
                    'status' => $sub->status,
                    'submitted_at' => $sub->submitted_at ? $sub->submitted_at->format('M d, Y H:i') : ($sub->created_at ? $sub->created_at->format('M d, Y H:i') : 'N/A'),
                ];
            });

        // Attendance stats
        $totalAttendance = \App\Models\StudentAttendance::where('user_id', $user->id)->count();
        $presentAttendance = \App\Models\StudentAttendance::where('user_id', $user->id)->where('status', 'present')->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => ucfirst($user->role),
                'status' => ucfirst($user->status ?? 'active'),
                'language_level' => strtoupper($user->language_level ?? 'Not Set'),
                'joined_at' => $user->created_at ? $user->created_at->format('M d, Y') : 'N/A',
                'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
            ],
            'courses' => $courses,
            'submissions' => $submissions,
            'attendance' => [
                'total_sessions' => $totalAttendance,
                'present_sessions' => $presentAttendance,
                'attendance_rate' => $attendanceRate,
            ],
        ]);
    }

    /**
     * Export test submissions to CSV with active filters.
     */
    public function exportTestSubmissions(Request $request)
    {
        $query = TestSubmission::with(['test.course', 'student']);

        if ($request->filled('course')) {
            $query->whereHas('test', function ($q) use ($request) {
                $q->where('course_id', $request->course);
            });
        }

        if ($request->filled('test')) {
            $query->where('test_id', $request->test);
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->status === 'passed') {
                $query->where('passed', true);
            } elseif ($request->status === 'failed') {
                $query->where('passed', false)->where(function ($q) {
                    $q->where('status', '!=', 'pending')->orWhereNull('status');
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->get();
        $filename = 'test-submissions-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($submissions) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'Submission ID',
                'Student Name',
                'Student Email',
                'Course',
                'Test Title',
                'Score (%)',
                'Result',
                'Evaluation Status',
                'Submitted Date',
            ]);

            foreach ($submissions as $sub) {
                $result = $sub->passed ? 'Passed' : ($sub->status === 'pending' ? 'Pending Evaluation' : 'Failed');
                fputcsv($handle, [
                    $sub->id,
                    $sub->student->name ?? 'N/A',
                    $sub->student->email ?? 'N/A',
                    $sub->test->course->title ?? 'N/A',
                    $sub->test->title ?? 'N/A',
                    $sub->score !== null ? $sub->score : 'N/A',
                    $result,
                    ucfirst($sub->status ?? 'completed'),
                    $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i:s') : ($sub->created_at ? $sub->created_at->format('Y-m-d H:i:s') : 'N/A'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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
