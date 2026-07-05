<x-admin-layout title="Products">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Catalog' => null, 'Products' => null]" />
            <h4>Products</h4>
            <p class="text-[13px] text-muted">The applications you sell. Slugs must match the License Manager.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="boxes"></span> New Product
        </a>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body">
            <x-datatable-toolbar :action="route('admin.products.index')" search-placeholder="Search by name or slug…">
                <select class="panel-select w-auto py-2.5 text-[13px]" name="status" data-autosubmit aria-label="Status">
                    <option value="">All statuses</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
            </x-datatable-toolbar>
        </div>
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <x-sort-th field="name" label="Product" />
                    <th>Slug</th>
                    <th>Category</th>
                    <x-sort-th field="price" label="Price" />
                    <x-sort-th field="releases_count" label="Releases" />
                    <x-sort-th field="status" label="Status" />
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-16 shrink-0 overflow-hidden rounded-sm" style="background:var(--bg-input);">
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ $product->images->first()->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-muted"><span class="icon" data-icon="boxes"></span></div>
                                        @endif
                                    </div>
                                    <span class="font-semibold">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td><code>{{ $product->slug }}</code></td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td class="font-semibold">
                                @if($product->isOnSale())
                                    <span class="text-muted line-through">{{ format_price($product->price) }}</span>
                                    {{ format_price($product->sale_price) }}
                                @else
                                    {{ format_price($product->price) }}
                                @endif
                            </td>
                            <td>{{ $product->releases_count }}</td>
                            <td>
                                @if($product->isPublished())
                                    <span class="status-badge success">Published</span>
                                @else
                                    <span class="status-badge pending">Draft</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($product->isPublished())
                                        <a href="{{ route('store.products.show', $product->slug) }}" target="_blank" class="action-btn" title="View in store" aria-label="View {{ $product->name }} in store">
                                            <span class="icon" data-icon="eye"></span>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.products.edit', $product) }}" class="action-btn" title="Edit" aria-label="Edit {{ $product->name }}">
                                        <span class="icon" data-icon="square-pen"></span>
                                    </a>
                                    <button type="button" data-modal-open="delete-product-{{ $product->id }}" class="action-btn danger" title="Delete" aria-label="Delete {{ $product->name }}">
                                        <span class="icon" data-icon="trash-2"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No products {{ request()->hasAny(['q', 'status']) ? 'match your filters' : 'yet — create the first one' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
        {{ $products->links('vendor.pagination.admin') }}
    </div>

    @foreach($products as $product)
        <x-confirm-modal
            id="delete-product-{{ $product->id }}"
            title="Delete {{ $product->name }}?"
            message="All its screenshots and release files will be removed from the server. Past orders keep their records. This cannot be undone."
            :action="route('admin.products.destroy', $product)"
            method="DELETE"
            confirm-label="Delete Product" />
    @endforeach
</x-admin-layout>
