<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserWithRole(string $roleName, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role_id' => Role::where('name', $roleName)->value('id'),
        ], $attributes));
    }

    public function test_admin_can_open_the_user_list(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('User Management');
    }

    public function test_regular_user_cannot_open_user_management(): void
    {
        $user = $this->makeUserWithRole('user');

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_user_with_a_hashed_password(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'New Person',
                'email' => 'new.person@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'role_id' => Role::where('name', 'user')->value('id'),
                'status' => 1,
            ])
            ->assertRedirect(route('users.index'));

        $created = User::where('email', 'new.person@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('secret123', $created->password));
        $this->assertTrue($created->status);
        $this->assertNotNull($created->email_verified_at);
    }

    public function test_admin_can_update_a_user(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('user');

        $this->actingAs($admin)
            ->put(route('users.update', $target), [
                'name' => 'Renamed',
                'email' => $target->email,
                'role_id' => $target->role_id,
                'status' => 1,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertSame('Renamed', $target->fresh()->name);
    }

    public function test_deleting_a_user_sets_is_deleted_to_one_and_keeps_the_row(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('user');

        $this->actingAs($admin)
            ->delete(route('users.destroy', $target))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_deleted' => 1,
        ]);

        $this->assertNull(User::find($target->id));
        $this->assertNotNull(User::withDeletedRecords()->find($target->id));

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertDontSee($target->email);
    }

    public function test_a_deleted_user_cannot_log_in(): void
    {
        $target = $this->makeUserWithRole('user', [
            'password' => Hash::make('Admin@12345'),
        ]);

        $target->delete();

        $this->from(route('login'))
            ->post(route('process_login'), [
                'email' => $target->email,
                'password' => 'Admin@12345',
            ])
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_can_view_and_restore_a_deleted_user(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('user');
        $roleId = $target->role_id;
        $target->delete();

        $this->actingAs($admin)
            ->get(route('users.index', ['deleted' => 1]))
            ->assertOk()
            ->assertSee($target->email)
            ->assertSee('Restore');

        $this->patch(route('users.restore', $target->id))
            ->assertRedirect(route('users.index', ['deleted' => 1]));

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_deleted' => 0,
            'role_id' => $roleId,
            'email' => $target->email,
        ]);

        $this->get(route('users.index'))
            ->assertOk()
            ->assertSee($target->email);
    }

    public function test_super_user_cannot_restore_a_deleted_user(): void
    {
        $target = $this->makeUserWithRole('user');
        $target->delete();

        $this->actingAs($this->makeUserWithRole('super_user'))
            ->patch(route('users.restore', $target->id))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_deleted' => 1,
        ]);
    }

    public function test_admin_cannot_restore_a_super_admin_account(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('super_admin');
        $target->delete();

        $this->actingAs($admin)
            ->patch(route('users.restore', $target->id))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_deleted' => 1,
        ]);
    }
}
