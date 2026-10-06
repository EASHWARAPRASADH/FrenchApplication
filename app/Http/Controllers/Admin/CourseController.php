<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseFolder;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\CourseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function moveToCollection(Request $request, Course $course)
    {
        $validated = $request->validate([
            'destination_collection_id' => 'nullable|exists:course_collections,id',
        ]);

        $course->collection_id = $validated['destination_collection_id'] ?? null;
        $course->save();

        return response()->json([
            'success' => true,
            'message' => 'Course moved successfully',
            'course_id' => $course->id,
            'collection_id' => $course->collection_id,
        ]);
    }
    public function index(Request $request)
    {
        $query = Course::with(['teacher', 'enrollments'])
            ->withCount(['lessons', 'tests', 'enrollments']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        $courses = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'language' => 'required|in:french,spanish,german,italian',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'teacher_id' => 'required|exists:users,id',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validated['featured'] = $request->has('featured');
        $validated['price'] = $validated['price'] ?? 0;
        $validated['duration_hours'] = $validated['duration_hours'] ?? 1;

        $course = Course::create($validated);

        return redirect()
            ->route('admin.courses.builder', $course)
            ->with('success', 'Course created successfully! Now you can add content.');
    }

    public function show(Course $course)
    {
        // Redirect to the course builder for preview
        return redirect()->route('admin.courses.builder', $course);
    }

    public function edit(Course $course)
    {
        $teachers = User::where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get();

        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'language' => 'required|in:french,spanish,german,italian',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'teacher_id' => 'required|exists:users,id',
            'price' => 'nullable|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validated['featured'] = $request->has('featured');
        $validated['price'] = $validated['price'] ?? 0;
        $validated['duration_hours'] = $validated['duration_hours'] ?? 1;

        $course->update($validated);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Delete related data
        $course->lessons()->delete();
        $course->folders()->delete();
        $course->enrollments()->delete();
        $course->tests()->delete();

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Course Builder - The main interface for building course content
     */
    public function builder(Course $course)
    {
        // Load course with all related data for the builder
        $course->load([
            'folders' => function ($query) {
                $query->whereNull('parent_folder_id')->orderBy('order_index');
            },
            'folders.subfolders' => function ($query) {
                $query->orderBy('order_index');
            },
            'folders.lessons' => function ($query) {
                $query->orderBy('order_index');
            },
            'folders.tests' => function ($query) {
                $query->orderBy('order_index');
            },
            'lessons' => function ($query) {
                $query->whereNull('folder_id')->orderBy('order_index');
            },
            'tests' => function ($query) {
                $query->whereNull('folder_id')->orderBy('order_index');
            },
            'files' => function ($query) {
                $query->whereNull('folder_id')->orderBy('order_index');
            }
        ]);

        // Get course statistics
        $stats = [
            'total_folders' => $course->folders()->count(),
            'total_lessons' => $course->lessons()->count(),
            'total_tests' => $course->tests()->count(),
            'total_enrollments' => $course->enrollments()->count(),
            'completion_rate' => $this->calculateCompletionRate($course),
        ];

        return view('admin.courses.builder', compact('course', 'stats'));
    }

    private function calculateCompletionRate(Course $course)
    {
        $totalEnrollments = $course->enrollments()->count();

        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = $course->enrollments()
            ->where('status', 'completed')
            ->count();

        return round(($completedEnrollments / $totalEnrollments) * 100, 1);
    }

    /**
     * Get folder contents for AJAX navigation
     */
    public function getFolderContents(Course $course, CourseFolder $folder)
    {
        try {
            // Load folder with its contents
            $folder->load([
                'subfolders' => function ($query) {
                    $query->orderBy('order_index');
                },
                'lessons' => function ($query) {
                    $query->orderBy('order_index');
                },
                'tests' => function ($query) {
                    $query->orderBy('order_index');
                },
                'files' => function ($query) {
                    $query->orderBy('order_index');
                }
            ]);

            // Format the data for frontend
            $contents = [
                'folders' => $folder->subfolders->map(function ($subfolder) {
                    return [
                        'id' => $subfolder->id,
                        'name' => $subfolder->name,
                        'description' => $subfolder->description,
                        'items_count' => $subfolder->subfolders->count() + $subfolder->lessons->count() + $subfolder->tests->count(),
                        'updated_at' => $subfolder->updated_at->diffForHumans(),
                    ];
                }),
                'lessons' => $folder->lessons->map(function ($lesson) {
                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'description' => $lesson->description,
                        'duration' => $lesson->duration,
                        'status' => $lesson->status,
                        'updated_at' => $lesson->updated_at->diffForHumans(),
                    ];
                }),
                'tests' => $folder->tests->map(function ($test) {
                    return [
                        'id' => $test->id,
                        'title' => $test->title,
                        'description' => $test->description,
                        'duration' => $test->duration,
                        'passing_score' => $test->passing_score,
                        'status' => $test->status,
                        'updated_at' => $test->updated_at->diffForHumans(),
                    ];
                }),
                'files' => $folder->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'original_name' => $file->original_name,
                        'filename' => $file->filename,
                        'size' => $file->size,
                        'mime_type' => $file->mime_type,
                        'icon' => $file->icon,
                        'type' => $file->type,
                        'download_url' => $file->download_url,
                        'downloadable' => $file->downloadable,
                        'viewable' => $file->viewable,
                        'updated_at' => $file->updated_at->diffForHumans(),
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'contents' => $contents,
                'folder' => [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'description' => $folder->description,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading folder contents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Destination course options for merging (exclude the source course)
     */
    public function mergeOptions(Course $course)
    {
        $options = Course::where('id', '!=', $course->id)
            ->orderBy('title')
            ->get(['id', 'title', 'level', 'status']);

        return response()->json([
            'success' => true,
            'options' => $options->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'level' => $c->level,
                'status' => $c->status,
            ]),
        ]);
    }

    /**
     * Preview stats of what will be merged from the source course
     */
    public function mergeStats(Course $course)
    {
        $stats = [
            'folders' => CourseFolder::where('course_id', $course->id)->count(),
            'lessons' => Lesson::where('course_id', $course->id)->count(),
            'tests' => Test::where('course_id', $course->id)->count(),
            'files' => CourseFile::where('course_id', $course->id)->count(),
        ];

        return response()->json([
            'success' => true,
            'course' => ['id' => $course->id, 'title' => $course->title],
            'stats' => $stats,
        ]);
    }

    private function getNextOrderIndexFor(?int $parentFolderId, int $courseId): int
    {
        $maxOrder = CourseFolder::where('course_id', $courseId)
            ->where('parent_folder_id', $parentFolderId)
            ->max('order_index');
        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Merge entire source course into a destination course as a new top-level folder
     */
    public function merge(Request $request, Course $source)
    {
        $validated = $request->validate([
            'destination_course_id' => 'required|exists:courses,id',
        ]);

        $destId = (int) $validated['destination_course_id'];
        if ($destId === (int) $source->id) {
            return response()->json([
                'success' => false,
                'message' => 'Destination course cannot be the same as source course.'
            ], 422);
        }

        $dest = Course::findOrFail($destId);

        // Capture stats before moving
        $before = [
            'folders' => CourseFolder::where('course_id', $source->id)->count(),
            'lessons' => Lesson::where('course_id', $source->id)->count(),
            'tests' => Test::where('course_id', $source->id)->count(),
            'files' => CourseFile::where('course_id', $source->id)->count(),
        ];

        try {
            DB::beginTransaction();

            // Create a container folder in destination named after the source course
            $containerName = $source->title;
            $exists = CourseFolder::where('course_id', $dest->id)
                ->whereNull('parent_folder_id')
                ->where('name', $containerName)
                ->exists();
            if ($exists) {
                $containerName = $source->title . ' (from ' . $source->id . ')';
            }
            $container = CourseFolder::create([
                'course_id' => $dest->id,
                'parent_folder_id' => null,
                'name' => $containerName,
                'description' => null,
                'order_index' => $this->getNextOrderIndexFor(null, $dest->id),
                'status' => 'active',
            ]);

            // Move folders: first capture root-level folder ids before changing course_id
            $rootFolderIds = CourseFolder::where('course_id', $source->id)
                ->whereNull('parent_folder_id')
                ->pluck('id')
                ->all();
            // Reassign all folders to destination course
            CourseFolder::where('course_id', $source->id)
                ->update(['course_id' => $dest->id]);
            // Re-parent previous root folders under the new container
            if (!empty($rootFolderIds)) {
                CourseFolder::whereIn('id', $rootFolderIds)
                    ->update(['parent_folder_id' => $container->id]);
            }

            // Move lessons: capture root lessons first
            $rootLessonIds = Lesson::where('course_id', $source->id)
                ->whereNull('folder_id')
                ->pluck('id')->all();
            Lesson::where('course_id', $source->id)
                ->update(['course_id' => $dest->id]);
            if (!empty($rootLessonIds)) {
                Lesson::whereIn('id', $rootLessonIds)
                    ->update(['folder_id' => $container->id]);
            }

            // Move tests: capture root tests first
            $rootTestIds = Test::where('course_id', $source->id)
                ->whereNull('folder_id')
                ->pluck('id')->all();
            Test::where('course_id', $source->id)
                ->update(['course_id' => $dest->id]);
            if (!empty($rootTestIds)) {
                Test::whereIn('id', $rootTestIds)
                    ->update(['folder_id' => $container->id]);
            }

            // Move files: capture root files first
            $rootFileIds = CourseFile::where('course_id', $source->id)
                ->whereNull('folder_id')
                ->pluck('id')->all();
            CourseFile::where('course_id', $source->id)
                ->update(['course_id' => $dest->id]);
            if (!empty($rootFileIds)) {
                CourseFile::whereIn('id', $rootFileIds)
                    ->update(['folder_id' => $container->id]);
            }

            // Recalculate counts
            $dest->total_lessons = Lesson::where('course_id', $dest->id)->count();
            $dest->total_tests = Test::where('course_id', $dest->id)->count();
            $dest->save();

            $source->total_lessons = Lesson::where('course_id', $source->id)->count();
            $source->total_tests = Test::where('course_id', $source->id)->count();
            $source->save();

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => Auth::id(),
                'action' => 'merge_course',
                'description' => 'Merged course ' . $source->id . ' into course ' . $dest->id . ' as folder "' . $containerName . '"',
                'ip_address' => $request->ip(),
                'metadata' => json_encode([
                    'source_course_id' => $source->id,
                    'destination_course_id' => $dest->id,
                    'moved' => $before,
                    'container_folder_id' => $container->id,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merge completed successfully',
                'moved' => $before,
                'container_folder_id' => $container->id,
                'destination_course_id' => $dest->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Merge failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all courses and their folder trees for move/duplicate modals
     */
    public function allMoveOptions(Course $course)
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $folders = CourseFolder::get(['id', 'name', 'course_id', 'parent_folder_id']);

        $parentMap = [];
        $nameMap = [];
        foreach ($folders as $f) {
            $parentMap[$f->id] = $f->parent_folder_id;
            $nameMap[$f->id] = $f->name;
        }

        $courseOptions = [];
        foreach ($courses as $c) {
            $cFolders = $folders->where('course_id', $c->id);
            $folderOptions = [];

            // Add Root option for course
            $folderOptions[] = [
                'id' => null,
                'name' => 'Course Root',
                'path' => 'Root',
                'display_name' => 'Course Root (Top Level)',
            ];

            foreach ($cFolders as $f) {
                // Calculate depth for indentation
                $depth = 0;
                $curr = $f->parent_folder_id;
                while ($curr) {
                    $depth++;
                    $curr = $parentMap[$curr] ?? null;
                }

                $indent = str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $depth);
                $prefix = $depth > 0 ? "└─ 📁 " : "📁 ";

                $parts = [];
                $current = $f->id;
                while ($current) {
                    $parts[] = $nameMap[$current] ?? (string)$current;
                    $current = $parentMap[$current] ?? null;
                }
                $path = implode(' / ', array_reverse($parts));

                $folderOptions[] = [
                    'id' => $f->id,
                    'name' => $f->name,
                    'path' => $path,
                    'display_name' => $indent . $prefix . $f->name,
                ];
            }

            // Sort folders by path (except Root which stays first)
            $rootOpt = array_shift($folderOptions);
            usort($folderOptions, function ($a, $b) {
                return strcmp($a['path'], $b['path']);
            });
            array_unshift($folderOptions, $rootOpt);

            $courseOptions[] = [
                'id' => $c->id,
                'title' => $c->title,
                'folders' => $folderOptions,
            ];
        }

        return response()->json([
            'success' => true,
            'current_course_id' => $course->id,
            'courses' => $courseOptions,
        ]);
    }
}
