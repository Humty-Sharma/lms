@extends('layouts.app', ['title' => 'Companies &bull; Platform Admin'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1 tracking-tight">Registered Companies</h2>
        <p class="text-muted mb-0 small">Cross-tenant overview of all corporate instances registered on ShortVerse</p>
    </div>
    <a href="{{ route('invitations.index') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i>
        <span>Provision Tenant</span>
    </a>
</div>

<div class="card overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold fs-6">Tenants Directory</h5>
        <span class="badge bg-light text-secondary border font-mono small px-2 py-1">{{ $companies->total() }} organizations</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th scope="col" style="width: 12%;">Tenant ID</th>
                    <th scope="col" style="width: 30%;">Organization Name</th>
                    <th scope="col">Members</th>
                    <th scope="col">Short URLs</th>
                    <th scope="col">Invitations</th>
                    <th scope="col">Registered</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td class="text-muted font-mono small">#{{ $company->id }}</td>
                        <td>
                            <a href="{{ route('companies.show', $company) }}" class="fw-bold text-dark text-decoration-none d-flex align-items-center gap-2">
                                <div class="stat-icon-wrapper" style="width: 32px; height: 32px; font-size: 0.9rem; background: var(--primary-light); color: var(--primary);">
                                    <i class="bi bi-buildings"></i>
                                </div>
                                <span class="url-link">{{ $company->name }}</span>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border-0 px-2 py-1">
                                <i class="bi bi-people me-1"></i> {{ $company->users_count }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border-0 px-2 py-1">
                                <i class="bi bi-link-45deg me-1"></i> {{ $company->short_urls_count }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1">
                                {{ $company->invitations_count }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small font-mono">
                                {{ $company->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-primary py-1 px-3 fw-semibold">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            No companies registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
            {{ $companies->links() }}
        </div>
    @endif
</div>
@endsection
