@extends('layouts.auth-layout')

@section('title', 'Login')

@section('content')

<div class="auth-min-vh d-flex">
    <div class="flex-grow-1 d-flex align-items-center justify-content-center p-4 p-md-5 bg-white">
        <div class="w-100 max-w-420">
            @if(session('error'))
            <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-red-50 border border-red-200 text-red-800 mb-2">
                <i data-lucide="x-circle" class="lucide-md shrink-0 mt-0-5 text-red-500"></i>

                <div class="flex-1 min-w-0">
                    <p class="font-semibold mb-0-5">Authentication Error</p>
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
            @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-xl text-sm bg-emerald-50 border border-emerald-200 text-emerald-800 mb-2">
                <i data-lucide="check-circle" class="lucide-md shrink-0 mt-0-5 text-emerald-500"></i>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold mb-0-5">
                        Success...!!!
                    </p>
                        {{ session('success') }}
                    </p>
                </div>
                <button type="button" onclick="this.parentElement.remove();" class="shrink-0 opacity-50 p-0-5 rounded bg-transparent border-0">
                    <i data-lucide="x" class="lucide-sm"></i>
                </button>
            </div>
            @endif
            <div class="auth-brand justify-content-start mb-4">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="{{ config('app.name') }}" class="logo logo-img">
            <span class="fs-3 fw-bold text-primary">
                {{ config('app.name') }}
            </span>
            </div>
            <h1 class="fw-bold mb-2 fs-2">Welcome back</h1>
            <p class="text-muted mb-4">Sign in to your account to continue</p>
            <form id="loginForm" method="POST" action="{{ route('validate-login') }}">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Email Address</label>
                    <input type="email" data-encrypt="true" name="username" class="form-control" placeholder="you@example.com">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <label class="form-label small fw-medium mb-0">Password</label>
                        <a href="{{ route('forgot-password') }}" class="small text-primary">Forgot password?</a>
                    </div>
                    <div class="input-eye-wrap">
                        <input type="password" data-encrypt="true" name="password" id="pwd" class="form-control" placeholder="••••••••">
                        <button type="button" id="eye-btn" class="input-eye-btn">
                            <i data-lucide="eye" class="lucide-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small" for="remember">Remember me for 30 days</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    Sign In <i data-lucide="arrow-right" class="lucide-sm"></i>
                </button>
                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1">
                        <span class="px-3 text-muted small"> Copyright &copy; {{ date('Y') }} <a href="{{ config('app.company_website') }}" class="text-decoration-none" target="_blank"> {{ config('app.company_name') }} </a> </span>
                    <hr class="flex-grow-1">
                </div>
                <div class="row">
                    <div class="col-md-12">
                       <div id="loginAlert" class="d-none"></div>
                    </div>
                </div>                
            </form>            
        </div>
    </div>
        <div class="d-none d-xl-flex flex-grow-1 align-items-center justify-content-center p-5 auth-right-wrapper bg-gradient-auth-right">
        <div class="blob-wrap"><div class="blob-1"></div><div class="blob-2"></div></div>
        <div class="auth-right-content">
            <div class="row g-3 mb-4">
            <div class="col-4"><div class="glass-tile"><div class="fs-3 mb-1">👥</div><div class="fw-bold">24,521</div><div class="text-xs opacity-75">Total Users</div></div></div>
            <div class="col-4"><div class="glass-tile"><div class="fs-3 mb-1">💰</div><div class="fw-bold">$48.3K</div><div class="text-xs opacity-75">Revenue</div></div></div>
            <div class="col-4"><div class="glass-tile"><div class="fs-3 mb-1">📦</div><div class="fw-bold">1,429</div><div class="text-xs opacity-75">Orders</div></div></div>
            <div class="col-4"><div class="glass-tile"><div class="fs-3 mb-1">📈</div><div class="fw-bold">3.24%</div><div class="text-xs opacity-75">Conversion</div></div></div>
            <div class="col-4"><div class="glass-tile"><div class="fs-3 mb-1">⭐</div><div class="fw-bold">4.8/5</div><div class="text-xs opacity-75">Satisfaction</div></div></div>
            <div class="col-4"><div class="glass-tile"><div class="fs-3 mb-1">⚡</div><div class="fw-bold">99.98%</div><div class="text-xs opacity-75">Uptime</div></div></div>
            </div>
            <h2 class="fw-bold mb-2">The Premium Admin Dashboard</h2>
            <p class="opacity-75 small">Liner is a beautiful, fully-featured admin dashboard template built with Bootstrap 5. 60+ pages, dark mode, RTL support, and a powerful settings panel.</p>
            <div class="d-flex flex-wrap gap-3 mt-3">
            <span class="d-inline-flex align-items-center gap-2 small"><span class="icon-circle icon-circle-sm bg-success"><i data-lucide="check" class="lucide-xs"></i></span> Dark Mode</span>
            <span class="d-inline-flex align-items-center gap-2 small"><span class="icon-circle icon-circle-sm bg-success"><i data-lucide="check" class="lucide-xs"></i></span> RTL Support</span>
            <span class="d-inline-flex align-items-center gap-2 small"><span class="icon-circle icon-circle-sm bg-success"><i data-lucide="check" class="lucide-xs"></i></span> 60+ Pages</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/js/auth/login.js') }}"></script>
@endpush
