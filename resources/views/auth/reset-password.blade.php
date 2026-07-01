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
            <h2 class="fw-bold mb-1">Reset Your Password</h2>
            </div>
            <form id="resetPasswordForm" class="space-y-3" method="POST" action="{{ route('reset-password.update') }}" autocomplete="off"> 
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Email Address</label>
                    <div class="input-icon-wrap">
                    <i data-lucide="mail" class="lucide-sm input-icon"></i>
                        <input id="forgot-email" data-encrypt="true" name="email" autocomplete="off" type="email" value="{{ $user->email }}" readonly class="form-control ps-9" placeholder="john@example.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">New Password <span class="text-danger">*</span></label>
                    <div class="input-eye-wrap">
                        <input type="password" data-encrypt="true" name="new_password" autocomplete="new-password" id="new_password" class="form-control" placeholder="••••••••">
                        <button type="button"  class="input-eye-btn toggle-password">
                            <i data-lucide="eye" class="lucide-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-eye-wrap">
                        <input type="password" data-encrypt="true" name="confirm_password" autocomplete="confirm-password" id="confirm_password" class="form-control" placeholder="••••••••">
                        <button type="button"  class="input-eye-btn toggle-password">
                            <i data-lucide="eye" class="lucide-sm"></i>
                        </button>
                    </div>
                </div>
                <p id="reset-error" class="text-center text-xs text-danger d-none"></p>
                <button id="changePasswordBtn" type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    <i data-lucide="shield-check" class="lucide-sm"></i>
                    <span id="otp-btn-text">Reset Password</span>
                </button>
                <div id="changePasswordAlert" class="d-none mt-3"></div>
                <div class="mb-3">
                    <div id="resetPasswordAlert" class="d-none"></div>
                </div>
                 <div class="mb-3">
                    <div class="progress mt-2">
                        <div id="passwordStrength" class="progress-bar" style="width:0%"></div>
                    </div>
                    <small id="strengthText" class="text-muted"></small>
                </div>
                <div class="mb-3">
                    <div class="password-policy card border-0 bg-light ">

                        <div class="card-body">

                            <h6 class="fw-semibold mb-3">
                                Password Requirements
                            </h6>

                            <ul class="list-unstyled mb-0">

                                <li id="rule-length">
                                    <span class="rule-icon">
                                        <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                        <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                    </span>
                                    Minimum 8 characters
                                </li>

                                <li id="rule-upper">
                                    <span class="rule-icon">
                                        <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                        <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                    </span>
                                    One uppercase letter
                                </li>

                                <li id="rule-lower">
                                    <span class="rule-icon">
                                        <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                        <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                    </span>
                                    One lowercase letter
                                </li>

                                <li id="rule-number">
                                    <span class="rule-icon">
                                        <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                        <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                    </span>
                                    One number
                                </li>

                                <li id="rule-special">
                                    <span class="rule-icon">
                                        <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                        <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                    </span>
                                    One special character
                                </li>

                            </ul>

                        </div>

                    </div>
                </div>
            </form>
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
<script src="{{ asset('assets/plugins/js/auth/reset.password.js') }}"></script>
@endpush
