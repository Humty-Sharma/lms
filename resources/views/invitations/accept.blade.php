@extends('layouts.app', ['title' => 'Accept Invitation &bull; ShortVerse'])

@section('content')
<div class="row justify-content-center pt-md-4">
    <div class="col-12 col-md-7 col-lg-6 col-xl-5">
        <div class="card shadow-sm border-0" style="border-radius: 18px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08) !important;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-person-check-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold tracking-tight mb-1">Set Up Your Account</h3>
                    <p class="text-muted small">You've been invited to join <strong>{{ $invitation->company->name }}</strong></p>
                </div>

                <div class="card bg-light border p-3 mb-4" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Email:</span>
                        <span class="font-mono small fw-semibold text-dark">{{ $invitation->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Company:</span>
                        <span class="small fw-bold text-dark">{{ $invitation->company->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Assigned Role:</span>
                        <span class="{{ $invitation->role->value === 'admin' ? 'badge-role-admin' : 'badge-role-member' }}">
                            {{ $invitation->role->label() }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('invitations.accept.process', ['token' => $token]) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ada Lovelace" required autofocus>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password">
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="background: #16a34a; border-color: #16a34a;">
                        <i class="bi bi-check-circle"></i>
                        <span>Activate & Access Workspace</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
