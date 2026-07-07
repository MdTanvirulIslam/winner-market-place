<x-store-layout title="Terms of Service" meta-description="Terms of service for purchasing and using Winner Devs applications.">
    <div class="s-hero border-b border-border">
        <div class="relative mx-auto max-w-3xl px-4 py-14 text-center">
            <div class="s-eyebrow mb-5">Legal</div>
            <h1 class="font-heading text-3xl font-extrabold tracking-tight text-text sm:text-4xl">Terms of Service</h1>
            <p class="mt-3 text-[13px] text-muted">Last updated: {{ date('d F Y') }}</p>
        </div>
    </div>
    <div class="mx-auto max-w-3xl px-4 py-12">
        <div class="s-card space-y-6 p-8 text-[15px] leading-7 text-muted sm:p-10">
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">1. The service</h2>
                <p>{{ config('app.name') }} sells licensed copies of software applications developed by Winner Devs. By placing an order you agree to these terms.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">2. Licenses</h2>
                <p>Each purchase grants a license to install and use the application per the license limits stated on the product page (typically one production domain, unless otherwise agreed). Licenses are validated online through our license server. Reselling, sublicensing, or removing the licensing mechanism is not permitted.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">3. Delivery</h2>
                <p>Products are delivered digitally. After payment confirmation you receive your license key and installation credentials by email, and the application files become available in your account's download area. Keep your credentials confidential — the one-time credential link expires for your protection.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">4. Updates and support</h2>
                <p>Purchases include access to all future versions of the purchased product through your account. Support is provided in reasonable scope via our contact channels; custom development is quoted separately.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">5. Payments</h2>
                <p>Online payments are processed by SSLCommerz; we never see or store your card or wallet credentials. Manual payments (bKash / bank transfer) are confirmed by our team before delivery.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">6. Refunds</h2>
                <p>Refunds are governed by our <a href="{{ route('store.refund-policy') }}" class="font-semibold text-accent-light">Refund Policy</a>. A refunded order's license is suspended and downloads are revoked.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">7. Acceptable use</h2>
                <p>You may not use our products for unlawful purposes or attempt to disrupt this store, the license server, or other customers' use of them.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">8. Liability</h2>
                <p>Products are provided as described on their product pages. To the maximum extent permitted by law, our total liability for any claim is limited to the amount you paid for the product concerned.</p>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">9. Changes</h2>
                <p>We may update these terms; the version published on this page at the time of your order applies to that order. Questions? <a href="{{ route('store.contact') }}" class="font-semibold text-accent-light">Contact us</a>.</p>
            </section>
        </div>
    </div>
</x-store-layout>
