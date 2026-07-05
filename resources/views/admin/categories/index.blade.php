<x-admin-layout title="Categories">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Catalog' => null, 'Categories' => null]" />
            <h4>Categories</h4>
            <p class="text-[13px] text-muted">Group products for browsing and filtering.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="layout-template"></span> New Category
        </a>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body">
            <x-datatable-toolbar :action="route('admin.categories.index')" search-placeholder="Search by name or slug…" />
        </div>
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr>
                    <x-sort-th field="name" label="Name" />
                    <th>Slug</th>
                    <x-sort-th field="products_count" label="Products" />
                    <th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="font-semibold">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->products_count }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                                    <button type="button" data-modal-open="delete-category-{{ $category->id }}" class="text-[13px] font-semibold text-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No categories {{ request('q') ? 'match your search' : 'yet — create the first one' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
        {{ $categories->links('vendor.pagination.admin') }}
    </div>

    @foreach($categories as $category)
        <x-confirm-modal
            id="delete-category-{{ $category->id }}"
            title="Delete {{ $category->name }}?"
            message="Its {{ $category->products_count }} {{ Str::plural('product', $category->products_count) }} will become uncategorized. This cannot be undone."
            :action="route('admin.categories.destroy', $category)"
            method="DELETE"
            confirm-label="Delete Category" />
    @endforeach
</x-admin-layout>
