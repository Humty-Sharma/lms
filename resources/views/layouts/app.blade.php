<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ShortVerse &bull; Multi-Tenant URL Engine' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-page: #f8fafc;
            --card-border: rgba(226, 232, 240, 0.8);
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-page);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-mono {
            font-family: var(--font-mono);
        }

        /* Layout wrapper */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1020;
            transition: all 0.3s ease;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #ffffff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .sidebar-nav {
            padding: 1.25rem 0.85rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 0.685rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 0.5rem 0.75rem;
            margin-top: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.15s ease;
            margin-bottom: 0.25rem;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            transition: transform 0.15s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .sidebar-link:hover i {
            transform: translateX(2px);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: var(--sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.8);
        }

        .user-profile-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #334155;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        /* Main Content wrapper */
        .admin-main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            max-width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            overflow-x: hidden;
        }

        .top-navbar {
            height: 68px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1010;
        }

        .page-content {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Card and Components */
        .card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 4px 12px rgba(15, 23, 42, 0.03);
        }

        .stat-card {
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            background: #ffffff;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        /* Role Badges */
        .badge-role-superadmin {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-role-admin {
            background-color: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-role-member {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        /* Controls */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.625rem 0.875rem;
            font-size: 0.925rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 10px;
            padding: 0.625rem 1.25rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .code-pill {
            background: #f1f5f9;
            color: #334155;
            font-family: var(--font-mono);
            font-size: 0.785rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .url-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .url-link:hover {
            text-decoration: underline;
        }

        /* Table */
        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1rem;
        }

        .table tbody td {
            padding: 1rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .table tbody tr:hover td {
            background-color: rgba(248, 250, 252, 0.85);
        }

        .copy-btn {
            border-radius: 8px;
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
        }

        /* Responsive toggle */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    @auth
        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="admin-sidebar" id="adminSidebar">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 tracking-tight" style="line-height: 1.2;">Short<span style="color: #818cf8;">Verse</span></div>
                        <div class="text-secondary small font-mono" style="font-size: 0.65rem;">ENTERPRISE TENANT</div>
                    </div>
                </a>

                <div class="sidebar-nav">
                    <div class="nav-section-title">Navigation</div>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('short-urls.index') }}" class="sidebar-link {{ request()->routeIs('short-urls.*') ? 'active' : '' }}">
                        <i class="bi bi-link-45deg"></i>
                        <span>Short URLs</span>
                    </a>

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                        <div class="nav-section-title">Administration</div>
                        <a href="{{ route('invitations.index') }}" class="sidebar-link {{ request()->routeIs('invitations.*') ? 'active' : '' }}">
                            <i class="bi bi-send-plus"></i>
                            <span>Invitations</span>
                        </a>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('companies.index') }}" class="sidebar-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                            <i class="bi bi-buildings"></i>
                            <span>Companies</span>
                        </a>
                    @endif

                    <div class="nav-section-title">Settings</div>
                    <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Account & Security</span>
                    </a>
                </div>

                <!-- Sidebar User Footer -->
                <div class="sidebar-footer">
                    <a href="{{ route('profile.edit') }}" class="user-profile-badge text-decoration-none">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-white small fw-bold text-truncate">{{ auth()->user()->name }}</div>
                            <div class="text-secondary font-mono text-truncate" style="font-size: 0.7rem;">
                                @if(auth()->user()->isSuperAdmin())
                                    SuperAdmin
                                @else
                                    {{ auth()->user()->company->name }}
                                @endif
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-secondary small"></i>
                    </a>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="admin-main">
                <!-- Top Navbar -->
                <header class="top-navbar">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-secondary d-lg-none btn-sm" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <div class="d-none d-sm-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border font-mono small">
                                <i class="bi bi-shield-check text-success me-1"></i> Data Isolation Active
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            @if(auth()->user()->isSuperAdmin())
                                <span class="badge-role-superadmin"><i class="bi bi-shield-fill-check me-1"></i>Platform SuperAdmin</span>
                            @elseif(auth()->user()->isAdmin())
                                <span class="badge-role-admin"><i class="bi bi-person-gear me-1"></i>Admin &bull; {{ auth()->user()->company->name }}</span>
                            @else
                                <span class="badge-role-member"><i class="bi bi-person me-1"></i>Member &bull; {{ auth()->user()->company->name }}</span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-semibold">
                                <i class="bi bi-power me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="page-content">
                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center shadow-sm alert-dismissible fade show mb-4" role="alert" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;">
                            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                            <div class="fw-medium">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-info d-flex align-items-center shadow-sm alert-dismissible fade show mb-4" role="alert" style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1;">
                            <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
                            <div class="fw-medium">{{ session('status') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger shadow-sm alert-dismissible fade show mb-4" role="alert" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-exclamation-octagon-fill fs-5 me-2 text-danger"></i>
                                <strong class="fw-bold">Validation Error</strong>
                            </div>
                            <ul class="mb-0 ps-4 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <footer class="py-3 px-4 text-muted small bg-white border-top mt-auto">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span>&copy; {{ date('Y') }} ShortVerse &bull; Multi-Tenant Architecture</span>
                        <span class="font-mono" style="font-size: 0.75rem;">Laravel 11 &bull; MySQL</span>
                    </div>
                </footer>
            </div>
        </div>
    @else
        <!-- Guest View (Login, Accept Invitation) -->
        <div class="min-vh-100 d-flex flex-column justify-content-between bg-light">
            <div class="container py-4 flex-grow-1 d-flex align-items-center justify-content-center">
                <div class="w-100">
                    @yield('content')
                </div>
            </div>
            <footer class="py-3 text-center text-muted small">
                &copy; {{ date('Y') }} ShortVerse &bull; Multi-Tenant Enterprise URL Engine
            </footer>
        </div>
    @endauth

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text, buttonElement) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                buttonElement.classList.remove('btn-outline-secondary');
                buttonElement.classList.add('btn-success');
                setTimeout(() => {
                    buttonElement.innerHTML = originalHtml;
                    buttonElement.classList.remove('btn-success');
                    buttonElement.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                alert('Copy failed: ' + err);
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
