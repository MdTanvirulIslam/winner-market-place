<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Release;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Versioned product zips. Files live on the private local disk
// (storage/app/private) and are never web-accessible directly; customer
// downloads go through authorized signed routes in Phase 2.
class ReleaseController extends Controller
{
    public function index(Request $request): View
    {
        $releases = Release::with('product')
            ->when($request->filled('product'), fn ($query) => $query->whereRelation('product', 'slug', $request->product))
            ->orderByDesc('released_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.releases.index', [
            'releases' => $releases,
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.releases.create', [
            'products' => Product::orderBy('name')->get(),
            'preselected' => $request->integer('product_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'version' => [
                'required', 'string', 'max:50', 'regex:/^[0-9]+(\.[0-9]+)*([.-][a-zA-Z0-9]+)*$/',
                Rule::unique('releases')->where('product_id', $request->integer('product_id')),
            ],
            'notes' => 'nullable|string|max:5000',
            'file' => 'required|file|mimes:zip|max:204800',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $file = $request->file('file');

        $path = $file->storeAs(
            'releases/' . $product->slug,
            $product->slug . '-' . $data['version'] . '.zip',
            'local'
        );

        $product->releases()->create([
            'version' => $data['version'],
            'notes' => $data['notes'] ?? null,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'released_at' => now(),
        ]);

        return redirect()->route('admin.releases.index')->with('success', "Release {$data['version']} uploaded.");
    }

    public function edit(Release $release): View
    {
        return view('admin.releases.edit', compact('release'));
    }

    public function update(Request $request, Release $release): RedirectResponse
    {
        $data = $request->validate([
            'notes' => 'nullable|string|max:5000',
            'file' => 'nullable|file|mimes:zip|max:204800',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('local')->delete($release->file_path);

            $file = $request->file('file');
            $release->file_path = $file->storeAs(
                'releases/' . $release->product->slug,
                $release->product->slug . '-' . $release->version . '.zip',
                'local'
            );
            $release->file_size = $file->getSize();
        }

        $release->notes = $data['notes'] ?? null;
        $release->save();

        return redirect()->route('admin.releases.index')->with('success', "Release {$release->version} updated.");
    }

    public function destroy(Release $release): RedirectResponse
    {
        Storage::disk('local')->delete($release->file_path);
        $release->delete();

        return redirect()->route('admin.releases.index')->with('success', 'Release deleted.');
    }
}
