
<header class="app-header">
  <button class="header-icon-btn d-lg-none" data-toggle-mobile-sidebar aria-label="Menu"><i data-lucide="menu" class="lucide-md"></i></button>
  <button class="header-icon-btn d-none d-lg-flex" data-toggle-sidebar aria-label="Toggle"><i data-lucide="menu" class="lucide-md"></i></button>
  <div class="header-breadcrumb d-none d-md-flex" data-breadcrumb>
    <span class="text-muted">Home</span>
  </div>
  <div class="flex-grow-1"></div>
  <div class="search-wrap d-none d-sm-block">
    <input type="text" class="form-control form-control-sm search-input w-200" placeholder="Search...">
    <i data-lucide="search" class="lucide-sm search-icon"></i>
  </div>
  <button class="header-icon-btn" data-toggle-dark aria-label="Dark"><i data-lucide="moon" class="lucide-md"></i></button>
  <div class="dropdown">
    <button class="header-icon-btn" data-bs-toggle="dropdown" aria-label="Notifications">
      <i data-lucide="bell" class="lucide-md"></i>
      <span class="notif-dot">3</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end notif-menu">
      <div class="notif-header">
        <strong>Notifications</strong>
        <span class="badge badge-soft-primary">3 new</span>
      </div>
      <div class="notif-list custom-scrollbar">
        <div class="notif-item unread">
          <div class="icon-circle icon-circle-sm icon-circle-bg-success-soft"><i data-lucide="check-circle-2" class="lucide-sm"></i></div>
          <div class="flex-grow-1">
            <div class="notif-title">Order Completed</div>
            <div class="notif-msg">Order #1234 has been fulfilled</div>
            <div class="notif-time">2 min ago</div>
          </div>
        </div>
        <div class="notif-item unread">
          <div class="icon-circle icon-circle-sm icon-circle-bg-info-soft"><i data-lucide="info" class="lucide-sm"></i></div>
          <div class="flex-grow-1">
            <div class="notif-title">New User Registered</div>
            <div class="notif-msg">Sarah Johnson joined the platform</div>
            <div class="notif-time">15 min ago</div>
          </div>
        </div>
        <div class="notif-item">
          <div class="icon-circle icon-circle-sm icon-circle-bg-warning-soft"><i data-lucide="alert-circle" class="lucide-sm"></i></div>
          <div class="flex-grow-1">
            <div class="notif-title">Low Stock Alert</div>
            <div class="notif-msg">Wireless Mouse is running low</div>
            <div class="notif-time">1 hr ago</div>
          </div>
        </div>
      </div>
      <div class="notif-footer">
        <a href="#" class="text-primary fw-medium notif-footer-link">View all notifications</a>
      </div>
    </div>
  </div>
  <button class="header-icon-btn" data-toggle-settings aria-label="Settings"><i data-lucide="settings" class="lucide-md"></i></button>
  <div class="dropdown">
    <button class="user-button" data-bs-toggle="dropdown">
      <div class="avatar avatar-sm">
        @if (session('profile_photo')!='')        
          <img src="{{ asset('uploads/profile/' . session('profile_photo')) }}" alt="User Profile">
        @else
          {{ strtoupper(substr(session('username'), 0, 2)) }}
        @endif
      </div>
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
      <a class="dropdown-item user-menu-item" href="{{ route('profile') }}"><i data-lucide="user" class="lucide-sm"></i> My Profile</a>
      <a class="dropdown-item user-menu-item" href="{{ route('profile') }}"><i data-lucide="shield" class="lucide-sm"></i> Security</a>
      <a class="dropdown-item user-menu-item" href=""><i data-lucide="help-circle" class="lucide-sm"></i> Help Center</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item user-menu-item text-danger" href="{{ route('logout') }}"><i data-lucide="log-out" class="lucide-sm"></i> Sign Out</a>
    </div>
  </div>
</header>