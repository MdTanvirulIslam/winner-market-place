<x-store-layout meta-description="Premium web applications by Winner Devs — news portals, POS, inventory, HRM and more, licensed and delivered automatically.">
    {{-- Hero --}}
    <section class="s-hero border-b" style="border-color:var(--s-glass-border);">
        <div class="relative mx-auto max-w-6xl px-4 py-20 text-center sm:py-28">
            <div class="animate-in opacity-0">
                <div class="s-eyebrow mb-6"><span class="icon" data-icon="badge-check"></span> The Winner Devs Store</div>
            </div>
            <h1 class="animate-in mx-auto max-w-3xl font-heading font-extrabold tracking-[-0.04em] text-text opacity-0" style="font-size:clamp(2.4rem, 5.5vw, 4rem);line-height:1.08;">
                Professional web applications, <span class="s-gradient-text">ready to launch</span>
            </h1>
            <p class="animate-in mx-auto mt-6 max-w-2xl text-[16px] leading-8 text-muted opacity-0">
                News portals, POS software, inventory management, HRM, and more — buy once,
                get your license by email, download instantly, and receive every future update.
            </p>
            <form action="{{ route('store.products') }}" method="GET" class="animate-in mx-auto mt-10 flex max-w-xl items-center gap-2 opacity-0">
                <div class="relative flex-1">
                    <span class="icon pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted" data-icon="search"></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="panel-input !pl-11" aria-label="Search products">
                </div>
                <button type="submit" class="s-btn">Search</button>
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

    {{-- Trust strip --}}
    <section class="border-b" style="border-color:var(--s-glass-border);">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 text-center sm:grid-cols-3">
            <div class="flex items-center justify-center gap-2.5 text-[13px] font-semibold text-muted"><span class="icon text-lg text-accent-light" data-icon="key-round"></span> Instant license delivery by email</div>
            <div class="flex items-center justify-center gap-2.5 text-[13px] font-semibold text-muted"><span class="icon text-lg text-accent-light" data-icon="download"></span> Lifetime re-downloads of every version</div>
            <div class="flex items-center justify-center gap-2.5 text-[13px] font-semibold text-muted"><span class="icon text-lg text-accent-light" data-icon="lock-keyhole"></span> Secure payments via SSLCommerz</div>
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
    <section class="s-hero border-t" style="border-color:var(--s-glass-border);">
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
    <section class="border-t" style="border-color:var(--s-glass-border);">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="s-card relative overflow-hidden p-10 text-center sm:p-14">
                <div class="pointer-events-none absolute inset-0" style="background:radial-gradient(600px circle at 50% -20%, var(--accent-subtle), transparent 70%);"></div>
                <h2 class="relative font-heading text-2xl font-extrabold tracking-tight text-text sm:text-3xl">Launch your next project today</h2>
                <p class="relative mx-auto mt-3 max-w-xl text-[14px] leading-7 text-muted">Every application ships with a license key, automatic activation, and lifetime access to updates.</p>
                <div class="relative mt-7 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('store.products') }}" class="s-btn">Browse Products</a>
                    <a href="{{ route('store.contact') }}" class="s-btn-ghost">Talk to Us</a>
                </div>
            </div>
        </div>
    </section>
</x-store-layout>
