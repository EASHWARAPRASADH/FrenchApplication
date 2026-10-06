@extends('layouts.app')

@section('title', 'Reset Password - TS Language Platform')

@section('content')
    <style>
        /* Custom Forgot Password Page Styles - Matching Login/Register */
        .auth-wrapper {
            min-height: 85vh;
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

        .auth-header {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.04) 0%, rgba(124, 58, 237, 0.04) 100%);
            padding: 3rem 2rem 2rem;
            text-align: center;
            border-bottom: 1px solid rgba(79, 70, 229, 0.06);
        }

        .auth-title {
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .auth-subtitle {
            color: #64748b;
            font-size: 1.1rem;
            line-height: 1.6;
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

        .btn-primary-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 14px;
            font-weight: 700;
            padding: 1.25rem;
            letter-spacing: 0.025em;
            font-size: 1.15rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            color: white;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(79, 70, 229, 0.5);
            color: white;
        }
    </style>

    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-6 col-xl-5">
                    <div class="glass-card">
                        <div class="auth-header">
                            <img src="/ts_logo.png" alt="Logo" style="width: 80px; margin-bottom: 1rem;">
                            <h1 class="auth-title">Reset Password</h1>
                            <p class="auth-subtitle">
                                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                            </p>
                        </div>

                        <div class="card-body p-4 p-md-5 pt-2">
                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="alert alert-success border-0 shadow-sm mb-4 bg-success bg-opacity-10 text-success rounded-3"
                                    role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger border-0 shadow-sm mb-4 bg-danger bg-opacity-10 text-danger rounded-3"
                                    role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <!-- Email Address -->
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

                                <button type="submit" class="btn btn-primary-gradient shadow-lg mb-4">
                                    <i class="bi bi-envelope-fill me-2"></i> Email Password Reset Link
                                </button>

                                <div class="text-center">
                                    <a href="{{ route('login') }}" class="text-secondary text-decoration-none fw-medium">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Login
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection