@extends('layouts.app')

@section('title', 'Teacher Account Pending Approval - TS Language School')

@section('content')
<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 text-center">
            <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                <div class="mb-4">
                    <span class="badge bg-warning-subtle text-warning p-3 rounded-circle" style="font-size: 2.5rem;">
                        <i class="bi bi-clock-history"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-3">Account Pending Approval</h3>
                <p class="text-muted mb-4 leading-relaxed">
                    Thank you for applying to be an instructor at TS Language School, <strong>{{ $user->name }}</strong>. 
                    Your teacher profile is currently under review by our administration. Once approved, you will have access to create and manage courses.
                </p>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill py-2">
                        <i class="bi bi-house me-2"></i>Return to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
