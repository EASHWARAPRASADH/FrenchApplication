@extends('layouts.app')

@section('title', 'My Students - TS Language School')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Students</h2>
            <p class="text-muted">Students actively enrolled in your courses</p>
        </div>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        @if($students->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <p class="text-muted mt-2">No students enrolled in your courses yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Language Level</th>
                            <th>Enrolled Course(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        {{ $student->name }}
                                    </div>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td><span class="badge bg-info text-capitalize">{{ str_replace('_', ' ', $student->language_level) }}</span></td>
                                <td>
                                    @foreach($student->enrollments as $enrollment)
                                        @if($enrollment->course && $enrollment->course->teacher_id == Auth::id())
                                            <span class="badge bg-light text-dark border me-1">{{ $enrollment->course->title }}</span>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
