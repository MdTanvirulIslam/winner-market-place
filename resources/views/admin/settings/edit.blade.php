<x-admin-layout title="Settings">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>Settings</h4>
            <p class="text-[13px] text-muted">Store preferences and integration status.</p>
        </div>
    </div>

    <div class="grid gap-3 lg:grid-cols-12">
        <div class="animate-in opacity-0 lg:col-span-7">
            <div class="content-card">
                <div class="content-card-header"><h5>Store</h5></div>
                <div class="content-card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="panel-label" for="store_name">Store Name</label>
                            <input class="panel-input mt-1" type="text" id="store_name" name="store_name" value="{{ old('store_name', $setting->store_name) }}" required>
                            <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
                        </div>
                        <div>
                            <label class="panel-label" for="support_email">Support Email</label>
                            <input class="panel-input mt-1" type="email" id="support_email" name="support_email" value="{{ old('support_email', $setting->support_email) }}" placeholder="support@example.com">
                            <x-input-error :messages="$errors->get('support_email')" class="mt-2" />
                        </div>
                        <div>
                            <label class="panel-label" for="currency">Currency</label>
                            <select class="panel-select mt-1" id="currency" name="currency" required>
                                <option value="BDT" @selected(old('currency', $setting->currency) === 'BDT')>BDT — Bangladeshi Taka</option>
                                <option value="USD" @selected(old('currency', $setting->currency) === 'USD')>USD — US Dollar</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="animate-in opacity-0 lg:col-span-5">
            <div class="content-card h-full">
                <div class="content-card-header"><h5>Integrations</h5></div>
                <div class="content-card-body">
                    <p class="mb-4 text-[13px] text-muted">
                        Credentials are read from the server's <code class="font-semibold">.env</code> file and are never shown here.
                        Edit them on the server, then reload this page.
                    </p>
                    <div class="space-y-3">
                        @foreach($integrations as $label => $configured)
                            <div class="flex items-center justify-between border-b pb-3 last:border-b-0" style="border-color:var(--border);">
                                <span class="text-[13px] font-medium">{{ $label }}</span>
                                @if($configured)
                                    <span class="status-badge success">Configured</span>
                                @else
                                    <span class="status-badge failed">Not set</span>
                                @endif
                            </div>
                        @endforeach
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium">SSLCommerz mode</span>
                            <span class="status-badge {{ $sandbox ? 'pending' : 'success' }}">{{ $sandbox ? 'Sandbox' : 'Live' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
