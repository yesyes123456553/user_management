@extends('Layout.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Add Employee</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>

    @include('partials.alerts')

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('employees.store') }}" method="POST">
                @include('Employees._form')

                <button type="submit" class="btn btn-primary">Save Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection