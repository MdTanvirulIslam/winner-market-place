<x-store-layout title="Checkout">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <h1 class="mb-8 font-heading text-3xl font-extrabold text-text">Checkout</h1>

        <div class="grid gap-6 md:grid-cols-2">
            {{-- Order summary --}}
            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <h5 class="mb-4 text-sm font-bold text-text">Order Summary</h5>
                <div class="mb-4 flex items-center gap-3">
                    <div class="h-14 w-20 shrink-0 overflow-hidden rounded-sm" style="background:var(--bg-input);">
                        @if($product->images->isNotEmpty())
                            <img src="{{ $product->images->first()->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-text">{{ $product->name }}</div>
                        <div class="text-[12px] text-muted">Single-site license · lifetime updates</div>
                    </div>
                </div>
                <div class="space-y-2 border-t pt-4 text-[13px]" style="border-color:var(--border);">
                    @if($product->isOnSale())
                        <div class="flex justify-between text-muted"><span>Regular price</span><span class="line-through">{{ format_price($product->price) }}</span></div>
                        <div class="flex justify-between text-muted"><span>Discount</span><span>−{{ format_price((float) $product->price - $product->effectivePrice()) }}</span></div>
                    @endif
                    <div class="flex justify-between text-[15px] font-extrabold text-text"><span>Total</span><span>{{ format_price($product->effectivePrice()) }}</span></div>
                </div>
            </div>

            {{-- Customer + confirm --}}
            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <h5 class="mb-4 text-sm font-bold text-text">Your Details</h5>
                <div class="mb-4 space-y-2 text-[13px]">
                    <div class="flex justify-between"><span class="text-muted">Name</span><span class="font-semibold text-text">{{ auth()->user()->name }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">Email</span><span class="font-semibold text-text">{{ auth()->user()->email }}</span></div>
                </div>
                <p class="mb-4 text-[12px] leading-5 text-muted">
                    Your license key and credentials will be emailed to this address.
                    After placing the order you'll see the payment instructions —
                    once we confirm your payment, downloads unlock automatically.
                </p>
                <form method="POST" action="{{ route('store.checkout.store', $product->slug) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="panel-label" for="customer_phone">Phone Number</label>
                        <input class="panel-input mt-1" type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="01XXXXXXXXX" required>
                        <p class="mt-1 text-[12px] text-muted">Needed by the payment gateway and for order support.</p>
                        <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
                        Place Order — {{ format_price($product->effectivePrice()) }}
                    </button>
                </form>
                <a href="{{ route('store.products.show', $product->slug) }}" class="mt-3 block text-center text-[13px] font-semibold text-muted">Back to product</a>
            </div>
        </div>
    </div>
</x-store-layout>
