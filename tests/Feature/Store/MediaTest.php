<?php

namespace Tests\Feature\Store;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_screenshots_are_streamed_via_the_media_route(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/1/shot.png', 'png-bytes');

        $this->get('/media/products/1/shot.png')
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=604800, public');
    }

    public function test_missing_and_out_of_scope_paths_are_rejected(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/1/shot.png', 'png-bytes');

        $this->get('/media/products/1/missing.png')->assertNotFound();
        // Only product screenshots are public — nothing else on the disk.
        $this->get('/media/other/file.txt')->assertNotFound();
        // No path traversal.
        $this->get('/media/products/..%2F..%2F.env')->assertNotFound();
    }
}
