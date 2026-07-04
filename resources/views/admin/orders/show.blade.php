<x-admin-layout :title="'Order ' . $order->order_no">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Orders' => route('admin.orders.index'), $order->order_no => null]" />
            <h4>{{ $order->order_no }}</h4>
            <p class="text-[13px] text-muted">Placed {{ $order->created_at->format('d M Y, H:i') }} · {{ ucfirst($order->payment_method) }}</p>
        </div>
        <span class="status-badge {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span>
    </div>

    @if($order->provisioningFailed())
        <div class="animate-in opacity-0 mb-4 flex items-center gap-3 rounded-lg border px-5 py-4" style="border-color:rgba(239,68,68,0.4);background:rgba(239,68,68,0.06);">
            <span class="icon text-lg" style="color:var(--danger);" data-icon="triangle-alert"></span>
            <div class="flex-1">
                <div class="text-[13px] font-bold text-text">License provisioning failed</div>
                <div class="text-[12px] text-muted">{{ $order->provisioning_error }}</div>
            </div>
            <form method="POST" action="{{ route('admin.orders.retry-provisioning', $order) }}">
                @csrf
                <button type="submit" class="rounded-lg px-4 py-2 text-[13px] font-bold text-white" style="background:var(--danger);">Retry Provisioning</button>
            </form>
        </div>
    @endif

    <div class="grid gap-3 lg:grid-cols-12">
        <div class="animate-in opacity-0 lg:col-span-7 space-y-3">
            <div class="content-card">
                <div class="content-card-header"><h5>Order Details</h5></div>
                <div class="content-card-body">
                    <dl class="space-y-2.5 text-[13px]">
                        <div class="flex justify-between"><dt class="text-muted">Product</dt><dd class="font-semibold">{{ $order->product_name }} (<code>{{ $order->product_slug }}</code>)</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Amount</dt><dd class="font-semibold">{{ format_price($order->amount) }} {{ $order->currency }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Customer</dt><dd class="font-semibold">{{ $order->customer_name }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Email</dt><dd class="font-semibold">{{ $order->customer_email }}</dd></div>
                        @if($order->paid_at)
                            <div class="flex justify-between"><dt class="text-muted">Paid</dt><dd class="font-semibold">{{ $order->paid_at->format('d M Y, H:i') }}</dd></div>
                        @endif
                        @if($order->delivered_at)
                            <div class="flex justify-between"><dt class="text-muted">Delivered</dt><dd class="font-semibold">{{ $order->delivered_at->format('d M Y, H:i') }}</dd></div>
                        @endif
                        @if($order->refunded_at)
                            <div class="flex justify-between"><dt class="text-muted">Refunded</dt><dd class="font-semibold">{{ $order->refunded_at->format('d M Y, H:i') }}</dd></div>
                        @endif
                        @if($order->sslcz_tran_id)
                            <div class="flex justify-between"><dt class="text-muted">SSLCommerz Tran ID</dt><dd class="font-semibold">{{ $order->sslcz_tran_id }}</dd></div>
                        @endif
                        @if($order->license_key)
                            <div class="flex justify-between"><dt class="text-muted">License Key</dt><dd class="font-mono font-semibold text-accent">{{ $order->license_key }}</dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="content-card">
                <div class="content-card-header"><h5>Download Activity ({{ $order->downloads->count() }})</h5></div>
                <div class="content-card-body p-0"><div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Version</th><th>IP</th><th>When</th></tr></thead>
                        <tbody>
                            @forelse($order->downloads->sortByDesc('created_at') as $download)
                                <tr>
                                    <td><code>v{{ $download->version }}</code></td>
                                    <td>{{ $download->ip_address }}</td>
                                    <td>{{ $download->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">No downloads yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>

        <div class="animate-in opacity-0 lg:col-span-5">
            <div class="content-card">
                <div class="content-card-header"><h5>Actions</h5></div>
                <div class="content-card-body space-y-3">
                    @if($order->isPending())
                        <button type="button" data-modal-open="mark-paid" class="w-full rounded-lg bg-accent px-4 py-3 text-sm font-bold text-white transition-colors duration-300 hover:bg-accent-hover">Mark as Paid</button>
                        <p class="text-[12px] leading-5 text-muted">Confirms the money arrived (bKash / bank / cash). The license is created in the License Manager, the customer gets both emails, and downloads unlock.</p>
                        <button type="button" data-modal-open="cancel-order" class="w-full rounded-lg border px-4 py-2.5 text-sm font-semibold text-muted transition-colors duration-300 hover:text-danger" style="border-color:var(--border);">Cancel Order</button>
                    @elseif($order->status === 'paid' && $order->provisioningFailed())
                        <form method="POST" action="{{ route('admin.orders.retry-provisioning', $order) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-accent px-4 py-3 text-sm font-bold text-white transition-colors duration-300 hover:bg-accent-hover">Retry Provisioning</button>
                        </form>
                        <p class="text-[12px] leading-5 text-muted">Idempotent — retrying never creates a duplicate license.</p>
                    @endif

                    @if(in_array($order->status, ['paid', 'delivered'], true))
                        <button type="button" data-modal-open="refund-order" class="w-full rounded-lg border px-4 py-2.5 text-sm font-semibold transition-colors duration-300" style="border-color:rgba(239,68,68,0.4);color:var(--danger);">Mark as Refunded</button>
                        <p class="text-[12px] leading-5 text-muted">Refund the money outside the app first, then mark it here and suspend the license in the License Manager.</p>
                    @endif

                    @if(in_array($order->status, ['cancelled', 'refunded', 'failed'], true))
                        <p class="text-[13px] text-muted">No actions available for {{ $order->status }} orders.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($order->isPending())
        <x-confirm-modal
            id="mark-paid"
            title="Payment received for {{ $order->order_no }}?"
            message="This issues the license in the License Manager, emails the customer their key and credentials, and unlocks downloads. Only confirm once the money ({{ format_price($order->amount) }}) has actually arrived."
            :action="route('admin.orders.mark-paid', $order)"
            variant="accent"
            confirm-label="Yes, Payment Received" />
        <x-confirm-modal
            id="cancel-order"
            title="Cancel order {{ $order->order_no }}?"
            message="The customer will see the order as cancelled. They can always place a new one."
            :action="route('admin.orders.cancel', $order)"
            confirm-label="Cancel Order" />
    @endif

    @if(in_array($order->status, ['paid', 'delivered'], true))
        <x-confirm-modal
            id="refund-order"
            title="Mark {{ $order->order_no }} as refunded?"
            message="Downloads will be blocked immediately. Make sure the money was already refunded in the SSLCommerz panel or by hand — and remember to suspend the license in the License Manager afterwards."
            :action="route('admin.orders.refund', $order)"
            confirm-label="Mark Refunded" />
    @endif
</x-admin-layout>
