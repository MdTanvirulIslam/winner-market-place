<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use WithDataTable;

    public function index(Request $request): View
    {
        $query = Category::withCount('products')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('slug', 'like', '%' . $request->q . '%');
            }));

        $categories = $this->dataTable(
            $request,
            $query,
            ['name', 'products_count', 'created_at'],
            fn ($query) => $query->orderBy('name')
        );

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Products keep existing with category_id = null (nullOnDelete).
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-z0-9]+(-[a-z0-9]+)*$/|unique:categories,slug' . ($category ? ',' . $category->id : ''),
            'description' => 'nullable|string|max:2000',
        ]);
    }
}
