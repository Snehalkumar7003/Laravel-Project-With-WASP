<header class="app-header">
    <button class="header-icon-btn d-lg-none" data-toggle-mobile-sidebar aria-label="Menu"><i data-lucide="menu" class="lucide-md"></i></button>
    <button class="header-icon-btn d-none d-lg-flex" data-toggle-sidebar aria-label="Toggle"><i data-lucide="menu" class="lucide-md"></i></button>
    <div class="header-breadcrumb d-none d-md-flex" data-breadcrumb><span class="text-muted">Home</span></div>
    <div class="flex-grow-1"></div>
    <button class="header-icon-btn" data-toggle-dark aria-label="Dark"><i data-lucide="moon" class="lucide-md"></i></button>
    <div class="dropdown">
        <button class="user-button" data-bs-toggle="dropdown">
            <div class="avatar avatar-sm">{{ strtoupper(substr(session('username'), 0, 2)) }}</div>
            <div class="d-none d-sm-block text-start">
            <div class="user-name">{{session('username')}}</div>
            <div class="user-role">{{session('role_name')}}</div>
            </div>
            <i data-lucide="chevron-down" class="lucide-sm d-none d-sm-inline"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end user-menu">
            <div class="user-menu-header">
                <div class="fw-semibold">{{session('username')}}</div>
                <div class="text-muted small">{{ session('email') }}</div>
            </div>
            <a class="dropdown-item user-menu-item" href="#"><i data-lucide="help-circle" class="lucide-sm"></i> Help Center</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item user-menu-item text-danger" href="{{route('logout')}}"><i data-lucide="log-out" class="lucide-sm"></i> Sign Out</a>
        </div>
    </div>
</header>