<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Upload files to the course
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:102400', // 100MB max
            'course_id' => 'required|exists:courses,id',
            'folder_id' => 'nullable|exists:course_folders,id'
        ]);

        try {
            $uploadedFiles = [];
            $courseId = $request->course_id;
            $folderId = $request->folder_id;

            foreach ($request->file('files') as $file) {
                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
                
                // Store file in course-specific directory
                $path = $file->storeAs(
                    "courses/{$courseId}/files",
                    $filename,
                    'public'
                );

                // Get file info
                $fileInfo = [
                    'original_name' => $originalName,
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'course_id' => $courseId,
                    'folder_id' => $folderId,
                    'uploaded_at' => now()
                ];

                $uploadedFiles[] = $fileInfo;

                // Here you could save file info to database if you have a files table
                // File::create($fileInfo);
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
                'files' => $uploadedFiles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload content files for lessons (images, audio, video)
     */
    public function uploadContent(Request $request): JsonResponse
    {
        // Debug logging
        \Log::info('Upload request received', [
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated',
            'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'no file',
            'type' => $request->input('type', 'not provided'),
            'all_files' => $request->allFiles(),
            'content_length' => $request->header('Content-Length'),
            'content_type' => $request->header('Content-Type')
        ]);

        // Check if file upload failed due to PHP limits
        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            $error = 'File upload failed';
            $uploadError = $request->file('file') ? $request->file('file')->getError() : 'No file received';

            // Common PHP upload error codes
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];

            if (isset($errorMessages[$uploadError])) {
                $error = $errorMessages[$uploadError];
            }

            \Log::error('PHP file upload error', [
                'error_code' => $uploadError,
                'error_message' => $error,
                'php_limits' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'max_file_uploads' => ini_get('max_file_uploads')
                ]
            ]);

            return response()->json([
                'success' => false,
                'message' => $error . '. Current PHP upload limit: ' . ini_get('upload_max_filesize') . '. If this persists, contact your hosting provider to increase PHP upload limits.'
            ], 422);
        }

        try {
            $request->validate([
                'file' => 'required|file', // No size limit
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('File validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()['file'] ?? ['Unknown validation error'])
            ], 422);
        }

        try {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();

            // Validate file type
            $allowedTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
                'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a',
                'video/mp4', 'video/webm', 'video/ogg', 'video/avi', 'video/mov'
            ];

            if (!in_array($mimeType, $allowedTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File type not allowed. Please upload images, audio, or video files.'
                ], 422);
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;

            // Determine storage directory based on file type
            $directory = 'lesson-content/';
            if (str_starts_with($mimeType, 'image/')) {
                $directory .= 'images';
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $directory .= 'audio';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $directory .= 'videos';
            }

            // Store file
            $path = $file->storeAs($directory, $filename, 'public');
            // Use custom route instead of Storage::url() to bypass LiteSpeed symlink restrictions
            $url = route('serve-storage', ['path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'url' => $url,
                'path' => $path,
                'filename' => $filename,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'size' => $file->getSize()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get allowed file types and their icons
     */
    public function getAllowedTypes(): array
    {
        return [
            'pdf' => ['icon' => 'bi-file-earmark-pdf', 'color' => 'text-danger'],
            'doc' => ['icon' => 'bi-file-earmark-word', 'color' => 'text-primary'],
            'docx' => ['icon' => 'bi-file-earmark-word', 'color' => 'text-primary'],
            'ppt' => ['icon' => 'bi-file-earmark-ppt', 'color' => 'text-warning'],
            'pptx' => ['icon' => 'bi-file-earmark-ppt', 'color' => 'text-warning'],
            'mp4' => ['icon' => 'bi-file-earmark-play', 'color' => 'text-info'],
            'mp3' => ['icon' => 'bi-file-earmark-music', 'color' => 'text-success'],
            'jpg' => ['icon' => 'bi-file-earmark-image', 'color' => 'text-secondary'],
            'jpeg' => ['icon' => 'bi-file-earmark-image', 'color' => 'text-secondary'],
            'png' => ['icon' => 'bi-file-earmark-image', 'color' => 'text-secondary'],
            'gif' => ['icon' => 'bi-file-earmark-image', 'color' => 'text-secondary'],
        ];
    }
}
