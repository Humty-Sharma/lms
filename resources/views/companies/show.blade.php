@extends('layouts.app', ['title' => $company->name . ' &bull; Tenant Overview'])

@section('content')
<!-- Header & Back Navigation -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Back to Companies">
            <i class="bi bi-arrow-left fs-6"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="h3 fw-bold mb-0 tracking-tight">{{ $company->name }}</h2>
                <span class="badge bg-light text-muted border font-mono small">ID: #{{ $company->id }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">Tenant provisioned on {{ $company->created_at->format('M d, Y - H:i') }}</p>
        </div>
    </div>

    <div>
        <a href="{{ route('invitations.index') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-send-plus"></i>
            <span>Send Tenant Invite</span>
        </a>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Company Members</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($company->users_count) }}</div>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Short URLs</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($company->short_urls_count) }}</div>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                    <i class="bi bi-link-45deg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Invitations</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($company->invitations_count) }}</div>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(100, 116, 139, 0.1); color: #475569;">
                    <i class="bi bi-envelope"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Organization Details -->
<ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="companyTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active px-3 py-2 fw-semibold" id="users-tab" data-bs-toggle="pill" data-bs-target="#users" type="button" role="tab">
            <i class="bi bi-people me-1"></i> Team Members ({{ $users->total() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-3 py-2 fw-semibold" id="urls-tab" data-bs-toggle="pill" data-bs-target="#urls" type="button" role="tab">
            <i class="bi bi-link-45deg me-1"></i> Short URLs ({{ $shortUrls->total() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-3 py-2 fw-semibold" id="invites-tab" data-bs-toggle="pill" data-bs-target="#invites" type="button" role="tab">
            <i class="bi bi-envelope-paper me-1"></i> Invitations ({{ $invitations->total() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="companyTabContent">
    <!-- Tab 1: Team Members -->
    <div class="tab-pane fade show active" id="users" role="tabpanel">
        <div class="card overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Member Name</th>
                            <th scope="col">Email Address</th>
                            <th scope="col">Role</th>
                            <th scope="col">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="font-mono small text-muted">{{ $user->email }}</td>
                                <td>
                                    <span class="{{ $user->role->value === 'admin' ? 'badge-role-admin' : 'badge-role-member' }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="small text-muted font-mono">{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No users found in this company.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Tab 2: Short URLs -->
    <div class="tab-pane fade" id="urls" role="tabpanel">
        <div class="card overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 30%;">Short Link</th>
                            <th scope="col" style="width: 40%;">Destination</th>
                            <th scope="col">Creator</th>
                            <th scope="col">Created</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortUrls as $url)
                            @php
                                $fullShortUrl = url('/s/' . $url->code);
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ $fullShortUrl }}" target="_blank" class="url-link font-mono text-truncate" style="font-size: 0.85rem; max-width: 240px;">
                                            {{ $fullShortUrl }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary copy-btn py-0 px-2" onclick="copyToClipboard('{{ $fullShortUrl }}', this)">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                    <span class="code-pill">code: {{ $url->code }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 280px;" title="{{ $url->original_url }}">
                                        <a href="{{ $url->original_url }}" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none small">
                                            {{ $url->original_url }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">{{ $url->user->name ?? 'N/A' }}</span>
                                </td>
                                <td class="small text-muted font-mono">{{ $url->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ $fullShortUrl }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="Test Redirect">
                                        <i class="bi bi-arrow-up-right text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No short URLs generated by this company yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($shortUrls->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top">
                    {{ $shortUrls->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Tab 3: Invitations -->
    <div class="tab-pane fade" id="invites" role="tabpanel">
        <div class="card overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Invitee Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Invited By</th>
                            <th scope="col">State</th>
                            <th scope="col">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invitations as $invite)
                            <tr>
                                <td class="font-mono small fw-semibold">{{ $invite->email }}</td>
                                <td>
                                    <span class="{{ $invite->role->value === 'admin' ? 'badge-role-admin' : 'badge-role-member' }}">
                                        {{ $invite->role->label() }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $invite->inviter->name ?? 'System' }}</td>
                                <td>
                                    @if($invite->accepted_at)
                                        <span class="badge bg-success-subtle text-success border-0 px-2 py-1"><i class="bi bi-check2-circle me-1"></i> Accepted</span>
                                    @elseif($invite->revoked_at)
                                        <span class="badge bg-danger-subtle text-danger border-0 px-2 py-1"><i class="bi bi-x-circle me-1"></i> Revoked</span>
                                    @elseif($invite->expires_at->isPast())
                                        <span class="badge bg-secondary-subtle text-secondary border-0 px-2 py-1"><i class="bi bi-clock-history me-1"></i> Expired</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border-0 px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                    @endif
                                </td>
                                <td class="small text-muted font-mono">{{ $invite->expires_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No invitations recorded for this company.</td>
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
    </div>
</div>
@endsection

