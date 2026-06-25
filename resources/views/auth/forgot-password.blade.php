@extends('layouts.auth-layout')

@section('title', 'Login')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="w-100 max-w-420">
        <div class="text-center mb-4">
        <img src="{{ asset('assets/img/logo.svg') }}" alt="{{ config('app.name') }}" class="logo logo-lg mx-auto mb-3 logo-img">
            <h1 class="fs-4 fw-bold">{{ config('app.name') }}</h1>
        </div>

        <div class="card p-4">
        <div id="forgot-view">
            <div class="text-center mb-4">
            <div class="icon-circle icon-circle-xl icon-circle-bg-primary-soft mx-auto mb-3"><i data-lucide="mail" class="lucide-xl text-primary"></i></div>
            <h2 class="fw-bold mb-1">Forgot Password?</h2>
            <p class="text-muted small">No worries! Enter your email address and we'll send you a link to reset your password.</p>
            </div>
            <form id="forgot-form" class="space-y-3">
            <div class="mb-3">
                <label class="form-label small fw-medium">Email Address</label>
                <div class="input-icon-wrap">
                <i data-lucide="mail" class="lucide-sm input-icon"></i>
                <input id="forgot-email" type="email" class="form-control ps-9" placeholder="john@example.com" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                <i data-lucide="send" class="lucide-sm"></i>
                <span id="forgot-btn-text">Send Reset Link</span>
            </button>
            </form>
        </div>

        <div id="forgot-sent" class="text-center d-none">
            <div class="icon-circle icon-circle-xl icon-circle-bg-success-soft mx-auto mb-3"><i data-lucide="check-circle" class="lucide-xl text-success"></i></div>
            <h2 class="fw-bold mb-1">Check Your Email</h2>
            <p class="text-muted small mb-1">We've sent a password reset link to</p>
            <p class="fw-semibold text-primary mb-3" id="forgot-shown-email"></p>
            <p class="text-muted text-xs mb-4">
            Didn't receive the email? Check your spam folder or
            <button id="forgot-try-again" class="btn btn-link btn-sm p-0 text-primary">try again</button>
            </p>
        </div>

        <div class="text-center mt-3">
            <a href="{{route('login')}}" class="d-inline-flex align-items-center gap-2 small text-muted text-decoration-none">
            <i data-lucide="arrow-left" class="lucide-sm"></i> Back to Login
            </a>
        </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@endpush
