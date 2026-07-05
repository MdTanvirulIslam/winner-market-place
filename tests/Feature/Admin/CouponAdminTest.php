<?php

namespace Tests\Feature\Admin;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponAdminTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    public function test_customers_cannot_access_coupon_admin(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin/coupons')->assertForbidden();
    }

    public function test_staff_can_create_a_coupon_with_uppercased_code(): void
    {
        $this->actingAs($this->staff())->post('/admin/coupons', [
            'code' => 'launch20',
            'type' => 'percent',
            'value' => 20,
            'active' => 1,
        ])->assertRedirect(route('admin.coupons.index'));

        $coupon = Coupon::first();
        $this->assertSame('LAUNCH20', $coupon->code);
        $this->assertTrue($coupon->isRedeemable());
    }

    public function test_percent_coupons_cannot_exceed_100(): void
    {
        $this->actingAs($this->staff())->post('/admin/coupons', [
            'code' => 'TOOMUCH',
            'type' => 'percent',
            'value' => 150,
            'active' => 1,
        ])->assertSessionHasErrors('value');
    }

    public function test_staff_can_update_and_delete_a_coupon(): void
    {
        $coupon = Coupon::factory()->create(['code' => 'SAVE10']);

        $this->actingAs($this->staff())->patch(route('admin.coupons.update', $coupon), [
            'code' => 'SAVE10',
            'type' => 'fixed',
            'value' => 500,
            // no 'active' checkbox → disabled
        ])->assertRedirect(route('admin.coupons.index'));

        $coupon->refresh();
        $this->assertSame('fixed', $coupon->type);
        $this->assertFalse($coupon->active);
        $this->assertFalse($coupon->isRedeemable());

        $this->actingAs($this->staff())->delete(route('admin.coupons.destroy', $coupon));
        $this->assertNull($coupon->fresh());
    }

    public function test_coupon_states_are_reported(): void
    {
        $expired = Coupon::factory()->create(['expires_at' => now()->subDay()]);
        $exhausted = Coupon::factory()->create(['max_uses' => 1, 'used_count' => 1]);

        $this->assertFalse($expired->isRedeemable());
        $this->assertFalse($exhausted->isRedeemable());
        $this->assertStringContainsString('expired', $expired->rejectionReason());
        $this->assertStringContainsString('limit', $exhausted->rejectionReason());
    }
}
