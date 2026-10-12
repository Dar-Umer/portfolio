<div class="mb-4 d-flex flex-wrap gap-2 align-items-center justify-content-between bg-white p-3 rounded-3 shadow-sm border">
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.cms.homepage') }}" class="btn btn-sm {{ request()->routeIs('admin.cms.homepage*') ? 'btn-primary shadow-sm' : 'btn-light border' }}">
            <i class="fas fa-home me-1"></i> Homepage Sections
        </a>
        <a href="{{ route('admin.cms.services') }}" class="btn btn-sm {{ request()->routeIs('admin.cms.services*') ? 'btn-primary shadow-sm' : 'btn-light border' }}">
            <i class="fas fa-concierge-bell me-1"></i> Services Directory
        </a>
        <a href="{{ route('admin.cms.portfolio') }}" class="btn btn-sm {{ request()->routeIs('admin.cms.portfolio*') ? 'btn-primary shadow-sm' : 'btn-light border' }}">
            <i class="fas fa-briefcase me-1"></i> Portfolio Showcase
        </a>
    </div>
    <div>
        <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline-dark">
            <i class="fas fa-external-link-alt me-1"></i> View Live Site
        </a>
    </div>
</div>

