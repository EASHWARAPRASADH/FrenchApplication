@extends('layouts.app')

@section('title', 'My Progress - TS Language Platform')

@section('content')
<style>
/* Progress Tracking Styles */
.progress-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 0;
    border-radius: 0 0 24px 24px;
    margin-bottom: 3rem;
}

.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.stat-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
}

.stat-icon.courses {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-icon.lessons {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-icon.tests {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-icon.streak {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6b7280;
    font-size: 1rem;
    font-weight: 500;
}

.progress-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.course-progress-item {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.course-progress-item:hover {
    border-color: #e0e7ff;
    background: #f8faff;
}

.course-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1.5rem;
}

.course-info {
    flex: 1;
}

.course-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.course-meta {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.progress-bar-container {
    background: #f3f4f6;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.progress-bar.low {
    background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
}

.progress-bar.medium {
    background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
}

.progress-bar.high {
    background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
}

.progress-text {
    display: flex;
    justify-content: between;
    align-items: center;
    font-size: 0.8rem;
    color: #6b7280;
}

.course-actions {
    display: flex;
    gap: 0.5rem;
}

.activity-timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 0;
    width: 2px;
    height: 100%;
    background: #e5e7eb;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-dot {
    position: absolute;
    left: -2.5rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
}

.timeline-content {
    background: #f8faff;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid #667eea;
}

.timeline-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.timeline-meta {
    color: #6b7280;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
}

.timeline-description {
    color: #4b5563;
    font-size: 0.9rem;
}

.achievement-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.achievement-card {
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.achievement-card.earned {
    border-color: #fbbf24;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.achievement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.achievement-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.achievement-icon.earned {
    color: #f59e0b;
}

.achievement-icon.locked {
    color: #d1d5db;
}

.achievement-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.achievement-description {
    color: #6b7280;
    font-size: 0.8rem;
    margin-bottom: 1rem;
}

.achievement-progress {
    background: #f3f4f6;
    border-radius: 10px;
    height: 6px;
    overflow: hidden;
}

.achievement-progress-bar {
    background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.filter-tabs {
    background: white;
    border-radius: 12px;
    padding: 0.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    display: inline-flex;
}

.filter-tab {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-tab.active {
    background: #667eea;
    color: white;
}

@media (max-width: 768px) {
    .stats-overview {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .course-progress-item {
        flex-direction: column;
        text-align: center;
    }
    
    .course-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .achievement-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="progress-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">My Progress</h1>
                <p class="mb-0 opacity-90">Track your learning journey and achievements</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex justify-content-md-end gap-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold">{{ $overallProgress }}%</div>
                        <small class="opacity-75">Overall Progress</small>
                    </div>
                    <div class="text-center">
                        <div class="fs-4 fw-bold">{{ $currentStreak }}</div>
                        <small class="opacity-75">Day Streak</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Statistics Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-icon courses">
                <i class="bi bi-book"></i>
            </div>
            <div class="stat-number">{{ $totalCourses }}</div>
            <div class="stat-label">Enrolled Courses</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon lessons">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-number">{{ $completedLessons }}</div>
            <div class="stat-label">Lessons Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon tests">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <div class="stat-number">{{ $completedTests }}</div>
            <div class="stat-label">Tests Passed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <i class="bi bi-pie-chart"></i>
            </div>
            <div class="stat-number">{{ $overallProgress }}%</div>
            <div class="stat-label">Overall Progress</div>
        </div>
    </div>

    <div class="row">
        <!-- Course Progress -->
        <div class="col-lg-8">
            <div class="progress-section">
                <div class="section-header">
                    <h4 class="mb-0">Course Progress</h4>
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">All</button>
                        <button class="filter-tab" data-filter="active">Active</button>
                        <button class="filter-tab" data-filter="completed">Completed</button>
                    </div>
                </div>

                @forelse($courseProgress as $progress)
                    @php
                        $progressPercentage = $progress['total_lessons'] > 0 
                            ? round(($progress['completed_lessons'] / $progress['total_lessons']) * 100) 
                            : 0;
                        $progressClass = $progressPercentage < 30 ? 'low' : ($progressPercentage < 70 ? 'medium' : 'high');
                        $status = $progressPercentage >= 100 ? 'completed' : 'active';
                    @endphp
                    
                    <div class="course-progress-item" data-status="{{ $status }}">
                        <div class="course-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-title">{{ $progress['course']->title }}</div>
                            <div class="course-meta">
                                {{ $progress['completed_lessons'] }} of {{ $progress['total_lessons'] }} lessons completed
                                • Last activity {{ $progress['last_activity'] ?? 'Never' }}
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <div class="progress-text">
                                <span>{{ $progressPercentage }}% Complete</span>
                                <span>{{ $progress['time_spent'] ?? '0' }} hours spent</span>
                            </div>
                        </div>
                        <div class="course-actions">
                            <a href="{{ route('student.course.show', $progress['course']) }}" class="btn btn-sm btn-primary">
                                Continue
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-book fs-1 text-muted"></i>
                        <h5 class="mt-3">No Courses Yet</h5>
                        <p class="text-muted">Start learning by enrolling in a course!</p>
                    </div>
                @endforelse
            </div>

            <!-- Recent Activity -->
            <div class="progress-section">
                <div class="section-header">
                    <h4 class="mb-0">Recent Activity</h4>
                </div>

                <div class="activity-timeline">
                    @forelse($recentActivity as $activity)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">{{ $activity['title'] }}</div>
                                <div class="timeline-meta">
                                    {{ $activity['course'] }} • {{ $activity['date'] }}
                                </div>
                                <div class="timeline-description">{{ $activity['description'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history fs-3 text-muted"></i>
                            <p class="text-muted mt-2">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Achievements Sidebar -->
        <div class="col-lg-4">
            <div class="progress-section">
                <div class="section-header">
                    <h5 class="mb-0">Achievements</h5>
                </div>

                <div class="achievement-grid">
                    @forelse($achievements as $achievement)
                        <div class="achievement-card {{ $achievement['earned'] ? 'earned' : '' }}">
                            <div class="achievement-icon {{ $achievement['earned'] ? 'earned' : 'locked' }}">
                                <i class="bi bi-{{ $achievement['icon'] }}"></i>
                            </div>
                            <div class="achievement-title">{{ $achievement['name'] }}</div>
                            <div class="achievement-description">{{ $achievement['description'] }}</div>
                            @if(!$achievement['earned'])
                                <div class="achievement-progress">
                                    <div class="achievement-progress-bar" style="width: {{ $achievement['progress'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $achievement['progress'] }}% complete</small>
                            @else
                                <small class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Earned {{ $achievement['earned_date'] }}
                                </small>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-trophy fs-3 text-muted"></i>
                            <p class="text-muted mt-2">No achievements yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Filter functionality
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Filter courses
        const filter = this.dataset.filter;
        const courses = document.querySelectorAll('.course-progress-item');
        
        courses.forEach(course => {
            if (filter === 'all' || course.dataset.status === filter) {
                course.style.display = 'flex';
            } else {
                course.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection
