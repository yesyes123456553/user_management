@extends('Layout.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Add Student</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>

    @include('partials.alerts')

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('students.store') }}" method="POST">
                @include('Students._form')

                <button type="submit" class="btn btn-primary">Save Student</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection