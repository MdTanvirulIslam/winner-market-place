<x-store-layout title="Privacy Policy" meta-description="How Winner Marketplace collects, uses, and protects your data.">
    <div class="s-hero border-b" style="border-color:var(--s-glass-border);">
        <div class="relative mx-auto max-w-3xl px-4 py-14 text-center">
            <div class="s-eyebrow mb-5">Legal</div>
            <h1 class="font-heading text-3xl font-extrabold tracking-tight text-text sm:text-4xl">Privacy Policy</h1>
            <p class="mt-3 text-[13px] text-muted">Last updated: {{ date('d F Y') }}</p>
        </div>
    </div>
    <div class="mx-auto max-w-3xl px-4 py-12">
        <div class="s-card space-y-6 p-8 text-[15px] leading-7 text-muted sm:p-10">
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">What we collect</h2>
                <p>Your account details (name, email, phone), your orders, and download activity (versions and IP addresses, for abuse prevention). Passwords are stored hashed — we cannot read them.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">Payments</h2>
                <p>Online payments happen on SSLCommerz's secure pages. We receive only the transaction outcome and reference — never your card number, PIN, or wallet credentials.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">How we use your data</h2>
                <p>To deliver what you bought: issuing licenses, sending your credentials and order emails, providing downloads, and answering support requests. We do not sell your data or send marketing you didn't ask for.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">Sharing</h2>
                <p>Order details are shared with our license server (to issue your license) and with SSLCommerz (to process your payment). Nothing else, unless the law requires it.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">Cookies</h2>
                <p>We use only functional cookies: your login session and CSRF protection. No advertising trackers.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">Your choices</h2>
                <p>You can update your details or delete your account from your profile page. Deleting your account does not erase completed order records, which we keep for accounting. For any privacy request, <a href="{{ route('store.contact') }}" class="font-semibold text-accent-light">contact us</a>.</p>
            </section>
        </div>
    </div>
</x-store-layout>
