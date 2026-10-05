<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseFolder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    /**
     * Store a newly created folder
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'parent_folder_id' => 'nullable|exists:course_folders,id'
        ]);

        try {
            $folder = CourseFolder::create([
                'name' => $request->name,
                'description' => $request->description,
                'course_id' => $request->course_id,
                'parent_folder_id' => $request->parent_folder_id,
                'order_index' => $this->getNextOrderIndex($request->course_id, $request->parent_folder_id)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully',
                'folder' => $folder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified folder
     */
    public function destroy(CourseFolder $folder): JsonResponse
    {
        try {
            // Check if folder has content
            $hasContent = $folder->subfolders()->count() > 0 ||
                         $folder->lessons()->count() > 0 ||
                         $folder->tests()->count() > 0;

            if ($hasContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete folder that contains content. Please move or delete the content first.'
                ], 400);
            }

            $folder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Folder deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single folder (for editing)
     */
    public function show(CourseFolder $folder): JsonResponse
    {
        return response()->json([
            'success' => true,
            'folder' => $folder
        ]);
    }

    /**
     * Update an existing folder (rename/edit)
     */
    public function update(Request $request, CourseFolder $folder): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $folder->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Folder updated successfully',
                'folder' => $folder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating folder: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get list of valid destination folders for moving a folder within a course
     * Excludes the folder itself and all of its descendants
     */
    public function moveOptions(\App\Models\Course $course, CourseFolder $folder): JsonResponse
    {
        // Get all folders for the course
        $all = CourseFolder::where('course_id', $course->id)
            ->get(['id','name','parent_folder_id']);

        // Build maps for quick lookup
        $parentMap = [];
        $nameMap = [];
        $childrenMap = [];
        foreach ($all as $f) {
            $parentMap[$f->id] = $f->parent_folder_id;
            $nameMap[$f->id] = $f->name;
            $childrenMap[$f->parent_folder_id ?? 0][] = $f->id; // use 0 for null root
        }

        // Collect descendants of the current folder to exclude
        $excludeIds = $this->getDescendantIdsFor($folder->id, $childrenMap);
        $excludeIds[$folder->id] = true; // also exclude itself

        // Build options excluding invalid destinations
        $options = [];
        foreach ($all as $f) {
            if (isset($excludeIds[$f->id])) continue;

            // Calculate depth for indentation
            $depth = 0;
            $curr = $f->parent_folder_id;
            while ($curr) {
                $depth++;
                $curr = $parentMap[$curr] ?? null;
            }

            // Generate clean indented name for select option display using non-breaking spaces
            $indent = str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $depth);
            $prefix = $depth > 0 ? "└─ 📁 " : "📁 ";

            $options[] = [
                'id' => $f->id,
                'name' => $f->name,
                'path' => $this->buildPath($f->id, $parentMap, $nameMap),
                'display_name' => $indent . $prefix . $f->name,
            ];
        }

        // Sort options by path for nicer UX
        usort($options, function ($a, $b) {
            return strcmp($a['path'], $b['path']);
        });

        return response()->json([
            'success' => true,
            'folder_name' => $folder->name,
            'current_parent_id' => $folder->parent_folder_id,
            'root' => [ 'id' => null, 'name' => 'Root', 'path' => 'Root' ],
            'options' => $options,
        ]);
    }

    /**
     * Move a folder under another folder (or to root) in any course
     */
    public function move(Request $request, CourseFolder $folder): JsonResponse
    {
        $request->validate([
            'destination_course_id' => 'required|exists:courses,id',
            'destination_folder_id' => 'nullable|exists:course_folders,id',
        ]);

        $courseId = (int)$request->destination_course_id;
        $destId = $request->destination_folder_id ? (int)$request->destination_folder_id : null;

        // No-op if destination is the same as current location
        if (($folder->parent_folder_id ?? null) === $destId && $folder->course_id === $courseId) {
            return response()->json([
                'success' => true,
                'message' => 'Folder already in selected location',
            ]);
        }

        // Cannot move into itself
        if ($destId && $destId === (int)$folder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot move a folder into itself.',
            ], 400);
        }

        // Validate destination folder if provided
        if ($destId) {
            $dest = CourseFolder::findOrFail($destId);
            if ($dest->course_id !== $courseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination folder is in a different course than selected.',
                ], 400);
            }

            // If moving within the same course, check descendants
            if ($folder->course_id === $courseId) {
                // Build children map for descendant check
                $all = CourseFolder::where('course_id', $folder->course_id)
                    ->get(['id','parent_folder_id']);
                $childrenMap = [];
                foreach ($all as $f) {
                    $childrenMap[$f->parent_folder_id ?? 0][] = $f->id;
                }
                $desc = $this->getDescendantIdsFor($folder->id, $childrenMap);
                if (isset($desc[$destId])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot move a folder into one of its own subfolders.',
                    ], 400);
                }
            }
        }

        \DB::beginTransaction();
        try {
            // Move and set order to the end among new siblings
            $folder->parent_folder_id = $destId;
            $folder->order_index = $this->getNextOrderIndex($courseId, $destId);

            if ($folder->course_id !== $courseId) {
                // Course changed, update this folder and all sub-elements recursively
                $this->moveFolderToCourse($folder, $courseId);
            } else {
                $folder->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Folder moved successfully',
                'folder' => $folder,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error moving folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recursively update course_id of a folder, its lessons, tests, and subfolders
     */
    private function moveFolderToCourse(CourseFolder $folder, int $newCourseId)
    {
        $folder->course_id = $newCourseId;
        $folder->save();

        // Update all lessons in this folder
        \App\Models\Lesson::where('folder_id', $folder->id)->update(['course_id' => $newCourseId]);

        // Update all tests in this folder
        \App\Models\Test::where('folder_id', $folder->id)->update(['course_id' => $newCourseId]);

        // Recursively update subfolders
        foreach ($folder->subfolders as $sub) {
            $this->moveFolderToCourse($sub, $newCourseId);
        }
    }

    /**
     * Duplicate a folder with all lessons, tests, and subfolders recursively
     */
    public function duplicate(Request $request, CourseFolder $folder): JsonResponse
    {
        $request->validate([
            'destination_course_id' => 'required|exists:courses,id',
            'destination_folder_id' => 'nullable|exists:course_folders,id',
            'new_name' => 'nullable|string|max:255'
        ]);

        $courseId = (int)$request->destination_course_id;
        $folderId = $request->destination_folder_id ? (int)$request->destination_folder_id : null;

        // Verify folder belongs to course if provided
        if ($folderId) {
            $destFolder = CourseFolder::findOrFail($folderId);
            if ($destFolder->course_id !== $courseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected folder does not belong to the target course.'
                ], 400);
            }
        }

        \DB::beginTransaction();
        try {
            $newFolder = $this->duplicateFolderRecursive($folder, $courseId, $folderId, $request->new_name);
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Folder duplicated successfully',
                'folder' => $newFolder
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error duplicating folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: recursively duplicate a folder and its contents
     */
    private function duplicateFolderRecursive(CourseFolder $folder, int $courseId, ?int $parentFolderId, ?string $newName = null): CourseFolder
    {
        $newFolder = $folder->replicate();
        $newFolder->course_id = $courseId;
        $newFolder->parent_folder_id = $parentFolderId;
        $newFolder->name = $newName ?: ($folder->name . ' (Copy)');
        $newFolder->order_index = $this->getNextOrderIndex($courseId, $parentFolderId);
        $newFolder->save();

        // Replicate all lessons
        foreach ($folder->lessons as $lesson) {
            $newLesson = $lesson->replicate();
            $newLesson->course_id = $courseId;
            $newLesson->folder_id = $newFolder->id;
            $newLesson->status = 'draft';
            $newLesson->order_index = \App\Models\Lesson::where('course_id', $courseId)->where('folder_id', $newFolder->id)->max('order_index') + 1;
            $newLesson->save();

            foreach ($lesson->contentBlocks as $cb) {
                $newCb = $cb->replicate();
                $newCb->lesson_id = $newLesson->id;
                $newCb->save();
            }
        }

        // Replicate all tests
        foreach ($folder->tests as $test) {
            $newTest = $test->replicate();
            $newTest->course_id = $courseId;
            $newTest->folder_id = $newFolder->id;
            $newTest->status = 'draft';
            $newTest->order_index = \App\Models\Test::where('course_id', $courseId)->where('folder_id', $newFolder->id)->max('order_index') + 1;
            $newTest->save();

            foreach ($test->questions as $q) {
                $newQ = $q->replicate();
                $newQ->test_id = $newTest->id;
                $newQ->save();

                foreach ($q->options as $opt) {
                    $newOpt = $opt->replicate();
                    $newOpt->question_id = $newQ->id;
                    $newOpt->save();
                }

                foreach ($q->dragDropItems as $dd) {
                    $newDd = $dd->replicate();
                    $newDd->question_id = $newQ->id;
                    $newDd->save();
                }
            }
        }

        // Replicate all subfolders recursively
        foreach ($folder->subfolders as $sub) {
            $this->duplicateFolderRecursive($sub, $courseId, $newFolder->id);
        }

        return $newFolder;
    }

    /**
     * Helper: collect descendant ids (as a set) using a children adjacency map
     * $childrenMap is keyed by parent_id (use 0 for null)
     */
    private function getDescendantIdsFor(int $rootId, array $childrenMap): array
    {
        $result = [];
        $stack = [$rootId];
        while (!empty($stack)) {
            $current = array_pop($stack);
            $kids = $childrenMap[$current] ?? [];
            foreach ($kids as $kid) {
                if (!isset($result[$kid])) {
                    $result[$kid] = true;
                    $stack[] = $kid;
                }
            }
        }
        return $result;
    }

    /**
     * Helper: build path like "Parent / Child / Subchild" for display
     */
    private function buildPath(int $id, array $parentMap, array $nameMap): string
    {
        $parts = [];
        $current = $id;
        while ($current) {
            $parts[] = $nameMap[$current] ?? (string)$current;
            $current = $parentMap[$current] ?? null;
        }
        $parts = array_reverse($parts);
        return 'Root / ' . implode(' / ', $parts);
    }

    /**
     * Get the next order index for a folder
     */
    private function getNextOrderIndex(int $courseId, ?int $parentFolderId): int
    {
        $maxOrder = CourseFolder::where('course_id', $courseId)
            ->where('parent_folder_id', $parentFolderId)
            ->max('order_index');

        return ($maxOrder ?? 0) + 1;
    }
}
