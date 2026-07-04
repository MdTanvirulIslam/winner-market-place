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
                    <span class="stat-card-badge" style="background:rgba(34,197,94,0.1);color:#16a34a;">{{ $publishedCount }} live</span>
                </div>
                <div class="stat-card-value">{{ number_format($productCount) }}</div>
                <div class="stat-card-label">Products</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#f59e0b;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(245,158,11,0.1);color:#d97706;"><span class="icon" data-icon="shopping-cart"></span></div>
                    @if($pendingCount > 0)
                        <span class="stat-card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">{{ $pendingCount }} pending</span>
                    @endif
                </div>
                <div class="stat-card-value">{{ number_format($orderCount) }}</div>
                <div class="stat-card-label">Completed Orders</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#8b5cf6;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(139,92,246,0.1);color:#7c3aed;"><span class="icon" data-icon="dollar-sign"></span></div>
                </div>
                <div class="stat-card-value">{{ format_price($revenueThisMonth) }}</div>
                <div class="stat-card-label">Revenue This Month</div>
            </div>
        </div>
    </section>

    @if($failedProvisioningCount > 0)
        <section class="animate-in opacity-0 mb-4 flex items-center gap-3 rounded-lg border px-5 py-4" style="border-color:rgba(239,68,68,0.4);background:rgba(239,68,68,0.06);">
            <span class="icon text-lg" style="color:var(--danger);" data-icon="triangle-alert"></span>
            <div class="flex-1 text-[13px] font-semibold text-text">
                {{ $failedProvisioningCount }} paid {{ Str::plural('order', $failedProvisioningCount) }} awaiting license provisioning.
            </div>
            <a href="{{ route('admin.orders.index', ['provisioning_failed' => 1]) }}" class="text-[13px] font-bold" style="color:var(--danger);">Review now</a>
        </section>
    @endif

    <section class="grid gap-3 lg:grid-cols-12">
        <div class="animate-in opacity-0 lg:col-span-8">
            <div class="content-card">
                <div class="content-card-header"><h5>Recent Orders</h5><a href="{{ route('admin.orders.index') }}" class="text-[13px] font-semibold text-accent">View All</a></div>
                <div class="content-card-body p-0"><div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Order</th><th>Customer</th><th>Product</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-accent">{{ $order->order_no }}</a></td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->product_name }}</td>
                                    <td class="font-semibold">{{ format_price($order->amount) }}</td>
                                    <td><span class="status-badge {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No orders yet — create a manual order or wait for the first sale.</td></tr>
                            @endforelse
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
