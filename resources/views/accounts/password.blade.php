<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ubah Kata Sandi - LibraSys</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #141414;
            color: #ffffff;
            min-height: 100vh;
        }

        .account-wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 48px 16px;
        }

        .account-panel {
            background-color: #1f1f1f;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 24px;
        }

        .form-control {
            background-color: #141414;
            border-color: #333333;
            color: #ffffff;
        }

        .form-control:focus {
            background-color: #141414;
            border-color: #777777;
            color: #ffffff;
            box-shadow: none;
        }
    </style>
</head>

<body data-bs-theme="dark">
    <main class="account-wrapper">
        <a id="back-link"
           href="/login"
           class="link-light text-decoration-none d-inline-block mb-4">
            &larr; Kembali
        </a>

        <h1 class="h3 fw-bold mb-2">Ubah Kata Sandi</h1>

        <p class="text-secondary mb-4">
            Setelah kata sandi berhasil diubah, Anda perlu login kembali.
        </p>

        <div id="alert" aria-live="polite"></div>

        <div class="account-panel">
            <form id="password-form">
                <div class="mb-3">
                    <label for="current-password" class="form-label">
                        Kata Sandi Saat Ini
                    </label>

                    <input type="password"
                           id="current-password"
                           class="form-control"
                           autocomplete="current-password"
                           required>
                </div>

                <div class="mb-3">
                    <label for="new-password" class="form-label">
                        Kata Sandi Baru
                    </label>

                    <input type="password"
                           id="new-password"
                           class="form-control"
                           autocomplete="new-password"
                           minlength="15"
                           maxlength="64"
                           aria-describedby="password-help"
                           required>

                    <div id="password-help" class="form-text">
                        Gunakan 15–64 karakter. Kata sandi baru harus berbeda
                        dari kata sandi saat ini.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password-confirmation" class="form-label">
                        Konfirmasi Kata Sandi Baru
                    </label>

                    <input type="password"
                           id="password-confirmation"
                           class="form-control"
                           autocomplete="new-password"
                           minlength="15"
                           maxlength="64"
                           required>
                </div>

                <button type="submit"
                        id="save-password-btn"
                        class="btn btn-light w-100">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </main>

    <script src="{{ asset('js/api.js') }}"></script>

    <script>
    (() => {
        if (!Auth.getToken()) {
            Auth.clear();
            window.location.replace('/login');
            return;
        }

        const form = document.getElementById('password-form');
        const saveButton = document.getElementById('save-password-btn');
        const backLink = document.getElementById('back-link');

        let saving = false;

        backLink.href = homeFor(Auth.getRole());

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (saving) return;
            if (!form.reportValidity()) return;

            // Jangan trim password: spasi bisa merupakan bagian password.
            const currentPassword =
                document.getElementById('current-password').value;

            const password =
                document.getElementById('new-password').value;

            const confirmation =
                document.getElementById('password-confirmation').value;

            if (password !== confirmation) {
                showAlert('Konfirmasi kata sandi baru tidak cocok.');
                return;
            }

            if (password === currentPassword) {
                showAlert(
                    'Kata sandi baru harus berbeda dari kata sandi saat ini.'
                );
                return;
            }

            saving = true;
            saveButton.disabled = true;
            saveButton.textContent = 'Menyimpan...';

            try {
                const response = await apiFetch('/change-password', {
                    method: 'PUT',
                    body: JSON.stringify({
                        current_password: currentPassword,
                        password: password,
                        password_confirmation: confirmation,
                    }),
                });

                Auth.clear();
                form.reset();

                form.querySelectorAll('input').forEach((input) => {
                    input.disabled = true;
                });

                showAlert(
                    response.message ||
                    'Kata sandi berhasil diubah. Silakan login kembali.',
                    'success'
                );

                backLink.href = '/login';
                backLink.textContent = 'Ke halaman login';

                saveButton.textContent = 'Berhasil';

                window.setTimeout(() => {
                    window.location.replace('/login');
                }, 1800);
            } catch (error) {
                showAlert(formatErrors(error));

                saving = false;
                saveButton.disabled = false;
                saveButton.textContent = 'Simpan Perubahan';
            }
        });
    })();
    </script>
</body>
</html>