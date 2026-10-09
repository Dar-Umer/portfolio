<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Console | The Umer Store')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --app-primary: #0066cc;
            --app-primary-hover: #0052a3;
            --app-primary-light: #eff6ff;
            --app-success: #059669;
            --app-warning: #d97706;
            --app-danger: #dc2626;
            --app-sidebar-bg: #0f172a;
            --app-sidebar-hover: #1e293b;
            --app-sidebar-active: #0066cc;
            --app-sidebar-text: #94a3b8;
            --app-border: #e2e8f0;
            --app-bg: #f8fafc;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--app-bg);
            color: #1e293b;
            font-size: 13.5px;
            letter-spacing: -0.01em;
            line-height: 1.5;
        }

        /* Top Brand Accent Stripe */
        .top-brand-stripe {
            height: 3px;
            width: 100%;
            background: linear-gradient(90deg, #2563eb 0%, #06b6d4 50%, #10b981 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 2000;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--app-sidebar-bg);
            height: 100vh;
            color: var(--app-sidebar-text);
            position: fixed;
            top: 3px;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand {
            padding: 18px 22px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand .brand-logo-badge {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            padding: 14px 12px;
        }

        .sidebar-nav-container::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .nav-category {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
            padding: 16px 14px 6px;
        }

        .sidebar a {
            color: #94a3b8;
            text-decoration: none;
            padding: 9px 14px;
            display: flex;
            align-items: center;
            border-radius: 6px;
            margin-bottom: 3px;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar a i {
            width: 22px;
            font-size: 14px;
            margin-right: 10px;
            color: #64748b;
            text-align: center;
            transition: color 0.15s ease;
        }

        .sidebar a:hover {
            color: #ffffff;
            background: var(--app-sidebar-hover);
        }

        .sidebar a:hover i {
            color: #38bdf8;
        }

        .sidebar a.active {
            color: #ffffff;
            background: var(--app-sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .sidebar a.active i {
            color: #ffffff;
        }

        .sidebar-footer {
            padding: 14px 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: #090e17;
        }

        /* Top Header Bar */
        .top-navbar {
            margin-left: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--app-border);
            padding: 12px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 3px;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 28px 32px;
            min-height: calc(100vh - 65px);
        }

        /* Clean Card System */
        .card {
            border: 1px solid var(--app-border);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
            background: #ffffff;
            margin-bottom: 22px;
            overflow: hidden;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 22px;
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
        }

        /* Stat Box with Colored Edge Indicator */
        .card-stat {
            border: 1px solid var(--app-border);
            border-radius: 10px;
            padding: 18px 22px;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .card-stat:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .card-stat::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--app-primary);
        }
        .card-stat.border-red::before { background: var(--app-danger); }
        .card-stat.border-green::before { background: var(--app-success); }
        .card-stat.border-yellow::before { background: var(--app-warning); }

        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* Table Styling */
        .table {
            color: #334155;
            font-size: 13.5px;
            margin-bottom: 0;
        }

        .table thead th {
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 18px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.12s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Zoho Buttons */
        .btn {
            font-weight: 500;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 13px;
        }

        .btn-primary {
            background-color: var(--zoho-blue);
            border-color: var(--zoho-blue);
        }

        .btn-primary:hover {
            background-color: var(--zoho-blue-dark);
            border-color: var(--zoho-blue-dark);
        }

        .btn-outline-primary {
            color: var(--zoho-blue);
            border-color: #c0dcfa;
        }
        .btn-outline-primary:hover {
            background-color: #f0f7ff;
            color: var(--zoho-blue);
            border-color: var(--zoho-blue);
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11.5px;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 7px 12px;
            font-size: 13px;
            color: #1e293b;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--app-primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
        }

        /* Search inputs */
        .search-pill {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12.5px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Accent Stripe -->
    <div class="top-brand-stripe"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo-badge me-2">U</div>
            <div>
                <div class="fw-bold text-white fs-6 lh-1">The Umer Store</div>
                <div class="text-muted" style="font-size: 10px; letter-spacing: 0.05em;">ENTERPRISE ADMIN</div>
            </div>
        </div>

        <div class="sidebar-nav-container">
            <div class="nav-category">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>

            <div class="nav-category">Customer Relations</div>
            <a href="{{ route('admin.clients.index') }}" class="{{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                <i class="fas fa-user-friends"></i> Customers & Accounts
            </a>
            <a href="{{ route('admin.projects.index') }}" class="{{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram"></i> Projects & Milestones
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="{{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> Support Tickets & SLA
            </a>

            <div class="nav-category">Commercial & Billing</div>
            <a href="{{ route('admin.quotations.index') }}" class="{{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Estimates & Quotes
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="{{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt"></i> Tax Invoices & Payments
            </a>

            <div class="nav-category">Engineering & CMS</div>
            <a href="{{ route('admin.github.index') }}" class="{{ request()->routeIs('admin.github.*') ? 'active' : '' }}">
                <i class="fab fa-github"></i> Git Sync & Releases
            </a>
            <a href="{{ route('admin.cms.homepage') }}" class="{{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                <i class="fas fa-desktop"></i> Portal CMS
            </a>

            <div class="nav-category">Analytics & Setup</div>
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Business Reports & Exports
            </a>
            <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Users & Role Matrix
            </a>
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                <i class="fas fa-sliders-h"></i> System Settings
            </a>
            <a href="{{ route('admin.settings.audit') }}" class="{{ request()->routeIs('admin.settings.audit') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i> Audit Log Trail
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2 text-white fw-bold" style="width: 32px; height: 32px; font-size: 11px; background: #2563eb;">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="small text-truncate" style="max-width: 120px;">
                        <div class="text-white fw-semibold lh-sm" style="font-size: 12px;">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size: 10px;">Administrator</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Sign Out">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Top Navigation Header -->
    <header class="top-navbar">
        <div class="d-flex align-items-center">
            <h6 class="fw-bold text-dark mb-0 fs-6">@yield('page_title', 'Dashboard')</h6>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm" style="font-size: 12px;">
                <i class="fas fa-external-link-alt me-1"></i> Public Website
            </a>
            <span class="badge bg-primary text-white px-2 py-1" style="font-size: 11px;">
                <i class="fas fa-check-circle me-1"></i> Active Session
            </span>
        </div>
    </header>

    <!-- Content Area -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show d-flex align-items-center mb-3 py-2 px-3" role="alert">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <div class="small fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show d-flex align-items-center mb-3 py-2 px-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                <div class="small fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
