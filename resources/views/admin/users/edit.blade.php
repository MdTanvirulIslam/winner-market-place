<x-admin-layout title="Edit Admin User">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>Edit Admin User</h4>
            <p class="text-[13px] text-muted">{{ $user->name }} ({{ $user->email }})</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                @include('admin.users._form', ['user' => $user])
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Save Changes</button>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
