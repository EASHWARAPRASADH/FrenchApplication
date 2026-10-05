<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentTestAttempt;
use App\Models\TestSubmission;
use App\Models\LessonProgress;
use App\Models\StudentDailyStatus;
use App\Models\StudentDailyTopic;
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

        // Resolve filter dates (specific date, month, range, or presets)
        $filter = $this->resolveDateFilter($request);
        $dateFrom = $filter['date_from'];
        $dateTo = $filter['date_to'];
        $courseId = $request->get('course_id');

        // Fetch detailed reading / daily study log activity
        $readingData = $this->getReadingActivity($student, $dateFrom, $dateTo);

        // Build versioned cache key for test metrics
        $cacheKey = "student_analytics_v5:{$student->id}:{$filter['filter_type']}:{$dateFrom}:{$dateTo}:{$courseId}";

        // Get metrics (10 min cache for performance)
        $metrics = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($student, $dateFrom, $dateTo, $courseId, $readingData) {
            return $this->calculateMetrics($student, $dateFrom, $dateTo, $courseId, $readingData);
        });

        // Ensure readingData is up-to-date in metrics if reading was fresh
        $metrics['reading_minutes'] = $readingData['total_minutes'];
        $metrics['reading_formatted'] = $readingData['total_formatted'];
        $metrics['reading_hours'] = $readingData['total_hours'];
        $metrics['active_days'] = $readingData['active_days_count'];
        $metrics['topics_count'] = $readingData['topics_count'];
        $metrics['avg_daily_reading_formatted'] = $readingData['avg_daily_formatted'];

        $totalTestMinutes = $metrics['test_minutes'] ?? 0;
        $totalLearningMinutes = $readingData['total_minutes'] + $totalTestMinutes;
        $metrics['total_learning_minutes'] = $totalLearningMinutes;
        $metrics['total_learning_formatted'] = $this->formatMinutes($totalLearningMinutes);
        $metrics['total_learning_hours'] = round($totalLearningMinutes / 60, 1);

        // Get test history (paginated)
        $testHistory = $this->getTestHistory($student, $dateFrom, $dateTo, $courseId);

        // Get available courses for filter
        $courses = Course::whereHas('tests.attempts', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        return view('admin.students.analytics', compact(
            'student',
            'metrics',
            'readingData',
            'testHistory',
            'courses',
            'filter',
            'dateFrom',
            'dateTo',
            'courseId'
        ));
    }

    /**
     * Resolve filter dates from request (specific date, month, range, presets)
     */
    private function resolveDateFilter(Request $request): array
    {
        $canadaToday = Carbon::now('America/Toronto')->format('Y-m-d');
        $preset = $request->get('preset');
        $filterType = $request->get('filter_type');
        $date = $request->get('date');
        $month = $request->get('month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Handle preset shortcuts
        if ($preset) {
            switch ($preset) {
                case 'today':
                    $filterType = 'date';
                    $date = $canadaToday;
                    break;
                case 'yesterday':
                    $filterType = 'date';
                    $date = Carbon::now('America/Toronto')->subDay()->format('Y-m-d');
                    break;
                case 'this_month':
                    $filterType = 'month';
                    $month = Carbon::now('America/Toronto')->format('Y-m');
                    break;
                case 'last_month':
                    $filterType = 'month';
                    $month = Carbon::now('America/Toronto')->subMonth()->format('Y-m');
                    break;
                case 'last_7_days':
                    $filterType = 'range';
                    $dateFrom = Carbon::now('America/Toronto')->subDays(6)->format('Y-m-d');
                    $dateTo = $canadaToday;
                    break;
                case 'last_30_days':
                    $filterType = 'range';
                    $dateFrom = Carbon::now('America/Toronto')->subDays(29)->format('Y-m-d');
                    $dateTo = $canadaToday;
                    break;
                case 'all':
                    $filterType = 'all';
                    $date = null;
                    $month = null;
                    $dateFrom = null;
                    $dateTo = null;
                    break;
            }
        }

        // Determine filterType if not set explicitly
        if (!$filterType) {
            if ($date) {
                $filterType = 'date';
            } elseif ($month) {
                $filterType = 'month';
            } elseif ($dateFrom || $dateTo) {
                $filterType = 'range';
            } else {
                $filterType = 'all';
            }
        }

        // Compute normalized $dateFrom and $dateTo based on filterType
        $label = 'All Time';
        if ($filterType === 'date' && $date) {
            $parsed = Carbon::parse($date);
            $dateFrom = $parsed->format('Y-m-d');
            $dateTo = $dateFrom;
            $label = $parsed->format('M d, Y');
            if ($date === $canadaToday) {
                $label .= ' (Today)';
            }
        } elseif ($filterType === 'month' && $month) {
            $parsedMonth = Carbon::parse($month . '-01');
            $dateFrom = $parsedMonth->copy()->startOfMonth()->format('Y-m-d');
            $dateTo = $parsedMonth->copy()->endOfMonth()->format('Y-m-d');
            $label = $parsedMonth->format('F Y');
        } elseif ($filterType === 'range') {
            if ($dateFrom && $dateTo) {
                $label = Carbon::parse($dateFrom)->format('M d, Y') . ' - ' . Carbon::parse($dateTo)->format('M d, Y');
            } elseif ($dateFrom) {
                $label = 'From ' . Carbon::parse($dateFrom)->format('M d, Y');
            } elseif ($dateTo) {
                $label = 'Until ' . Carbon::parse($dateTo)->format('M d, Y');
            }
        } else {
            // all
            $dateFrom = null;
            $dateTo = null;
            $label = 'All Time';
        }

        return [
            'filter_type' => $filterType,
            'date' => $date,
            'month' => $month,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'preset' => $preset,
            'label' => $label,
        ];
    }

    /**
     * Get detailed daily reading and study activity from student_daily_statuses and student_daily_topics
     */
    private function getReadingActivity(User $student, $dateFrom = null, $dateTo = null): array
    {
        $statusQuery = StudentDailyStatus::where('user_id', $student->id)
            ->with(['topics' => function ($q) {
                $q->orderBy('starting_time')->orderBy('id');
            }]);

        if ($dateFrom && $dateTo) {
            if ($dateFrom === $dateTo) {
                $statusQuery->where('log_date', $dateFrom);
            } else {
                $statusQuery->whereBetween('log_date', [$dateFrom, $dateTo]);
            }
        } elseif ($dateFrom) {
            $statusQuery->where('log_date', '>=', $dateFrom);
        } elseif ($dateTo) {
            $statusQuery->where('log_date', '<=', $dateTo);
        }

        $statuses = $statusQuery->orderBy('log_date', 'desc')->get();

        $totalMinutes = 0;
        $topicsCount = 0;
        $dailyBreakdown = [];
        $allTopics = [];

        foreach ($statuses as $status) {
            $statusDate = $status->log_date ? $status->log_date->format('Y-m-d') : null;
            if (!$statusDate) {
                continue;
            }

            $dayMinutes = 0;
            $dayTopics = [];

            foreach ($status->topics as $topic) {
                $mins = $this->parseDurationMinutes($topic->duration, $topic->starting_time, $topic->ending_time);
                $dayMinutes += $mins;
                $topicsCount++;

                $topicItem = [
                    'id' => $topic->id,
                    'topic' => $topic->topic ?: 'Study Session',
                    'starting_time' => $topic->starting_time,
                    'ending_time' => $topic->ending_time,
                    'duration' => $topic->duration,
                    'duration_formatted' => $this->formatMinutes($mins),
                    'minutes' => $mins,
                    'log_date' => $statusDate,
                    'date_formatted' => $status->log_date->format('M d, Y')
                ];

                $dayTopics[] = $topicItem;
                $allTopics[] = $topicItem;
            }

            $totalMinutes += $dayMinutes;

            $dailyBreakdown[] = [
                'date' => $statusDate,
                'date_formatted' => $status->log_date->format('M d, Y'),
                'day_name' => $status->log_date->format('l'),
                'total_minutes' => $dayMinutes,
                'total_formatted' => $this->formatMinutes($dayMinutes),
                'total_hours' => round($dayMinutes / 60, 1),
                'topics' => $dayTopics,
            ];
        }

        $activeDaysCount = count(array_filter($dailyBreakdown, fn($d) => $d['total_minutes'] > 0 || count($d['topics']) > 0));
        $avgDailyMinutes = $activeDaysCount > 0 ? round($totalMinutes / $activeDaysCount) : 0;

        return [
            'total_minutes' => $totalMinutes,
            'total_formatted' => $this->formatMinutes($totalMinutes),
            'total_hours' => round($totalMinutes / 60, 1),
            'active_days_count' => $activeDaysCount,
            'topics_count' => $topicsCount,
            'avg_daily_minutes' => $avgDailyMinutes,
            'avg_daily_formatted' => $this->formatMinutes($avgDailyMinutes),
            'days' => $dailyBreakdown,
            'all_topics' => $allTopics,
        ];
    }

    /**
     * Parse duration text (e.g., '1h 30m', '45m') or start/end times into total minutes
     */
    private function parseDurationMinutes($duration, $start = null, $end = null): int
    {
        $duration = trim((string)$duration);
        $mins = 0;

        if (preg_match('/(?:(\d+)\s*h)?\s*(?:(\d+)\s*m)?/i', $duration, $m)) {
            $h = !empty($m[1]) ? (int)$m[1] : 0;
            $mi = !empty($m[2]) ? (int)$m[2] : 0;
            $mins = ($h * 60) + $mi;
        }

        // If duration string had no minutes, calculate from start and end time
        if ($mins === 0 && !empty($start) && !empty($end)) {
            try {
                $s = Carbon::parse($start);
                $e = Carbon::parse($end);
                if ($e->lessThan($s)) {
                    $e->addDay();
                }
                $mins = $s->diffInMinutes($e);
            } catch (\Throwable $ex) {
                // Ignore parse errors
            }
        }

        return $mins;
    }

    /**
     * Format minutes into human-readable 'Xh Ym' or 'Ym'
     */
    private function formatMinutes(int $totalMinutes): string
    {
        if ($totalMinutes <= 0) {
            return '0m';
        }
        $hours = floor($totalMinutes / 60);
        $mins = $totalMinutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$mins}m";
        }
    }

    /**
     * Get chart data for score progression and daily study time
     */
    public function getChartData(User $student, Request $request): JsonResponse
    {
        $filter = $this->resolveDateFilter($request);
        $dateFrom = $filter['date_from'];
        $dateTo = $filter['date_to'];
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

        // Build daily study reading progression if month or range is selected
        $dailyStudyLabels = [];
        $dailyReadingMinutes = [];
        $dailyReadingHours = [];

        if ($dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
            $daysDiff = $startDate->diffInDays($endDate);

            if ($daysDiff <= 35) {
                // Fetch daily statuses within this period
                $readingData = $this->getReadingActivity($student, $dateFrom, $dateTo);
                $readingMap = collect($readingData['days'])->keyBy('date');

                $curr = $startDate->copy();
                while ($curr <= $endDate) {
                    $dStr = $curr->format('Y-m-d');
                    $dailyStudyLabels[] = $curr->format('M d');
                    $dayInfo = $readingMap->get($dStr);
                    $mins = $dayInfo ? $dayInfo['total_minutes'] : 0;
                    $dailyReadingMinutes[] = $mins;
                    $dailyReadingHours[] = round($mins / 60, 2);
                    $curr->addDay();
                }
            }
        }

        return response()->json([
            'labels' => $gradedAttempts->map(fn($a) => $a->completed_at ? $a->completed_at->format('M d, Y') : '')->values(),
            'scores' => $gradedAttempts->map(fn($a) => (float) $a->score)->values(),
            'testNames' => $gradedAttempts->map(fn($a) => $a->test->title ?? 'Test')->values(),
            'courseNames' => $gradedAttempts->map(fn($a) => $a->test->course->title ?? 'N/A')->values(),
            'attempts' => $gradedAttempts->pluck('attempt_number')->values(),
            'passed' => $gradedAttempts->map(fn($a) => (bool)($a->passed || $a->score >= ($a->test->passing_score ?? 95)))->values(),
            'dailyStudy' => [
                'labels' => $dailyStudyLabels,
                'minutes' => $dailyReadingMinutes,
                'hours' => $dailyReadingHours,
            ]
        ]);
    }

    /**
     * Calculate performance metrics
     */
    private function calculateMetrics(User $student, $dateFrom = null, $dateTo = null, $courseId = null, array $readingData = []): array
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

        $avgScore = $gradedCount > 0 ? round((float) $gradedAttempts->avg('score'), 2) : 0;
        $totalTestSeconds = $attempts->sum('time_taken');
        $totalTestMinutes = round($totalTestSeconds / 60);
        $totalTestHours = round($totalTestSeconds / 3600, 1);

        // Pass rate strictly checking passing mark (95%)
        $passedCount = $gradedAttempts->filter(function ($a) {
            $passingScore = $a->test->passing_score ?? 95;
            return $a->passed || ($a->score >= $passingScore);
        })->count();

        $passRate = $gradedCount > 0 ? round(($passedCount / $gradedCount) * 100, 1) : 0;

        // Calculate improvement
        $improvement = $this->calculateImprovement($student->id, $dateFrom, $dateTo, $courseId, $pendingSubmissions);

        $readingMinutes = $readingData['total_minutes'] ?? 0;
        $totalLearningMinutes = $readingMinutes + $totalTestMinutes;

        return [
            'avg_score' => $avgScore,
            'tests_completed' => $totalAttempts,
            'graded_count' => $gradedCount,
            'pending_evaluations' => $pendingCount,
            'pass_rate' => $passRate,
            'improvement' => $improvement,

            // Test time metrics
            'test_seconds' => $totalTestSeconds,
            'test_minutes' => $totalTestMinutes,
            'test_hours' => $totalTestHours,
            'test_time_formatted' => $this->formatMinutes($totalTestMinutes),

            // Reading time metrics
            'reading_minutes' => $readingMinutes,
            'reading_hours' => round($readingMinutes / 60, 1),
            'reading_formatted' => $this->formatMinutes($readingMinutes),
            'active_days' => $readingData['active_days_count'] ?? 0,
            'topics_count' => $readingData['topics_count'] ?? 0,
            'avg_daily_reading_formatted' => $readingData['avg_daily_formatted'] ?? '0m',

            // Combined total learning time
            'total_learning_minutes' => $totalLearningMinutes,
            'total_learning_hours' => round($totalLearningMinutes / 60, 1),
            'total_learning_formatted' => $this->formatMinutes($totalLearningMinutes),

            // Legacy total_time field for backwards compatibility
            'total_time' => round($totalLearningMinutes / 60, 1),

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

        $recentAvg = (float) $recentTests->avg('score');
        $previousAvg = (float) $previousTests->avg('score');

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
