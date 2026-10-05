@php
    $content = file_get_contents(public_path('index-next.html'));
    if ($content !== false) {
        // Map Next.js links to Laravel routes
        $replacements = [
            'href="/"' => 'href="' . route('home') . '"',
            'href="/about/"' => 'href="' . route('home') . '#why"',
            'href="/media-library/"' => 'href="' . route('home') . '#platform"',
            'href="/certification/"' => 'href="' . route('home') . '#levels"',
            'href="/membership/"' => 'href="' . route('register') . '"',
            'href="/contact/"' => 'href="' . route('home') . '#footer"',
            'href="/auth/sign-in/"' => 'href="' . route('login') . '"',
            'href="/auth/register/"' => 'href="' . route('register') . '"',
            'href="/courses/french/"' => 'href="' . route('courses.index') . '"',
            'href="/exams/orientation-test/"' => 'href="' . route('home') . '#process"',
        ];
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        echo $content;
        exit; // Ensure the rest of this Blade file does not render
    }
@endphp

@extends('layouts.app')

@section('title', 'TS Language Learning Platform - Master French Online')

@section('content')

<!-- Hero Section - Minimalistic -->
<section class="hero-section scroll-section" data-scroll-section id="hero">
    <!-- Aesthetic Blurred Background -->
    <div class="hero-bg-blur">
        <div class="hero-bg-image-1"></div>
        <div class="hero-bg-image-2"></div>
        <div class="hero-bg-image-3"></div>
    </div>

    <div class="container position-relative">
        <div class="row align-items-center min-vh-100">
            <div class="col-12 text-center hero-content">
                <div class="hero-text-wrapper">
                    <div class="hero-brand reveal-text">
                        TS Language Platform
                    </div>
                    <h1 class="hero-title reveal-text">
                        Master French with our<br>comprehensive online<br>learning platform.
                    </h1>
                    <div class="hero-buttons reveal-buttons mt-5">
                        <a href="{{ route('register') }}" class="btn-minimal btn-minimal-dark">
                            Start Learning
                        </a>
                        <a href="{{ route('courses.index') }}" class="btn-minimal btn-minimal-light">
                            Browse Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimal Scroll Indicator -->
    <div class="scroll-indicator">
        <div class="scroll-line"></div>
    </div>
</section>

<!-- Statistics Section - Minimalistic -->
<section class="stats-section scroll-section" data-scroll-section id="stats">
    <div class="container py-5">
        <div class="row text-center g-5">
            <div class="col-md-3 col-6">
                <div class="stat-minimal scroll-reveal">
                    <h2 class="stat-number-minimal" data-target="{{ $stats['total_students'] }}">0</h2>
                    <p class="stat-label">Students</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-minimal scroll-reveal">
                    <h2 class="stat-number-minimal" data-target="{{ $stats['total_courses'] }}">0</h2>
                    <p class="stat-label">Courses</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-minimal scroll-reveal">
                    <h2 class="stat-number-minimal" data-target="{{ $stats['total_lessons'] }}">0</h2>
                    <p class="stat-label">Lessons</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-minimal scroll-reveal">
                    <h2 class="stat-number-minimal" data-target="{{ $stats['total_enrollments'] }}">0</h2>
                    <p class="stat-label">Completions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Section 1 - Minimalistic -->
<section class="feature-section scroll-section" data-scroll-section id="feature-1">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 scroll-reveal">
                <div class="feature-image-wrapper">
                    <div class="feature-image" style="background-image: url('https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=1200&q=80');"></div>
                </div>
            </div>
            <div class="col-lg-6 scroll-reveal">
                <div class="feature-content">
                    <h2 class="feature-title">Interactive Learning</h2>
                    <p class="feature-description">Experience modern French education with native speakers and proven methods. Our platform combines cutting-edge technology with traditional teaching excellence.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Section 2 - Reversed -->
<section class="feature-section feature-section-alt scroll-section" data-scroll-section id="feature-2">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 order-lg-2 scroll-reveal">
                <div class="feature-image-wrapper">
                    <div class="feature-image" style="background-image: url('https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?w=1200&q=80');"></div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 scroll-reveal">
                <div class="feature-content">
                    <h2 class="feature-title">Native Speakers</h2>
                    <p class="feature-description">Learn from experienced French teachers who bring authentic language and culture to every lesson. Perfect your accent and fluency.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Section 3 -->
<section class="feature-section scroll-section" data-scroll-section id="feature-3">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 scroll-reveal">
                <div class="feature-image-wrapper">
                    <div class="feature-image" style="background-image: url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1200&q=80');"></div>
                </div>
            </div>
            <div class="col-lg-6 scroll-reveal">
                <div class="feature-content">
                    <h2 class="feature-title">Track Progress</h2>
                    <p class="feature-description">Monitor your improvement with detailed analytics and personalized feedback. Set goals and achieve fluency at your own pace.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-section scroll-section" data-scroll-section id="why">
    <div class="container">
        <div class="text-center mb-5 scroll-reveal">
            <h2 class="section-title-minimal">Why Choose Us</h2>
        </div>
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <div class="why-card scroll-reveal">
                    <div class="why-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h4 class="why-title">Certified Teachers</h4>
                    <p class="why-description">All instructors are native French speakers with teaching certifications</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="why-card scroll-reveal">
                    <div class="why-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h4 class="why-title">Flexible Schedule</h4>
                    <p class="why-description">Learn at your own pace with 24/7 access to course materials</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="why-card scroll-reveal">
                    <div class="why-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="why-title">Proven Results</h4>
                    <p class="why-description">95% of students achieve fluency within 12 months</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="why-card scroll-reveal">
                    <div class="why-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4 class="why-title">Community</h4>
                    <p class="why-description">Join a global community of French language learners</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses Section - Minimalistic -->
@if($featuredCourses->count() > 0)
<section class="courses-section scroll-section py-5" data-scroll-section id="courses">
    <div class="container">
        <div class="text-center mb-5 scroll-reveal">
            <h2 class="section-title-minimal">Featured Courses</h2>
            <p class="section-subtitle-minimal">Start your French learning journey with our most popular courses</p>
        </div>
        <div class="row g-4">
            @foreach($featuredCourses as $index => $course)
                <div class="col-lg-4 col-md-6">
                    <div class="course-card-new scroll-reveal">
                        <div class="course-image-wrapper">
                            <div class="course-image" style="background-image: url('https://images.unsplash.com/photo-{{ ['1499856871958-5b9627545d1a', '1502602898657-3e91760cbb34', '1511739001486-6bfe10ce785f', '1546410531-bb4caa6b424d', '1488190211105-8b0e65b80b4e', '1434030216411-0b793f4b4173'][$index % 6] }}?w=600&q=80');"></div>
                            <div class="course-overlay">
                                @if($course->price == 0)
                                    <span class="course-badge">Free</span>
                                @else
                                    <span class="course-badge">${{ $course->price }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="course-content-new">
                            <div class="course-level-badge">{{ ucfirst($course->level) }}</div>
                            <h5 class="course-title-new">{{ $course->title }}</h5>
                            <p class="course-description-new">{{ Str::limit($course->description, 100) }}</p>
                            <div class="course-meta-new">
                                <div class="course-meta-item">
                                    <i class="bi bi-person"></i>
                                    <span>{{ $course->teacher->name }}</span>
                                </div>
                                <div class="course-meta-item">
                                    <i class="bi bi-clock"></i>
                                    <span>{{ $course->duration_hours }}h</span>
                                </div>
                            </div>
                            <a href="{{ route('courses.show', $course) }}" class="course-btn-new">
                                Enroll Now
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-5 scroll-reveal">
            <a href="{{ route('courses.index') }}" class="btn-minimal btn-minimal-dark">
                View All Courses
            </a>
        </div>
    </div>
</section>
@endif

<!-- Process Section - Minimalistic -->
<section class="process-section scroll-section py-5" data-scroll-section id="process">
    <div class="container">
        <div class="text-center mb-5 scroll-reveal">
            <h2 class="section-title-minimal">How It Works</h2>
        </div>
        <div class="row g-5">
            <div class="col-md-4">
                <div class="process-step scroll-reveal">
                    <div class="process-number">01</div>
                    <h4 class="process-title">Sign Up</h4>
                    <p class="process-description">Create your account and set your goals</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="process-step scroll-reveal">
                    <div class="process-number">02</div>
                    <h4 class="process-title">Learn</h4>
                    <p class="process-description">Access lessons and track progress</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="process-step scroll-reveal">
                    <div class="process-number">03</div>
                    <h4 class="process-title">Master</h4>
                    <p class="process-description">Achieve fluency and earn certificates</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Learning Levels Section -->
<section class="levels-section scroll-section" data-scroll-section id="levels">
    <div class="container">
        <div class="text-center mb-5 scroll-reveal">
            <h2 class="section-title-minimal">All Levels Welcome</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="level-card scroll-reveal">
                    <div class="level-badge">A1-A2</div>
                    <h3 class="level-title">Beginner</h3>
                    <p class="level-description">Start your French journey from scratch. Learn basic vocabulary, grammar, and everyday phrases.</p>
                    <ul class="level-features">
                        <li>Alphabet & Pronunciation</li>
                        <li>Basic Conversations</li>
                        <li>Essential Grammar</li>
                        <li>Common Phrases</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="level-card level-card-featured scroll-reveal">
                    <div class="level-badge">B1-B2</div>
                    <h3 class="level-title">Intermediate</h3>
                    <p class="level-description">Build confidence and fluency. Engage in complex conversations and understand native speakers.</p>
                    <ul class="level-features">
                        <li>Advanced Grammar</li>
                        <li>Fluent Conversations</li>
                        <li>Reading Comprehension</li>
                        <li>Writing Skills</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="level-card scroll-reveal">
                    <div class="level-badge">C1-C2</div>
                    <h3 class="level-title">Advanced</h3>
                    <p class="level-description">Master French like a native. Perfect your accent, idioms, and professional communication.</p>
                    <ul class="level-features">
                        <li>Native Fluency</li>
                        <li>Business French</li>
                        <li>Cultural Nuances</li>
                        <li>Professional Writing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section - Minimalistic -->
<section class="testimonials-section scroll-section py-5" data-scroll-section id="testimonials">
    <div class="container">
        <div class="text-center mb-5 scroll-reveal">
            <h2 class="section-title-minimal">Testimonials</h2>
        </div>
        <div class="row g-4">
            @foreach($testimonials as $index => $testimonial)
                <div class="col-lg-4">
                    <div class="testimonial-minimal scroll-reveal">
                        <p class="testimonial-text-minimal">"{{ $testimonial['content'] }}"</p>
                        <div class="testimonial-author-minimal">
                            <p class="author-name-minimal">{{ $testimonial['name'] }}</p>
                            <p class="author-role-minimal">{{ $testimonial['role'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Platform Features Section -->
<section class="platform-section scroll-section" data-scroll-section id="platform">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-12">
                <div class="platform-image-wrapper scroll-reveal">
                    <div class="platform-image"></div>
                    <div class="platform-overlay">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 text-center">
                                    <h2 class="platform-title">Modern Learning Platform</h2>
                                    <p class="platform-description">Access your courses anywhere, anytime. Our platform works seamlessly across all devices.</p>
                                    <div class="platform-features-grid">
                                        <div class="platform-feature">
                                            <i class="bi bi-laptop"></i>
                                            <span>Desktop</span>
                                        </div>
                                        <div class="platform-feature">
                                            <i class="bi bi-tablet"></i>
                                            <span>Tablet</span>
                                        </div>
                                        <div class="platform-feature">
                                            <i class="bi bi-phone"></i>
                                            <span>Mobile</span>
                                        </div>
                                        <div class="platform-feature">
                                            <i class="bi bi-cloud"></i>
                                            <span>Cloud</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section - Minimalistic -->
<section class="cta-section scroll-section" data-scroll-section id="cta">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-12 text-center scroll-reveal">
                <h2 class="cta-title-minimal">Start Learning Today</h2>
                <div class="cta-buttons-minimal mt-5">
                    <a href="{{ route('register') }}" class="btn-minimal btn-minimal-dark">
                        Get Started
                    </a>
                    <a href="{{ route('login') }}" class="btn-minimal btn-minimal-light">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">Learn</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li><a href="{{ route('courses.index') }}">Lessons</a></li>
                    <li><a href="{{ route('courses.index') }}">Tests</a></li>
                    <li><a href="{{ route('courses.index') }}">Support</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">Help Center</h5>
                <ul class="footer-links">
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">Company</h5>
                <ul class="footer-links">
                    <li><a href="#">About</a></li>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Terms</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-heading">TS Language Platform</h5>
                <p class="footer-description">Master French with our comprehensive online learning platform.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="footer-copyright">© 2025 TS Language Platform. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-tagline">Made with <span class="footer-heart">♥</span> for language learners</p>
                </div>
            </div>
        </div>
    </div>
</footer>

@push('styles')
<style>
    /* ===== MINIMALISTIC BLACK & WHITE DESIGN ===== */
    :root {
        --black: #000000;
        --white: #ffffff;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
    }

    * {
        scroll-behavior: smooth;
    }

    html, body {
        overflow-x: hidden;
        background: var(--white);
        color: var(--black);
    }

    /* ===== HERO SECTION ===== */
    .hero-section {
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        position: relative;
        overflow: hidden;
    }

    /* Aesthetic Blurred Background */
    .hero-bg-blur {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
    }

    .hero-bg-image-1,
    .hero-bg-image-2,
    .hero-bg-image-3 {
        position: absolute;
        background-size: cover;
        background-position: center;
        filter: grayscale(100%) blur(60px);
        opacity: 0.15;
        animation: float 20s ease-in-out infinite;
    }

    .hero-bg-image-1 {
        width: 500px;
        height: 500px;
        top: -100px;
        right: -100px;
        background-image: url('https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80');
        border-radius: 50%;
        animation-delay: 0s;
    }

    .hero-bg-image-2 {
        width: 600px;
        height: 600px;
        bottom: -150px;
        left: -150px;
        background-image: url('https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80');
        border-radius: 50%;
        animation-delay: 7s;
    }

    .hero-bg-image-3 {
        width: 400px;
        height: 400px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-image: url('https://images.unsplash.com/photo-1511739001486-6bfe10ce785f?w=800&q=80');
        border-radius: 50%;
        animation-delay: 14s;
    }

    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        33% {
            transform: translate(30px, -30px) scale(1.1);
        }
        66% {
            transform: translate(-30px, 30px) scale(0.9);
        }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-brand {
        font-size: clamp(0.75rem, 1.5vw, 1rem);
        font-weight: 400;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--gray-600);
        margin-bottom: 2rem;
    }

    .hero-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 300;
        letter-spacing: -0.02em;
        line-height: 1.3;
        color: var(--black);
        margin: 0;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* ===== MINIMAL BUTTONS ===== */
    .btn-minimal {
        display: inline-block;
        padding: 1.125rem 3rem;
        font-size: 0.875rem;
        font-weight: 400;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-decoration: none;
        border: 1px solid;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-minimal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--black);
        transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: -1;
    }

    .btn-minimal-dark {
        background: var(--black);
        color: var(--white);
        border-color: var(--black);
    }

    .btn-minimal-dark:hover {
        background: var(--gray-900);
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        letter-spacing: 0.15em;
    }

    .btn-minimal-light {
        background: transparent;
        color: var(--black);
        border-color: var(--black);
        z-index: 1;
    }

    .btn-minimal-light::before {
        background: var(--black);
    }

    .btn-minimal-light:hover {
        color: var(--white);
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        letter-spacing: 0.15em;
    }

    .btn-minimal-light:hover::before {
        left: 0;
    }

    /* ===== SCROLL INDICATOR ===== */
    .scroll-indicator {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }

    .scroll-line {
        width: 1px;
        height: 60px;
        background: var(--black);
        position: relative;
        animation: scrollLine 2s ease-in-out infinite;
    }

    .scroll-line::after {
        content: '';
        position: absolute;
        width: 1px;
        height: 20px;
        background: var(--black);
        top: 0;
        left: 0;
        animation: scrollDot 2s ease-in-out infinite;
    }

    @keyframes scrollLine {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; }
    }

    @keyframes scrollDot {
        0% { transform: translateY(0); }
        100% { transform: translateY(40px); opacity: 0; }
    }

    /* ===== STATISTICS SECTION ===== */
    .stats-section {
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .stat-minimal {
        text-align: center;
        padding: 3rem 0;
        position: relative;
    }

    .stat-minimal::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 1px;
        background: var(--gray-300);
        opacity: 0;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-minimal:hover::after {
        opacity: 1;
        width: 60px;
    }

    .stat-number-minimal {
        font-size: clamp(3rem, 6vw, 5rem);
        font-weight: 200;
        letter-spacing: -0.03em;
        color: var(--black);
        margin: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-minimal:hover .stat-number-minimal {
        transform: scale(1.05);
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 400;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--gray-500);
        margin-top: 1rem;
    }

    /* ===== FEATURE SECTIONS ===== */
    .feature-section {
        background: var(--gray-100);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .feature-section-alt {
        background: var(--white);
    }

    .feature-image-wrapper {
        width: 100%;
        height: 600px;
        overflow: hidden;
        background: var(--black);
        position: relative;
    }

    .feature-image {
        width: 100%;
        height: 100%;
        background: url('https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=1200&q=80') center/cover no-repeat;
        filter: grayscale(100%) contrast(1.1);
        opacity: 0.7;
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .feature-image-wrapper:hover .feature-image {
        transform: scale(1.08);
        opacity: 0.85;
    }

    .feature-content {
        padding: 4rem 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .feature-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 200;
        letter-spacing: -0.03em;
        line-height: 1.1;
        color: var(--black);
        margin-bottom: 2rem;
    }

    .feature-description {
        font-size: 1.125rem;
        line-height: 1.8;
        color: var(--gray-600);
        max-width: 500px;
        font-weight: 300;
    }

    /* ===== WHY CHOOSE US SECTION ===== */
    .why-section {
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .why-card {
        text-align: center;
        padding: 2rem 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .why-card:hover {
        transform: translateY(-5px);
    }

    .why-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--gray-300);
        border-radius: 50%;
        font-size: 2rem;
        color: var(--black);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .why-card:hover .why-icon {
        border-color: var(--black);
        transform: rotate(360deg);
    }

    .why-title {
        font-size: 1.25rem;
        font-weight: 400;
        letter-spacing: -0.01em;
        color: var(--black);
        margin-bottom: 1rem;
    }

    .why-description {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--gray-600);
        font-weight: 300;
    }

    /* ===== COURSES SECTION ===== */
    .courses-section {
        background: var(--gray-100);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .section-title-minimal {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 200;
        letter-spacing: -0.03em;
        color: var(--black);
        margin: 0 0 1rem 0;
    }

    .section-subtitle-minimal {
        font-size: 1.125rem;
        font-weight: 300;
        color: var(--gray-600);
        margin-bottom: 3rem;
    }

    /* New Course Card Design */
    .course-card-new {
        background: var(--white);
        border: 1px solid var(--gray-300);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .course-card-new:hover {
        border-color: var(--black);
        transform: translateY(-10px);
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.12);
    }

    .course-image-wrapper {
        position: relative;
        width: 100%;
        height: 250px;
        overflow: hidden;
        background: var(--gray-200);
    }

    .course-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        filter: grayscale(100%);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .course-card-new:hover .course-image {
        transform: scale(1.1);
        filter: grayscale(0%);
    }

    .course-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 100%);
        display: flex;
        align-items: flex-end;
        padding: 1.5rem;
    }

    .course-badge {
        background: var(--white);
        color: var(--black);
        padding: 0.5rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 400;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .course-content-new {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .course-level-badge {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: var(--gray-100);
        color: var(--gray-700);
        font-size: 0.7rem;
        font-weight: 400;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        width: fit-content;
    }

    .course-title-new {
        font-size: 1.5rem;
        font-weight: 400;
        letter-spacing: -0.02em;
        color: var(--black);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .course-description-new {
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--gray-600);
        margin-bottom: 1.5rem;
        flex-grow: 1;
        font-weight: 300;
    }

    .course-meta-new {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--gray-200);
    }

    .course-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--gray-600);
    }

    .course-meta-item i {
        font-size: 1rem;
        color: var(--gray-500);
    }

    .course-btn-new {
        display: block;
        width: 100%;
        padding: 1rem;
        background: var(--black);
        color: var(--white);
        text-align: center;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 400;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--black);
    }

    .course-btn-new:hover {
        background: var(--white);
        color: var(--black);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* ===== PROCESS SECTION ===== */
    .process-section {
        background: var(--gray-100);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .process-step {
        text-align: center;
        padding: 3rem 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .process-step:hover {
        transform: translateY(-5px);
    }

    .process-number {
        font-size: 4rem;
        font-weight: 100;
        letter-spacing: -0.03em;
        color: var(--gray-300);
        margin-bottom: 2rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .process-step:hover .process-number {
        color: var(--black);
        transform: scale(1.1);
    }

    .process-title {
        font-size: 1.75rem;
        font-weight: 300;
        letter-spacing: -0.02em;
        color: var(--black);
        margin-bottom: 1.25rem;
    }

    .process-description {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--gray-600);
        max-width: 320px;
        margin: 0 auto;
        font-weight: 300;
    }

    /* ===== LEARNING LEVELS SECTION ===== */
    .levels-section {
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .level-card {
        background: var(--white);
        border: 1px solid var(--gray-300);
        padding: 3rem 2rem;
        height: 100%;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .level-card:hover {
        border-color: var(--black);
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    }

    .level-card-featured {
        background: var(--black);
        color: var(--white);
        border-color: var(--black);
    }

    .level-card-featured .level-badge {
        background: var(--white);
        color: var(--black);
    }

    .level-card-featured .level-title,
    .level-card-featured .level-description {
        color: var(--white);
    }

    .level-card-featured .level-features {
        color: var(--gray-300);
    }

    .level-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        background: var(--black);
        color: var(--white);
        font-size: 0.75rem;
        font-weight: 400;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        margin-bottom: 2rem;
    }

    .level-title {
        font-size: 1.75rem;
        font-weight: 300;
        letter-spacing: -0.02em;
        color: var(--black);
        margin-bottom: 1.25rem;
    }

    .level-description {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--gray-600);
        margin-bottom: 2rem;
        font-weight: 300;
    }

    .level-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .level-features li {
        font-size: 0.9rem;
        color: var(--gray-600);
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--gray-200);
        letter-spacing: 0.02em;
    }

    .level-features li:last-child {
        border-bottom: none;
    }

    .level-features li::before {
        content: '—';
        margin-right: 0.75rem;
        color: var(--gray-400);
    }

    /* ===== TESTIMONIALS SECTION ===== */
    .testimonials-section {
        background: var(--gray-100);
        border-bottom: 1px solid var(--gray-200);
        padding: 5rem 0;
    }

    .testimonial-minimal {
        background: var(--white);
        border: 1px solid var(--gray-300);
        padding: 3rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .testimonial-minimal::before {
        content: '"';
        position: absolute;
        top: 1.5rem;
        left: 2rem;
        font-size: 6rem;
        font-weight: 100;
        color: var(--gray-200);
        line-height: 1;
        font-family: Georgia, serif;
    }

    .testimonial-minimal:hover {
        border-color: var(--black);
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    }

    .testimonial-text-minimal {
        font-size: 1.125rem;
        line-height: 1.8;
        color: var(--black);
        margin-bottom: 2.5rem;
        flex-grow: 1;
        font-weight: 300;
        font-style: italic;
        position: relative;
        z-index: 1;
    }

    .testimonial-author-minimal {
        padding-top: 1.75rem;
        border-top: 1px solid var(--gray-200);
    }

    .author-name-minimal {
        font-size: 0.9rem;
        font-weight: 400;
        color: var(--black);
        margin: 0 0 0.5rem 0;
        letter-spacing: 0.02em;
    }

    .author-role-minimal {
        font-size: 0.8rem;
        color: var(--gray-500);
        margin: 0;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    /* ===== PLATFORM SECTION ===== */
    .platform-section {
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
    }

    .platform-image-wrapper {
        position: relative;
        height: 80vh;
        min-height: 600px;
        overflow: hidden;
    }

    .platform-image {
        width: 100%;
        height: 100%;
        background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&q=80') center/cover no-repeat;
        filter: grayscale(100%) contrast(1.1);
        opacity: 0.3;
    }

    .platform-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(2px);
    }

    .platform-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 200;
        letter-spacing: -0.03em;
        color: var(--black);
        margin-bottom: 1.5rem;
    }

    .platform-description {
        font-size: 1.25rem;
        line-height: 1.7;
        color: var(--gray-600);
        margin-bottom: 3rem;
        font-weight: 300;
    }

    .platform-features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .platform-feature {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border: 1px solid var(--gray-300);
        background: var(--white);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .platform-feature:hover {
        border-color: var(--black);
        transform: translateY(-5px);
    }

    .platform-feature i {
        font-size: 2rem;
        color: var(--black);
    }

    .platform-feature span {
        font-size: 0.8rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--gray-600);
    }

    /* ===== CTA SECTION ===== */
    .cta-section {
        background: var(--black);
        color: var(--white);
        border-bottom: none;
        padding: 8rem 0;
    }

    .cta-title-minimal {
        font-size: clamp(3rem, 8vw, 6rem);
        font-weight: 200;
        letter-spacing: -0.03em;
        line-height: 1;
        color: var(--white);
        margin: 0;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .cta-title-minimal:hover {
        letter-spacing: -0.01em;
    }

    .cta-buttons-minimal {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .cta-section .btn-minimal-dark {
        background: var(--white);
        color: var(--black);
        border-color: var(--white);
    }

    .cta-section .btn-minimal-dark::before {
        background: var(--gray-200);
    }

    .cta-section .btn-minimal-dark:hover {
        background: var(--gray-100);
        box-shadow: 0 15px 40px rgba(255, 255, 255, 0.2);
    }

    .cta-section .btn-minimal-light {
        background: transparent;
        color: var(--white);
        border-color: var(--white);
    }

    .cta-section .btn-minimal-light::before {
        background: var(--white);
    }

    .cta-section .btn-minimal-light:hover {
        color: var(--black);
        box-shadow: 0 15px 40px rgba(255, 255, 255, 0.15);
    }

    /* ===== FOOTER SECTION ===== */
    .footer-section {
        background: var(--white);
        border-top: 1px solid var(--gray-200);
        padding: 5rem 0 2rem;
    }

    .footer-heading {
        font-size: 0.875rem;
        font-weight: 400;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--black);
        margin-bottom: 1.5rem;
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
        font-size: 0.95rem;
        color: var(--gray-600);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 300;
    }

    .footer-links a:hover {
        color: var(--black);
        padding-left: 5px;
    }

    .footer-description {
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--gray-600);
        font-weight: 300;
    }

    .footer-bottom {
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid var(--gray-200);
    }

    .footer-copyright,
    .footer-tagline {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
        font-weight: 300;
    }

    .footer-heart {
        color: var(--black);
        display: inline-block;
        animation: heartbeat 1.5s ease-in-out infinite;
    }

    @keyframes heartbeat {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    /* ===== SCROLL REVEAL ANIMATIONS ===== */
    .scroll-reveal {
        opacity: 0;
        transform: translateY(40px);
        transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1),
                    transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .scroll-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-text {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1),
                    transform 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .reveal-text.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-buttons {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1) 0.2s,
                    transform 1s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
    }

    .reveal-buttons.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 992px) {
        .feature-content {
            padding: 2rem 0;
        }

        .feature-image-wrapper {
            height: 400px;
            margin-bottom: 2rem;
        }
    }

    @media (max-width: 768px) {
        .hero-bg-image-1 {
            width: 350px;
            height: 350px;
            top: -50px;
            right: -50px;
        }

        .hero-bg-image-2 {
            width: 400px;
            height: 400px;
            bottom: -100px;
            left: -100px;
        }

        .hero-bg-image-3 {
            width: 300px;
            height: 300px;
        }

        .btn-minimal {
            padding: 0.875rem 2rem;
            font-size: 0.875rem;
        }

        .feature-image-wrapper {
            height: 300px;
            margin-bottom: 2rem;
        }

        .course-image-wrapper {
            height: 200px;
        }

        .course-content-new {
            padding: 1.5rem;
        }

        .testimonial-minimal {
            padding: 2rem;
        }

        .process-step {
            padding: 1.5rem 0;
        }

        .hero-buttons,
        .cta-buttons-minimal {
            flex-direction: column;
        }

        .why-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .platform-features-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .platform-feature {
            padding: 1rem;
        }

        .platform-image-wrapper {
            height: 60vh;
            min-height: 500px;
        }

        .level-card {
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .footer-section {
            padding: 3rem 0 1.5rem;
        }

        .footer-bottom {
            margin-top: 2rem;
        }
    }

    @media (max-width: 576px) {
        .hero-bg-image-1 {
            width: 250px;
            height: 250px;
            top: -30px;
            right: -30px;
        }

        .hero-bg-image-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            left: -50px;
        }

        .hero-bg-image-3 {
            width: 200px;
            height: 200px;
        }

        .hero-buttons,
        .cta-buttons-minimal {
            width: 100%;
        }

        .btn-minimal {
            width: 100%;
            text-align: center;
        }

        .stat-number-minimal {
            font-size: 2.5rem;
        }

        .feature-image-wrapper {
            height: 250px;
        }

        .platform-features-grid {
            grid-template-columns: 1fr;
        }

        .platform-image-wrapper {
            height: 50vh;
            min-height: 400px;
        }

        .platform-title {
            font-size: 2rem;
        }

        .platform-description {
            font-size: 1rem;
        }
    }

    /* ===== PERFORMANCE OPTIMIZATIONS ===== */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    /* Hardware acceleration */
    .scroll-reveal,
    .feature-image,
    .course-card-minimal,
    .testimonial-minimal {
        transform: translateZ(0);
        backface-visibility: hidden;
    }
</style>
@endpush
@push('scripts')
<script>
    // Minimalistic Scroll Animations
    document.addEventListener('DOMContentLoaded', function() {

        // ===== INTERSECTION OBSERVER FOR SCROLL REVEALS =====
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                } else {
                    entry.target.classList.remove('is-visible');
                }
            });
        }, observerOptions);

        // Observe all reveal elements
        document.querySelectorAll('.scroll-reveal, .reveal-text, .reveal-buttons').forEach(el => {
            observer.observe(el);
        });

        // ===== COUNTER ANIMATIONS =====
        const statNumbers = document.querySelectorAll('.stat-number-minimal');
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    const target = parseInt(entry.target.getAttribute('data-target')) || 0;
                    const duration = 2000;
                    const start = 0;
                    const increment = target / (duration / 16);
                    let current = 0;

                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            entry.target.textContent = target.toLocaleString();
                            clearInterval(timer);
                        } else {
                            entry.target.textContent = Math.ceil(current).toLocaleString();
                        }
                    }, 16);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => counterObserver.observe(stat));

        // ===== SCROLL INDICATOR FADE =====
        window.addEventListener('scroll', () => {
            const scrollIndicator = document.querySelector('.scroll-indicator');
            if (scrollIndicator) {
                const scrolled = window.scrollY;
                const opacity = Math.max(0, 1 - (scrolled / 500));
                scrollIndicator.style.opacity = opacity;
            }
        });

        // ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>
@endpush

@endsection
