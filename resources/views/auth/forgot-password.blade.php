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
            <form id="forgot-form" class="space-y-3" method="POST" action="{{ route('forgot-password.send') }}">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Email Address</label>
                    <div class="input-icon-wrap">
                    <i data-lucide="mail" class="lucide-sm input-icon"></i>
                        <input id="forgot-email" data-encrypt="true" name="email" type="email" class="form-control ps-9" placeholder="john@example.com">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    <i data-lucide="send" class="lucide-sm"></i>
                    <span id="forgot-btn-text">Send Reset Link</span>
                </button>
                <div class="row">
                    <div class="col-md-12">
                       <div id="loginAlert" class="d-none"></div>
                        @if(session('error'))
                            <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-red-50 border border-red-200 text-red-800 mb-2">
                                <i data-lucide="x-circle" class="lucide-md shrink-0 mt-0-5 text-red-500"></i>

                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold mb-0-5">Error..!!!</p>
                                    <p class="opacity-80 leading-relaxed m-0">
                                        {{ session('error') }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onclick="this.parentElement.remove();"
                                    class="shrink-0 opacity-50 p-0-5 rounded bg-transparent border-0">
                                    <i data-lucide="x" class="lucide-sm"></i>
                                </button>
                            </div>
                        @endif
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
<script src="{{ asset('assets/plugins/js/auth/forgot.password.js') }}"></script>
@endpush
