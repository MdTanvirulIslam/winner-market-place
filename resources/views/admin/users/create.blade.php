<x-admin-layout title="New Admin User">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Admin Users' => route('admin.users.index'), 'New' => null]" />
            <h4>New Admin User</h4>
            <p class="text-[13px] text-muted">Create a staff or super admin account.</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                @include('admin.users._form', ['user' => null])
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Create User</button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
