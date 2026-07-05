<x-admin-layout title="Coupons">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Sales' => null, 'Coupons' => null]" />
            <h4>Coupons</h4>
            <p class="text-[13px] text-muted">Discount codes customers can apply at checkout.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="ticket"></span> New Coupon
        </a>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body">
            <x-datatable-toolbar :action="route('admin.coupons.index')" search-placeholder="Search by code…" />
        </div>
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <x-sort-th field="code" label="Code" />
                    <x-sort-th field="value" label="Discount" />
                    <x-sort-th field="used_count" label="Usage" />
                    <x-sort-th field="expires_at" label="Expires" />
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td><code class="font-bold">{{ $coupon->code }}</code></td>
                            <td class="font-semibold">
                                {{ $coupon->type === 'percent' ? rtrim(rtrim(number_format((float) $coupon->value, 2), '0'), '.') . '%' : format_price($coupon->value) }}
                            </td>
                            <td>{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}</td>
                            <td>{{ $coupon->expires_at?->format('d M Y') ?? 'Never' }}</td>
                            <td>
                                @if($coupon->isRedeemable())
                                    <span class="status-badge success">Active</span>
                                @elseif(! $coupon->active)
                                    <span class="status-badge failed">Disabled</span>
                                @elseif($coupon->isExpired())
                                    <span class="status-badge failed">Expired</span>
                                @else
                                    <span class="status-badge pending">Exhausted</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                                    <button type="button" data-modal-open="delete-coupon-{{ $coupon->id }}" class="text-[13px] font-semibold text-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No coupons {{ request('q') ? 'match your search' : 'yet — create the first one' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
        {{ $coupons->links('vendor.pagination.admin') }}
    </div>

    @foreach($coupons as $coupon)
        <x-confirm-modal
            id="delete-coupon-{{ $coupon->id }}"
            title="Delete {{ $coupon->code }}?"
            message="Customers will no longer be able to apply this code. Past orders keep their discount. This cannot be undone."
            :action="route('admin.coupons.destroy', $coupon)"
            method="DELETE"
            confirm-label="Delete Coupon" />
    @endforeach
</x-admin-layout>
