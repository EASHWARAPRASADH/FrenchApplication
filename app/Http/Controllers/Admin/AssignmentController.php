<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\CourseFolder;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\CourseFile;
use App\Models\StudentContentPermission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display course assignments
     */
    public function index()
    {
        // Get all courses with enrollment counts
        $courses = Course::withCount(['enrollments', 'lessons', 'tests'])
            ->with(['teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all students
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        // Get recent enrollments
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.assignments.index', compact('courses', 'students', 'recentEnrollments'));
    }

    /**
     * Show assignment form for a specific course
     */
    public function show(Course $course)
    {
        // Load course relationships
        $course->load(['lessons', 'tests']);

        // Get all students
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        // Get currently enrolled students for this course
        $enrollments = $course->enrollments()->with('user')->get();
        $enrolledStudents = $enrollments->map(function ($enrollment) {
            $user = $enrollment->user;
            $user->pivot = $enrollment; // Add enrollment data as pivot
            return $user;
        });

        // Get students not enrolled in this course
        $availableStudents = $students->diff($enrolledStudents);

        return view('admin.assignments.show', compact('course', 'enrolledStudents', 'availableStudents'));
    }

    /**
     * Assign course to students
     */
    public function assign(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $assignedCount = 0;
            $alreadyEnrolled = [];

            foreach ($request->student_ids as $studentId) {
                // Check if student is already enrolled
                $existingEnrollment = Enrollment::where('user_id', $studentId)
                    ->where('course_id', $course->id)
                    ->first();

                if ($existingEnrollment) {
                    $student = User::find($studentId);
                    $alreadyEnrolled[] = $student->name;
                    continue;
                }

                // Create new enrollment
                Enrollment::create([
                    'user_id' => $studentId,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'progress_percentage' => 0,
                    'payment_status' => 'free',
                    'payment_amount' => 0
                ]);

                $assignedCount++;
            }

            DB::commit();

            $message = "Successfully assigned {$assignedCount} student(s) to the course.";
            if (!empty($alreadyEnrolled)) {
                $message .= " Note: " . implode(', ', $alreadyEnrolled) . " were already enrolled.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'assigned_count' => $assignedCount,
                'already_enrolled' => $alreadyEnrolled
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error assigning students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove student from course
     */
    public function unassign(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:users,id'
        ]);

        try {
            $enrollment = Enrollment::where('user_id', $request->student_id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not enrolled in this course.'
                ], 404);
            }

            $student = User::find($request->student_id);
            $enrollment->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully removed {$student->name} from the course."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course assignment data for AJAX
     */
    public function getCourseData(Course $course): JsonResponse
    {
        $course->load(['enrollments.user', 'teacher']);

        return response()->json([
            'success' => true,
            'course' => $course,
            'enrolled_students' => $course->enrollments->map(function ($enrollment) {
                return [
                    'id' => $enrollment->user->id,
                    'name' => $enrollment->user->name,
                    'email' => $enrollment->user->email,
                    'enrolled_at' => $enrollment->enrolled_at->format('M d, Y'),
                    'progress' => $enrollment->progress_percentage,
                    'status' => $enrollment->status
                ];
            })
        ]);
    }
    /**
     * Manage granular content permissions for a student in a course.
     */
    /**
     * Manage granular content permissions for a student in a course.
     */
    public function permissions(Course $course, $student_id, Request $request)
    {
        $student = User::findOrFail($student_id);

        // Ensure student is enrolled
        $enrolled = Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists();
        if (!$enrolled) {
            return redirect()->route('admin.assignments.course.show', $course)->with('error', 'Student is not enrolled in this course.');
        }

        $perPage = 20;
        $cacheMinutes = 10;
        $cachePrefix = "permissions:{$course->id}:{$student->id}";

        // Load top-level items with nested relations (cached, unpaginated for tree view)
        $topFolders = \Cache::remember(
            "{$cachePrefix}:top-folders",
            now()->addMinutes($cacheMinutes),
            fn() => CourseFolder::where('course_id', $course->id)
                ->whereNull('parent_folder_id')
                ->with([
                    'subfolders.lessons',
                    'subfolders.tests',
                    'subfolders.files',
                    'subfolders.subfolders',
                    'lessons',
                    'tests',
                    'files',
                ])
                ->orderBy('order_index')
                ->get()
        );

        $rootLessons = \Cache::remember(
            "{$cachePrefix}:root-lessons",
            now()->addMinutes($cacheMinutes),
            fn() => Lesson::where('course_id', $course->id)
                ->whereNull('folder_id')
                ->orderBy('order_index')
                ->get()
        );

        $rootTests = \Cache::remember(
            "{$cachePrefix}:root-tests",
            now()->addMinutes($cacheMinutes),
            fn() => Test::where('course_id', $course->id)
                ->whereNull('folder_id')
                ->orderBy('order_index')
                ->get()
        );

        $rootFiles = \Cache::remember(
            "{$cachePrefix}:root-files",
            now()->addMinutes($cacheMinutes),
            fn() => CourseFile::where('course_id', $course->id)
                ->whereNull('folder_id')
                ->orderBy('order_index')
                ->get()
        );

        // Load all items for granular control (NO pagination, show full list)
        $allFolders = \Cache::remember(
            "{$cachePrefix}:all-folders",
            now()->addMinutes($cacheMinutes),
            fn() => CourseFolder::where('course_id', $course->id)
                ->with('parentFolder')
                ->orderBy('parent_folder_id')
                ->orderBy('order_index')
                ->get()
        );

        $allLessons = \Cache::remember(
            "{$cachePrefix}:all-lessons",
            now()->addMinutes($cacheMinutes),
            fn() => Lesson::where('course_id', $course->id)
                ->with('folder')
                ->orderBy('order_index')
                ->get()
        );

        $allTests = \Cache::remember(
            "{$cachePrefix}:all-tests",
            now()->addMinutes($cacheMinutes),
            fn() => Test::where('course_id', $course->id)
                ->with('folder')
                ->orderBy('order_index')
                ->get()
        );

        // Existing permissions for this student+course keyed by tuple (cached)
        $existing = \Cache::remember(
            "{$cachePrefix}:existing",
            now()->addMinutes($cacheMinutes),
            fn() => StudentContentPermission::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->get()
                ->keyBy(function ($p) {
                    return $p->content_type . ':' . $p->content_id;
                })
        );

        return view('admin.assignments.permissions', compact(
            'course',
            'student',
            'topFolders',
            'rootLessons',
            'rootTests',
            'rootFiles',
            'allFolders',
            'allLessons',
            'allTests',
            'existing'
        ));
    }

    /**
     * Save granular permissions for a student in a course.
     */
    public function savePermissions(Request $request, Course $course, $student_id)
    {
        $student = User::findOrFail($student_id);
        $request->validate([
            'permissions' => 'array'
        ]);

        // Ensure enrollment
        $enrolled = Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists();
        if (!$enrolled) {
            return back()->with('error', 'Student is not enrolled in this course.');
        }

        try {
            DB::beginTransaction();

            $perms = $request->input('permissions', []);
            $now = now();
            $adminId = auth()->id();

            // Strategy: upsert records for all submitted entries; remove any not submitted if they belong to this course+student
            $seenKeys = [];
            foreach (['folder', 'lesson', 'test', 'file'] as $type) {
                if (!isset($perms[$type]) || !is_array($perms[$type]))
                    continue;
                foreach ($perms[$type] as $id => $value) {
                    $key = $type . ':' . (int) $id;
                    $seenKeys[] = $key;
                    StudentContentPermission::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                            'content_type' => $type,
                            'content_id' => (int) $id,
                        ],
                        [
                            'has_access' => (bool) $value,
                            'granted_by' => $adminId,
                            'granted_at' => $now,
                        ]
                    );
                }
            }

            // Revoke any previously granted access that was not submitted (deny-by-default)
            StudentContentPermission::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->where('has_access', true)
                ->whereNotIn(DB::raw("CONCAT(content_type,':',content_id)"), $seenKeys)
                ->delete();

            DB::commit();

            // Clear permission cache for this student+course
            $this->clearPermissionCache($course->id, $student->id);

            return back()->with('success', 'Permissions updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save permissions: ' . $e->getMessage());
        }
    }

    /**
     * Quick allocate a specific set/sub-folder to a student (Solo mode or Progressive unlock).
     */
    public function quickAssignSet(Request $request, Course $course, $student_id)
    {
        $student = User::findOrFail($student_id);

        $request->validate([
            'section_folder_id' => 'required|exists:course_folders,id',
            'target_folder_id' => 'nullable|exists:course_folders,id',
            'mode' => 'required|in:solo,progressive_next,revoke_section',
        ]);

        $sectionFolder = CourseFolder::where('id', $request->section_folder_id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $mode = $request->mode;
        $targetFolder = null;
        if (in_array($mode, ['solo', 'progressive_next'])) {
            if (!$request->target_folder_id) {
                return $request->wantsJson() || $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Target set/folder is required.'], 422)
                    : back()->with('error', 'Target set/folder is required.');
            }
            $targetFolder = CourseFolder::where('id', $request->target_folder_id)
                ->where('course_id', $course->id)
                ->firstOrFail();
        }

        try {
            DB::beginTransaction();
            $adminId = auth()->id();
            $now = now();

            if ($mode === 'solo') {
                // 1. Find all sibling folders under section_folder_id except target_folder_id
                $siblingFolderIds = CourseFolder::where('course_id', $course->id)
                    ->where('parent_folder_id', $sectionFolder->id)
                    ->where('id', '!=', $targetFolder->id)
                    ->pluck('id')
                    ->all();

                if (!empty($siblingFolderIds)) {
                    // Revoke sibling folders
                    StudentContentPermission::where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->where('content_type', 'folder')
                        ->whereIn('content_id', $siblingFolderIds)
                        ->delete();

                    // Revoke tests & lessons in sibling folders
                    $siblingTestIds = Test::where('course_id', $course->id)
                        ->whereIn('folder_id', $siblingFolderIds)
                        ->pluck('id')->all();
                    if (!empty($siblingTestIds)) {
                        StudentContentPermission::where('student_id', $student->id)
                            ->where('course_id', $course->id)
                            ->where('content_type', 'test')
                            ->whereIn('content_id', $siblingTestIds)
                            ->delete();
                    }

                    $siblingLessonIds = Lesson::where('course_id', $course->id)
                        ->whereIn('folder_id', $siblingFolderIds)
                        ->pluck('id')->all();
                    if (!empty($siblingLessonIds)) {
                        StudentContentPermission::where('student_id', $student->id)
                            ->where('course_id', $course->id)
                            ->where('content_type', 'lesson')
                            ->whereIn('content_id', $siblingLessonIds)
                            ->delete();
                    }
                }

                // 2. Grant target folder
                StudentContentPermission::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'content_type' => 'folder',
                        'content_id' => $targetFolder->id,
                    ],
                    [
                        'has_access' => true,
                        'granted_by' => $adminId,
                        'granted_at' => $now,
                    ]
                );

                // 3. Grant all tests and lessons inside target folder
                $targetTests = Test::where('course_id', $course->id)->where('folder_id', $targetFolder->id)->pluck('id');
                foreach ($targetTests as $tId) {
                    StudentContentPermission::updateOrCreate(
                        ['student_id' => $student->id, 'course_id' => $course->id, 'content_type' => 'test', 'content_id' => $tId],
                        ['has_access' => true, 'granted_by' => $adminId, 'granted_at' => $now]
                    );
                }

                $targetLessons = Lesson::where('course_id', $course->id)->where('folder_id', $targetFolder->id)->pluck('id');
                foreach ($targetLessons as $lId) {
                    StudentContentPermission::updateOrCreate(
                        ['student_id' => $student->id, 'course_id' => $course->id, 'content_type' => 'lesson', 'content_id' => $lId],
                        ['has_access' => true, 'granted_by' => $adminId, 'granted_at' => $now]
                    );
                }

                $message = "Solo Mode Active: '{$targetFolder->name}' assigned exclusively. Other sets in '{$sectionFolder->name}' were locked.";

            } elseif ($mode === 'progressive_next') {
                // Grant target folder and its tests/lessons without revoking previous ones
                StudentContentPermission::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'content_type' => 'folder',
                        'content_id' => $targetFolder->id,
                    ],
                    [
                        'has_access' => true,
                        'granted_by' => $adminId,
                        'granted_at' => $now,
                    ]
                );

                $targetTests = Test::where('course_id', $course->id)->where('folder_id', $targetFolder->id)->pluck('id');
                foreach ($targetTests as $tId) {
                    StudentContentPermission::updateOrCreate(
                        ['student_id' => $student->id, 'course_id' => $course->id, 'content_type' => 'test', 'content_id' => $tId],
                        ['has_access' => true, 'granted_by' => $adminId, 'granted_at' => $now]
                    );
                }

                $targetLessons = Lesson::where('course_id', $course->id)->where('folder_id', $targetFolder->id)->pluck('id');
                foreach ($targetLessons as $lId) {
                    StudentContentPermission::updateOrCreate(
                        ['student_id' => $student->id, 'course_id' => $course->id, 'content_type' => 'lesson', 'content_id' => $lId],
                        ['has_access' => true, 'granted_by' => $adminId, 'granted_at' => $now]
                    );
                }

                $message = "Progressive Mode: '{$targetFolder->name}' unlocked successfully.";

            } elseif ($mode === 'revoke_section') {
                // Revoke all folders under this section (and section itself)
                $childFolderIds = CourseFolder::where('course_id', $course->id)
                    ->where('parent_folder_id', $sectionFolder->id)
                    ->pluck('id')->all();
                $allFolderIdsToRevoke = array_merge([$sectionFolder->id], $childFolderIds);

                StudentContentPermission::where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->where('content_type', 'folder')
                    ->whereIn('content_id', $allFolderIdsToRevoke)
                    ->delete();

                $allTestIds = Test::where('course_id', $course->id)
                    ->whereIn('folder_id', $allFolderIdsToRevoke)
                    ->pluck('id')->all();
                if (!empty($allTestIds)) {
                    StudentContentPermission::where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->where('content_type', 'test')
                        ->whereIn('content_id', $allTestIds)
                        ->delete();
                }

                $allLessonIds = Lesson::where('course_id', $course->id)
                    ->whereIn('folder_id', $allFolderIdsToRevoke)
                    ->pluck('id')->all();
                if (!empty($allLessonIds)) {
                    StudentContentPermission::where('student_id', $student->id)
                        ->where('course_id', $course->id)
                        ->where('content_type', 'lesson')
                        ->whereIn('content_id', $allLessonIds)
                        ->delete();
                }

                $message = "Locked all sets under '{$sectionFolder->name}'.";
            }

            DB::commit();

            $this->clearPermissionCache($course->id, $student->id);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error applying set allocation: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error applying set allocation: ' . $e->getMessage());
        }
    }

    /**
     * Clear cached permission data for a specific course and student
     */
    private function clearPermissionCache(int $courseId, int $studentId): void
    {
        $prefix = "permissions:{$courseId}:{$studentId}";

        // Clear top-level caches
        \Cache::forget("{$prefix}:top-folders");
        \Cache::forget("{$prefix}:root-lessons");
        \Cache::forget("{$prefix}:root-tests");
        \Cache::forget("{$prefix}:root-files");

        \Cache::forget("{$prefix}:all-folders");
        \Cache::forget("{$prefix}:all-lessons");
        \Cache::forget("{$prefix}:all-tests");

        // Clear existing permissions cache
        \Cache::forget("{$prefix}:existing");
    }


}
