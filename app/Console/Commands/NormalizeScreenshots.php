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

            if ($size && $size[0] === Screenshot::WIDTH && $size[1] === Screenshot::HEIGHT) {
                $skipped++;
                continue;
            }

            $extension = strtolower(pathinfo($image->path, PATHINFO_EXTENSION));
            $output = Screenshot::normalize($binary, $extension);

            if ($output === null) {
                $this->error("FAILED {$image->path}");
                $failed++;
                continue;
            }

            $disk->put($image->path, $output);
            $this->info("OK {$image->path}");
            $normalized++;
        }

        $this->line("Normalized {$normalized}, skipped {$skipped} already correct, {$failed} failed.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
