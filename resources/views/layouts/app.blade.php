<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="stylesheet" href="/css/header_enhanced.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'TS Language Learning Platform')</title>

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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
    <!-- Navigation - Modern Professional Design -->
    <nav class="navbar navbar-expand-lg navbar-premium sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="/logooo.png" alt="TS Language Platform" class="navbar-logo">
                <span class="navbar-brand-text">TS Language Platform</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @guest
                        <!-- Public navigation for non-authenticated users -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}"
                                href="{{ route('courses.index') }}">
                                Courses
                            </a>
                        </li>
                    @endguest

                    @auth
                        @if(auth()->user()->role === 'student')
                            <!-- Student navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                                    href="{{ route('student.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.status.*') ? 'active' : '' }}"
                                    href="{{ route('student.status.index') }}">
                                    <i class="bi bi-calendar-check me-1"></i>Tracker
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}"
                                    href="{{ route('student.attendance.index') }}">
                                    <i class="bi bi-check2-square me-1"></i>Attendance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}"
                                    href="{{ route('student.courses') }}">
                                    My Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.progress') ? 'active' : '' }}"
                                    href="{{ route('student.progress') }}">
                                    <i class="bi bi-graph-up me-1"></i>Progress
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.analytics') ? 'active' : '' }}"
                                    href="{{ route('student.analytics') }}">
                                    <i class="bi bi-bar-chart me-1"></i>Analytics
                                </a>
                            </li>

                        @elseif(auth()->user()->role === 'admin')
                            <!-- Admin navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                    href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"
                                    href="{{ route('admin.courses.index') }}">
                                    Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}"
                                    href="{{ route('admin.assignments.index') }}">
                                    <i class="bi bi-person-check me-1"></i>Assignments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people me-1"></i>Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.status.*') ? 'active' : '' }}"
                                    href="{{ route('admin.status.index') }}">
                                    <i class="bi bi-table me-1"></i>Reports
                                </a>
                            </li>
                        @elseif(auth()->user()->role === 'teacher')
                            <!-- Teacher navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"
                                    href="{{ route('teacher.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.courses') ? 'active' : '' }}"
                                    href="{{ route('teacher.courses') }}">
                                    My Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.students') ? 'active' : '' }}"
                                    href="{{ route('teacher.students') }}">
                                    <i class="bi bi-people me-1"></i>Students
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link nav-link-auth" href="{{ route('login') }}">
                                
                                Sign In
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning ms-2" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i>
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                id="userDropdownLink" onclick="toggleUserDropdown(event)" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    {{ Auth::user()->initials }}
                                </div>
                                <div class="d-none d-md-block">
                                    <div class="user-name">{{ Auth::user()->name }}</div>
                                    <small class="user-role">{{ ucfirst(Auth::user()->role) }}</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu">
                                <li class="dropdown-header">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            {{ Auth::user()->initials }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                            @push('scripts')
                                                <script>
                                                    // Plain JavaScript Dropdown Toggle
                                                    function toggleUserDropdown(e) {
                                                        e.preventDefault();
                                                        e.stopPropagation();
                                                        const menu = document.getElementById('userDropdownMenu');
                                                        const link = document.getElementById('userDropdownLink');

                                                        if (menu.classList.contains('show')) {
                                                            menu.classList.remove('show');
                                                            link.setAttribute('aria-expanded', 'false');
                                                        } else {
                                                            menu.classList.add('show');
                                                            link.setAttribute('aria-expanded', 'true');
                                                        }
                                                    }

                                                    // Close when clicking outside
                                                    document.addEventListener('click', function (e) {
                                                        const menu = document.getElementById('userDropdownMenu');
                                                        const link = document.getElementById('userDropdownLink');
                                                        if (menu && link && menu.classList.contains('show')) {
                                                            if (!link.contains(e.target) && !menu.contains(e.target)) {
                                                                menu.classList.remove('show');
                                                                link.setAttribute('aria-expanded', 'false');
                                                            }
                                                        }
                                                    });

                                                    // Filter functionality
                                                    document.querySelectorAll('.filter-tab').forEach(tab => {
                                                        tab.addEventListener('click', function () {
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
                                                    // Flash message helpers
                                                    window.showSuccessMessage = function (message) {
                                                        const container = document.getElementById('flash-messages');
                                                        if (!container) return;
                                                        const div = document.createElement('div');
                                                        div.className = 'alert alert-success alert-dismissible fade show';
                                                        div.role = 'alert';
                                                        div.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                                                        container.appendChild(div);
                                                    };
                                                    window.showErrorMessage = function (message) {
                                                        const container = document.getElementById('flash-messages');
                                                        if (!container) return;
                                                        const div = document.createElement('div');
                                                        div.className = 'alert alert-danger alert-dismissible fade show';
                                                        div.role = 'alert';
                                                        div.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                                                        container.appendChild(div);
                                                    };
                                                </script>
                                            @endpush
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                @if(auth()->user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                        </a></li>
                                @elseif(auth()->user()->role === 'student')
                                    <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i>Student Dashboard
                                        </a></li>
                                @elseif(auth()->user()->role === 'teacher')
                                    <li><a class="dropdown-item" href="{{ route('teacher.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i>Teacher Dashboard
                                        </a></li>
                                @endif
                                @if(auth()->user()->role === 'student')
                                    <li><a class="dropdown-item" href="{{ route('student.progress') }}">
                                            <i class="bi bi-graph-up me-2"></i>My Progress
                                        </a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
        <div id="flash-messages"></div>
    </main>

    <!-- Footer - Modern Professional Design -->
    <footer class="footer-modern mt-5">
        <div class="container">
            <div class="footer-content">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-brand mb-3">
                            <span class="footer-brand-text">TS Language Platform</span>
                        </div>
                        <p class="footer-description">Master French with our comprehensive online learning platform
                            designed for all levels.</p>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h6 class="footer-heading">LEARN</h6>
                        <ul class="footer-links">
                            <li><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li><a href="{{ route('student.dashboard') }}">Lessons</a></li>
                            <li><a href="{{ route('test') }}">Tests</a></li>
                            <li><a href="#">Resources</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h6 class="footer-heading">SUPPORT</h6>
                        <ul class="footer-links">
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Contact Us</a></li>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Community</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h6 class="footer-heading">COMPANY</h6>
                        <ul class="footer-links">
                            <li><a href="{{ route('about') }}">About Us</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Careers</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h6 class="footer-heading">NEWSLETTER</h6>
                        <p class="footer-newsletter-text">Stay updated with our latest courses and tips</p>
                        <form class="newsletter-form">
                            <input type="email" class="newsletter-input" placeholder="Enter email">
                            <button type="submit" class="newsletter-btn">
                                <span
                                    style="font-size: 0.7rem; font-weight: bold; text-transform: uppercase;">Subscribe</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-12 text-center">
                        <p class="footer-copyright">&copy; 2026 TS Language Platform. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Modern Professional Navigation */
        .navbar {
            padding: 0;
            transition: all 0.3s ease;
        }

        .navbar-premium {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 102, 204, 0.08);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }

        .navbar .container {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .navbar-logo {
            height: 120px;
            width: auto;
            max-width: none;
            object-fit: contain;
            display: block;
            filter: brightness(1.05);
        }

        .navbar-brand {
            padding: 0;
            margin-right: 2rem;
            color: #0F172A !important;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .navbar-brand-text {
            font-family: "Inter", sans-serif;
            font-weight: 700;
            letter-spacing: -0.01em;
            font-size: 1.125rem;
            white-space: nowrap;
            background: linear-gradient(135deg, #0066cc 0%, #00c4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-premium .nav-link {
            color: #64748B !important;
            font-weight: 500;
            font-size: 0.9375rem;
            padding: 0.625rem 1rem !important;
            border-radius: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0 0.125rem;
        }

        .navbar-premium .nav-link:hover {
            color: #0066cc !important;
            background: rgba(0, 102, 204, 0.06);
        }

        .navbar-premium .nav-link.active {
            color: #0066cc !important;
            background: rgba(0, 102, 204, 0.1);
            font-weight: 600;
        }

        .navbar-premium .nav-link i {
            font-size: 1rem;
            margin-right: 0.375rem;
        }

        /* User Avatar */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0066cc 0%, #00c4ff 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.025em;
        }

        .dropdown-toggle {
            padding: 0.5rem 0.875rem !important;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .dropdown-toggle:hover {
            background: rgba(0, 102, 204, 0.06);
        }

        .user-name {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.9375rem;
            line-height: 1.2;
        }

        .user-role {
            color: #64748B;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 10px 25px 0 rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.625rem 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.15s ease;
        }

        .dropdown-item:hover {
            background: rgba(0, 102, 204, 0.06);
            color: #0066cc;
        }

        .dropdown-item i {
            width: 20px;
            opacity: 0.7;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: rgba(0, 0, 0, 0.06);
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #0F172A;
        }

        .navbar-toggler {
            border: 2px solid rgba(0, 102, 204, 0.2) !important;
            border-radius: 10px;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.15);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%234F46E5' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        @media (max-width: 992px) {
            .navbar-brand-text {
            font-family: "Inter", sans-serif;
                display: none;
            }

            .navbar-logo {
                height: 90px;
            }
        }

        @media (max-width: 576px) {
            .navbar-logo {
                height: 75px;
            }

            .navbar {
                padding: 1rem 0;
            }
        }

        /* Modern Footer Styles */
        .footer-modern {
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
            border-top: 1px solid rgba(0, 102, 204, 0.08);
            padding: 4rem 0 2rem;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-logo {
            height: 90px;
            width: auto;
        }

        .footer-brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            color: #0F172A;
            margin: 0;
            background: linear-gradient(135deg, #0066cc 0%, #00c4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-description {
            color: #64748B;
            font-size: 0.9375rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .social-links {
            display: flex;
            gap: 0.75rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(0, 102, 204, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0066cc;
            transition: all 0.2s ease;
            font-size: 1.125rem;
        }

        .social-link:hover {
            background: linear-gradient(135deg, #0066cc 0%, #00c4ff 100%);
            color: white;
            transform: translateY(-2px);
        }

        .footer-heading {
            font-weight: 700;
            font-size: 0.9375rem;
            color: #0F172A;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #64748B;
            text-decoration: none;
            font-size: 0.9375rem;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: #0066cc;
        }

        .footer-newsletter-text {
            color: #64748B;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .newsletter-form {
            display: flex;
            gap: 0.5rem;
        }

        .newsletter-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0, 102, 204, 0.2);
            border-radius: 10px;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .newsletter-input:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .newsletter-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #0066cc 0%, #00c4ff 100%);
            border: none;
            border-radius: 10px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.25);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0, 102, 204, 0.08);
        }

        .footer-copyright,
        .footer-tagline {
            color: #64748B;
            font-size: 0.875rem;
            margin: 0;
        }

        .footer-tagline .bi-heart-fill {
            color: #EF4444 !important;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        @media (max-width: 768px) {
            .footer-modern {
                padding: 3rem 0 1.5rem;
            }

            .footer-bottom {
                margin-top: 2rem;
                text-align: center;
            }

            .footer-copyright,
            .footer-tagline {
                text-align: center !important;
                margin-bottom: 0.5rem;
            }
        }
    </style>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Flash message helpers
        window.showSuccessMessage = function (message) {
            const container = document.getElementById('flash-messages');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'alert alert-success alert-dismissible fade show';
            div.role = 'alert';
            div.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            container.appendChild(div);
        };
        window.showErrorMessage = function (message) {
            const container = document.getElementById('flash-messages');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'alert alert-danger alert-dismissible fade show';
            div.role = 'alert';
            div.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            container.appendChild(div);
        };
    </script>
</body>

</html>