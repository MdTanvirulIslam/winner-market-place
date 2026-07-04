<x-store-layout title="Products" meta-description="Browse all Winner Devs applications — search by name or filter by category.">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="mb-8">
            <h1 class="font-heading text-3xl font-extrabold text-text">Products</h1>
            <p class="mt-1 text-[14px] text-muted">{{ $products->total() }} {{ Str::plural('application', $products->total()) }} available</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            {{-- Filter sidebar --}}
            <aside>
                <div class="rounded-lg border p-5" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-3 text-sm font-bold text-text">Categories</h5>
                    <div class="space-y-2 text-[13px]">
                        <a href="{{ route('store.products', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}"
                           class="block font-semibold {{ request('category') ? 'text-muted hover:text-accent' : 'text-accent' }}">All Products</a>
                        @foreach($categories as $category)
                            <a href="{{ route('store.products', array_filter(['category' => $category->slug, 'q' => request('q'), 'sort' => request('sort')])) }}"
                               class="flex items-center justify-between font-semibold {{ request('category') === $category->slug ? 'text-accent' : 'text-muted hover:text-accent' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-[11px]">{{ $category->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <div>
                <form method="GET" action="{{ route('store.products') }}" class="mb-6 flex flex-wrap items-center gap-2">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative min-w-[200px] flex-1">
                        <span class="icon pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted" data-icon="search"></span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="panel-input !pl-11" aria-label="Search products">
                    </div>
                    <select name="sort" class="panel-select !w-auto" aria-label="Sort products" onchange="this.form.submit()">
                        <option value="" @selected(!request('sort'))>Newest first</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: low to high</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: high to low</option>
                    </select>
                    <button type="submit" class="rounded-lg bg-accent px-5 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Apply</button>
                </form>

                @if($products->isEmpty())
                    <div class="rounded-lg border p-12 text-center text-muted" style="border-color:var(--border);background:var(--bg-card);">
                        <span class="icon mx-auto mb-3 block text-3xl" data-icon="search"></span>
                        No products match your search.
                        <a href="{{ route('store.products') }}" class="mt-1 block font-semibold text-accent">Clear filters</a>
                    </div>
                @else
                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($products as $product)
                            @include('partials.store.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-store-layout>
