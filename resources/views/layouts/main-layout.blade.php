<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="X-CSRF-TOKEN">
    <meta name="csrf-token-hash" content="{{ csrf_token() }}">
    <meta name="app-base-url" content="{{ url('/') }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/app.js') }}"></script>
</head>


<body>
    @include('partials.sidebar')
    @include('partials.header')
    @include('partials.theme-setting')
    <main class="app-main space-y-6">
        @yield('content')
    </main>
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/lucide.min.js') }}"></script>
    <script src="{{ asset('assets/js/jsencrypt.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/additional-methods.min.js') }}"></script>
    
    <script src="{{ asset('assets/plugins/js/config/app.config.js') }}"></script>
    <script src="{{ asset('assets/plugins/js/security/security.js') }}"></script>
    <script src="{{ asset('assets/plugins/js/security/device-fingerprint.js') }}"></script>
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
     {{-- Page specific JS --}}
    @stack('scripts')
</body>
</html>
