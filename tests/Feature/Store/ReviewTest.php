<?php

namespace Tests\Feature\Store;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private function buyer(Product $product): User
    {
        $buyer = User::factory()->create(['role' => 'customer']);
        Order::factory()->delivered()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);

        return $buyer;
    }

    public function test_verified_buyer_can_submit_a_review_that_awaits_moderation(): void
    {
        $product = Product::factory()->published()->create();
        $buyer = $this->buyer($product);

        $this->actingAs($buyer)->post('/products/' . $product->slug . '/reviews', [
            'rating' => 5,
            'body' => 'Excellent product, saved us weeks of work.',
        ])->assertSessionHas('success');

        $review = Review::first();
        $this->assertSame('pending', $review->status);
        $this->assertSame(5, $review->rating);

        // Pending reviews are not shown publicly.
        $this->get('/products/' . $product->slug)
            ->assertOk()
            ->assertDontSee('Excellent product, saved us weeks of work.');
    }

    public function test_non_buyers_cannot_review(): void
    {
        $product = Product::factory()->published()->create();
        $stranger = User::factory()->create(['role' => 'customer']);

        $this->actingAs($stranger)->post('/products/' . $product->slug . '/reviews', [
            'rating' => 5,
            'body' => 'I never bought this but it looks nice.',
        ])->assertForbidden();
    }

    public function test_a_paid_but_undelivered_order_does_not_qualify(): void
    {
        $product = Product::factory()->published()->create();
        $customer = User::factory()->create(['role' => 'customer']);
        Order::factory()->paid()->create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($customer)->post('/products/' . $product->slug . '/reviews', [
            'rating' => 4,
            'body' => 'Cannot wait to actually receive it!',
        ])->assertForbidden();
    }

    public function test_a_buyer_can_only_review_once(): void
    {
        $product = Product::factory()->published()->create();
        $buyer = $this->buyer($product);
        Review::factory()->create(['product_id' => $product->id, 'user_id' => $buyer->id]);

        $this->actingAs($buyer)->post('/products/' . $product->slug . '/reviews', [
            'rating' => 1,
            'body' => 'Changed my mind, writing another one.',
        ]);

        $this->assertSame(1, Review::count());
    }

    public function test_approved_reviews_appear_with_average_rating(): void
    {
        $product = Product::factory()->published()->create();
        Review::factory()->approved()->create([
            'product_id' => $product->id,
            'rating' => 4,
            'body' => 'Solid and reliable software.',
        ]);
        Review::factory()->approved()->create([
            'product_id' => $product->id,
            'rating' => 5,
            'body' => 'Support answered within the hour.',
        ]);

        $this->get('/products/' . $product->slug)
            ->assertOk()
            ->assertSee('Solid and reliable software.')
            ->assertSee('Support answered within the hour.')
            ->assertSee('4.5');
    }

    public function test_guests_are_sent_to_login(): void
    {
        $product = Product::factory()->published()->create();

        $this->post('/products/' . $product->slug . '/reviews', [
            'rating' => 5,
            'body' => 'A guest walks into a review form…',
        ])->assertRedirect('/login');
    }
}
