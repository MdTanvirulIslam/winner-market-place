<x-store-layout title="Products" meta-description="Browse all Winner Devs applications — search by name or filter by category.">
    <div class="mx-auto max-w-6xl px-4 py-12">
        <div class="mb-10">
            <h1 class="font-heading text-3xl font-extrabold tracking-tight text-text sm:text-4xl">Products</h1>
            <p class="mt-2 text-[14px] text-muted">{{ $products->total() }} {{ Str::plural('application', $products->total()) }} available</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[240px_1fr]">
            {{-- Filter sidebar --}}
            <aside>
                <div class="s-card p-5 lg:sticky lg:top-24">
                    <h5 class="mb-4 text-[13px] font-bold uppercase tracking-[0.14em] text-text">Categories</h5>
                    <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:gap-2.5 lg:overflow-visible lg:pb-0">
                        <a href="{{ route('store.products', array_filter(['q' => request('q'), 'sort' => request('sort')])) }}"
                           class="s-pill shrink-0 justify-between lg:flex {{ request('category') ? '' : 'active' }}">All Products</a>
                        @foreach($categories as $category)
                            <a href="{{ route('store.products', array_filter(['category' => $category->slug, 'q' => request('q'), 'sort' => request('sort')])) }}"
                               class="s-pill shrink-0 justify-between gap-3 lg:flex {{ request('category') === $category->slug ? 'active' : '' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-[11px] opacity-70">{{ $category->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <div>
                <form method="GET" action="{{ route('store.products') }}" class="mb-7 flex flex-wrap items-center gap-2">
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
                    <button type="submit" class="s-btn !py-3">Apply</button>
                </form>

                @if($products->isEmpty())
                    <div class="s-card p-14 text-center text-muted">
                        <span class="icon mx-auto mb-3 block text-3xl" data-icon="search"></span>
                        No products match your search.
                        <a href="{{ route('store.products') }}" class="mt-2 block font-semibold text-accent-light">Clear filters</a>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($products as $product)
                            @include('partials.store.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-store-layout>
