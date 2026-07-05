<x-admin-layout title="Reviews">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Sales' => null, 'Reviews' => null]" />
            <h4>Reviews</h4>
            <p class="text-[13px] text-muted">
                Customer reviews from verified buyers.
                @if($pendingCount > 0) <strong class="text-text">{{ $pendingCount }} awaiting moderation.</strong> @endif
            </p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select class="panel-select" name="status" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach(['pending', 'approved', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="content-card animate-in opacity-0">
        <div class="content-card-body p-0"><div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Product</th><th>Customer</th><th>Rating</th><th>Review</th><th>Status</th><th>Date</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="font-semibold">{{ $review->product?->name ?? '—' }}</td>
                            <td>{{ $review->user?->name ?? '—' }}</td>
                            <td>@include('partials.store.stars', ['rating' => $review->rating])</td>
                            <td class="max-w-md"><span class="line-clamp-2 text-[13px]">{{ $review->body }}</span></td>
                            <td>
                                <span class="status-badge {{ ['approved' => 'success', 'pending' => 'pending'][$review->status] ?? 'failed' }}">{{ ucfirst($review->status) }}</span>
                            </td>
                            <td class="text-[13px] text-muted">{{ $review->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($review->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                            @csrf
                                            <button type="submit" class="text-[13px] font-semibold text-success" style="color:#16a34a;">Approve</button>
                                        </form>
                                    @endif
                                    @if($review->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                            @csrf
                                            <button type="submit" class="text-[13px] font-semibold text-muted">Reject</button>
                                        </form>
                                    @endif
                                    <button type="button" data-modal-open="delete-review-{{ $review->id }}" class="text-[13px] font-semibold text-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No reviews {{ request('status') ? 'with this status' : 'yet' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>

    @foreach($reviews as $review)
        <x-confirm-modal
            id="delete-review-{{ $review->id }}"
            title="Delete this review?"
            message="The review by {{ $review->user?->name ?? 'a customer' }} will be permanently removed. This cannot be undone."
            :action="route('admin.reviews.destroy', $review)"
            method="DELETE"
            confirm-label="Delete Review" />
    @endforeach
</x-admin-layout>
