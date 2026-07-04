<?php

namespace Tests\Feature\Admin;

use App\Mail\OrderCompletedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Self-invented test values — never real credentials.
        config([
            'marketplace.license_manager.url' => 'https://license-manager.test',
            'marketplace.license_manager.token' => 'test-token-for-phpunit-only',
        ]);
    }

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function fakeProvisioningSuccess(): void
    {
        Http::fake([
            'license-manager.test/api/licenses' => Http::response([
                'created' => true,
                'license' => ['key' => 'WD-TESTKEY-123', 'product' => 'test-product'],
                'delivery_url' => 'https://license-manager.test/claim/abc',
                'delivery_expires_at' => now()->addHours(72)->toIso8601String(),
                'email' => 'sent',
            ], 201),
        ]);
    }

    public function test_manual_order_creates_a_customer_account(): void
    {
        $product = Product::factory()->published()->create();

        $this->actingAs($this->staff())->post('/admin/orders', [
            'product_id' => $product->id,
            'customer_name' => 'Walk-in Customer',
            'customer_email' => 'walkin@example.com',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame('pending', $order->status);

        $user = User::where('email', 'walkin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('customer', $user->role);
        $this->assertSame($user->id, $order->user_id);
    }

    public function test_mark_paid_provisions_license_and_delivers(): void
    {
        Mail::fake();
        $this->fakeProvisioningSuccess();

        $order = Order::factory()->create();

        $this->actingAs($this->staff())->post(route('admin.orders.mark-paid', $order));

        $order->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertSame('WD-TESTKEY-123', $order->license_key);
        $this->assertSame('provisioned', $order->provisioning_status);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($order->delivered_at);

        Http::assertSent(function ($request) use ($order) {
            return str_contains($request->url(), '/api/licenses')
                && $request['product'] === $order->product_slug
                && $request['customer_email'] === $order->customer_email
                && $request['send_email'] === true
                && $request->hasHeader('Authorization', 'Bearer test-token-for-phpunit-only');
        });

        Mail::assertSent(OrderCompletedMail::class, fn ($mail) => $mail->hasTo($order->customer_email));
    }

    public function test_provisioning_failure_keeps_order_paid_with_flag(): void
    {
        Mail::fake();
        Http::fake([
            'license-manager.test/api/licenses' => Http::response(['message' => 'Server Error'], 500),
        ]);

        $order = Order::factory()->create();

        $this->actingAs($this->staff())->post(route('admin.orders.mark-paid', $order));

        $order->refresh();
        $this->assertSame('paid', $order->status); // money is never ambiguous
        $this->assertSame('failed', $order->provisioning_status);
        $this->assertNotNull($order->provisioning_error);
        $this->assertNull($order->license_key);

        Mail::assertNothingSent();
    }

    public function test_retry_provisioning_delivers_after_a_failure(): void
    {
        Mail::fake();
        $this->fakeProvisioningSuccess();

        $order = Order::factory()->paid()->create(['provisioning_status' => 'failed', 'provisioning_error' => 'HTTP 500']);

        $this->actingAs($this->staff())->post(route('admin.orders.retry-provisioning', $order));

        $order->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertSame('WD-TESTKEY-123', $order->license_key);
    }

    public function test_retry_never_creates_a_duplicate_license(): void
    {
        Mail::fake();
        Http::fake();

        // License already exists (e.g. delivery-step crash after provisioning).
        $order = Order::factory()->paid()->create([
            'license_key' => 'WD-EXISTING-KEY',
            'provisioning_status' => 'failed',
        ]);

        $this->actingAs($this->staff())->post(route('admin.orders.retry-provisioning', $order));

        $order->refresh();
        $this->assertSame('delivered', $order->status);
        $this->assertSame('WD-EXISTING-KEY', $order->license_key);

        Http::assertNothingSent(); // idempotent — no second license created
    }

    public function test_refund_blocks_downloads(): void
    {
        $order = Order::factory()->delivered()->create();

        $this->actingAs($this->staff())->post(route('admin.orders.refund', $order));

        $order->refresh();
        $this->assertSame('refunded', $order->status);
        $this->assertFalse($order->allowsDownloads());
    }

    public function test_only_pending_orders_can_be_cancelled(): void
    {
        $delivered = Order::factory()->delivered()->create();
        $pending = Order::factory()->create();

        $this->actingAs($this->staff())->post(route('admin.orders.cancel', $delivered));
        $this->assertSame('delivered', $delivered->fresh()->status);

        $this->actingAs($this->staff())->post(route('admin.orders.cancel', $pending));
        $this->assertSame('cancelled', $pending->fresh()->status);
    }

    public function test_customers_cannot_touch_admin_order_actions(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create();

        $this->actingAs($customer)->post(route('admin.orders.mark-paid', $order))->assertForbidden();
        $this->assertSame('pending', $order->fresh()->status);
    }
}
