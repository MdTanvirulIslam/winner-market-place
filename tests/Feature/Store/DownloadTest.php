<?php

namespace Tests\Feature\Store;

use App\Models\Order;
use App\Models\Product;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    private function deliveredOrderWithRelease(): array
    {
        Storage::fake('local');

        $product = Product::factory()->published()->create();
        $release = Release::factory()->create([
            'product_id' => $product->id,
            'file_path' => 'releases/' . $product->slug . '/' . $product->slug . '-1.0.0.zip',
            'version' => '1.0.0',
        ]);
        Storage::disk('local')->put($release->file_path, 'zip-bytes');

        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->delivered()->create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        return [$customer, $order, $release];
    }

    private function signedUrl(Order $order, Release $release): string
    {
        return URL::temporarySignedRoute('account.download', now()->addMinutes(30), [$order, $release]);
    }

    public function test_owner_can_download_with_a_signed_link(): void
    {
        [$customer, $order, $release] = $this->deliveredOrderWithRelease();

        $response = $this->actingAs($customer)->get($this->signedUrl($order, $release));

        $response->assertOk();
        $response->assertDownload($order->product_slug . '-v1.0.0.zip');

        $this->assertSame(1, $release->fresh()->download_count);
        $this->assertDatabaseHas('downloads', [
            'order_id' => $order->id,
            'release_id' => $release->id,
            'version' => '1.0.0',
        ]);
    }

    public function test_unsigned_links_are_rejected(): void
    {
        [$customer, $order, $release] = $this->deliveredOrderWithRelease();

        $this->actingAs($customer)
            ->get(route('account.download', [$order, $release]))
            ->assertForbidden();
    }

    public function test_other_users_cannot_download_someone_elses_order(): void
    {
        [, $order, $release] = $this->deliveredOrderWithRelease();
        $stranger = User::factory()->create(['role' => 'customer']);

        $this->actingAs($stranger)->get($this->signedUrl($order, $release))->assertNotFound();
    }

    public function test_pending_orders_cannot_download(): void
    {
        [$customer, $order, $release] = $this->deliveredOrderWithRelease();
        $order->update(['status' => 'pending']);

        $this->actingAs($customer)->get($this->signedUrl($order, $release))->assertForbidden();
    }

    public function test_refunded_orders_cannot_download(): void
    {
        [$customer, $order, $release] = $this->deliveredOrderWithRelease();
        $order->update(['status' => 'refunded']);

        $this->actingAs($customer)->get($this->signedUrl($order, $release))->assertForbidden();
    }

    public function test_releases_of_other_products_are_rejected(): void
    {
        [$customer, $order] = $this->deliveredOrderWithRelease();
        $otherRelease = Release::factory()->create(); // belongs to a different product

        $this->actingAs($customer)->get($this->signedUrl($order, $otherRelease))->assertNotFound();
    }
}
