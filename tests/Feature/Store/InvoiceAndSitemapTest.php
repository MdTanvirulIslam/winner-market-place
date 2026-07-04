<?php

namespace Tests\Feature\Store;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceAndSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_the_invoice_of_a_delivered_order(): void
    {
        $order = Order::factory()->delivered()->create();

        $this->actingAs($order->user)
            ->get(route('account.orders.invoice', $order))
            ->assertOk()
            ->assertSee($order->order_no)
            ->assertSee('Invoice')
            ->assertSee($order->license_key);
    }

    public function test_pending_orders_have_no_invoice(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($order->user)
            ->get(route('account.orders.invoice', $order))
            ->assertNotFound();
    }

    public function test_strangers_cannot_view_invoices(): void
    {
        $order = Order::factory()->delivered()->create();
        $stranger = User::factory()->create(['role' => 'customer']);

        $this->actingAs($stranger)
            ->get(route('account.orders.invoice', $order))
            ->assertNotFound();
    }

    public function test_checkout_sends_the_order_placed_email(): void
    {
        Mail::fake();
        $product = Product::factory()->published()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post('/checkout/' . $product->slug, [
            'customer_phone' => '01712345678',
        ]);

        Mail::assertSent(OrderPlacedMail::class, fn ($mail) => $mail->hasTo($customer->email));
    }

    public function test_sitemap_lists_published_products_only(): void
    {
        $published = Product::factory()->published()->create();
        $draft = Product::factory()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('store.products.show', $published->slug), false)
            ->assertDontSee(route('store.products.show', $draft->slug), false)
            ->assertSee(route('store.refund-policy'), false);
    }

    public function test_product_pages_carry_open_graph_tags(): void
    {
        $product = Product::factory()->published()->create();

        $this->get('/products/' . $product->slug)
            ->assertOk()
            ->assertSee('property="og:title"', false)
            ->assertSee('rel="canonical"', false);
    }
}
