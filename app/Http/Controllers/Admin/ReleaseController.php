<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\WithDataTable;
use App\Http\Controllers\Controller;
use App\Mail\NewReleaseMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// Versioned product zips. Files live on the private local disk
// (storage/app/private) and are never web-accessible directly; customer
// downloads go through authorized signed routes in Phase 2.
class ReleaseController extends Controller
{
    use WithDataTable;

    public function index(Request $request): View
    {
        $query = Release::with('product')
            ->when($request->filled('product'), fn ($query) => $query->whereRelation('product', 'slug', $request->product))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('version', 'like', '%' . $request->q . '%')
                    ->orWhereRelation('product', 'name', 'like', '%' . $request->q . '%');
            }));

        $releases = $this->dataTable(
            $request,
            $query,
            ['version', 'file_size', 'download_count', 'released_at'],
            fn ($query) => $query->orderByDesc('released_at')
        );

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

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->rejectFailedUpload($request);

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'version' => [
                'required', 'string', 'max:50', 'regex:/^[0-9]+(\.[0-9]+)*([.-][a-zA-Z0-9]+)*$/',
                Rule::unique('releases')->where('product_id', $request->integer('product_id')),
            ],
            'notes' => 'nullable|string|max:20000',
            'file' => 'required|file|mimes:zip|max:204800',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $file = $request->file('file');

        $path = $file->storeAs(
            'releases/' . $product->slug,
            $product->slug . '-' . $data['version'] . '.zip',
            'local'
        );

        $release = $product->releases()->create([
            'version' => $data['version'],
            'notes' => $data['notes'] ?? null,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'released_at' => now(),
        ]);

        $notified = $request->boolean('notify_buyers') ? $this->notifyBuyers($release) : null;

        $message = "Release {$data['version']} uploaded."
            . ($notified !== null ? " Notified {$notified} " . Str::plural('buyer', $notified) . '.' : '');

        return $this->uploadResponse($request, $message);
    }

    public function edit(Release $release): View
    {
        return view('admin.releases.edit', compact('release'));
    }

    public function update(Request $request, Release $release): RedirectResponse|JsonResponse
    {
        $this->rejectFailedUpload($request);

        $data = $request->validate([
            'notes' => 'nullable|string|max:20000',
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

        return $this->uploadResponse($request, "Release {$release->version} updated.");
    }

    /**
     * The AJAX uploader expects JSON with a redirect target; plain form
     * posts (JS disabled) get the classic redirect. Both carry the flash.
     */
    private function uploadResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        $request->session()->flash('success', $message);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('admin.releases.index')]);
        }

        return redirect()->route('admin.releases.index');
    }

    /**
     * Surface the real PHP upload error (usually the host's ini size limits)
     * instead of a vague validation message.
     */
    private function rejectFailedUpload(Request $request): void
    {
        if ($request->hasFile('file') && ! $request->file('file')->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'file' => 'Upload failed: ' . $request->file('file')->getErrorMessage(),
            ]);
        }
    }

    /**
     * Email every buyer with a delivered (not refunded) order for the
     * release's product. Returns how many buyers were emailed.
     */
    private function notifyBuyers(Release $release): int
    {
        $emails = Order::where('product_id', $release->product_id)
            ->where('status', 'delivered')
            ->distinct()
            ->pluck('customer_email');

        $sent = 0;

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new NewReleaseMail($release));
                $sent++;
            } catch (Throwable $e) {
                report($e); // one bad address must not block the rest
            }
        }

        return $sent;
    }

    public function destroy(Release $release): RedirectResponse
    {
        Storage::disk('local')->delete($release->file_path);
        $release->delete();

        return redirect()->route('admin.releases.index')->with('success', 'Release deleted.');
    }
}
