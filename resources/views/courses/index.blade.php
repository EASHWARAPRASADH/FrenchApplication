@extends('layouts.app')

@section('title', 'French Courses - TS Language Platform')

@section('content')
<!-- Hero Section - Minimalistic -->
<section class="courses-hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-12 text-center">
                <div class="courses-hero-brand">All Courses</div>
                <h1 class="courses-hero-title">
                    Explore Our French<br>Language Courses
                </h1>
                <p class="courses-hero-subtitle">
                    {{ $courses->total() }} courses designed for all skill levels
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filters Section -->
<section class="courses-filter-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="filter-wrapper">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select class="filter-select" id="levelFilter">
                                <option value="">All Levels</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="filter-select" id="priceFilter">
                                <option value="">All Prices</option>
                                <option value="free">Free</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Courses Grid -->
<section class="courses-grid-section">
    <div class="container">
        @if($courses->count() > 0)
            <div class="row g-4" id="coursesGrid">
                @foreach($courses as $index => $course)
                    <div class="col-lg-4 col-md-6 course-card"
                         data-level="{{ $course->level }}"
                         data-price="{{ $course->price == 0 ? 'free' : 'paid' }}">
                        <div class="course-card-simple">
                            <div class="course-header-simple">
                                <div class="course-level-badge">{{ ucfirst($course->level) }}</div>
                                @if($course->price == 0)
                                    <span class="course-price-badge">Free</span>
                                @else
                                    <span class="course-price-badge">${{ number_format($course->price, 2) }}</span>
                                @endif
                            </div>

                            <h5 class="course-title-simple">{{ $course->title }}</h5>
                            <p class="course-description-simple">{{ Str::limit($course->description, 120) }}</p>

                            <div class="course-stats-grid">
                                <div class="course-stat-item">
                                    <span class="stat-label">Duration</span>
                                    <span class="stat-value">{{ $course->duration_hours }}h</span>
                                </div>
                                <div class="course-stat-item">
                                    <span class="stat-label">Lessons</span>
                                    <span class="stat-value">{{ $course->total_lessons }}</span>
                                </div>
                                <div class="course-stat-item">
                                    <span class="stat-label">Students</span>
                                    <span class="stat-value">{{ $course->total_enrollments }}</span>
                                </div>
                            </div>

                            <div class="course-meta-simple">
                                <div class="course-meta-item">
                                    <i class="bi bi-person"></i>
                                    <span>{{ $course->teacher?->name ?? 'Instructor' }}</span>
                                </div>
                                @if($course->average_rating > 0)
                                    <div class="course-meta-item">
                                        <i class="bi bi-star-fill"></i>
                                        <span>{{ number_format($course->average_rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('courses.show', $course) }}" class="course-btn-simple">
                                View Course
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $courses->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-book"></i>
                <h3>No courses found</h3>
                <p>Check back later for new courses or adjust your filters.</p>
            </div>
        @endif
    </div>
</section>



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelFilter = document.getElementById('levelFilter');
    const priceFilter = document.getElementById('priceFilter');
    const courseCards = document.querySelectorAll('.course-card');

    function filterCourses() {
        const selectedLevel = levelFilter.value;
        const selectedPrice = priceFilter.value;

        courseCards.forEach(card => {
            const cardLevel = card.dataset.level;
            const cardPrice = card.dataset.price;

            const levelMatch = !selectedLevel || cardLevel === selectedLevel;
            const priceMatch = !selectedPrice || cardPrice === selectedPrice;

            if (levelMatch && priceMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    levelFilter.addEventListener('change', filterCourses);
    priceFilter.addEventListener('change', filterCourses);
});
</script>
@endpush

@push('styles')
<style>
.course-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.course-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.badge {
    font-size: 0.75rem;
}

/* ===== COURSES PAGE STYLES ===== */
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
.courses-hero-section {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: 5rem 0 3rem;
}

.min-vh-50 {
    min-height: 50vh;
}

.courses-hero-brand {
    font-size: 0.875rem;
    font-weight: 400;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--gray-600);
    margin-bottom: 1.5rem;
}

.courses-hero-title {
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 300;
    letter-spacing: -0.02em;
    line-height: 1.2;
    color: var(--black);
    margin: 0 0 1.5rem 0;
}

.courses-hero-subtitle {
    font-size: 1.125rem;
    font-weight: 300;
    color: var(--gray-600);
    margin: 0;
}

/* Filter Section */
.courses-filter-section {
    background: var(--gray-100);
    padding: 2rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.filter-wrapper {
    background: var(--white);
    padding: 1.5rem;
    border: 1px solid var(--gray-300);
}

.filter-select {
    width: 100%;
    padding: 0.875rem 1.25rem;
    border: 1px solid var(--gray-300);
    background: var(--white);
    color: var(--gray-700);
    font-size: 0.95rem;
    font-weight: 300;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: var(--black);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

/* Courses Grid */
.courses-grid-section {
    background: var(--gray-100);
    padding: 4rem 0;
}

/* Simple Course Card - No Images */
.course-card-simple {
    background: var(--white);
    border: 1px solid var(--gray-300);
    padding: 2.5rem;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.course-card-simple::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--black);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.course-card-simple:hover {
    border-color: var(--black);
    transform: translateY(-8px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
}

.course-card-simple:hover::before {
    transform: scaleX(1);
}

.course-header-simple {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.course-price-badge {
    font-size: 0.75rem;
    font-weight: 400;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gray-600);
    padding: 0.5rem 1rem;
    background: var(--gray-100);
}

.course-title-simple {
    font-size: 1.75rem;
    font-weight: 400;
    letter-spacing: -0.02em;
    color: var(--black);
    margin-bottom: 1.25rem;
    line-height: 1.3;
}

.course-description-simple {
    font-size: 1rem;
    line-height: 1.7;
    color: var(--gray-600);
    margin-bottom: 2rem;
    flex-grow: 1;
    font-weight: 300;
}

.course-level-badge {
    display: inline-block;
    padding: 0.4rem 1rem;
    background: var(--gray-100);
    color: var(--gray-700);
    font-size: 0.7rem;
    font-weight: 400;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 1.25rem;
    width: fit-content;
}

.course-title-new {
    font-size: 1.5rem;
    font-weight: 400;
    letter-spacing: -0.02em;
    color: var(--black);
    margin-bottom: 1rem;
    line-height: 1.3;
}

.course-description-new {
    font-size: 0.95rem;
    line-height: 1.7;
    color: var(--gray-600);
    margin-bottom: 1.5rem;
    flex-grow: 1;
    font-weight: 300;
}

.course-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.course-stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 400;
    color: var(--black);
}

.course-meta-simple {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.course-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 300;
}

.course-meta-item i {
    font-size: 1rem;
    color: var(--gray-500);
}

.course-btn-simple {
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
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--black);
    position: relative;
    overflow: hidden;
}

.course-btn-simple::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--white);
    transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 0;
}

.course-btn-simple:hover::before {
    left: 0;
}

.course-btn-simple:hover {
    color: var(--black);
}

.course-btn-simple span {
    position: relative;
    z-index: 1;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 4rem;
}

.pagination-wrapper .pagination {
    gap: 0.5rem;
}

.pagination-wrapper .page-link {
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
    padding: 0.75rem 1.25rem;
    font-weight: 300;
    transition: all 0.3s ease;
}

.pagination-wrapper .page-link:hover {
    color: var(--black);
    background: var(--gray-100);
    border-color: var(--black);
}

.pagination-wrapper .page-item.active .page-link {
    background: var(--black);
    border-color: var(--black);
    color: var(--white);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 5rem 0;
}

.empty-state i {
    font-size: 5rem;
    color: var(--gray-300);
    margin-bottom: 2rem;
}

.empty-state h3 {
    font-size: 1.75rem;
    font-weight: 300;
    color: var(--gray-700);
    margin-bottom: 1rem;
}

.empty-state p {
    font-size: 1rem;
    color: var(--gray-600);
    font-weight: 300;
}

/* Responsive */
@media (max-width: 768px) {
    .courses-hero-section {
        padding: 3rem 0 2rem;
    }

    .course-card-simple {
        padding: 2rem;
    }

    .course-stats-grid {
        gap: 0.75rem;
    }

    .course-title-simple {
        font-size: 1.5rem;
    }
}
</style>
@endpush
@endsection
