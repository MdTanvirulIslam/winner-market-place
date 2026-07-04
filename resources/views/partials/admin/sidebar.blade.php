<aside class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
    <nav class="sidebar-nav">
        <div class="sidebar-label">Overview</div>
        <a class="sidebar-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="house"></span><span class="link-text">Dashboard</span>
        </a>

        {{-- Catalog and sales sections arrive in Phase 1 and 2. --}}
        <div class="sidebar-label">Catalog</div>
        <span class="sidebar-link opacity-50 cursor-default" title="Coming in Phase 1">
            <span class="icon nav-icon" data-icon="layout-template"></span><span class="link-text">Categories</span><span class="sidebar-meta">P1</span>
        </span>
        <span class="sidebar-link opacity-50 cursor-default" title="Coming in Phase 1">
            <span class="icon nav-icon" data-icon="boxes"></span><span class="link-text">Products</span><span class="sidebar-meta">P1</span>
        </span>
        <span class="sidebar-link opacity-50 cursor-default" title="Coming in Phase 1">
            <span class="icon nav-icon" data-icon="download"></span><span class="link-text">Releases</span><span class="sidebar-meta">P1</span>
        </span>

        <div class="sidebar-label">Sales</div>
        <span class="sidebar-link opacity-50 cursor-default" title="Coming in Phase 2">
            <span class="icon nav-icon" data-icon="shopping-cart"></span><span class="link-text">Orders</span><span class="sidebar-meta">P2</span>
        </span>
        <span class="sidebar-link opacity-50 cursor-default" title="Coming in Phase 2">
            <span class="icon nav-icon" data-icon="users"></span><span class="link-text">Customers</span><span class="sidebar-meta">P2</span>
        </span>

        @if(auth()->user()->isSuperAdmin())
            <div class="sidebar-label">Admin</div>
            <a class="sidebar-link{{ request()->routeIs('admin.users.*') ? ' active' : '' }}" href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>
                <span class="icon nav-icon" data-icon="shield"></span><span class="link-text">Admin Users</span>
            </a>
            <a class="sidebar-link{{ request()->routeIs('admin.settings.*') ? ' active' : '' }}" href="{{ route('admin.settings.edit') }}" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif>
                <span class="icon nav-icon" data-icon="settings"></span><span class="link-text">Settings</span>
            </a>
        @endif
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="sidebar-user-info"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Staff' }}</span></div>
        </div>
    </div>
</aside>
