<aside class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
    <nav class="sidebar-nav">
        <div class="sidebar-label">Overview</div>
        <a class="sidebar-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="house"></span><span class="link-text">Dashboard</span>
        </a>

        <div class="sidebar-label">Catalog</div>
        <a class="sidebar-link{{ request()->routeIs('admin.categories.*') ? ' active' : '' }}" href="{{ route('admin.categories.index') }}" @if(request()->routeIs('admin.categories.*')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="layout-template"></span><span class="link-text">Categories</span>
        </a>
        <a class="sidebar-link{{ request()->routeIs('admin.products.*') ? ' active' : '' }}" href="{{ route('admin.products.index') }}" @if(request()->routeIs('admin.products.*')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="boxes"></span><span class="link-text">Products</span>
        </a>
        <a class="sidebar-link{{ request()->routeIs('admin.releases.*') ? ' active' : '' }}" href="{{ route('admin.releases.index') }}" @if(request()->routeIs('admin.releases.*')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="download"></span><span class="link-text">Releases</span>
        </a>

        <div class="sidebar-label">Sales</div>
        <a class="sidebar-link{{ request()->routeIs('admin.orders.*') ? ' active' : '' }}" href="{{ route('admin.orders.index') }}" @if(request()->routeIs('admin.orders.*')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="shopping-cart"></span><span class="link-text">Orders</span>
            @php($failedProvisioningCount = \App\Models\Order::where('provisioning_status', 'failed')->count())
            @if($failedProvisioningCount > 0)
                <span class="sidebar-meta" style="background:rgba(239,68,68,0.25);color:#fca5a5;">{{ $failedProvisioningCount }}</span>
            @endif
        </a>
        <a class="sidebar-link{{ request()->routeIs('admin.customers.*') ? ' active' : '' }}" href="{{ route('admin.customers.index') }}" @if(request()->routeIs('admin.customers.*')) aria-current="page" @endif>
            <span class="icon nav-icon" data-icon="users"></span><span class="link-text">Customers</span>
        </a>

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
