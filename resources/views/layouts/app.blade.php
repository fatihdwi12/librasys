<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LibraSys')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #000000;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #1e1e1e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #2d2d2d;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 24px 28px 16px;
            border-bottom: 1px solid #2d2d2d;
        }

        .sidebar-brand h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
        }

        .sidebar-brand p {
            font-size: 0.85rem;
            color: #a0a0a0;
            margin: 0;
        }

        .sidebar-menu {
            padding: 20px 16px;
            flex-grow: 1;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #a0a0a0;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link-custom i {
            font-size: 1.2rem;
        }

        /* Main Layout Content Wrapper */
        .main-wrapper {
            margin-left: 250px;
            padding: 36px 40px;
            min-height: 100vh;
            background-color: #000000;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div>
            <!-- Brand Logo / Title -->
            <div class="sidebar-brand">
                <h2>Librasys</h2>
                <p>Admin Panel</p>
            </div>

            <!-- Menu List -->
            <div class="sidebar-menu">
                <a href="/dashboard" class="nav-link-custom {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/books" class="nav-link-custom {{ request()->is('books*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Books</span>
                </a>
                <a href="/members" class="nav-link-custom {{ request()->is('members*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Member</span>
                </a>
                <a href="/loans" class="nav-link-custom {{ request()->is('loans*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Active Loan</span>
                </a>
                <a href="/categories"
                    class="nav-link-custom {{ request()->is('categories*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                <a href="/available-books" class="nav-link-custom {{ request()->is('available-books*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Available Books</span>
                </a>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="p-3">
            <a href="#" id="logout-btn" class="nav-link-custom text-secondary">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

        <!-- Main Content Area -->
    <main class="main-wrapper">
        <div id="alert"></div>
        @yield('content')
    </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        // Hanya admin yang boleh membuka halaman yang memakai layout ini
        requireRole('admin');

        document.getElementById('logout-btn').addEventListener('click', (event) => {
            event.preventDefault();
            logout();
        });
    </script>
    @stack('scripts')
</body>
</html>