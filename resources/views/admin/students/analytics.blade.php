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
        <div class="row mb-3">
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Average Score</h6>
                        <h2 class="mb-0 text-primary">{{ $metrics['avg_score'] }}%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Tests Completed</h6>
                        <h2 class="mb-0 text-success">{{ $metrics['tests_completed'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Study Time</h6>
                        <h2 class="mb-0 text-info">{{ $metrics['total_time'] }}h</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Current Streak</h6>
                        <h2 class="mb-0 text-warning">{{ $metrics['current_streak'] }}</h2>
                        <small class="text-muted">days</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Pass Rate</h6>
                        <h2 class="mb-0 text-success">{{ $metrics['pass_rate'] }}%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Improvement</h6>
                        <h2 class="mb-0 {{ $metrics['improvement'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $metrics['improvement'] > 0 ? '+' : '' }}{{ $metrics['improvement'] }}%
                        </h2>
                        <small class="text-muted">vs previous</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Score Progression Chart --}}
        <div class="card mb-3">
            <div class="card-header">
                <strong><i class="bi bi-graph-up me-2"></i>Score Progression</strong>
            </div>
            <div class="card-body">
                <canvas id="scoreChart" height="80"></canvas>
            </div>
        </div>

        {{-- Test History Table --}}
        <div class="card">
            <div class="card-header">
                <strong><i class="bi bi-list-check me-2"></i>Test History</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th>Course</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Time Spent</th>
                                <th>Attempt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($testHistory as $attempt)
                                <tr>
                                    <td>{{ $attempt->test->title }}</td>
                                    <td>{{ $attempt->test->course->title ?? 'N/A' }}</td>
                                    <td>{{ $attempt->completed_at->format('M d, Y') }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $attempt->score >= 70 ? 'bg-success' : ($attempt->score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $attempt->score }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($attempt->passed)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Passed</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>
                                        @endif
                                    </td>
                                    <td>{{ gmdate('H:i:s', $attempt->time_taken) }}</td>
                                    <td>{{ $attempt->attempt_number }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No test attempts found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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