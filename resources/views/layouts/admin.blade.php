<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        <h3>Admin Panel</h3>
        <a href="{{ route('admin.bills.approval.index') }}">Approvals</a>
        <a href="{{ route('logout') }}">Logout</a>
    </nav>

    <main class="container mt-4">
        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
