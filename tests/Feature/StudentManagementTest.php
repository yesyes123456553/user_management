<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeUserWithRole(string $roleName): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', $roleName)->value('id'),
        ]);
    }

    public function test_admin_can_search_deleted_students_and_restore_their_data(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $department = Department::factory()->create();
        $student = Student::factory()->create([
            'student_id' => 'STU-RESTORE',
            'dept_id' => $department->id,
            'course' => 'Computer Science',
        ]);
        Student::factory()->create(['student_id' => 'STU-OTHER']);

        $this->actingAs($admin)
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'is_deleted' => 1,
            'dept_id' => $department->id,
            'course' => 'Computer Science',
        ]);

        $this->get(route('students.index', ['deleted' => 1, 'q' => 'STU-RESTORE']))
            ->assertOk()
            ->assertSee('STU-RESTORE')
            ->assertDontSee('STU-OTHER')
            ->assertSee('Restore');

        $this->patch(route('students.restore', $student->id))
            ->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'is_deleted' => 0,
            'dept_id' => $department->id,
            'course' => 'Computer Science',
            'student_id' => 'STU-RESTORE',
        ]);

        $restoredStudent = Student::with('department')->findOrFail($student->id);

        $this->assertSame($department->id, $restoredStudent->department->id);

        $this->get(route('students.index'))
            ->assertOk()
            ->assertSee('STU-RESTORE');
    }

    public function test_super_user_cannot_restore_a_deleted_student(): void
    {
        $student = Student::factory()->create();
        $student->delete();

        $this->actingAs($this->makeUserWithRole('super_user'))
            ->patch(route('students.restore', $student->id))
            ->assertForbidden();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'is_deleted' => 1,
        ]);
    }

    public function test_active_student_cannot_be_restored(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($this->makeUserWithRole('admin'))
            ->patch(route('students.restore', $student->id))
            ->assertNotFound();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'is_deleted' => 0,
        ]);
    }
}
