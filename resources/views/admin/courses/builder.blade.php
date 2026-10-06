@extends('layouts.admin')

@section('title', 'Course Builder - ' . $course->title)

@push('styles')
    <style>
        .file-manager {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            min-height: 600px;
        }

        .file-manager-toolbar {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .file-manager-sidebar {
            background: white;
            border-right: 1px solid #dee2e6;
            min-height: 550px;
            padding: 1rem;
        }

        .file-manager-content {
            background: white;
            min-height: 550px;
            padding: 1rem;
        }

        .breadcrumb-nav {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #dee2e6;
            font-size: 0.9rem;
        }

        .file-item {
            padding: 0.75rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 0.5rem;
        }

        .file-item:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .file-item.selected {
            background: #e3f2fd;
            border-color: #2196f3;
        }

        .file-item.folder {
            background: #fff3e0;
            border-color: #ffb74d;
        }

        .file-item.lesson {
            background: #e8f5e8;
            border-color: #4caf50;
        }

        .file-item.test {
            background: #fce4ec;
            border-color: #e91e63;
        }

        .file-item.document {
            background: #f3e5f5;
            border-color: #ce93d8;
        }

        .file-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .folder-tree {
            list-style: none;
            padding-left: 0;
        }

        .folder-tree li {
            margin: 0.25rem 0;
        }

        .folder-tree .folder-item {
            padding: 0.5rem;
            border-radius: 0.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background 0.2s ease;
        }

        .folder-tree .folder-item:hover {
            background: #f8f9fa;
        }

        .folder-tree .folder-item.active {
            background: #e3f2fd;
            color: #1976d2;
        }

        .folder-tree .nested {
            padding-left: 1.5rem;
        }

        .context-menu {
            position: absolute;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 150px;
        }

        .context-menu-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            transition: background 0.2s ease;
        }

        .context-menu-item:hover {
            background: #f8f9fa;
        }

        .context-menu-item:last-child {
            border-bottom: none;
        }

        .view-toggle {
            display: flex;
            background: #f8f9fa;
            border-radius: 0.375rem;
            padding: 0.25rem;
        }

        .view-toggle button {
            border: none;
            background: transparent;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.2s ease;
        }

        .view-toggle button.active {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .grid-view .file-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem;
            min-height: 120px;
        }

        .grid-view .file-icon {
            width: 48px;
            height: 48px;
            margin-right: 0;
            margin-bottom: 0.5rem;
        }

        .list-view .file-item {
            display: flex;
            align-items: center;
        }

        .properties-panel {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
@endpush

@section('content')
    <!-- Course Builder Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-tools me-2"></i>
                Course Builder: {{ $course->title }}
            </h1>
            <p class="text-muted">
                {{ ucfirst($course->language) }} • {{ ucfirst($course->level) }} •
                <span
                    class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($course->status) }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Courses
            </a>
        </div>
    </div>

    <!-- File Manager Interface -->
    <div class="file-manager">
        <!-- Toolbar -->
        <div class="file-manager-toolbar">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" onclick="createFolder()">
                            <i class="bi bi-folder-plus me-1"></i>
                            New Folder
                        </button>
                        <button class="btn btn-success btn-sm" onclick="createLesson()">
                            <i class="bi bi-file-earmark-text-fill me-1"></i>
                            New Lesson
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="createTest()">
                            <i class="bi bi-clipboard-check-fill me-1"></i>
                            New Test
                        </button>
                        <div class="vr mx-2"></div>
                        <button class="btn btn-outline-secondary btn-sm" onclick="uploadFile()">
                            <i class="bi bi-cloud-upload me-1"></i>
                            Upload
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <div class="view-toggle">
                            <button class="active" onclick="setView('list')">
                                <i class="bi bi-list"></i>
                            </button>
                            <button onclick="setView('grid')">
                                <i class="bi bi-grid"></i>
                            </button>
                        </div>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control form-control-sm" placeholder="Search files..."
                                id="searchInput">
                            <button class="btn btn-outline-secondary btn-sm" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb-nav">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#" onclick="navigateToRoot()">
                            <i class="bi bi-house me-1"></i>
                            {{ $course->title }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" id="currentPath">Root</li>
                </ol>
            </nav>
        </div>

        <div class="row g-0">
            <!-- Sidebar - Folder Tree -->
            <div class="col-md-3">
                <div class="file-manager-sidebar">
                    <h6 class="mb-3">
                        <i class="bi bi-folder-fill me-2"></i>
                        Course Structure
                    </h6>
                    <ul class="folder-tree" id="folderTree">
                        <li>
                            <div class="folder-item active" onclick="selectFolder('root')">
                                <i class="bi bi-house me-2"></i>
                                {{ $course->title }}
                            </div>
                            <ul class="nested">
                                @foreach($course->folders as $folder)
                                    <li>
                                        <div class="folder-item" onclick="selectFolder('{{ $folder->id }}')"
                                            oncontextmenu="showContextMenu(event, 'folder', '{{ $folder->id }}')">
                                            <i class="bi bi-folder me-2"></i>
                                            {{ $folder->name }}
                                        </div>
                                        @if($folder->subfolders->count() > 0)
                                            <ul class="nested">
                                                @foreach($folder->subfolders as $subfolder)
                                                    <li>
                                                        <div class="folder-item" onclick="selectFolder('{{ $subfolder->id }}')"
                                                            oncontextmenu="showContextMenu(event, 'folder', '{{ $subfolder->id }}')">
                                                            <i class="bi bi-folder me-2"></i>
                                                            {{ $subfolder->name }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="file-manager-content">
                    <div id="fileList" class="list-view">
                        <!-- Root Level Items -->
                        @foreach($course->folders as $folder)
                            <div class="file-item folder" data-type="folder" data-id="{{ $folder->id }}"
                                ondblclick="openFolder('{{ $folder->id }}')"
                                oncontextmenu="showContextMenu(event, 'folder', '{{ $folder->id }}')">
                                <i class="bi bi-folder-fill file-icon text-warning"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $folder->name }}</div>
                                    <small class="text-muted">
                                        {{ $folder->subfolders->count() + $folder->lessons->count() + $folder->tests->count() }}
                                        items
                                        • Modified {{ $folder->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="file-actions">
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openMoveDuplicateModal('folder', '{{ $folder->id }}', false)" title="Move">
                                        <i class="bi bi-arrows-move"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openMoveDuplicateModal('folder', '{{ $folder->id }}', true)" title="Duplicate">
                                        <i class="bi bi-files"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="editItem('folder', '{{ $folder->id }}')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteItem('folder', '{{ $folder->id }}')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @foreach($course->lessons->where('folder_id', null) as $lesson)
                            <div class="file-item lesson" data-type="lesson" data-id="{{ $lesson->id }}"
                                ondblclick="openLesson('{{ $lesson->id }}')"
                                oncontextmenu="showContextMenu(event, 'lesson', '{{ $lesson->id }}')">
                                <i class="bi bi-file-earmark-text-fill file-icon text-success"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $lesson->title }}</div>
                                    <small class="text-muted">
                                        Lesson • {{ $lesson->duration ?? 'No duration set' }}
                                        • Modified {{ $lesson->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="file-actions">
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="previewItem('lesson', '{{ $lesson->id }}')" title="Preview">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openMoveDuplicateModal('lesson', '{{ $lesson->id }}', false)" title="Move">
                                        <i class="bi bi-arrows-move"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openMoveDuplicateModal('lesson', '{{ $lesson->id }}', true)" title="Duplicate">
                                        <i class="bi bi-files"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="editItem('lesson', '{{ $lesson->id }}')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteItem('lesson', '{{ $lesson->id }}')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @foreach($course->tests as $test)
                            <div class="file-item test" data-type="test" data-id="{{ $test->id }}"
                                ondblclick="openTest('{{ $test->id }}')"
                                oncontextmenu="showContextMenu(event, 'test', '{{ $test->id }}')">
                                <i class="bi bi-clipboard-check-fill file-icon text-danger"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $test->title }}</div>
                                    <small class="text-muted">
                                        Test • {{ $test->questions->count() ?? 0 }} questions
                                        • Modified {{ $test->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="file-actions">
                                    <button class="btn btn-sm btn-outline-info" onclick="previewItem('test', '{{ $test->id }}')"
                                        title="Preview">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openMoveDuplicateModal('test', '{{ $test->id }}', false)" title="Move">
                                        <i class="bi bi-arrows-move"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openMoveDuplicateModal('test', '{{ $test->id }}', true)" title="Duplicate">
                                        <i class="bi bi-files"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="editItem('test', '{{ $test->id }}')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteItem('test', '{{ $test->id }}')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @foreach($course->files as $file)
                            <div class="file-item document" data-type="file" data-id="{{ $file->id }}"
                                oncontextmenu="showContextMenu(event, 'file', '{{ $file->id }}')">
                                <i class="bi {{ $file->icon }} file-icon"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $file->original_name }}</div>
                                    <small class="text-muted">
                                        {{ $file->type }} • {{ number_format($file->size / 1024, 2) }} KB
                                        • Uploaded {{ $file->created_at->diffForHumans() }}
                                    </small>
                                    <div class="mt-2">
                                        <span class="badge {{ $file->downloadable ? 'bg-success' : 'bg-danger' }} me-2">
                                            <i
                                                class="bi bi-download me-1"></i>{{ $file->downloadable ? 'Download Allowed' : 'Download Blocked' }}
                                        </span>
                                        <span class="badge {{ $file->viewable ? 'bg-info' : 'bg-secondary' }}">
                                            <i
                                                class="bi bi-eye me-1"></i>{{ $file->viewable ? 'View Allowed' : 'View Blocked' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    @php
                                        $isViewable = str_contains($file->mime_type, 'powerpoint') || str_contains($file->mime_type, 'presentation') ||
                                            str_contains($file->mime_type, 'pdf') || str_contains($file->mime_type, 'word') ||
                                            str_contains($file->mime_type, 'document') || str_contains($file->mime_type, 'excel') ||
                                            str_contains($file->mime_type, 'spreadsheet') || str_contains($file->mime_type, 'csv');
                                    @endphp
                                    @if($isViewable)
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewFile('{{ $file->id }}', '{{ addslashes($file->original_name) }}', '{{ $file->download_url }}', '{{ $file->mime_type }}')"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @endif
                                    <a href="{{ $file->download_url }}" class="btn btn-sm btn-outline-info" title="Download"
                                        download>
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openFileSettings('{{ $file->id }}', '{{ $file->original_name }}', {{ $file->downloadable ? 'true' : 'false' }}, {{ $file->viewable ? 'true' : 'false' }})"
                                        title="Settings">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteItem('file', '{{ $file->id }}')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @if($course->folders->count() == 0 && $course->lessons->where('folder_id', null)->count() == 0 && $course->tests->count() == 0 && $course->files->count() == 0)
                            <div class="text-center py-5">
                                <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">This course is empty</h5>
                                <p class="text-muted">Start building your course by creating folders, lessons, or tests.</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-primary" onclick="createFolder()">
                                        <i class="bi bi-folder-plus me-1"></i>
                                        Create Folder
                                    </button>
                                    <button class="btn btn-success" onclick="createLesson()">
                                        <i class="bi bi-file-earmark-text-fill me-1"></i>
                                        Create Lesson
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>

    <!-- Properties Panel -->
    <div class="properties-panel" id="propertiesPanel" style="display: none;">
        <h6 class="mb-3">
            <i class="bi bi-info-circle me-2"></i>
            Properties
        </h6>
        <div id="propertiesContent">
            <p class="text-muted">Select an item to view its properties</p>
        </div>
    </div>

    <!-- Context Menu -->
    <div class="context-menu" id="contextMenu" style="display: none;">
        <div class="context-menu-item" onclick="editSelectedItem()">
            <i class="bi bi-pencil me-2"></i>
            Edit
        </div>
        <div class="context-menu-item" onclick="duplicateSelectedItem()">
            <i class="bi bi-files me-2"></i>
            Duplicate
        </div>
        <div class="context-menu-item" onclick="moveSelectedItem()">
            <i class="bi bi-arrow-right me-2"></i>
            Move
        </div>
        <div class="context-menu-item text-danger" onclick="deleteSelectedItem()">
            <i class="bi bi-trash me-2"></i>
            Delete
        </div>
    </div>

    <!-- Modals -->
    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-folder-plus me-2"></i>
                        Create New Folder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createFolderForm">
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" required>
                        </div>
                        <div class="mb-3">
                            <label for="folderDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="folderDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitCreateFolder()">Create Folder</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Lesson Modal -->
    <div class="modal fade" id="createLessonModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>
                        Create New Lesson
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createLessonForm">
                        <div class="mb-3">
                            <label for="lessonTitle" class="form-label">Lesson Title</label>
                            <input type="text" class="form-control" id="lessonTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="lessonDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="lessonDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lessonDuration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="lessonDuration" min="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitCreateLesson()">Create Lesson</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Test Modal -->
    <div class="modal fade" id="createTestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check-fill me-2"></i>
                        Create New Test
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createTestForm">
                        <div class="mb-3">
                            <label for="testTitle" class="form-label">Test Title</label>
                            <input type="text" class="form-control" id="testTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="testDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="testDescription" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="testDuration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="testDuration" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="testPassingScore" class="form-label">Passing Score (%)</label>
                                    <input type="number" class="form-control" id="testPassingScore" min="0" max="100"
                                        value="95">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="submitCreateTest()">Create Test</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Move / Duplicate Item Modal -->
    <div class="modal fade" id="moveDuplicateItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveDuplicateModalTitle">
                        <i class="bi bi-arrows-move me-2"></i>
                        Move/Duplicate Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center" style="font-size: 0.9rem; border-radius: 8px; border: none; background: #e0f2fe; color: #0369a1;">
                        <i class="bi bi-info-circle-fill me-2" style="font-size: 1.1rem;"></i>
                        <div>
                            Target Item: <strong id="targetItemNameDisplay" style="color: #0284c7;">...</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="destCourseSelect" class="form-label fw-semibold" style="font-size: 0.9rem;">Destination Course</label>
                        <select id="destCourseSelect" class="form-select" onchange="onDestCourseChanged()">
                            <!-- Courses populated dynamically -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="destFolderSelect" class="form-label fw-semibold" style="font-size: 0.9rem;">Destination Folder</label>
                        <select id="destFolderSelect" class="form-select" style="font-family: monospace, system-ui; font-size: 0.95rem;">
                            <!-- Folders populated dynamically -->
                        </select>
                    </div>

                    <div class="mb-3" id="newNameInputGroup" style="display: none;">
                        <label for="newItemNameInput" class="form-label fw-semibold" style="font-size: 0.9rem;">New Name / Title</label>
                        <input type="text" id="newItemNameInput" class="form-control" placeholder="Leave empty for auto-name">
                    </div>

                    <div class="small text-muted" id="moveDuplicateWarningText" style="font-size: 0.82rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Note: Moving items to other courses will automatically shift all sub-elements.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn id-submit-btn btn-primary" onclick="submitMoveDuplicateItem()">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View File Modal -->
    <div class="modal fade" id="viewFileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark me-2"></i>
                        <span id="viewFileTitle">View File</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="fileViewerContainer">
                        <!-- File viewer will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="downloadFileBtn" href="#" class="btn btn-primary" download>
                        <i class="bi bi-download me-1"></i>
                        Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- File Settings Modal -->
    <div class="modal fade" id="fileSettingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-gear me-2"></i>
                        File Settings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 class="mb-2">
                            <i class="bi bi-file-earmark me-2"></i>
                            <span id="settingsFileName">File Name</span>
                        </h6>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="downloadableToggle" checked>
                        <label class="form-check-label" for="downloadableToggle">
                            <i class="bi bi-download me-2"></i>
                            Allow Students to Download
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="viewableToggle" checked>
                        <label class="form-check-label" for="viewableToggle">
                            <i class="bi bi-eye me-2"></i>
                            Allow Students to View/Preview
                        </label>
                    </div>
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-2"></i>
                        These settings control what students can do with this file in the course.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveFileSettings()">Save Settings</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Add LibreOffice Online Viewer for PPTX -->


        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        {{-- Removed broken pptxjs CDN --}}
        <script>
            const courseId = {{ $course->id }};

            let currentFolder = 'root';
            let selectedItems = [];
            let currentView = 'list';

            // Universal Move / Duplicate State
            let activeItemType = null;
            let activeItemId = null;
            let isDuplicationMode = false;
            let courseOptionsData = null; // store course-folder structures fetched from server

            // Fetch course and folder options
            async function fetchMoveDuplicateOptions(type, id) {
                try {
                    if (courseOptionsData) {
                        return { success: true, courses: courseOptionsData, current_course_id: courseId };
                    }
                    const r = await fetch(`/admin/courses/${courseId}/all-move-options`);
                    const data = await r.json();
                    if (!data.success) throw new Error(data.message || 'Failed to load options');
                    courseOptionsData = data.courses;
                    return data;
                } catch (e) {
                    console.error(e);
                    alert('Error loading course structures');
                }
            }

            async function openMoveDuplicateModal(type, id, duplication = false) {
                activeItemType = type;
                activeItemId = id;
                isDuplicationMode = duplication;

                // Set targeting UI
                document.getElementById('targetItemNameDisplay').textContent = `${type.toUpperCase()} #${id}`;
                document.getElementById('newItemNameInput').value = '';

                // Set Title & Button text
                const titleEl = document.getElementById('moveDuplicateModalTitle');
                const submitBtn = document.querySelector('#moveDuplicateItemModal .id-submit-btn');
                const nameGroup = document.getElementById('newNameInputGroup');

                if (isDuplicationMode) {
                    titleEl.innerHTML = `<i class="bi bi-files me-2"></i>Duplicate ${type.toUpperCase()}`;
                    submitBtn.textContent = 'Duplicate';
                    nameGroup.style.display = 'block';
                } else {
                    titleEl.innerHTML = `<i class="bi bi-arrows-move me-2"></i>Move ${type.toUpperCase()}`;
                    submitBtn.textContent = 'Move';
                    nameGroup.style.display = 'none';
                }

                // Fetch metadata
                const data = await fetchMoveDuplicateOptions(type, id);
                if (!data) return;

                courseOptionsData = data.courses;

                // Populate Courses
                const courseSelect = document.getElementById('destCourseSelect');
                courseSelect.innerHTML = '';
                courseOptionsData.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.title;
                    courseSelect.appendChild(opt);
                });

                // Pre-select current course
                let preselectedCourseId = (type === 'test' && data.current_course_id) ? data.current_course_id : courseId;
                courseSelect.value = preselectedCourseId;

                // Trigger folder population
                onDestCourseChanged();

                // Pre-select current folder if applicable
                if (type === 'test' && data.current_folder_id) {
                    document.getElementById('destFolderSelect').value = data.current_folder_id;
                } else if (currentFolder !== 'root') {
                    document.getElementById('destFolderSelect').value = currentFolder;
                }

                const modal = new bootstrap.Modal(document.getElementById('moveDuplicateItemModal'));
                modal.show();
            }

            window.onDestCourseChanged = function() {
                const courseIdSelected = parseInt(document.getElementById('destCourseSelect').value);
                const folderSelect = document.getElementById('destFolderSelect');
                folderSelect.innerHTML = '';

                const courseData = courseOptionsData.find(c => c.id === courseIdSelected);
                if (courseData && courseData.folders) {
                    courseData.folders.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id || '';
                        opt.innerHTML = f.id ? f.display_name : 'Course Root (Top Level)';
                        // Prevent moving a folder into itself
                        if (activeItemType === 'folder' && !isDuplicationMode && courseIdSelected === courseId && f.id === parseInt(activeItemId)) {
                            opt.disabled = true;
                            opt.innerHTML += ' (Cannot select self)';
                        }
                        folderSelect.appendChild(opt);
                    });
                }
            };

            window.submitMoveDuplicateItem = function() {
                const destCourseId = document.getElementById('destCourseSelect').value;
                const destFolderId = document.getElementById('destFolderSelect').value;
                const newTitle = document.getElementById('newItemNameInput').value.trim();

                if (!destCourseId) {
                    alert('Please select a destination course');
                    return;
                }

                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let endpoint = `/admin/${activeItemType}s/${activeItemId}/${isDuplicationMode ? 'duplicate' : 'move'}`;

                let payload = {
                    destination_course_id: destCourseId,
                    destination_folder_id: destFolderId || null
                };

                if (isDuplicationMode) {
                    if (activeItemType === 'folder') {
                        payload.new_name = newTitle;
                    } else {
                        payload.new_title = newTitle;
                    }
                }

                const method = isDuplicationMode ? 'POST' : 'PUT';

                const submitBtn = document.querySelector('#moveDuplicateItemModal .id-submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = isDuplicationMode ? 'Duplicating...' : 'Moving...';

                fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        bootstrap.Modal.getInstance(document.getElementById('moveDuplicateItemModal')).hide();
                        location.reload();
                    } else {
                        alert(res.message || 'Operation failed');
                        submitBtn.disabled = false;
                        submitBtn.textContent = isDuplicationMode ? 'Duplicate' : 'Move';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error encountered');
                    submitBtn.disabled = false;
                    submitBtn.textContent = isDuplicationMode ? 'Duplicate' : 'Move';
                });
            };


            // Initialize the file manager
            document.addEventListener('DOMContentLoaded', function () {
                // Hide context menu when clicking elsewhere
                document.addEventListener('click', function () {
                    hideContextMenu();
                });

                // Search functionality
                document.getElementById('searchInput').addEventListener('input', function () {
                    filterFiles(this.value);
                });
                
                // Load initial folder from URL
                const urlParams = new URLSearchParams(window.location.search);
                const initialFolder = urlParams.get('folder');
                if (initialFolder) {
                    selectFolder(initialFolder);
                }
            });

            // View Management
            function setView(view) {
                currentView = view;
                const fileList = document.getElementById('fileList');
                const buttons = document.querySelectorAll('.view-toggle button');

                buttons.forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');

                fileList.className = view + '-view';
            }

            // Navigation
            function navigateToRoot() {
                currentFolder = 'root';
                updateBreadcrumb('Root');
                
                // Update URL
                const url = new URL(window.location);
                url.searchParams.delete('folder');
                window.history.pushState({folder: 'root'}, '', url);
                
                loadFolderContent('root');
            }

            function selectFolder(folderId) {
                console.log('selectFolder called with:', folderId);
                currentFolder = folderId;
                console.log('currentFolder set to:', currentFolder);

                const folderItems = document.querySelectorAll('.folder-item');
                folderItems.forEach(item => item.classList.remove('active'));

                // Find and activate the clicked folder item
                const clickedItem = document.querySelector(`[onclick="selectFolder('${folderId}')"]`);
                if (clickedItem) {
                    clickedItem.classList.add('active');
                }

                if (folderId === 'root') {
                    updateBreadcrumb('{{ $course->title }}');
                } else {
                    // Get folder name from the clicked element
                    const folderName = clickedItem ? clickedItem.textContent.trim() : 'Folder';
                    updateBreadcrumb(folderName);
                }
                
                // Update URL state
                const url = new URL(window.location);
                if (folderId === 'root') {
                    url.searchParams.delete('folder');
                } else {
                    url.searchParams.set('folder', folderId);
                }
                window.history.pushState({folder: folderId}, '', url);

                loadFolderContent(folderId);
            }

            function updateBreadcrumb(path) {
                document.getElementById('currentPath').textContent = path;
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                const folderId = event.state && event.state.folder ? event.state.folder : (new URLSearchParams(window.location.search).get('folder') || 'root');
                currentFolder = folderId;
                loadFolderContent(folderId);
            });

            // File Operations
            function createFolder() {
                const modal = new bootstrap.Modal(document.getElementById('createFolderModal'));
                modal.show();
            }

            function createLesson() {
                const modal = new bootstrap.Modal(document.getElementById('createLessonModal'));
                modal.show();
            }

            function createTest() {
                const modal = new bootstrap.Modal(document.getElementById('createTestModal'));
                modal.show();
            }

            function uploadFile() {
                // Create a file input dynamically
                const input = document.createElement('input');
                input.type = 'file';
                input.multiple = true;
                input.accept = '.pdf,.doc,.docx,.ppt,.pptx,.odc,.odp,.odt,.xls,.xlsx,.csv,.mp4,.mp3,.jpg,.jpeg,.png,.gif';
                input.onchange = function (e) {
                    handleFileUpload(e.target.files);
                };
                input.click();
            }

            // Item Actions
            function openFolder(folderId) {
                selectFolder(folderId);
            }

            function openLesson(lessonId) {
                window.open(`/admin/lessons/${lessonId}/edit`, '_blank');
            }

            function openTest(testId) {
                window.open(`/admin/tests/${testId}/edit`, '_blank');
            }

            function editItem(type, id) {
                switch (type) {
                    case 'folder':
                        editFolder(id);
                        break;
                    case 'lesson':
                        editLesson(id);
                        break;
                    case 'test':
                        editTest(id);
                        break;
                }
            }

            // Edit lesson function
            function editLesson(lessonId) {
                window.open(`/admin/lessons/${lessonId}/edit`, '_blank');
            }

            // Edit test function
            function editTest(testId) {
                window.open(`/admin/tests/${testId}/edit`, '_blank');
            }

            // Preview item function
            function previewItem(type, id) {
                if (type === 'lesson') {
                    window.open(`/student/lesson/${id}/preview`, '_blank');
                } else if (type === 'test') {
                    window.open(`/student/test/${id}/preview`, '_blank');
                }
            }

            function deleteItem(type, id) {
                if (confirm(`Are you sure you want to delete this ${type}?`)) {
                    // Make AJAX request to delete
                    let endpoint = `/admin/${type}s/${id}`;

                    // Special handling for files
                    if (type === 'file') {
                        endpoint = `/admin/files/${id}`;
                    }

                    fetch(endpoint, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload(); // Refresh the page
                            } else {
                                alert('Error deleting item: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting item');
                        });
                }
            }

            // Context Menu
            function showContextMenu(event, type, id) {
                event.preventDefault();
                const contextMenu = document.getElementById('contextMenu');
                contextMenu.style.display = 'block';
                contextMenu.style.left = event.pageX + 'px';
                contextMenu.style.top = event.pageY + 'px';

                // Store selected item info
                contextMenu.dataset.type = type;
                contextMenu.dataset.id = id;
            }

            function hideContextMenu() {
                document.getElementById('contextMenu').style.display = 'none';
            }


            // Context menu action: Edit
            function editSelectedItem() {
                const menu = document.getElementById('contextMenu');
                const type = menu.dataset.type;
                const id = menu.dataset.id;
                hideContextMenu();
                if (type && id) {
                    editItem(type, id);
                }
            }

            // Context menu action: Duplicate
            function duplicateSelectedItem() {
                const menu = document.getElementById('contextMenu');
                const type = menu.dataset.type;
                const id = menu.dataset.id;
                hideContextMenu();
                if (type && id) {
                    openMoveDuplicateModal(type, id, true);
                }
            }

            // Context menu action: Move
            function moveSelectedItem() {
                const menu = document.getElementById('contextMenu');
                const type = menu.dataset.type;
                const id = menu.dataset.id;
                hideContextMenu();
                if (type && id) {
                    openMoveDuplicateModal(type, id, false);
                }
            }

            // Context menu action: Delete
            function deleteSelectedItem() {
                const menu = document.getElementById('contextMenu');
                const type = menu.dataset.type;
                const id = menu.dataset.id;
                hideContextMenu();
                if (type && id) {
                    deleteItem(type, id);
                }
            }

            function openMoveFolder(id) {
                openMoveDuplicateModal('folder', id, false);
            }

            // Form Submissions
            function submitCreateFolder() {
                const name = document.getElementById('folderName').value;
                const description = document.getElementById('folderDescription').value;

                if (!name) {
                    alert('Please enter a folder name');
                    return;
                }

                console.log('Creating folder:', name, description);

                fetch('/admin/folders', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        description: description,
                        course_id: {{ $course->id }},
                        parent_folder_id: currentFolder === 'root' ? null : currentFolder
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);
                        if (data.success) {
                            alert('Folder created successfully!');
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createFolderModal'));
                            modal.hide();
                            // Clear form
                            document.getElementById('folderName').value = '';
                            document.getElementById('folderDescription').value = '';
                            // Refresh current folder content
                            loadFolderContent(currentFolder);
                        } else {
                            alert('Error creating folder: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error creating folder');
                    });
            }




            // Edit/Rename folder
            function editFolder(folderId) {
                // Fetch current folder details

                fetch(`/admin/folders/${folderId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Unable to load folder');
                        const currentName = data.folder.name || '';
                        const currentDesc = data.folder.description || '';
                        const name = prompt('Folder name:', currentName);
                        if (name === null) return; // cancelled
                        const description = prompt('Folder description (optional):', currentDesc);

                        fetch(`/admin/folders/${folderId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ name, description })
                        })
                            .then(r => r.json())
                            .then(res => {
                                if (res.success) {
                                    alert('Folder updated');
                                    loadFolderContent(currentFolder);
                                } else {
                                    alert(res.message || 'Unable to update folder');
                                }
                            })
                            .catch(err => {
                                console.error('Update folder error:', err);
                                alert('Network error updating folder');
                            });
                    })
                    .catch(err => {
                        console.error('Load folder error:', err);
                        alert('Unable to load folder details');
                    });
            }

            function submitCreateLesson() {
                const title = document.getElementById('lessonTitle').value;
                const description = document.getElementById('lessonDescription').value;
                const duration = document.getElementById('lessonDuration').value;

                console.log('Creating lesson in folder:', currentFolder);

                if (!title) {
                    alert('Please enter a lesson title');
                    return;
                }

                fetch('/admin/lessons', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: title,
                        description: description,
                        duration: duration,
                        course_id: {{ $course->id }},
                        folder_id: currentFolder === 'root' ? null : currentFolder
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Lesson creation response:', data);
                        if (data.success) {
                            alert('Lesson created successfully!');
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createLessonModal'));
                            modal.hide();
                            // Clear form
                            document.getElementById('lessonTitle').value = '';
                            document.getElementById('lessonDescription').value = '';
                            document.getElementById('lessonDuration').value = '';
                            // Refresh current folder content
                            loadFolderContent(currentFolder);
                        } else {
                            alert('Error creating lesson: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error creating lesson');
                    });
            }

            function submitCreateTest() {
                const title = document.getElementById('testTitle').value;
                const description = document.getElementById('testDescription').value;
                const duration = document.getElementById('testDuration').value;
                const passingScore = document.getElementById('testPassingScore').value;

                console.log('Creating test in folder:', currentFolder);



                if (!title) {
                    alert('Please enter a test title');
                    return;
                }

                fetch('/admin/tests', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: title,
                        description: description,
                        duration: duration,
                        passing_score: passingScore,
                        course_id: {{ $course->id }},
                        folder_id: currentFolder === 'root' ? null : currentFolder
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Test creation response:', data);
                        if (data.success) {
                            alert('Test created successfully!');
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createTestModal'));
                            modal.hide();
                            // Clear form
                            document.getElementById('testTitle').value = '';
                            document.getElementById('testDescription').value = '';
                            document.getElementById('testDuration').value = '';
                            document.getElementById('testPassingScore').value = '';
                            // Refresh current folder content
                            loadFolderContent(currentFolder);
                        } else {
                            alert('Error creating test: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error creating test');
                    });
            }

            // Search and Filter
            function filterFiles(searchTerm) {
                const fileItems = document.querySelectorAll('.file-item');
                fileItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm.toLowerCase())) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // File Upload Handler
            function handleFileUpload(files) {
                if (files.length === 0) return;

                // Show loading modal
                const uploadModal = new bootstrap.Modal(document.getElementById('uploadingModal'));
                uploadModal.show();

                const formData = new FormData();
                for (let file of files) {
                    formData.append('files[]', file);
                }
                formData.append('course_id', {{ $course->id }});
                if (currentFolder !== 'root') {
                    formData.append('folder_id', currentFolder);
                }

                fetch('/admin/files/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errData => {
                                throw new Error(errData.message || `Server returned ${response.status}`);
                            }).catch(e => {
                                // If body isn't JSON (e.g. 413 default page)
                                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            bootstrap.Modal.getInstance(document.getElementById('uploadingModal')).hide();
                            alert('Error uploading files: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const modalEl = document.getElementById('uploadingModal');
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                        alert('Network error uploading files: ' + error.message);
                    });
            }

            // Load folder content dynamically
            function loadFolderContent(folderId) {
                const contentArea = document.getElementById('fileList');

                if (folderId === 'root') {
                    // For root, reload the page to show the server-rendered content
                    window.location.href = window.location.pathname;
                    return;
                }

                // Show loading state
                contentArea.innerHTML = '<div class="text-center py-5"><i class="bi bi-hourglass-split"></i> Loading...</div>';

                // Make AJAX request to get folder contents
                fetch(`/admin/courses/{{ $course->id }}/folder/${folderId}/items`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderFolderContents(data.contents, folderId);
                        } else {
                            contentArea.innerHTML = '<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle"></i> Error loading folder contents</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading folder:', error);
                        contentArea.innerHTML = '<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle"></i> Error loading folder contents</div>';
                    });
            }

            // Render folder contents in the main area
            function renderFolderContents(contents, folderId) {
                const contentArea = document.getElementById('fileList');

                if (!contents || (contents.folders.length === 0 && contents.lessons.length === 0 && contents.tests.length === 0 && (!contents.files || contents.files.length === 0))) {
                    contentArea.innerHTML = `
                                                    <div class="text-center py-5">
                                                        <i class="bi bi-folder-x fs-1 text-muted mb-3"></i>
                                                        <h5 class="text-muted">This folder is empty</h5>
                                                        <p class="text-muted">Add folders, lessons, tests, or files to get started.</p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <button class="btn btn-primary btn-sm" onclick="createFolder()">
                                                                <i class="bi bi-folder-plus me-1"></i> New Folder
                                                            </button>
                                                            <button class="btn btn-success btn-sm" onclick="createLesson()">
                                                                <i class="bi bi-file-earmark-text-fill me-1"></i> New Lesson
                                                            </button>
                                                            <button class="btn btn-warning btn-sm" onclick="createTest()">
                                                                <i class="bi bi-clipboard-check-fill me-1"></i> New Test
                                                            </button>
                                                        </div>
                                                    </div>
                                                `;
                    return;
                }

                let html = '';

                // Add back navigation if not in root
                if (folderId !== 'root') {
                    html += `
                                                    <div class="file-item folder" onclick="goBack()" style="border-bottom: 1px solid #dee2e6; margin-bottom: 10px;">
                                                        <i class="bi bi-arrow-left file-icon text-secondary"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold">.. (Go Back)</div>
                                                            <small class="text-muted">Return to parent folder</small>
                                                        </div>
                                                    </div>
                                                `;
                }

                // Render folders
                contents.folders.forEach(folder => {
                    html += `
                                                    <div class="file-item folder" data-type="folder" data-id="${folder.id}"
                                                         ondblclick="openFolder('${folder.id}')"
                                                         oncontextmenu="showContextMenu(event, 'folder', '${folder.id}')">
                                                        <i class="bi bi-folder-fill file-icon text-warning"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold">${folder.name}</div>
                                                            <small class="text-muted">
                                                                ${folder.items_count || 0} items • Modified ${folder.updated_at}
                                                            </small>
                                                        </div>
                                                        <div class="file-actions">
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openMoveDuplicateModal('folder', '${folder.id}', false)" title="Move">
                                                                <i class="bi bi-arrows-move"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openMoveDuplicateModal('folder', '${folder.id}', true)" title="Duplicate">
                                                                <i class="bi bi-files"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-primary" onclick="editFolder('${folder.id}')" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('folder', '${folder.id}')" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                 `;
                });

                // Render lessons
                contents.lessons.forEach(lesson => {
                    html += `
                                                    <div class="file-item lesson" data-type="lesson" data-id="${lesson.id}"
                                                         ondblclick="openLesson('${lesson.id}')"
                                                         oncontextmenu="showContextMenu(event, 'lesson', '${lesson.id}')">
                                                        <i class="bi bi-file-earmark-text-fill file-icon text-success"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold">${lesson.title}</div>
                                                            <small class="text-muted">
                                                                ${lesson.duration || 'No duration'} min • ${lesson.status} • Modified ${lesson.updated_at}
                                                            </small>
                                                        </div>
                                                        <div class="file-actions">
                                                            <button class="btn btn-sm btn-outline-info" onclick="previewItem('lesson', '${lesson.id}')" title="Preview">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openMoveDuplicateModal('lesson', '${lesson.id}', false)" title="Move">
                                                                <i class="bi bi-arrows-move"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openMoveDuplicateModal('lesson', '${lesson.id}', true)" title="Duplicate">
                                                                <i class="bi bi-files"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openLesson('${lesson.id}')" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('lesson', '${lesson.id}')" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                 `;
                });

                // Render tests
                contents.tests.forEach(test => {
                    html += `
                                                    <div class="file-item test" data-type="test" data-id="${test.id}"
                                                         ondblclick="openTest('${test.id}')"
                                                         oncontextmenu="showContextMenu(event, 'test', '${test.id}')">
                                                        <i class="bi bi-clipboard-check-fill file-icon text-danger"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold">${test.title}</div>
                                                            <small class="text-muted">
                                                                ${test.duration || 'No duration'} min • ${test.passing_score}% pass • ${test.status} • Modified ${test.updated_at}
                                                            </small>
                                                        </div>
                                                        <div class="file-actions">
                                                            <button class="btn btn-sm btn-outline-info" onclick="previewItem('test', '${test.id}')" title="Preview">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openMoveDuplicateModal('test', '${test.id}', false)" title="Move">
                                                                <i class="bi bi-arrows-move"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openMoveDuplicateModal('test', '${test.id}', true)" title="Duplicate">
                                                                <i class="bi bi-files"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="openTest('${test.id}')" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('test', '${test.id}')" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                 `;
                });

                // Render files
                if (contents.files && contents.files.length > 0) {
                    contents.files.forEach(file => {
                        const isViewable = file.mime_type.includes('powerpoint') || file.mime_type.includes('presentation') ||
                            file.mime_type.includes('pdf') || file.mime_type.includes('word') ||
                            file.mime_type.includes('document') || file.mime_type.includes('excel') ||
                            file.mime_type.includes('spreadsheet') || file.mime_type.includes('csv') ||
                            file.original_name.toLowerCase().endsWith('.xlsx') || file.original_name.toLowerCase().endsWith('.xls') ||
                            file.original_name.toLowerCase().endsWith('.csv');

                        // Escape quotes for JS function calls
                        const safeFileName = file.original_name.replace(/'/g, "\\'");
                        const safeDownloadUrl = (file.download_url + '?cb=' + Date.now()).replace(/'/g, "\\'");
                        const safeMimeType = file.mime_type.replace(/'/g, "\\'");

                        html += `
                                                        <div class="file-item document" data-type="file" data-id="${file.id}"
                                                             oncontextmenu="showContextMenu(event, 'file', '${file.id}')">
                                                            <i class="bi ${file.icon} file-icon"></i>
                                                            <div class="flex-grow-1">
                                                                <div class="fw-semibold">${file.original_name}</div>
                                                                <small class="text-muted">
                                                                    ${file.type} • ${(file.size / 1024).toFixed(2)} KB • Modified ${file.updated_at}
                                                                </small>
                                                                <div class="mt-2">
                                                                    <span class="badge ${file.downloadable ? 'bg-success' : 'bg-danger'} me-2">
                                                                        <i class="bi bi-download me-1"></i>${file.downloadable ? 'Download Allowed' : 'Download Blocked'}
                                                                    </span>
                                                                    <span class="badge ${file.viewable ? 'bg-info' : 'bg-secondary'}">
                                                                        <i class="bi bi-eye me-1"></i>${file.viewable ? 'View Allowed' : 'View Blocked'}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="file-actions">
                                                                ${isViewable ? `<button class="btn btn-sm btn-outline-primary" onclick="viewFile('${file.id}', '${safeFileName}', '${safeDownloadUrl}', '${safeMimeType}')" title="View">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>` : ''}
                                                                <a href="${file.download_url}" class="btn btn-sm btn-outline-info" title="Download" download>
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <button class="btn btn-sm btn-outline-warning" onclick="openFileSettings('${file.id}', '${safeFileName}', ${file.downloadable}, ${file.viewable})" title="Settings">
                                                                    <i class="bi bi-gear"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('file', '${file.id}')" title="Delete">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    `;
                    });
                }

                contentArea.innerHTML = html;
            }

            // Go back to parent folder
            function goBack() {
                // For now, go back to root - in a full implementation, you'd track the folder hierarchy
                selectFolder('root');
                loadFolderContent('root');
            }

            // View file in modal
            function viewFile(fileId, fileName, downloadUrl, mimeType) {
                const modal = new bootstrap.Modal(document.getElementById('viewFileModal'));
                const container = document.getElementById('fileViewerContainer');
                const titleElement = document.getElementById('viewFileTitle');
                const downloadBtn = document.getElementById('downloadFileBtn');

                titleElement.textContent = fileName;
                if (downloadBtn) downloadBtn.href = downloadUrl;

                // Clear previous content
                container.innerHTML = '';

                const lowerName = fileName.toLowerCase();
                const isPpt = mimeType.includes('powerpoint') || mimeType.includes('presentation') || lowerName.endsWith('.pptx') || lowerName.endsWith('.ppt');
                const isExcel = mimeType.includes('excel') || mimeType.includes('spreadsheet') || mimeType.includes('csv') || lowerName.endsWith('.xlsx') || lowerName.endsWith('.xls') || lowerName.endsWith('.csv');
                const isWord = mimeType.includes('word') || mimeType.includes('document') || lowerName.endsWith('.docx') || lowerName.endsWith('.doc');
                const isPdf = mimeType.includes('pdf') || lowerName.endsWith('.pdf');

                if (isPpt || isExcel || isWord) {
                    const docTypeName = isPpt ? 'PowerPoint Presentation' : (isExcel ? 'Excel Spreadsheet' : 'Word Document');
                    const encodedUrl = encodeURIComponent(downloadUrl);
                    container.innerHTML = `
                        <div class="p-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border"><i class="bi bi-file-earmark me-1"></i>${docTypeName}</span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-primary" id="btnOfficeViewer" onclick="switchViewerTab('office', '${encodedUrl}')">
                                        <i class="bi bi-microsoft me-1"></i>Office Viewer (Fast)
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnGoogleViewer" onclick="switchViewerTab('google', '${encodedUrl}')">
                                        <i class="bi bi-google me-1"></i>Google Viewer
                                    </button>
                                </div>
                            </div>
                            <div id="viewerIframeWrapper" style="position: relative; min-height: 600px;">
                                <iframe id="builderDocIframe"
                                    src="https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}"
                                    style="width: 100%; height: 600px; border: 1px solid #e2e8f0; border-radius: 0.375rem;"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    `;
                } else if (isPdf) {
                    container.innerHTML = `
                        <div class="text-center p-2">
                            <p class="text-muted mb-2">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>Viewing PDF document
                            </p>
                            <iframe
                                src="${downloadUrl}#toolbar=0"
                                style="width: 100%; height: 600px; border: 1px solid #e2e8f0; border-radius: 0.375rem;"
                                allowfullscreen>
                            </iframe>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="alert alert-info m-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Preview not available for this file type. Please download to view.
                        </div>
                    `;
                }

                modal.show();
            }

            function switchViewerTab(engine, encodedUrl) {
                const iframe = document.getElementById('builderDocIframe');
                const btnOffice = document.getElementById('btnOfficeViewer');
                const btnGoogle = document.getElementById('btnGoogleViewer');
                if (!iframe) return;

                if (engine === 'office') {
                    iframe.src = `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}`;
                    btnOffice?.classList.add('btn-primary');
                    btnOffice?.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                    btnGoogle?.classList.remove('btn-primary');
                    btnGoogle?.classList.add('btn-outline-secondary');
                } else {
                    iframe.src = `https://docs.google.com/gview?url=${encodedUrl}&embedded=true`;
                    btnGoogle?.classList.add('btn-primary');
                    btnGoogle?.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                    btnOffice?.classList.remove('btn-primary');
                    btnOffice?.classList.add('btn-outline-secondary');
                }
            }

            // Open file settings modal
            let currentFileId = null;
            function openFileSettings(fileId, fileName, downloadable, viewable) {
                currentFileId = fileId;
                document.getElementById('settingsFileName').textContent = fileName;
                document.getElementById('downloadableToggle').checked = downloadable;
                document.getElementById('viewableToggle').checked = viewable;

                const modal = new bootstrap.Modal(document.getElementById('fileSettingsModal'));
                modal.show();
            }

            // Save file settings
            function saveFileSettings() {
                if (!currentFileId) return;

                const saveBtn = document.querySelector('#fileSettingsModal .btn-primary');
                const originalText = saveBtn.innerHTML;
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

                const downloadable = document.getElementById('downloadableToggle').checked;
                const viewable = document.getElementById('viewableToggle').checked;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value;

                if (!csrfToken) {
                    alert('Error: CSRF token not found. Please refresh the page and try again.');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                    return;
                }

                console.log('Saving file settings for file ID:', currentFileId);
                console.log('Downloadable:', downloadable, 'Viewable:', viewable);

                fetch(`/admin/files/${currentFileId}/settings`, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        downloadable: downloadable,
                        viewable: viewable
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP error! status: ${response.status}, body: ${text.substring(0, 200)}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('fileSettingsModal')).hide();
                            // Reload the page to show updated settings
                            location.reload();
                        } else {
                            alert('Error saving settings: ' + (data.message || 'Unknown error'));
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error saving settings: ' + error.message);
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalText;
                    });
            }
        </script>
    @endpush

    <!-- Uploading Modal -->
    <div class="modal fade" id="uploadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Uploading Files...</h5>
                    <p class="text-muted mb-0">Please wait while your files are being processed.</p>
                </div>
            </div>
        </div>
    </div>
@endsection