<a href="{{ route('store.products.show', $product->slug) }}" class="s-card s-card-hover group block overflow-hidden">
    <div class="relative aspect-[16/10] overflow-hidden bg-input">
        <img src="{{ $product->coverUrl() }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        @if($product->isOnSale())
            <span class="s-badge-sale absolute left-3 top-3">SALE</span>
        @endif
    </div>
    <div class="p-5">
        @if($product->category)
            <div class="mb-1.5 text-[11px] font-bold uppercase tracking-[0.16em] text-accent-light">{{ $product->category->name }}</div>
        @endif
        <h5 class="mb-1 font-heading text-[16px] font-bold text-text transition-colors duration-300 group-hover:text-accent-light">{{ $product->name }}</h5>
        @if(($product->approved_reviews_count ?? 0) > 0)
            <div class="mb-1.5 flex items-center gap-1.5 text-[12px] text-muted">
                @include('partials.store.stars', ['rating' => (float) $product->approved_reviews_avg_rating])
                <span>({{ $product->approved_reviews_count }})</span>
            </div>
        @endif
        <p class="mb-4 line-clamp-2 text-[13px] leading-5 text-muted">{{ $product->shortDescriptionText() }}</p>
        <div class="flex items-center justify-between border-t border-border pt-4">
            <div class="font-heading text-[16px] font-extrabold text-text">
                @if($product->isOnSale())
                    <span class="mr-1 text-[13px] font-semibold text-muted line-through">{{ format_price($product->price) }}</span>{{ format_price($product->sale_price) }}
                @else
                    {{ format_price($product->price) }}
                @endif
            </div>
            <span class="inline-flex items-center gap-1 text-[13px] font-semibold text-accent-light transition-transform duration-300 group-hover:translate-x-0.5">View details <span class="icon text-[11px]" data-icon="chevron-right"></span></span>
        </div>
    </div>
</a>
