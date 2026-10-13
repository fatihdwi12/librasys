<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - LibraSys</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons untuk Icon Mata Password -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #eef0f2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
        }
        .toggle-capsule {
            background-color: #e9ecef;
            border-radius: 50px;
            padding: 4px;
        }
        .toggle-tab {
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .toggle-tab.active {
            background-color: #ffffff;
            color: #212529;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        .toggle-tab:not(.active) {
            color: #6c757d;
        }
        .form-control {
            border-radius: 12px;
            padding: 10px 14px;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }
        .form-control::placeholder {
            color: #adb5bd;
        }
        .btn-dark-custom {
            background-color: #4d4847;
            color: #ffffff;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            border: none;
            transition: background-color 0.2s ease;
        }
        .btn-dark-custom:hover {
            background-color: #383433;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="card auth-card bg-white p-3">
        <div class="card-body p-4">
            <!-- Header -->
            <h1 class="h3 fw-bold text-start mb-1" style="color: #1a1a1a;">Librasys Login</h1>
            <p class="text-muted small mb-4">enjoy for you book!</p>

            <!-- Toggle Switch Login / Register -->
            <div class="toggle-capsule d-flex justify-content-between align-items-center mb-4">
                <a href="/login" class="toggle-tab active text-center flex-fill">Log in</a>
                <a href="/register" class="toggle-tab text-center flex-fill">Create Account</a>
            </div>

            <!-- Subtitle -->
            <h2 class="h6 fw-bold mb-3 text-start">Welcome Back!</h2>

            <div id="alert"></div>

            <!-- Form -->
            <form id="login-form">
                <div class="mb-3 text-start">
                    <label for="email" class="form-label small fw-semibold text-secondary mb-1">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Put your email!" value="admin@librasys.test" required>
                </div>

                <div class="mb-4 text-start">
                    <label for="password" class="form-label small fw-semibold text-secondary mb-1">Password</label>
                    <div class="position-relative">
                        <input type="password" class="form-control pe-5" id="password" placeholder="Keep it Secret!" value="password" required>
                        <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y me-1 text-secondary" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark-custom w-100" id="login-btn">Login</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        // Toggle Visibility Password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // Submit Form Login
        document.getElementById('login-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = document.getElementById('login-btn');
            button.disabled = true;

            try {
                const response = await apiFetch('/login', {
                    method: 'POST',
                    skipAuthRedirect: true,
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                    }),
                });

                Auth.setToken(response.data.token.access_token);
                window.location.href = '/dashboard';
            } catch (error) {
                showAlert(formatErrors(error));
                button.disabled = false;
            }
        });
    </script>
</body>
</html>