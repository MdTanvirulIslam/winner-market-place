<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Streams product screenshots from the public disk. Serving through PHP
// instead of the public/storage symlink because shared-hosting deploys kept
// breaking the symlink/permissions — this path has no such dependency.
class MediaController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse|Response
    {
        abort_unless(str_starts_with($path, 'products/'), 404);
        abort_if(str_contains($path, '..'), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
