<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Member Area - LibraSys')</title>
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

        /* Sidebar */
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
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link-custom i {
            font-size: 1.2rem;
        }

        /* Content */
        .main-wrapper {
            margin-left: 250px;
            padding: 36px 40px;
            min-height: 100vh;
            background-color: #000000;
        }

        .card-dark {
            background-color: #1f1f1f;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 24px;
        }

        .profile-box {
            background-color: #141414;
            border: 1px solid #333333;
            border-radius: 8px;
            padding: 10px 14px;
            color: #ffffff;
            font-size: 0.95rem;
            min-height: 42px;
            display: flex;
            align-items: center;
        }

        .stat-card {
            background-color: #1f1f1f;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 24px 28px;
            max-width: 300px;
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cccccc;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .stat-number {
            font-size: 3.2rem;
            font-weight: 500;
            color: #ffffff;
            line-height: 1;
        }

        /* Tabel dan input gelap */
        .table-dark-custom {
            color: #e0e0e0;
        }

        .table-dark-custom th {
            background-color: #181818;
            color: #a0a0a0;
            border-bottom: 1px solid #333333;
            font-weight: 600;
        }

        .table-dark-custom td {
            border-bottom: 1px solid #2a2a2a;
            background-color: #1f1f1f;
            color: #ffffff;
        }

        .form-control-dark {
            background-color: #141414;
            border: 1px solid #333333;
            color: #ffffff;
            border-radius: 8px;
        }

        .form-control-dark:focus {
            background-color: #181818;
            border-color: #555555;
            color: #ffffff;
            box-shadow: none;
        }

        /* Kartu buku */
        .book-card {
            background-color: #1f1f1f;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 20px;
            transition: border-color 0.2s ease, transform 0.2s ease;
        }

        .book-card:hover {
            border-color: #555555;
            transform: translateY(-2px);
        }

        .book-title {
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
        }

        .filter-btn.active {
            background-color: #ffffff;
            color: #000000;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <h2>Librasys</h2>
                <p>Member Panel</p>
            </div>

            <div class="sidebar-menu">
                <a href="/member" class="nav-link-custom {{ request()->is('member') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/member/books" class="nav-link-custom {{ request()->is('member/books*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Books</span>
                </a>
                <a href="/member/loans" class="nav-link-custom {{ request()->is('member/loans*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>My Loan</span>
                </a>
            </div>
        </div>

        <div class="p-3 border-top border-secondary-subtle">
            <div class="d-flex align-items-center mb-2 px-2">
                <i class="bi bi-person-circle text-secondary me-2 fs-5"></i>
                <span class="text-light small" id="user-name">-</span>
            </div>
            <button class="nav-link-custom text-secondary" id="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-wrapper">
        <div class="mb-4">
            <h1 class="fw-bold mb-1" style="font-size: 2.2rem; color: #ffffff;">@yield('heading')</h1>
            <p class="text-secondary" style="font-size: 0.95rem;">@yield('subheading')</p>
        </div>

        <div id="alert"></div>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        requireRole('member');
        document.getElementById('logout-btn').addEventListener('click', logout);

        // Profil diminta sekali, lalu dipakai ulang oleh sidebar dan halaman.
        window.currentUser = apiFetch('/me').then((response) => response.data);
        window.currentUser
            .then((user) => {
                document.getElementById('user-name').textContent = user.name;
            })
            .catch(() => {});
    </script>
    @stack('scripts')
</body>
</html>