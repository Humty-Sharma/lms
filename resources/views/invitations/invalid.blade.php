@extends('layouts.app', ['title' => 'Invalid Invitation - ShortVerse'])

@section('content')
<div class="row justify-content-center pt-md-5">
    <div class="col-12 col-md-6 text-center">
        <div class="card p-5 border-0 shadow-sm">
            <div class="text-danger mb-3">
                <i class="bi bi-exclamation-octagon fs-1"></i>
            </div>
            <h3 class="fw-bold mb-2">Invalid or Expired Invitation</h3>
            <p class="text-muted">
                This invitation link is invalid, has expired, or has already been used to create an account.
            </p>
            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Return to Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

