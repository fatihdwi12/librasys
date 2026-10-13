<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LibraSys')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">LibraSys</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('books') ? 'active' : '' }}" href="/books">Books</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('categories') ? 'active' : '' }}" href="/categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('members') ? 'active' : '' }}" href="/members">Members</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('loans') ? 'active' : '' }}" href="/loans">Loans</a></li>
                </ul>
                <span class="navbar-text me-3" id="user-name"></span>
                <button class="btn btn-outline-light btn-sm" id="logout-btn">Logout</button>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div id="alert"></div>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        requireAuth();
        document.getElementById('logout-btn').addEventListener('click', logout);
        apiFetch('/me')
            .then((response) => {
                document.getElementById('user-name').textContent = response.data.name;
            })
            .catch(() => {});
    </script>
    @stack('scripts')
</body>
</html>