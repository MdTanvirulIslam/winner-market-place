<?php

namespace Tests\Feature\Store;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function checkout(User $customer, Product $product): void
    {
        $this->actingAs($customer)
            ->post('/checkout/' . $product->slug, ['customer_phone' => '01712345678']);
    }

    public function test_percent_coupon_discounts_the_order(): void
    {
        $product = Product::factory()->published()->create(['price' => 10000, 'sale_price' => null]);
        $coupon = Coupon::factory()->create(['code' => 'SAVE20', 'type' => 'percent', 'value' => 20]);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->post('/checkout/' . $product->slug . '/coupon', ['code' => 'save20'])
            ->assertSessionHas('success');

        $this->checkout($customer, $product);

        $order = Order::first();
        $this->assertSame('8000.00', (string) $order->amount);
        $this->assertSame('2000.00', (string) $order->discount_amount);
        $this->assertSame('SAVE20', $order->coupon_code);
        $this->assertSame(1, $coupon->fresh()->used_count);
        // Consumed: the code no longer sits in the session.
        $this->assertNull(session('checkout_coupon'));
    }

    public function test_fixed_coupon_is_capped_at_the_order_total(): void
    {
        $product = Product::factory()->published()->create(['price' => 500, 'sale_price' => null]);
        Coupon::factory()->fixed(2000)->create(['code' => 'BIGCUT']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/checkout/' . $product->slug . '/coupon', ['code' => 'BIGCUT']);
        $this->checkout($customer, $product);

        $order = Order::first();
        $this->assertSame('0.00', (string) $order->amount);
        $this->assertSame('500.00', (string) $order->discount_amount);
    }

    public function test_unknown_and_unredeemable_coupons_are_rejected(): void
    {
        $product = Product::factory()->published()->create();
        Coupon::factory()->create(['code' => 'EXPIRED', 'expires_at' => now()->subDay()]);
        Coupon::factory()->create(['code' => 'USEDUP', 'max_uses' => 1, 'used_count' => 1]);
        Coupon::factory()->create(['code' => 'DISABLED', 'active' => false]);
        $customer = User::factory()->create(['role' => 'customer']);

        foreach (['NOPE', 'EXPIRED', 'USEDUP', 'DISABLED'] as $code) {
            $this->actingAs($customer)
                ->from('/checkout/' . $product->slug)
                ->post('/checkout/' . $product->slug . '/coupon', ['code' => $code])
                ->assertSessionHasErrors('code');
        }

        $this->checkout($customer, $product);
        $this->assertNull(Order::first()->coupon_code);
    }

    public function test_coupon_can_be_removed_before_placing_the_order(): void
    {
        $product = Product::factory()->published()->create(['price' => 10000, 'sale_price' => null]);
        Coupon::factory()->create(['code' => 'SAVE10', 'value' => 10]);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/checkout/' . $product->slug . '/coupon', ['code' => 'SAVE10']);
        $this->actingAs($customer)->delete('/checkout/' . $product->slug . '/coupon');
        $this->checkout($customer, $product);

        $order = Order::first();
        $this->assertSame('10000.00', (string) $order->amount);
        $this->assertNull($order->coupon_code);
    }

    public function test_a_coupon_that_becomes_unredeemable_is_dropped_at_order_time(): void
    {
        $product = Product::factory()->published()->create(['price' => 10000, 'sale_price' => null]);
        $coupon = Coupon::factory()->create(['code' => 'LASTONE', 'max_uses' => 1]);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/checkout/' . $product->slug . '/coupon', ['code' => 'LASTONE']);

        // Someone else exhausts the coupon before the order is placed.
        $coupon->update(['used_count' => 1]);

        $this->checkout($customer, $product);

        $order = Order::first();
        $this->assertSame('10000.00', (string) $order->amount);
        $this->assertNull($order->coupon_code);
        $this->assertSame(1, $coupon->fresh()->used_count); // not double-counted
    }
}
