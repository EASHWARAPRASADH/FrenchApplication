<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel - TS Language Platform')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app_design_system.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        :root {
            --primary-color: #0066CC;
            --primary-gradient: linear-gradient(135deg, #0066CC 0%, #1E40AF 100%);
            --bg-color: #F8FAFC;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: #0F172A;
            overflow-x: hidden;
        }

        /* Navbar Premium */
        .navbar-premium {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            height: 60px;
            z-index: 1030;
        }

        .navbar-brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Sidebar Modern */
        .admin-sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - 60px);
            background: #FFFFFF;
            border-right: 1px solid #E2E8F0;
            position: fixed;
            top: 60px;
            left: 0;
            z-index: 1020;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            padding: 1.25rem 0.85rem;
        }

        .admin-sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            font-weight: 600;
            margin-bottom: 0.5rem;
            margin-top: 1.25rem;
            padding-left: 0.75rem;
        }

        .sidebar-category:first-child {
            margin-top: 0;
        }

        .nav-link {
            color: #64748B;
            font-weight: 500;
            font-size: 0.9375rem;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background-color: #EBF5FF;
        }

        .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 102, 204, 0.2);
        }

        .nav-link i {
            font-size: 1.1rem;
            margin-right: 0.875rem;
            width: 24px;
            text-align: center;
        }

        /* Content Area */
        .admin-content {
            margin-left: var(--sidebar-width);
            margin-top: 60px;
            padding: 1.25rem;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: calc(100vh - 60px);
        }

        .admin-content.expanded {
            margin-left: 0;
        }

        /* Mobile */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        /* User Avatar */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-premium fixed-top">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark p-0 me-3 d-lg-none" type="button" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-shield-check text-primary fs-4"></i>
                    <span class="navbar-brand-text">TS Admin</span>
                </a>
            </div>

            <!-- Right Menu -->
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}"
                    class="btn btn-outline-light text-dark border-0 hover-bg-gray-100 d-none d-md-block">
                    <i class="bi bi-box-arrow-up-right me-1"></i> View Site
                </a>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown">
                        <div class="user-avatar text-white shadow-sm">
                            {{ Auth::user()->initials }}
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2 p-2">
                        <li>
                            <div class="px-3 py-2 border-bottom mb-2">
                                <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li><a class="dropdown-item rounded-2 mb-1" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2 text-primary"></i>Profile
                            </a></li>
                        <li><a class="dropdown-item rounded-2 mb-1" href="{{ route('admin.settings.index') }}">
                                <i class="bi bi-gear me-2 text-primary"></i>Settings
                            </a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="admin-sidebar shadow-sm" id="adminSidebar">
        <div class="nav flex-column">

            <div class="sidebar-category">Overview</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}"
                href="{{ route('admin.analytics.index') }}">
                <i class="bi bi-graph-up-arrow"></i> Analytics
            </a>

            <div class="sidebar-category">Management</div>
            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                href="{{ route('admin.users.index') }}">
                <i class="bi bi-people-fill"></i> Users
            </a>
            <a class="nav-link {{ request()->routeIs('admin.status*') ? 'active' : '' }}"
                href="{{ route('admin.status.index') }}">
                <i class="bi bi-clipboard-data-fill"></i> Status Reports
            </a>
            <a class="nav-link {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}"
                href="{{ route('admin.attendance.index') }}">
                <i class="bi bi-person-check-fill"></i> Attendance
            </a>

            <div class="sidebar-category">Learning</div>
            <a class="nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}"
                href="{{ route('admin.courses.index') }}">
                <i class="bi bi-book-fill"></i> Courses
            </a>
            <a class="nav-link {{ request()->routeIs('admin.course-builder*') ? 'active' : '' }}"
                href="{{ route('admin.course-builder.index') }}">
                <i class="bi bi-bricks"></i> Builder
            </a>
            <a class="nav-link {{ request()->routeIs('admin.assignments*') ? 'active' : '' }}"
                href="{{ route('admin.assignments.index') }}">
                <i class="bi bi-person-check-fill"></i> Assignments
            </a>
            <a class="nav-link {{ request()->routeIs('admin.test-submissions*') ? 'active' : '' }}"
                href="{{ route('admin.test-submissions.index') }}">
                <i class="bi bi-file-earmark-text-fill"></i> Submissions
            </a>
            <a class="nav-link {{ request()->fullUrl() === route('admin.test-submissions.index', ['status' => 'pending']) ? 'active' : '' }}"
                href="{{ route('admin.test-submissions.index', ['status' => 'pending']) }}">
                <i class="bi bi-hourglass-split"></i> Pending Grades
            </a>

            <div class="sidebar-category">System</div>
            <a class="nav-link {{ request()->routeIs('admin.files*') ? 'active' : '' }}"
                href="{{ route('admin.files.index') }}">
                <i class="bi bi-folder-fill"></i> Files
            </a>
            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"
                href="{{ route('admin.settings.index') }}">
                <i class="bi bi-gear-fill"></i> Settings
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-content" id="adminContent">
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 lead"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 lead"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 lead"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', function (e) {
                    if (window.innerWidth < 992 &&
                        sidebar.classList.contains('show') &&
                        !sidebar.contains(e.target) &&
                        !toggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>