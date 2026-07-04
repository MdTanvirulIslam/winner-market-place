<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_includes_revenue_chart_data(): void
    {
        Order::factory()->delivered()->create(['amount' => 5000, 'paid_at' => now()]);
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/admin')
            ->assertOk()
            ->assertSee('__adminChart', false)
            ->assertSee('Revenue Overview')
            ->assertSee('Best Sellers')
            ->assertSee('data-chart-range="monthly"', false);
    }

    public function test_notifications_dropdown_shows_real_events(): void
    {
        $pending = Order::factory()->create();
        Order::factory()->paid()->create(['provisioning_status' => 'failed']);
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/admin')->assertOk();

        $response->assertSee('New order ' . $pending->order_no, false);
        $response->assertSee('Provisioning failed');
        // Badge counts pending + failed = 2
        $response->assertSee('badge-count', false);
    }

    public function test_notifications_show_empty_state_when_quiet(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/admin')
            ->assertOk()
            ->assertSee('All caught up');
    }

    public function test_delete_still_works_through_the_modal_form(): void
    {
        // The modal only changed the trigger — the form action is identical.
        $category = \App\Models\Category::factory()->create();
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/admin/categories')
            ->assertOk()
            ->assertSee('data-modal-open="delete-category-' . $category->id . '"', false);

        $this->actingAs($staff)->delete(route('admin.categories.destroy', $category));
        $this->assertNull($category->fresh());
    }
}
