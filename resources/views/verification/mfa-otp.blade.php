@extends('layouts.verification-layout')
@section('title', 'OTP Verification')
@section('content')

<div class="d-flex align-items-center justify-content-center p-4">
  <div class="w-100 max-w-420">
    
    <div class="card p-4">
      <div class="text-center mb-4">
        <div class="icon-circle icon-circle-xl icon-circle-bg-primary-soft mx-auto mb-3"><i data-lucide="shield-check" class="lucide-xl text-primary"></i></div>
        <h2 class="fw-bold mb-1">Two-Step Verification</h2>
        <p class="text-muted small">We sent a 6-digit verification code to <br><strong class="text-body">{{ session('email') }}</strong></p>
      </div>
      <form id="otp-form" class="space-y-3" method="POST" action="{{ route('otp.verify') }}" autocomplete="one-time-code">
        <input type="hidden" name="otp" data-encrypt='true' id="otp">
        <label class="form-label small fw-medium text-center d-block">Enter your 6-digit code</label>
        <div class="d-flex justify-content-center gap-2 mb-3" id="otp-inputs">
          <input class="form-control otp-input" maxlength="1" data-index="0" inputmode="numeric">
          <input class="form-control otp-input" maxlength="1" data-index="1" inputmode="numeric">
          <input class="form-control otp-input" maxlength="1" data-index="2" inputmode="numeric">
          <input class="form-control otp-input" maxlength="1" data-index="3" inputmode="numeric">
          <input class="form-control otp-input" maxlength="1" data-index="4" inputmode="numeric">
          <input class="form-control otp-input" maxlength="1" data-index="5" inputmode="numeric">
        </div>
        <p id="otp-error" class="text-center text-xs text-danger d-none"></p>
        <button id="otp-submit" type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
          <i data-lucide="shield-check" class="lucide-sm"></i>
          <span id="otp-btn-text">Verify Code</span>
        </button>
      </form>
      <div class="text-center mt-3 small">
        <p class="small text-danger d-none" id="otp-attempts">
            Attempts remaining:
            <span>5</span>
        </p>
        <p class="text-muted mb-1">
          Didn't receive the code?
          <button id="otp-resend" type="button" disabled class="btn btn-link btn-sm p-0 text-primary fw-medium d-inline-flex align-items-center gap-1">
            <i data-lucide="rotate-ccw" class="lucide-xs"></i> Resend 
          </button>
          <span id="otp-timer">in 05:00</span>
          <span id="otp-resent" class="text-success d-none">Code resent!</span>
        </p>
        
        <div id="alertBox" class="d-none"></div>
        <a href="{{ route('logout') }}" class="d-inline-flex align-items-center gap-2 text-muted text-decoration-none">
          <i data-lucide="arrow-left" class="lucide-sm"></i> Back to Login
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/js/auth/otp.js') }}"></script>
@endpush

