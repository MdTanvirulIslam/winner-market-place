<x-admin-layout title="Releases">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Catalog' => null, 'Releases' => null]" />
            <h4>Releases</h4>
            <p class="text-[13px] text-muted">Versioned product zips. Files are stored outside the web root and served only through authorized downloads.</p>
        </div>
        <a href="{{ route('admin.releases.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="arrow-up"></span> Upload Release
        </a>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body">
            <x-datatable-toolbar :action="route('admin.releases.index')" search-placeholder="Search by product or version…">
                <select class="panel-select w-auto py-2.5 text-[13px]" name="product" data-autosubmit aria-label="Product">
                    <option value="">All products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->slug }}" @selected(request('product') === $product->slug)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </x-datatable-toolbar>
        </div>
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <th>Product</th>
                    <x-sort-th field="version" label="Version" />
                    <x-sort-th field="file_size" label="Size" />
                    <x-sort-th field="download_count" label="Downloads" />
                    <x-sort-th field="released_at" label="Released" />
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($releases as $release)
                        <tr>
                            <td class="font-semibold">{{ $release->product->name }}</td>
                            <td><code>v{{ $release->version }}</code></td>
                            <td>{{ $release->fileSizeForHumans() }}</td>
                            <td>{{ number_format($release->download_count) }}</td>
                            <td>{{ $release->released_at?->format('d M Y') }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.releases.edit', $release) }}" class="action-btn" title="Edit" aria-label="Edit v{{ $release->version }} of {{ $release->product->name }}">
                                        <span class="icon" data-icon="square-pen"></span>
                                    </a>
                                    <button type="button" data-modal-open="delete-release-{{ $release->id }}" class="action-btn danger" title="Delete" aria-label="Delete v{{ $release->version }} of {{ $release->product->name }}">
                                        <span class="icon" data-icon="trash-2"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No releases {{ request()->hasAny(['q', 'product']) ? 'match your filters' : 'yet — upload the first zip' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
        {{ $releases->links('vendor.pagination.admin') }}
    </div>

    @foreach($releases as $release)
        <x-confirm-modal
            id="delete-release-{{ $release->id }}"
            title="Delete v{{ $release->version }} of {{ $release->product->name }}?"
            message="The zip file will be removed from the server and customers will no longer be able to download this version."
            :action="route('admin.releases.destroy', $release)"
            method="DELETE"
            confirm-label="Delete Release" />
    @endforeach
</x-admin-layout>
