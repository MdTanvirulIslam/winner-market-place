<?php

namespace Tests\Feature\Store;

use App\Models\Category;
use App\Models\Product;
use App\Models\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_published_products_only(): void
    {
        $published = Product::factory()->published()->create(['name' => 'Visible Product']);
        Product::factory()->create(['name' => 'Hidden Draft Product']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible Product')
            ->assertDontSee('Hidden Draft Product');
    }

    public function test_listing_shows_published_products_only(): void
    {
        Product::factory()->published()->create(['name' => 'Visible Product']);
        Product::factory()->create(['name' => 'Hidden Draft Product']);

        $this->get('/products')
            ->assertOk()
            ->assertSee('Visible Product')
            ->assertDontSee('Hidden Draft Product');
    }

    public function test_listing_can_be_searched(): void
    {
        Product::factory()->published()->create(['name' => 'News Portal Pro']);
        Product::factory()->published()->create(['name' => 'POS Terminal']);

        $this->get('/products?q=News')
            ->assertOk()
            ->assertSee('News Portal Pro')
            ->assertDontSee('POS Terminal');
    }

    public function test_listing_can_be_filtered_by_category(): void
    {
        $news = Category::factory()->create(['slug' => 'news']);
        $pos = Category::factory()->create(['slug' => 'pos']);
        Product::factory()->published()->create(['name' => 'News App', 'category_id' => $news->id]);
        Product::factory()->published()->create(['name' => 'POS App', 'category_id' => $pos->id]);

        $this->get('/products?category=news')
            ->assertOk()
            ->assertSee('News App')
            ->assertDontSee('POS App');
    }

    public function test_published_product_detail_renders_with_changelog(): void
    {
        $product = Product::factory()->published()->create(['name' => 'News Portal Pro']);
        Release::factory()->create([
            'product_id' => $product->id,
            'version' => '2.1.0',
            'notes' => 'Added dark mode.',
        ]);

        $this->get('/products/' . $product->slug)
            ->assertOk()
            ->assertSee('News Portal Pro')
            ->assertSee('v2.1.0')
            ->assertSee('Added dark mode.');
    }

    public function test_draft_products_are_not_publicly_visible(): void
    {
        $draft = Product::factory()->create();

        $this->get('/products/' . $draft->slug)->assertNotFound();
    }

    public function test_sale_price_is_displayed(): void
    {
        Product::factory()->published()->create([
            'name' => 'Discounted App',
            'price' => 10000,
            'sale_price' => 7000,
        ]);

        $this->get('/products')
            ->assertOk()
            ->assertSee('SALE');
    }
}
