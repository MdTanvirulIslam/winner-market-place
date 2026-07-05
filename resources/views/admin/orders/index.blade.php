<x-admin-layout title="Orders">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Sales' => null, 'Orders' => null]" />
            <h4>Orders</h4>
            <p class="text-[13px] text-muted">All sales — gateway and manual.</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="shopping-cart"></span> New Manual Order
        </a>
    </div>

    @if($failedCount > 0)
        <div class="animate-in opacity-0 mb-4 flex items-center gap-3 rounded-lg border px-5 py-4" style="border-color:rgba(239,68,68,0.4);background:rgba(239,68,68,0.06);">
            <span class="icon text-lg" style="color:var(--danger);" data-icon="triangle-alert"></span>
            <div class="flex-1 text-[13px] font-semibold text-text">
                {{ $failedCount }} paid {{ Str::plural('order', $failedCount) }} could not be provisioned — the customer paid but has no license yet.
            </div>
            <a href="{{ route('admin.orders.index', ['provisioning_failed' => 1]) }}" class="text-[13px] font-bold" style="color:var(--danger);">Show them</a>
        </div>
    @endif

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body">
            <x-datatable-toolbar :action="route('admin.orders.index')" search-placeholder="Search by order no, customer name or email…">
                @if(request()->boolean('provisioning_failed'))
                    <input type="hidden" name="provisioning_failed" value="1">
                @endif
                <select class="panel-select w-auto py-2.5 text-[13px]" name="status" data-autosubmit aria-label="Status">
                    <option value="">All statuses</option>
                    @foreach(['pending', 'paid', 'delivered', 'failed', 'cancelled', 'refunded'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </x-datatable-toolbar>
        </div>
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <x-sort-th field="order_no" label="Order" />
                    <x-sort-th field="customer_name" label="Customer" />
                    <th>Product</th>
                    <x-sort-th field="amount" label="Amount" />
                    <x-sort-th field="status" label="Status" />
                    <th>License</th>
                    <x-sort-th field="created_at" label="Date" />
                </tr></thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-accent">{{ $order->order_no }}</a></td>
                            <td>
                                <div class="font-semibold">{{ $order->customer_name }}</div>
                                <div class="text-[12px] text-muted">{{ $order->customer_email }}</div>
                            </td>
                            <td>{{ $order->product_name }}</td>
                            <td class="font-semibold">{{ format_price($order->amount) }}</td>
                            <td><span class="status-badge {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></td>
                            <td>
                                @if($order->provisioningFailed())
                                    <span class="status-badge failed">Failed</span>
                                @elseif($order->license_key)
                                    <span class="status-badge success">Issued</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No orders {{ request()->hasAny(['q', 'status', 'provisioning_failed']) ? 'match your filters' : 'yet' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
        {{ $orders->links('vendor.pagination.admin') }}
    </div>
</x-admin-layout>
