@extends('layouts.app')

@section('title', 'My Assigned Courses - TS Language School')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Assigned Courses</h2>
            <p class="text-muted">Courses you are currently instructing</p>
        </div>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        @if($courses->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-book fs-1 text-muted"></i>
                <p class="text-muted mt-2">No courses assigned yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Level</th>
                            <th>Lessons</th>
                            <th>Students</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td class="fw-semibold">{{ $course->title }}</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($course->level) }}</span></td>
                                <td>{{ $course->lessons_count }}</td>
                                <td>{{ $course->enrollments_count }}</td>
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
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
