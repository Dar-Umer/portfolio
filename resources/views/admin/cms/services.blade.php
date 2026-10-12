@extends('layouts.admin')

@section('page_title', 'CMS: Services Directory')

@section('content')
@include('admin.cms._nav')

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle text-primary me-2"></i> Add New Service</h6>
            <form method="POST" action="{{ route('admin.cms.services.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Service Title</label>
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Cloud ERP Engineering" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">FontAwesome Icon Class</label>
                    <input type="text" name="icon" class="form-control form-control-sm" placeholder="e.g. fas fa-cloud-upload-alt" value="fas fa-cubes" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Starting Price (Optional)</label>
                    <input type="number" step="0.01" name="starting_price" class="form-control form-control-sm" placeholder="e.g. 50000">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Short Summary</label>
                    <textarea name="short_description" class="form-control form-control-sm" rows="2" placeholder="Brief elevator pitch..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Full Description (Optional)</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Detailed service offering..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Publish Service</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Active Public Services</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>Icon</th>
                            <th>Title & Price</th>
                            <th>Summary</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td style="width: 50px;">
                                    <div class="bg-light text-primary rounded p-2 text-center" style="width: 38px; height: 38px;">
                                        <i class="{{ $service->icon }}"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $service->title }}</div>
                                    @if($service->starting_price)
                                        <div class="text-muted small">From ₹{{ number_format($service->starting_price, 2) }}</div>
                                    @endif
                                </td>
                                <td class="text-muted">{{ Str::limit($service->short_description, 80) }}</td>
                                <td>
                                    <span class="badge {{ $service->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                        {{ $service->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.cms.services.destroy', $service) }}" onsubmit="return confirm('Remove service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No services published.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
