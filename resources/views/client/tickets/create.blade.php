@extends('layouts.public')

@section('title', 'Create Support Ticket | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 p-md-5 bg-white">
                <div class="mb-4">
                    <a href="{{ route('client.tickets.index') }}" class="small text-muted text-decoration-none mb-2 d-inline-block">&larr; Back to Tickets</a>
                    <h4 class="fw-bold mb-1">Create Support Ticket</h4>
                    <p class="text-muted small">Provide clear reproduction details to expedite SLA turnaround.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('client.tickets.store') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Related Project</label>
                            <select name="project_id" class="form-select form-select-sm">
                                <option value="">General Project / Consulting</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="ticket_category_id" class="form-select form-select-sm">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('ticket_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Priority</label>
                            <select name="priority" class="form-select form-select-sm" required>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low (Minor suggestion/query)</option>
                                <option value="Normal" {{ old('priority', 'Normal') == 'Normal' ? 'selected' : '' }}>Normal (Standard defect)</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High (Impacting operations)</option>
                                <option value="Critical" {{ old('priority') == 'Critical' ? 'selected' : '' }}>Critical (System halted/Major outage)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Issue Type</label>
                            <select name="issue_type" class="form-select form-select-sm" required>
                                <option value="Bug Report">Bug Report</option>
                                <option value="Feature Request">Feature Request</option>
                                <option value="Technical Support">Technical Support</option>
                                <option value="UI / Design">UI / Design</option>
                                <option value="Security Issue">Security Issue</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject / Short Summary</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="e.g. Inconsistent checkout total on mobile" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Detailed Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Explain the observed issue in detail..." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Steps to Reproduce (Optional)</label>
                        <textarea name="steps_to_reproduce" class="form-control" rows="2" placeholder="1. Go to page... 2. Click button...">{{ old('steps_to_reproduce') }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expected Result (Optional)</label>
                            <input type="text" name="expected_result" class="form-control form-control-sm" value="{{ old('expected_result') }}" placeholder="What should happen">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Actual Result (Optional)</label>
                            <input type="text" name="actual_result" class="form-control form-control-sm" value="{{ old('actual_result') }}" placeholder="What actually happened">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        Submit Support Ticket <i class="fas fa-paper-plane ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
