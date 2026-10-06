@extends('layouts.admin')

@section('title', 'Test Submissions - Admin Panel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Test Submissions</h1>
            <p class="text-muted">Review and grade test submissions</p>
        </div>
        <div>
            <a href="#" class="btn btn-outline-primary me-2" onclick="exportResults()">
                <i class="bi bi-download me-2"></i>Export Results
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter by Course</label>
                    <select class="form-select" id="courseFilter" onchange="filterSubmissions()">
                        <option value="">All Courses</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ optional($course)->id }}">{{ optional($course)->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter by Test</label>
                    <select class="form-select" id="testFilter" onchange="filterSubmissions()">
                        <option value="">All Tests</option>
                        @foreach($tests ?? [] as $test)
                            <option value="{{ optional($test)->id }}">{{ optional($test)->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter by Status</label>
                    <select class="form-select" id="statusFilter" onchange="filterSubmissions()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Evaluation
                        </option>
                        <option value="passed" {{ request('status') === 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search Student</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name or email"
                        onkeyup="filterSubmissions()">
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    @if($submissions->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Test</th>
                            <th>Course</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Time Taken</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if(optional($submission->student)->avatar_url)
                                            <img src="{{ $submission->student->avatar_url }}" alt="{{ $submission->student->name }}"
                                                class="rounded-circle me-2" width="32" height="32">
                                        @else
                                            <div class="rounded-circle me-2 bg-secondary d-flex align-items-center justify-content-center text-white"
                                                style="width: 32px; height: 32px;">
                                                {{ substr(optional($submission->student)->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-500">{{ optional($submission->student)->name ?? 'Unknown Student' }}
                                            </div>
                                            <small
                                                class="text-muted">{{ optional($submission->student)->email ?? 'No Email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($submission->test)->title ?? 'Test Deleted' }}</td>
                                <td>{{ optional(optional($submission->test)->course)->title ?? 'Course N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-600">{{ number_format($submission->score, 2) }}%</span>
                                        <div class="progress ms-2" style="width: 60px; height: 6px;">
                                            <div class="progress-bar {{ $submission->score >= ($submission->test->passing_score ?? 50) ? 'bg-success' : 'bg-danger' }}"
                                                style="width: {{ $submission->score }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(($submission->status ?? 'completed') === 'pending')
                                        <span class="badge bg-warning text-dark">Pending Review</span>
                                    @elseif($submission->passed)
                                        <span class="badge bg-success">Passed</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $submission->submitted_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td>
                                    <small>{{ $submission->time_taken ? gmdate('H:i:s', $submission->time_taken) : 'N/A' }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.test-submissions.show', $submission->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $submissions->links() }}
        </div>
    @else
        <div class="card text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h4 class="mt-3">No Test Submissions</h4>
            <p class="text-muted">No test submissions found. Students will appear here once they submit tests.</p>
        </div>
    @endif

    <style>
        .fw-500 {
            font-weight: 500;
        }

        .fw-600 {
            font-weight: 600;
        }
    </style>

    <script>
        function filterSubmissions() {
            const course = document.getElementById('courseFilter').value;
            const test = document.getElementById('testFilter').value;
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchInput').value;

            // Convert current URL parameters to object
            const url = new URL(window.location.href);
            if (course) url.searchParams.set('course', course); else url.searchParams.delete('course');
            if (test) url.searchParams.set('test', test); else url.searchParams.delete('test');
            if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');

            window.location.href = url.toString();
        }

        function exportResults() {
            const url = new URL("{{ route('admin.test-submissions.export') }}", window.location.origin);
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.forEach((value, key) => url.searchParams.set(key, value));
            window.location.href = url.toString();
        }
    </script>
@endsection