@extends('layouts.admin')

@section('title', 'Dashboard - TS Admin')

@section('content')
    <style>
        .stat-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            /* Very subtle border */
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            /* Soft shadow on hover */
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-gradient-header {
            background: linear-gradient(135deg, #0066CC 0%, #1E40AF 100%);
            border-radius: 1rem;
            color: white;
        }
    </style>

    <!-- Welcome Header -->
    <div class="bg-gradient-header p-3 px-4 mb-3 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center position-relative z-1">
            <div class="col-md-8">
                <h2 class="fw-bold mb-1">
                    Hello, {{ auth()->user()->name }}
                </h2>
                <p class="mb-0 text-white-50 small">
                    Here's what's happening on your platform today.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light text-primary fw-medium px-3 btn-sm">
                        <i class="bi bi-people me-1"></i>Manage Users
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-light px-3 btn-sm">
                        <i class="bi bi-book-fill me-1"></i>Courses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row g-3 mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-3 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                        Active
                    </span>
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($totalUsers) }}</h2>
                <div class="text-muted small">Total Users</div>
                <div class="mt-3 pt-3 border-top d-flex gap-3 small">
                    <span class="text-dark fw-medium">{{ $totalStudents }} Students</span>
                    <span class="text-muted">|</span>
                    <span class="text-dark fw-medium">{{ $totalTeachers }} Teachers</span>
                </div>
            </div>
        </div>

        <!-- Courses -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-3 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                        {{ $publishedCourses }} Published
                    </span>
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($totalCourses) }}</h2>
                <div class="text-muted small">Total Courses</div>
                <div class="mt-3 pt-3 border-top small text-warning">
                    <i class="bi bi-hourglass-split me-1"></i> {{ $draftCourses }} drafts pending
                </div>
            </div>
        </div>

        <!-- Enrollments -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-3 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($totalEnrollments) }}</h2>
                <div class="text-muted small">Total Enrollments</div>
                <div class="mt-3 pt-3 border-top small text-success">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ $activeEnrollments }} active learning
                </div>
            </div>
        </div>

        <!-- Pending Actions -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-3 h-100 shadow-sm {{ $pendingTeachers > 0 ? 'border-danger border-2' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    @if($pendingTeachers > 0)
                        <span class="badge bg-danger rounded-pill px-3">Action Needed</span>
                    @else
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">All Good</span>
                    @endif
                </div>
                <h2 class="fw-bold mb-1">{{ number_format($pendingTeachers) }}</h2>
                <div class="text-muted small">Pending Approvals</div>
                @if($pendingTeachers > 0)
                    <div class="mt-3">
                        <a href="{{ route('admin.users.index') }}?status=pending" class="btn btn-sm btn-danger w-100">Review
                            Now</a>
                    </div>
                @else
                    <div class="mt-3 pt-3 border-top small text-muted">
                        No pending requests
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-4 p-4 border-end">
                    <div class="d-flex align-items-center">
                        <div
                            class="icon-box me-3 {{ $systemHealth['database']['status'] === 'healthy' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                            <i class="bi bi-database-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Database</div>
                            <div class="small text-muted">{{ $systemHealth['database']['message'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 p-4 border-end">
                    <div class="d-flex align-items-center">
                        <div
                            class="icon-box me-3 {{ $systemHealth['storage']['status'] === 'healthy' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                            <i class="bi bi-hdd-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Storage</div>
                            <div class="small text-muted">{{ $systemHealth['storage']['message'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 p-4">
                    <div class="d-flex align-items-center">
                        <div
                            class="icon-box me-3 {{ $systemHealth['cache']['status'] === 'healthy' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                            <i class="bi bi-lightning-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Cache</div>
                            <div class="small text-muted">{{ $systemHealth['cache']['message'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                    <h5 class="fw-bold mb-0">Recent Users</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light text-primary fw-medium">View
                        All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentUsers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentUsers as $user)
                                <div class="list-group-item d-flex align-items-center p-3 border-0 border-bottom mx-3">
                                    <div class="avatar-circle me-3 flex-shrink-0"
                                        style="background-color: {{ ['#4F46E5', '#7C3AED', '#EC4899'][rand(0, 2)] }}">
                                        {{ $user->initials }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-{{ $user->role === 'student' ? 'primary' : ($user->role === 'teacher' ? 'success' : 'warning') }} bg-opacity-10 text-{{ $user->role === 'student' ? 'primary' : ($user->role === 'teacher' ? 'success' : 'dark') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        <div class="small text-muted mt-1">{{ $user->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">No recent users found.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Courses -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                    <h5 class="fw-bold mb-0">Recent Courses</h5>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-light text-primary fw-medium">View
                        All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentCourses->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentCourses as $course)
                                <div class="list-group-item p-3 border-0 border-bottom mx-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="fw-semibold text-dark">{{ $course->title }}</div>
                                        <small class="text-muted">{{ $course->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center small text-muted">
                                            <i class="bi bi-person me-1"></i> {{ $course->teacher->name }}
                                        </div>
                                        <div>
                                            <span
                                                class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }} rounded-pill">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                            <span class="badge bg-light text-dark border ms-1">{{ ucfirst($course->level) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">No courses found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection