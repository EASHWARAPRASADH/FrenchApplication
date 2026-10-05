<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Store a newly created test
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'course_id' => 'required|exists:courses,id',
            'folder_id' => 'nullable|exists:course_folders,id'
        ]);

        try {
            $test = Test::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'passing_score' => $request->passing_score ?? 95,
                'course_id' => $request->course_id,
                'folder_id' => $request->folder_id,
                'status' => 'draft',
                'order_index' => $this->getNextOrderIndex($request->course_id, $request->folder_id)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test created successfully',
                'test' => $test
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified test
     */
    public function edit(Test $test)
    {
        // Load test with questions and their options
        $test->load([
            'questions' => function ($query) {
                $query->orderBy('order');
            },
            'questions.options' => function ($query) {
                $query->orderBy('order');
            },
            'questions.dragDropItems' => function ($query) {
                $query->orderBy('order');
            }
        ]);

        return view('admin.tests.edit', compact('test'));
    }

    /**
     * Update test basic information
     */
    public function update(Request $request, Test $test): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1|max:30',
            'randomize_questions' => 'boolean',
            'show_results' => 'boolean',
            'status' => 'required|in:draft,published,archived'
        ]);

        try {
            $test->update([
                'title' => $request->title,
                'description' => $request->description,
                'time_limit' => $request->duration, // Map duration to time_limit
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'randomize_questions' => $request->boolean('randomize_questions'),
                'show_answers' => $request->boolean('show_results'), // Map show_results to show_answers
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test updated successfully',
                'test' => $test
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add question to test
     */
    public function addQuestion(Request $request, Test $test): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:mcq,mcq_image,audio,video,drag_drop,text_input,fill_blanks,image_mcq,image_audio_mcq,passage_mcq,expression_ecrite',
            'question_text' => 'required|string',
            'passage' => 'nullable|string',
            'question_media' => 'nullable|string',
            'correct_answer' => 'nullable|array',
            'explanation' => 'nullable|string',
            'points' => 'nullable|integer|min:1',
            'options' => 'nullable|array',
            'min_words' => 'nullable|integer|min:0',
            'max_words' => 'nullable|integer|gt:min_words'
        ]);

        try {
            $order = $test->questions()->max('order') + 1;

            $question = $test->questions()->create([
                'type' => $request->type,
                'question_text' => $request->question_text,
                'passage' => $request->passage,
                'question_media' => $request->question_media,
                'correct_answer' => $request->correct_answer ?? [],
                'explanation' => $request->explanation,
                'points' => $request->points ?? 1,
                'order' => $order,
                'min_words' => $request->min_words,
                'max_words' => $request->max_words
            ]);

            // Add options for MCQ questions
            if (in_array($request->type, ['mcq', 'mcq_image', 'audio', 'video', 'image_mcq', 'image_audio_mcq', 'passage_mcq']) && $request->options) {
                foreach ($request->options as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'option_image' => $option['image'] ?? null,
                        'is_correct' => $option['is_correct'] ?? false,
                        'order' => $index + 1
                    ]);
                }
            }

            // Update total questions count
            $test->update(['total_questions' => $test->questions()->count()]);

            // Load relationships and render HTML
            $question->load(['options']);

            $questionHtml = view('admin.tests.partials.question-block-inline', [
                'question' => $question
            ])->render();

            return response()->json([
                'success' => true,
                'message' => 'Question added successfully',
                'question' => $question,
                'questionHtml' => $questionHtml
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding question', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error adding question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateQuestion(Request $request, Test $test, TestQuestion $question): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:mcq,mcq_image,audio,video,drag_drop,text_input,fill_blanks,image_audio_mcq,passage_mcq,expression_ecrite',
            'question_text' => 'required|string',
            'passage' => 'nullable|string',
            'question_media' => 'nullable|string',
            'correct_answer' => 'nullable|array',
            'explanation' => 'nullable|string',
            'points' => 'nullable|integer|min:1',
            'options' => 'nullable|array',
            'drag_drop_items' => 'nullable|array',
            'min_words' => 'nullable|integer|min:0',
            'max_words' => 'nullable|integer|gt:min_words'
        ]);

        try {
            $question->update([
                'type' => $request->type,
                'question_text' => $request->question_text,
                'passage' => $request->passage,
                'question_media' => $request->question_media,
                'correct_answer' => $request->correct_answer ?? [],
                'explanation' => $request->explanation,
                'points' => $request->points ?? 1,
                'min_words' => $request->min_words,
                'max_words' => $request->max_words
            ]);

            // Update options for MCQ questions
            if (in_array($request->type, ['mcq', 'mcq_image', 'audio', 'video', 'image_mcq', 'image_audio_mcq', 'passage_mcq']) && $request->options) {
                // Delete existing options
                $question->options()->delete();

                // Add new options
                foreach ($request->options as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'option_image' => $option['image'] ?? null,
                        'is_correct' => $option['is_correct'] ?? false,
                        'order' => $index + 1
                    ]);
                }
            }

            // Update drag drop items
            if ($request->type === 'drag_drop' && $request->drag_drop_items) {
                // Delete existing items
                $question->dragDropItems()->delete();

                // Add new items
                foreach ($request->drag_drop_items as $index => $item) {
                    $question->dragDropItems()->create([
                        'item_text' => $item['item_text'],
                        'match_text' => $item['match_text'],
                        'order' => $index + 1
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'question' => $question->load(['options', 'dragDropItems'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update basic question information (text, explanation, points, and options)
     */
    public function updateQuestionBasic(Request $request, Test $test, \App\Models\TestQuestion $question): JsonResponse
    {
        try {
            $request->validate([
                'question_text' => 'required|string',
                'passage' => 'nullable|string',
                'explanation' => 'nullable|string',
                'points' => 'nullable|integer|min:1',
                'options' => 'nullable|array',
                'options.*.text' => 'nullable|string',
                'options.*.is_correct' => 'nullable|boolean'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        }

        try {
            \Log::info('Updating question', [
                'question_id' => $question->id,
                'request_data' => $request->all()
            ]);

            // Update question basic info
            $question->update([
                'question_text' => $request->question_text,
                'passage' => $request->passage,
                'explanation' => $request->explanation,
                'points' => $request->points ?? 1
            ]);

            // Update options if provided
            if ($request->has('options') && in_array($question->type, ['mcq', 'mcq_image', 'audio', 'video', 'image_mcq', 'image_audio_mcq', 'passage_mcq'])) {
                \Log::info('Updating options', ['options' => $request->options]);

                // Delete existing options
                $question->options()->delete();

                // Add new options
                foreach ($request->options as $index => $option) {
                    if (isset($option['text']) && !empty(trim($option['text']))) {
                        $question->options()->create([
                            'option_text' => trim($option['text']),
                            'option_image' => null, // For now, we're only handling text updates
                            'is_correct' => $option['is_correct'] ?? false,
                            'order' => $index + 1
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuestion(Test $test, TestQuestion $question): JsonResponse
    {
        try {
            $question->delete();

            // Update total questions count
            $test->update(['total_questions' => $test->questions()->count()]);

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorderQuestions(Request $request, Test $test): JsonResponse
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|integer|exists:test_questions,id',
            'questions.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->questions as $questionData) {
                TestQuestion::where('id', $questionData['id'])
                    ->where('test_id', $test->id)
                    ->update(['order' => $questionData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Questions reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering questions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Test $test): JsonResponse
    {
        try {
            $test->delete();

            return response()->json([
                'success' => true,
                'message' => 'Test deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the next order index for a test
     */
    private function getNextOrderIndex(int $courseId, ?int $folderId): int
    {
        $maxOrder = Test::where('course_id', $courseId)
            ->where('folder_id', $folderId)
            ->max('order_index');

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Download CSV template for Passage + Question + MCQ
     */
    public function downloadPassageMcqTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="passage_mcq_template.csv"'
        ];
        $csv = "Passage Text,Question Text,Option A,Option B,Option C,Option D,Correct Answer,Points,Explanation\n" .
            "A short passage here?,What is the correct answer?,Paris,London,Berlin,Madrid,A,1,Capital of France\n" .
            "Another passage...,Choose the right option,Red,Blue,Green,Yellow,B,2,Primary color";
        return response($csv, 200, $headers);
    }

    /**
     * Parse CSV and return preview + sanitized items for Passage MCQ bulk import
     */
    public function bulkParsePassageMcq(Request $request, Test $test): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return response()->json(['success' => false, 'message' => 'Cannot open CSV file'], 400);
        }

        $rowNum = 0;
        $preview = [];
        $items = [];
        $hasErrors = false;
        // Helper to normalize encoding to UTF-8 (handles Windows-1252/ISO-8859-1)
        $normalize = function ($v) {
            $s = (string) ($v ?? '');
            if ($s === '')
                return '';
            if (!mb_detect_encoding($s, 'UTF-8', true)) {
                $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $s);
                if ($converted === false) {
                    $converted = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $s);
                }
                if ($converted !== false) {
                    $s = $converted;
                } else {
                    $s = utf8_encode($s);
                }
            }
            return trim($s);
        };

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($rowNum === 1) {
                // Skip header if it looks like one
                $first = trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) ($row[0] ?? '')));
                if (stripos($first, 'passage') === 0) {
                    continue;
                }
            }
            // Skip empty rows
            if (!array_filter($row, fn($v) => trim((string) $v) !== '')) {
                continue;
            }

            // Ensure 9 columns
            $row = array_pad($row, 9, '');
            [$passage, $question, $a, $b, $c, $d, $correctLetter, $points, $explanation] = array_map($normalize, $row);

            $errors = [];
            if ($passage === '')
                $errors[] = 'Passage required';
            if ($question === '')
                $errors[] = 'Question required';
            $opts = [$a, $b, $c, $d];
            foreach (['A', 'B', 'C', 'D'] as $i => $L) {
                if ($opts[$i] === '') {
                    $errors[] = "Option $L required";
                }
            }
            $cl = strtoupper($correctLetter);
            if (!in_array($cl, ['A', 'B', 'C', 'D'])) {
                $errors[] = 'Correct Answer must be A/B/C/D';
            }
            $pts = (int) preg_replace('/[^0-9]/', '', (string) $points);
            if ($pts < 1) {
                $errors[] = 'Points must be a positive integer';
            }

            $preview[] = [
                'row' => $rowNum,
                'passage' => $passage,
                'question_text' => $question,
                'options' => array_map($normalize, $opts),
                'correct_letter' => $cl ?: '',
                'points' => $pts ?: 0,
                'explanation' => $explanation,
                'errors' => $errors,
            ];

            if (!$errors) {
                $items[] = [
                    'passage' => $passage,
                    'question_text' => $question,
                    'options' => array_values($opts),
                    'correct_index' => ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3][$cl],
                    'points' => $pts,
                    'explanation' => $explanation,
                ];
            } else {
                $hasErrors = true;
            }
        }
        fclose($handle);

        return response()->json([
            'success' => true,
            'hasErrors' => $hasErrors,
            'totalRows' => count($preview),
            'validCount' => count($items),
            'preview' => $preview,
            'items' => $items,
        ]);
    }

    /**
     * Create questions from sanitized items for Passage MCQ bulk import
     */
    public function bulkImportPassageMcq(Request $request, Test $test): JsonResponse
    {
        $data = $request->validate([
            'items' => 'required|array|min:1'
        ]);
        $items = $data['items'];

        // Normalize encoding helper
        $normalize = function ($v) {
            $s = (string) ($v ?? '');
            if ($s === '')
                return '';
            if (!mb_detect_encoding($s, 'UTF-8', true)) {
                $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $s);
                if ($converted === false) {
                    $converted = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $s);
                }
                if ($converted !== false) {
                    $s = $converted;
                } else {
                    $s = utf8_encode($s);
                }
            }
            return trim($s);
        };

        $created = 0;
        $htmlSnippets = [];
        \DB::beginTransaction();
        try {
            $order = (int) ($test->questions()->max('order') ?? 0);
            foreach ($items as $it) {
                // Basic guard
                if (!isset($it['passage'], $it['question_text'], $it['options'], $it['correct_index'], $it['points'])) {
                    continue;
                }
                $options = $it['options'];
                if (!is_array($options) || count($options) < 4) {
                    continue;
                }
                $correctIdx = (int) $it['correct_index'];
                if ($correctIdx < 0 || $correctIdx > 3) {
                    continue;
                }
                $order++;

                $question = $test->questions()->create([
                    'type' => 'passage_mcq',
                    'question_text' => $normalize($it['question_text']),
                    'passage' => $normalize($it['passage']),
                    'question_media' => null,
                    'correct_answer' => [$correctIdx],
                    'explanation' => $normalize($it['explanation'] ?? ''),
                    'points' => (int) $it['points'],
                    'order' => $order
                ]);

                foreach ($options as $idx => $text) {
                    $question->options()->create([
                        'option_text' => $normalize($text),
                        'option_image' => null,
                        'is_correct' => $idx === $correctIdx,
                        'order' => $idx + 1
                    ]);
                }

                $question->load(['options']);
                $htmlSnippets[] = view('admin.tests.partials.question-block-inline', ['question' => $question])->render();
                $created++;
            }

            // Update total count
            $test->update(['total_questions' => $test->questions()->count()]);
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'count' => $created,
            'questionHtml' => $htmlSnippets
        ]);
    }

    /**
     * Get move/duplicate options for a test (all courses and their folders)
     */
    public function moveOptions(Test $test): JsonResponse
    {
        $courses = \App\Models\Course::orderBy('title')->get(['id', 'title']);
        $folders = \App\Models\CourseFolder::get(['id', 'name', 'course_id', 'parent_folder_id']);

        // Build folder parent-name map for path calculation
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
                'path' => 'Root'
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

                $folderOptions[] = [
                    'id' => $f->id,
                    'name' => $f->name,
                    'path' => $this->buildPath($f->id, $parentMap, $nameMap),
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
                'folders' => $folderOptions
            ];
        }

        return response()->json([
            'success' => true,
            'test_title' => $test->title,
            'current_course_id' => $test->course_id,
            'current_folder_id' => $test->folder_id,
            'courses' => $courseOptions
        ]);
    }

    /**
     * Helper: build path like "Parent / Child"
     */
    private function buildPath(int $id, array $parentMap, array $nameMap): string
    {
        $parts = [];
        $current = $id;
        while ($current) {
            $parts[] = $nameMap[$current] ?? (string)$current;
            $current = $parentMap[$current] ?? null;
        }
        return implode(' / ', array_reverse($parts));
    }

    /**
     * Move a test to another course and/or folder
     */
    public function move(Request $request, Test $test): JsonResponse
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

        try {
            $test->update([
                'course_id' => $courseId,
                'folder_id' => $folderId,
                'order_index' => $this->getNextOrderIndex($courseId, $folderId)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test moved successfully',
                'test' => $test
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate a test with all questions and options to another course and/or folder
     */
    public function duplicate(Request $request, Test $test): JsonResponse
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
            // Replicate test
            $newTest = $test->replicate();
            $newTest->course_id = $courseId;
            $newTest->folder_id = $folderId;
            $newTest->title = $request->new_title ?: ($test->title . ' (Copy)');
            $newTest->order_index = $this->getNextOrderIndex($courseId, $folderId);
            $newTest->status = 'draft'; // Always copy as draft
            $newTest->save();

            // Replicate questions
            $questions = $test->questions()->orderBy('order')->get();
            foreach ($questions as $q) {
                $newQ = $q->replicate();
                $newQ->test_id = $newTest->id;
                $newQ->save();

                // Replicate options
                foreach ($q->options as $opt) {
                    $newOpt = $opt->replicate();
                    $newOpt->question_id = $newQ->id;
                    $newOpt->save();
                }

                // Replicate drag & drop items
                foreach ($q->dragDropItems as $dd) {
                    $newDd = $dd->replicate();
                    $newDd->question_id = $newQ->id;
                    $newDd->save();
                }
            }

            // Sync total questions
            $newTest->update(['total_questions' => $newTest->questions()->count()]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Test duplicated successfully',
                'test' => $newTest
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error duplicating test: ' . $e->getMessage()
            ], 500);
        }
    }

}
