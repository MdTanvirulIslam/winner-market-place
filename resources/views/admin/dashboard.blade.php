<x-admin-layout title="Dashboard">
    <section class="welcome-banner animate-in opacity-0">
        <h2>Welcome back, {{ auth()->user()->name }}</h2>
        <p>Here's what's happening in your store today.</p>
    </section>

    <section class="mb-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#0d9488;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(13,148,136,0.1);color:#0d9488;"><span class="icon" data-icon="users"></span></div>
                </div>
                <div class="stat-card-value">{{ number_format($customerCount) }}</div>
                <div class="stat-card-label">Customers</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#06b6d4;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(6,182,212,0.1);color:#06b6d4;"><span class="icon" data-icon="boxes"></span></div>
                    <span class="stat-card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">Phase 1</span>
                </div>
                <div class="stat-card-value">0</div>
                <div class="stat-card-label">Products</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#f59e0b;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(245,158,11,0.1);color:#d97706;"><span class="icon" data-icon="shopping-cart"></span></div>
                    <span class="stat-card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">Phase 2</span>
                </div>
                <div class="stat-card-value">0</div>
                <div class="stat-card-label">Orders</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#8b5cf6;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(139,92,246,0.1);color:#7c3aed;"><span class="icon" data-icon="dollar-sign"></span></div>
                    <span class="stat-card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">Phase 2</span>
                </div>
                <div class="stat-card-value">&#2547;0</div>
                <div class="stat-card-label">Revenue</div>
            </div>
        </div>
    </section>

    <section class="grid gap-3 lg:grid-cols-12">
        <div class="animate-in opacity-0 lg:col-span-8">
            <div class="content-card">
                <div class="content-card-header"><h5>Development Roadmap</h5></div>
                <div class="content-card-body p-0"><div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Phase</th><th>Scope</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td class="font-semibold">Phase 0</td><td>Foundation — auth, roles, admin panel</td><td><span class="status-badge success">Done</span></td></tr>
                            <tr><td class="font-semibold">Phase 1</td><td>Catalog — categories, products, releases + public store</td><td><span class="status-badge pending">Next</span></td></tr>
                            <tr><td class="font-semibold">Phase 2</td><td>Orders &amp; manual selling + license provisioning</td><td><span class="status-badge pending">Planned</span></td></tr>
                            <tr><td class="font-semibold">Phase 3</td><td>SSLCommerz online payments</td><td><span class="status-badge pending">Planned</span></td></tr>
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>
        <div class="animate-in opacity-0 lg:col-span-4">
            <div class="content-card h-full">
                <div class="content-card-header"><h5>Team</h5></div>
                <div class="content-card-body">
                    <p class="mb-3 text-[13px] text-muted">{{ $adminCount }} admin {{ Str::plural('account', $adminCount) }} on this store.</p>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
                            <span class="icon" data-icon="shield"></span> Manage Admin Users
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
