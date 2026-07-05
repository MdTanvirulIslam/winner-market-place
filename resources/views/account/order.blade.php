<x-store-layout :title="'Order ' . $order->order_no">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <h1 class="mb-6 font-heading text-3xl font-extrabold text-text">My Account</h1>
        @include('partials.store.account-nav')

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-heading text-xl font-extrabold text-text">{{ $order->order_no }}</h2>
                <p class="text-[13px] text-muted">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if(in_array($order->status, ['paid', 'delivered', 'refunded'], true))
                    <a href="{{ route('account.orders.invoice', $order) }}" class="rounded-lg border px-4 py-2 text-[13px] font-semibold text-text transition-colors duration-300 hover:border-accent hover:text-accent" style="border-color:var(--border);">Invoice</a>
                @endif
                <span class="status-badge {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <h5 class="mb-4 text-sm font-bold text-text">Order Details</h5>
                <dl class="space-y-2.5 text-[13px]">
                    <div class="flex justify-between"><dt class="text-muted">Product</dt><dd class="font-semibold text-text">{{ $order->product_name }}</dd></div>
                    @if((float) $order->discount_amount > 0)
                        <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-semibold text-text">{{ format_price((float) $order->amount + (float) $order->discount_amount) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Coupon {{ $order->coupon_code }}</dt><dd class="font-semibold text-text">−{{ format_price($order->discount_amount) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-muted">Amount</dt><dd class="font-semibold text-text">{{ format_price($order->amount) }}</dd></div>
                    @if($order->paid_at)
                        <div class="flex justify-between"><dt class="text-muted">Paid</dt><dd class="font-semibold text-text">{{ $order->paid_at->format('d M Y, H:i') }}</dd></div>
                    @endif
                    @if($order->delivered_at)
                        <div class="flex justify-between"><dt class="text-muted">Delivered</dt><dd class="font-semibold text-text">{{ $order->delivered_at->format('d M Y, H:i') }}</dd></div>
                    @endif
                </dl>
            </div>

            @if($order->isPending())
                <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-3 text-sm font-bold text-text">Pay Online</h5>
                    <form method="POST" action="{{ route('payment.start', $order) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-accent px-4 py-3 text-sm font-bold text-white transition-colors duration-300 hover:bg-accent-hover">
                            Pay {{ format_price($order->amount) }} Now — bKash / Nagad / Card
                        </button>
                    </form>
                    <p class="mt-2 text-[12px] leading-5 text-muted">You'll be taken to SSLCommerz's secure payment page. Your license is issued automatically the moment the payment is confirmed.</p>

                    <h5 class="mb-3 mt-6 border-t pt-5 text-sm font-bold text-text" style="border-color:var(--border);">Or Pay Manually</h5>
                    @if($setting->payment_instructions)
                        <p class="whitespace-pre-line text-[13px] leading-6 text-text">{{ $setting->payment_instructions }}</p>
                    @else
                        <p class="text-[13px] leading-6 text-muted">
                            Contact us to complete your payment{{ $setting->support_email ? ' at ' . $setting->support_email : '' }}.
                            Mention your order number <strong class="text-text">{{ $order->order_no }}</strong>.
                        </p>
                    @endif
                    <p class="mt-3 text-[12px] text-muted">As soon as we confirm your payment, your license is issued and downloads unlock automatically.</p>
                </div>
            @elseif($order->isDelivered())
                <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-3 text-sm font-bold text-text">Your License</h5>
                    @if($order->license_key)
                        <div class="mb-3 rounded-lg px-4 py-3 font-mono text-[13px] font-bold text-accent" style="background:var(--bg-input);">{{ $order->license_key }}</div>
                    @endif
                    <p class="text-[12px] leading-5 text-muted">
                        Your installation credentials were emailed to you with a secure one-time link.
                        Link expired? Contact support and we'll send a fresh one.
                    </p>
                    <a href="{{ route('account.downloads') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
                        <span class="icon" data-icon="download"></span> Go to Downloads
                    </a>
                </div>
            @elseif($order->status === 'refunded')
                <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-3 text-sm font-bold text-text">Refunded</h5>
                    <p class="text-[13px] leading-6 text-muted">This order was refunded{{ $order->refunded_at ? ' on ' . $order->refunded_at->format('d M Y') : '' }}. Downloads are no longer available.</p>
                </div>
            @elseif($order->status === 'paid')
                <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-3 text-sm font-bold text-text">Payment Received</h5>
                    <p class="text-[13px] leading-6 text-muted">We're preparing your license — it will arrive by email shortly, and downloads unlock right after.</p>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
