@extends('layouts.public')

@section('title', 'Get in Touch | The Umer Store')

@section('content')
<div class="container py-5">
    <div class="row g-4 align-items-stretch">
        <!-- Contact Info Column -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 p-md-5 h-100 text-white" style="background: radial-gradient(circle at top right, #1e293b 0%, #0f172a 100%);">
                <span class="badge bg-primary px-3 py-2 text-uppercase mb-3 fw-semibold align-self-start" style="font-size: 11px;">Direct Communication</span>
                <h2 class="display-6 fw-bold text-white mb-2">Let's Discuss Your Next Platform</h2>
                <p class="text-light opacity-75 small mb-4" style="line-height: 1.6;">
                    Whether you need a custom e-commerce system, government portal, delivery platform, or Laravel architecture consultation — I'm ready to collaborate.
                </p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="rounded-circle bg-success text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fab fa-whatsapp fs-5"></i>
                        </div>
                        <div>
                            <div class="small text-muted" style="font-size: 11px; text-transform: uppercase;">Instant WhatsApp</div>
                            <a href="https://wa.me/9109622756099?text=Hello%20Umer%2C%20I%20would%20like%20to%20discuss%20a%20project." target="_blank" class="fw-bold text-white text-decoration-none">
                                +91 96227 56099
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="rounded-circle bg-primary text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-phone fs-6"></i>
                        </div>
                        <div>
                            <div class="small text-muted" style="font-size: 11px; text-transform: uppercase;">Direct Call</div>
                            <a href="tel:+919622756099" class="fw-bold text-white text-decoration-none">+91 96227 56099</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="rounded-circle bg-info text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-envelope fs-6"></i>
                        </div>
                        <div>
                            <div class="small text-muted" style="font-size: 11px; text-transform: uppercase;">Email Address</div>
                            <a href="mailto:hello@theumer.store" class="fw-bold text-white text-decoration-none">hello@theumer.store</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="rounded-circle bg-secondary text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt fs-6"></i>
                        </div>
                        <div>
                            <div class="small text-muted" style="font-size: 11px; text-transform: uppercase;">Operating Location</div>
                            <div class="fw-semibold text-white">Jammu &amp; Kashmir, India</div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 small text-muted">
                    <span class="text-success"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Available</span> for Freelance &bull; MVPs &bull; Full Enterprise Delivery
                </div>
            </div>
        </div>

        <!-- Inquiry Form Column -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 p-md-5 bg-white h-100 shadow-sm border-0">
                <div class="mb-4">
                    <span class="section-tag mb-2">Project Inquiry</span>
                    <h3 class="fw-bold text-dark mb-1">Send Project Requirements</h3>
                    <p class="text-muted small">Submit your message below. It logs instantly to the platform lead engine.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.post') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Your Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Business Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@company.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Phone Number (Optional)</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Subject / System Type</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="e.g. Delivery Platform / Portal" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Project Specifications &amp; Objectives</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Briefly describe your requirements, key features, target deadline, or technical scope..." required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold shadow-sm">
                        Send Inquiry Message <i class="fas fa-paper-plane ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
