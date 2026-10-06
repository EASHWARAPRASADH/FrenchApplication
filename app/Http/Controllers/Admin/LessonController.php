<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    /**
     * Store a newly created lesson
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'course_id' => 'required|exists:courses,id',
            'folder_id' => 'nullable|exists:course_folders,id'
        ]);

        try {
            $lesson = Lesson::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'course_id' => $request->course_id,
                'folder_id' => $request->folder_id,
                'lesson_type' => 'content',
                'status' => 'draft',
                'order_index' => $this->getNextOrderIndex($request->course_id, $request->folder_id)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson created successfully',
                'lesson' => $lesson
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating lesson: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified lesson
     */
    public function edit(Lesson $lesson)
    {
        // Load lesson with content blocks
        $lesson->load(['contentBlocks' => function($query) {
            $query->orderBy('order');
        }]);

        return view('admin.lessons.edit', compact('lesson'));
    }

    /**
     * Update lesson basic information
     */
    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published,archived'
        ]);

        try {
            $lesson->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson updated successfully',
                'lesson' => $lesson
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lesson: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add content block to lesson
     */
    public function addContentBlock(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:title,text,image,audio,video,exercise,document',
            'content' => 'required|array',
            'order' => 'nullable|integer'
        ]);

        try {
            $order = $request->order ?? ($lesson->contentBlocks()->max('order') + 1);

            $contentBlock = $lesson->contentBlocks()->create([
                'type' => $request->type,
                'content' => $request->content,
                'order' => $order
            ]);

            // Render the content block HTML
            $blockHtml = view('admin.lessons.partials.content-block-inline', [
                'block' => $contentBlock
            ])->render();

            return response()->json([
                'success' => true,
                'message' => 'Content block added successfully',
                'contentBlock' => $contentBlock,
                'blockHtml' => $blockHtml
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding content block: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update content block
     */
    public function updateContentBlock(Request $request, Lesson $lesson, $blockId): JsonResponse
    {
        $request->validate([
            'content' => 'required|array'
        ]);

        try {
            $contentBlock = $lesson->contentBlocks()->findOrFail($blockId);
            $contentBlock->update([
                'content' => $request->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content block updated successfully',
                'contentBlock' => $contentBlock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating content block: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete content block
     */
    public function deleteContentBlock(Lesson $lesson, $blockId): JsonResponse
    {
        try {
            $contentBlock = $lesson->contentBlocks()->findOrFail($blockId);
            $contentBlock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Content block deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting content block: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder content blocks
     */
    public function reorderContentBlocks(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'blocks' => 'required|array',
            'blocks.*.id' => 'required|integer',
            'blocks.*.order' => 'required|integer'
        ]);

        try {
            foreach ($request->blocks as $blockData) {
                $lesson->contentBlocks()
                    ->where('id', $blockData['id'])
                    ->update(['order' => $blockData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Content blocks reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering content blocks: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        try {
            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lesson deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting lesson: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the next order index for a lesson
     */
    private function getNextOrderIndex(int $courseId, ?int $folderId): int
    {
        $maxOrder = Lesson::where('course_id', $courseId)
            ->where('folder_id', $folderId)
            ->max('order_index');

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Move a lesson to another course and/or folder
     */
    public function move(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'destination_course_id' => 'required|exists:courses,id',
            'destination_folder_id' => 'nullable|exists:course_folders,id',
        ]);

        $courseId = (int)$request->destination_course_id;
        $folderId = $request->destination_folder_id ? (int)$request->destination_folder_id : null;

        // Verify folder belongs to course if provided
        if ($folderId) {
            $folder = \App\Models\CourseFolder::findOrFail($folderId);
            if ($folder->course_id !== $courseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected folder does not belong to the target course.'
                ], 400);
            }
        }

        \DB::beginTransaction();
        try {
            $oldCourseId = $lesson->course_id;

            $lesson->update([
                'course_id' => $courseId,
                'folder_id' => $folderId,
                'order_index' => $this->getNextOrderIndex($courseId, $folderId)
            ]);

            if ($oldCourseId !== $courseId) {
                // Synchronize permissions for this lesson into destination course
                $perms = \App\Models\StudentContentPermission::where('content_type', 'lesson')
                    ->where('content_id', $lesson->id)
                    ->get();

                foreach ($perms as $p) {
                    if ($p->course_id === $courseId) {
                        continue;
                    }

                    $existing = \App\Models\StudentContentPermission::where('student_id', $p->student_id)
                        ->where('course_id', $courseId)
                        ->where('content_type', 'lesson')
                        ->where('content_id', $lesson->id)
                        ->first();

                    if ($existing) {
                        if (!$existing->has_access && $p->has_access) {
                            $existing->has_access = true;
                            $existing->save();
                        }
                        $p->delete();
                    } else {
                        $p->course_id = $courseId;
                        $p->save();
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lesson moved successfully',
                'lesson' => $lesson
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error moving lesson: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate a lesson with all content blocks to another course and/or folder
     */
    public function duplicate(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'destination_course_id' => 'required|exists:courses,id',
            'destination_folder_id' => 'nullable|exists:course_folders,id',
            'new_title' => 'nullable|string|max:255'
        ]);

        $courseId = (int)$request->destination_course_id;
        $folderId = $request->destination_folder_id ? (int)$request->destination_folder_id : null;

        // Verify folder belongs to course if provided
        if ($folderId) {
            $folder = \App\Models\CourseFolder::findOrFail($folderId);
            if ($folder->course_id !== $courseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected folder does not belong to the target course.'
                ], 400);
            }
        }

        \DB::beginTransaction();
        try {
            // Replicate lesson
            $newLesson = $lesson->replicate();
            $newLesson->course_id = $courseId;
            $newLesson->folder_id = $folderId;
            $newLesson->title = $request->new_title ?: ($lesson->title . ' (Copy)');
            $newLesson->order_index = $this->getNextOrderIndex($courseId, $folderId);
            $newLesson->status = 'draft'; // Always copy as draft
            $newLesson->save();

            // Replicate content blocks
            foreach ($lesson->contentBlocks as $cb) {
                $newCb = $cb->replicate();
                $newCb->lesson_id = $newLesson->id;
                $newCb->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lesson duplicated successfully',
                'lesson' => $newLesson
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error duplicating lesson: ' . $e->getMessage()
            ], 500);
        }
    }
}
