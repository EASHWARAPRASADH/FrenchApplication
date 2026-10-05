@extends('layouts.app')

@section('title', 'Student Dashboard - TS Language Platform')

@section('content')
    <style>
        /* Professional Premium Design System */
        :root {
            /* Sophisticated Color Palette */
            --indigo-600: #4F46E5;
            --indigo-700: #4338CA;
            --purple-600: #7C3AED;
            --emerald-600: #059669;
            --sky-600: #0284C7;
            --amber-600: #D97706;
            --rose-600: #E11D48;

            /* Professional Gradients */
            --gradient-primary: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            --gradient-success: linear-gradient(135deg, #059669 0%, #10B981 100%);
            --gradient-info: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
            --gradient-warning: linear-gradient(135deg, #D97706 0%, #F59E0B 100%);
            --gradient-rose: linear-gradient(135deg, #E11D48 0%, #F43F5E 100%);

            /* Refined Shadows */
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 25px 0 rgba(0, 0, 0, 0.08);
            --shadow-xl: 0 20px 40px 0 rgba(0, 0, 0, 0.10);
        }

        .dashboard-container {
            max-width: 1360px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
            background: #F8FAFC;
        }

        /* Premium Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #0066CC 0%, #1E40AF 100%);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            color: white;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -5%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-icon {
            font-size: 3rem;
            margin-bottom: 1.25rem;
            display: inline-block;
            animation: float 3.5s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .welcome-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .welcome-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            line-height: 1.5;
            font-weight: 400;
        }

        /* Stats Grid - Professional Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
        }

        .stat-icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .stat-icon-wrapper.primary {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(124, 58, 237, 0.08) 100%);
        }

        .stat-icon-wrapper.success {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.08) 0%, rgba(16, 185, 129, 0.08) 100%);
        }

        .stat-icon-wrapper.warning {
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.08) 0%, rgba(245, 158, 11, 0.08) 100%);
        }

        .stat-icon-wrapper.danger {
            background: linear-gradient(135deg, rgba(225, 29, 72, 0.08) 0%, rgba(244, 63, 94, 0.08) 100%);
        }

        .stat-icon {
            font-size: 2rem;
        }

        .stat-number {
            font-size: 2.25rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.375rem;
            line-height: 1;
        }

        .stat-text {
            color: #64748B;
            font-size: 0.975rem;
            font-weight: 500;
        }

        /* Courses Section - Premium */
        .courses-section {
            background: white;
            border-radius: 20px;
            padding: 2.25rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #F1F5F9;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0F172A;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .section-title i {
            color: #4F46E5;
            font-size: 1.375rem;
        }

        /* Level Cards - Sophisticated Design */
        .course-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .course-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
        }

        .course-header {
            padding: 2.25rem 1.75rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .course-header::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -15%;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
        }

        /* Professional Level Colors */
        .level-A1 .course-header {
            background: linear-gradient(135deg, #059669 0%, #10B981 100%);
        }

        .level-A2 .course-header {
            background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
        }

        .level-B1 .course-header {
            background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%);
        }

        .level-B2 .course-header {
            background: linear-gradient(135deg, #7C3AED 0%, #A855F7 100%);
        }

        .level-C1 .course-header {
            background: linear-gradient(135deg, #E11D48 0%, #F43F5E 100%);
        }

        .level-C2 .course-header {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.625rem;
            position: relative;
            z-index: 1;
        }

        .course-meta {
            font-size: 0.975rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .course-body {
            padding: 1.75rem;
        }

        .course-description {
            color: #64748B;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .course-btn {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            border: none;
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.9375rem;
        }

        .course-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
            color: white;
        }

        /* Sidebar Cards - Clean & Professional */
        .sidebar-card {
            background: white;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .sidebar-header {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #F1F5F9;
        }

        .sidebar-header i {
            color: #4F46E5;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            transition: background 0.2s ease;
            margin-bottom: 0.625rem;
        }

        .activity-item:hover {
            background: #F8FAFC;
        }

        .activity-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .activity-icon.completed {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
            color: #059669;
        }

        .activity-icon.in-progress {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.1) 0%, rgba(14, 165, 233, 0.1) 100%);
            color: #0284C7;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.9375rem;
            margin-bottom: 0.25rem;
        }

        .activity-subtitle {
            color: #64748B;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            color: #94A3B8;
            font-size: 0.8125rem;
        }

        /* Achievement Grid */
        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .achievement-item {
            text-align: center;
            padding: 1.125rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .achievement-item:hover {
            background: #F8FAFC;
            transform: scale(1.05);
        }

        .achievement-icon {
            font-size: 2.75rem;
            margin-bottom: 0.625rem;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(217, 119, 6, 0.25));
        }

        .achievement-name {
            font-size: 0.8125rem;
            color: #64748B;
            font-weight: 500;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 3.5rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: #E2E8F0;
            margin-bottom: 1.25rem;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #64748B;
            margin-bottom: 0.625rem;
        }

        .empty-text {
            color: #94A3B8;
            font-size: 0.9375rem;
            margin-bottom: 1.75rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1.5rem 1rem;
            }

            .welcome-card {
                padding: 2rem 1.5rem;
            }

            .welcome-title {
                font-size: 1.75rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .achievement-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Load Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }
    </style>

    <div class="dashboard-container">
        <!-- Premium Welcome Card -->
        <div class="welcome-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 welcome-content">
                <div>
                    <h1 class="welcome-title">Bonjour, {{ $user->name }}!</h1>
                    <p class="welcome-subtitle mb-0">
                        Ready to continue your French learning journey? Let's keep up the fantastic work!
                    </p>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="{{ route('student.courses') }}" class="btn btn-light text-primary fw-semibold px-3 shadow-sm">
                        <i class="bi bi-play-circle me-1"></i>My Courses
                    </a>
                    <a href="{{ route('student.status.index') }}" class="btn btn-outline-light px-3">
                        <i class="bi bi-clock-history me-1"></i>Status Tracker
                    </a>
                </div>
            </div>
        </div>

        <!-- Professional Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrapper primary">
                    <i class="bi bi-book stat-icon" style="color: #4F46E5;"></i>
                </div>
                <div class="stat-number">{{ $totalCourses }}</div>
                <div class="stat-text">Enrolled Courses</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper success">
                    <i class="bi bi-check-circle stat-icon" style="color: #059669;"></i>
                </div>
                <div class="stat-number">{{ $completedLessons }}</div>
                <div class="stat-text">Lessons Completed</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper warning">
                    <i class="bi bi-graph-up stat-icon" style="color: #D97706;"></i>
                </div>
                <div class="stat-number">{{ $progressPercentage }}%</div>
                <div class="stat-text">Overall Progress</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper danger">
                    <i class="bi bi-trophy stat-icon" style="color: #E11D48;"></i>
                </div>
                <div class="stat-number">{{ $achievements->count() }}</div>
                <div class="stat-text">Achievements</div>
            </div>
        </div>

        <div class="row">
            <!-- Courses Section -->
            <div class="col-lg-8 mb-4">
                <div class="courses-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="bi bi-book"></i>
                            <span>My Courses</span>
                        </h2>
                        <a href="{{ route('student.courses') }}" class="btn btn-sm btn-outline-primary"
                            style="border-radius: 10px; font-weight: 500;">
                            View All
                        </a>
                    </div>

                    @if(isset($levels) && $levels->count() > 0)
                        <div class="row g-3">
                            @foreach($levels as $lvl)
                                @php $label = $lvl['level']; @endphp
                                <div class="col-lg-4 col-md-6">
                                    <a href="{{ route('student.courses.level', ['level' => $label]) }}"
                                        class="text-decoration-none">
                                        <div class="course-card level-{{ $label }}">
                                            <div class="course-header">
                                                <div class="course-title">Level {{ $label }}</div>
                                                <div class="course-meta">
                                                    <i class="bi bi-layers me-1"></i>
                                                    {{ $lvl['count'] }} course{{ $lvl['count'] == 1 ? '' : 's' }}
                                                </div>
                                            </div>
                                            <div class="course-body">
                                                <div class="course-description">
                                                    Explore your courses in Level {{ $label }} and continue your learning journey
                                                </div>
                                                <button class="course-btn">
                                                    <i class="bi bi-folder2-open"></i>
                                                    <span>Open Level</span>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-book empty-icon"></i>
                            <h3 class="empty-title">No Courses Yet</h3>
                            <p class="empty-text">Start your learning journey by enrolling in a course!</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary"
                                style="border-radius: 12px; padding: 0.875rem 2rem; font-weight: 600;">
                                <i class="bi bi-search me-2"></i>
                                Browse Courses
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Activity -->
                <div class="sidebar-card">
                    <h3 class="sidebar-header">
                        <i class="bi bi-clock-history"></i>
                        <span>Recent Activity</span>
                    </h3>
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        @foreach($recentActivities->take(5) as $activity)
                            <div class="activity-item">
                                <div class="activity-icon {{ $activity->icon_class ?? 'primary' }}">
                                    <i class="bi {{ $activity->icon ?? 'bi-circle' }}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ Str::limit($activity->title, 35) }}</div>
                                    <div class="activity-subtitle">{{ Str::limit($activity->subtitle, 30) }}</div>
                                    <div class="activity-time">{{ $activity->date->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @elseif(isset($recentProgress) && $recentProgress->count() > 0)
                        {{-- Fallback for legacy variable if recentActivities is missing --}}
                        @foreach($recentProgress->take(5) as $progress)
                            <div class="activity-item">
                                <div class="activity-icon {{ $progress->status === 'completed' ? 'completed' : 'in-progress' }}">
                                    <i
                                        class="bi {{ $progress->status === 'completed' ? 'bi-check-circle-fill' : 'bi-play-circle' }}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ Str::limit($progress->lesson->title ?? 'Lesson', 35) }}</div>
                                    <div class="activity-subtitle">
                                        {{ Str::limit($progress->lesson->course->title ?? 'Course', 30) }}</div>
                                    <div class="activity-time">{{ $progress->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state" style="padding: 2.5rem 1rem;">
                            <i class="bi bi-clock-history" style="font-size: 3rem; color: #E2E8F0; margin-bottom: 0.75rem;"></i>
                            <p class="empty-text mb-0">No recent activity</p>
                        </div>
                    @endif
                </div>

                <!-- Achievements -->
                <div class="sidebar-card">
                    <h3 class="sidebar-header">
                        <i class="bi bi-trophy"></i>
                        <span>Recent Achievements</span>
                    </h3>
                    @if($achievements->count() > 0)
                        <div class="achievement-grid">
                            @foreach($achievements->take(6) as $achievement)
                                <div class="achievement-item">
                                    <div class="mb-2">
                                        <i class="bi bi-{{ $achievement->icon ?? 'trophy' }}"
                                            style="font-size: 2.5rem; color: #fbbf24; filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));"></i>
                                    </div>
                                    <div class="achievement-name">{{ Str::limit($achievement->name, 15) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding: 2.5rem 1rem;">
                            <i class="bi bi-trophy" style="font-size: 3rem; color: #E2E8F0; margin-bottom: 0.75rem;"></i>
                            <p class="empty-text mb-0">No achievements yet</p>
                        </div>
                    @endif
                </div>

                <!-- Recommended Courses -->
                @if($recommendedCourses->count() > 0)
                    <div class="sidebar-card">
                        <h3 class="sidebar-header">
                            <i class="bi bi-lightbulb"></i>
                            <span>Recommended for You</span>
                        </h3>
                        @foreach($recommendedCourses as $course)
                            <div class="activity-item" style="display: block; margin-bottom: 1.125rem;">
                                <div class="activity-title mb-1">{{ $course->title }}</div>
                                <p class="activity-subtitle mb-2">{{ Str::limit($course->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge"
                                        style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: white; padding: 0.4rem 0.875rem; border-radius: 8px; font-weight: 500;">{{ ucfirst($course->level) }}</span>
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-primary"
                                        style="border-radius: 10px; font-weight: 500;">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection