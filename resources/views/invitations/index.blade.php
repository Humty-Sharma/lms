@extends('layouts.app', ['title' => 'Invitations &bull; ShortVerse'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1 tracking-tight">Organization Invitations</h2>
        <p class="text-muted mb-0 small">
            @if($user->isSuperAdmin())
                Create a new company account and send an invitation link to their Admin.
            @else
                Invite new team members or admins to join <strong>{{ $user->company->name }}</strong>.
            @endif
        </p>
    </div>
</div>

@if(session('invitation_url'))
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 14px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center text-success bg-success-subtle rounded-circle" style="width: 40px; height: 40px;">
                        <i class="bi bi-check2 fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Invitation Link Ready</h6>
                        <span class="text-muted small">Sent to <strong class="text-dark">{{ session('invited_email') }}</strong> &bull; Valid for 7 days</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ session('invitation_url') }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open Link
                    </a>
                </div>
            </div>

            <div class="input-group">
                <input type="text" class="form-control font-mono bg-light border-end-0 small" style="font-size: 0.85rem;" value="{{ session('invitation_url') }}" readonly>
                <button class="btn btn-primary px-3 fw-semibold d-flex align-items-center gap-1" type="button" onclick="copyToClipboard('{{ session('invitation_url') }}', this)">
                    <i class="bi bi-clipboard"></i>
                    <span>Copy Link</span>
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Invitation Form Card -->
<div class="card mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="mb-0 fw-bold fs-6">
            <i class="bi bi-send-plus text-primary me-2"></i>
            {{ $user->isSuperAdmin() ? 'Create Company & Invite Admin' : 'Invite User to ' . $user->company->name }}
        </h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('invitations.store') }}">
            @csrf

            <div class="row g-3">
                @if($user->isSuperAdmin())
                    <div class="col-12 col-md-5">
                        <label for="company_name" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">New Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Acme Technologies Inc." required>
                        @error('company_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="email" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">Admin Work Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="admin@acme.com" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <span>Issue Invite</span>
                        </button>
                    </div>
                @else
                    <div class="col-12 col-md-5">
                        <label for="email" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">Colleague Work Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="colleague@yourcompany.com" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="role" class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing: 0.05em;">Assigned Permission Level <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member (Can create & view own URLs)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Can create & view all company URLs)</option>
                        </select>
                        @error('role')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <span>Send Invitation</span>
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="card overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold fs-6">Sent Invitations</h5>
        <span class="badge bg-light text-secondary border font-mono small px-2 py-1">{{ $invitations->total() }} dispatched</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th scope="col">Invitee Email</th>
                    <th scope="col">Company Tenant</th>
                    <th scope="col">Assigned Role</th>
                    <th scope="col">Issued By</th>
                    <th scope="col">State</th>
                    <th scope="col">Expires</th>
                    <th scope="col" class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invitations as $invitation)
                    <tr>
                        <td class="fw-semibold font-mono" style="font-size: 0.875rem;">{{ $invitation->email }}</td>
                        <td>
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('companies.show', $invitation->company) }}" class="badge bg-light text-primary border text-decoration-none fw-medium px-2 py-1" title="View company details">
                                    <i class="bi bi-buildings me-1"></i>{{ $invitation->company->name }}
                                </a>
                            @else
                                <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                    {{ $invitation->company->name }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $invitation->role->value === 'admin' ? 'badge-role-admin' : 'badge-role-member' }}">
                                {{ $invitation->role->label() }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $invitation->inviter->name ?? 'System' }}</td>
                        <td>
                            @if($invitation->accepted_at)
                                <span class="badge bg-success-subtle text-success border-0 px-2 py-1"><i class="bi bi-check2-circle me-1"></i> Accepted</span>
                            @elseif($invitation->revoked_at)
                                <span class="badge bg-danger-subtle text-danger border-0 px-2 py-1"><i class="bi bi-x-circle me-1"></i> Revoked</span>
                            @elseif($invitation->expires_at->isPast())
                                <span class="badge bg-secondary-subtle text-secondary border-0 px-2 py-1"><i class="bi bi-clock-history me-1"></i> Expired</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border-0 px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                            @endif
                        </td>
                        <td class="small text-muted font-mono">{{ $invitation->expires_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            @if(! $invitation->accepted_at && ! $invitation->revoked_at)
                                <form method="POST" action="{{ route('invitations.revoke', $invitation) }}" class="d-inline" onsubmit="return confirm('Revoke this pending invitation?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Revoke Invitation">
                                        <i class="bi bi-x-circle me-1"></i> Revoke
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No invitation records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invitations->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
            {{ $invitations->links() }}
        </div>
    @endif
</div>
@endsection
