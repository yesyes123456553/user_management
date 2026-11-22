<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_the_sb_admin_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('backend/css/styles.css')
            ->assertSee(route('process_login'))
            ->assertSee('Remember Me')
            ->assertDontSee('sidenavAccordion');
    }

    public function test_active_user_can_log_in_and_remember_their_session(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Admin@12345'),
            'remember_token' => null,
        ]);

        $this->post(route('process_login'), [
            'email' => $user->email,
            'password' => 'Admin@12345',
            'remember' => '1',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_invalid_credentials_are_rejected_and_email_is_preserved(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('process_login'), [
                'email' => $user->email,
                'password' => 'incorrect',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('danger')
            ->assertSessionHas('_old_input.email', $user->email);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Admin@12345'),
            'status' => 0,
        ]);

        $this->from(route('login'))
            ->post(route('process_login'), [
                'email' => $user->email,
                'password' => 'Admin@12345',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('danger');

        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication_and_logout_ends_the_session(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('backend/css/styles.css')
            ->assertSee('Primary Card');

        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertGuest();
    }

    public function test_database_seeder_creates_the_hashed_administrator_account(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Administrator', $admin->name);
        $this->assertSame(1, (int) $admin->status);
        $this->assertTrue(Hash::check('Admin@12345', $admin->password));
    }
}
