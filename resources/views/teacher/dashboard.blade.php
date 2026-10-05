@extends('layouts.app')

@section('title', 'Teacher Dashboard - TS Language School')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Welcome back, {{ $user->name }}</h2>
            <p class="text-muted">Instructor Portal & Course Overview</p>
        </div>
        <div>
            <a href="{{ route('teacher.courses') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-book me-2"></i>My Courses
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-book fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Assigned Courses</h6>
                        <h3 class="fw-bold mb-0">{{ $courses->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Active Students</h6>
                        <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle me-3">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Instructor Status</h6>
                        <h4 class="fw-bold mb-0 text-capitalize text-success">{{ $user->status }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses List -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-4">My Assigned Courses</h5>
        @if($courses->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted"></i>
                <p class="text-muted mt-2">No courses assigned yet. Contact administrator to assign courses.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Name</th>
                            <th>Level</th>
                            <th>Lessons</th>
                            <th>Enrolled</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td class="fw-semibold">{{ $course->title }}</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $course->level }}</span></td>
                                <td>{{ $course->lessons_count }} lessons</td>
                                <td>{{ $course->enrollments_count }} students</td>
                                <td>
                                    <span class="badge {{ $course->status === 'published' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
