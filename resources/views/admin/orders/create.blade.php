<x-admin-layout title="New Manual Order">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Orders' => route('admin.orders.index'), 'New Manual Order' => null]" />
            <h4>New Manual Order</h4>
            <p class="text-[13px] text-muted">For WhatsApp / bank-transfer deals — create the order, then Mark as Paid once the money arrives.</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="panel-label" for="product_id">Product</label>
                    <select class="panel-select mt-1" id="product_id" name="product_id" required>
                        <option value="">— Choose product —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }} ({{ format_price($product->effectivePrice()) }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="panel-label" for="customer_name">Customer Name</label>
                        <input class="panel-input mt-1" type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                        <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                    </div>
                    <div>
                        <label class="panel-label" for="customer_email">Customer Email</label>
                        <input class="panel-input mt-1" type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                        <p class="mt-1 text-[12px] text-muted">License and downloads go to this address; an account is created if none exists.</p>
                        <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <label class="panel-label" for="amount">Agreed Amount (optional)</label>
                    <input class="panel-input mt-1" type="number" step="0.01" min="0" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Leave empty to use the product price">
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Create Order</button>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
