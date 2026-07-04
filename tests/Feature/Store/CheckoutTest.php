<?php

namespace Tests\Feature\Store;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_login_before_checkout(): void
    {
        $product = Product::factory()->published()->create();

        $this->get('/checkout/' . $product->slug)->assertRedirect('/login');
    }

    public function test_checkout_creates_a_pending_order_with_snapshot(): void
    {
        $product = Product::factory()->published()->create([
            'price' => 10000,
            'sale_price' => 8000,
        ]);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->post('/checkout/' . $product->slug);

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('account.orders.show', $order));

        $this->assertSame('pending', $order->status);
        $this->assertSame($product->slug, $order->product_slug);
        $this->assertSame($product->name, $order->product_name);
        $this->assertSame($customer->email, $order->customer_email);
        $this->assertSame('8000.00', (string) $order->amount); // sale price wins
        $this->assertMatchesRegularExpression('/^WM-\d{4}-\d{6}$/', $order->order_no);
    }

    public function test_an_open_pending_order_is_reused(): void
    {
        $product = Product::factory()->published()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/checkout/' . $product->slug);
        $this->actingAs($customer)->post('/checkout/' . $product->slug);

        $this->assertSame(1, Order::count());
    }

    public function test_draft_products_cannot_be_bought(): void
    {
        $draft = Product::factory()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/checkout/' . $draft->slug)->assertNotFound();
        $this->actingAs($customer)->post('/checkout/' . $draft->slug)->assertNotFound();
    }

    public function test_customers_only_see_their_own_orders(): void
    {
        $order = Order::factory()->create();
        $stranger = User::factory()->create(['role' => 'customer']);

        $this->actingAs($stranger)->get(route('account.orders.show', $order))->assertNotFound();
    }
}
