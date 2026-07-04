<x-store-layout title="Profile">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <h1 class="mb-6 font-heading text-3xl font-extrabold text-text">My Account</h1>
        @unless(auth()->user()->isAdmin())
            @include('partials.store.account-nav')
        @endunless

        <div class="space-y-4">
            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-lg border p-6" style="border-color:var(--border);background:var(--bg-card);">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
