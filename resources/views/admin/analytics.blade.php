<x-admin-layout title="Analytics">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Sales' => null, 'Analytics' => null]" />
            <h4>Sales Analytics</h4>
            <p class="text-[13px] text-muted">Lifetime performance across orders, products, and coupons.</p>
        </div>
    </div>

    <section class="mb-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#8b5cf6;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(139,92,246,0.1);color:#7c3aed;"><span class="icon" data-icon="dollar-sign"></span></div>
                </div>
                <div class="stat-card-value">{{ format_price($revenueTotal) }}</div>
                <div class="stat-card-label">Total Revenue</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#f59e0b;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(245,158,11,0.1);color:#d97706;"><span class="icon" data-icon="shopping-cart"></span></div>
                </div>
                <div class="stat-card-value">{{ number_format($orderCount) }}</div>
                <div class="stat-card-label">Completed Orders</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#0d9488;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(13,148,136,0.1);color:#0d9488;"><span class="icon" data-icon="chart-line"></span></div>
                </div>
                <div class="stat-card-value">{{ format_price($averageOrder) }}</div>
                <div class="stat-card-label">Average Order Value</div>
            </div>
        </div>
        <div class="animate-in opacity-0">
            <div class="stat-card" style="--card-accent:#ef4444;">
                <div class="stat-card-header">
                    <div class="stat-card-icon" style="background:rgba(239,68,68,0.1);color:#dc2626;"><span class="icon" data-icon="undo-2"></span></div>
                    @if($discountTotal > 0)
                        <span class="stat-card-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">{{ format_price($discountTotal) }} discounts</span>
                    @endif
                </div>
                <div class="stat-card-value">{{ number_format($refundCount) }}</div>
                <div class="stat-card-label">Refunds ({{ format_price($refundedAmount) }})</div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="animate-in opacity-0">
            <div class="content-card">
                <div class="content-card-header">
                    <h5>Revenue</h5>
                    <div class="flex gap-1.5">
                        <button class="font-size-btn active" data-chart-range="weekly">Last 30 Days</button>
                        <button class="font-size-btn" data-chart-range="monthly">Last 12 Months</button>
                    </div>
                </div>
                <div class="content-card-body"><div class="bar-chart" id="barChart"></div></div>
            </div>
        </div>
    </section>

    <section class="grid gap-3 lg:grid-cols-12">
        <div class="animate-in opacity-0 lg:col-span-7">
            <div class="content-card h-full">
                <div class="content-card-header"><h5>Product Performance</h5></div>
                <div class="content-card-body p-0"><div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Product</th><th>Sales</th><th>Revenue</th><th>Share</th></tr></thead>
                        <tbody>
                            @forelse($productPerformance as $row)
                                <tr>
                                    <td class="font-semibold">{{ $row->product_name }}</td>
                                    <td>{{ $row->sales_count }}</td>
                                    <td class="font-semibold">{{ format_price($row->revenue) }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="progress-bar-custom flex-1" style="min-width:60px;"><div class="progress-fill w-0" data-width="{{ $row->share }}%"></div></div>
                                            <span class="text-[12px] text-muted">{{ $row->share }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No completed sales yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>

        <div class="animate-in opacity-0 lg:col-span-5">
            <div class="content-card h-full">
                <div class="content-card-header">
                    <h5>Coupon Performance</h5>
                    <a href="{{ route('admin.coupons.index') }}" class="text-[13px] font-semibold text-accent">Manage</a>
                </div>
                <div class="content-card-body p-0"><div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Code</th><th>Orders</th><th>Discounts</th><th>Revenue</th></tr></thead>
                        <tbody>
                            @forelse($couponPerformance as $row)
                                <tr>
                                    <td>
                                        <code class="font-bold">{{ $row->code }}</code>
                                        @unless($row->redeemable)<span class="status-badge failed ml-1">Inactive</span>@endunless
                                    </td>
                                    <td>{{ $row->order_count }}</td>
                                    <td>−{{ format_price($row->discount_total) }}</td>
                                    <td class="font-semibold">{{ format_price($row->revenue) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No coupons yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>
    </section>

    <script>
        window.__adminChart = @json($chartData);
    </script>
</x-admin-layout>
