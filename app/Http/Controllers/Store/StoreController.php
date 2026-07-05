<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function home(): View
    {
        return view('store.home', [
            'featured' => Product::published()
                ->with(['category', 'images'])
                ->withCount('approvedReviews')
                ->withAvg('approvedReviews', 'rating')
                ->latest()
                ->take(8)
                ->get(),
            'categories' => Category::withCount(['products' => fn ($query) => $query->published()])
                ->orderBy('name')
                ->get()
                ->filter(fn ($category) => $category->products_count > 0),
        ]);
    }

    public function index(Request $request): View
    {
        $products = Product::published()
            ->with(['category', 'images'])
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('short_description', 'like', '%' . $request->q . '%');
            }))
            ->when($request->filled('category'), fn ($query) => $query->whereRelation('category', 'slug', $request->category))
            ->when(
                $request->input('sort') === 'price_asc',
                fn ($query) => $query->orderByRaw('COALESCE(sale_price, price) asc'),
                fn ($query) => $request->input('sort') === 'price_desc'
                    ? $query->orderByRaw('COALESCE(sale_price, price) desc')
                    : $query->latest()
            )
            ->paginate(12)
            ->withQueryString();

        return view('store.products', [
            'products' => $products,
            'categories' => Category::withCount(['products' => fn ($query) => $query->published()])
                ->orderBy('name')
                ->get()
                ->filter(fn ($category) => $category->products_count > 0),
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        abort_unless($product->isPublished(), 404);

        $product->load(['category', 'images', 'releases', 'approvedReviews.user']);

        $user = $request->user();

        return view('store.show', [
            'product' => $product,
            'reviews' => $product->approvedReviews->sortByDesc('created_at')->values(),
            'averageRating' => round((float) $product->approvedReviews->avg('rating'), 1),
            'canReview' => $user !== null && Review::canBeWrittenBy($user, $product),
            'ownReview' => $user?->id
                ? $product->reviews()->where('user_id', $user->id)->first()
                : null,
            'related' => Product::published()
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
                ->with('images')
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}
