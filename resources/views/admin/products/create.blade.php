<x-admin-layout title="New Product">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>New Product</h4>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-4xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @include('admin.products._form', ['product' => null])
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Create Product</button>
                    <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
