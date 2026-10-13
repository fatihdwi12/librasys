@extends('layouts.member')

@section('title', 'Books - LibraSys')
@section('heading', 'Books')
@section('subheading', 'Jelajahi koleksi buku perpustakaan')

@section('content')
    <form class="row g-2 mb-4" id="filter-form">
        <div class="col-md-6">
            <input type="text" class="form-control form-control-dark" id="search" placeholder="Cari judul, penulis, atau ISBN">
        </div>
        <div class="col-md-4">
            <select class="form-select form-control-dark" id="category">
                <option value="">Semua kategori</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-light fw-semibold">Cari</button>
        </div>
    </form>

    <div class="row g-3" id="books-grid">
        <div class="col-12 text-secondary">Memuat...</div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4" id="pagination"></div>
@endsection

@push('scripts')
<script>
    async function loadCategories() {
        const response = await apiFetch('/categories');
        const select = document.getElementById('category');

        response.data.forEach((category) => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            select.appendChild(option);
        });
    }

    function bookCard(book) {
        const availability = book.stock > 0
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Tersedia &middot; ${escapeHtml(book.stock)}</span>`
            : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Stok habis</span>';

        return `
            <div class="col-md-6 col-xl-4">
                <div class="book-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <span class="badge rounded-pill text-bg-dark border border-secondary">
                            ${escapeHtml(book.category ? book.category.name : '-')}
                        </span>
                        ${availability}
                    </div>
                    <h3 class="book-title mb-1">${escapeHtml(book.title)}</h3>
                    <p class="text-secondary small mb-3">${escapeHtml(book.author)}</p>
                    <div class="d-flex justify-content-between text-secondary small">
                        <span><i class="bi bi-calendar3 me-1"></i>${escapeHtml(book.published_year ?? '-')}</span>
                        <span><i class="bi bi-upc me-1"></i>${escapeHtml(book.isbn)}</span>
                    </div>
                </div>
            </div>`;
    }

    function renderPagination(meta) {
        const box = document.getElementById('pagination');

        if (meta.total === 0) {
            box.innerHTML = '';
            return;
        }

        box.innerHTML = `
            <span class="text-secondary small">Halaman ${meta.current_page} dari ${meta.last_page} &middot; ${meta.total} buku</span>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="prev-page" ${meta.current_page <= 1 ? 'disabled' : ''}>Sebelumnya</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="next-page" ${meta.current_page >= meta.last_page ? 'disabled' : ''}>Berikutnya</button>
            </div>`;

        document.getElementById('prev-page').addEventListener('click', () => loadBooks(meta.current_page - 1));
        document.getElementById('next-page').addEventListener('click', () => loadBooks(meta.current_page + 1));
    }

    async function loadBooks(page = 1) {
        const params = new URLSearchParams({ per_page: 9, page });

        const search = document.getElementById('search').value.trim();
        const category = document.getElementById('category').value;
        if (search) params.set('search', search);
        if (category) params.set('category_id', category);

        const grid = document.getElementById('books-grid');

        try {
            const response = await apiFetch(`/books?${params.toString()}`);

            if (response.data.length === 0) {
                grid.innerHTML = '<div class="col-12 text-secondary">Buku tidak ditemukan.</div>';
            } else {
                grid.innerHTML = response.data.map(bookCard).join('');
            }

            renderPagination(response.meta);
        } catch (error) {
            showAlert(error.message);
        }
    }

    document.getElementById('filter-form').addEventListener('submit', (event) => {
        event.preventDefault();
        loadBooks(1);
    });

    document.getElementById('category').addEventListener('change', () => loadBooks(1));

    (async () => {
        try {
            await loadCategories();
        } catch (error) {
            showAlert(error.message);
        }
        loadBooks(1);
    })();
</script>
@endpush