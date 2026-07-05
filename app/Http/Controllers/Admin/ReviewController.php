<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    use WithDataTable;

    public function index(Request $request): View
    {
        $query = Review::with(['product', 'user'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('body', 'like', '%' . $request->q . '%')
                    ->orWhereRelation('product', 'name', 'like', '%' . $request->q . '%')
                    ->orWhereRelation('user', 'name', 'like', '%' . $request->q . '%');
            }));

        $reviews = $this->dataTable(
            $request,
            $query,
            ['rating', 'status', 'created_at'],
            fn ($query) => $query->latest()
        );

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'pendingCount' => Review::where('status', 'pending')->count(),
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => 'approved']);

        return back()->with('success', 'Review approved — it is now visible on the product page.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
