<!DOCTYPE html>
<html lang="en">
@include('partials.header', ['title' => 'Login | User Management System'])

<body class="bg-primary">

    <div id="layoutAuthentication">

        <div id="layoutAuthentication_content">

            <main>
                <div class="container">

                    <div class="row justify-content-center">

                        <div class="col-11 col-sm-9 col-md-7 col-lg-5">

                            <div class="card shadow-lg border-0 rounded-lg mt-5">

                                <div class="card-header text-center">

                                    <h3 class="fw-bold my-3">
                                        <i class="fas fa-user-shield me-2"></i>
                                        Login
                                    </h3>

                                    <p class="text-muted mb-2">
                                        User Management System
                                    </p>

                                </div>

                                <div class="card-body">

                                    {{-- Success Message --}}
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show">
                                            {{ session('success') }}

                                            <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="alert">
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Login Error --}}
                                    @if(session('danger'))
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            {{ session('danger') }}

                                            <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="alert">
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Validation Errors --}}
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form action="{{ route('process_login') }}" method="POST">

                                        @csrf

                                        {{-- Email --}}
                                        <div class="form-floating mb-3">

                                            <input
                                                type="email"
                                                name="email"
                                                id="email"
                                                class="form-control"
                                                placeholder="name@example.com"
                                                value="{{ old('email') }}"
                                                autocomplete="username"
                                                required
                                                autofocus>

                                            <label for="email">
                                                Email Address
                                            </label>

                                        </div>

                                        {{-- Password --}}
                                        <div class="form-floating mb-3">

                                            <input
                                                type="password"
                                                name="password"
                                                id="password"
                                                class="form-control"
                                                placeholder="Password"
                                                autocomplete="current-password"
                                                required>

                                            <label for="password">
                                                Password
                                            </label>

                                        </div>

                                        {{-- Remember Me --}}
                                        <div class="form-check mb-3">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="remember"
                                                id="remember"
                                                value="1"
                                                @checked(old('remember'))>

                                            <label class="form-check-label"
                                                   for="remember">
                                                Remember Me
                                            </label>

                                        </div>

                                        {{-- Login Button --}}
                                        <div class="d-grid">

                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-lg">

                                                <i class="fas fa-sign-in-alt me-2"></i>
                                                Login

                                            </button>

                                        </div>

                                    </form>

                                </div>

                                <div class="card-footer text-center py-3">

                                    <div class="small text-muted">
                                        User Management System
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </main>

        </div>

    </div>

    @include('partials.scripts')

</body>
</html>