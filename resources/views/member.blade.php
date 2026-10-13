<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Area - LibraSys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/member">LibraSys</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3" id="user-name"></span>
                <button class="btn btn-outline-light btn-sm" id="logout-btn">Logout</button>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div id="alert"></div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Profil Anggota</h2>
                <div class="row">
                    <div class="col-md-4"><div class="text-muted small">Nama</div><div id="profile-name">-</div></div>
                    <div class="col-md-4"><div class="text-muted small">Email</div><div id="profile-email">-</div></div>
                    <div class="col-md-2"><div class="text-muted small">Kode Anggota</div><div id="profile-code">-</div></div>
                    <div class="col-md-2"><div class="text-muted small">Status</div><div id="profile-status">-</div></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Pinjaman Saya</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Dipinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Dikembalikan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="loans-body">
                            <tr><td colspan="5" class="text-muted">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">Daftar Buku</h2>
                <form class="row g-2 mb-3" id="search-form">
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="search" placeholder="Cari judul, penulis, atau ISBN">
                    </div>
                    <div class="col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody id="books-body">
                            <tr><td colspan="5" class="text-muted">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        requireRole('member');
        document.getElementById('logout-btn').addEventListener('click', logout);

        async function loadProfile() {
            const response = await apiFetch('/me');
            const user = response.data;

            document.getElementById('user-name').textContent = user.name;
            document.getElementById('profile-name').textContent = user.name;
            document.getElementById('profile-email').textContent = user.email;

            if (user.member) {
                document.getElementById('profile-code').textContent = user.member.member_code;
                const badge = user.member.status === 'active' ? 'success' : 'secondary';
                document.getElementById('profile-status').innerHTML =
                    `<span class="badge text-bg-${badge}">${escapeHtml(user.member.status)}</span>`;
            }
        }

        async function loadLoans() {
            const response = await apiFetch('/my-loans');
            const body = document.getElementById('loans-body');

            if (response.data.length === 0) {
                body.innerHTML = '<tr><td colspan="5" class="text-muted">Belum ada pinjaman.</td></tr>';
                return;
            }

            body.innerHTML = response.data.map((loan) => {
                const badge = loan.status === 'borrowed' ? 'warning' : 'success';
                return `
                    <tr>
                        <td>${escapeHtml(loan.book ? loan.book.title : '-')}</td>
                        <td>${formatDate(loan.borrowed_at)}</td>
                        <td>${formatDate(loan.due_at)}</td>
                        <td>${formatDate(loan.returned_at)}</td>
                        <td><span class="badge text-bg-${badge}">${escapeHtml(loan.status)}</span></td>
                    </tr>`;
            }).join('');
        }

        async function loadBooks(search = '') {
            const query = new URLSearchParams({ per_page: 10 });
            if (search) query.set('search', search);

            const response = await apiFetch(`/books?${query.toString()}`);
            const body = document.getElementById('books-body');

            if (response.data.length === 0) {
                body.innerHTML = '<tr><td colspan="5" class="text-muted">Buku tidak ditemukan.</td></tr>';
                return;
            }

            body.innerHTML = response.data.map((book) => {
                const availability = book.stock > 0
                    ? `<span class="badge text-bg-success">Tersedia (${book.stock})</span>`
                    : '<span class="badge text-bg-secondary">Stok habis</span>';

                return `
                    <tr>
                        <td>${escapeHtml(book.title)}</td>
                        <td>${escapeHtml(book.author)}</td>
                        <td>${escapeHtml(book.category ? book.category.name : '-')}</td>
                        <td>${escapeHtml(book.published_year ?? '-')}</td>
                        <td>${availability}</td>
                    </tr>`;
            }).join('');
        }

        document.getElementById('search-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            try {
                await loadBooks(document.getElementById('search').value.trim());
            } catch (error) {
                showAlert(error.message);
            }
        });

        (async () => {
            try {
                await Promise.all([loadProfile(), loadLoans(), loadBooks()]);
            } catch (error) {
                showAlert(error.message);
            }
        })();
    </script>
</body>
</html>