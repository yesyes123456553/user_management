<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserWithRole(string $roleName): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $roleName)->value('id'),
        ]);
    }

    public function test_regular_user_cannot_open_employee_management(): void
    {
        $this->actingAs($this->makeUserWithRole('user'))
            ->get(route('employees.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('employees.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_list_employees(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->makeUserWithRole('admin'))
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSee($employee->employee_code);
    }

    public function test_admin_can_create_an_employee(): void
    {
        $this->actingAs($this->makeUserWithRole('admin'))
            ->post(route('employees.store'), [
                'employee_code' => 'EMP-0001',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'department' => 'IT',
                'position' => 'Developer',
                'hire_date' => '2026-01-15',
                'status' => 1,
            ])
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('employees', [
            'employee_code' => 'EMP-0001',
            'email' => 'juan@example.com',
            'is_deleted' => 0,
        ]);
    }

    public function test_employee_code_must_be_unique(): void
    {
        Employee::factory()->create(['employee_code' => 'EMP-0001']);

        $this->actingAs($this->makeUserWithRole('admin'))
            ->post(route('employees.store'), [
                'employee_code' => 'EMP-0001',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria@example.com',
            ])
            ->assertSessionHasErrors('employee_code');
    }

    public function test_admin_can_update_an_employee(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->makeUserWithRole('super_user'))
            ->put(route('employees.update', $employee), [
                'employee_code' => $employee->employee_code,
                'first_name' => 'Updated',
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'status' => 1,
            ])
            ->assertRedirect(route('employees.index'));

        $this->assertSame('Updated', $employee->fresh()->first_name);
    }

    public function test_deleting_an_employee_sets_is_deleted_to_one_and_keeps_the_row(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->makeUserWithRole('admin'))
            ->delete(route('employees.destroy', $employee))
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'is_deleted' => 1,
        ]);

        $this->assertNull(Employee::find($employee->id));

        $this->actingAs($this->makeUserWithRole('admin'))
            ->get(route('employees.index'))
            ->assertDontSee($employee->employee_code);
    }

    public function test_admin_can_search_deleted_employees_and_restore_their_data(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $department = Department::factory()->create();
        $employee = Employee::factory()->create([
            'employee_code' => 'EMP-RESTORE',
            'dept_id' => $department->id,
            'department' => $department->college_name,
        ]);
        Employee::factory()->create(['employee_code' => 'EMP-OTHER']);

        $this->actingAs($admin)
            ->delete(route('employees.destroy', $employee))
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'is_deleted' => 1,
            'dept_id' => $department->id,
            'department' => $department->college_name,
        ]);

        $this->get(route('employees.index', ['deleted' => 1, 'q' => 'EMP-RESTORE']))
            ->assertOk()
            ->assertSee('EMP-RESTORE')
            ->assertDontSee('EMP-OTHER')
            ->assertSee('Restore');

        $this->patch(route('employees.restore', $employee->id))
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'is_deleted' => 0,
            'dept_id' => $department->id,
            'department' => $department->college_name,
            'employee_code' => 'EMP-RESTORE',
        ]);

        $this->get(route('employees.index'))
            ->assertOk()
            ->assertSee('EMP-RESTORE');
    }

    public function test_super_user_cannot_restore_an_employee(): void
    {
        $employee = Employee::factory()->create();
        $employee->delete();

        $this->actingAs($this->makeUserWithRole('super_user'))
            ->patch(route('employees.restore', $employee->id))
            ->assertForbidden();

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'is_deleted' => 1,
        ]);
    }

    public function test_active_employee_cannot_be_restored(): void
    {
        $employee = Employee::factory()->create();

        $this->actingAs($this->makeUserWithRole('admin'))
            ->patch(route('employees.restore', $employee->id))
            ->assertNotFound();

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'is_deleted' => 0,
        ]);
    }
}
