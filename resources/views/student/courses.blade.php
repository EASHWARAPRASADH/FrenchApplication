@extends('layouts.app')

@section('title', 'My Courses - TS Language Platform')

@section('content')
    <style>
        /* Student Courses Styles */
        .course-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .course-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .course-teacher {
            opacity: 0.9;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        .course-body {
            padding: 1.5rem;
        }

        .course-description {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .progress-section {
            margin-bottom: 1.5rem;
        }

        .progress-label {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .progress-bar-container {
            background: #f3f4f6;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .course-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            text-align: center;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 8px;
        }

        .stat-number {
            display: block;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .course-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-continue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-outline {
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            border-color: #667eea;
            color: #667eea;
            background: #f8faff;
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            z-index: 2;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: #059669;
        }

        .status-completed {
            background: rgba(59, 130, 246, 0.2);
            color: #2563eb;
        }

        .status-paused {
            background: rgba(245, 158, 11, 0.2);
            color: #d97706;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 24px 24px;
        }

        .filter-tabs {
            background: white;
            border-radius: 12px;
            padding: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: inline-flex;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #6b7280;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-tab.active {
            background: #667eea;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .empty-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .course-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .course-actions {
                flex-direction: column;
            }

            .filter-tabs {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">My Courses</h1>
                    <p class="mb-0 opacity-90">Continue your French learning journey</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex justify-content-md-end gap-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $enrollments->total() }}</div>
                            <small class="opacity-75">Total Courses</small>
                        </div>
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $enrollments->where('status', 'active')->count() }}</div>
                            <small class="opacity-75">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if(isset($levels) && $levels->count() > 0)
            <!-- Levels Grid (Hierarchical first level) -->
            <div class="row">
                @foreach($levels as $lvl)
                    @php $label = $lvl['level']; @endphp
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="{{ route('student.courses.level', ['level' => $label]) }}" class="text-decoration-none text-dark">
                            <div class="course-card">
                                <div class="course-header">
                                    <div class="course-title">Level {{ $label }}</div>
                                    <div class="course-teacher">
                                        <i class="bi bi-layers me-1"></i>
                                        {{ $lvl['count'] }} course{{ $lvl['count'] == 1 ? '' : 's' }}
                                    </div>
                                </div>
                                <div class="course-body">
                                    <div class="course-description">
                                        Click to view your courses in Level {{ $label }}
                                    </div>
                                    <div class="course-actions">
                                        <span class="btn btn-continue w-100 text-center">
                                            <i class="bi bi-folder2-open me-2"></i>Open Level
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if(!isset($levels) || $levels->count() === 0)
            <!-- Filter Tabs -->
            <div class="d-flex justify-content-center mb-4">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">All Courses</button>
                    <button class="filter-tab" data-filter="active">Active</button>
                    <button class="filter-tab" data-filter="completed">Completed</button>
                    <button class="filter-tab" data-filter="paused">Paused</button>
                </div>
            </div>

            <!-- Courses Grid -->
            @if($enrollments->count() > 0)
                <div class="row" id="coursesGrid">
                    @foreach($enrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $totalLessons = $course->lessons->count();
                            $completedLessons = $course->lessons->where('lessonProgress.status', 'completed')->count();
                            $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
                            $status = $enrollment->status ?? 'active';
                        @endphp

                        <div class="col-lg-4 col-md-6 mb-4 course-item" data-status="{{ $status }}">
                            <div class="course-card">
                                <div class="course-header">
                                    <div class="status-badge status-{{ $status }}">
                                        {{ ucfirst($status) }}
                                    </div>
                                    <div class="course-title">{{ $course->title }}</div>
                                    <div class="course-teacher">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $course->teacher?->name ?? 'Instructor' }}
                                    </div>
                                </div>

                                <div class="course-body">
                                    <div class="course-description">
                                        {{ Str::limit($course->description, 120) }}
                                    </div>

                                    <div class="progress-section">
                                        <div class="progress-label">
                                            <span>Progress</span>
                                            <span class="fw-bold">{{ $progressPercentage }}%</span>
                                        </div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>

                                    <div class="course-stats">
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $course->lessons->count() }}</span>
                                            <div class="stat-label">Lessons</div>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $course->tests->count() }}</span>
                                            <div class="stat-label">Tests</div>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $completedLessons }}</span>
                                            <div class="stat-label">Completed</div>
                                        </div>
                                    </div>

                                    <div class="course-actions">
                                        <a href="{{ route('student.course.show', $course) }}" class="btn btn-continue">
                                            <i class="bi bi-play-fill me-2"></i>
                                            {{ $progressPercentage > 0 ? 'Continue' : 'Start' }}
                                        </a>
                                        <button class="btn btn-outline" onclick="showCourseDetails(this)"
                                            data-course-id="{{ $course->id }}"
                                            data-title="{{ e($course->title) }}"
                                            data-description="{{ e($course->description ?? 'No description provided.') }}"
                                            data-teacher="{{ e($course->teacher->name ?? 'Instructor') }}"
                                            data-level="{{ ucfirst($course->level ?? 'General') }}"
                                            data-lessons="{{ $course->lessons->count() }}"
                                            data-tests="{{ $course->tests->count() }}"
                                            data-completed="{{ $completedLessons }}"
                                            data-progress="{{ $progressPercentage }}"
                                            data-url="{{ route('student.course.show', $course) }}"
                                            title="Course Details">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $enrollments->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-book empty-icon"></i>
                    <h3>No Courses Yet</h3>
                    <p class="text-muted mb-4">Start your learning journey by enrolling in a course!</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-search me-2"></i>
                        Browse Courses
                    </a>
                </div>
            @endif
        @endif

        <!-- Evaluated Tests Section -->
        @if(isset($evaluatedTests) && $evaluatedTests->count() > 0)
            <div class="mt-5 mb-4">
                <h3 class="mb-3">Recent Evaluations</h3>
                <div class="row">
                    @foreach($evaluatedTests as $submission)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span
                                            class="badge {{ $submission->passed ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $submission->passed ? 'text-success' : 'text-danger' }}">
                                            {{ $submission->passed ? 'Passed' : 'Failed' }}
                                        </span>
                                        <small class="text-muted">{{ $submission->updated_at->format('M d, Y') }}</small>
                                    </div>
                                    <h5 class="card-title fw-bold mb-1">{{ $submission->test->title ?? 'Unknown Test' }}</h5>
                                    <p class="text-muted small mb-3">{{ $submission->test->course->title ?? 'Unknown Course' }}</p>

                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <div class="text-muted small">Score</div>
                                            <div class="fs-4 fw-bold {{ $submission->passed ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($submission->score, 1) }}%
                                            </div>
                                        </div>
                                        <a href="{{ route('student.test.results', ['test' => $submission->test_id, 'attempt' => $submission->attempt_id]) }}"
                                            class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            View Review <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Course Details Modal -->
    <div class="modal fade" id="courseDetailsModal" tabindex="-1" aria-labelledby="courseDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem;">
                    <div>
                        <span class="badge bg-white text-dark mb-2 px-2 py-1" id="modalCourseLevel">Level</span>
                        <h5 class="modal-title fw-bold text-white mb-1" id="courseDetailsModalLabel">Course Title</h5>
                        <p class="mb-0 opacity-75 small text-white" id="modalCourseTeacher"><i class="bi bi-person me-1"></i>Instructor</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold mb-2">About this Course</h6>
                    <p class="text-muted small mb-4" id="modalCourseDesc" style="white-space: pre-line;"></p>
                    
                    <div class="row g-3 text-center mb-4">
                        <div class="col-4">
                            <div class="p-3 rounded bg-light">
                                <h5 class="fw-bold mb-0 text-primary" id="modalCourseLessons">0</h5>
                                <small class="text-muted">Lessons</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded bg-light">
                                <h5 class="fw-bold mb-0 text-primary" id="modalCourseTests">0</h5>
                                <small class="text-muted">Tests</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded bg-light">
                                <h5 class="fw-bold mb-0 text-success" id="modalCourseProgress">0%</h5>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" id="modalCourseProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted d-block text-end" id="modalCourseProgressText">0 of 0 lessons completed</small>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="modalCourseGoLink" class="btn btn-primary rounded-pill px-4">
                        Go to Course <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Filter functionality
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    // Update active tab
                    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Filter courses
                    const filter = this.dataset.filter;
                    const courses = document.querySelectorAll('.course-item');

                    courses.forEach(course => {
                        if (filter === 'all' || course.dataset.status === filter) {
                            course.style.display = 'block';
                        } else {
                            course.style.display = 'none';
                        }
                    });
                });
            });

            // Course details modal
            function showCourseDetails(btnOrId) {
                let btn = btnOrId;
                if (typeof btnOrId === 'number' || typeof btnOrId === 'string') {
                    btn = document.querySelector(`button[data-course-id="${btnOrId}"]`);
                }
                if (!btn || !btn.dataset) return;

                const title = btn.dataset.title || 'Course Details';
                const desc = btn.dataset.description || 'No description provided.';
                const teacher = btn.dataset.teacher || 'Instructor';
                const level = btn.dataset.level || 'General';
                const lessons = btn.dataset.lessons || '0';
                const tests = btn.dataset.tests || '0';
                const completed = btn.dataset.completed || '0';
                const progress = btn.dataset.progress || '0';
                const url = btn.dataset.url || '#';

                document.getElementById('courseDetailsModalLabel').textContent = title;
                document.getElementById('modalCourseLevel').textContent = level;
                document.getElementById('modalCourseTeacher').innerHTML = `<i class="bi bi-person me-1"></i>${teacher}`;
                document.getElementById('modalCourseDesc').textContent = desc;
                document.getElementById('modalCourseLessons').textContent = lessons;
                document.getElementById('modalCourseTests').textContent = tests;
                document.getElementById('modalCourseProgress').textContent = progress + '%';
                document.getElementById('modalCourseProgressBar').style.width = progress + '%';
                document.getElementById('modalCourseProgressText').textContent = `${completed} of ${lessons} lessons completed`;
                document.getElementById('modalCourseGoLink').href = url;

                const modal = new bootstrap.Modal(document.getElementById('courseDetailsModal'));
                modal.show();
            }
        </script>
    @endpush
@endsection