<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Admin Console') | The Umer Store</title>
    
    <!-- Modern Enterprise Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --app-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --app-mono: 'JetBrains Mono', monospace;
            
            /* Modern Indigo / Cobalt Brand Palette */
            --app-primary: #2563eb;
            --app-primary-hover: #1d4ed8;
            --app-primary-subtle: #eff6ff;
            --app-primary-border: #bfdbfe;
            
            --app-success: #10b981;
            --app-success-subtle: #ecfdf5;
            --app-success-border: #a7f3d0;
            
            --app-warning: #f59e0b;
            --app-warning-subtle: #fffbeb;
            --app-warning-border: #fde68a;
            
            --app-danger: #ef4444;
            --app-danger-subtle: #fef2f2;
            --app-danger-border: #fecaca;
            
            --app-info: #06b6d4;
            --app-info-subtle: #ecfeff;
            --app-info-border: #a5f3fc;
            
            /* Slate Neutrals */
            --app-bg: #f8fafc;
            --app-card-bg: #ffffff;
            --app-border: #e2e8f0;
            --app-border-subtle: #f1f5f9;
            --app-text-main: #0f172a;
            --app-text-muted: #64748b;
            --app-text-dim: #94a3b8;
            
            /* Sidebar Dark Obsidian */
            --sidebar-width: 268px;
            --sidebar-bg: #0b0f19;
            --sidebar-hover: #172033;
            --sidebar-active-bg: rgba(37, 99, 235, 0.15);
            --sidebar-text: #94a3b8;
            --sidebar-text-hover: #ffffff;
            --sidebar-border: rgba(255, 255, 255, 0.07);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--app-font);
            background-color: var(--app-bg);
            color: var(--app-text-main);
            font-size: 13.5px;
            letter-spacing: -0.015em;
            line-height: 1.5;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Accent Glow */
        .top-brand-stripe {
            height: 3px;
            width: 100%;
            background: linear-gradient(90deg, #2563eb 0%, #6366f1 35%, #06b6d4 70%, #10b981 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 2100;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            color: var(--sidebar-text);
            position: fixed;
            top: 3px;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--sidebar-border);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sidebar-brand {
            padding: 20px 22px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--sidebar-border);
            background: rgba(0, 0, 0, 0.2);
        }

        .sidebar-brand .brand-logo-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
            letter-spacing: -0.05em;
        }

        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
        }

        .sidebar-nav-container::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
        }

        .nav-category {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #475569;
            font-weight: 700;
            padding: 16px 12px 6px;
        }

        .sidebar a.nav-item-link {
            color: var(--sidebar-text);
            text-decoration: none;
            padding: 9px 12px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 3px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
            position: relative;
        }

        .sidebar a.nav-item-link i.nav-icon {
            width: 22px;
            font-size: 14px;
            margin-right: 10px;
            color: #64748b;
            text-align: center;
            transition: color 0.15s ease;
        }

        .sidebar a.nav-item-link:hover {
            color: var(--sidebar-text-hover);
            background: var(--sidebar-hover);
        }

        .sidebar a.nav-item-link:hover i.nav-icon {
            color: #60a5fa;
        }

        .sidebar a.nav-item-link.active {
            color: #ffffff;
            background: var(--sidebar-active-bg);
            font-weight: 600;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .sidebar a.nav-item-link.active i.nav-icon {
            color: #3b82f6;
        }

        .sidebar a.nav-item-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 3px;
            background: #3b82f6;
            border-radius: 0 4px 4px 0;
        }

        .nav-pill-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            margin-left: auto;
        }

        .sidebar-footer {
            padding: 16px 18px;
            border-top: 1px solid var(--sidebar-border);
            background: #070a12;
        }

        /* Top Header Navbar */
        .top-navbar {
            margin-left: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--app-border);
            padding: 12px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 3px;
            z-index: 1000;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: margin-left 0.25s ease;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 28px 36px 48px;
            min-height: calc(100vh - 65px);
            transition: margin-left 0.25s ease;
        }

        /* Modern Card Component */
        .card {
            border: 1px solid var(--app-border);
            border-radius: 12px;
            background: var(--app-card-bg);
            margin-bottom: 24px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .card:hover {
            border-color: #cbd5e1;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--app-border-subtle);
            padding: 16px 22px;
            font-weight: 700;
            font-size: 13.5px;
            color: var(--app-text-main);
            letter-spacing: -0.01em;
        }

        /* Stat Indicator Cards */
        .card-stat {
            border: 1px solid var(--app-border);
            border-radius: 12px;
            padding: 20px 22px;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .card-stat:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
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
        .card-stat.border-green::before { background: var(--app-success); }
        .card-stat.border-yellow::before { background: var(--app-warning); }
        .card-stat.border-red::before { background: var(--app-danger); }
        .card-stat.border-info::before { background: var(--app-info); }

        .stat-icon-wrapper {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        /* Clean Table System */
        .table {
            color: #334155;
            font-size: 13px;
            margin-bottom: 0;
        }

        .table thead th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 13px 20px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.12s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Buttons Overhaul */
        .btn {
            font-family: var(--app-font);
            font-weight: 600;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 13px;
            letter-spacing: -0.01em;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-color: #1d4ed8;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            border-color: #1e40af;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            color: var(--app-primary);
            border-color: #bfdbfe;
            background: #ffffff;
        }

        .btn-outline-primary:hover {
            background-color: var(--app-primary);
            border-color: var(--app-primary);
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
            transform: translateY(-1px);
        }

        .btn-light {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #334155;
        }

        .btn-light:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* Soft Pastel Badges */
        .badge {
            font-weight: 600;
            padding: 4px 9px;
            border-radius: 6px;
            font-size: 11px;
            letter-spacing: -0.01em;
        }

        .badge-soft-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-soft-success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge-soft-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-soft-danger  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-soft-info    { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
        .badge-soft-dark    { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 8px 14px;
            font-size: 13px;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.15s ease;
        }

        .form-control-sm, .form-select-sm {
            padding: 6px 12px;
            font-size: 12.5px;
            border-radius: 6px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--app-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: 0;
        }

        .form-label {
            color: #334155;
            font-weight: 600;
            font-size: 12.5px;
            margin-bottom: 5px;
        }

        /* Code & Monospace Badges */
        code, .font-monospace {
            font-family: var(--app-mono) !important;
            font-size: 12px;
        }

        /* Mobile Hamburger & Backdrop */
        .mobile-toggle-btn {
            display: none;
            background: none;
            border: 0;
            font-size: 18px;
            color: #334155;
            padding: 4px 8px;
            margin-right: 12px;
            border-radius: 6px;
        }

        .mobile-toggle-btn:hover {
            background-color: #f1f5f9;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            .mobile-toggle-btn {
                display: block;
            }
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .top-navbar {
                margin-left: 0;
                padding: 12px 18px;
            }
            .main-content {
                margin-left: 0;
                padding: 20px 18px 40px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Accent Stripe -->
    <div class="top-brand-stripe"></div>

    <!-- Mobile Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebarNav">
        <div class="sidebar-brand">
            <div class="brand-logo-badge me-2">U</div>
            <div>
                <div class="fw-bold text-white fs-6 lh-1">The Umer Store</div>
                <div class="text-dim" style="font-size: 10px; letter-spacing: 0.06em; margin-top: 3px;">ENTERPRISE CONSOLE</div>
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto d-lg-none" id="sidebarCloseBtn" aria-label="Close"></button>
        </div>

        <div class="sidebar-nav-container">
            <div class="nav-category">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large nav-icon"></i> Dashboard
            </a>

            <div class="nav-category">Customer Relations</div>
            <a href="{{ route('admin.inquiries.index') }}" class="nav-item-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">
                <i class="fas fa-envelope-open-text nav-icon"></i> Inquiries & Leads
                @php $unreadLeadCount = \App\Models\ContactMessage::where('is_read', false)->count(); @endphp
                @if($unreadLeadCount > 0)
                    <span class="nav-pill-badge bg-danger text-white">{{ $unreadLeadCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.clients.index') }}" class="nav-item-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                <i class="fas fa-user-friends nav-icon"></i> Customers & Accounts
            </a>
            <a href="{{ route('admin.projects.index') }}" class="nav-item-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram nav-icon"></i> Projects & Milestones
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="nav-item-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt nav-icon"></i> Support Tickets & SLA
                @php $openTktCount = \App\Models\Ticket::whereNull('resolved_at')->count(); @endphp
                @if($openTktCount > 0)
                    <span class="nav-pill-badge bg-primary text-white">{{ $openTktCount }}</span>
                @endif
            </a>

            @hasanyrole('Super Admin|Administrator')
            <div class="nav-category">Commercial & Billing</div>
            <a href="{{ route('admin.quotations.index') }}" class="nav-item-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice nav-icon"></i> Estimates & Quotes
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="nav-item-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <i class="fas fa-money-check-alt nav-icon"></i> Tax Invoices & Billing
            </a>
            @endhasanyrole

            <div class="nav-category">Engineering & CMS</div>
            <a href="{{ route('admin.github.index') }}" class="nav-item-link {{ request()->routeIs('admin.github.*') ? 'active' : '' }}">
                <i class="fab fa-github nav-icon"></i> Git Sync & Releases
            </a>
            <a href="{{ route('admin.cms.homepage') }}" class="nav-item-link {{ request()->routeIs('admin.cms.homepage*') ? 'active' : '' }}">
                <i class="fas fa-desktop nav-icon"></i> Homepage CMS
            </a>
            <a href="{{ route('admin.cms.services') }}" class="nav-item-link {{ request()->routeIs('admin.cms.services*') ? 'active' : '' }}">
                <i class="fas fa-concierge-bell nav-icon"></i> Services CMS
            </a>
            <a href="{{ route('admin.cms.portfolio') }}" class="nav-item-link {{ request()->routeIs('admin.cms.portfolio*') ? 'active' : '' }}">
                <i class="fas fa-briefcase nav-icon"></i> Portfolio CMS
            </a>

            @hasanyrole('Super Admin|Administrator')
            <div class="nav-category">Analytics & Setup</div>
            <a href="{{ route('admin.reports.index') }}" class="nav-item-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie nav-icon"></i> Business Reports & Exports
            </a>
            <a href="{{ route('admin.roles.index') }}" class="nav-item-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog nav-icon"></i> Users & Role Matrix
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                <i class="fas fa-sliders-h nav-icon"></i> System Settings
            </a>
            <a href="{{ route('admin.settings.audit') }}" class="nav-item-link {{ request()->routeIs('admin.settings.audit') ? 'active' : '' }}">
                <i class="fas fa-shield-alt nav-icon"></i> Audit Log Trail
            </a>
            @endhasanyrole
        </div>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2 text-white fw-bold shadow-sm" style="width: 34px; height: 34px; font-size: 12px; background: linear-gradient(135deg, #2563eb, #6366f1);">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="small text-truncate" style="max-width: 125px;">
                        <div class="text-white fw-semibold lh-sm text-truncate" style="font-size: 12px;">{{ auth()->user()->name }}</div>
                        <div class="text-dim" style="font-size: 10.5px;">{{ auth()->user()->roles->pluck('name')->first() ?? 'Staff' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger py-1 px-2 border-0" title="Sign Out">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Top Navigation Header -->
    <header class="top-navbar">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-6">@yield('page_title', 'Dashboard')</h6>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Quick Actions Dropdown -->
            <div class="dropdown">
                <button class="btn btn-primary btn-sm px-3 shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-plus"></i> Quick Create
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                    <li><h6 class="dropdown-header text-uppercase" style="font-size: 10px;">Operations</h6></li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('admin.projects.create') }}">
                            <i class="fas fa-project-diagram text-primary me-2"></i> New Project
                        </a>
                    </li>
                    @hasanyrole('Super Admin|Administrator')
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('admin.clients.create') }}">
                            <i class="fas fa-user-plus text-success me-2"></i> Onboard Client
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('admin.quotations.create') }}">
                            <i class="fas fa-file-invoice text-info me-2"></i> New Quotation
                        </a>
                    </li>
                    @endhasanyrole
                </ul>
            </div>

            <!-- Public Site Link -->
            <a href="{{ route('home') }}" target="_blank" class="btn btn-light btn-sm text-secondary" title="View Public Portfolio Site">
                <i class="fas fa-external-link-alt me-1"></i> Public Site
            </a>

            <!-- Active User Role Badge -->
            <span class="badge badge-soft-primary px-2 py-1 d-none d-sm-inline-flex align-items-center">
                <span class="status-dot bg-success"></span> {{ auth()->user()->roles->pluck('name')->first() ?? 'Staff' }}
            </span>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show d-flex align-items-center mb-4 py-3 px-3" role="alert" style="border-radius: 10px;">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 26px; height: 26px; font-size: 12px;">
                    <i class="fas fa-check"></i>
                </div>
                <div class="small fw-semibold text-dark">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show d-flex align-items-center mb-4 py-3 px-3" role="alert" style="border-radius: 10px;">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3" style="width: 26px; height: 26px; font-size: 12px;">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div class="small fw-semibold text-dark">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info border-0 shadow-sm alert-dismissible fade show d-flex align-items-center mb-4 py-3 px-3" role="alert" style="border-radius: 10px;">
                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" style="width: 26px; height: 26px; font-size: 12px;">
                    <i class="fas fa-info"></i>
                </div>
                <div class="small fw-semibold text-dark">{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning border-0 shadow-sm alert-dismissible fade show d-flex align-items-center mb-4 py-3 px-3" role="alert" style="border-radius: 10px;">
                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width: 26px; height: 26px; font-size: 12px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="small fw-semibold text-dark">{{ session('warning') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4 py-3 px-3" role="alert" style="border-radius: 10px;">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-exclamation-circle me-2 text-danger fs-5"></i>
                    <span class="small fw-bold">Please correct the following input issues:</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
                <ul class="mb-0 small ps-4 text-secondary">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Toggle
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebarNav');
        const backdrop = document.getElementById('sidebarBackdrop');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        function toggleSidebar() {
            sidebar?.classList.toggle('show');
            backdrop?.classList.toggle('show');
        }

        mobileBtn?.addEventListener('click', toggleSidebar);
        backdrop?.addEventListener('click', toggleSidebar);
        closeBtn?.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts')
</body>
</html>
