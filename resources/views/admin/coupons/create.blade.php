<x-admin-layout title="New Coupon">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Coupons' => route('admin.coupons.index'), 'New' => null]" />
            <h4>New Coupon</h4>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf
                @include('admin.coupons._form', ['coupon' => null])
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Create Coupon</button>
                    <a href="{{ route('admin.coupons.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
