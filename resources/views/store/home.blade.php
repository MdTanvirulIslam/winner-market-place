<x-store-layout meta-description="Premium web applications by Winner Devs — news portals, POS, inventory, HRM and more, licensed and delivered automatically.">
    {{-- Hero --}}
    <section class="border-b" style="background:var(--bg-card);border-color:var(--border);">
        <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:py-20">
            <div class="mb-4 inline-flex rounded-full bg-accent/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-accent">The Winner Devs Store</div>
            <h1 class="mx-auto max-w-3xl font-heading text-4xl font-extrabold tracking-[-0.04em] text-text sm:text-5xl">
                Professional web applications, ready to launch
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-[15px] leading-7 text-muted">
                News portals, POS software, inventory management, HRM, and more — buy once,
                get your license by email, download instantly, and receive every future update.
            </p>
            <form action="{{ route('store.products') }}" method="GET" class="mx-auto mt-8 flex max-w-xl items-center gap-2">
                <div class="relative flex-1">
                    <span class="icon pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted" data-icon="search"></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="panel-input !pl-11" aria-label="Search products">
                </div>
                <button type="submit" class="rounded-lg bg-accent px-6 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Search</button>
            </form>

            @if($categories->isNotEmpty())
                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    @foreach($categories as $category)
                        <a href="{{ route('store.products', ['category' => $category->slug]) }}" class="rounded-full border px-4 py-1.5 text-[13px] font-semibold text-muted transition-colors duration-300 hover:border-accent hover:text-accent" style="border-color:var(--border);">
                            {{ $category->name }} ({{ $category->products_count }})
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Featured products --}}
    <section class="mx-auto max-w-6xl px-4 py-14">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="font-heading text-2xl font-extrabold text-text sm:text-3xl">Newest Products</h2>
                <p class="mt-1 text-[14px] text-muted">Fresh from the Winner Devs workshop.</p>
            </div>
            <a href="{{ route('store.products') }}" class="text-sm font-semibold text-accent">View all →</a>
        </div>

        @if($featured->isEmpty())
            <div class="rounded-lg border p-12 text-center text-muted" style="border-color:var(--border);background:var(--bg-card);">
                <span class="icon mx-auto mb-3 block text-3xl" data-icon="boxes"></span>
                Products are being prepared — check back soon.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featured as $product)
                    @include('partials.store.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>

    {{-- How it works --}}
    <section class="border-t" style="background:var(--bg-card);border-color:var(--border);">
        <div class="mx-auto max-w-6xl px-4 py-14">
            <h2 class="mb-10 text-center font-heading text-2xl font-extrabold text-text sm:text-3xl">How It Works</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-xl text-accent"><span class="icon" data-icon="shopping-cart"></span></div>
                    <h5 class="mb-1 text-[15px] font-bold text-text">1. Buy</h5>
                    <p class="text-[13px] leading-5 text-muted">Pick your application and pay securely — bKash, Nagad, Rocket, or card.</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-xl text-accent"><span class="icon" data-icon="mail"></span></div>
                    <h5 class="mb-1 text-[15px] font-bold text-text">2. Get Your License</h5>
                    <p class="text-[13px] leading-5 text-muted">Your license key and credentials arrive by email within moments.</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-xl text-accent"><span class="icon" data-icon="download"></span></div>
                    <h5 class="mb-1 text-[15px] font-bold text-text">3. Download</h5>
                    <p class="text-[13px] leading-5 text-muted">Grab the application zip from your account — every version, forever.</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-xl text-accent"><span class="icon" data-icon="badge-check"></span></div>
                    <h5 class="mb-1 text-[15px] font-bold text-text">4. Install &amp; Activate</h5>
                    <p class="text-[13px] leading-5 text-muted">One setup command and your application activates automatically.</p>
                </div>
            </div>
        </div>
    </section>
</x-store-layout>
