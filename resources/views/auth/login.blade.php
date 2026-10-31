@extends('layouts.app', ['title' => 'Sign In &bull; ShortVerse'])

@section('content')
<div class="d-flex justify-content-center align-items-center py-4">
    <div class="auth-card-wrapper" style="width: 100%; max-width: 440px;">
        <div class="card border-0" style="background: #ffffff; border-radius: 24px; box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.05); padding: 2.5rem 2.25rem;">
            
            <!-- Top Shield Icon -->
            <div class="text-center mb-3">
                <div class="d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: #0f172a; color: #ffffff; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);">
                    <i class="bi bi-shield-fill fs-5"></i>
                </div>
            </div>

            <!-- Header Titles -->
            <div class="text-center mb-4">
                <h3 class="fw-bold mb-1" style="font-size: 1.75rem; letter-spacing: -0.03em; color: #0f172a;">Welcome back</h3>
                <p class="text-muted small mb-0" style="font-size: 0.9rem;">Sign in to your multi-tenant workspace</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Work Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.06em;">WORK EMAIL</label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                            <i class="bi bi-envelope fs-6"></i>
                        </span>
                        <input type="email" class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="developer@company.com" style="border-radius: 0 12px 12px 0; border-color: #e2e8f0; font-size: 0.925rem; height: 46px; color: #334155;">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1 ps-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.06em;">PASSWORD</label>
                    <div class="input-group custom-input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3 pe-2" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                            <i class="bi bi-key fs-6"></i>
                        </span>
                        <input type="password" class="form-control border-start-0 ps-1 @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password" placeholder="••••••••" style="border-radius: 0 12px 12px 0; border-color: #e2e8f0; font-size: 0.925rem; height: 46px; color: #334155;">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1 ps-1" style="font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Keep me signed in -->
                <div class="mb-4 form-check d-flex align-items-center gap-2 ps-0">
                    <input type="checkbox" class="form-check-input mt-0" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} style="width: 17px; height: 17px; border-radius: 5px; border-color: #cbd5e1; cursor: pointer;">
                    <label class="form-check-label text-muted" for="remember" style="font-size: 0.865rem; cursor: pointer; user-select: none;">Keep me signed in on this device</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2 text-white" style="background: #4f46e5; border-radius: 12px; height: 48px; font-size: 0.95rem; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35); transition: all 0.2s ease;">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <!-- Quick 1-Click Demo Login Card for Reviewer / Evaluator -->
            <div class="mt-4 p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                        <i class="bi bi-shield-check text-primary"></i> DEMO CREDENTIALS
                    </span>
                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" onclick="fillSuperAdmin()" style="font-size: 0.72rem; border-radius: 6px;">
                        Autofill
                    </button>
                </div>
                <div class="d-flex flex-column gap-1 small text-muted font-mono" style="font-size: 0.75rem;">
                    <div class="d-flex justify-content-between">
                        <span>Email:</span>
                        <span class="text-dark fw-semibold">superadmin@example.com</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Password:</span>
                        <span class="text-dark fw-semibold">Password123!</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Disclaimer / Help Note -->
            <div class="mt-3 pt-3 border-top text-center" style="border-color: #f1f5f9 !important;">
                <p class="text-muted mb-0 small lh-base d-flex align-items-start justify-content-center gap-2" style="font-size: 0.825rem; color: #64748b;">
                    <i class="bi bi-info-circle flex-shrink-0 mt-1" style="font-size: 0.85rem;"></i>
                    <span>New users join via secure invitation links provided by their Company Admin or SuperAdmin.</span>
                </p>
            </div>

        </div>
    </div>
</div>

<style>
    .custom-input-group:focus-within .input-group-text,
    .custom-input-group:focus-within .form-control {
        border-color: #4f46e5 !important;
    }
    .custom-input-group .form-control:focus {
        box-shadow: none !important;
    }
    .custom-input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        border-radius: 12px;
    }
    .btn:hover {
        background: #4338ca !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45) !important;
    }
    .btn:active {
        transform: translateY(0);
    }
</style>

<script>
    function fillSuperAdmin() {
        document.getElementById('email').value = 'superadmin@example.com';
        document.getElementById('password').value = 'Password123!';
    }
</script>
@endsection
