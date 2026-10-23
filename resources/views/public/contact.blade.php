@extends('layouts.public')

@section('title', 'Get in Touch | The Umer Store')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row g-4 align-items-stretch">
        <!-- Contact Info Column -->
        <div class="col-lg-5">
            <div class="card-cyber p-4 p-md-5 h-100 text-white d-flex flex-column" style="background: radial-gradient(circle at top right, #131c38 0%, #080d1d 100%); border: 1px solid rgba(0, 242, 254, 0.25);">
                <div class="mb-3">
                    <span class="cyber-badge mb-2">DIRECT COMMUNICATION</span>
                    <h2 class="display-6 fw-bold text-white mb-2">Let's Discuss Your Next Platform</h2>
                    <p class="text-light opacity-75 small mb-4" style="line-height: 1.6;">
                        Whether you need a custom e-commerce system, government portal, delivery platform, or Laravel architecture consultation — I'm ready to collaborate.
                    </p>
                </div>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="rounded-circle bg-success text-white p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; box-shadow: 0 0 12px rgba(37, 211, 102, 0.4);">
                            <i class="fab fa-whatsapp fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="small text-muted font-mono" style="font-size: 11px; text-transform: uppercase;">Instant WhatsApp</div>
                            <a href="https://wa.me/9109622756099?text=Hello%20Umer%2C%20I%20would%20like%20to%20discuss%20a%20project." target="_blank" class="fw-bold text-white text-decoration-none hover-cyan">
                                +91 96227 56099
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="rounded-circle bg-primary text-white p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fas fa-phone fs-6"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="small text-muted font-mono" style="font-size: 11px; text-transform: uppercase;">Direct Phone</div>
                            <a href="tel:+919622756099" class="fw-bold text-white text-decoration-none hover-cyan">+91 96227 56099</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="rounded-circle bg-info text-dark p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fas fa-envelope fs-6"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="small text-muted font-mono" style="font-size: 11px; text-transform: uppercase;">Engineering Email</div>
                            <a href="mailto:hello@theumer.store" class="fw-bold text-white text-decoration-none hover-cyan text-truncate d-block">hello@theumer.store</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div class="rounded-circle bg-secondary text-white p-2 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fas fa-map-marker-alt fs-6"></i>
                        </div>
                        <div>
                            <div class="small text-muted font-mono" style="font-size: 11px; text-transform: uppercase;">Primary Base</div>
                            <div class="fw-semibold text-white">Jammu &amp; Kashmir, India</div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 small text-muted">
                    <span class="text-success"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Available</span> for Architecture &bull; MVPs &bull; Enterprise Scale
                </div>
            </div>
        </div>

        <!-- Inquiry Form Column -->
        <div class="col-lg-7">
            <div class="card-cyber p-4 p-md-5 h-100 shadow-sm">
                <div class="mb-4">
                    <span class="cyber-badge mb-2">PROJECT INQUIRY</span>
                    <h3 class="fw-bold text-white mb-1">Send Project Requirements</h3>
                    <p class="text-muted small">Submit your message below. It logs directly to the platform triage queue.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small mb-4">
                        @foreach ($errors->all() as $error)
                            <div><i class="fas fa-exclamation-triangle me-1"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.post') }}">
                    @csrf

                    {{-- Honeypot anti-spam trap: invisible to legitimate users, robots fill it --}}
                    <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                        <label for="website_url">Do not fill this field</label>
                        <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-light">Your Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff;">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-light">Business Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@company.com" required style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-light">Phone / WhatsApp (Optional)</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91 98765 43210" style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff;">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-light">Subject / System Type</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="e.g. Delivery Platform / Portal" required style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-light">Project Specifications &amp; Objectives <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Briefly describe your requirements, key features, target deadline, or technical scope..." required style="background: rgba(4, 6, 13, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff;">{{ old('message') }}</textarea>
                    </div>

                    @php
                        $recaptchaSiteKey = \App\Models\Setting::get('recaptcha_site_key', config('services.recaptcha.site_key'));
                    @endphp

                    @if(!empty($recaptchaSiteKey))
                        <div class="mb-4 d-flex flex-column align-items-start">
                            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}" data-theme="dark"></div>
                            @error('g-recaptcha-response')
                                <div class="text-danger small mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif

                    <button type="submit" class="btn btn-cyber-primary w-100 py-3 fw-bold shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Submit Inquiry Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $recaptchaSiteKey = \App\Models\Setting::get('recaptcha_site_key', config('services.recaptcha.site_key'));
@endphp
@if(!empty($recaptchaSiteKey))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endpush
