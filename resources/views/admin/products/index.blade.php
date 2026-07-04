<x-admin-layout title="Products">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>Products</h4>
            <p class="text-[13px] text-muted">The applications you sell. Slugs must match the License Manager.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="boxes"></span> New Product
        </a>
    </div>

    <div class="content-card animate-in opacity-0 mb-4">
        <div class="content-card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="min-w-[220px] flex-1">
                    <label class="panel-label" for="q">Search</label>
                    <input class="panel-input mt-1" type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Name or slug...">
                </div>
                <div>
                    <label class="panel-label" for="status">Status</label>
                    <select class="panel-select mt-1" id="status" name="status">
                        <option value="">All</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Filter</button>
            </form>
        </div>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Product</th><th>Slug</th><th>Category</th><th>Price</th><th>Releases</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="font-semibold">{{ $product->name }}</td>
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
                                        <a href="{{ route('store.products.show', $product->slug) }}" target="_blank" class="text-[13px] font-semibold text-muted">View</a>
                                    @endif
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete {{ $product->name }} with all its screenshots and releases?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[13px] font-semibold text-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No products yet — create the first one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</x-admin-layout>
