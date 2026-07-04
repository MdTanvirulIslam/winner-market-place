<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    public function test_settings_page_shows_integration_status_without_credential_values(): void
    {
        // Self-invented test values — never real credentials.
        config([
            'marketplace.license_manager.token' => 'test-secret-token-abc123',
            'marketplace.sslcommerz.store_password' => 'test-store-pass-xyz789',
        ]);

        $response = $this->actingAs($this->superAdmin())->get('/admin/settings');

        $response->assertOk()
            ->assertSee('Configured')
            ->assertDontSee('test-secret-token-abc123')
            ->assertDontSee('test-store-pass-xyz789');
    }

    public function test_settings_page_reports_unconfigured_integrations(): void
    {
        config([
            'marketplace.license_manager.url' => null,
            'marketplace.license_manager.token' => null,
        ]);

        $this->actingAs($this->superAdmin())->get('/admin/settings')
            ->assertOk()
            ->assertSee('Not set');
    }

    public function test_store_settings_can_be_updated(): void
    {
        $response = $this->actingAs($this->superAdmin())->patch('/admin/settings', [
            'store_name' => 'Winner Store',
            'support_email' => 'support@winnerdevs.com',
            'currency' => 'BDT',
        ]);

        $response->assertRedirect(route('admin.settings.edit'));

        $setting = Setting::current();
        $this->assertSame('Winner Store', $setting->store_name);
        $this->assertSame('support@winnerdevs.com', $setting->support_email);
    }

    public function test_admin_users_crud_respects_self_guards(): void
    {
        $superAdmin = $this->superAdmin();

        // Create a staff member.
        $this->actingAs($superAdmin)->post('/admin/users', [
            'name' => 'Staff Member',
            'email' => 'staff@winnerdevs.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
        ])->assertRedirect(route('admin.users.index'));

        $staff = User::where('email', 'staff@winnerdevs.com')->first();
        $this->assertSame('staff', $staff->role);

        // A super admin cannot delete their own account.
        $this->actingAs($superAdmin)
            ->delete(route('admin.users.destroy', $superAdmin));
        $this->assertNotNull($superAdmin->fresh());

        // A super admin cannot demote themselves.
        $this->actingAs($superAdmin)->patch(route('admin.users.update', $superAdmin), [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'role' => 'staff',
        ]);
        $this->assertSame('super_admin', $superAdmin->fresh()->role);

        // Deleting someone else works.
        $this->actingAs($superAdmin)->delete(route('admin.users.destroy', $staff));
        $this->assertNull($staff->fresh());
    }
}
