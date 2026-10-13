@extends('layouts.app')

@section('title', 'Categories - LibraSys')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Categories</h1>
            <p class="text-secondary mb-0">
                Kelola kategori koleksi buku perpustakaan.
            </p>
        </div>

        <button type="button" class="btn btn-light" id="add-category-btn">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Kategori
        </button>
    </div>

    <div class="category-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h6 mb-0">Daftar Kategori</h2>
            <span class="text-secondary small" id="category-total">Memuat...</span>
        </div>

        <div class="table-responsive">
            <table class="table category-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Buku</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody id="categories-body">
                    <tr>
                        <td colspan="4" class="text-secondary text-center py-4">
                            Memuat kategori...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Satu modal untuk tambah dan ubah kategori -->
    <div class="modal fade"
         id="category-modal"
         tabindex="-1"
         aria-labelledby="category-modal-title"
         aria-hidden="true"
         data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="category-form">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="category-modal-title">
                            Tambah Kategori
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div id="category-form-error"
                             class="alert alert-danger d-none"
                             role="alert"></div>

                        <div class="mb-3">
                            <label for="category-name" class="form-label">
                                Nama Kategori
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="category-name"
                                   maxlength="255"
                                   required>
                        </div>

                        <div>
                            <label for="category-description" class="form-label">
                                Deskripsi
                            </label>

                            <textarea class="form-control"
                                      id="category-description"
                                      rows="3"
                                      placeholder="Opsional"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                                class="btn btn-light"
                                id="save-category-btn">
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
    .category-panel {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 24px;
    }

    .category-table {
        --bs-table-bg: #1f1f1f;
        --bs-table-color: #ffffff;
        --bs-table-border-color: #333333;
    }

    .category-table th {
        background-color: #181818;
        color: #a0a0a0;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 14px 12px;
    }

    .category-table td {
        padding: 14px 12px;
    }

    .category-table .description-cell {
        min-width: 220px;
        max-width: 420px;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    #category-modal .modal-content {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
    }

    #category-modal .form-control {
        background-color: #141414;
        border-color: #333333;
        color: #ffffff;
    }

    #category-modal .form-control:focus {
        border-color: #777777;
        box-shadow: none;
    }
</style>
@endpush

@push('scripts')
<script>
    const categoryModal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('category-modal')
    );

    const categoryForm = document.getElementById('category-form');
    const categoriesBody = document.getElementById('categories-body');
    const saveCategoryButton = document.getElementById('save-category-btn');
    const categoryFormError = document.getElementById('category-form-error');

    let categories = [];
    let editingCategoryId = null;

    function clearCategoryFormError() {
        categoryFormError.textContent = '';
        categoryFormError.classList.add('d-none');
    }

    function displayCategoryFormError(message) {
        categoryFormError.textContent = message;
        categoryFormError.classList.remove('d-none');
    }

    function renderCategories() {
        document.getElementById('category-total').textContent =
            `${categories.length} kategori`;

        if (categories.length === 0) {
            categoriesBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-secondary text-center py-4">
                        Belum ada kategori.
                    </td>
                </tr>`;
            return;
        }

        categoriesBody.innerHTML = categories.map((category) => `
            <tr>
                <td class="fw-semibold">${escapeHtml(category.name)}</td>

                <td class="description-cell text-secondary">
                    ${escapeHtml(category.description || '-')}
                </td>

                <td>
                    <span class="badge text-bg-secondary">
                        ${escapeHtml(category.books_count ?? 0)}
                    </span>
                </td>

                <td class="text-end text-nowrap">
                    <button type="button"
                            class="btn btn-outline-light btn-sm"
                            data-action="edit"
                            data-id="${Number(category.id)}">
                        <i class="bi bi-pencil me-1"></i>
                        Ubah
                    </button>

                    <button type="button"
                            class="btn btn-outline-danger btn-sm ms-1"
                            data-action="delete"
                            data-id="${Number(category.id)}">
                        <i class="bi bi-trash me-1"></i>
                        Hapus
                    </button>
                </td>
            </tr>
        `).join('');
    }

    async function loadCategories() {
        const response = await apiFetch('/categories');
        categories = response.data;
        renderCategories();
    }

    function openCategoryModal(category = null) {
        categoryForm.reset();
        clearCategoryFormError();

        editingCategoryId = category ? category.id : null;

        document.getElementById('category-modal-title').textContent =
            category ? 'Ubah Kategori' : 'Tambah Kategori';

        document.getElementById('category-name').value =
            category ? category.name : '';

        document.getElementById('category-description').value =
            category ? (category.description ?? '') : '';

        saveCategoryButton.disabled = false;
        saveCategoryButton.textContent = 'Simpan';

        categoryModal.show();
    }

    document.getElementById('add-category-btn').addEventListener('click', () => {
        openCategoryModal();
    });

    document.getElementById('category-modal').addEventListener('shown.bs.modal', () => {
        document.getElementById('category-name').focus();
    });

    categoryForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearCategoryFormError();

        const name = document.getElementById('category-name').value.trim();
        const description = document.getElementById('category-description').value.trim();

        if (!name) {
            displayCategoryFormError('Nama kategori wajib diisi.');
            return;
        }

        const isEditing = editingCategoryId !== null;
        const path = isEditing
            ? `/categories/${editingCategoryId}`
            : '/categories';

        saveCategoryButton.disabled = true;
        saveCategoryButton.textContent = 'Menyimpan...';

        try {
            const response = await apiFetch(path, {
                method: isEditing ? 'PUT' : 'POST',
                body: JSON.stringify({
                    name,
                    description: description || null,
                }),
            });

            categoryModal.hide();
            showAlert(response.message, 'success');

            try {
                await loadCategories();
            } catch (error) {
                showAlert(`Data tersimpan, tetapi tabel gagal dimuat ulang: ${error.message}`);
            }
        } catch (error) {
            displayCategoryFormError(formatErrors(error));
        } finally {
            saveCategoryButton.disabled = false;
            saveCategoryButton.textContent = 'Simpan';
        }
    });

    categoriesBody.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');

        if (!button) return;

        const id = Number(button.dataset.id);
        const category = categories.find((item) => item.id === id);

        if (!category) return;

        if (button.dataset.action === 'edit') {
            openCategoryModal(category);
            return;
        }

        const confirmed = window.confirm(
            `Hapus kategori "${category.name}"?`
        );

        if (!confirmed) return;

        button.disabled = true;

        try {
            const response = await apiFetch(`/categories/${id}`, {
                method: 'DELETE',
            });

            showAlert(response.message, 'success');

            try {
                await loadCategories();
            } catch (error) {
                showAlert(`Kategori dihapus, tetapi tabel gagal dimuat ulang: ${error.message}`);
            }
        } catch (error) {
            showAlert(formatErrors(error));
        } finally {
            button.disabled = false;
        }
    });

    loadCategories().catch((error) => {
        categoriesBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-secondary text-center py-4">
                    Gagal memuat kategori.
                </td>
            </tr>`;

        document.getElementById('category-total').textContent = '-';
        showAlert(error.message);
    });
</script>
@endpush