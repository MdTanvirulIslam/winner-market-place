<div>
    <label class="panel-label" for="code">Code</label>
    <input class="panel-input mt-1" type="text" id="code" name="code" value="{{ old('code', $coupon?->code) }}" placeholder="e.g. LAUNCH20" required style="text-transform:uppercase;">
    <p class="mt-1 text-[12px] text-muted">Letters, numbers and dashes. Customers type this at checkout (case-insensitive).</p>
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="panel-label" for="type">Type</label>
        <select class="panel-select mt-1" id="type" name="type" required>
            <option value="percent" @selected(old('type', $coupon?->type ?? 'percent') === 'percent')>Percent off</option>
            <option value="fixed" @selected(old('type', $coupon?->type) === 'fixed')>Fixed amount off</option>
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="value">Value</label>
        <input class="panel-input mt-1" type="number" id="value" name="value" value="{{ old('value', $coupon?->value) }}" step="0.01" min="0.01" placeholder="e.g. 20" required>
        <p class="mt-1 text-[12px] text-muted">Percent (max 100) or the amount in your store currency.</p>
        <x-input-error :messages="$errors->get('value')" class="mt-2" />
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="panel-label" for="expires_at">Expires (optional)</label>
        <input class="panel-input mt-1" type="date" id="expires_at" name="expires_at" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d')) }}">
        <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="max_uses">Max Uses (optional)</label>
        <input class="panel-input mt-1" type="number" id="max_uses" name="max_uses" value="{{ old('max_uses', $coupon?->max_uses) }}" min="1" placeholder="Unlimited">
        <x-input-error :messages="$errors->get('max_uses')" class="mt-2" />
    </div>
</div>

<label class="flex items-center gap-2 text-sm text-text">
    <input type="checkbox" name="active" value="1" @checked(old('active', $coupon?->active ?? true))>
    Active — customers can apply this coupon
</label>
