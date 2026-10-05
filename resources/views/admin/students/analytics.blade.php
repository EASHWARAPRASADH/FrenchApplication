@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Student Analytics - ' . $student->name)

@section('content')
    <div class="container-fluid py-3">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Student Analytics</h3>
                <div class="text-muted small">{{ $student->name }} ({{ $student->email }})</div>
            </div>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Users
                </a>
            @endif
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ request()->url() }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4">
                        <label for="course_id" class="form-label">Course</label>
                        <select class="form-select" id="course_id" name="course_id">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Performance Overview Cards --}}
        <div class="row g-3 mb-3 row-cols-2 row-cols-md-3 row-cols-lg-5">
            <div class="col">
                <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-center p-3">
                        <h6 class="text-muted mb-2">Average Score</h6>
                        <h2 class="mb-0 text-primary fw-bold">{{ $metrics['avg_score'] }}%</h2>
                        @if(!empty($metrics['pending_evaluations']) && $metrics['pending_evaluations'] > 0)
                            <small class="text-muted mt-1">{{ $metrics['graded_count'] }} graded tests</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-center p-3">
                        <h6 class="text-muted mb-2">Tests Completed</h6>
                        <h2 class="mb-0 text-success fw-bold">{{ $metrics['tests_completed'] }}</h2>
                        @if(!empty($metrics['pending_evaluations']) && $metrics['pending_evaluations'] > 0)
                            <small class="text-warning mt-1"><i class="bi bi-clock me-1"></i>{{ $metrics['pending_evaluations'] }} pending review</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-center p-3">
                        <h6 class="text-muted mb-2">Study Time</h6>
                        <h2 class="mb-0 text-info fw-bold">{{ $metrics['total_time'] }}h</h2>
                        <small class="text-muted mt-1">Total time spent</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-center p-3">
                        <h6 class="text-muted mb-2">Pass Rate</h6>
                        <h2 class="mb-0 text-success fw-bold">{{ $metrics['pass_rate'] }}%</h2>
                        <small class="text-muted mt-1">Graded tests</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-center p-3">
                        <h6 class="text-muted mb-2">Improvement</h6>
                        <h2 class="mb-0 fw-bold {{ $metrics['improvement'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $metrics['improvement'] > 0 ? '+' : '' }}{{ $metrics['improvement'] }}%
                        </h2>
                        <small class="text-muted mt-1">vs previous</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Score Progression Chart --}}
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <strong><i class="bi bi-graph-up me-2"></i>Score Progression</strong>
            </div>
            <div class="card-body">
                <canvas id="scoreChart" height="80"></canvas>
            </div>
        </div>

        {{-- Test History Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-list-check me-2"></i>Test History</strong>
                <span class="badge bg-secondary">{{ $testHistory->total() }} Total Attempts</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Test Name</th>
                                <th>Course</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Time Spent</th>
                                <th>Attempt</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($testHistory as $attempt)
                                <tr>
                                    <td>
                                        <strong>{{ $attempt->test->title }}</strong>
                                    </td>
                                    <td>{{ $attempt->test->course->title ?? 'N/A' }}</td>
                                    <td>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @if(!empty($attempt->is_pending_evaluation))
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pending
                                            </span>
                                        @else
                                            <span class="badge {{ $attempt->score >= 70 ? 'bg-success' : ($attempt->score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($attempt->score, 2) }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($attempt->is_pending_evaluation))
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split me-1"></i>Under Review
                                            </span>
                                        @elseif($attempt->passed)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Passed</span>
                                        @else
                                            @php
                                                $passingScore = round($attempt->test->passing_score ?? 70);
                                            @endphp
                                            <span class="badge bg-danger" title="Required passing score: {{ $passingScore }}%">
                                                <i class="bi bi-x-circle me-1"></i>Failed
                                                @if($passingScore > 70)
                                                    <small class="text-white-50">({{ $passingScore }}%)</small>
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ gmdate('H:i:s', $attempt->time_taken) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $attempt->attempt_number }}</span></td>
                                    <td class="text-end pe-3">
                                        @if(auth()->user()->role === 'admin' && !empty($attempt->submission_id))
                                            <a href="{{ route('admin.test-submissions.show', $attempt->submission_id) }}" class="btn btn-sm btn-outline-primary" title="Review submission">
                                                <i class="bi bi-pencil-square me-1"></i>Review
                                            </a>
                                        @else
                                            <a href="{{ route('student.test.results', ['test' => $attempt->test_id, 'attempt' => $attempt->id]) }}" class="btn btn-sm btn-outline-secondary" title="View details">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox display-4 d-block mb-2 text-muted"></i>
                                        No test attempts found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($testHistory->hasPages())
                <div class="card-footer bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Showing {{ $testHistory->firstItem() ?? 0 }} to {{ $testHistory->lastItem() ?? 0 }} of {{ $testHistory->total() }} attempts
                        </small>
                        <div>
                            {{ $testHistory->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Fetch chart data and render
            const chartUrl = '{{ auth()->user()->role === "admin" ? route("admin.students.analytics.chart-data", $student) : route("student.analytics.chart-data") }}';
            const urlParams = new URLSearchParams(window.location.search);

            fetch(chartUrl + '?' + urlParams.toString())
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('scoreChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Test Scores (%)',
                                data: data.scores,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'Score (%)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Test Date'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: function (context) {
                                            return data.testNames[context[0].dataIndex];
                                        },
                                        label: function (context) {
                                            return 'Score: ' + context.parsed.y + '%';
                                        }
                                    }
                                },
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                    document.getElementById('scoreChart').parentElement.innerHTML =
                        '<div class="alert alert-warning">Unable to load chart data</div>';
                });
        </script>
    @endpush
@endsection