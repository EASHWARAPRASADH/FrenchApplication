@extends('layouts.app')

@section('title', $course->title . ' - TS Language Platform')

@section('content')
    <style>
        /* Course Learning Interface Styles */
        .course-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            border-radius: 0 0 24px 24px;
            margin-bottom: 2rem;
        }

        .course-content {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
            min-height: calc(100vh - 200px);
        }

        .sidebar {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .progress-overview {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: conic-gradient(#3b82f6 0deg, #3b82f6 var(--progress, 0deg), #e5e7eb var(--progress, 0deg));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            font-weight: 600;
            color: #1f2937;
        }

        .content-list {
            max-height: 60vh;
            overflow-y: auto;
        }

        .content-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .content-item:hover {
            background: #f8faff;
            border-color: #e0e7ff;
        }

        .content-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .content-item.completed {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .content-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .lesson-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }

        .test-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .content-info {
            flex: 1;
        }

        .content-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .content-meta {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .content-status {
            margin-left: auto;
            font-size: 1.2rem;
        }

        .main-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            min-height: 500px;
        }

        .welcome-screen {
            text-align: center;
            padding: 4rem 2rem;
        }

        .welcome-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: #f8faff;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 600;
            color: #667eea;
            display: block;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .achievement-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            margin: 0.25rem;
        }

        @media (max-width: 768px) {
            .course-content {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .sidebar {
                position: static;
                order: 2;
            }

            .main-content {
                order: 1;
            }
        }
    </style>

    <div class="course-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('student.courses') }}" class="text-white-50">My
                                    Courses</a></li>
                            <li class="breadcrumb-item active text-white">{{ $course->title }}</li>
                        </ol>
                    </nav>
                    <h1 class="mb-2">{{ $course->title }}</h1>
                    <p class="mb-0 opacity-90">{{ $course->description }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex justify-content-md-end gap-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $progressPercentage }}%</div>
                            <small class="opacity-75">Complete</small>
                        </div>
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $completedLessons }}</div>
                            <small class="opacity-75">Lessons Done</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="course-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Progress Overview -->
                <div class="progress-overview">
                    <div class="progress-circle" style="--progress: {{ $progressPercentage * 3.6 }}deg">
                        <span class="progress-text">{{ $progressPercentage }}%</span>
                    </div>
                    <h6 class="mb-1">Course Progress</h6>
                    <small class="text-muted">{{ $completedLessons }} of
                        {{ $totalLessons ?? ($course->lessons->count() ?? 0) }} lessons completed</small>
                </div>

                <!-- Course Content List -->
                <div class="content-list">
                    <h6 class="mb-3">Course Content</h6>

                    @if($topFolders->isEmpty() && $rootLessons->isEmpty() && $rootTests->isEmpty())
                        <div class="alert alert-light border py-4 text-center my-3 rounded-3 shadow-sm">
                            <i class="bi bi-shield-lock text-muted fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Content Locked</h6>
                            <p class="text-muted small mb-0">No folders or test sets have been assigned to your account yet. Please attend your scheduled live session or contact your instructor to unlock your materials.</p>
                        </div>
                    @endif

                    {{-- Top-level folders --}}
                    <div class="mb-2 small text-muted">Folders</div>
                    @forelse($topFolders as $folder)
                        <a href="{{ route('student.course.folder', ['course' => $course->id, 'folder' => $folder->id]) }}"
                            class="text-decoration-none text-dark">
                            <div class="content-item">
                                <div class="content-icon"
                                    style="background: linear-gradient(135deg,#10b981,#059669); color:white;">
                                    <i class="bi bi-folder2-open"></i>
                                </div>
                                <div class="content-info">
                                    <div class="content-title">{{ $folder->name }}</div>
                                    <div class="content-meta">
                                        @php
                                            $metaParts = [];
                                            $foldersCount = $folderAllowedCounts[$folder->id]['folders'] ?? 0;
                                            $lessonsCount = $folderAllowedCounts[$folder->id]['lessons'] ?? 0;
                                            $testsCount = $folderAllowedCounts[$folder->id]['tests'] ?? 0;
                                            
                                            if ($foldersCount > 0) {
                                                $metaParts[] = $foldersCount . ' ' . Str::plural('subfolder', $foldersCount);
                                            }
                                            if ($lessonsCount > 0) {
                                                $metaParts[] = $lessonsCount . ' ' . Str::plural('lesson', $lessonsCount);
                                            }
                                            if ($testsCount > 0) {
                                                $metaParts[] = $testsCount . ' ' . Str::plural('test', $testsCount);
                                            }
                                        @endphp
                                        Folder{{ !empty($metaParts) ? ' • ' . implode(', ', $metaParts) : '' }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-muted mb-3">No folders yet.</div>
                    @endforelse

                    {{-- Root-level lessons --}}
                    <div class="mt-3 mb-2 small text-muted">Lessons at Root</div>
                    @forelse($rootLessons as $lesson)
                        <div class="content-item {{ $lesson->isCompleted ? 'completed' : '' }}"
                            onclick="loadLesson({{ $lesson->id }})">
                            <div class="content-icon lesson-icon">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div class="content-info">
                                <div class="content-title">{{ $lesson->title }}</div>
                                <div class="content-meta">Lesson • {{ $lesson->estimated_duration ?? '10' }} min</div>
                            </div>
                            <div class="content-status">
                                @if($lesson->isCompleted)
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                @else
                                    <i class="bi bi-circle text-muted"></i>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">No root-level lessons.</div>
                    @endforelse

                    {{-- Root-level tests --}}
                    <div class="mt-3 mb-2 small text-muted">Tests at Root</div>
                    @forelse($rootTests as $test)
                        <div class="content-item" onclick="loadTest({{ $test->id }})">
                            <div class="content-icon test-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="content-info">
                                <div class="content-title">{{ $test->title }}</div>
                                <div class="content-meta">Test • {{ $test->questions()->count() }} questions</div>
                            </div>
                            <div class="content-status">
                                <i class="bi bi-circle text-muted"></i>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">No root-level tests.</div>
                    @endforelse

                    {{-- Root-level files --}}
                    <div class="mt-3 mb-2 small text-muted">Files at Root</div>
                    @forelse($rootFiles as $file)
                        <div class="content-item">
                            <div class="content-icon"
                                style="background: linear-gradient(135deg, #6b7280, #4b5563); color: white;">
                                @if(Str::contains($file->mime_type, ['image', 'jpg', 'png', 'jpeg']))
                                    <i class="bi bi-image"></i>
                                @elseif(Str::contains($file->mime_type, ['pdf']))
                                    <i class="bi bi-file-earmark-pdf"></i>
                                @else
                                    <i class="bi bi-file-earmark-text"></i>
                                @endif
                            </div>
                            <div class="content-info">
                                <div class="content-title">{{ $file->original_name }}</div>
                                <div class="content-meta">
                                    {{ strtoupper($file->type) }} •
                                    @if($file->downloadable)
                                        <a href="{{ route('file.download', $file) }}" class="text-primary text-decoration-none me-2" download>
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    @endif
                                    @if($file->viewable)
                                        @php
                                            $isOfficeViewable = in_array($file->type, ['PowerPoint', 'Word', 'Excel']);
                                            $isPdf = $file->type === 'PDF';
                                        @endphp

                                        @if($isPdf)
                                            <a href="javascript:void(0)"
                                                onclick="openSecureViewer('{{ route('file.preview', $file) }}', 'PDF', '{{ $file->downloadable ? route('file.download', $file) : '' }}', '{{ addslashes($file->original_name) }}')"
                                                class="text-info text-decoration-none">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @elseif($isOfficeViewable)
                                            <a href="javascript:void(0)"
                                                onclick="openSecureViewer('{{ $file->download_url }}', '{{ $file->type }}', '{{ $file->downloadable ? route('file.download', $file) : '' }}', '{{ addslashes($file->original_name) }}')"
                                                class="text-info text-decoration-none">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @else
                                            <a href="javascript:void(0)"
                                                onclick="openSecureViewer('{{ route('file.preview', $file) }}', '{{ $file->type }}', '{{ $file->downloadable ? route('file.download', $file) : '' }}', '{{ addslashes($file->original_name) }}')"
                                                class="text-info text-decoration-none">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">No root-level files.</div>
                    @endforelse
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content" id="mainContent">
                <!-- Welcome Screen -->
                <div class="welcome-screen">
                    <i class="bi bi-book welcome-icon"></i>
                    <h3>Welcome to {{ $course->title }}</h3>
                    <p class="text-muted mb-4">Select a lesson or test from the sidebar to begin your learning journey.</p>

                    <!-- Course Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-number">{{ $totalLessons ?? ($course->lessons->count() ?? 0) }}</span>
                            <div class="stat-label">Lessons</div>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">{{ $totalTests ?? ($course->tests->count() ?? 0) }}</span>
                            <div class="stat-label">Tests</div>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">{{ $estimatedDuration ?? '5' }}</span>
                            <div class="stat-label">Hours</div>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">{{ ucfirst($course->level) }}</span>
                            <div class="stat-label">Level</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if($nextLesson)
                            <button class="btn btn-primary-gradient" onclick="loadLesson({{ $nextLesson->id }})">
                                <i class="bi bi-play-fill me-2"></i>
                                {{ $progressPercentage > 0 ? 'Continue Learning' : 'Start Course' }}
                            </button>
                        @endif
                        <button class="btn btn-outline-secondary" onclick="showCourseInfo()">
                            <i class="bi bi-info-circle me-2"></i>
                            Course Info
                        </button>
                    </div>

                    <!-- Recent Achievements -->
                    @if($recentAchievements && $recentAchievements->count() > 0)
                        <div class="mt-4">
                            <h6>Recent Achievements</h6>
                            <div class="d-flex justify-content-center flex-wrap">
                                @foreach($recentAchievements as $achievement)
                                    <span class="achievement-badge">
                                        <i class="bi bi-trophy me-1"></i>
                                        {{ $achievement['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
      <!-- Secure Viewer Modal -->
  <div class="modal fade" id="secureViewerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary py-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="modal-title text-white mb-0"><i class="bi bi-shield-lock me-2 text-success"></i><span id="secureViewerTitle">Secure Viewer</span></h6>
                    <div id="engineSwitchGroup" class="btn-group btn-group-sm ms-3" style="display: none;">
                        <button type="button" class="btn btn-primary" id="btnViewerOffice" onclick="switchViewerEngine('office')">
                            <i class="bi bi-microsoft me-1"></i>Office Viewer (Fast)
                        </button>
                        <button type="button" class="btn btn-outline-light" id="btnViewerGoogle" onclick="switchViewerEngine('google')">
                            <i class="bi bi-google me-1"></i>Google Viewer
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    {{-- Non-intrusive Premium Header Download Button (strictly shown ONLY if downloadable is true) --}}
                    <a id="secureViewerHeaderDownload" href="" class="btn btn-outline-light btn-sm me-3" style="display: none; border-radius: 6px;" download>
                        <i class="bi bi-download me-1"></i> Download File
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 100%; overflow: hidden; background: #1e293b;" oncontextmenu="return false;">
                 {{-- The Toolbar Blocker: Covers the top 55px of the iframe --}}
                <div id="toolbarBlocker" style="position: absolute; top: 0; left: 0; width: 100%; height: 55px; background: transparent; z-index: 1056; cursor: not-allowed;" title="Download tools disabled"></div>
                
                <iframe id="secureFrame" src="" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
            </div>
        </div>
    </div>
  </div>

    @push('scripts')
        <script>
            let currentDocRawUrl = '';
            let currentDocType = '';

            function openSecureViewer(url, type, downloadUrl = '', title = 'Secure Viewer') {
                const frame = document.getElementById('secureFrame');
                const blocker = document.getElementById('toolbarBlocker');
                const headerDownload = document.getElementById('secureViewerHeaderDownload');
                const titleEl = document.getElementById('secureViewerTitle');
                const engineGroup = document.getElementById('engineSwitchGroup');
                
                frame.src = 'about:blank';
                if (titleEl) titleEl.textContent = title;

                currentDocRawUrl = url;
                currentDocType = type;

                const isOfficeDoc = ['PowerPoint', 'Word', 'Excel'].includes(type);

                if (isOfficeDoc) {
                    if (engineGroup) engineGroup.style.display = 'inline-flex';
                    setViewerEngineActive('office');
                    frame.src = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(url)}`;
                    blocker.style.display = 'block';
                } else if (type === 'PDF') {
                    if (engineGroup) engineGroup.style.display = 'none';
                    frame.src = url + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
                    blocker.style.display = 'block';
                } else {
                    if (engineGroup) engineGroup.style.display = 'none';
                    frame.src = url;
                    blocker.style.display = 'none';
                }

                if (downloadUrl) {
                    headerDownload.href = downloadUrl;
                    headerDownload.style.display = 'inline-block';
                } else {
                    headerDownload.style.display = 'none';
                }

                const modal = new bootstrap.Modal(document.getElementById('secureViewerModal'));
                modal.show();
            }

            function switchViewerEngine(engine) {
                const frame = document.getElementById('secureFrame');
                if (!frame || !currentDocRawUrl) return;

                setViewerEngineActive(engine);
                if (engine === 'office') {
                    frame.src = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(currentDocRawUrl)}`;
                } else {
                    frame.src = `https://docs.google.com/gview?url=${encodeURIComponent(currentDocRawUrl)}&embedded=true&cb=${Date.now()}`;
                }
            }

            function setViewerEngineActive(engine) {
                const btnOffice = document.getElementById('btnViewerOffice');
                const btnGoogle = document.getElementById('btnViewerGoogle');
                if (engine === 'office') {
                    btnOffice?.classList.add('btn-primary');
                    btnOffice?.classList.remove('btn-outline-light');
                    btnGoogle?.classList.remove('btn-primary');
                    btnGoogle?.classList.add('btn-outline-light');
                } else {
                    btnGoogle?.classList.add('btn-primary');
                    btnGoogle?.classList.remove('btn-outline-light');
                    btnOffice?.classList.remove('btn-primary');
                    btnOffice?.classList.add('btn-outline-light');
                }
            }

            // Block Ctrl+S / Cmd+S / Ctrl+P inside the document
            window.addEventListener('keydown', function(e) {
                const modal = document.getElementById('secureViewerModal');
                if (modal && modal.classList.contains('show')) {
                    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) {
                        e.preventDefault();
                        return false;
                    }
                }
            });

            // Load lesson content
            function loadLesson(lessonId) {
                const mainContent = document.getElementById('mainContent');
                mainContent.innerHTML = '<div class="text-center py-5"><i class="bi bi-hourglass-split fs-1"></i><br>Loading lesson...</div>';

                // Update active state
                document.querySelectorAll('.content-item').forEach(item => item.classList.remove('active'));
                event.currentTarget.classList.add('active');

                // Redirect to lesson page
                window.location.href = `/student/lesson/${lessonId}`;
            }

            // Load test content
            function loadTest(testId) {
                const mainContent = document.getElementById('mainContent');
                mainContent.innerHTML = '<div class="text-center py-5"><i class="bi bi-hourglass-split fs-1"></i><br>Loading test...</div>';

                // Update active state
                document.querySelectorAll('.content-item').forEach(item => item.classList.remove('active'));
                event.currentTarget.classList.add('active');

                // Redirect to test page
                window.location.href = `/student/test/${testId}`;
            }

            // Show course information
            function showCourseInfo() {
                alert('Course information modal would open here');
            }
        </script>
    @endpush
@endsection