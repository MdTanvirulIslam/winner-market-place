<a href="{{ route('store.products.show', $product->slug) }}" class="group block overflow-hidden rounded-lg border bg-card shadow transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" style="border-color:var(--border);">
    <div class="relative aspect-[16/10] overflow-hidden" style="background:var(--bg-input);">
        @if($product->images->isNotEmpty())
            <img src="{{ $product->images->first()->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl text-muted"><span class="icon" data-icon="boxes"></span></div>
        @endif
        @if($product->isOnSale())
            <span class="absolute left-3 top-3 rounded-full bg-danger px-2.5 py-1 text-[11px] font-bold text-white">SALE</span>
        @endif
    </div>
    <div class="p-4">
        @if($product->category)
            <div class="mb-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-accent">{{ $product->category->name }}</div>
        @endif
        <h5 class="mb-1 text-[15px] font-bold text-text">{{ $product->name }}</h5>
        @if(($product->approved_reviews_count ?? 0) > 0)
            <div class="mb-1 flex items-center gap-1.5 text-[12px] text-muted">
                @include('partials.store.stars', ['rating' => (float) $product->approved_reviews_avg_rating])
                <span>({{ $product->approved_reviews_count }})</span>
            </div>
        @endif
        <p class="mb-3 line-clamp-2 text-[13px] leading-5 text-muted">{{ $product->shortDescriptionText() }}</p>
        <div class="flex items-center justify-between">
            <div class="text-[15px] font-extrabold text-text">
                @if($product->isOnSale())
                    <span class="mr-1 text-[13px] font-semibold text-muted line-through">{{ format_price($product->price) }}</span>{{ format_price($product->sale_price) }}
                @else
                    {{ format_price($product->price) }}
                @endif
            </div>
            <span class="text-[13px] font-semibold text-accent">View details</span>
        </div>
    </div>
</a>
