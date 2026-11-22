@extends('Layout.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Add User</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>

    @include('partials.alerts')

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @include('Users._form')

                <button type="submit" class="btn btn-primary">Save User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection