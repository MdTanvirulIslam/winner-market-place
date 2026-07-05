<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataTableTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    public function test_columns_sort_and_unknown_sort_falls_back_to_default(): void
    {
        Product::factory()->create(['name' => 'Alpha App', 'price' => 900]);
        Product::factory()->create(['name' => 'Zulu App', 'price' => 100]);

        $this->actingAs($this->staff())->get('/admin/products?sort=price&dir=asc')
            ->assertOk()
            ->assertSeeInOrder(['Zulu App', 'Alpha App']);

        $this->actingAs($this->staff())->get('/admin/products?sort=price&dir=desc')
            ->assertOk()
            ->assertSeeInOrder(['Alpha App', 'Zulu App']);

        // Not in the whitelist — must not error, must not order by it.
        $this->actingAs($this->staff())->get('/admin/products?sort=secret_column&dir=desc')
            ->assertOk();
    }

    public function test_per_page_is_clamped_to_the_allowed_options(): void
    {
        Category::factory()->count(12)->create();
        $staff = $this->staff();

        // Allowed value applies.
        $response = $this->actingAs($staff)->get('/admin/categories?per_page=10');
        $response->assertOk()->assertSee('of <strong>12</strong>', false);
        $this->assertSame(10, $response->viewData('categories')->perPage());

        // Bogus values fall back to the default of 20.
        foreach (['1', '500', 'abc'] as $bogus) {
            $response = $this->actingAs($staff)->get('/admin/categories?per_page=' . $bogus);
            $this->assertSame(20, $response->viewData('categories')->perPage());
        }
    }

    public function test_category_and_coupon_search_filters_rows(): void
    {
        Category::factory()->create(['name' => 'News Portals', 'slug' => 'news-portals']);
        Category::factory()->create(['name' => 'POS Systems', 'slug' => 'pos-systems']);
        Coupon::factory()->create(['code' => 'LAUNCH20']);
        Coupon::factory()->create(['code' => 'WINTER50']);

        $this->actingAs($this->staff())->get('/admin/categories?q=news')
            ->assertOk()
            ->assertSee('News Portals')
            ->assertDontSee('POS Systems');

        $this->actingAs($this->staff())->get('/admin/coupons?q=launch')
            ->assertOk()
            ->assertSee('LAUNCH20')
            ->assertDontSee('WINTER50');
    }

    public function test_every_admin_list_page_renders_with_the_datatable_toolbar(): void
    {
        $staff = $this->staff();

        foreach (['categories', 'products', 'releases', 'orders', 'customers', 'coupons', 'reviews'] as $page) {
            $this->actingAs($staff)->get('/admin/' . $page)
                ->assertOk()
                ->assertSee('datatable-toolbar', false)
                ->assertSee('pagination-wrap', false);
        }
    }

    public function test_pagination_footer_shows_entry_counts(): void
    {
        Category::factory()->count(25)->create();

        $this->actingAs($this->staff())->get('/admin/categories')
            ->assertOk()
            ->assertSee('Showing', false)
            ->assertSee('of <strong>25</strong>', false);
    }
}
