@extends('Layout.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Employee Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Employees</li>
    </ol>

    @include('partials.alerts')

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>
                <i class="fas fa-id-badge me-1"></i>
                {{ $showDeleted ? 'Deleted Employees' : 'All Employees' }}
            </span>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('employees.index') }}"
                   class="btn {{ $showDeleted ? 'btn-outline-secondary' : 'btn-secondary' }} btn-sm">
                    Show Active Employees
                </a>
                <a href="{{ route('employees.index', ['deleted' => 1]) }}"
                   class="btn {{ $showDeleted ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm">
                    Show Deleted Employees
                </a>

                <form action="{{ route('employees.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                           placeholder="Search code, name, email, department">
                    @if($showDeleted)
                        <input type="hidden" name="deleted" value="1">
                    @endif
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                </form>

                @unless($showDeleted)
                    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Employee
                    </a>
                @endunless
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->employee_code }}</td>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->department ?? '-' }}</td>
                                <td>{{ $employee->position ?? '-' }}</td>
                                <td>{{ $employee->hire_date?->format('M d, Y') ?? '-' }}</td>
                                <td>
                                    @if($employee->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($showDeleted)
                                        <form action="{{ route('employees.restore', $employee->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Restore this employee?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-trash-restore"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('employees.destroy', $employee) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    {{ $showDeleted ? 'No deleted employees found.' : 'No employees found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection