<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentTestAttempt;
use App\Models\TestSubmission;
use App\Models\LessonProgress;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAnalyticsController extends Controller
{
    /**
     * Show student analytics dashboard
     */
    public function show(User $student, Request $request)
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid student');
        }

        // Get filter parameters
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $courseId = $request->get('course_id');

        // Build versioned cache key
        $cacheKey = "student_analytics_v3:{$student->id}:{$dateFrom}:{$dateTo}:{$courseId}";

        // Get metrics with caching
        $metrics = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($student, $dateFrom, $dateTo, $courseId) {
            return $this->calculateMetrics($student, $dateFrom, $dateTo, $courseId);
        });

        // Get test history (paginated)
        $testHistory = $this->getTestHistory($student, $dateFrom, $dateTo, $courseId);

        // Get available courses for filter
        $courses = Course::whereHas('tests.attempts', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        return view('admin.students.analytics', compact('student', 'metrics', 'testHistory', 'courses', 'dateFrom', 'dateTo', 'courseId'));
    }

    /**
     * Get chart data for score progression
     */
    public function getChartData(User $student, Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $courseId = $request->get('course_id');

        $query = StudentTestAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['test.course']);

        if ($dateFrom) {
            $query->where('completed_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('completed_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }
        if ($courseId) {
            $query->whereHas('test', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $allAttempts = $query->orderBy('completed_at')->get();

        // Get pending submissions so we exclude pending 0% tests from the chart
        $pendingSubmissions = TestSubmission::where('student_id', $student->id)
            ->where('status', 'pending')
            ->get()
            ->keyBy(fn($s) => $s->test_id . '_' . $s->attempt_number);

        $gradedAttempts = $allAttempts->filter(function ($a) use ($pendingSubmissions) {
            $key = $a->test_id . '_' . $a->attempt_number;
            return !$pendingSubmissions->has($key);
        })->values();

        return response()->json([
            'labels' => $gradedAttempts->map(fn($a) => $a->completed_at ? $a->completed_at->format('M d, Y') : '')->values(),
            'scores' => $gradedAttempts->map(fn($a) => (float) $a->score)->values(),
            'testNames' => $gradedAttempts->map(fn($a) => $a->test->title ?? 'Test')->values(),
            'courseNames' => $gradedAttempts->map(fn($a) => $a->test->course->title ?? 'N/A')->values(),
            'attempts' => $gradedAttempts->pluck('attempt_number')->values(),
            'passed' => $gradedAttempts->pluck('passed')->values()
        ]);
    }

    /**
     * Calculate performance metrics
     */
    private function calculateMetrics(User $student, $dateFrom = null, $dateTo = null, $courseId = null): array
    {
        $query = StudentTestAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with('test');

        if ($dateFrom) {
            $query->where('completed_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('completed_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }
        if ($courseId) {
            $query->whereHas('test', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $attempts = $query->get();
        $totalAttempts = $attempts->count();

        // Pending submissions
        $pendingSubmissions = TestSubmission::where('student_id', $student->id)
            ->where('status', 'pending')
            ->get()
            ->keyBy(fn($s) => $s->test_id . '_' . $s->attempt_number);

        $gradedAttempts = $attempts->filter(function ($a) use ($pendingSubmissions) {
            $key = $a->test_id . '_' . $a->attempt_number;
            return !$pendingSubmissions->has($key);
        });

        $gradedCount = $gradedAttempts->count();
        $pendingCount = $totalAttempts - $gradedCount;

        $avgScore = $gradedCount > 0 ? round($gradedAttempts->avg('score'), 2) : 0;
        $totalTime = round($attempts->sum('time_taken') / 3600, 1); // in hours
        $passRate = $gradedCount > 0 ? round(($gradedAttempts->where('passed', true)->count() / $gradedCount) * 100, 1) : 0;

        // Calculate improvement
        $improvement = $this->calculateImprovement($student->id, $dateFrom, $dateTo, $courseId, $pendingSubmissions);

        return [
            'avg_score' => $avgScore,
            'tests_completed' => $totalAttempts,
            'graded_count' => $gradedCount,
            'pending_evaluations' => $pendingCount,
            'total_time' => $totalTime,
            'pass_rate' => $passRate,
            'improvement' => $improvement,
            'points' => $student->points ?? 0,
            'level' => $student->level ?? 1
        ];
    }

    /**
     * Calculate improvement trend
     */
    private function calculateImprovement($studentId, $dateFrom = null, $dateTo = null, $courseId = null, $pendingSubmissions = null): float
    {
        $query = StudentTestAttempt::where('student_id', $studentId)
            ->where('status', 'completed');

        if ($dateFrom) {
            $query->where('completed_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('completed_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }
        if ($courseId) {
            $query->whereHas('test', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $allAttempts = $query->orderBy('completed_at')->get();

        if ($pendingSubmissions) {
            $allAttempts = $allAttempts->filter(function ($a) use ($pendingSubmissions) {
                $key = $a->test_id . '_' . $a->attempt_number;
                return !$pendingSubmissions->has($key);
            })->values();
        }

        if ($allAttempts->count() < 2) {
            return 0;
        }

        // Compare last 5 tests to previous 5
        $recentTests = $allAttempts->take(-5);
        $previousTests = $allAttempts->slice(-10, 5);

        if ($previousTests->count() === 0) {
            return 0;
        }

        $recentAvg = $recentTests->avg('score');
        $previousAvg = $previousTests->avg('score');

        return round($recentAvg - $previousAvg, 2);
    }

    /**
     * Get test history with details (paginated)
     */
    private function getTestHistory(User $student, $dateFrom = null, $dateTo = null, $courseId = null, $perPage = 20)
    {
        $query = StudentTestAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['test.course']);

        if ($dateFrom) {
            $query->where('completed_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('completed_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }
        if ($courseId) {
            $query->whereHas('test', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $attempts = $query->orderBy('completed_at', 'desc')->paginate($perPage)->withQueryString();

        // Load submission records for attempts on this page
        $submissions = TestSubmission::where('student_id', $student->id)
            ->whereIn('test_id', $attempts->pluck('test_id')->unique())
            ->whereIn('attempt_number', $attempts->pluck('attempt_number')->unique())
            ->get()
            ->keyBy(fn($s) => $s->test_id . '_' . $s->attempt_number);

        foreach ($attempts as $attempt) {
            $key = $attempt->test_id . '_' . $attempt->attempt_number;
            $sub = $submissions->get($key);
            $attempt->submission_id = $sub ? $sub->id : null;
            $attempt->is_pending_evaluation = ($sub && $sub->status === 'pending');
        }

        return $attempts;
    }

    /**
     * Show analytics for the authenticated student (student viewing own analytics)
     */
    public function showOwn(Request $request)
    {
        $student = auth()->user();

        // Redirect if not a student
        if ($student->role !== 'student') {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return $this->show($student, $request);
    }

    /**
     * Get chart data for the authenticated student
     */
    public function getOwnChartData(Request $request): JsonResponse
    {
        $student = auth()->user();

        if (!$student) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return $this->getChartData($student, $request);
    }
}
