<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_customers_cannot_access_the_admin_panel(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin')->assertForbidden();
        $this->actingAs($customer)->get('/admin/settings')->assertForbidden();
        $this->actingAs($customer)->get('/admin/users')->assertForbidden();
    }

    public function test_staff_can_view_the_dashboard_but_not_super_admin_pages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/admin')->assertOk();
        $this->actingAs($staff)->get('/admin/settings')->assertForbidden();
        $this->actingAs($staff)->get('/admin/users')->assertForbidden();
    }

    public function test_super_admins_can_access_everything(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->get('/admin')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/settings')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/users')->assertOk();
    }

    public function test_staff_do_not_see_super_admin_navigation(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/admin')
            ->assertOk()
            ->assertDontSee('Admin Users');
    }

    public function test_registration_creates_customer_accounts_only(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky User',
            'email' => 'sneaky@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'super_admin', // must be ignored
        ]);

        $this->assertSame('customer', User::where('email', 'sneaky@example.com')->first()->role);
    }
}
