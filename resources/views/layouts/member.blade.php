<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Member Area - LibraSys')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #000000;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #1e1e1e;
            height: 100vh;
            overflow-y: auto;
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

        .nav-link-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .nav-link-custom i {
            font-size: 1.2rem;
        }

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

        #self-password-modal .modal-content {
            background-color: #1f1f1f;
            color: #ffffff;
            border: 1px solid #333333;
            border-radius: 12px;
        }

        #self-password-modal .modal-header,
        #self-password-modal .modal-footer {
            border-color: #333333;
        }
    </style>

    @stack('styles')
</head>

<body>
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <h2>Librasys</h2>
                <p>Member Panel</p>
            </div>

            <div class="sidebar-menu">
                <a href="/member"
                   class="nav-link-custom {{ request()->is('member') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/member/books"
                   class="nav-link-custom {{ request()->is('member/books*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Books</span>
                </a>

                <a href="/member/loans"
                   class="nav-link-custom {{ request()->is('member/loans*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>My Loan</span>
                </a>

                <button type="button"
                        id="open-member-password-btn"
                        class="nav-link-custom"
                        data-bs-toggle="modal"
                        data-bs-target="#self-password-modal">
                    <i class="bi bi-key"></i>
                    <span>Ubah Kata Sandi</span>
                </button>
            </div>
        </div>

        <div class="p-3 border-top border-secondary-subtle">
            <div class="d-flex align-items-center mb-2 px-2">
                <i class="bi bi-person-circle text-secondary me-2 fs-5"></i>
                <span class="text-light small" id="user-name">-</span>
            </div>

            <button type="button"
                    class="nav-link-custom text-secondary"
                    id="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <main class="main-wrapper">
        <div class="mb-4">
            <h1 class="fw-bold mb-1"
                style="font-size: 2.2rem; color: #ffffff;">
                @yield('heading')
            </h1>

            <p class="text-secondary" style="font-size: 0.95rem;">
                @yield('subheading')
            </p>
        </div>

        <div id="alert" aria-live="polite"></div>

        @yield('content')
    </main>

    <div class="modal fade"
         id="self-password-modal"
         tabindex="-1"
         aria-labelledby="self-password-modal-title"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="self-password-form">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="self-password-modal-title">
                            Ubah Kata Sandi Saya
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div id="self-password-error"
                             class="alert alert-danger d-none"
                             role="alert"></div>

                        <div id="self-password-success"
                             class="alert alert-success d-none"
                             role="status"></div>

                        <div class="mb-3">
                            <label for="self-current-password"
                                   class="form-label">
                                Kata Sandi Saat Ini
                            </label>

                            <input type="password"
                                   id="self-current-password"
                                   class="form-control form-control-dark"
                                   autocomplete="current-password"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="self-new-password"
                                   class="form-label">
                                Kata Sandi Baru
                            </label>

                            <input type="password"
                                   id="self-new-password"
                                   class="form-control form-control-dark"
                                   autocomplete="new-password"
                                   minlength="15"
                                   maxlength="64"
                                   aria-describedby="self-password-help"
                                   required>

                            <div id="self-password-help" class="form-text">
                                Gunakan 15–64 karakter dan berbeda
                                dari kata sandi saat ini.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="self-password-confirmation"
                                   class="form-label">
                                Konfirmasi Kata Sandi Baru
                            </label>

                            <input type="password"
                                   id="self-password-confirmation"
                                   class="form-control form-control-dark"
                                   autocomplete="new-password"
                                   minlength="15"
                                   maxlength="64"
                                   required>
                        </div>

                        <p class="small text-secondary mb-0">
                            Setelah berhasil, Anda akan diminta login kembali.
                            Perubahan ini tidak otomatis mengakhiri
                            semua sesi pada perangkat lain.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                                id="self-save-password-btn"
                                class="btn btn-light">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/api.js') }}"></script>

    <script>
    (() => {
        requireRole('member');

        if (!Auth.getToken() || Auth.getRole() !== 'member') {
            return;
        }

        const logoutButton = document.getElementById('logout-btn');
        const openPasswordButton =
            document.getElementById('open-member-password-btn');

        const passwordModalElement =
            document.getElementById('self-password-modal');

        const passwordForm =
            document.getElementById('self-password-form');

        const passwordError =
            document.getElementById('self-password-error');

        const passwordSuccess =
            document.getElementById('self-password-success');

        const savePasswordButton =
            document.getElementById('self-save-password-btn');

        let savingPassword = false;
        let passwordChanged = false;
        let loggingOut = false;

        function clearPasswordMessages() {
            passwordError.textContent = '';
            passwordError.classList.add('d-none');

            passwordSuccess.textContent = '';
            passwordSuccess.classList.add('d-none');
        }

        function displayPasswordError(message) {
            passwordError.textContent = message;
            passwordError.classList.remove('d-none');
        }

        function updatePasswordControls() {
            const locked = savingPassword || passwordChanged;

            savePasswordButton.disabled = locked;
            openPasswordButton.disabled = locked || loggingOut;
            logoutButton.disabled = locked || loggingOut;

            passwordForm.querySelectorAll('input').forEach((input) => {
                input.disabled = locked;
            });

            passwordModalElement
                .querySelectorAll('[data-bs-dismiss="modal"]')
                .forEach((button) => {
                    button.disabled = locked;
                });
        }

        logoutButton.addEventListener('click', async (event) => {
            event.preventDefault();

            if (savingPassword || passwordChanged || loggingOut) return;

            loggingOut = true;
            updatePasswordControls();

            try {
                await logout();
            } finally {
                loggingOut = false;
                updatePasswordControls();
            }
        });

        window.currentUser = apiFetch('/me').then((response) => response.data);

        window.currentUser
            .then((user) => {
                document.getElementById('user-name').textContent =
                    user.name ?? '-';
            })
            .catch(() => {
                document.getElementById('user-name').textContent = '-';
            });

        passwordModalElement.addEventListener('show.bs.modal', (event) => {
            if (loggingOut || passwordChanged) {
                event.preventDefault();
                return;
            }

            passwordForm.reset();
            clearPasswordMessages();

            savePasswordButton.textContent = 'Simpan Perubahan';
            updatePasswordControls();
        });

        passwordModalElement.addEventListener('shown.bs.modal', () => {
            document.getElementById('self-current-password').focus();
        });

        passwordModalElement.addEventListener('hide.bs.modal', (event) => {
            if (savingPassword || passwordChanged) {
                event.preventDefault();
            }
        });

        passwordModalElement.addEventListener('hidden.bs.modal', () => {
            passwordForm.reset();
            clearPasswordMessages();
        });

        passwordForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (savingPassword || passwordChanged || loggingOut) return;
            if (!passwordForm.reportValidity()) return;

            clearPasswordMessages();

            const currentPassword =
                document.getElementById('self-current-password').value;

            const newPassword =
                document.getElementById('self-new-password').value;

            const confirmation =
                document.getElementById('self-password-confirmation').value;

            if (newPassword !== confirmation) {
                displayPasswordError(
                    'Konfirmasi kata sandi baru tidak cocok.'
                );
                return;
            }

            if (newPassword === currentPassword) {
                displayPasswordError(
                    'Kata sandi baru harus berbeda dari kata sandi saat ini.'
                );
                return;
            }

            savingPassword = true;
            savePasswordButton.textContent = 'Menyimpan...';
            updatePasswordControls();

            try {
                const response = await apiFetch('/change-password', {
                    method: 'PUT',
                    body: JSON.stringify({
                        current_password: currentPassword,
                        password: newPassword,
                        password_confirmation: confirmation,
                    }),
                });

                Auth.clear();

                passwordChanged = true;
                passwordForm.reset();

                passwordSuccess.textContent =
                    response.message ||
                    'Kata sandi berhasil diubah. Silakan login kembali.';

                passwordSuccess.classList.remove('d-none');

                savePasswordButton.textContent = 'Berhasil';

                window.setTimeout(() => {
                    window.location.replace('/login');
                }, 1800);
            } catch (error) {
                displayPasswordError(formatErrors(error));

                document.getElementById('self-current-password').value = '';

                savePasswordButton.textContent = 'Simpan Perubahan';
            } finally {
                savingPassword = false;
                updatePasswordControls();
            }
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>