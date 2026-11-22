
<!DOCTYPE html>
<html lang="en">
@include('partials.header', ['title' => 'Dashboard | User Management System'])

<body class="sb-nav-fixed">
    @include('partials.navbar')

    <div id="layoutSidenav">
        @include('partials.sidebar')

        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
