@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Student Analytics - ' . $student->name)

@section('content')
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h3 class="mb-1 fw-bold text-dark">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Student Analytics & Study Tracker
                </h3>
                <div class="text-muted">
                    {{ $student->name }} <span class="mx-1">•</span> <span class="badge bg-light text-secondary border">{{ $student->email }}</span>
                    @if($student->level)
                        <span class="badge bg-info text-white ms-1">Level {{ $student->level }}</span>
                    @endif
                    @if($student->points)
                        <span class="badge bg-warning text-dark ms-1"><i class="bi bi-star-fill me-1"></i>{{ $student->points }} pts</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'student')
                    <a href="{{ route('student.status.index') }}" class="btn btn-outline-primary shadow-sm">
                        <i class="bi bi-journal-plus me-1"></i>Status Tracker
                    </a>
                @endif
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to Users
                    </a>
                @endif
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                {{-- Quick Presets & Filter Mode Tabs --}}
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3 pb-3 border-bottom">
                    {{-- Mode Selector Buttons --}}
                    <div>
                        <span class="small text-muted fw-bold text-uppercase d-block mb-1">Filter View</span>
                        <div class="btn-group btn-group-sm" role="group" id="filterModeButtons">
                            <button type="button" class="btn {{ ($filter['filter_type'] ?? '') === 'date' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="switchFilterMode('date')">
                                <i class="bi bi-calendar-event me-1"></i>Specific Date
                            </button>
                            <button type="button" class="btn {{ ($filter['filter_type'] ?? '') === 'month' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="switchFilterMode('month')">
                                <i class="bi bi-calendar3 me-1"></i>By Month
                            </button>
                            <button type="button" class="btn {{ ($filter['filter_type'] ?? '') === 'range' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="switchFilterMode('range')">
                                <i class="bi bi-calendar-range me-1"></i>Date Range
                            </button>
                        </div>
                    </div>

                    {{-- Quick Shortcuts --}}
                    <div>
                        <span class="small text-muted fw-bold text-uppercase d-block mb-1">Quick Shortcuts</span>
                        <div class="d-flex flex-wrap align-items-center gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['preset' => 'today', 'date' => null, 'month' => null, 'date_from' => null, 'date_to' => null, 'filter_type' => 'date']) }}" class="btn btn-sm {{ ($filter['preset'] ?? '') === 'today' ? 'btn-dark' : 'btn-light border' }}">Today</a>
                            <a href="{{ request()->fullUrlWithQuery(['preset' => 'yesterday', 'date' => null, 'month' => null, 'date_from' => null, 'date_to' => null, 'filter_type' => 'date']) }}" class="btn btn-sm {{ ($filter['preset'] ?? '') === 'yesterday' ? 'btn-dark' : 'btn-light border' }}">Yesterday</a>
                            <a href="{{ request()->fullUrlWithQuery(['preset' => 'this_month', 'date' => null, 'month' => null, 'date_from' => null, 'date_to' => null, 'filter_type' => 'month']) }}" class="btn btn-sm {{ ($filter['preset'] ?? '') === 'this_month' ? 'btn-dark' : 'btn-light border' }}">This Month</a>
                            <a href="{{ request()->fullUrlWithQuery(['preset' => 'last_month', 'date' => null, 'month' => null, 'date_from' => null, 'date_to' => null, 'filter_type' => 'month']) }}" class="btn btn-sm {{ ($filter['preset'] ?? '') === 'last_month' ? 'btn-dark' : 'btn-light border' }}">Last Month</a>
                            <a href="{{ request()->fullUrlWithQuery(['preset' => 'all', 'date' => null, 'month' => null, 'date_from' => null, 'date_to' => null, 'filter_type' => 'all']) }}" class="btn btn-sm {{ ($filter['filter_type'] ?? '') === 'all' && empty($filter['preset']) ? 'btn-dark' : 'btn-light border' }}">All Time</a>
                        </div>
                    </div>
                </div>

                {{-- Interactive Filter Form --}}
                <form method="GET" action="{{ request()->url() }}" id="analyticsFilterForm" class="row g-3 align-items-end">
                    <input type="hidden" name="filter_type" id="filter_type_input" value="{{ $filter['filter_type'] ?? 'all' }}">

                    {{-- Specific Date Input --}}
                    <div class="col-md-4 col-sm-6 filter-input-pane" id="pane_date" style="{{ ($filter['filter_type'] ?? '') === 'date' ? '' : 'display: none;' }}">
                        <label for="date_input" class="form-label fw-semibold small text-muted">
                            <i class="bi bi-calendar-event me-1 text-primary"></i>Specific Date
                        </label>
                        <input type="date" class="form-control" id="date_input" name="date" value="{{ $filter['date'] ?? '' }}">
                    </div>

                    {{-- Month Input --}}
                    <div class="col-md-4 col-sm-6 filter-input-pane" id="pane_month" style="{{ ($filter['filter_type'] ?? '') === 'month' ? '' : 'display: none;' }}">
                        <label for="month_input" class="form-label fw-semibold small text-muted">
                            <i class="bi bi-calendar3 me-1 text-primary"></i>Select Month
                        </label>
                        <input type="month" class="form-control" id="month_input" name="month" value="{{ $filter['month'] ?? '' }}">
                    </div>

                    {{-- Date Range Inputs --}}
                    <div class="col-md-3 col-sm-6 filter-input-pane" id="pane_range_from" style="{{ ($filter['filter_type'] ?? '') === 'range' ? '' : 'display: none;' }}">
                        <label for="date_from" class="form-label fw-semibold small text-muted">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filter['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-3 col-sm-6 filter-input-pane" id="pane_range_to" style="{{ ($filter['filter_type'] ?? '') === 'range' ? '' : 'display: none;' }}">
                        <label for="date_to" class="form-label fw-semibold small text-muted">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filter['date_to'] ?? '' }}">
                    </div>

                    {{-- Course Filter --}}
                    <div class="col-md-3 col-sm-6">
                        <label for="course_id" class="form-label fw-semibold small text-muted">Course (Optional)</label>
                        <select class="form-select" id="course_id" name="course_id">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ ($courseId ?? '') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Submit & Reset Buttons --}}
                    <div class="col-md-2 col-sm-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                            <i class="bi bi-funnel me-1"></i>Apply
                        </button>
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary" title="Reset all filters">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>

                {{-- Active Filter Indicator --}}
                <div class="mt-3 pt-2 d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-normal">
                        <i class="bi bi-clock-history me-1 text-primary"></i>
                        Viewing: <strong>{{ $filter['label'] ?? 'All Time' }}</strong>
                    </span>
                    @if(!empty($courseId))
                        @php $selCourse = $courses->firstWhere('id', $courseId); @endphp
                        @if($selCourse)
                            <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-normal">
                                <i class="bi bi-book me-1 text-success"></i>
                                Course: <strong>{{ $selCourse->title }}</strong>
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Overview Cards --}}
        <div class="row g-3 mb-4 row-cols-1 row-cols-sm-2 row-cols-lg-5">
            {{-- Reading Time Card --}}
            <div class="col">
                <div class="card h-100 shadow-sm border-0 border-top border-primary border-3">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Time Read / Studied</span>
                                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                                    <i class="bi bi-book-half fs-6"></i>
                                </span>
                            </div>
                            <h2 class="mb-0 text-primary fw-bold">{{ $metrics['reading_formatted'] }}</h2>
                            <small class="text-muted d-block mt-1">
                                {{ $metrics['reading_hours'] }} hours total
                            </small>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <i class="bi bi-check2-circle text-success me-1"></i>
                            <strong>{{ $metrics['topics_count'] }}</strong> topics logged
                            @if(($filter['filter_type'] ?? '') !== 'date' && $metrics['active_days'] > 0)
                                <span class="ms-1">across <strong>{{ $metrics['active_days'] }}</strong> days</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Test Practice Time Card --}}
            <div class="col">
                <div class="card h-100 shadow-sm border-0 border-top border-info border-3">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Test Practice Time</span>
                                <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                                    <i class="bi bi-pencil-square fs-6"></i>
                                </span>
                            </div>
                            <h2 class="mb-0 text-info fw-bold">{{ $metrics['test_time_formatted'] }}</h2>
                            <small class="text-muted d-block mt-1">
                                {{ $metrics['test_hours'] }} hours taking tests
                            </small>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <i class="bi bi-file-earmark-check text-info me-1"></i>
                            <strong>{{ $metrics['tests_completed'] }}</strong> test attempts
                        </div>
                    </div>
                </div>
            </div>

            {{-- Combined Total Learning Time Card --}}
            <div class="col">
                <div class="card h-100 shadow-sm border-0 border-top border-dark border-3">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Total Learning Time</span>
                                <span class="badge bg-dark-subtle text-dark p-2 rounded-circle">
                                    <i class="bi bi-stopwatch fs-6"></i>
                                </span>
                            </div>
                            <h2 class="mb-0 text-dark fw-bold">{{ $metrics['total_learning_formatted'] }}</h2>
                            <small class="text-muted d-block mt-1">
                                {{ $metrics['total_learning_hours'] }} hours combined
                            </small>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <i class="bi bi-pie-chart text-dark me-1"></i>
                            Reading + Test Practice
                        </div>
                    </div>
                </div>
            </div>

            {{-- Average Score Card --}}
            <div class="col">
                <div class="card h-100 shadow-sm border-0 border-top border-success border-3">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Average Test Score</span>
                                <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                                    <i class="bi bi-trophy fs-6"></i>
                                </span>
                            </div>
                            <h2 class="mb-0 text-success fw-bold">{{ $metrics['avg_score'] }}%</h2>
                            <small class="text-muted d-block mt-1">
                                @if(!empty($metrics['pending_evaluations']) && $metrics['pending_evaluations'] > 0)
                                    <span class="text-warning"><i class="bi bi-clock me-1"></i>{{ $metrics['pending_evaluations'] }} pending review</span>
                                @else
                                    {{ $metrics['graded_count'] }} graded tests
                                @endif
                            </small>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <span class="fw-semibold {{ $metrics['improvement'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="bi bi-graph-{{ $metrics['improvement'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ $metrics['improvement'] > 0 ? '+' : '' }}{{ $metrics['improvement'] }}%
                            </span>
                            vs prior tests
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pass Rate (95% passing mark) Card --}}
            <div class="col">
                <div class="card h-100 shadow-sm border-0 border-top border-warning border-3">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Pass Rate</span>
                                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                                    <i class="bi bi-check2-all fs-6"></i>
                                </span>
                            </div>
                            <h2 class="mb-0 text-dark fw-bold">{{ $metrics['pass_rate'] }}%</h2>
                            <small class="text-muted d-block mt-1">
                                Required: <strong class="text-danger">95% Pass Mark</strong>
                            </small>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <i class="bi bi-shield-check text-warning me-1"></i>
                            Strict 95% passing benchmark
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reading & Daily Learning Topics Breakdown Section --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-journal-bookmark-fill fs-5 text-primary me-2"></i>
                    <div>
                        <strong class="text-dark">Daily Reading & Topics Learned</strong>
                        <div class="text-muted small">Recorded in Status Tracker • Time spent learning French</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="bi bi-clock me-1"></i>{{ $readingData['total_formatted'] }} Read
                    </span>
                    @if(($filter['filter_type'] ?? '') !== 'date' && $readingData['active_days_count'] > 0)
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                            {{ $readingData['active_days_count'] }} Active Days (Avg {{ $readingData['avg_daily_formatted'] }}/day)
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if(($filter['filter_type'] ?? '') === 'date')
                    {{-- Single Specific Date View --}}
                    @if($readingData['topics_count'] > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Time Window</th>
                                        <th style="width: 15%;">Duration</th>
                                        <th>Topic Learned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($readingData['all_topics'] as $topic)
                                        <tr>
                                            <td>
                                                <i class="bi bi-clock me-1 text-muted"></i>
                                                {{ $topic['starting_time'] ?: '--:--' }} &rarr; {{ $topic['ending_time'] ?: '--:--' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    {{ $topic['duration_formatted'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $topic['topic'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-journal-x display-5 d-block mb-2 text-muted"></i>
                            <p class="mb-1">No reading or study topics recorded on <strong>{{ $filter['label'] }}</strong>.</p>
                            @if(auth()->user()->role === 'student')
                                <a href="{{ route('student.status.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-pencil-square me-1"></i>Record study in Status Tracker
                                </a>
                            @endif
                        </div>
                    @endif
                @else
                    {{-- Month / Range / All Time View --}}
                    @if(count($readingData['days']) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 18%;">Date</th>
                                        <th style="width: 15%;">Day</th>
                                        <th style="width: 15%;">Time Read</th>
                                        <th>Topics Learned</th>
                                        <th style="width: 12%;" class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($readingData['days'] as $day)
                                        <tr>
                                            <td>
                                                <strong>{{ $day['date_formatted'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $day['day_name'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                                    {{ $day['total_formatted'] }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(count($day['topics']) > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($day['topics'] as $topic)
                                                            <span class="badge bg-light text-dark border" title="{{ $topic['starting_time'] }} - {{ $topic['ending_time'] }} ({{ $topic['duration_formatted'] }})">
                                                                {{ Str::limit($topic['topic'], 50) }}
                                                                <small class="text-muted">({{ $topic['duration_formatted'] }})</small>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted small">No topics entered</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ request()->fullUrlWithQuery(['filter_type' => 'date', 'date' => $day['date'], 'preset' => null, 'month' => null, 'date_from' => null, 'date_to' => null]) }}" class="btn btn-sm btn-outline-primary" title="Filter to this specific date">
                                                    <i class="bi bi-zoom-in me-1"></i>View Day
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-5 d-block mb-2 text-muted"></i>
                            <p class="mb-1">No study reading activity found for the selected period.</p>
                            @if(auth()->user()->role === 'student')
                                <a href="{{ route('student.status.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-pencil-square me-1"></i>Go to Status Tracker
                                </a>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Score Progression & Activity Charts --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <strong><i class="bi bi-graph-up me-2 text-primary"></i>Performance & Study Progression</strong>
                            <div class="text-muted small">Test scores progression over time with 95% passing line</div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-dash-lg me-1"></i>Pass Target: 95%
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="scoreChart" height="90"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Test History Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <strong><i class="bi bi-list-check me-2 text-primary"></i>Test History & Results</strong>
                    <div class="text-muted small">Passing mark is strictly 95% for all tests</div>
                </div>
                <span class="badge bg-secondary px-3 py-2 fs-6">{{ $testHistory->total() }} Total Attempts</span>
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
                                @php
                                    $testPassScore = round($attempt->test->passing_score ?? 95);
                                    $isPassed = $attempt->passed || ($attempt->score >= $testPassScore);
                                @endphp
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
                                            <span class="badge {{ $isPassed ? 'bg-success' : ($attempt->score >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ number_format((float)($attempt->score ?? 0), 2) }}%
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($attempt->is_pending_evaluation))
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split me-1"></i>Under Review
                                            </span>
                                        @elseif($isPassed)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Passed
                                            </span>
                                        @else
                                            <span class="badge bg-danger" title="Required passing score: {{ $testPassScore }}%">
                                                <i class="bi bi-x-circle me-1"></i>Failed
                                                <small class="text-white-50">({{ $testPassScore }}%)</small>
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
                                        No test attempts found for this period
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
                            {{ $testHistory->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script>
            // Switch filter pane based on mode
            function switchFilterMode(mode) {
                document.getElementById('filter_type_input').value = mode;

                // Update tab buttons
                const buttons = document.querySelectorAll('#filterModeButtons button');
                buttons.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });

                // Toggle panes
                document.getElementById('pane_date').style.display = (mode === 'date') ? 'block' : 'none';
                document.getElementById('pane_month').style.display = (mode === 'month') ? 'block' : 'none';
                document.getElementById('pane_range_from').style.display = (mode === 'range') ? 'block' : 'none';
                document.getElementById('pane_range_to').style.display = (mode === 'range') ? 'block' : 'none';

                // Highlight active button
                if (mode === 'date') buttons[0].classList.replace('btn-outline-primary', 'btn-primary');
                if (mode === 'month') buttons[1].classList.replace('btn-outline-primary', 'btn-primary');
                if (mode === 'range') buttons[2].classList.replace('btn-outline-primary', 'btn-primary');
            }

            // Fetch chart data and render
            const chartUrl = '{{ auth()->user()->role === "admin" ? route("admin.students.analytics.chart-data", $student) : route("student.analytics.chart-data") }}';
            const urlParams = new URLSearchParams(window.location.search);

            fetch(chartUrl + '?' + urlParams.toString())
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('scoreChart').getContext('2d');
                    
                    const datasets = [];

                    // If we have test scores
                    if (data.labels && data.labels.length > 0) {
                        datasets.push({
                            label: 'Test Scores (%)',
                            data: data.scores,
                            borderColor: 'rgb(13, 110, 253)',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            yAxisID: 'y'
                        });

                        // 95% passing line dataset
                        datasets.push({
                            label: 'Passing Threshold (95%)',
                            data: data.labels.map(() => 95),
                            borderColor: 'rgba(220, 53, 69, 0.7)',
                            borderDash: [6, 6],
                            pointRadius: 0,
                            fill: false,
                            yAxisID: 'y'
                        });
                    }

                    // Render chart
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels && data.labels.length > 0 ? data.labels : ['No tests taken'],
                            datasets: datasets.length > 0 ? datasets : [{
                                label: 'No Data',
                                data: [0],
                                borderColor: '#ccc'
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
                                    },
                                    ticks: {
                                        stepSize: 20
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
                                            if (data.testNames && data.testNames[context[0].dataIndex]) {
                                                return data.testNames[context[0].dataIndex];
                                            }
                                            return context[0].label;
                                        },
                                        label: function (context) {
                                            if (context.dataset.label.includes('Passing')) {
                                                return 'Passing mark: 95%';
                                            }
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
                        '<div class="alert alert-light border text-muted text-center py-4">No test progression chart data for this period</div>';
                });
        </script>
    @endpush
@endsection