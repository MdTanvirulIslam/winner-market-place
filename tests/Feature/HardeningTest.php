<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_registration_is_rate_limited(): void
    {
        foreach (range(1, 10) as $i) {
            $this->post('/register', []);
        }

        $this->post('/register', [
            'name' => 'Over Limit',
            'email' => 'over@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(429);
    }

    public function test_custom_404_page_renders(): void
    {
        $this->get('/products/does-not-exist')
            ->assertNotFound()
            ->assertSee('Back to the store');
    }

    public function test_backup_command_zips_releases_and_prunes(): void
    {
        Storage::fake('local');

        $product = Product::factory()->create(['slug' => 'backup-product']);
        $release = Release::factory()->create([
            'product_id' => $product->id,
            'file_path' => 'releases/backup-product/backup-product-1.0.0.zip',
        ]);
        Storage::disk('local')->put($release->file_path, 'zip-bytes');

        // Pre-seed 8 old zips — the run must prune down to KEEP (7).
        foreach (range(1, 8) as $i) {
            Storage::disk('local')->put(sprintf('backups/releases-2026-01-0%d-000000.zip', $i), 'old');
        }

        $exit = Artisan::call('backup:run');
        $this->assertSame(0, $exit);

        $zips = collect(Storage::disk('local')->files('backups'))
            ->filter(fn ($file) => str_ends_with($file, '.zip'));

        $this->assertCount(7, $zips); // 8 old + 1 new, pruned to 7
        $this->assertStringContainsString('Releases zipped', Artisan::output());
    }

    public function test_backup_command_skips_db_dump_on_non_mysql(): void
    {
        Storage::fake('local');

        $exit = Artisan::call('backup:run');

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('not mysql', Artisan::output());
    }
}
