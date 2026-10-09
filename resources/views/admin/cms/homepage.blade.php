@extends('layouts.admin')

@section('page_title', 'CMS: Homepage Dynamic Sections')

@section('content')
<div class="row g-4">
    @foreach($sections as $sec)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark text-uppercase"><i class="fas fa-layer-group text-primary me-2"></i> Section: {{ $sec->section_key }}</h6>
                    <span class="badge {{ $sec->is_enabled ? 'bg-success' : 'bg-secondary' }}">{{ $sec->is_enabled ? 'Active' : 'Disabled' }}</span>
                </div>

                <form method="POST" action="{{ route('admin.cms.homepage.update', $sec) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Heading Title</label>
                        <input type="text" name="title" class="form-control form-control-sm" value="{{ $sec->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subtitle / Tagline</label>
                        <input type="text" name="subtitle" class="form-control form-control-sm" value="{{ $sec->subtitle }}">
                    </div>

                    @if($sec->section_key === 'hero')
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Primary CTA Text</label>
                                <input type="text" name="content[cta_primary_text]" class="form-control form-control-sm" value="{{ $sec->content['cta_primary_text'] ?? 'Client Portal' }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Primary CTA Link</label>
                                <input type="text" name="content[cta_primary_link]" class="form-control form-control-sm" value="{{ $sec->content['cta_primary_link'] ?? '/login' }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Years Exp</label>
                                <input type="text" name="content[years_experience]" class="form-control form-control-sm" value="{{ $sec->content['years_experience'] ?? '8+' }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Projects</label>
                                <input type="text" name="content[projects_completed]" class="form-control form-control-sm" value="{{ $sec->content['projects_completed'] ?? '45+' }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Clients</label>
                                <input type="text" name="content[happy_clients]" class="form-control form-control-sm" value="{{ $sec->content['happy_clients'] ?? '30+' }}">
                            </div>
                        </div>
                    @endif

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_enabled" value="1" {{ $sec->is_enabled ? 'checked' : '' }} id="secCheck{{ $sec->id }}">
                        <label class="form-check-label small" for="secCheck{{ $sec->id }}">Enable section on public homepage</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">Update Section</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
