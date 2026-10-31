@extends('layouts.app', ['title' => 'Profile Settings &bull; ShortVerse'])

@section('content')
<div class="mb-4">
    <h2 class="h3 fw-bold mb-1 tracking-tight">Account & Security</h2>
    <p class="text-muted small mb-0">Manage your profile details and update your password</p>
</div>

<div class="row g-4">
    <!-- Profile Details Card -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="stat-icon-wrapper" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                        <i class="bi bi-person-badge fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Profile Information</h5>
                        <p class="text-muted small mb-0">Update your account name and email address</p>
                    </div>
                </div>

                @if(session('status_profile'))
                    <div class="alert alert-success d-flex align-items-center small py-2 px-3 mb-4" role="alert" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 10px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span>{{ session('status_profile') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">FULL NAME</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Work Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">EMAIL ADDRESS</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role & Company Info (Read-only for security) -->
                    <div class="p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Assigned Role:</span>
                            <span class="{{ $user->isSuperAdmin() ? 'badge-role-superadmin' : ($user->isAdmin() ? 'badge-role-admin' : 'badge-role-member') }}">
                                {{ $user->role->label() }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Tenant Company:</span>
                            <span class="small fw-bold text-dark">{{ $user->isSuperAdmin() ? 'Platform-wide (All Tenants)' : $user->company->name }}</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary fw-semibold px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Update Card -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="stat-icon-wrapper" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Update Password</h5>
                        <p class="text-muted small mb-0">Ensure your account is using a secure password</p>
                    </div>
                </div>

                @if(session('status_password'))
                    <div class="alert alert-success d-flex align-items-center small py-2 px-3 mb-4" role="alert" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 10px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span>{{ session('status_password') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">CURRENT PASSWORD</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password" placeholder="••••••••">
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">NEW PASSWORD</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">CONFIRM NEW PASSWORD</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat new password">
                    </div>

                    <button type="submit" class="btn btn-dark fw-semibold px-4" style="background: #0f172a; border-radius: 10px;">
                        <i class="bi bi-key me-1"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

