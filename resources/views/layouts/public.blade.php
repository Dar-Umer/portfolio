<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The Umer Store | Enterprise Architecture & Client Portal')</title>
    <!-- Google Fonts: Space Grotesk, Plus Jakarta Sans, Inter, Fira Code -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --brand-primary: #0066cc;
            --brand-cyan: #00f2fe;
            --brand-neon-blue: #4facfe;
            --brand-purple: #7928ca;
            --brand-pink: #ff0080;
            --brand-emerald: #10b981;
            --brand-dark: #070913;
            --brand-dark-card: #0d1224;
            --brand-border: #1e293b;
            --bs-body-color: #cbd5e1;
            --bs-secondary-color: #94a3b8;
            --bs-tertiary-color: #64748b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--brand-dark);
            color: #cbd5e1;
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
        }

        .text-muted {
            color: #94a3b8 !important;
        }

        .text-secondary {
            color: #cbd5e1 !important;
        }

        p, .lead {
            color: #cbd5e1;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Space Grotesk', 'Plus Jakarta Sans', sans-serif;
            color: #ffffff;
            letter-spacing: -0.03em;
        }

        .font-mono {
            font-family: 'Fira Code', monospace;
        }

        /* Top Announcement / Notice Marquee Strip (Inspired by planttechagro.com) */
        .notice-ticker-bar {
            background: linear-gradient(90deg, #050814 0%, #0c1229 50%, #050814 100%);
            border-bottom: 1px solid rgba(0, 242, 254, 0.2);
            font-size: 0.82rem;
            position: relative;
            z-index: 2020;
            overflow: hidden;
        }

        .notice-badge {
            background: linear-gradient(135deg, #00f2fe 0%, #0066cc 100%);
            color: #070913;
            font-weight: 800;
            letter-spacing: 0.08em;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 0.72rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 0 12px rgba(0, 242, 254, 0.5);
            flex-shrink: 0;
        }

        .marquee-track {
            display: flex;
            white-space: nowrap;
            overflow: hidden;
            width: 100%;
        }

        .marquee-content {
            display: flex;
            gap: 2.5rem;
            animation: marqueeScroll 26s linear infinite;
        }

        .marquee-track:hover .marquee-content {
            animation-play-state: paused;
        }

        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Ambient Glowing Background Orbs & Cyber Grid */
        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none;
            z-index: 0;
        }

        .orb-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.35;
            pointer-events: none;
            animation: orbFloat 14s infinite alternate ease-in-out;
            z-index: 0;
        }

        .orb-cyan {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #00f2fe 0%, rgba(0, 242, 254, 0) 70%);
            top: -100px;
            right: 5%;
        }

        .orb-purple {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, #7928ca 0%, rgba(121, 40, 202, 0) 70%);
            bottom: 10%;
            left: -150px;
            animation-duration: 18s;
            animation-delay: -5s;
        }

        .orb-blue {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, #2563eb 0%, rgba(37, 99, 235, 0) 70%);
            top: 45%;
            right: -100px;
            animation-duration: 12s;
            animation-delay: -2s;
        }

        @keyframes orbFloat {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(40px) scale(1.08) translateX(25px); }
            100% { transform: translateY(-30px) scale(0.95) translateX(-20px); }
        }

        /* Mouse Glow Spotlight Effect for Hero (as in planttechagro.com) */
        .hero-interactive-spotlight {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(650px circle at var(--mouse-x, 50%) var(--mouse-y, 35%), rgba(0, 242, 254, 0.12), transparent 45%);
            z-index: 1;
            transition: background 0.08s ease-out;
        }

        /* Animated Holographic Top Stripe */
        .holo-stripe {
            height: 3px;
            width: 100%;
            background: linear-gradient(90deg, #00f2fe, #4facfe, #0066cc, #7928ca, #ff0080, #00f2fe);
            background-size: 300% 100%;
            animation: holoGrad 6s linear infinite;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 2100;
        }

        @keyframes holoGrad {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        /* Futuristic Glass Navbar */
        .public-navbar {
            background: rgba(7, 9, 19, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            position: sticky;
            top: 0;
            z-index: 2000;
        }

        .brand-logo-cyber {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            color: #070913;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 17px;
            box-shadow: 0 0 20px rgba(0, 242, 254, 0.5);
            position: relative;
            overflow: hidden;
        }

        .brand-logo-cyber::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.4), transparent);
            transform: rotate(45deg);
            animation: cyberShine 3.5s infinite;
        }

        @keyframes cyberShine {
            0% { transform: translateX(-150%) rotate(45deg); }
            30%, 100% { transform: translateX(150%) rotate(45deg); }
        }

        .nav-link {
            font-weight: 500;
            color: #94a3b8 !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: #00f2fe !important;
            background: rgba(0, 242, 254, 0.06);
            text-shadow: 0 0 12px rgba(0, 242, 254, 0.6);
        }

        /* Cyber Glass Cards */
        .card-cyber {
            background: rgba(13, 18, 36, 0.7);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .card-cyber::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 242, 254, 0.4), transparent);
            opacity: 0;
            transition: opacity 0.35s ease;
        }

        .card-cyber:hover {
            transform: translateY(-6px);
            border-color: rgba(0, 242, 254, 0.3);
            box-shadow: 0 20px 40px -15px rgba(0, 242, 254, 0.15), 0 0 20px rgba(0, 242, 254, 0.08);
        }

        .card-cyber:hover::before {
            opacity: 1;
        }

        /* Futuristic Neon Text with High-Contrast Fallbacks */
        .neon-text-cyan {
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            -webkit-box-decoration-break: clone;
            box-decoration-break: clone;
            color: #00f2fe;
        }

        .neon-text-purple {
            background: linear-gradient(135deg, #c084fc 0%, #f472b6 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            -webkit-box-decoration-break: clone;
            box-decoration-break: clone;
            color: #c084fc;
        }

        /* High-Contrast Badges on Dark Surface */
        .badge.bg-dark {
            background-color: rgba(15, 23, 42, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            color: #f1f5f9 !important;
        }

        .footer-cyber .text-muted,
        .footer-cyber a.text-muted {
            color: #94a3b8 !important;
            transition: color 0.2s ease;
        }

        .footer-cyber a.text-muted:hover {
            color: #00f2fe !important;
        }

        /* Cyber Glow Buttons */
        .btn-cyber-primary {
            background: linear-gradient(135deg, #00f2fe 0%, #0066cc 100%);
            color: #070913 !important;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 242, 254, 0.35);
            transition: all 0.25s ease;
        }

        .btn-cyber-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 32px rgba(0, 242, 254, 0.6);
            color: #000000 !important;
        }

        .btn-cyber-outline {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.25s ease;
        }

        .btn-cyber-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #00f2fe;
            color: #00f2fe;
            box-shadow: 0 0 16px rgba(0, 242, 254, 0.25);
        }

        /* Pulsing Status Dot */
        .pulse-live {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 10px #10b981;
            display: inline-block;
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.3); }
        }

        /* Futuristic Section Badge */
        .cyber-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(0, 242, 254, 0.08);
            border: 1px solid rgba(0, 242, 254, 0.25);
            color: #00f2fe;
            box-shadow: 0 0 16px rgba(0, 242, 254, 0.15);
        }

        /* Floating elements animation */
        .float-slow {
            animation: floatAnim 6s ease-in-out infinite;
        }
        @keyframes floatAnim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Global Mobile Overflow and Layout Fixes */
        html, body {
            overflow-x: clip;
            max-width: 100vw;
        }

        .hover-cyan {
            transition: color 0.2s ease;
        }
        .hover-cyan:hover {
            color: #00f2fe !important;
            text-shadow: 0 0 10px rgba(0, 242, 254, 0.5);
        }

        /* Mobile Navbar Menu Drawer */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(13, 18, 36, 0.97);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(0, 242, 254, 0.25);
                border-radius: 16px;
                padding: 1.25rem;
                margin-top: 0.85rem;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.7);
            }
            .navbar-nav .nav-link {
                padding: 10px 14px !important;
                border-radius: 8px;
                margin-bottom: 4px;
            }
            .public-navbar .btn {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
            }
        }

        /* Ambient Orbs Responsive Scaling */
        @media (max-width: 768px) {
            .orb-glow {
                width: 240px !important;
                height: 240px !important;
                filter: blur(50px);
                opacity: 0.22;
            }
            .notice-ticker-bar {
                font-size: 0.76rem;
            }
            .notice-badge {
                padding: 2px 7px;
                font-size: 0.68rem;
            }
            .marquee-content {
                gap: 1.5rem;
            }
            .footer-cyber {
                padding: 45px 0 25px;
            }
        }

        /* Responsive Floating WhatsApp Button */
        .wa-float-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 2050;
            background-color: #25d366;
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: 0 0 25px rgba(37, 211, 102, 0.6);
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 50px;
            text-decoration: none;
            color: #ffffff !important;
            transition: all 0.25s ease;
        }
        .wa-float-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 35px rgba(37, 211, 102, 0.8);
            color: #ffffff !important;
        }

        @media (max-width: 576px) {
            .wa-float-btn {
                bottom: 18px;
                right: 18px;
                width: 48px;
                height: 48px;
                padding: 0;
                border-radius: 50% !important;
                justify-content: center;
            }
            .wa-float-btn span {
                display: none;
            }
            .wa-float-btn i {
                font-size: 1.35rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Animated Holographic Line -->
    <div class="holo-stripe"></div>

    <!-- Top Announcement Marquee Ticker (planttechagro inspired) -->
    <div class="notice-ticker-bar py-1">
        <div class="container-fluid px-3 d-flex align-items-center">
            <div class="notice-badge me-3">
                <i class="fas fa-bullhorn text-dark"></i> NOTICE
            </div>
            <div class="marquee-track flex-grow-1">
                <div class="marquee-content font-mono small text-light">
                    <span class="d-inline-flex align-items-center"><span class="pulse-live me-2"></span> 🚀 <strong>Production Release:</strong> v2.4 Enterprise Architecture Engine is live.</span>
                    <span class="d-inline-flex align-items-center"><i class="fas fa-calendar-check text-info me-2"></i> 📅 Q1/Q2 Production Slots Open for Custom Web Apps &amp; Mobile Builds.</span>
                    <span class="d-inline-flex align-items-center"><i class="fas fa-shield-alt text-success me-2"></i> 🔒 Strict Multi-Tenant Isolation &amp; Concurrency-Safe Ledger Active.</span>
                    <span class="d-inline-flex align-items-center"><i class="fab fa-whatsapp text-success me-2"></i> ⚡ Direct WhatsApp Consultation: +91 96227 56099</span>
                    <!-- Duplicate sequence for smooth seamless infinite loop -->
                    <span class="d-inline-flex align-items-center"><span class="pulse-live me-2"></span> 🚀 <strong>Production Release:</strong> v2.4 Enterprise Architecture Engine is live.</span>
                    <span class="d-inline-flex align-items-center"><i class="fas fa-calendar-check text-info me-2"></i> 📅 Q1/Q2 Production Slots Open for Custom Web Apps &amp; Mobile Builds.</span>
                    <span class="d-inline-flex align-items-center"><i class="fas fa-shield-alt text-success me-2"></i> 🔒 Strict Multi-Tenant Isolation &amp; Concurrency-Safe Ledger Active.</span>
                    <span class="d-inline-flex align-items-center"><i class="fab fa-whatsapp text-success me-2"></i> ⚡ Direct WhatsApp Consultation: +91 96227 56099</span>
                </div>
            </div>
            <div class="d-none d-md-flex align-items-center ms-3 ps-3 border-start border-secondary border-opacity-25 text-nowrap font-mono" style="font-size: 11px;">
                <span class="text-success"><i class="fas fa-signal me-1"></i> LATENCY 14ms</span>
            </div>
        </div>
    </div>

    <!-- Cyber Background Grid -->
    <div class="cyber-grid"></div>

    <!-- Glowing Background Ambient Orbs -->
    <div class="orb-glow orb-cyan"></div>
    <div class="orb-glow orb-purple"></div>
    <div class="orb-glow orb-blue"></div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg public-navbar py-3">
        <div class="container position-relative z-10">
            <a class="navbar-brand d-flex align-items-center text-white" href="{{ route('home') }}">
                <div class="brand-logo-cyber me-2">UB</div>
                <div>
                    <span class="fw-bold tracking-tight text-white fs-5">theumer<span class="text-info">.store</span></span>
                </div>
            </a>
            <button class="navbar-toggler border-0 shadow-none text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <i class="fas fa-bars fs-4 text-info"></i>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('portfolio*') ? 'active' : '' }}" href="{{ route('portfolio') }}">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#architecture">Architecture</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact*') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @auth
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Administrator'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-cyber-primary btn-sm px-3">
                                <i class="fas fa-terminal me-1"></i> Admin Console
                            </a>
                        @else
                            <a href="{{ route('client.dashboard') }}" class="btn btn-cyber-outline btn-sm px-3">
                                <i class="fas fa-user-circle me-1"></i> Client Portal
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-cyber-outline btn-sm text-danger border-danger px-2" title="Sign Out">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-cyber-primary btn-sm px-4">
                            <i class="fas fa-shield-alt me-1"></i> Client Desk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3 position-relative z-10">
            <div class="alert alert-success border-0 shadow-lg alert-dismissible fade show d-flex align-items-center py-2 px-3 text-dark" style="background: #10b981;" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div class="small fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3 position-relative z-10">
            <div class="alert alert-danger border-0 shadow-lg alert-dismissible fade show d-flex align-items-center py-2 px-3 text-white" style="background: #ef4444;" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div class="small fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main class="position-relative z-10">
        @yield('content')
    </main>

    <!-- Futuristic Cyber Footer -->
    <footer class="footer-cyber mt-5">
        <div class="container position-relative z-10">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="brand-logo-cyber me-2">UB</div>
                        <h5 class="text-white fw-bold mb-0">The Umer Store</h5>
                    </div>
                    <p class="small text-muted mb-4">Production-grade enterprise web applications, real-time client management portals, SLA support workflows, and scalable cloud systems.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-cyber-outline rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="fab fa-github"></i></a>
                        <a href="#" class="btn btn-sm btn-cyber-outline rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-sm btn-cyber-outline rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white fw-bold mb-3 font-mono text-uppercase" style="font-size: 13px; letter-spacing: 0.05em;">Capabilities</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#services" class="text-muted text-decoration-none">Laravel Architecture</a></li>
                        <li class="mb-2"><a href="#services" class="text-muted text-decoration-none">Client Portals</a></li>
                        <li class="mb-2"><a href="#services" class="text-muted text-decoration-none">API &amp; Cloud Systems</a></li>
                        <li class="mb-2"><a href="#services" class="text-muted text-decoration-none">Database Optimization</a></li>
                        <li class="mb-2"><a href="#services" class="text-muted text-decoration-none">SLA Helpdesk</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3 font-mono text-uppercase" style="font-size: 13px; letter-spacing: 0.05em;">Client Gateway</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-muted text-decoration-none"><i class="fas fa-lock me-1 text-info"></i> Portal Sign In</a></li>
                        <li class="mb-2"><a href="{{ route('portfolio') }}" class="text-muted text-decoration-none"><i class="fas fa-cubes me-1 text-info"></i> Case Studies</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-muted text-decoration-none"><i class="fas fa-paper-plane me-1 text-info"></i> Project Consultation</a></li>
                        <li class="mb-2"><span class="badge bg-success-subtle text-success border border-success-subtle font-mono">99.9% Production Uptime</span></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3 font-mono text-uppercase" style="font-size: 13px; letter-spacing: 0.05em;">Direct Channel</h6>
                    <p class="small text-muted mb-2"><i class="fas fa-envelope me-2 text-info"></i> <a href="mailto:hello@theumer.store" class="text-muted text-decoration-none">hello@theumer.store</a></p>
                    <p class="small text-muted mb-2"><i class="fas fa-phone me-2 text-info"></i> <a href="tel:+919622756099" class="text-muted text-decoration-none">+91 96227 56099</a></p>
                    <p class="small text-muted mb-2"><i class="fab fa-whatsapp me-2 text-success"></i> <a href="https://wa.me/9109622756099" target="_blank" class="text-success text-decoration-none">Chat on WhatsApp</a></p>
                    <p class="small text-muted mb-0"><i class="fas fa-map-marker-alt me-2 text-info"></i> Jammu &amp; Kashmir, India</p>
                </div>
            </div>
            <hr class="border-secondary opacity-25 mt-4 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
                <div>&copy; {{ date('Y') }} <strong>Umer Bashir</strong> &bull; The Umer Store. All rights reserved.</div>
                <div class="mt-2 mt-md-0 font-mono" style="font-size: 11px;">
                    <span class="badge bg-dark text-info border border-info me-2">ENTERPRISE CORE</span>
                    <span class="badge bg-dark text-success border border-success">SSL ENCRYPTED</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Action Button (Responsive) -->
    <a href="https://wa.me/9109622756099?text=Hello%20Umer%2C%20I%20visited%20your%20portal%20and%20would%20like%20to%20discuss%20a%20project." 
       target="_blank" 
       rel="noopener" 
       class="wa-float-btn float-slow"
       title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span>Chat on WhatsApp</span>
    </a>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
