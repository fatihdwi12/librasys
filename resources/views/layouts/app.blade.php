<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LibraSys')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #000000;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #282828;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        /* Logo / Brand Header */
        .sidebar-brand {
            padding: 32px 28px 24px;
            border-bottom: 1px solid #282828;
        }

        .sidebar-brand h2 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0;
            color: #ffffff;
            line-height: 1.2;
        }

        .sidebar-brand p {
            font-size: 0.875rem;
            color: #8e8e93;
            margin-top: 4px;
            margin-bottom: 0;
            font-weight: 400;
        }

        /* Menu List */
        .sidebar-menu {
            padding: 24px 16px;
            flex-grow: 1;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #8e8e93;
            padding: 12px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 6px;
            transition: all 0.2s ease-in-out;
        }

        .nav-link-custom:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
        }

        .nav-link-custom.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }

        .nav-link-custom i {
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: 260px;
            padding: 40px;
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
                <a href="/returns" class="nav-link-custom {{ request()->is('returns*') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down-left"></i>
                    <span>Pengembalian</span>
                </a>
                <a href="/categories" class="nav-link-custom {{ request()->is('categories*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Categories</span>
                </a>
                
                <a href="{{ route('account.password') }}" class="nav-link-custom {{ request()->routeIs('account.password') ? 'active' : '' }}">
                    <i class="bi bi-key"></i>
                    <span>Ubah Kata Sandi</span>
                </a>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="p-3 border-top border-dark-subtle">
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
        requireRole('admin');

        document.getElementById('logout-btn').addEventListener('click', (event) => {
            event.preventDefault();
            logout();
        });
    </script>
    @stack('scripts')
</body>
</html>