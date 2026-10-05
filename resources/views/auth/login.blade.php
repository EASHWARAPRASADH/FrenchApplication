@extends('layouts.app')

@section('title', 'Login - TS Language Platform')

@section('content')
    <style>
        /* Custom Login Page Styles */
        .login-wrapper {
            min-height: 85vh;
            /* Occupy most of the screen */
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.08), transparent 45%),
                radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.08), transparent 45%);
            padding: 4rem 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 28px;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-header {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.04) 0%, rgba(124, 58, 237, 0.04) 100%);
            padding: 3.5rem 2.5rem 2.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(79, 70, 229, 0.06);
        }

        .login-logo {
            width: 110px;
            height: auto;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 4px 6px rgba(79, 70, 229, 0.15));
            transition: transform 0.3s ease;
        }

        .glass-card:hover .login-logo {
            transform: scale(1.05) rotate(-2deg);
        }

        .login-title {
            font-family: "Playfair Display", serif;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
            font-size: 2.25rem;
        }

        .login-subtitle {
            font-family: "Inter", sans-serif;
            color: #64748b;
            font-size: 1.15rem;
            line-height: 1.6;
            max-width: 85%;
            margin: 0 auto;
        }

        .form-floating>.form-control {
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            padding-left: 1.25rem;
            height: 4rem;
            font-size: 1.1rem;
            background-color: #f8fafc;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-floating>.form-control:focus {
            border-color: #6366f1;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .form-floating>label {
            padding-left: 1.25rem;
            padding-top: 1.1rem;
            color: #94a3b8;
            font-size: 1rem;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
        }

        .btn-login {
            background: linear-gradient(135deg, #0066cc 0%, #7c3aed 100%);
            border: none;
            border-radius: 14px;
            font-weight: 700;
            padding: 1.25rem;
            letter-spacing: 0.025em;
            font-size: 1.15rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(79, 70, 229, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .quick-access-btn {
            background: white;
            border: 2px solid #f1f5f9;
            border-radius: 18px;
            padding: 1.25rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #64748b;
        }

        .quick-access-btn:hover {
            border-color: #e0e7ff;
            background: #eef2ff;
            color: #0066cc;
            transform: translateY(-3px);
        }

        .quick-access-btn.active {
            border-color: #6366f1;
            background: #eef2ff;
            color: #0066cc;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.2);
        }

        .quick-access-icon {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
            transition: transform 0.2s ease;
        }

        .quick-access-btn:hover .quick-access-icon {
            transform: scale(1.15);
        }

        .divider-text {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin: 2.5rem 0;
        }

        .divider-text::before,
        .divider-text::after {
            content: '';
            flex: 1;
            border-bottom: 2px solid #f1f5f9;
            height: 1px;
        }

        .divider-text::before {
            margin-right: 1.25rem;
        }

        .divider-text::after {
            margin-left: 1.25rem;
        }

        .custom-checkbox .form-check-input {
            width: 1.4em;
            height: 1.4em;
            border-color: #cbd5e1;
            border-radius: 0.4rem;
            cursor: pointer;
            margin-top: 0.1em;
        }

        .custom-checkbox .form-check-input:checked {
            background-color: #0066cc;
            border-color: #0066cc;
        }

        .custom-checkbox .form-check-label {
            font-size: 1rem;
            padding-left: 0.25rem;
            cursor: pointer;
            padding-top: 0.2rem;
        }

        .alert {
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }
    </style>

    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-6 col-xl-5">
                    <div class="glass-card">
                        <div class="login-header">
                            <img src="/logooo.png" alt="Logo" class="login-logo">
                            <h1 class="login-title">Welcome Back!</h1>
                            <p class="login-subtitle">Sign in to access your comprehensive French learning dashboard.</p>
                        </div>

                        <div class="card-body p-5 pt-0">
                            <div class="px-2 pb-2">
                                <!-- Role Alert -->
                                @if(request('role'))
                                    <div class="alert {{ request('role') === 'admin' ? 'alert-warning' : 'alert-info' }} d-flex align-items-center mb-4 border-0 shadow-sm bg-opacity-10 {{ request('role') === 'admin' ? 'bg-warning' : 'bg-info' }}"
                                        role="alert">
                                        <i
                                            class="bi {{ request('role') === 'admin' ? 'bi-shield-check' : 'bi-mortarboard' }} fs-5 me-3"></i>
                                        <div class="fs-6">
                                            Signing in as <strong>{{ ucfirst(request('role')) }}</strong>
                                        </div>
                                    </div>
                                @endif

                                @if (session('status'))
                                    <div class="alert alert-success border-0 shadow-sm mb-4 bg-success bg-opacity-10 text-success"
                                        role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Email Input -->
                                    <div class="form-floating mb-4">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                                        <label for="email">Email Address</label>
                                        @error('email')
                                            <div class="text-danger small mt-2 ms-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Password Input -->
                                    <div class="form-floating mb-4">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Password" required>
                                        <label for="password">Password</label>
                                        @error('password')
                                            <div class="text-danger small mt-2 ms-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-5 custom-checkbox">
                                        <div class="form-check d-flex align-items-start">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember_me">
                                            <label class="form-check-label text-secondary" for="remember_me">
                                                Keep me logged in
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}"
                                                class="text-decoration-none text-primary fw-semibold hover-underline">Forgot
                                                Password?</a>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-login w-100 text-white shadow-lg mb-4">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                                    </button>

                                    <div class="text-center">
                                        <p class="text-secondary mb-0" style="font-size: 1.05rem;">
                                            Don't have an account?
                                            <a href="{{ route('register') }}"
                                                class="text-primary fw-bold text-decoration-none ms-1">Create Account</a>
                                        </p>
                                    </div>
                                </form>

                                <div class="divider-text">Quick Access</div>

                                <!-- Quick Access Roles -->
                                <div class="row g-3">
                                    <div class="col-6">
                                        <a href="{{ route('login') }}?role=student"
                                            class="quick-access-btn {{ request('role') === 'student' ? 'active' : '' }}">
                                            <i class="bi bi-mortarboard quick-access-icon"></i>
                                            <span class="fw-bold fs-6">Student</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('login') }}?role=admin"
                                            class="quick-access-btn {{ request('role') === 'admin' ? 'active' : '' }}">
                                            <i class="bi bi-shield-check quick-access-icon"></i>
                                            <span class="fw-bold fs-6">Admin</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection