<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->isPublished(), 404);

        // Verified buyers only — a delivered (not refunded) order is proof.
        abort_unless(Review::canBeWrittenBy($request->user(), $product), 403);

        if ($product->reviews()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('info', 'You have already reviewed this product.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:10|max:2000',
        ]);

        $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $data['rating'],
            'body' => $data['body'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thanks! Your review will appear once it has been approved.');
    }
}
