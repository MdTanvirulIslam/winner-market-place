<x-store-layout title="About Us" meta-description="Winner Devs builds premium, licensed web applications — news portals, POS, inventory, HRM — delivered and updated automatically.">
    <div class="s-hero border-b" style="border-color:var(--s-glass-border);">
        <div class="relative mx-auto max-w-3xl px-4 py-14 text-center">
            <div class="s-eyebrow mb-5">Who we are</div>
            <h1 class="font-heading text-3xl font-extrabold tracking-tight text-text sm:text-4xl">About {{ config('app.name') }}</h1>
        </div>
    </div>
    <div class="mx-auto max-w-3xl px-4 py-12">
        <div class="s-card space-y-4 p-8 text-[15px] leading-7 text-muted sm:p-10">
            <p>
                {{ config('app.name') }} is the official store of <strong class="text-text">Winner Devs</strong> —
                a software team building production-ready web applications for businesses in Bangladesh and beyond:
                news portals, POS software, inventory management, HRM systems, ticket management, and more.
            </p>
            <p>
                Every product here is built, maintained, and supported by us — no third-party marketplace,
                no middlemen. When you buy an application you get:
            </p>
            <ul class="list-disc space-y-2 pl-6">
                <li><strong class="text-text">Instant delivery</strong> — your license key and installation credentials arrive by email the moment your payment is confirmed.</li>
                <li><strong class="text-text">A genuine license</strong> — activated automatically during installation and verifiable at any time.</li>
                <li><strong class="text-text">Lifetime re-downloads</strong> — every version we ever release for your product stays available in your account.</li>
                <li><strong class="text-text">Direct support</strong> — questions answered by the same people who wrote the code.</li>
            </ul>
            <p>
                Pay the way you already do — bKash, Nagad, Rocket, or card via SSLCommerz — or arrange a
                manual payment through <a href="{{ route('store.contact') }}" class="font-semibold text-accent-light">our contact page</a>.
            </p>
        </div>
    </div>
</x-store-layout>
