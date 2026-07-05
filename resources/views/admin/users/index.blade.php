<x-admin-layout title="Admin Users">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Admin' => null, 'Admin Users' => null]" />
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
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[13px] font-bold text-white" style="background:linear-gradient(135deg,#a78bfa,#7c3aed);">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <span class="font-semibold">{{ $user->name }}</span>
                                </div>
                            </td>
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
                                        <button type="button" data-modal-open="delete-user-{{ $user->id }}" class="text-[13px] font-semibold text-danger">Delete</button>
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
        {{ $users->links('vendor.pagination.admin') }}
    </div>

    @foreach($users as $user)
        @unless($user->is(auth()->user()))
            <x-confirm-modal
                id="delete-user-{{ $user->id }}"
                title="Delete {{ $user->name }}?"
                message="{{ $user->email }} will lose all admin access immediately. This cannot be undone."
                :action="route('admin.users.destroy', $user)"
                method="DELETE"
                confirm-label="Delete User" />
        @endunless
    @endforeach
</x-admin-layout>
