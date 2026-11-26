<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    private function rules(?Student $student = null): array
    {
        return [
            'student_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'student_id')->ignore($student?->id),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student?->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'dept_id' => ['nullable', 'integer', 'exists:departments,id'],
            'course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:4'],
        ];
    }

    // READ
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $showDeleted = $request->boolean('deleted');

        $query = $showDeleted ? Student::onlyDeleted() : Student::query();

        $students = $query
            ->with('department')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_id', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('course', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($query) use ($search) {
                            $query->where('college_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('Students.index', [
            'students' => $students,
            'search' => $search,
            'showDeleted' => $showDeleted,
        ]);
    }

    // CREATE FORM
    public function create(): View
    {
        return view('Students.create', [
            'departments' => Department::orderBy('college_name')->get(),
        ]);
    }

    // STORE
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $data['status'] = $request->boolean('status');

        Student::create($data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    // EDIT FORM
    public function edit(Student $student): View
    {
        return view('Students.edit', [
            'student' => $student,
            'departments' => Department::orderBy('college_name')->get(),
        ]);
    }

    // UPDATE
    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate($this->rules($student));

        $data['status'] = $request->boolean('status');

        $student->update($data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    // DELETE (soft delete: is_deleted becomes 1, the row stays in MySQL)
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $student = Student::onlyDeleted()->findOrFail($id);

        $student->restoreDeleted();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student restored successfully.');
    }
}
