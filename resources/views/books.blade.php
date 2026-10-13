@extends('layouts.app')

@section('title', 'Books - LibraSys')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Books</h1>
            <p class="text-secondary mb-0">
                Kelola koleksi buku perpustakaan.
            </p>
        </div>

        <button type="button"
                class="btn btn-light"
                id="add-book-btn"
                disabled>
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Buku
        </button>
    </div>

    <div class="books-panel">
        <form id="books-filter-form" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="books-search" class="form-label small">
                    Pencarian
                </label>
                <input type="search"
                       id="books-search"
                       class="form-control"
                       placeholder="Judul, penulis, atau ISBN">
            </div>

            <div class="col-md-3">
                <label for="books-category" class="form-label small">
                    Kategori
                </label>
                <select id="books-category" class="form-select">
                    <option value="">Semua kategori</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="books-per-page" class="form-label small">
                    Per halaman
                </label>
                <select id="books-per-page" class="form-select">
                    <option value="3">3</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-light w-100">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table books-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Judul / ISBN</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Tahun</th>
                        <th>Stok Tersedia</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody id="books-body">
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            Memuat buku...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
            <span id="books-summary" class="text-secondary small">-</span>

            <div class="btn-group">
                <button type="button"
                        id="books-prev"
                        class="btn btn-outline-secondary btn-sm"
                        disabled>
                    Sebelumnya
                </button>

                <button type="button"
                        id="books-next"
                        class="btn btn-outline-secondary btn-sm"
                        disabled>
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade"
         id="book-modal"
         tabindex="-1"
         aria-labelledby="book-modal-title"
         aria-hidden="true"
         data-bs-theme="dark"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="book-form">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="book-modal-title">
                            Tambah Buku
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div id="book-form-error"
                             class="alert alert-danger d-none"
                             role="alert"></div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="book-title" class="form-label">
                                    Judul
                                </label>
                                <input type="text"
                                       id="book-title"
                                       class="form-control"
                                       maxlength="255"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label for="book-category" class="form-label">
                                    Kategori
                                </label>
                                <select id="book-category"
                                        class="form-select"
                                        required>
                                    <option value="">Pilih kategori</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="book-author" class="form-label">
                                    Penulis
                                </label>
                                <input type="text"
                                       id="book-author"
                                       class="form-control"
                                       maxlength="255"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="book-isbn" class="form-label">
                                    ISBN
                                </label>
                                <input type="text"
                                       id="book-isbn"
                                       class="form-control"
                                       maxlength="50"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="book-year" class="form-label">
                                    Tahun Terbit
                                </label>
                                <input type="number"
                                       id="book-year"
                                       class="form-control"
                                       min="1000"
                                       max="{{ now()->year }}"
                                       step="1"
                                       placeholder="Opsional">
                            </div>

                            <div class="col-md-6">
                                <label for="book-stock" class="form-label">
                                    Stok Tersedia
                                </label>
                                <input type="number"
                                       id="book-stock"
                                       class="form-control"
                                       min="0"
                                       step="1"
                                       value="0"
                                       required>
                            </div>

                            <div class="col-12">
                                <label for="book-description" class="form-label">
                                    Deskripsi
                                </label>
                                <textarea id="book-description"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Opsional"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                                id="save-book-btn"
                                class="btn btn-light">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .books-panel {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 24px;
    }

    .books-panel .form-control,
    .books-panel .form-select,
    #book-modal .form-control,
    #book-modal .form-select {
        background-color: #141414;
        border-color: #333333;
        color: #ffffff;
        color-scheme: dark;
    }

    .books-panel .form-control::placeholder,
    #book-modal .form-control::placeholder {
        color: #888888;
    }

    .books-panel .form-control:focus,
    .books-panel .form-select:focus,
    #book-modal .form-control:focus,
    #book-modal .form-select:focus {
        border-color: #777777;
        box-shadow: none;
    }

    .books-table {
        --bs-table-bg: #1f1f1f;
        --bs-table-color: #ffffff;
        --bs-table-border-color: #333333;
    }

    .books-table th {
        background-color: #181818;
        color: #a0a0a0;
        font-size: 0.875rem;
        padding: 14px 12px;
    }

    .books-table td {
        padding: 14px 12px;
    }

    .books-table td:first-child {
        min-width: 240px;
    }

    #book-modal .modal-content {
        background-color: #1f1f1f;
        color: #ffffff;
        border: 1px solid #333333;
        border-radius: 12px;
    }

    #book-modal .modal-header,
    #book-modal .modal-footer {
        border-color: #333333;
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const booksBody = document.getElementById('books-body');
    const previousButton = document.getElementById('books-prev');
    const nextButton = document.getElementById('books-next');
    const summary = document.getElementById('books-summary');
    const filterForm = document.getElementById('books-filter-form');

    const bookForm = document.getElementById('book-form');
    const formError = document.getElementById('book-form-error');
    const saveButton = document.getElementById('save-book-btn');
    const addButton = document.getElementById('add-book-btn');
    const modalElement = document.getElementById('book-modal');
    const bookModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    let currentPage = 1;
    let lastPage = 1;
    let loadingBooks = false;
    let savingBook = false;
    let actionBusy = false;
    let categoriesLoaded = false;
    let editingBookId = null;

    let appliedFilters = {
        search: '',
        category_id: '',
        per_page: '10',
    };

    function updateControls() {
        const busy = loadingBooks || savingBook || actionBusy;

        previousButton.disabled = busy || currentPage <= 1;
        nextButton.disabled = busy || currentPage >= lastPage;
        addButton.disabled = busy || !categoriesLoaded;

        filterForm.querySelectorAll('input, select, button').forEach((element) => {
            element.disabled = busy;
        });

        booksBody.querySelectorAll('button[data-action]').forEach((button) => {
            button.disabled = busy || (
                button.dataset.action === 'edit' && !categoriesLoaded
            );
        });

        saveButton.disabled = savingBook;

        modalElement.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
            button.disabled = savingBook;
        });
    }

    function clearFormError() {
        formError.textContent = '';
        formError.classList.add('d-none');
    }

    function displayFormError(message) {
        formError.textContent = message;
        formError.classList.remove('d-none');
    }

    async function loadCategoryOptions() {
        const response = await apiFetch('/categories');
        const filterSelect = document.getElementById('books-category');
        const formSelect = document.getElementById('book-category');

        filterSelect.replaceChildren(new Option('Semua kategori', ''));
        formSelect.replaceChildren(new Option('Pilih kategori', ''));

        response.data.forEach((category) => {
            filterSelect.add(new Option(category.name, category.id));
            formSelect.add(new Option(category.name, category.id));
        });

        categoriesLoaded = true;
        updateControls();
    }

    function renderBooks(books) {
        if (books.length === 0) {
            booksBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        Buku tidak ditemukan.
                    </td>
                </tr>`;
            return;
        }

        booksBody.innerHTML = books.map((book) => {
            const stock = Number(book.stock) > 0
                ? `<span class="badge text-bg-success">${escapeHtml(book.stock)}</span>`
                : '<span class="badge text-bg-secondary">Habis</span>';

            return `
                <tr>
                    <td>
                        <div class="fw-semibold">${escapeHtml(book.title)}</div>
                        <div class="text-secondary small mt-1">
                            ISBN: ${escapeHtml(book.isbn)}
                        </div>
                    </td>
                    <td>${escapeHtml(book.author)}</td>
                    <td>${escapeHtml(book.category?.name ?? '-')}</td>
                    <td>${escapeHtml(book.published_year ?? '-')}</td>
                    <td>${stock}</td>
                    <td class="text-end text-nowrap">
                        <button type="button"
                                class="btn btn-outline-light btn-sm"
                                data-action="edit"
                                data-id="${Number(book.id)}">
                            <i class="bi bi-pencil me-1"></i>
                            Ubah
                        </button>

                        <button type="button"
                                class="btn btn-outline-danger btn-sm ms-1"
                                data-action="delete"
                                data-id="${Number(book.id)}">
                            <i class="bi bi-trash me-1"></i>
                            Hapus
                        </button>
                    </td>
                </tr>`;
        }).join('');
    }

    async function loadBooks(page = 1) {
        if (loadingBooks) return;

        loadingBooks = true;
        updateControls();

        const params = new URLSearchParams({
            page: String(page),
            per_page: appliedFilters.per_page,
        });

        if (appliedFilters.search) {
            params.set('search', appliedFilters.search);
        }

        if (appliedFilters.category_id) {
            params.set('category_id', appliedFilters.category_id);
        }

        try {
            let response = await apiFetch(`/books?${params.toString()}`);

            // Setelah hapus, halaman yang diminta mungkin sudah tidak tersedia.
            const availableLastPage = Math.max(
                1,
                Number(response.meta.last_page)
            );

            if (page > availableLastPage) {
                params.set('page', String(availableLastPage));
                response = await apiFetch(`/books?${params.toString()}`);
            }

            renderBooks(response.data);

            currentPage = Number(response.meta.current_page);
            lastPage = Math.max(1, Number(response.meta.last_page));

            summary.textContent = Number(response.meta.total) === 0
                ? 'Tidak ada buku yang sesuai.'
                : `Menampilkan ${response.meta.from}–${response.meta.to} dari ${response.meta.total} buku · Halaman ${currentPage}/${lastPage}`;
        } catch (error) {
            showAlert(`Daftar buku gagal dimuat: ${formatErrors(error)}`);
        } finally {
            loadingBooks = false;
            updateControls();
        }
    }

    function applyFilters() {
        if (loadingBooks || savingBook || actionBusy) return;

        appliedFilters = {
            search: document.getElementById('books-search').value.trim(),
            category_id: document.getElementById('books-category').value,
            per_page: document.getElementById('books-per-page').value,
        };

        loadBooks(1);
    }

    function openBookModal(book = null) {
        bookForm.reset();
        clearFormError();

        editingBookId = book ? book.id : null;

        document.getElementById('book-modal-title').textContent =
            book ? 'Ubah Buku' : 'Tambah Buku';

        document.getElementById('book-title').value = book?.title ?? '';
        document.getElementById('book-category').value =
            book?.category_id ?? book?.category?.id ?? '';
        document.getElementById('book-author').value = book?.author ?? '';
        document.getElementById('book-isbn').value = book?.isbn ?? '';
        document.getElementById('book-year').value = book?.published_year ?? '';
        document.getElementById('book-stock').value = book?.stock ?? 0;
        document.getElementById('book-description').value =
            book?.description ?? '';

        saveButton.textContent = 'Simpan';
        updateControls();
        bookModal.show();
    }

    filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
    });

    document.getElementById('books-category').addEventListener(
        'change',
        applyFilters
    );

    document.getElementById('books-per-page').addEventListener(
        'change',
        applyFilters
    );

    previousButton.addEventListener('click', () => {
        loadBooks(currentPage - 1);
    });

    nextButton.addEventListener('click', () => {
        loadBooks(currentPage + 1);
    });

    addButton.addEventListener('click', () => {
        openBookModal();
    });

    modalElement.addEventListener('shown.bs.modal', () => {
        document.getElementById('book-title').focus();
    });

    modalElement.addEventListener('hide.bs.modal', (event) => {
        if (savingBook) {
            event.preventDefault();
        }
    });

    bookForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (savingBook) return;
        if (!bookForm.reportValidity()) return;

        clearFormError();

        const year = document.getElementById('book-year').value;
        const description = document.getElementById('book-description')
            .value.trim();

        const data = {
            category_id: Number(document.getElementById('book-category').value),
            title: document.getElementById('book-title').value.trim(),
            author: document.getElementById('book-author').value.trim(),
            isbn: document.getElementById('book-isbn').value.trim(),
            published_year: year ? Number(year) : null,
            stock: Number(document.getElementById('book-stock').value),
            description: description || null,
        };

        if (!data.title || !data.author || !data.isbn || !data.category_id) {
            displayFormError('Kategori, judul, penulis, dan ISBN wajib diisi.');
            return;
        }

        const isEditing = editingBookId !== null;
        const path = isEditing ? `/books/${editingBookId}` : '/books';

        savingBook = true;
        saveButton.textContent = 'Menyimpan...';
        updateControls();

        try {
            const response = await apiFetch(path, {
                method: isEditing ? 'PUT' : 'POST',
                body: JSON.stringify(data),
            });

            savingBook = false;
            bookModal.hide();

            showAlert(
                response.message || (
                    isEditing
                        ? 'Buku berhasil diperbarui.'
                        : 'Buku berhasil ditambahkan.'
                ),
                'success'
            );

            await loadBooks(isEditing ? currentPage : 1);
        } catch (error) {
            displayFormError(formatErrors(error));
        } finally {
            savingBook = false;
            saveButton.textContent = 'Simpan';
            updateControls();
        }
    });

    booksBody.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');

        if (!button || loadingBooks || savingBook || actionBusy) return;

        const id = Number(button.dataset.id);
        const action = button.dataset.action;

        if (action === 'edit') {
            if (!categoriesLoaded) {
                showAlert('Kategori belum berhasil dimuat. Muat ulang halaman.');
                return;
            }

            actionBusy = true;
            updateControls();

            try {
                const response = await apiFetch(`/books/${id}`);
                openBookModal(response.data);
            } catch (error) {
                showAlert(formatErrors(error));
            } finally {
                actionBusy = false;
                updateControls();
            }

            return;
        }

        if (action !== 'delete') return;

        const title = button.closest('tr')
            .querySelector('.fw-semibold').textContent;

        if (!window.confirm(`Hapus buku "${title}"? Tindakan ini tidak dapat dibatalkan.`)) {
            return;
        }

        actionBusy = true;
        updateControls();

        try {
            const response = await apiFetch(`/books/${id}`, {
                method: 'DELETE',
            });

            showAlert(
                response.message || 'Buku berhasil dihapus.',
                'success'
            );

            await loadBooks(currentPage);
        } catch (error) {
            showAlert(formatErrors(error));
        } finally {
            actionBusy = false;
            updateControls();
        }
    });

    updateControls();

    loadCategoryOptions().catch((error) => {
        showAlert(`Kategori gagal dimuat: ${formatErrors(error)}`);
    });

    loadBooks();
})();
</script>
@endpush