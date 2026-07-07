<x-store-layout meta-description="Premium web applications by Winner Devs — news portals, POS, inventory, HRM and more, licensed and delivered automatically.">
    {{-- Hero --}}
    <section class="s-hero border-b border-border">
        <div class="relative mx-auto max-w-6xl px-4 py-20 text-center sm:py-24">
            <div class="animate-in opacity-0">
                <div class="s-eyebrow mb-6"><span class="icon" data-icon="badge-check"></span> The Winner Devs Store</div>
            </div>
            <h1 class="s-hero-title animate-in mx-auto max-w-3xl font-heading font-extrabold tracking-[-0.03em] text-text opacity-0">
                Professional web applications, <span class="s-gradient-text">ready to launch</span>
            </h1>
            <p class="animate-in mx-auto mt-6 max-w-2xl text-[16px] leading-8 text-muted opacity-0">
                News portals, POS software, inventory management, HRM, and more — buy once,
                get your license by email, download instantly, and receive every future update.
            </p>

            {{-- Combined category + keyword search --}}
            <form action="{{ route('store.products') }}" method="GET" class="animate-in mx-auto mt-10 max-w-2xl opacity-0">
                <div class="s-search-bar">
                    @if($categories->isNotEmpty())
                        <select name="category" aria-label="Choose category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products here..." aria-label="Search products">
                    <button type="submit" class="s-btn !py-2.5"><span class="icon" data-icon="search"></span> Search</button>
                </div>
            </form>

            @if($categories->isNotEmpty())
                <div class="animate-in mt-7 flex flex-wrap items-center justify-center gap-2 opacity-0">
                    @foreach($categories as $category)
                        <a href="{{ route('store.products', ['category' => $category->slug]) }}" class="s-pill">
                            {{ $category->name }} <span class="text-[11px] opacity-70">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Trust boxes --}}
    <section class="mx-auto max-w-6xl px-4 pt-12">
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="s-work-box animate-in opacity-0">
                <div class="s-work-icon"><span class="icon" data-icon="key-round"></span></div>
                <h4 class="mb-1.5 font-heading text-[15px] font-bold text-text">Instant License Delivery</h4>
                <p class="text-[13px] leading-6 text-muted">Your license key and credentials arrive by email the moment payment is confirmed.</p>
            </div>
            <div class="s-work-box s-work-box--red animate-in opacity-0">
                <div class="s-work-icon"><span class="icon" data-icon="download"></span></div>
                <h4 class="mb-1.5 font-heading text-[15px] font-bold text-text">Lifetime Re-downloads</h4>
                <p class="text-[13px] leading-6 text-muted">Every version we ever release for your product stays available in your account.</p>
            </div>
            <div class="s-work-box s-work-box--green animate-in opacity-0">
                <div class="s-work-icon"><span class="icon" data-icon="lock-keyhole"></span></div>
                <h4 class="mb-1.5 font-heading text-[15px] font-bold text-text">Secure Payments</h4>
                <p class="text-[13px] leading-6 text-muted">Pay via SSLCommerz — bKash, Nagad, Rocket &amp; cards, on their secure pages.</p>
            </div>
        </div>
    </section>

    {{-- Featured products --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mb-10 flex items-end justify-between">
            <div>
                <h2 class="font-heading text-2xl font-extrabold tracking-tight text-text sm:text-3xl">Newest Products</h2>
                <p class="mt-1.5 text-[14px] text-muted">Fresh from the Winner Devs workshop.</p>
            </div>
            <a href="{{ route('store.products') }}" class="s-btn-ghost !px-4 !py-2 text-[13px]">View all <span class="icon text-[11px]" data-icon="chevron-right"></span></a>
        </div>

        @if($featured->isEmpty())
            <div class="s-card p-14 text-center text-muted">
                <span class="icon mx-auto mb-3 block text-3xl" data-icon="boxes"></span>
                Products are being prepared — check back soon.
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featured as $product)
                    @include('partials.store.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>

    {{-- How it works --}}
    <section class="s-hero border-y border-border">
        <div class="relative mx-auto max-w-6xl px-4 py-16">
            <div class="mb-12 text-center">
                <h2 class="font-heading text-2xl font-extrabold tracking-tight text-text sm:text-3xl">How It Works</h2>
                <p class="mt-2 text-[14px] text-muted">From checkout to a running application in four steps.</p>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="s-card p-6 text-center">
                    <div class="s-step mx-auto mb-4"><span class="icon" data-icon="shopping-cart"></span></div>
                    <h5 class="mb-1.5 font-heading text-[15px] font-bold text-text">1. Buy</h5>
                    <p class="text-[13px] leading-6 text-muted">Pick your application and pay securely — bKash, Nagad, Rocket, or card.</p>
                </div>
                <div class="s-card p-6 text-center">
                    <div class="s-step mx-auto mb-4"><span class="icon" data-icon="mail"></span></div>
                    <h5 class="mb-1.5 font-heading text-[15px] font-bold text-text">2. Get Your License</h5>
                    <p class="text-[13px] leading-6 text-muted">Your license key and credentials arrive by email within moments.</p>
                </div>
                <div class="s-card p-6 text-center">
                    <div class="s-step mx-auto mb-4"><span class="icon" data-icon="download"></span></div>
                    <h5 class="mb-1.5 font-heading text-[15px] font-bold text-text">3. Download</h5>
                    <p class="text-[13px] leading-6 text-muted">Grab the application zip from your account — every version, forever.</p>
                </div>
                <div class="s-card p-6 text-center">
                    <div class="s-step mx-auto mb-4"><span class="icon" data-icon="badge-check"></span></div>
                    <h5 class="mb-1.5 font-heading text-[15px] font-bold text-text">4. Install &amp; Activate</h5>
                    <p class="text-[13px] leading-6 text-muted">One setup command and your application activates automatically.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Closing CTA --}}
    <section>
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="s-cta-band p-10 text-center sm:p-14">
                <h2 class="relative font-heading text-2xl font-extrabold tracking-tight sm:text-3xl">Launch your next project today</h2>
                <p class="relative mx-auto mt-3 max-w-xl text-[14px] leading-7 text-white/85">Every application ships with a license key, automatic activation, and lifetime access to updates.</p>
                <div class="relative mt-7 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('store.products') }}" class="s-btn-light">Browse Products</a>
                    <a href="{{ route('store.contact') }}" class="s-btn-light !bg-white/15 !text-white">Talk to Us</a>
                </div>
            </div>
        </div>
    </section>
</x-store-layout>
