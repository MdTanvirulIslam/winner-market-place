<x-admin-layout title="Admin Users">
    <div class="page-header animate-in opacity-0">
        <div>
            <h4>Admin Users</h4>
            <p class="text-[13px] text-muted">Staff and super admin accounts for this store.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="user-plus"></span> New Admin User
        </a>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="font-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->isSuperAdmin())
                                    <span class="status-badge success">Super Admin</span>
                                @else
                                    <span class="status-badge pending">Staff</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                                    @unless($user->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[13px] font-semibold text-danger">Delete</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No admin users yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
