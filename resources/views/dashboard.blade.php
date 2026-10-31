@extends('layouts.app', ['title' => 'Dashboard &bull; ShortVerse'])

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="h3 fw-bold mb-0 tracking-tight">
                @if($user->isSuperAdmin())
                    Platform Console
                @elseif($user->isAdmin())
                    {{ $user->company->name }} <span class="text-muted fw-normal fs-5">&bull; Admin Workspace</span>
                @else
                    {{ $user->company->name }} <span class="text-muted fw-normal fs-5">&bull; My Links</span>
                @endif
            </h2>
        </div>
        <div class="text-muted small d-flex align-items-center gap-2">
            <span>Signed in as <strong>{{ $user->email }}</strong></span>
            <span>&bull;</span>
            @if($user->isSuperAdmin())
                <span class="badge-role-superadmin"><i class="bi bi-shield-fill-check me-1"></i>Platform SuperAdmin</span>
            @elseif($user->isAdmin())
                <span class="badge-role-admin"><i class="bi bi-person-gear me-1"></i>Company Admin</span>
            @else
                <span class="badge-role-member"><i class="bi bi-person me-1"></i>Team Member</span>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        @if($user->isSuperAdmin() || $user->isAdmin())
            <a href="{{ route('invitations.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-send-plus"></i>
                <span>Invite User</span>
            </a>
        @endif
        @if($user->isAdmin() || $user->isMember())
            <a href="#createUrlCard" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-plus-lg"></i>
                <span>Create Short URL</span>
            </a>
        @endif
    </div>
</div>

<!-- Key Performance & Volume Indicators -->
<div class="row g-3 mb-4">
    @if($user->isSuperAdmin())
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Registered Tenants</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['total_companies'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                        <i class="bi bi-buildings"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    <span class="text-success fw-semibold"><i class="bi bi-check-circle"></i> Active</span> Multi-Tenant Accounts
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Platform Users</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['total_users'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    SuperAdmins, Admins & Members
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Platform Short URLs</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['total_urls'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0284c7;">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Across all companies
                </div>
            </div>
        </div>
    @elseif($user->isAdmin())
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Company Members</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['company_users'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Within <strong>{{ $user->company->name }}</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Company URLs</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['company_urls'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Total links in organization
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Authored by You</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['my_urls'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0284c7;">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Created under your login
                </div>
            </div>
        </div>
    @else
        <div class="col-12 col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">My Short URLs</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($stats['my_urls'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Exclusively visible to you
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Assigned Tenant</div>
                        <div class="fs-3 fw-bold text-dark mt-1 text-truncate">{{ $user->company->name }}</div>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(100, 116, 139, 0.1); color: #475569;">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Company data isolation verified
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Create URL Component (Admin / Member only) -->
@if($user->isAdmin() || $user->isMember())
    <div class="card mb-4" id="createUrlCard">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="p-2 rounded-3" style="background: var(--primary-light); color: var(--primary);">
                    <i class="bi bi-magic fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Create Short URL</h5>
                    <p class="text-muted small mb-0">Generate a unique short code for your destination URL</p>
                </div>
            </div>

            <form method="POST" action="{{ route('short-urls.store') }}">
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-globe2"></i></span>
                            <input type="url" name="original_url" class="form-control form-control-lg border-start-0 ps-0 @error('original_url') is-invalid @enderror" placeholder="https://domain.com/very/long/destination/url" value="{{ old('original_url') }}" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <button class="btn btn-primary btn-lg w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" type="submit">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Shorten Link</span>
                        </button>
                    </div>
                </div>
                @error('original_url')
                    <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>
@else
    <div class="alert alert-light border d-flex align-items-center gap-3 p-3 mb-4 shadow-sm" style="border-radius: 14px;">
        <div class="p-2 rounded-circle" style="background: #f1f5f9; color: #475569;">
            <i class="bi bi-info-circle-fill fs-4"></i>
        </div>
        <div class="small text-muted">
            <i class="bi bi-info-circle me-1"></i> As SuperAdmin, you can monitor links across all companies. URL creation is enabled for tenant Admins and Members.
        </div>
    </div>
@endif

<!-- Data Table Card -->
<div class="card overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <div class="d-flex align-items-center gap-2">
            <h5 class="mb-0 fw-bold fs-6">
                @if($user->isSuperAdmin())
                    All Platform Short URLs <span class="text-muted fw-normal">(Cross-Tenant Visibility)</span>
                @elseif($user->isAdmin())
                    {{ $user->company->name }} URLs <span class="text-muted fw-normal">(Company Scope)</span>
                @else
                    My Authored URLs <span class="text-muted fw-normal">(Self Scope)</span>
                @endif
            </h5>
        </div>
        <span class="badge bg-light text-secondary border font-mono small px-2 py-1">{{ $shortUrls->total() }} records</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th scope="col" style="width: 28%;">Short Link</th>
                    <th scope="col" style="width: 32%;">Destination</th>
                    @if($user->isSuperAdmin())
                        <th scope="col">Company</th>
                        <th scope="col">Author</th>
                    @elseif($user->isAdmin())
                        <th scope="col">Author</th>
                    @endif
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
                                <a href="{{ $fullShortUrl }}" target="_blank" class="url-link font-mono text-truncate" style="font-size: 0.85rem; max-width: 240px;" title="{{ $fullShortUrl }}">
                                    {{ $fullShortUrl }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary copy-btn py-0 px-2 flex-shrink-0" onclick="copyToClipboard('{{ $fullShortUrl }}', this)" title="Copy link">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                            <div class="mt-1">
                                <span class="code-pill">code: {{ $url->code }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 260px;" title="{{ $url->original_url }}">
                                <a href="{{ $url->original_url }}" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none small">
                                    <i class="bi bi-box-arrow-up-right me-1 small"></i>{{ $url->original_url }}
                                </a>
                            </div>
                        </td>
                        @if($user->isSuperAdmin())
                            <td>
                                <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                    <i class="bi bi-building me-1"></i>{{ $url->company->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $url->user->name ?? 'N/A' }}</div>
                                <div class="text-muted font-mono" style="font-size: 0.75rem;">{{ $url->user->email ?? '' }}</div>
                            </td>
                        @elseif($user->isAdmin())
                            <td>
                                <div class="small fw-medium">{{ $url->user->name ?? 'N/A' }}</div>
                                @if($url->user_id === $user->id)
                                    <span class="badge bg-primary-subtle text-primary border-0 small" style="font-size: 0.65rem;">You</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            <span class="text-muted small font-mono" title="{{ $url->created_at }}">
                                {{ $url->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ $fullShortUrl }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="Test Redirect">
                                <i class="bi bi-arrow-up-right text-primary"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="p-3 d-inline-flex rounded-circle bg-light text-muted mb-3">
                                <i class="bi bi-link-45deg fs-2"></i>
                            </div>
                            <div class="fw-bold text-dark">No short URLs found</div>
                            <div class="text-muted small mt-1">
                                @if($user->isSuperAdmin())
                                    Tenant companies haven't generated any short links yet.
                                @else
                                    Paste a destination link in the form above to generate your first short URL.
                                @endif
                            </div>
                        </td>
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
@endsection
