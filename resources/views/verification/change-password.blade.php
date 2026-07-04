@extends('layouts.verification-layout')
@section('title', 'Change Password')
@section('content')

<!-- <div class="d-flex justify-content-center p-2"> -->
<div class="d-flex align-items-center justify-content-center p-4">
    <div class="w-100 max-w-420">
        <div class="card p-2">
            <div class="text-center">
                <h2 class="fw-bold mb-1">Change Password</h2>            
            </div>
            <form class="space-y-3" id="changePasswordForm" method="POST" action="{{ route('change-password.update') }}" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Current Password <span class="text-danger">*</span></label>
                    <div class="input-eye-wrap">
                        <input type="password" data-encrypt="true" name="password" id="pwd" class="form-control" placeholder="••••••••" autocomplete="current-password">
                        <button type="button"  class="input-eye-btn toggle-password">
                            <i data-lucide="eye" class="lucide-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">New Password <span class="text-danger">*</span></label>
                    <div class="input-eye-wrap">
                        <input type="password" data-encrypt="true" name="new_password" id="new_password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                        <button type="button"  class="input-eye-btn toggle-password">
                            <i data-lucide="eye" class="lucide-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-eye-wrap">
                        <input type="password" data-encrypt="true" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" autocomplete="confirm-password">
                        <button type="button"  class="input-eye-btn toggle-password">
                            <i data-lucide="eye" class="lucide-sm"></i>
                        </button>
                    </div>
                </div>
                <p id="otp-error" class="text-center text-xs text-danger d-none"></p>
                <button id="changePasswordBtn" type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    <i data-lucide="shield-check" class="lucide-sm"></i>
                    <span id="otp-btn-text">Update Password</span>
                </button>
                <div class="mb-3">
                    <div id="changePasswordAlert" class="d-none"></div>
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
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/js/auth/change.password.js') }}"></script>
@endpush

