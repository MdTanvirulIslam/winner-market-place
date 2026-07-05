<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    use WithDataTable;

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images'])
            ->withCount('releases')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('slug', 'like', '%' . $request->q . '%');
            }))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status));

        $products = $this->dataTable(
            $request,
            $query,
            ['name', 'price', 'status', 'releases_count', 'created_at'],
            fn ($query) => $query->orderByDesc('created_at')
        );

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => null,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = Product::create($data);

        $this->storeImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'releases']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));

        $this->storeImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        foreach ($product->releases as $release) {
            Storage::disk('local')->delete($release->file_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Screenshot removed.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            // Must match the License Manager product slug — the join key
            // used for license provisioning in Phase 2.
            'slug' => 'required|string|max:255|regex:/^[a-z0-9]+(-[a-z0-9]+)*$/|unique:products,slug' . ($product ? ',' . $product->id : ''),
            'short_description' => 'required|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'requirements' => 'nullable|string',
            'demo_url' => 'nullable|url|max:255',
            'price' => 'required|numeric|min:0|max:99999999',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'status' => 'required|in:draft,published',
        ]);
    }

    private function storeImages(Request $request, Product $product): void
    {
        // PHP drops uploads beyond its own ini limits before Laravel's rules
        // run, which surfaces as a vague "must be an image" — report the
        // real reason instead.
        foreach ($request->file('images', []) as $index => $file) {
            if (! $file->isValid()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "images.{$index}" => 'Upload failed: ' . $file->getErrorMessage(),
                ]);
            }
        }

        $request->validate([
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if (! $request->hasFile('images')) {
            return;
        }

        $sortOrder = (int) $product->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            $path = $file->store('products/' . $product->id, 'public');

            $product->images()->create([
                'path' => $path,
                'sort_order' => ++$sortOrder,
            ]);
        }
    }
}
