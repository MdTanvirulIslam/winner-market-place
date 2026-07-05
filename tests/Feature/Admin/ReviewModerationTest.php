<?php

namespace Tests\Feature\Admin;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    public function test_customers_cannot_access_review_moderation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin/reviews')->assertForbidden();
    }

    public function test_moderation_queue_lists_and_filters_reviews(): void
    {
        Review::factory()->create(['body' => 'A pending opinion about things.']);
        Review::factory()->approved()->create(['body' => 'An approved verdict on stuff.']);

        $this->actingAs($this->staff())->get('/admin/reviews')
            ->assertOk()
            ->assertSee('A pending opinion about things.')
            ->assertSee('An approved verdict on stuff.');

        $this->actingAs($this->staff())->get('/admin/reviews?status=pending')
            ->assertOk()
            ->assertSee('A pending opinion about things.')
            ->assertDontSee('An approved verdict on stuff.');
    }

    public function test_staff_can_approve_reject_and_delete_reviews(): void
    {
        $review = Review::factory()->create();

        $this->actingAs($this->staff())->post(route('admin.reviews.approve', $review));
        $this->assertSame('approved', $review->fresh()->status);

        $this->actingAs($this->staff())->post(route('admin.reviews.reject', $review));
        $this->assertSame('rejected', $review->fresh()->status);

        $this->actingAs($this->staff())->delete(route('admin.reviews.destroy', $review));
        $this->assertNull($review->fresh());
    }
}
