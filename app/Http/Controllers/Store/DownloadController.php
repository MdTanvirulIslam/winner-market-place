<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Serves release zips from the private disk. Reached only through
// temporary signed URLs generated in the account area, and re-checks
// ownership + order state on every request.
class DownloadController extends Controller
{
    public function download(Request $request, Order $order, Release $release): StreamedResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->allowsDownloads(), 403, 'Downloads are not available for this order.');
        abort_unless($order->product_id !== null && $release->product_id === $order->product_id, 404);
        abort_unless(Storage::disk('local')->exists($release->file_path), 404, 'File not found.');

        $release->increment('download_count');

        $order->downloads()->create([
            'release_id' => $release->id,
            'user_id' => $request->user()->id,
            'version' => $release->version,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return Storage::disk('local')->download(
            $release->file_path,
            $order->product_slug . '-v' . $release->version . '.zip'
        );
    }
}
