<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private function rules(?Employee $employee = null): array
    {
        return [
            'employee_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_code')->ignore($employee?->id),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee?->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
        ];
    }

    // READ
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $showDeleted = $request->boolean('deleted');

        $query = $showDeleted ? Employee::onlyDeleted() : Employee::query();

        $employees = $query
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('employee_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('Employees.index', [
            'employees' => $employees,
            'search' => $search,
            'showDeleted' => $showDeleted,
        ]);
    }

    // CREATE FORM
    public function create(): View
    {
        return view('Employees.create');
    }

    // STORE
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $data['status'] = $request->boolean('status');

        Employee::create($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    // EDIT FORM
    public function edit(Employee $employee): View
    {
        return view('Employees.edit', [
            'employee' => $employee,
        ]);
    }

    // UPDATE
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate($this->rules($employee));

        $data['status'] = $request->boolean('status');

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    // DELETE (soft delete: is_deleted becomes 1, the row stays in MySQL)
    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $employee = Employee::onlyDeleted()->findOrFail($id);

        $employee->restoreDeleted();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee restored successfully.');
    }
}
