<x-store-layout title="Contact Us" meta-description="Get in touch with the Winner Devs team — presales questions, support, or manual payment arrangements.">
    <div class="mx-auto max-w-3xl px-4 py-12">
        <h1 class="mb-2 font-heading text-3xl font-extrabold text-text">Contact Us</h1>
        <p class="mb-8 text-[15px] text-muted">Presales question, support request, or arranging a manual payment — we're happy to help.</p>

        <div class="grid gap-6 md:grid-cols-[1fr_260px]">
            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <form method="POST" action="{{ route('store.contact.send') }}" class="space-y-4">
                    @csrf
                    {{-- Honeypot: humans never see or fill this field. --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="panel-label" for="name">Your Name</label>
                            <input class="panel-input mt-1" type="text" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <label class="panel-label" for="email">Your Email</label>
                            <input class="panel-input mt-1" type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label class="panel-label" for="subject">Subject</label>
                        <input class="panel-input mt-1" type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="e.g. Question about News Portal, or order WM-..." required>
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>
                    <div>
                        <label class="panel-label" for="message">Message</label>
                        <textarea class="panel-textarea mt-1" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>
                    <button type="submit" class="rounded-lg bg-accent px-6 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Send Message</button>
                </form>
            </div>

            <div class="space-y-4">
                <div class="rounded-lg border p-5" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-2 text-sm font-bold text-text">Support Email</h5>
                    <p class="text-[13px] text-muted">{{ $setting->support_email ?: 'Use the form — it reaches us directly.' }}</p>
                </div>
                <div class="rounded-lg border p-5" style="border-color:var(--border);background:var(--bg-card);">
                    <h5 class="mb-2 text-sm font-bold text-text">Order Questions</h5>
                    <p class="text-[13px] leading-5 text-muted">Include your order number (WM-...) so we can help faster. Find it under <a href="{{ route('account.orders') }}" class="font-semibold text-accent">My Purchases</a>.</p>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
