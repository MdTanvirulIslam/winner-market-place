<x-admin-layout title="Releases">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>Releases</h4>
            <p class="text-[13px] text-muted">Versioned product zips. Files are stored outside the web root and served only through authorized downloads.</p>
        </div>
        <a href="{{ route('admin.releases.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="arrow-up"></span> Upload Release
        </a>
    </div>

    <div class="content-card animate-in opacity-0 mb-4">
        <div class="content-card-body">
            <form method="GET" action="{{ route('admin.releases.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="panel-label" for="product">Product</label>
                    <select class="panel-select mt-1" id="product" name="product">
                        <option value="">All products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->slug }}" @selected(request('product') === $product->slug)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Filter</button>
            </form>
        </div>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Product</th><th>Version</th><th>Size</th><th>Downloads</th><th>Released</th><th class="text-right">Actions</th></tr></thead>
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
                                    <a href="{{ route('admin.releases.edit', $release) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                                    <form method="POST" action="{{ route('admin.releases.destroy', $release) }}" onsubmit="return confirm('Delete v{{ $release->version }} of {{ $release->product->name }}? The zip file will be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[13px] font-semibold text-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No releases yet — upload the first zip.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>

    <div class="mt-4">{{ $releases->links() }}</div>
</x-admin-layout>
