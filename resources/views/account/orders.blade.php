<x-store-layout title="My Purchases">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <h1 class="mb-6 font-heading text-3xl font-extrabold text-text">My Account</h1>
        @include('partials.store.account-nav')

        @if($orders->isEmpty())
            <div class="rounded-lg border p-12 text-center text-muted" style="border-color:var(--border);background:var(--bg-card);">
                <span class="icon mx-auto mb-3 block text-3xl" data-icon="shopping-cart"></span>
                You haven't ordered anything yet.
                <a href="{{ route('store.products') }}" class="mt-1 block font-semibold text-accent">Browse products</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border p-5 transition-all duration-300 hover:shadow" style="border-color:var(--border);background:var(--bg-card);">
                        <div>
                            <div class="text-sm font-bold text-text">{{ $order->product_name }}</div>
                            <div class="text-[12px] text-muted">{{ $order->order_no }} · {{ $order->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-extrabold text-text">{{ format_price($order->amount) }}</span>
                            <span class="status-badge {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
</x-store-layout>
