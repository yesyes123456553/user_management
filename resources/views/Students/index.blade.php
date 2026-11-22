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
            <span><i class="fas fa-user-graduate me-1"></i> All Students</span>

            <div class="d-flex gap-2">
                <form action="{{ route('students.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                           placeholder="Search ID, name, email, college">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                </form>

                <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add Student
                </a>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No students found.</td>
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