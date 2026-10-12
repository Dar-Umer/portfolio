@extends('layouts.public')

@section('title', 'Umer Bashir | Premium Full-Stack Architect & Enterprise Software Platform')

@push('styles')
<style>
    /* Hero Interactive Glow & Responsive Typography */
    .hero-container {
        position: relative;
        overflow: hidden;
    }

    .hero-title {
        font-size: clamp(2rem, 5.2vw, 3.75rem);
        line-height: 1.15;
        letter-spacing: -0.04em;
    }

    .hero-lead {
        font-size: clamp(1rem, 2.2vw, 1.18rem);
        line-height: 1.75;
        color: #cbd5e1 !important;
    }

    .hero-actions .btn {
        min-height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 576px) {
        .hero-actions {
            flex-direction: column;
            width: 100%;
        }
        .hero-actions .btn {
            width: 100%;
        }
    }

    .hero-spotlight {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(750px circle at var(--gx, 50%) var(--gy, 30%), rgba(0, 242, 254, 0.15), transparent 60%);
        z-index: 1;
        transition: background 0.05s ease-out;
    }

    .typing-cursor {
        display: inline-block;
        width: 3px;
        height: 1.1em;
        background-color: #00f2fe;
        vertical-align: middle;
        margin-left: 4px;
        animation: blink 0.85s infinite;
        box-shadow: 0 0 10px #00f2fe;
    }

    @keyframes blink {
        0%, 49% { opacity: 1; }
        50%, 100% { opacity: 0; }
    }

    /* Production Project Slider */
    .slider-card {
        background: rgba(13, 18, 36, 0.9);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(0, 242, 254, 0.25);
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 25px rgba(0, 242, 254, 0.1);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @media (max-width: 991px) {
        .slider-card.float-slow {
            animation: none;
        }
    }

    .slider-slide {
        display: none;
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .slider-slide.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .slide-dot {
        width: 32px;
        height: 8px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.25);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
    }

    .slide-dot.active {
        background: linear-gradient(90deg, #00f2fe, #4facfe);
        width: 48px;
        box-shadow: 0 0 10px rgba(0, 242, 254, 0.6);
    }

    /* Responsive Filter Bar */
    .filter-scroll-wrapper {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        flex-wrap: nowrap;
    }
    .filter-scroll-wrapper::-webkit-scrollbar {
        display: none;
    }
    @media (min-width: 768px) {
        .filter-scroll-wrapper {
            flex-wrap: wrap;
            overflow-x: visible;
        }
    }

    .filter-btn {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #cbd5e1;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 0.88rem;
        cursor: pointer;
        transition: all 0.25s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .filter-btn:hover {
        background: rgba(0, 242, 254, 0.12);
        border-color: rgba(0, 242, 254, 0.5);
        color: #00f2fe;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #00f2fe 0%, #0066cc 100%);
        color: #070913;
        border-color: transparent;
        font-weight: 700;
        box-shadow: 0 0 20px rgba(0, 242, 254, 0.4);
    }

    /* Stack chip badges */
    .stack-chip {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 8px 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        color: #f1f5f9;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .stack-chip:hover {
        background: rgba(0, 242, 254, 0.1);
        border-color: rgba(0, 242, 254, 0.4);
        color: #00f2fe;
        transform: translateY(-2px);
    }

    @media (max-width: 576px) {
        .stat-card-num {
            font-size: 1.5rem !important;
        }
        .stat-card-label {
            font-size: 11px !important;
        }
    }
</style>
@endpush

@section('content')
    @php
        $hero = $sections['hero'] ?? null;
        $heroContent = $hero ? $hero->content : [];
    @endphp

    <!-- 1. Hero Section with Mouse Glow & Dynamic Headline -->
    <section class="hero-container position-relative pt-4 pt-lg-5 pb-5 pb-lg-6" id="heroSection">
        <div class="hero-spotlight" id="heroSpotlight"></div>

        <div class="container position-relative z-10 py-lg-4">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <!-- Status Chip -->
                    <div class="cyber-badge mb-3 d-inline-flex">
                        <span class="pulse-live"></span>
                        <span>AVAILABLE FOR ENTERPRISE ARCHITECTURE &amp; FULL BUILDS</span>
                    </div>

                    <!-- Glowing Animated Dynamic Headline with Typewriter -->
                    <h1 class="hero-title fw-bold mb-3 text-white">
                        Hi, I'm <span class="neon-text-cyan">Umer Bashir</span><br>
                        <span>I Architect </span>
                        <span class="neon-text-purple" id="typewriterText">High-Performance Web Systems</span>
                        <span class="typing-cursor"></span>
                    </h1>

                    <p class="hero-lead mb-4 mx-auto mx-lg-0" style="max-width: 620px;">
                        Specialized full-stack software engineer delivering robust client portals, secure enterprise architectures, and resilient backends with
                        <strong class="text-white">Laravel 12</strong>, <strong class="text-white">PHP 8.2+</strong>, 
                        <strong class="text-white">JavaScript</strong>, and <strong class="text-white">Mobile Architectures</strong>.
                    </p>

                    <!-- Responsive Action Buttons -->
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 gap-sm-3 mb-4 mb-lg-5 hero-actions">
                        <a href="{{ route('contact') }}" class="btn btn-cyber-primary btn-lg px-4 py-3 fw-bold">
                            <i class="fas fa-bolt me-2"></i> Hire Me Now
                        </a>
                        <a href="#projects" class="btn btn-cyber-outline btn-lg px-4 py-3">
                            <i class="fas fa-layer-group me-2"></i> View Production Work
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-cyber-outline btn-lg px-3 py-3" title="Access Client Portal">
                            <i class="fas fa-shield-alt text-info me-1"></i> Client Desk
                        </a>
                    </div>

                    <!-- Metrics Proof Bar (Responsive 2x2 on Mobile, 4-col on Desktop) -->
                    <div class="row g-2 g-sm-3 text-center text-lg-start">
                        <div class="col-6 col-md-3">
                            <div class="p-3 card-cyber text-center h-100">
                                <div class="fs-3 fw-bold neon-text-cyan mb-0 font-mono stat-card-num">4+</div>
                                <div class="mt-1 stat-card-label" style="font-size: 12px; color: #94a3b8; font-weight: 500;">Live Production</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 card-cyber text-center h-100">
                                <div class="fs-3 fw-bold text-white mb-0 font-mono stat-card-num">8+</div>
                                <div class="mt-1 stat-card-label" style="font-size: 12px; color: #94a3b8; font-weight: 500;">Years Exp</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 card-cyber text-center h-100">
                                <div class="fs-3 fw-bold neon-text-purple mb-0 font-mono stat-card-num">100%</div>
                                <div class="mt-1 stat-card-label" style="font-size: 12px; color: #94a3b8; font-weight: 500;">Client Focus</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 card-cyber text-center h-100">
                                <div class="fs-3 fw-bold text-success mb-0 font-mono stat-card-num">24/7</div>
                                <div class="mt-1 stat-card-label" style="font-size: 12px; color: #94a3b8; font-weight: 500;">SLA Mindset</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Production Project Showcase Carousel -->
                <div class="col-lg-5">
                    <div class="slider-card position-relative float-slow" id="heroProjectSlider">
                        <!-- Slider Top Window Chrome -->
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom border-secondary border-opacity-25" style="background: rgba(4, 6, 13, 0.9);">
                            <div class="d-flex gap-2">
                                <span class="rounded-circle" style="width: 10px; height: 10px; background: #ef4444; box-shadow: 0 0 6px #ef4444;"></span>
                                <span class="rounded-circle" style="width: 10px; height: 10px; background: #f59e0b; box-shadow: 0 0 6px #f59e0b;"></span>
                                <span class="rounded-circle" style="width: 10px; height: 10px; background: #10b981; box-shadow: 0 0 6px #10b981;"></span>
                            </div>
                            <span class="small font-mono" style="font-size: 12px; color: #94a3b8;" id="sliderCounter">PROD_PREVIEW [1/{{ count($projects) > 0 ? count($projects) : 1 }}]</span>
                            <span class="badge bg-success-subtle text-success border border-success font-mono" style="font-size: 10px;">VERIFIED LIVE</span>
                        </div>

                        <!-- Slides Container -->
                        <div class="p-3 p-sm-4" style="min-height: 340px;">
                            @forelse($projects as $index => $sliderProj)
                                <div class="slider-slide {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-dark text-info border border-info font-mono">{{ $sliderProj->category->name ?? 'Enterprise' }}</span>
                                        <span class="text-success small font-mono"><i class="fas fa-check-circle me-1"></i> {{ $sliderProj->status }}</span>
                                    </div>

                                    <h4 class="fw-bold text-white mb-2">{{ $sliderProj->title }}</h4>
                                    <p class="small mb-3" style="line-height: 1.6; color: #cbd5e1;">{{ $sliderProj->short_description }}</p>

                                    <!-- Code / Tech Matrix inside Slide -->
                                    <div class="p-3 rounded-3 mb-3 font-mono small text-start overflow-hidden" style="background: rgba(4, 6, 13, 0.85); border: 1px solid rgba(255, 255, 255, 0.12); font-size: 11.5px; line-height: 1.7;">
                                        <div class="text-info text-truncate"><span style="color: #94a3b8;">$</span> client: <span class="text-white">{{ $sliderProj->client_name ?? 'Confidential Enterprise' }}</span></div>
                                        <div class="text-light text-truncate">&bull; Stack: {{ implode(' &bull; ', array_slice($sliderProj->technologies ?? ['Laravel', 'MySQL'], 0, 4)) }}</div>
                                        <div class="text-success">&check; Concurrency Lock: VERIFIED</div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-1 mb-4">
                                        @foreach(array_slice($sliderProj->technologies ?? [], 0, 4) as $tech)
                                            <span class="badge bg-dark text-light border border-secondary font-mono small" style="font-size: 10.5px;">{{ $tech }}</span>
                                        @endforeach
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25 gap-2">
                                        <a href="{{ route('portfolio.detail', $sliderProj->slug) }}" class="btn btn-sm btn-cyber-primary px-3 font-mono text-nowrap">
                                            Case Study &rarr;
                                        </a>
                                        @if($sliderProj->project_url)
                                            <a href="{{ $sliderProj->project_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-cyber-outline px-2 font-mono text-nowrap">
                                                Live URL &nearr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="slider-slide active text-center py-5">
                                    <div class="text-info mb-2"><i class="fas fa-server fa-3x"></i></div>
                                    <h5 class="text-white">Enterprise Systems</h5>
                                    <p class="small" style="color: #cbd5e1;">Systems online and verified.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Slider Navigation Controls -->
                        <div class="px-3 px-sm-4 pb-3 d-flex align-items-center justify-content-between" style="background: rgba(4, 6, 13, 0.6);">
                            <div class="d-flex gap-2" id="sliderDots">
                                @foreach($projects as $index => $p)
                                    <button class="slide-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})" aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-cyber-outline p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" onclick="prevSlide()" title="Previous Project" aria-label="Previous">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-sm btn-cyber-outline p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" onclick="nextSlide()" title="Next Project" aria-label="Next">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Core Stack Matrix -->
    <section class="py-4 position-relative border-top border-bottom border-secondary border-opacity-25" style="background: rgba(13, 18, 36, 0.6); backdrop-filter: blur(10px);">
        <div class="container position-relative z-10">
            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3 text-center text-lg-start">
                <div>
                    <span class="text-uppercase text-info font-mono fw-bold" style="letter-spacing: 0.1em; font-size: 11px;">
                        &lt;CORE_STACK_MATRIX&gt;
                    </span>
                    <h6 class="text-white fw-bold mb-0 mt-1">Enterprise Architecture Engine</h6>
                </div>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-end gap-2">
                    <span class="stack-chip font-mono"><i class="fab fa-laravel text-danger"></i> Laravel 12</span>
                    <span class="stack-chip font-mono"><i class="fab fa-php text-primary"></i> PHP 8.2+</span>
                    <span class="stack-chip font-mono"><i class="fas fa-database text-info"></i> MySQL / MariaDB</span>
                    <span class="stack-chip font-mono"><i class="fab fa-js text-warning"></i> JavaScript</span>
                    <span class="stack-chip font-mono"><i class="fab fa-docker text-primary"></i> Docker</span>
                    <span class="stack-chip font-mono"><i class="fab fa-android text-success"></i> Mobile Architectures</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Core Professional Services Section -->
    <section id="services" class="py-5 py-lg-6 position-relative">
        <div class="container position-relative z-10 py-2">
            <div class="text-center mx-auto mb-5" style="max-width: 680px;">
                <span class="cyber-badge mb-2">SOLUTIONS ARCHITECTURE</span>
                <h2 class="display-6 fw-bold mt-2 text-white">Core Professional Services</h2>
                <p class="mb-4" style="color: #94a3b8; font-size: 1.05rem;">High-reliability engineering practices covering the complete product lifecycle from architecture to production operations.</p>
            </div>

            <div class="row g-3 g-md-4">
                @foreach($services as $service)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card-cyber h-100 p-4 d-flex flex-column">
                            <div class="rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; background: rgba(0, 242, 254, 0.1); color: #00f2fe; font-size: 20px; box-shadow: 0 0 16px rgba(0, 242, 254, 0.2);">
                                <i class="{{ $service->icon }}"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-white">{{ $service->title }}</h5>
                            <p class="small mb-4 flex-grow-1" style="line-height: 1.6; color: #cbd5e1;">{{ $service->short_description }}</p>
                            
                            @if($service->starting_price)
                                <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center mb-3">
                                    <span class="small font-mono" style="font-size: 11px; color: #94a3b8;">BASE ESTIMATE</span>
                                    <span class="fw-bold text-info font-mono">₹{{ number_format($service->starting_price, 2) }}</span>
                                </div>
                            @endif

                            <a href="{{ route('contact') }}" class="btn btn-sm btn-cyber-outline w-100 font-mono py-2" style="font-size: 12px;">
                                Inquire Scope &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. Interactive Architecture Topology & Pillars -->
    <section id="architecture" class="py-5 py-lg-6 position-relative border-top border-bottom border-secondary border-opacity-25" style="background: rgba(13, 18, 36, 0.4);">
        <div class="container position-relative z-10 py-2">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6">
                    <span class="cyber-badge mb-2">SYSTEM TOPOLOGY</span>
                    <h2 class="display-6 fw-bold mt-2 mb-3 text-white">Engineered with Concurrency &amp; Tenant Isolation</h2>
                    <p class="mb-4" style="color: #94a3b8; font-size: 1.05rem;">Unlike generic software setups, our core engine enforces transactional safety, concurrency guarantees, and strict multi-tenant customer separation.</p>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-start p-3 card-cyber">
                            <div class="rounded-circle p-2 me-3 mt-1 d-flex align-items-center justify-content-center text-info flex-shrink-0" style="width: 36px; height: 36px; background: rgba(0, 242, 254, 0.1);">
                                <i class="fas fa-lock fs-6"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-white">Strict Tenant Data Isolation</h6>
                                <p class="small mb-0" style="color: #cbd5e1; line-height: 1.6;">Every client query is strictly scoped by authenticated client profile. Internal developer notes are permanently hidden from client views.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start p-3 card-cyber">
                            <div class="rounded-circle p-2 me-3 mt-1 d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 36px; height: 36px; background: rgba(16, 185, 129, 0.1);">
                                <i class="fas fa-file-contract fs-6"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-white">Immutable Billing Snapshots &amp; Concurrency Locking</h6>
                                <p class="small mb-0" style="color: #cbd5e1; line-height: 1.6;">Sequential documents (<code class="text-info font-mono">INV-YYYY-XXXXXX</code>) utilize row-level transactional database locks to completely eliminate race conditions and duplicates.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start p-3 card-cyber">
                            <div class="rounded-circle p-2 me-3 mt-1 d-flex align-items-center justify-content-center text-warning flex-shrink-0" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.1);">
                                <i class="fas fa-stopwatch fs-6"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-white">Finite State Machine &amp; SLA Tracking</h6>
                                <p class="small mb-0" style="color: #cbd5e1; line-height: 1.6;">Support tickets follow rigorous lifecycle validations (Open &rarr; In Progress &rarr; Client Action &rarr; Resolved &rarr; Closed) with automated SLA breach alarms.</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('contact') }}" class="btn btn-cyber-primary px-4 py-3 fw-semibold">
                        Request Architecture Review <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="p-3 p-sm-4 p-md-5 rounded-4 card-cyber position-relative" style="border: 1px solid rgba(0, 242, 254, 0.2);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-white font-mono fs-6"><i class="fas fa-cubes text-info me-2"></i> ARCHITECTURE_TOPOLOGY_V2</h5>
                            <span class="badge bg-success-subtle text-success border border-success font-mono" style="font-size: 10px;">ACTIVE ENGINE</span>
                        </div>
                        
                        <div class="p-3 rounded-3 mb-3" style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                <span class="fw-semibold small text-info font-mono"><i class="fas fa-globe me-1"></i> Public Presentation Layer</span>
                                <span class="badge bg-dark text-info border border-info font-mono" style="font-size: 10px;">Blade &bull; Bootstrap 5</span>
                            </div>
                            <div class="small" style="font-size: 12px; color: #cbd5e1; line-height: 1.5;">SEO-optimized responsive UI, dynamic CMS section editor, portfolio showcase.</div>
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                <span class="fw-semibold small text-success font-mono"><i class="fas fa-id-card me-1"></i> Client Portal &amp; Ticketing Desk</span>
                                <span class="badge bg-dark text-success border border-success font-mono" style="font-size: 10px;">Isolated Auth Scope</span>
                            </div>
                            <div class="small" style="font-size: 12px; color: #cbd5e1; line-height: 1.5;">Customer self-service, ticket filing, real-time replies, quote acceptance &amp; invoice history.</div>
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                <span class="fw-semibold small text-warning font-mono"><i class="fas fa-shield-alt me-1"></i> Enterprise Admin Console</span>
                                <span class="badge bg-dark text-warning border border-warning font-mono" style="font-size: 10px;">Full RBAC Guard</span>
                            </div>
                            <div class="small" style="font-size: 12px; color: #cbd5e1; line-height: 1.5;">Client CRM profiles, project milestone timelines, Git sync, GST billing &amp; audit logging.</div>
                        </div>

                        <div class="p-3 rounded-3" style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                <span class="fw-semibold small text-cyan font-mono" style="color: #00f2fe;"><i class="fas fa-database me-1"></i> Resilient Storage &amp; Workflows</span>
                                <span class="badge bg-dark text-info border border-info font-mono" style="font-size: 10px;">MySQL / MariaDB</span>
                            </div>
                            <div class="small" style="font-size: 12px; color: #cbd5e1; line-height: 1.5;">Transactional row locking, immutable billing snapshots, audit trail logs &amp; SLA calculations.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Real Production Projects with Interactive Category Filters -->
    <section id="projects" class="py-5 py-lg-6 position-relative">
        <div class="container position-relative z-10 py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
                <div>
                    <span class="cyber-badge mb-2">PRODUCTION SHOWCASE</span>
                    <h2 class="display-6 fw-bold mt-2 mb-0 text-white">Real Projects, <span class="neon-text-cyan">Live in Production</span></h2>
                </div>
                <a href="{{ route('portfolio') }}" class="btn btn-cyber-outline">
                    View Complete Portfolio <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            <!-- Responsive Category Filter Buttons (Horizontal swipe on mobile) -->
            <div class="filter-scroll-wrapper mb-4" id="portfolioFilterBar">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="SaaS Platforms">SaaS Platforms</button>
                <button class="filter-btn" data-filter="Enterprise ERP">Enterprise ERP</button>
                <button class="filter-btn" data-filter="Government Portals">Government Portals</button>
                <button class="filter-btn" data-filter="E-Commerce & Delivery">E-Commerce &amp; Delivery</button>
            </div>

            <div class="row g-4" id="portfolioGrid">
                @foreach($projects as $proj)
                    @php
                        $categoryName = $proj->category->name ?? 'Enterprise ERP';
                    @endphp
                    <div class="col-12 col-md-6 portfolio-item" data-category="{{ $categoryName }}">
                        <div class="card-cyber h-100 p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-dark text-info border border-info font-mono px-2 py-1">{{ $categoryName }}</span>
                                <span class="badge bg-dark text-success border border-success font-mono px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ $proj->status }}</span>
                            </div>

                            <h4 class="fw-bold mb-2">
                                <a href="{{ route('portfolio.detail', $proj->slug) }}" class="text-decoration-none text-white hover-cyan">
                                    {{ $proj->title }}
                                </a>
                            </h4>
                            <p class="small mb-4" style="line-height: 1.6; color: #cbd5e1;">{{ $proj->short_description }}</p>

                            <div class="d-flex flex-wrap gap-1 mb-4">
                                @if($proj->technologies)
                                    @foreach($proj->technologies as $tech)
                                        <span class="badge bg-dark text-light border border-secondary font-mono small" style="font-size: 11px;">{{ $tech }}</span>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center gap-2">
                                <span class="small font-mono text-truncate" style="color: #94a3b8;"><i class="fas fa-building me-1 text-info"></i> {{ $proj->client_name ?? 'Client Project' }}</span>
                                <div class="d-flex gap-2 flex-shrink-0">
                                    @if($proj->project_url)
                                        <a href="{{ $proj->project_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-cyber-outline py-1 px-2 font-mono" style="font-size: 11px;">
                                            Live Site &nearr;
                                        </a>
                                    @endif
                                    <a href="{{ route('portfolio.detail', $proj->slug) }}" class="btn btn-sm btn-cyber-primary py-1 px-3 font-mono" style="font-size: 11px;">
                                        Details &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. Client Testimonials -->
    @if($testimonials->count() > 0)
        <section class="py-5 py-lg-6 position-relative border-top border-bottom border-secondary border-opacity-25" style="background: rgba(13, 18, 36, 0.4);">
            <div class="container position-relative z-10 py-2">
                <div class="text-center mx-auto mb-5" style="max-width: 650px;">
                    <span class="cyber-badge mb-2">RECOMMENDATIONS</span>
                    <h2 class="display-6 fw-bold mt-2 text-white">What Clients &amp; Partners Say</h2>
                    <p style="color: #94a3b8; font-size: 1.05rem;">Direct feedback from business owners and engineering teams.</p>
                </div>

                <div class="row justify-content-center g-4">
                    @foreach($testimonials as $test)
                        <div class="col-12 col-lg-8">
                            <div class="card-cyber p-4 p-md-5 text-center" style="border: 1px solid rgba(0, 242, 254, 0.2);">
                                <div class="text-warning mb-3">
                                    @for($i=0; $i<$test->rating; $i++)
                                        <i class="fas fa-star" style="color: #00f2fe; filter: drop-shadow(0 0 6px #00f2fe);"></i>
                                    @endfor
                                </div>
                                <blockquote class="lead fst-italic mb-4" style="font-size: 1.15rem; line-height: 1.8; color: #f8fafc;">
                                    "{{ $test->comment }}"
                                </blockquote>
                                <div class="fw-bold text-white fs-6 font-mono">{{ $test->client_name }}</div>
                                <div class="small font-mono mt-1" style="color: #94a3b8;">{{ $test->client_position }} &bull; {{ $test->company_name }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 7. High-Converting Futuristic Call to Action -->
    <section class="py-5 py-lg-6 position-relative text-white overflow-hidden" style="background: radial-gradient(circle at center, #0f172a 0%, #070913 100%); border-top: 1px solid rgba(0, 242, 254, 0.2);">
        <div class="container position-relative z-10 py-3 text-center">
            <span class="cyber-badge mb-3">START COLLABORATION</span>
            <h2 class="display-5 fw-bold mb-3 text-white">Let's Build Your <span class="neon-text-cyan">Next Big Platform</span></h2>
            <p class="lead mx-auto mb-4" style="max-width: 650px; font-size: 1.15rem; color: #cbd5e1;">
                Tell me your project idea — whether a delivery ecosystem, e-commerce store, government portal, or custom business system — and let's engineer it to production scale.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2 gap-sm-3 mb-4">
                <a href="{{ route('contact') }}" class="btn btn-cyber-primary btn-lg px-4 px-sm-5 py-3 fw-bold">
                    <i class="fas fa-paper-plane me-2"></i> Start Project Consultation
                </a>
                <a href="https://wa.me/9109622756099?text=Hello%20Umer%2C%20I%20would%20like%20to%20discuss%20a%20project." target="_blank" rel="noopener" class="btn btn-cyber-outline btn-lg px-4 py-3 fw-semibold">
                    <i class="fab fa-whatsapp text-success me-2"></i> Chat on WhatsApp
                </a>
            </div>
            <div>
                <span class="badge bg-dark text-info border border-info font-mono py-2 px-3 small">
                    <i class="fas fa-clock me-1"></i> Average Response SLA: Under 2 Hours
                </span>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // 1. Mouse Spotlight Tracking on Hero
    (function initHeroSpotlight() {
        const heroSection = document.getElementById('heroSection');
        const heroSpotlight = document.getElementById('heroSpotlight');
        if (!heroSection || !heroSpotlight) return;

        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            heroSpotlight.style.setProperty('--gx', `${x}px`);
            heroSpotlight.style.setProperty('--gy', `${y}px`);
        });
    })();

    // 2. Typewriter headline animation
    (function initTypewriter() {
        const phrases = [
            "High-Performance Web Systems",
            "Enterprise Laravel Architectures",
            "Scalable Client Ticketing Portals",
            "Resilient Multi-Tenant Platforms",
            "Mission-Critical Cloud APIs"
        ];
        const el = document.getElementById('typewriterText');
        if (!el) return;

        let phraseIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let delay = 100;

        function typeLoop() {
            const currentPhrase = phrases[phraseIndex];

            if (isDeleting) {
                el.textContent = currentPhrase.substring(0, charIndex - 1);
                charIndex--;
                delay = 45;
            } else {
                el.textContent = currentPhrase.substring(0, charIndex + 1);
                charIndex++;
                delay = 100;
            }

            if (!isDeleting && charIndex === currentPhrase.length) {
                isDeleting = true;
                delay = 2000;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                phraseIndex = (phraseIndex + 1) % phrases.length;
                delay = 400;
            }

            setTimeout(typeLoop, delay);
        }

        typeLoop();
    })();

    // 3. Hero Project Showcase Slider
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider-slide');
    const dots = document.querySelectorAll('.slide-dot');
    const sliderCounter = document.getElementById('sliderCounter');
    const totalSlides = slides.length;
    let autoSlideTimer = null;

    function showSlide(index) {
        if (totalSlides === 0) return;
        currentSlide = (index + totalSlides) % totalSlides;

        slides.forEach((s, i) => {
            s.classList.toggle('active', i === currentSlide);
        });

        dots.forEach((d, i) => {
            d.classList.toggle('active', i === currentSlide);
        });

        if (sliderCounter) {
            sliderCounter.textContent = `PROD_PREVIEW [${currentSlide + 1}/${totalSlides}]`;
        }
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
        resetAutoSlide();
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
        resetAutoSlide();
    }

    function goToSlide(index) {
        showSlide(index);
        resetAutoSlide();
    }

    function resetAutoSlide() {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
        if (totalSlides > 1) {
            autoSlideTimer = setInterval(() => {
                showSlide(currentSlide + 1);
            }, 6000);
        }
    }

    if (totalSlides > 1) {
        resetAutoSlide();
    }

    // 4. Portfolio Category Filtering
    (function initPortfolioFilters() {
        const filterBtns = document.querySelectorAll('#portfolioFilterBar .filter-btn');
        const items = document.querySelectorAll('#portfolioGrid .portfolio-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');

                items.forEach(item => {
                    const itemCat = item.getAttribute('data-category') || '';
                    if (filter === 'all' || itemCat.toLowerCase().includes(filter.toLowerCase())) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    })();
</script>
@endpush
