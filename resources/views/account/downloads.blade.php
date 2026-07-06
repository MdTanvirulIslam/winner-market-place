<x-store-layout title="My Downloads">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <h1 class="mb-6 font-heading text-3xl font-extrabold text-text">My Account</h1>
        @include('partials.store.account-nav')

        @if($orders->isEmpty())
            <div class="rounded-lg border p-12 text-center text-muted" style="border-color:var(--border);background:var(--bg-card);">
                <span class="icon mx-auto mb-3 block text-3xl" data-icon="download"></span>
                Nothing to download yet — completed purchases appear here.
                <a href="{{ route('store.products') }}" class="mt-1 block font-semibold text-accent">Browse products</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-16 shrink-0 overflow-hidden rounded-sm" style="background:var(--bg-input);">
                                    <img src="{{ $order->product?->coverUrl() ?? asset('images/product-placeholder.jpg') }}" alt="{{ $order->product_name }}" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-text">{{ $order->product_name }}</div>
                                    <div class="text-[12px] text-muted">{{ $order->order_no }}</div>
                                </div>
                            </div>
                            @if($order->license_key)
                                <div class="rounded-lg px-3 py-1.5 font-mono text-[12px] font-bold text-accent" style="background:var(--bg-input);">{{ $order->license_key }}</div>
                            @endif
                        </div>

                        @if($order->product && $order->product->releases->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($order->product->releases as $release)
                                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border px-4 py-3" style="border-color:var(--border);">
                                        <div>
                                            <span class="mr-2 rounded-full bg-accent/10 px-2.5 py-0.5 text-[12px] font-bold text-accent">v{{ $release->version }}</span>
                                            <span class="text-[12px] text-muted">{{ $release->released_at?->format('d M Y') }} · {{ $release->fileSizeForHumans() }}</span>
                                            @if($loop->first)
                                                <span class="ml-1 rounded-full bg-success/10 px-2 py-0.5 text-[11px] font-bold" style="color:var(--success);">Latest</span>
                                            @endif
                                        </div>
                                        <a href="{{ URL::temporarySignedRoute('account.download', now()->addMinutes(30), [$order, $release]) }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2 text-[13px] font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
                                            <span class="icon" data-icon="download"></span> Download
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[13px] text-muted">No downloadable files yet — you'll be notified when the first release ships.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-store-layout>
