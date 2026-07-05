<?php

namespace Tests\Feature\Admin;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_cannot_access_analytics(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin/analytics')->assertForbidden();
    }

    public function test_analytics_page_shows_sales_and_coupon_figures(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        Coupon::factory()->create(['code' => 'SAVE20', 'used_count' => 1]);
        // The factory snapshots product_name from the related product.
        Order::factory()->delivered()->create([
            'amount' => 8000,
            'discount_amount' => 2000,
            'coupon_code' => 'SAVE20',
            'product_id' => Product::factory()->create(['name' => 'News Portal Application'])->id,
        ]);
        Order::factory()->paid()->create([
            'amount' => 5000,
            'product_id' => Product::factory()->create(['name' => 'POS System'])->id,
        ]);
        Order::factory()->create([ // pending — excluded
            'amount' => 9999,
            'product_id' => Product::factory()->create(['name' => 'Draft Thing'])->id,
        ]);

        $response = $this->actingAs($staff)->get('/admin/analytics');

        $response->assertOk()
            ->assertSee('Total Revenue')
            ->assertSee('SAVE20')
            ->assertSee('News Portal Application')
            ->assertSee('POS System')
            // 8000 + 5000 — the pending 9999 order is excluded from revenue.
            ->assertSee('13,000.00');
    }
}
