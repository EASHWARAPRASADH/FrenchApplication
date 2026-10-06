<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseFolder;
use App\Models\CourseFile;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\TestSubmission;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\StudentTestAttempt;
use App\Models\StudentContentPermission;
use App\Helpers\TextNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Removed constructor middleware - handled by routes

    public function index()
    {
        $user = Auth::user();

        // Get enrolled courses with progress
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with([
                'course.lessons.progress' => function ($query) use ($user) {
                    $query->where('student_id', $user->id);
                },
                'course.teacher'
            ])
            ->get();

        // Calculate overall statistics
        $totalCourses = $enrollments->count();
        $completedLessons = LessonProgress::where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $completedTests = TestSubmission::where('student_id', $user->id)
            ->where('passed', true)
            ->count();

        $totalLessons = $enrollments->sum(function ($enrollment) {
            return $enrollment->course->lessons->count();
        });

        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Get recent lesson progress
        $recentLessons = LessonProgress::where('student_id', $user->id)
            ->with(['lesson.course'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($progress) {
                return (object) [
                    'type' => 'lesson',
                    'title' => $progress->lesson->title ?? 'Unknown Lesson',
                    'subtitle' => $progress->lesson->course->title ?? 'Unknown Course',
                    'date' => $progress->updated_at,
                    'status' => $progress->status,
                    'is_completed' => $progress->status === 'completed',
                    'icon' => $progress->status === 'completed' ? 'bi-check-circle-fill' : 'bi-play-circle',
                    'icon_class' => $progress->status === 'completed' ? 'completed' : 'in-progress'
                ];
            });

        // Get recent test results
        $recentTests = TestSubmission::where('student_id', $user->id)
            ->with(['test.course'])
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($submission) {
                return (object) [
                    'type' => 'test',
                    'title' => $submission->test->title ?? 'Unknown Test',
                    'subtitle' => $submission->test->course->title ?? 'Unknown Course',
                    'date' => $submission->submitted_at,
                    'status' => $submission->passed ? 'passed' : 'failed',
                    'is_completed' => true,
                    'icon' => $submission->passed ? 'bi-patch-check-fill' : 'bi-x-circle-fill',
                    'icon_class' => $submission->passed ? 'success' : 'danger'
                ];
            });

        // Merge and sort recent activity
        $recentActivities = $recentLessons->concat($recentTests)
            ->sortByDesc('date')
            ->take(5);

        // Get achievements (Dynamic Calculation)
        $allAchievements = collect([
            [
                'name' => 'First Steps',
                'description' => 'Complete your first lesson',
                'icon' => 'trophy',
                'earned' => $completedLessons > 0,
                'progress' => min(100, $completedLessons * 100),
                'earned_at' => $completedLessons > 0 ?
                    LessonProgress::where('student_id', $user->id)
                        ->where('status', 'completed')
                        ->oldest('completed_at')
                        ->first()?->completed_at : null
            ],
            [
                'name' => 'Dedicated Learner',
                'description' => 'Complete 10 lessons',
                'icon' => 'award',
                'earned' => $completedLessons >= 10,
                'progress' => min(100, ($completedLessons / 10) * 100),
                'earned_at' => $completedLessons >= 10 ?
                    LessonProgress::where('student_id', $user->id)
                        ->where('status', 'completed')
                        ->skip(9)
                        ->first()?->completed_at : null
            ],
            [
                'name' => 'Test Master',
                'description' => 'Pass 5 tests',
                'icon' => 'patch-check',
                'earned' => $completedTests >= 5,
                'progress' => min(100, ($completedTests / 5) * 100),
                'earned_at' => $completedTests >= 5 ?
                    TestSubmission::where('student_id', $user->id)
                        ->where('passed', true)
                        ->skip(4)
                        ->first()?->submitted_at : null
            ]
        ]);

        $achievements = $allAchievements->where('earned', true)->map(function ($item) {
            return (object) $item;
        })->sortByDesc('earned_at')->take(6);

        // Get recommended courses
        $recommendedCourses = Course::where('status', 'published')
            ->whereNotIn('id', $enrollments->pluck('course_id'))
            ->where('level', $user->language_level ?? 'beginner')
            ->limit(3)
            ->get();

        // Study streak calculation
        $currentStreak = $user->current_streak ?? 0;
        $longestStreak = $user->longest_streak ?? 0;

        // Group enrolled courses by CEFR Level for dashboard (A1..C2)
        $levelOrder = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
        $enrollmentsByLevel = collect($levelOrder)
            ->mapWithKeys(function ($level) use ($enrollments) {
                return [
                    $level => $enrollments->filter(function ($enr) use ($level) {
                        return strtoupper($enr->course->level ?? '') === $level;
                    })
                ];
            })
            ->filter(fn($group) => $group->count() > 0);
        // Include any non-standard levels at the end under "Other"
        $others = $enrollments->filter(function ($enr) use ($levelOrder) {
            return !in_array(strtoupper($enr->course->level ?? ''), $levelOrder);
        });
        if ($others->count() > 0) {
            $enrollmentsByLevel->put('Other', $others);
        }

        // Build levels array (label + count) for dashboard level cards
        $levels = $enrollmentsByLevel
            ->keys()
            ->filter(fn($label) => in_array($label, $levelOrder))
            ->map(function ($label) use ($enrollmentsByLevel) {
                return [
                    'level' => $label,
                    'count' => ($enrollmentsByLevel->get($label)?->count()) ?? 0,
                ];
            })
            ->values();

        // Keep legacy variables empty or as is if needed, but recentActivities is the new standard
        $recentProgress = $recentLessons;

        return view('student.dashboard', compact(
            'user',
            'enrollments',
            'enrollmentsByLevel',
            'levels',
            'totalCourses',
            'completedLessons',
            'totalLessons',
            'progressPercentage',
            'recentProgress',
            'recentTests',
            'recentActivities',
            'achievements',
            'recommendedCourses',
            'currentStreak',
            'longestStreak'
        ));
    }

    public function courses()
    {
        $user = Auth::user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.lessons', 'course.teacher'])
            ->paginate(12);

        // Group enrolled courses by CEFR level (A1..C2) for hierarchical view
        $levels = collect();
        if ($enrollments->count() > 0) {
            $grouped = $enrollments->getCollection()->groupBy(function ($enrollment) {
                return strtoupper($enrollment->course->level ?? 'OTHER');
            });
            $levels = $grouped->map(function ($items, $level) {
                return [
                    'level' => $level,
                    'count' => $items->count(),
                ];
            })->values();
        }

        // Get recently evaluated tests
        $evaluatedTests = TestSubmission::select('test_submissions.*', 'student_test_attempts.id as attempt_id')
            ->join('student_test_attempts', function ($join) {
                $join->on('test_submissions.student_id', '=', 'student_test_attempts.student_id')
                    ->on('test_submissions.test_id', '=', 'student_test_attempts.test_id')
                    ->on('test_submissions.attempt_number', '=', 'student_test_attempts.attempt_number');
            })
            ->where('test_submissions.student_id', $user->id)
            ->where('test_submissions.status', 'completed')
            ->with(['test.course'])
            ->orderBy('test_submissions.updated_at', 'desc')
            ->limit(6)
            ->get();

        return view('student.courses', compact('enrollments', 'levels', 'evaluatedTests'));
    }

    public function coursesByLevel(string $level)
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->whereHas('course', function ($q) use ($level) {
                $q->where('level', $level);
            })
            ->with(['course.teacher'])
            ->paginate(12);

        // Build permission-aware counts per course
        $allowedCountsByCourse = [];
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (!$course) {
                continue;
            }
            $allowed = $this->getStudentAllowedContent($user->id, $course);
            $lessonCount = count($allowed['lesson']);
            $testCount = count($allowed['test']);
            $allowedMinutes = empty($allowed['lesson']) ? 0 : Lesson::whereIn('id', $allowed['lesson'])->sum('estimated_time');
            $hours = round((($allowedMinutes ?: 0) / 60), 1);

            $allowedCountsByCourse[$course->id] = [
                'lessons' => $lessonCount,
                'tests' => $testCount,
                'hours' => $hours,
            ];
        }

        return view('student.courses_level', compact('enrollments', 'level', 'allowedCountsByCourse'));
    }

    public function progress()
    {
        $user = Auth::user();

        // Get enrolled courses with progress
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.lessons', 'course.tests'])
            ->get();

        // Calculate statistics
        $totalCourses = $enrollments->count();
        // Count completed lessons, handling both string and boolean status representations
        $completedLessons = LessonProgress::where('student_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'completed')
                    ->orWhere('status', true);
            })
            ->count();
        $completedTests = TestSubmission::where('student_id', $user->id)
            ->where('passed', true)
            ->count();

        // Calculate overall progress
        $totalLessons = $enrollments->sum(function ($enrollment) {
            return $enrollment->course->lessons->count();
        });
        $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        // Get course progress details
        $courseProgress = $enrollments->map(function ($enrollment) use ($user) {
            $course = $enrollment->course;
            $totalLessons = $course->lessons->count();
            $completedLessons = LessonProgress::where('student_id', $user->id)
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->where(function ($q) {
                    $q->where('status', 'completed')
                        ->orWhere('status', true);
                })
                ->count();

            return [
                'course' => $course,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'last_activity' => LessonProgress::where('student_id', $user->id)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->latest('updated_at')
                    ->first()?->updated_at?->diffForHumans(),
                'time_spent' => LessonProgress::where('student_id', $user->id)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->sum('time_spent') ?: 0
            ];
        });

        // Get recent activity
        $recentActivity = LessonProgress::where('student_id', $user->id)
            ->with(['lesson.course'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($progress) {
                return [
                    'title' => $progress->status === 'completed' ? 'Completed lesson' : 'Started lesson',
                    'course' => $progress->lesson->course->title,
                    'date' => $progress->updated_at->diffForHumans(),
                    'description' => $progress->lesson->title
                ];
            });

        // Get real achievements data from database
        $achievements = collect([
            [
                'name' => 'First Steps',
                'description' => 'Complete your first lesson',
                'icon' => 'trophy',
                'earned' => $completedLessons > 0,
                'progress' => min(100, $completedLessons * 100),
                'earned_date' => $completedLessons > 0 ?
                    LessonProgress::where('student_id', $user->id)
                        ->where('status', 'completed')
                        ->oldest('completed_at')
                        ->first()?->completed_at?->diffForHumans() : null
            ],
            [
                'name' => 'Dedicated Learner',
                'description' => 'Complete 10 lessons',
                'icon' => 'award',
                'earned' => $completedLessons >= 10,
                'progress' => min(100, ($completedLessons / 10) * 100),
                'earned_date' => $completedLessons >= 10 ?
                    LessonProgress::where('student_id', $user->id)
                        ->where('status', 'completed')
                        ->skip(9)
                        ->first()?->completed_at?->diffForHumans() : null
            ],
            [
                'name' => 'Test Master',
                'description' => 'Pass 5 tests',
                'icon' => 'patch-check',
                'earned' => $completedTests >= 5,
                'progress' => min(100, ($completedTests / 5) * 100),
                'earned_date' => $completedTests >= 5 ?
                    TestSubmission::where('student_id', $user->id)
                        ->where('passed', true)
                        ->skip(4)
                        ->first()?->submitted_at?->diffForHumans() : null
            ]
        ]);

        $currentStreak = $user->current_streak ?? 0;

        return view('student.progress', compact(
            'totalCourses',
            'completedLessons',
            'completedTests',
            'overallProgress',
            'currentStreak',
            'courseProgress',
            'recentActivity',
            'achievements'
        ));
    }

    /**
     * Resolve permission-aware content and visible folder containers for a student.
     * Implements Transparent Container Browsing:
     * Ancestor folders are visible to navigate through to reach permitted content,
     * but only explicitly granted content (or direct items of explicitly granted folders)
     * are accessible/visible.
     */
    private function getStudentAllowedContent(int $studentId, Course $course): array
    {
        $hasCustomPermissions = StudentContentPermission::where('student_id', $studentId)
            ->where('course_id', $course->id)
            ->exists();

        if (!$hasCustomPermissions) {
            $allFolderIds = CourseFolder::where('course_id', $course->id)->pluck('id')->all();
            return [
                'has_custom' => false,
                'visible_folders' => $allFolderIds,
                'folder' => $allFolderIds,
                'lesson' => $course->lessons()->pluck('id')->all(),
                'test' => $course->tests()->pluck('id')->all(),
                'file' => $course->files()->pluck('id')->all(),
            ];
        }

        $perms = StudentContentPermission::where('student_id', $studentId)
            ->where('course_id', $course->id)
            ->where('has_access', true)
            ->get()
            ->groupBy('content_type')
            ->map(fn($g) => $g->pluck('content_id')->all());

        $explicitFolderIds = $perms['folder'] ?? [];
        $explicitLessonIds = $perms['lesson'] ?? [];
        $explicitTestIds = $perms['test'] ?? [];
        $explicitFileIds = $perms['file'] ?? [];

        // If a folder was explicitly allowed, allow lessons/tests/files directly inside it
        $directFolderLessons = empty($explicitFolderIds) ? [] : Lesson::where('course_id', $course->id)
            ->whereIn('folder_id', $explicitFolderIds)
            ->pluck('id')->all();

        $directFolderTests = empty($explicitFolderIds) ? [] : Test::where('course_id', $course->id)
            ->whereIn('folder_id', $explicitFolderIds)
            ->pluck('id')->all();

        $directFolderFiles = empty($explicitFolderIds) ? [] : CourseFile::where('course_id', $course->id)
            ->whereIn('folder_id', $explicitFolderIds)
            ->pluck('id')->all();

        $allowedLessonIds = array_values(array_unique(array_merge($explicitLessonIds, $directFolderLessons)));
        $allowedTestIds = array_values(array_unique(array_merge($explicitTestIds, $directFolderTests)));
        $allowedFileIds = array_values(array_unique(array_merge($explicitFileIds, $directFolderFiles)));

        // Transparent Container Browsing:
        // Find all folders that hold permitted content or are explicitly allowed
        $lessonFolders = empty($allowedLessonIds) ? [] : Lesson::where('course_id', $course->id)
            ->whereIn('id', $allowedLessonIds)
            ->whereNotNull('folder_id')
            ->pluck('folder_id')->all();

        $testFolders = empty($allowedTestIds) ? [] : Test::where('course_id', $course->id)
            ->whereIn('id', $allowedTestIds)
            ->whereNotNull('folder_id')
            ->pluck('folder_id')->all();

        $fileFolders = empty($allowedFileIds) ? [] : CourseFile::where('course_id', $course->id)
            ->whereIn('id', $allowedFileIds)
            ->whereNotNull('folder_id')
            ->pluck('folder_id')->all();

        $startingFolderIds = array_unique(array_merge($explicitFolderIds, $lessonFolders, $testFolders, $fileFolders));

        // Walk up parent chain for all starting folders to include ancestor containers
        $allFolders = CourseFolder::where('course_id', $course->id)->get(['id', 'parent_folder_id']);
        $parentMap = $allFolders->pluck('parent_folder_id', 'id')->all();

        $visibleFolderIds = [];
        foreach ($startingFolderIds as $fId) {
            $curr = (int)$fId;
            while ($curr && !in_array($curr, $visibleFolderIds, true)) {
                $visibleFolderIds[] = $curr;
                $curr = isset($parentMap[$curr]) && $parentMap[$curr] ? (int)$parentMap[$curr] : null;
            }
        }

        return [
            'has_custom' => true,
            'visible_folders' => $visibleFolderIds,
            'folder' => $explicitFolderIds,
            'lesson' => $allowedLessonIds,
            'test' => $allowedTestIds,
            'file' => $allowedFileIds,
        ];
    }

    /**
     * Show a specific course
     */
    public function showCourse(Course $course)
    {
        // Check if user is enrolled in the course
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You must be enrolled in this course to access it.');
        }

        // Load course relationships
        $course->load(['lessons.contentBlocks', 'tests.questions', 'folders']);

        $allowed = $this->getStudentAllowedContent(Auth::id(), $course);
        $visibleFolderIds = $allowed['visible_folders'];
        $allowedLessonIds = $allowed['lesson'];
        $allowedTestIds = $allowed['test'];
        $allowedFileIds = $allowed['file'];

        $topFolders = \App\Models\CourseFolder::where('course_id', $course->id)
            ->whereNull('parent_folder_id')
            ->orderBy('order_index')
            ->get()
            ->filter(function ($folder) use ($visibleFolderIds) {
                return in_array($folder->id, $visibleFolderIds, true);
            });

        $rootLessons = $course->lessons()->whereNull('folder_id')->orderBy('order_index')->get()
            ->filter(function ($lesson) use ($allowedLessonIds) {
                return in_array($lesson->id, $allowedLessonIds, true);
            });

        $rootTests = $course->tests()->whereNull('folder_id')->orderBy('order_index')->get()
            ->filter(function ($test) use ($allowedTestIds) {
                return in_array($test->id, $allowedTestIds, true);
            });

        // Folder-level allowed counts for sidebar display (lessons, tests, subfolders)
        $allowedLessonsByFolder = \App\Models\Lesson::where('course_id', $course->id)
            ->whereIn('id', $allowedLessonIds)
            ->pluck('folder_id')
            ->countBy();
        $allowedTestsByFolder = \App\Models\Test::where('course_id', $course->id)
            ->whereIn('id', $allowedTestIds)
            ->pluck('folder_id')
            ->countBy();
        $allowedSubfoldersByFolder = \App\Models\CourseFolder::where('course_id', $course->id)
            ->whereIn('id', $visibleFolderIds)
            ->whereNotNull('parent_folder_id')
            ->pluck('parent_folder_id')
            ->countBy();

        $folderAllowedCounts = [];
        foreach ($topFolders as $f) {
            $folderAllowedCounts[$f->id] = [
                'folders' => (int) ($allowedSubfoldersByFolder[$f->id] ?? 0),
                'lessons' => (int) ($allowedLessonsByFolder[$f->id] ?? 0),
                'tests' => (int) ($allowedTestsByFolder[$f->id] ?? 0),
            ];
        }

        // Progress based only on allowed lessons
        $totalLessons = count($allowedLessonIds);
        $completedLessons = LessonProgress::where('student_id', Auth::id())
            ->whereIn('lesson_id', $allowedLessonIds)
            ->where('status', 'completed')
            ->count();
        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Next lesson among allowed lessons (prefer the first not yet completed)
        $completedIds = LessonProgress::where('student_id', Auth::id())
            ->where('status', 'completed')
            ->whereIn('lesson_id', $allowedLessonIds)
            ->pluck('lesson_id')
            ->all();
        $nextLesson = \App\Models\Lesson::where('course_id', $course->id)
            ->whereIn('id', $allowedLessonIds)
            ->whereNotIn('id', $completedIds)
            ->orderBy('order_index')
            ->first();
        if (!$nextLesson) {
            $nextLesson = \App\Models\Lesson::where('course_id', $course->id)
                ->whereIn('id', $allowedLessonIds)
                ->orderBy('order_index')
                ->first();
        }

        // Allowed totals for stats
        $totalTests = count($allowedTestIds);
        $allowedMinutes = \App\Models\Lesson::whereIn('id', $allowedLessonIds)->sum('estimated_time');
        $estimatedDuration = round((($allowedMinutes ?: 0) / 60), 1); // hours (1 decimal)

        // Get recent achievements for this course
        $recentAchievements = collect([]);

        // Check for course-specific achievements
        if ($completedLessons > 0 && $completedLessons == 1) {
            $recentAchievements->push([
                'name' => 'Course Started',
                'description' => 'Started learning ' . $course->title,
                'icon' => 'play-circle',
                'earned_date' => 'Recently'
            ]);
        }

        if ($progressPercentage >= 50) {
            $recentAchievements->push([
                'name' => 'Halfway There',
                'description' => 'Completed 50% of ' . $course->title,
                'icon' => 'speedometer',
                'earned_date' => 'Recently'
            ]);
        }

        // Get root-level files (files not in any folder)
        $rootFiles = $course->files()
            ->whereNull('folder_id')
            ->where(function ($q) {
                $q->where('downloadable', true)
                    ->orWhere('viewable', true);
            })
            ->get()
            ->filter(fn($file) => in_array($file->id, $allowedFileIds, true));

        return view('student.course', compact(
            'course',
            'enrollment',
            'progressPercentage',
            'completedLessons',
            'totalLessons',
            'nextLesson',
            'recentAchievements',
            'estimatedDuration',
            'topFolders',
            'rootLessons',
            'rootTests',
            'rootFiles',
            'totalTests',
            'folderAllowedCounts'
        ));
    }

    /**
     * Enroll authenticated student in a course
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();
        if ($user->role !== 'student') {
            return redirect()->back()->with('error', 'Only students can enroll in courses.');
        }

        Enrollment::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id
        ], [
            'status' => 'active',
            'enrolled_at' => now()
        ]);

        return redirect()->route('student.course.show', $course)
            ->with('success', 'You have successfully enrolled in ' . $course->title . '!');
    }

    /**
     * Show contents of a folder within a course (subfolders, lessons, tests)
     */
    public function showFolder(Course $course, CourseFolder $folder)
    {
        // Check enrollment
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();
        if (!$enrollment) {
            abort(403, 'You must be enrolled in this course to access this folder.');
        }

        // Ensure folder belongs to the course
        if ($folder->course_id !== $course->id) {
            abort(404);
        }


        $allowed = $this->getStudentAllowedContent(Auth::id(), $course);
        $visibleFolderIds = $allowed['visible_folders'];
        $allowedLessonIds = $allowed['lesson'];
        $allowedTestIds = $allowed['test'];
        $allowedFileIds = $allowed['file'];

        if ($allowed['has_custom'] && !in_array($folder->id, $visibleFolderIds, true)) {
            abort(403, 'Access denied for this folder.');
        }

        // Subfolders (transparently visible or explicitly allowed)
        $subFolders = CourseFolder::where('course_id', $course->id)
            ->where('parent_folder_id', $folder->id)
            ->orderBy('order_index')
            ->get()
            ->filter(fn($f) => in_array($f->id, $visibleFolderIds, true));

        // Lessons and tests within this folder (only explicitly allowed)
        $folderLessons = $course->lessons()->where('folder_id', $folder->id)
            ->orderBy('order_index')->get()
            ->filter(fn($l) => in_array($l->id, $allowedLessonIds, true));

        $folderTests = $course->tests()->where('folder_id', $folder->id)
            ->orderBy('order_index')->get()
            ->filter(fn($t) => in_array($t->id, $allowedTestIds, true));

        // Build breadcrumb chain (Course > ...parents... > Current)
        $breadcrumbs = [];
        $current = $folder;
        while ($current) {
            $breadcrumbs[] = $current;
            $current = $current->parentFolder;
        }
        $breadcrumbs = array_reverse($breadcrumbs);


        // Permission-aware counts for subfolders displayed in sidebar
        $subFolderCounts = [];
        if ($subFolders->count() > 0) {
            $subFolderIds = $subFolders->pluck('id')->all();
            $allowedLessonsByFolder = Lesson::whereIn('id', $allowedLessonIds)
                ->whereIn('folder_id', $subFolderIds)
                ->pluck('folder_id')
                ->countBy();
            $allowedTestsByFolder = Test::whereIn('id', $allowedTestIds)
                ->whereIn('folder_id', $subFolderIds)
                ->pluck('folder_id')
                ->countBy();
            foreach ($subFolders as $sf) {
                $subFolderCounts[$sf->id] = [
                    'lessons' => (int) ($allowedLessonsByFolder[$sf->id] ?? 0),
                    'tests' => (int) ($allowedTestsByFolder[$sf->id] ?? 0),
                ];
            }
        }

        // Files within this folder
        $folderFiles = $course->files()
            ->where('folder_id', $folder->id)
            ->where(function ($q) {
                $q->where('downloadable', true)
                    ->orWhere('viewable', true);
            })
            ->get()
            ->filter(fn($f) => in_array($f->id, $allowedFileIds, true));

        return view('student.folder', compact(
            'course',
            'folder',
            'subFolders',
            'folderLessons',
            'folderTests',
            'folderFiles',
            'breadcrumbs',
            'subFolderCounts'
        ));
    }


    /**
     * Show a specific lesson
     */
    public function showLesson(Lesson $lesson)
    {


        // Check if user is enrolled in the course
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $lesson->course_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You must be enrolled in this course to access this lesson.');
        }

        $allowed = $this->getStudentAllowedContent(Auth::id(), $lesson->course);
        if ($allowed['has_custom'] && !in_array($lesson->id, $allowed['lesson'], true)) {
            abort(403, 'Access denied for this lesson.');
        }

        // Get lesson progress
        $progress = LessonProgress::where('student_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->first();

        // Load lesson content blocks and course files
        $lesson->load([
            'contentBlocks',
            'course' => function ($query) {
                $query->with([
                    'files' => function ($q) {
                        $q->where(function ($subQuery) {
                            $subQuery->where('downloadable', true)
                                ->orWhere('viewable', true);
                        });
                    },
                    'folders' => function ($q) {
                        $q->with([
                            'files' => function ($fileQuery) {
                                $fileQuery->where(function ($subQuery) {
                                    $subQuery->where('downloadable', true)
                                        ->orWhere('viewable', true);
                                });
                            }
                        ]);
                    }
                ]);
            }
        ]);

        // Calculate lesson progress
        $totalBlocks = $lesson->contentBlocks->count();
        $currentBlock = 0; // Would track which block user is currently viewing
        $progressPercentage = $totalBlocks > 0 ? round((($currentBlock + 1) / $totalBlocks) * 100) : 0;
        $isCompleted = $progress && $progress->status === 'completed';

        // Find next lesson
        $nextLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        return view('student.lesson', compact(
            'lesson',
            'progress',
            'totalBlocks',
            'currentBlock',
            'progressPercentage',
            'isCompleted',
            'nextLesson'
        ));
    }

    /**
     * Complete a lesson
     */
    public function completeLesson(Request $request, Lesson $lesson)
    {
        // Check enrollment
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $lesson->course_id)
            ->first();

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Not enrolled'], 403);
        }

        // Create or update lesson progress
        $progress = LessonProgress::updateOrCreate(
            [
                'student_id' => Auth::id(),
                'lesson_id' => $lesson->id
            ],
            [
                'status' => 'completed',
                'completed_at' => now()
            ]
        );

        // Award points and update level if this is a new completion
        if ($progress->wasRecentlyCreated || ($progress->status === 'completed' && $progress->updated_at->diffInSeconds($progress->created_at) < 5)) {
            $user = Auth::user();
            $pointsAwarded = 10; // Base points per lesson

            // Award bonus points based on lesson difficulty (if available)
            if ($lesson->estimated_time) {
                $pointsAwarded += ceil($lesson->estimated_time / 10); // 1 point per 10 minutes
            }

            // Update user points
            $user->points += $pointsAwarded;

            // Update level based on points (every 100 points = 1 level)
            $newLevel = floor($user->points / 100) + 1;
            $user->level = $newLevel;

            // Update streak
            $lastProgress = LessonProgress::where('student_id', $user->id)
                ->where('id', '!=', $progress->id)
                ->where('status', 'completed')
                ->latest('completed_at')
                ->first();

            if ($lastProgress && $lastProgress->completed_at->isToday()) {
                // Same day, don't increment streak
            } elseif ($lastProgress && $lastProgress->completed_at->isYesterday()) {
                // Consecutive day, increment streak
                $user->current_streak += 1;
                if ($user->current_streak > $user->longest_streak) {
                    $user->longest_streak = $user->current_streak;
                }
            } else {
                // New streak or gap in streak
                $user->current_streak = 1;
            }

            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson completed successfully!',
            'points_awarded' => $pointsAwarded ?? 0,
            'new_level' => Auth::user()->fresh()->level
        ]);
    }

    /**
     * Show a specific test
     */
    public function showTest(Test $test)
    {
        // Check if user is enrolled in the course
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $test->course_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You must be enrolled in this course to access this test.');
        }

        $allowed = $this->getStudentAllowedContent(Auth::id(), $test->course);
        if ($allowed['has_custom'] && !in_array($test->id, $allowed['test'], true)) {
            abort(403, 'Access denied for this test.');
        }

        // Load test questions with options
        $test->load(['questions.options', 'course']);

        // Get user's attempts for this test
        $attempts = StudentTestAttempt::where('student_id', Auth::id())
            ->where('test_id', $test->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        // Check if user can take the test
        $canTakeTest = true;
        $attemptsLeft = $test->max_attempts;
        $currentAttempt = $attempts->count() + 1;

        if ($test->max_attempts && $attempts->count() >= $test->max_attempts) {
            $canTakeTest = false;
            $attemptsLeft = 0;
        } else if ($test->max_attempts) {
            $attemptsLeft = $test->max_attempts - $attempts->count();
        }

        // Check if there's an active attempt
        $activeAttempt = $attempts->where('status', 'in_progress')->first();

        // If no active attempt and user can take test, create one
        if (!$activeAttempt && $canTakeTest) {
            $activeAttempt = StudentTestAttempt::create([
                'student_id' => Auth::id(),
                'test_id' => $test->id,
                'attempt_number' => $currentAttempt,
                'status' => 'in_progress',
                'started_at' => now()
            ]);
        }

        // Calculate test data
        $totalQuestions = $test->questions->count();
        $currentQuestion = 0; // Starting question

        return view('student.test', compact(
            'test',
            'attempts',
            'canTakeTest',
            'attemptsLeft',
            'currentAttempt',
            'totalQuestions',
            'currentQuestion',
            'activeAttempt'
        ));
    }

    /**
     * Preview test (for admins/teachers)
     */
    public function previewTest(Test $test)
    {
        $user = Auth::user();

        // Allow admins and teachers to preview tests
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Access denied. Admin or teacher role required.');
        }

        // Load test questions with options
        $test->load(['questions.options']);

        return view('student.test.preview', compact('test'));
    }

    /**
     * Preview lesson (for admins/teachers)
     */
    public function previewLesson(Lesson $lesson)
    {
        $user = Auth::user();

        // Allow admins and teachers to preview lessons
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Access denied. Admin or teacher role required.');
        }

        // Load lesson content blocks
        $lesson->load('contentBlocks');

        return view('student.lesson.preview', compact('lesson'));
    }

    /**
     * Start a test attempt
     */
    public function startTest(Request $request, Test $test)
    {
        // Check enrollment and attempt limits
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $test->course_id)
            ->first();

        if (!$enrollment) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Not enrolled in course'], 403);
            }
            return back()->with('error', 'Not enrolled in course.');
        }

        $allowed = $this->getStudentAllowedContent(Auth::id(), $test->course);
        if ($allowed['has_custom'] && !in_array($test->id, $allowed['test'], true)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Access denied for this test.'], 403);
            }
            abort(403, 'Access denied for this test.');
        }

        $attempts = StudentTestAttempt::where('student_id', Auth::id())
            ->where('test_id', $test->id)
            ->count();

        if ($test->max_attempts && $attempts >= $test->max_attempts) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Maximum attempts reached'], 403);
            }
            return back()->with('error', 'Maximum attempts reached.');
        }

        // Create new attempt
        $attempt = StudentTestAttempt::create([
            'student_id' => Auth::id(),
            'test_id' => $test->id,
            'attempt_number' => $attempts + 1,
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'attempt_id' => $attempt->id,
                'redirect_url' => route('student.test.show', $test->id)
            ]);
        }

        return redirect()->route('student.test.show', $test->id)->with('success', 'New attempt started.');
    }

    /**
     * Submit test answers
     */
    public function submitTest(Request $request, \App\Models\Test $test)
    {
        // If answers are base64 encoded to bypass WAF, decode them before validation
        if ($request->has('is_encoded') && $request->input('is_encoded') === true && is_string($request->input('answers'))) {
            try {
                $decodedJson = base64_decode($request->input('answers'));
                $decodedAnswers = json_decode($decodedJson, true);
                if (is_array($decodedAnswers)) {
                    $request->merge(['answers' => $decodedAnswers]);
                }
            } catch (\Exception $e) {
                // Fail gracefully and let validation handle it
            }
        }

        try {
            $request->validate([
                'test_id' => 'required|exists:tests,id',
                'answers' => 'required|array',
                'time_taken' => 'nullable|integer'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        }

        try {

            // Check enrollment
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $test->course_id)
                ->first();

            if (!$enrollment) {
                return response()->json(['success' => false, 'message' => 'Not enrolled in course'], 403);
            }

            $allowed = $this->getStudentAllowedContent(Auth::id(), $test->course);
            if ($allowed['has_custom'] && !in_array($test->id, $allowed['test'], true)) {
                return response()->json(['success' => false, 'message' => 'Access denied for this test.'], 403);
            }

            // Get current attempt
            $currentAttempt = StudentTestAttempt::where('student_id', Auth::id())
                ->where('test_id', $test->id)
                ->where('status', 'in_progress')
                ->latest()
                ->first();

            if (!$currentAttempt) {
                // No active attempt found — create one now if the user is allowed to take the test
                $attemptsCount = StudentTestAttempt::where('student_id', Auth::id())
                    ->where('test_id', $test->id)
                    ->count();

                if ($test->max_attempts && $attemptsCount >= $test->max_attempts) {
                    return response()->json(['success' => false, 'message' => 'Maximum attempts reached'], 403);
                }

                $currentAttempt = StudentTestAttempt::create([
                    'student_id' => Auth::id(),
                    'test_id' => $test->id,
                    'attempt_number' => $attemptsCount + 1,
                    'status' => 'in_progress',
                    'started_at' => now()
                ]);
            }

            // Load test questions with options and ensure same order as the client (sort by 'order')
            $test->load('questions.options');
            $questions = $test->questions->sortBy('order')->values();

            // Calculate score
            $totalQuestions = $questions->count();
            $correctAnswers = 0;
            $answers = $request->answers;

            foreach ($questions as $index => $question) {
                $questionKey = (string) $index;
                $userAnswer = $answers[$questionKey] ?? null;

                if ($question->type === 'fill_blanks') {
                    // Handle fill in the blanks
                    $correctAnswerArray = $question->correct_answer ?? [];
                    $userAnswerArray = is_array($userAnswer) ? $userAnswer : [];

                    $correctCount = 0;
                    foreach ($correctAnswerArray as $i => $correctAnswer) {
                        if (
                            isset($userAnswerArray[$i]) &&
                            TextNormalizer::compare($userAnswerArray[$i], $correctAnswer)
                        ) {
                            $correctCount++;
                        }
                    }

                    if ($correctCount === count($correctAnswerArray)) {
                        $correctAnswers++;
                    }
                } else {
                    // Handle MCQ-like questions; ensure options are in the same order as rendered (order asc)
                    $correctOptionIndex = null;
                    $options = $question->options->sortBy('order')->values();
                    foreach ($options as $optionIndex => $option) {
                        if ($option->is_correct) {
                            $correctOptionIndex = $optionIndex;
                            break;
                        }
                    }

                    if ($userAnswer !== null && (int) $userAnswer === $correctOptionIndex) {
                        $correctAnswers++;
                    }
                }
            }

            $scorePercentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

            // Check if test needs manual evaluation
            // We check if the test has any question of type 'expression_ecrite'
            // OR if the test type itself is set (fallback)
            $isExpressionEcrite = ($test->type === 'expression_ecrite') ||
                $test->questions()->where('type', 'expression_ecrite')->exists();
            $status = $isExpressionEcrite ? 'pending' : 'completed';

            if ($isExpressionEcrite) {
                // For pending evaluation, score is tentative (0) and not passed yet
                $scorePercentage = 0;
                $passed = false;
            } else {
                $passed = $scorePercentage >= $test->passing_score;
            }

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                // Update attempt
                $currentAttempt->update([
                    'status' => 'completed', // Attempt is finished even if evaluation is pending
                    'completed_at' => now(),
                    'score' => $scorePercentage,
                    'passed' => $passed,
                    'time_taken' => $request->time_taken
                ]);

                // Create test submission record
                $submission = TestSubmission::create([
                    'student_id' => Auth::id(),
                    'test_id' => $test->id,
                    'score' => $scorePercentage,
                    'passed' => $passed,
                    'submitted_at' => now(),
                    'answers' => $answers,
                    'time_taken' => $request->time_taken,
                    'attempt_number' => $currentAttempt->attempt_number,
                    'status' => $status
                ]);

                // Award points if test is passed (skip for pending evaluation)
                if ($passed && !$isExpressionEcrite) {
                    $user = Auth::user();
                    $pointsAwarded = 25; // Base points for passing a test

                    // Award bonus points based on score
                    if ($scorePercentage >= 90) {
                        $pointsAwarded += 15; // Perfect score bonus
                    } elseif ($scorePercentage >= 80) {
                        $pointsAwarded += 10; // Good score bonus
                    } elseif ($scorePercentage >= 70) {
                        $pointsAwarded += 5; // Passing bonus
                    }

                    // Update user points
                    $user->points += $pointsAwarded;

                    // Update level based on points (every 100 points = 1 level)
                    $newLevel = floor($user->points / 100) + 1;
                    $user->level = $newLevel;

                    $user->save();
                }

                \Illuminate\Support\Facades\DB::commit();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $e;
            }

            return response()->json([
                'success' => true,
                'message' => 'Test submitted successfully',
                'redirect_url' => route('student.test.results', ['test' => $test->id, 'attempt' => $currentAttempt->id])
            ]);

        } catch (\Exception $e) {
            \Log::error('Test submission error', [
                'user_id' => Auth::id(),
                'test_id' => $test->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error submitting test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show test results
     */
    public function showTestResults(\App\Models\Test $test, \App\Models\StudentTestAttempt $attempt)
    {
        // Verify the attempt belongs to the current user (or user is admin)
        $studentId = $attempt->student_id;
        if ($studentId !== Auth::id() && Auth::user()?->role !== 'admin') {
            abort(403, 'Access denied.');
        }

        // Check enrollment if student
        if (Auth::user()?->role !== 'admin') {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $test->course_id)
                ->first();

            if (!$enrollment) {
                abort(403, 'You must be enrolled in this course to view results.');
            }
        }

        // Load test with questions and options (we'll also create a sorted collection for consistent indexing)
        $test->load(['questions.options', 'course']);
        $questions = $test->questions->sortBy('order')->values();

        // Get all attempts for this test
        $allAttempts = StudentTestAttempt::where('student_id', $studentId)
            ->where('test_id', $test->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        // Check if user can retake the test
        $canRetake = true;
        $attemptsLeft = $test->max_attempts;

        if ($test->max_attempts && $allAttempts->count() >= $test->max_attempts) {
            $canRetake = false;
            $attemptsLeft = 0;
        } else if ($test->max_attempts) {
            $attemptsLeft = $test->max_attempts - $allAttempts->count();
        }

        // Load submission for this attempt (used for score and answers)
        $submission = TestSubmission::where('student_id', $studentId)
            ->where('test_id', $test->id)
            ->where('attempt_number', $attempt->attempt_number)
            ->latest('submitted_at')
            ->first();

        // Check for pending evaluation (Expression Ecrite)
        // Check finding explicit test type or checking questions
        $isExpressionEcrite = ($test->type === 'expression_ecrite') ||
            $test->questions->where('type', 'expression_ecrite')->isNotEmpty();

        $evaluationPending = $isExpressionEcrite && ($submission->status ?? 'completed') === 'pending';
        $teacherRemarks = $submission->remarks ?? null;

        // For pending evaluations, show 0 or "Pending" score
        if ($evaluationPending) {
            $displayScore = 0;
            $displayPassed = false;
        } else {
            $displayScore = $submission->score ?? ($attempt->score ?? 0);
            $displayPassed = $submission->passed ?? ($attempt->passed ?? ($displayScore >= $test->passing_score));
        }



        // Build detailed results (always show after submission)
        $detailedResults = null;
        $answersForReview = $submission->answers ?? $attempt->answers;
        if ($answersForReview) {
            $detailedResults = [];
            foreach ($questions as $index => $question) {
                $questionKey = (string) $index;
                $userAnswer = $answersForReview[$questionKey] ?? null;

                $result = [
                    'question' => $question,
                    'user_answer' => $userAnswer,
                    'is_correct' => false,
                    'correct_answer' => null
                ];

                if ($question->type === 'fill_blanks') {
                    $correctAnswers = $question->correct_answer ?? [];
                    $userAnswers = is_array($userAnswer) ? $userAnswer : [];

                    $result['correct_answer'] = $correctAnswers;
                    $result['is_correct'] = count($correctAnswers) === count(array_filter($correctAnswers, function ($correct, $i) use ($userAnswers) {
                        return isset($userAnswers[$i]) && TextNormalizer::compare($userAnswers[$i], $correct);
                    }, ARRAY_FILTER_USE_BOTH));
                    $result['correct_answer'] = $correctAnswers;
                    $result['is_correct'] = count($correctAnswers) === count(array_filter($correctAnswers, function ($correct, $i) use ($userAnswers) {
                        return isset($userAnswers[$i]) && TextNormalizer::compare($userAnswers[$i], $correct);
                    }, ARRAY_FILTER_USE_BOTH));

                } elseif ($question->type === 'expression_ecrite') {
                    // Pass the full answer structure (which might be an array with correction)
                    // If it's a legacy simple string answer, normalized it
                    // Actually $userAnswer here comes from $answersForReview[$questionKey].
                    // In the Admin update, we saved answers as an array where key is questionId => ['answer' => ..., 'correction' => ...]
                    // But here $answersForReview is simply the JSON array stored in DB.
                    // Wait, let's check how we save it in Admin DashboardController.
                    // $answers[$questionId]['correction'] = $correction;
                    // So $userAnswer might be an array ['answer' => '...', 'correction' => '...'] OR just 'string' (old format)

                    $result['formatted_answer'] = is_array($userAnswer) ? $userAnswer : ['answer' => $userAnswer];
                    // Override user_answer with just the text part for display compatibility if needed, 
                    // but results.blade.php uses $result['user_answer'] for original text.
                    $result['user_answer'] = $result['formatted_answer']['answer'] ?? ($userAnswer ?: '');

                    $result['is_correct'] = false; // Manual grading
                } else {
                    // MCQ questions - ensure option indices match render order
                    $options = $question->options->sortBy('order')->values();
                    foreach ($options as $optionIndex => $option) {
                        if ($option->is_correct) {
                            $result['correct_answer'] = $optionIndex;
                            $result['is_correct'] = $userAnswer !== null && (int) $userAnswer === $optionIndex;
                            break;
                        }
                    }
                }

                $detailedResults[] = $result;
            }
        }

        return view('student.test.results', compact(
            'test',
            'attempt',
            'allAttempts',
            'canRetake',
            'attemptsLeft',
            'detailedResults'
        ) + [
            'displayScore' => $displayScore,
            'displayPassed' => $displayPassed,
            'evaluationPending' => $evaluationPending,
            'teacherRemarks' => $teacherRemarks
        ]);

    }

    /**
     * Download a course file
     */
    public function downloadFile(\App\Models\CourseFile $file)
    {
        // Enforce downloadable permission strictly
        if (!$file->downloadable && Auth::user()?->role !== 'admin') {
            abort(403, 'Downloading is disabled for this file.');
        }

        // Check if user is enrolled in the course (or staff)
        if (Auth::user()?->role !== 'admin' && Auth::user()?->role !== 'teacher') {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $file->course_id)
                ->first();

            if (!$enrollment) {
                abort(403, 'You must be enrolled in this course to download files.');
            }

            // Check student content permissions
            $allowed = $this->getStudentAllowedContent(Auth::id(), $file->course);
            if ($allowed['has_custom'] && !in_array($file->id, $allowed['file'], true)) {
                abort(403, 'Access denied for this file.');
            }
        }

        // Determine physical file path
        $pathPublic = storage_path('app/public/' . $file->path);
        $pathUploads = base_path('storage/uploads/' . $file->path);

        if (file_exists($pathPublic)) {
            return response()->download($pathPublic, $file->original_name ?? $file->filename);
        } elseif (file_exists($pathUploads)) {
            return response()->download($pathUploads, $file->original_name ?? $file->filename);
        }

        \Log::warning('File download failed: File not found', [
            'file_id' => $file->id,
            'db_path' => $file->path,
            'tried_public' => $pathPublic,
            'tried_uploads' => $pathUploads
        ]);

        abort(404, 'File not found on server.');
    }

    /**
     * Preview a course file inline (e.g. for PDF or browser-renderable documents)
     */
    public function previewFile(Request $request, \App\Models\CourseFile $file)
    {
        $user = Auth::user();
        $isStaff = in_array($user?->role, ['admin', 'teacher']);

        if (!$isStaff) {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $file->course_id)
                ->first();

            if (!$enrollment) {
                abort(403, 'You must be enrolled in this course to view files.');
            }

            // Check student content permissions
            $allowed = $this->getStudentAllowedContent(Auth::id(), $file->course);
            if ($allowed['has_custom'] && !in_array($file->id, $allowed['file'], true)) {
                abort(403, 'Access denied for this file.');
            }

            // Check viewable flag
            if (!$file->viewable) {
                abort(403, 'Viewing is disabled for this file.');
            }
        }

        // Locate physical file
        $pathPublic = storage_path('app/public/' . $file->path);
        $pathUploads = base_path('storage/uploads/' . $file->path);
        $filePath = file_exists($pathPublic) ? $pathPublic : (file_exists($pathUploads) ? $pathUploads : null);

        if (!$filePath) {
            abort(404, 'File not found on server.');
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $extensionMimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
        ];

        $mimeType = $extensionMimes[$ext] ?? ($file->mime_type ?: 'application/octet-stream');

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . addslashes($file->original_name ?? $file->filename) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

