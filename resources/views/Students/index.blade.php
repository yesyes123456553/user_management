@extends('Layout.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Student Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Students</li>
    </ol>

    @include('partials.alerts')

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>
                <i class="fas fa-user-graduate me-1"></i>
                {{ $showDeleted ? 'Deleted Students' : 'All Students' }}
            </span>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('students.index') }}"
                   class="btn {{ $showDeleted ? 'btn-outline-secondary' : 'btn-secondary' }} btn-sm">
                    Show Active Students
                </a>
                <a href="{{ route('students.index', ['deleted' => 1]) }}"
                   class="btn {{ $showDeleted ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm">
                    Show Deleted Students
                </a>

                <form action="{{ route('students.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                           placeholder="Search ID, name, email, college">
                    @if($showDeleted)
                        <input type="hidden" name="deleted" value="1">
                    @endif
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                </form>

                @unless($showDeleted)
                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Student
                    </a>
                @endunless
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>College</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->department?->college_name ?? '-' }}</td>
                                <td>{{ $student->course ?? '-' }}</td>
                                <td>{{ $student->year_level ?? '-' }}</td>
                                <td>
                                    @if($student->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($showDeleted)
                                        <form action="{{ route('students.restore', $student->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Restore this student?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-trash-restore"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('students.destroy', $student) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete this student?');">
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
                                    {{ $showDeleted ? 'No deleted students found.' : 'No students found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection