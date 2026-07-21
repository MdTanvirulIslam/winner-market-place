<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Support\Screenshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

// Brings screenshots uploaded before server-side processing existed to the
// standard gallery size. Idempotent — runs from deploy/post-deploy.sh, so
// already-correct files are skipped on every subsequent deploy.
class NormalizeScreenshots extends Command
{
    protected $signature = 'screenshots:normalize';

    protected $description = 'Resize and crop existing product screenshots to the standard gallery size';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $normalized = $skipped = $failed = 0;

        foreach (ProductImage::all() as $image) {
            if (! $disk->exists($image->path)) {
                $this->warn("MISSING {$image->path}");
                $failed++;
                continue;
            }

            $binary = $disk->get($image->path);
            $size = @getimagesizefromstring($binary);
            $isWebp = strtolower(pathinfo($image->path, PATHINFO_EXTENSION)) === 'webp';

            // Already WebP at the gallery size — nothing to do. Legacy .png/.jpg
            // files are re-encoded even when correctly sized so the whole
            // library ends up as WebP.
            if ($isWebp && $size && $size[0] === Screenshot::WIDTH && $size[1] === Screenshot::HEIGHT) {
                $skipped++;
                continue;
            }

            $output = Screenshot::normalize($binary);

            if ($output === null) {
                $this->error("FAILED {$image->path}");
                $failed++;
                continue;
            }

            // Screenshots are stored as WebP now; migrate legacy .png/.jpg
            // files to a new .webp path and drop the old file.
            if (strtolower(pathinfo($image->path, PATHINFO_EXTENSION)) !== 'webp') {
                $oldPath = $image->path;
                $newPath = dirname($image->path) . '/' . Screenshot::filename();
                $disk->put($newPath, $output);
                $disk->delete($oldPath);
                $image->update(['path' => $newPath]);
                $this->info("OK {$oldPath} -> {$newPath}");
            } else {
                $disk->put($image->path, $output);
                $this->info("OK {$image->path}");
            }

            $normalized++;
        }

        $this->line("Normalized {$normalized}, skipped {$skipped} already correct, {$failed} failed.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
