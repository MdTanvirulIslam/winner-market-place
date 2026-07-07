<x-store-layout title="Refund Policy" meta-description="Refund policy for digital products purchased from Winner Marketplace.">
    <div class="s-hero border-b border-border">
        <div class="relative mx-auto max-w-3xl px-4 py-14 text-center">
            <div class="s-eyebrow mb-5">Legal</div>
            <h1 class="font-heading text-3xl font-extrabold tracking-tight text-text sm:text-4xl">Refund Policy</h1>
            <p class="mt-3 text-[13px] text-muted">Last updated: {{ date('d F Y') }}</p>
        </div>
    </div>
    <div class="mx-auto max-w-3xl px-4 py-12">
        <div class="s-card space-y-6 p-8 text-[15px] leading-7 text-muted sm:p-10">
            <p>
                We sell digital software products delivered instantly. Because a delivered product cannot be
                "returned", refunds work as follows:
            </p>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">When you can get a refund</h2>
                <ul class="list-disc space-y-2 pl-6">
                    <li><strong class="text-text">You paid but received nothing</strong> — if payment was taken and your license or download never arrived and we cannot fix it within 72 hours, you get a full refund.</li>
                    <li><strong class="text-text">The product fundamentally does not work as described</strong> on its product page, on a server meeting the stated requirements, and our support cannot make it work within a reasonable time — full refund within 14 days of purchase.</li>
                    <li><strong class="text-text">Duplicate payment</strong> — accidental double payments are refunded in full.</li>
                </ul>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">When refunds are not available</h2>
                <ul class="list-disc space-y-2 pl-6">
                    <li>Change of mind after the application has been downloaded.</li>
                    <li>Missing features that were never listed on the product page — check the live demo before buying.</li>
                    <li>Server or hosting environments that do not meet the stated requirements.</li>
                    <li>Issues caused by code modifications made by you or third parties.</li>
                </ul>
            </section>
            <section>
                <h2 class="mb-2 font-heading text-lg font-bold text-text">How to request one</h2>
                <p><a href="{{ route('store.contact') }}" class="font-semibold text-accent-light">Contact us</a> with your order number and a description of the problem. Approved refunds are returned through the original payment channel (SSLCommerz refunds typically take 7–10 working days to appear). After a refund, the product's license is suspended and downloads are disabled.</p>
            </section>
        </div>
    </div>
</x-store-layout>
