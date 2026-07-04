<x-admin-layout title="Customers">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Sales' => null, 'Customers' => null]" />
            <h4>Customers</h4>
            <p class="text-[13px] text-muted">Everyone with a store account.</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 mb-4">
        <div class="content-card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="min-w-[220px] flex-1">
                    <label class="panel-label" for="q">Search</label>
                    <input class="panel-input mt-1" type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Name or email...">
                </div>
                <button type="submit" class="rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Search</button>
            </form>
        </div>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Joined</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[13px] font-bold text-white" style="background:linear-gradient(135deg,#14b8a6,#0d9488);">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                    <span class="font-semibold">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->orders_count }}</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.orders.index', ['q' => $customer->email]) }}" class="text-[13px] font-semibold text-accent">View Orders</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
</x-admin-layout>
