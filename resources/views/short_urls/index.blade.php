@extends('layouts.app', ['title' => 'Short URLs &bull; ShortVerse'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold mb-1 tracking-tight">URL Management Directory</h2>
        <p class="text-muted mb-0 small">
            @if($user->isSuperAdmin())
                Auditing all URLs across every registered tenant organization.
            @elseif($user->isAdmin())
                All URLs created by team members within <strong>{{ $user->company->name }}</strong>.
            @else
                URLs created exclusively by you under <strong>{{ $user->company->name }}</strong>.
            @endif
        </p>
    </div>
</div>

@if($user->isAdmin() || $user->isMember())
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);">
        <div class="card-body p-4 text-white">
            <h5 class="fw-bold mb-1"><i class="bi bi-link-45deg me-1"></i> Quick Shortener</h5>
            <p class="text-white text-opacity-75 small mb-3">Paste your destination URL below to generate an instant short link.</p>

            <form method="POST" action="{{ route('short-urls.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-12 col-md-9">
                        <input type="url" name="original_url" class="form-control form-control-lg bg-white @error('original_url') is-invalid @enderror" placeholder="https://example.com/long-page-path" value="{{ old('original_url') }}" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <button class="btn btn-dark btn-lg w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" type="submit" style="background: #0f172a; border-color: #0f172a;">
                            <i class="bi bi-lightning-charge"></i> Shorten
                        </button>
                    </div>
                </div>
                @error('original_url')
                    <div class="text-warning small mt-2 fw-medium">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>
@endif

<div class="card overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold fs-6">Active Short URLs</h5>
        <span class="badge bg-light text-secondary border font-mono small px-2 py-1">{{ $shortUrls->total() }} total links</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th scope="col" style="width: 28%;">Short Link</th>
                    <th scope="col" style="width: 32%;">Target Destination</th>
                    @if($user->isSuperAdmin())
                        <th scope="col">Company</th>
                        <th scope="col">Created By</th>
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
                                <button type="button" class="btn btn-sm btn-outline-secondary copy-btn py-0 px-2 flex-shrink-0" onclick="copyToClipboard('{{ $fullShortUrl }}', this)">
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
                                    {{ $url->company->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $url->user->name ?? 'N/A' }}</div>
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
                            <span class="text-muted small font-mono">
                                {{ $url->created_at->diffForHumans() }}
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="p-3 d-inline-flex rounded-circle bg-light mb-2">
                                <i class="bi bi-inbox fs-2"></i>
                            </div>
                            <div class="fw-semibold text-dark">No URLs exist in this scope</div>
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
