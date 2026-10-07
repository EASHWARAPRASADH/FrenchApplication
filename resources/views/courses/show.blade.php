@extends('layouts.app')

@section('title', $course->title . ' - TS Language Platform')

@section('content')
<!-- Course Header - Minimalistic -->
<section class="course-show-hero">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="breadcrumb-minimal">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                        <li class="breadcrumb-item active">{{ $course->title }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="course-badges-minimal">
                    <span class="level-badge-minimal">{{ ucfirst($course->level) }}</span>
                    @if($course->price == 0)
                        <span class="price-badge-minimal">Free</span>
                    @else
                        <span class="price-badge-minimal">${{ number_format($course->price, 2) }}</span>
                    @endif
                </div>

                <h1 class="course-show-title">{{ $course->title }}</h1>
                <p class="course-show-description">{{ $course->description }}</p>

                <div class="course-show-stats">
                    <div class="stat-box-minimal">
                        <i class="bi bi-clock"></i>
                        <div class="stat-content">
                            <span class="stat-value">{{ $course->duration_hours }}h</span>
                            <span class="stat-label">Duration</span>
                        </div>
                    </div>
                    <div class="stat-box-minimal">
                        <i class="bi bi-play-circle"></i>
                        <div class="stat-content">
                            <span class="stat-value">{{ $course->total_lessons }}</span>
                            <span class="stat-label">Lessons</span>
                        </div>
                    </div>
                    <div class="stat-box-minimal">
                        <i class="bi bi-people"></i>
                        <div class="stat-content">
                            <span class="stat-value">{{ $course->total_enrollments }}</span>
                            <span class="stat-label">Students</span>
                        </div>
                    </div>
                    <div class="stat-box-minimal">
                        <i class="bi bi-star"></i>
                        <div class="stat-content">
                            <span class="stat-value">
                                @if($course->average_rating > 0)
                                    {{ number_format($course->average_rating, 1) }}
                                @else
                                    New
                                @endif
                            </span>
                            <span class="stat-label">Rating</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="course-enroll-card">
                    <div class="teacher-info-minimal">
                        <i class="bi bi-person-circle"></i>
                        <div>
                            <span class="teacher-label">Instructor</span>
                            <span class="teacher-name">{{ $course->teacher?->name ?? 'Instructor' }}</span>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'student')
                            @php
                                $isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->exists();
                            @endphp
                            @if($isEnrolled)
                                <a href="{{ route('student.course.show', $course) }}" class="btn-enroll-minimal d-block text-center text-decoration-none">
                                    Continue Learning
                                </a>
                                <p class="enroll-note">You are enrolled in this course</p>
                            @else
                                <form action="{{ route('student.course.enroll', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-enroll-minimal w-100">
                                        Enroll Now
                                    </button>
                                </form>
                                <p class="enroll-note">Start learning immediately</p>
                            @endif
                        @else
                            <p class="preview-note">Course preview available</p>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn-enroll-minimal mb-3">
                            Sign Up to Enroll
                        </a>
                        <a href="{{ route('login') }}" class="btn-signin-minimal">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Content -->
<section class="course-content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Course Description -->
                <div class="content-card-minimal">
                    <h4 class="content-card-title">
                        <i class="bi bi-info-circle"></i>
                        About This Course
                    </h4>
                    <div class="content-card-body">
                        <p class="course-detail-text">{{ $course->description }}</p>

                        <h6 class="learning-title">What You'll Learn:</h6>
                        <ul class="learning-list">
                            <li><i class="bi bi-check-circle"></i>Essential French vocabulary and phrases</li>
                            <li><i class="bi bi-check-circle"></i>Proper pronunciation and accent training</li>
                            <li><i class="bi bi-check-circle"></i>Grammar fundamentals and sentence structure</li>
                            <li><i class="bi bi-check-circle"></i>Conversational skills for real-world situations</li>
                            <li><i class="bi bi-check-circle"></i>Cultural insights and context</li>
                        </ul>
                    </div>
                </div>

                <!-- Course Curriculum -->
                <div class="content-card-minimal">
                    <h4 class="content-card-title">
                        <i class="bi bi-list-ul"></i>
                        Course Curriculum
                    </h4>
                    <div class="content-card-body">
                        @if($course->lessons->count() > 0)
                            <div class="accordion" id="curriculumAccordion">
                                @foreach($course->lessons->groupBy('folder_id') as $folderId => $lessons)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#folder{{ $folderId ?? 'main' }}">
                                                <i class="bi bi-folder me-2"></i>
                                                {{ $folderId ? 'Module ' . $folderId : 'Main Lessons' }}
                                                <span class="badge bg-primary ms-2">{{ $lessons->count() }} lessons</span>
                                            </button>
                                        </h2>
                                        <div id="folder{{ $folderId ?? 'main' }}" class="accordion-collapse collapse" data-bs-parent="#curriculumAccordion">
                                            <div class="accordion-body">
                                                @foreach($lessons as $lesson)
                                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                        <div>
                                                            <i class="bi bi-play-circle me-2 text-primary"></i>
                                                            <strong>{{ $lesson->title }}</strong>
                                                            @if($lesson->description)
                                                                <p class="text-muted small mb-0 ms-4">{{ Str::limit($lesson->description, 100) }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted">{{ $lesson->estimated_duration }} min</small>
                                                            @auth
                                                                @if(auth()->user()->role === 'student')
                                                                    <br><small class="badge bg-secondary">Preview</small>
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-book text-muted fs-1 mb-3"></i>
                                <p class="text-muted">Course curriculum is being prepared. Check back soon!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Instructor Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>
                            Your Instructor
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($course->teacher?->name ?? 'Instructor') }}&color=7F9CF5&background=EBF4FF&size=100" 
                             alt="{{ $course->teacher?->name ?? 'Instructor' }}" class="rounded-circle mb-3" width="100" height="100">
                        <h6 class="fw-bold">{{ $course->teacher?->name ?? 'Instructor' }}</h6>
                        @if($course->teacher?->bio)
                            <p class="text-muted small">{{ Str::limit($course->teacher->bio, 150) }}</p>
                        @endif
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <strong class="d-block">{{ $course->teacher->role === 'admin' ? 'Platform' : 'Expert' }}</strong>
                                <small class="text-muted">Instructor</small>
                            </div>
                            <div class="col-6">
                                <strong class="d-block">{{ $course->teacher->created_at->diffForHumans() }}</strong>
                                <small class="text-muted">Teaching Since</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-star me-2"></i>
                            Course Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Interactive lessons
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Progress tracking
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Quizzes and tests
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Mobile friendly
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Lifetime access
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Certificate of completion
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
:root {
    --black: #000000;
    --white: #ffffff;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #6c757d;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
}

/* Hero Section */
.course-show-hero {
    background: var(--white);
    padding: 3rem 0 4rem;
    border-bottom: 1px solid var(--gray-200);
}

.breadcrumb-minimal {
    margin-bottom: 2rem;
}

.breadcrumb-minimal .breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.breadcrumb-minimal .breadcrumb-item {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.breadcrumb-minimal .breadcrumb-item a {
    color: var(--gray-600);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-minimal .breadcrumb-item a:hover {
    color: var(--black);
}

.breadcrumb-minimal .breadcrumb-item.active {
    color: var(--black);
}

.course-badges-minimal {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.level-badge-minimal,
.price-badge-minimal {
    padding: 0.5rem 1.25rem;
    background: var(--gray-100);
    color: var(--gray-700);
    font-size: 0.75rem;
    font-weight: 400;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    border: 1px solid var(--gray-300);
}

.course-show-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 400;
    letter-spacing: -0.02em;
    line-height: 1.2;
    color: var(--black);
    margin-bottom: 1.5rem;
}

.course-show-description {
    font-size: 1.125rem;
    line-height: 1.7;
    color: var(--gray-600);
    margin-bottom: 3rem;
    font-weight: 300;
}

.course-show-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-box-minimal {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.stat-box-minimal:hover {
    border-color: var(--black);
    transform: translateY(-2px);
}

.stat-box-minimal i {
    font-size: 2rem;
    color: var(--gray-600);
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 400;
    color: var(--black);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Enroll Card */
.course-enroll-card {
    background: var(--white);
    border: 1px solid var(--gray-300);
    padding: 2.5rem;
    position: sticky;
    top: 100px;
}

.teacher-info-minimal {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--gray-200);
}

.teacher-info-minimal i {
    font-size: 3rem;
    color: var(--gray-400);
}

.teacher-info-minimal div {
    display: flex;
    flex-direction: column;
}

.teacher-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 0.25rem;
}

.teacher-name {
    font-size: 1.125rem;
    font-weight: 400;
    color: var(--black);
}

.btn-enroll-minimal {
    display: block;
    width: 100%;
    padding: 1.125rem;
    background: var(--black);
    color: var(--white);
    text-align: center;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 400;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    transition: all 0.4s ease;
    border: 1px solid var(--black);
    cursor: pointer;
}

.btn-enroll-minimal:hover {
    background: var(--white);
    color: var(--black);
}

.btn-signin-minimal {
    display: block;
    width: 100%;
    padding: 1.125rem;
    background: var(--white);
    color: var(--black);
    text-align: center;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 400;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    transition: all 0.4s ease;
    border: 1px solid var(--gray-300);
}

.btn-signin-minimal:hover {
    border-color: var(--black);
}

.enroll-note,
.preview-note {
    font-size: 0.875rem;
    color: var(--gray-600);
    text-align: center;
    margin-top: 1rem;
    margin-bottom: 0;
}

/* Content Section */
.course-content-section {
    background: var(--gray-100);
    padding: 4rem 0;
}

.content-card-minimal {
    background: var(--white);
    border: 1px solid var(--gray-300);
    padding: 2.5rem;
    margin-bottom: 2rem;
}

.content-card-title {
    font-size: 1.5rem;
    font-weight: 400;
    color: var(--black);
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.content-card-title i {
    font-size: 1.25rem;
    color: var(--gray-600);
}

.content-card-body {
    color: var(--gray-700);
}

.course-detail-text {
    font-size: 1rem;
    line-height: 1.7;
    color: var(--gray-600);
    font-weight: 300;
}

.learning-title {
    font-size: 1.125rem;
    font-weight: 400;
    color: var(--black);
    margin: 2rem 0 1.5rem;
}

.learning-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.learning-list li {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.95rem;
    color: var(--gray-700);
}

.learning-list li:last-child {
    border-bottom: none;
}

.learning-list li i {
    color: var(--black);
    font-size: 1.125rem;
}

/* Accordion Styling */
.accordion-item {
    border: 1px solid var(--gray-300);
    margin-bottom: 0.5rem;
}

.accordion-button {
    background: var(--white);
    color: var(--black);
    font-weight: 400;
    padding: 1.25rem;
}

.accordion-button:not(.collapsed) {
    background: var(--gray-100);
    color: var(--black);
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: var(--gray-300);
}

.accordion-body {
    padding: 1.5rem;
}

/* Responsive */
@media (max-width: 992px) {
    .course-enroll-card {
        position: relative;
        top: 0;
        margin-bottom: 2rem;
    }

    .course-show-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .course-show-hero {
        padding: 2rem 0 3rem;
    }

    .course-show-stats {
        grid-template-columns: 1fr;
    }

    .content-card-minimal {
        padding: 1.5rem;
    }

    .course-enroll-card {
        padding: 2rem;
    }
}
</style>
@endpush

@endsection
