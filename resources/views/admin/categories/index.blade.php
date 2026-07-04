<x-admin-layout title="Categories">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>Categories</h4>
            <p class="text-[13px] text-muted">Group products for browsing and filtering.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="layout-template"></span> New Category
        </a>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="font-semibold">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->products_count }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete {{ $category->name }}? Its products will become uncategorized.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[13px] font-semibold text-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No categories yet — create the first one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-admin-layout>
