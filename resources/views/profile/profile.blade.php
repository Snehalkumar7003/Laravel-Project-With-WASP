@extends('layouts.main-layout')
@section('title', 'Profile')
@section('content')
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-500">Manage your profile, security, and notification preferences</p>
        </div>
    </div>
    <div class="acc-tabs">
        <button class="acc-tab acc-tab-active" data-tab="profile"><i data-lucide="user" class="lucide-sm"></i>Profile</button>
        <button class="acc-tab" data-tab="security"><i data-lucide="shield" class="lucide-sm"></i>Security</button>        
        <button class="acc-tab" data-tab="recent-activity"><i data-lucide="clock" class="lucide-sm"></i>Recent Activity</button>        
    </div>

    <div class="acc-pane" data-pane="profile">        
        <div class="space-y-6">
            <div class="grid grid-cols-1 grid-cols-xl-3 gap-6">
                <div class="xl-col-span-2 space-y-6">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                        <div class="card p-6">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="acc-sec-icon bg-primary-50">
                                    <i data-lucide="user" class="lucide-md text-primary-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-800 m-0">Personal Information</h3>
                                    <p class="text-xs text-slate-500 m-0">Update your personal details and public profile</p>
                                </div>
                            </div>                        
                            <div class="grid grid-cols-1 grid-cols-sm-2 gap-4 mb-5">
                                <div>                     
                                    <div class="flex items-center gap-4 mb-6 pb-6 border-b">
                                        <div class="relative shrink-0" data-fl-avatar>      
                                            <div class="fl-avatar-box">                     
                                                <div class="fl-avatar-empty {{ isset($user->profile_photo) ? 'hidden' : '' }}">
                                                    <i data-lucide="user" class="lucide-lg text-slate-400"></i>
                                                </div>
                                                <img class="fl-avatar-img {{ isset($user->profile_photo) ? '' : 'hidden' }}" {{ isset($user->profile_photo) ? 'src=' . asset('uploads/profile/' . $user->profile_photo) . '' : '' }} alt="avatar">
                                            </div>
                                            <input type="file" name="profile_image" accept="image/*" class="fe-sr-only" data-fl-avatar-input>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700 m-0">Profile Photo</p>
                                            <p class="text-xs text-slate-500 mt-0-5 m-0">JPG, PNG or GIF. Max 2MB.</p>
                                            <button type="button" class="mt-2 text-xs font-medium text-primary-600" data-fl-avatar-btn>
                                                Upload new photo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1-5">Username</label>
                                    <div class="relative">
                                        <i data-lucide="user" class="lucide-xs acc-input-icon"></i>
                                        <input type="text" name="username" id="username" class="input input1 acc-input-pl7" value="{{ $user->username }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1-5">Email Address</label>
                                    <div class="relative">
                                        <i data-lucide="mail" class="lucide-xs acc-input-icon"></i>
                                        <input type="email" name="email" id="email" readonly data-encrypt="true" class="input input1 acc-input-pl9" value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1-5">Phone Number</label>
                                    <div class="relative">
                                        <i data-lucide="phone" class="lucide-xs acc-input-icon"></i>
                                        <input type="tel" name="mobile" id="mobile" class="input input1 acc-input-pl9" data-encrypt="true" value="{{ $user->mobile }}">
                                    </div>
                                </div>                            
                            </div>
                            <div class="flex gap-3 mb-3"><button  type="submit" class="btn-primary"><i data-lucide="save" class="lucide-sm"></i>Save Changes</button><button class="btn-outline">Cancel</button></div>                            
                            <div id="profileAlert" class="d-none"></div>
                        </div>
                    </form>                
                </div>
            </div>
        </div>
    </div>
    <!-- ===== SECURITY TAB ===== -->
    <div class="acc-pane hidden" data-pane="security">
        <div class="grid grid-cols-1 grid-cols-xl-3 gap-6">
            <div class="xl-col-span-2 space-y-6">
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="acc-sec-icon bg-primary-50">
                            <i data-lucide="key" class="lucide-md text-primary-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 m-0">Change Password</h3>
                            <p class="text-xs text-slate-500 m-0">Last changed 30 days ago</p>
                        </div>
                    </div>
                    <form class="space-y-3" id="changePasswordForm" method="POST" action="{{ route('change-password.update') }}" autocomplete="off">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1-5">Current Password</label>
                                <div class="relative">
                                    <input type="password" data-encrypt="true" name="password" class="input acc-input-pr10" placeholder="Enter your current password">
                                    <button class="acc-pwd-toggle"><i data-lucide="eye" class="lucide-sm"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1-5">New Password</label>
                                <div class="relative">
                                    <input type="password" data-encrypt="true" name="new_password" id="new_password" class="input acc-input-pr10" placeholder="Enter a strong new password">
                                    <button class="acc-pwd-toggle"><i data-lucide="eye" class="lucide-sm"></i></button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1-5">Confirm New Password</label>
                                <div class="relative">
                                    <input type="password" data-encrypt="true" name="confirm_password" id="confirm_password" class="input acc-input-pr10" placeholder="Re-enter the new password">
                                    <button class="acc-pwd-toggle"><i data-lucide="eye" class="lucide-sm"></i></button>
                                </div>
                            </div>
                            <div id="acc-strength" class="hidden">
                                <div class="flex items-center justify-between mb-1-5">
                                    <span class="text-xs font-medium text-slate-600">Password Strength</span>
                                    <span class="text-xs font-semibold" id="acc-strength-label"></span>
                                </div>
                                <div class="flex gap-1">
                                    <div class="acc-strength-bar" data-bar="1"></div>
                                    <div class="acc-strength-bar" data-bar="2"></div>
                                    <div class="acc-strength-bar" data-bar="3"></div>
                                    <div class="acc-strength-bar" data-bar="4"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div id="changePasswordAlert" class="d-none"></div>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50">
                                <p class="text-xs font-semibold text-slate-600 mb-2-5 m-0">Password Requirements</p>
                                <div class="grid grid-cols-1 grid-cols-sm-2 gap-1-5">
                                    <p class="text-xs text-slate-500 flex items-center gap-2 m-0" id="rule-length">
                                        <span class="rule-icon">
                                            <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                            <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                        </span>
                                        Minimum 8 characters
                                    </p>
                                    <p class="text-xs text-slate-500 flex items-center gap-2 m-0" id="rule-upper">
                                        <span class="rule-icon">
                                            <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                            <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                        </span>
                                        At least one uppercase letter
                                    </p>
                                    <p class="text-xs text-slate-500 flex items-center gap-2 m-0" id="rule-lower">
                                        <span class="rule-icon">
                                            <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                            <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                        </span>
                                        At least one lowercase letter
                                    </p>
                                    <p class="text-xs text-slate-500 flex items-center gap-2 m-0" id="rule-number">
                                        <span class="rule-icon">
                                            <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                            <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                        </span>
                                        At least one number
                                    </p>
                                    <p class="text-xs text-slate-500 flex items-center gap-2 m-0" id="rule-special">
                                        <span class="rule-icon">
                                            <i class="rule-fail text-danger" data-lucide="x-circle"></i>
                                            <i class="rule-pass d-none text-success" data-lucide="check-circle"></i>
                                        </span>
                                        At least one special character
                                    </p>
                                </div>
                            </div>
                            <button class="btn-primary" type="submit" id="changePasswordBtn">
                                <i data-lucide="shield" class="lucide-sm"></i>
                                <span id="otp-btn-text">Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="acc-pane hidden" data-pane="recent-activity">
        <div class="grid grid-cols-1 grid-cols-xl-3 gap-6">
            <div class="xl-col-span-2 space-y-6">
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4 m-0">
                        Recent Activity
                    </h3>
                    <div class="activity-timeline" id="activityTimeline">
                    </div>
                    <div id="activityLoader" class="text-center py-3 d-none">
                        <i class="fa fa-spinner fa-spin"></i>
                        Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- required scripts here -->
 <script src="{{ asset('assets/plugins/js/profile/profile.js') }}"></script>
 <script src="{{ asset('assets/plugins/js/profile/profile.activity.js') }}"></script>
 <script src="{{ asset('assets/plugins/js/auth/change.password.js') }}"></script>
@endpush