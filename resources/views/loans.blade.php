@extends('layouts.app')

@section('title', 'Active Loan - LibraSys')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Active Loan</h1>

            <p class="text-secondary mb-0">
                Pantau peminjaman buku yang belum dikembalikan.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button"
                    id="reload-loans-btn"
                    class="btn btn-outline-light"
                    disabled>
                <i class="bi bi-arrow-clockwise me-1"></i>
                Muat Ulang
            </button>

            <button type="button"
                    id="add-loan-btn"
                    class="btn btn-light"
                    disabled>
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Peminjaman
            </button>
        </div>
    </div>

    <div class="loans-panel">
        <form id="loans-filter-form" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="loans-search" class="form-label small">
                    Pencarian
                </label>

                <input type="search"
                       id="loans-search"
                       class="form-control"
                       placeholder="Kode anggota, nama, judul buku, atau ISBN">
            </div>

            <div class="col-md-3">
                <label for="loans-condition" class="form-label small">
                    Kondisi
                </label>

                <select id="loans-condition" class="form-select">
                    <option value="">Semua peminjaman aktif</option>
                    <option value="on_time">Belum terlambat</option>
                    <option value="overdue">Terlambat</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="loans-per-page" class="form-label small">
                    Per halaman
                </label>

                <select id="loans-per-page" class="form-select">
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
            <table class="table loans-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID Loan</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Kondisi</th>
                        
                    </tr>
                </thead>

                <tbody id="loans-body">
                    <tr>
                        <td colspan="6"
                            class="text-center text-secondary py-4">
                            Memuat peminjaman aktif...
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button"
                                    class="btn btn-outline-success btn-sm"
                                    data-action="return"
                                    data-id="${Number(loan.id)}">
                                <i class="bi bi-check-circle me-1"></i>
                                Kembalikan
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
            <span id="loans-summary"
                  class="text-secondary small"
                  aria-live="polite">
                -
            </span>

            <div class="btn-group">
                <button type="button"
                        id="loans-prev"
                        class="btn btn-outline-secondary btn-sm"
                        disabled>
                    Sebelumnya
                </button>

                <button type="button"
                        id="loans-next"
                        class="btn btn-outline-secondary btn-sm"
                        disabled>
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    {{-- Modal tambah peminjaman --}}
    <div class="modal fade loans-modal"
         id="loan-modal"
         tabindex="-1"
         aria-labelledby="loan-modal-title"
         aria-hidden="true"
         data-bs-theme="dark"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="loan-form">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="loan-modal-title">
                            Tambah Peminjaman
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div id="loan-form-error"
                             class="alert alert-danger d-none"
                             role="alert"></div>

                        <p id="loan-options-status"
                           class="text-secondary small"
                           role="status"
                           aria-live="polite"></p>

                        <div class="mb-3">
                            <label for="loan-member" class="form-label">
                                Anggota
                            </label>

                            <select id="loan-member"
                                    class="form-select"
                                    required>
                                <option value="">Pilih anggota aktif</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="loan-book" class="form-label">
                                Buku
                            </label>

                            <select id="loan-book"
                                    class="form-select"
                                    required>
                                <option value="">Pilih buku tersedia</option>
                            </select>

                            <div class="form-text">
                                Setiap peminjaman mencatat satu eksemplar buku.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="loan-due-at" class="form-label">
                                Tanggal Jatuh Tempo
                            </label>

                            <input type="date"
                                   id="loan-due-at"
                                   class="form-control"
                                   required>
                        </div>

                        <p class="text-secondary small mb-0">
                            Stok buku berkurang satu setelah peminjaman
                            berhasil disimpan.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                                id="save-loan-btn"
                                class="btn btn-light"
                                disabled>
                            Simpan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .loans-panel {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 24px;
    }

    .loans-panel .form-control,
    .loans-panel .form-select,
    .loans-modal .form-control,
    .loans-modal .form-select {
        background-color: #141414;
        border-color: #333333;
        color: #ffffff;
        color-scheme: dark;
    }

    .loans-panel .form-control::placeholder,
    .loans-modal .form-control::placeholder {
        color: #888888;
    }

    .loans-panel .form-control:focus,
    .loans-panel .form-select:focus,
    .loans-modal .form-control:focus,
    .loans-modal .form-select:focus {
        background-color: #141414;
        border-color: #777777;
        color: #ffffff;
        box-shadow: none;
    }

    .loans-table {
        --bs-table-bg: #1f1f1f;
        --bs-table-color: #ffffff;
        --bs-table-border-color: #333333;
    }

    .loans-table th {
        background-color: #181818;
        color: #a0a0a0;
        font-size: 0.875rem;
        padding: 14px 12px;
        white-space: nowrap;
    }

    .loans-table td {
        padding: 14px 12px;
    }

    .loans-table .loan-member-cell {
        min-width: 180px;
        overflow-wrap: anywhere;
    }

    .loans-table .loan-book-cell {
        min-width: 220px;
        max-width: 360px;
        overflow-wrap: anywhere;
    }

    .loans-table .loan-date-cell {
        white-space: nowrap;
    }

    .loans-modal .modal-content {
        background-color: #1f1f1f;
        color: #ffffff;
        border: 1px solid #333333;
        border-radius: 12px;
    }

    .loans-modal .modal-header,
    .loans-modal .modal-footer {
        border-color: #333333;
    }

    .loans-modal .form-text {
        color: #a0a0a0;
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const loansBody = document.getElementById('loans-body');
    const summary = document.getElementById('loans-summary');

    const previousButton = document.getElementById('loans-prev');
    const nextButton = document.getElementById('loans-next');
    const reloadButton = document.getElementById('reload-loans-btn');
    const addLoanButton = document.getElementById('add-loan-btn');

    const filterForm = document.getElementById('loans-filter-form');
    const searchInput = document.getElementById('loans-search');
    const conditionSelect = document.getElementById('loans-condition');
    const perPageSelect = document.getElementById('loans-per-page');

    const loanForm = document.getElementById('loan-form');
    const loanFormError = document.getElementById('loan-form-error');
    const saveLoanButton = document.getElementById('save-loan-btn');

    const memberSelect = document.getElementById('loan-member');
    const bookSelect = document.getElementById('loan-book');
    const dueInput = document.getElementById('loan-due-at');
    const optionsStatus = document.getElementById('loan-options-status');

    const loanModalElement = document.getElementById('loan-modal');
    const loanModal = bootstrap.Modal.getOrCreateInstance(loanModalElement);

    let allLoans = [];
    let currentPage = 1;
    let lastPage = 1;

    let loadingLoans = false;
    let hasLoaded = false;

    let loadingOptions = false;
    let savingLoan = false;
    let optionsReady = false;
    let returningLoan = false;

    let appliedFilters = {
        search: '',
        condition: '',
        per_page: 10,
    };

    function isBusy() {
    return loadingLoans
        || loadingOptions
        || savingLoan
        || returningLoan;
}

    function updateControls() {
        const busy = isBusy();

        loansBody
            .querySelectorAll('button[data-action="return"]')
            .forEach((button) => {
                button.disabled = busy;
            });
            
        reloadButton.disabled = busy;
        addLoanButton.disabled = busy || !hasLoaded;

        previousButton.disabled =
            busy || !hasLoaded || currentPage <= 1;

        nextButton.disabled =
            busy || !hasLoaded || currentPage >= lastPage;

        filterForm
            .querySelectorAll('input, select, button')
            .forEach((element) => {
                element.disabled = busy || !hasLoaded;
            });

        loanForm
            .querySelectorAll('input, select')
            .forEach((element) => {
                element.disabled = loadingOptions || savingLoan;
            });

        saveLoanButton.disabled = busy || !optionsReady;
        saveLoanButton.textContent = savingLoan
            ? 'Menyimpan...'
            : 'Simpan Peminjaman';

        loanModalElement
            .querySelectorAll('[data-bs-dismiss="modal"]')
            .forEach((button) => {
                button.disabled = loadingOptions || savingLoan;
            });

        reloadButton.innerHTML = loadingLoans
            ? '<i class="bi bi-arrow-clockwise me-1"></i> Memuat...'
            : '<i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang';
    }

    function getDueTimestamp(loan) {
        if (!loan.due_at) return null;

        const timestamp = new Date(loan.due_at).getTime();

        return Number.isFinite(timestamp) ? timestamp : null;
    }

    function getCondition(loan, now) {
        const dueTimestamp = getDueTimestamp(loan);

        if (dueTimestamp === null) {
            return 'unknown';
        }

        return dueTimestamp < now ? 'overdue' : 'on_time';
    }

    function displayDate(value) {
        if (!value) return '-';

        const timestamp = new Date(value).getTime();

        if (!Number.isFinite(timestamp)) {
            return '-';
        }

        return formatDate(value);
    }

    function getFilteredLoans(now) {
        const search = appliedFilters.search
            .toLocaleLowerCase('id-ID');

        return allLoans.filter((loan) => {
            const matchesCondition =
                !appliedFilters.condition ||
                getCondition(loan, now) === appliedFilters.condition;

            const matchesSearch = !search || [
                loan.member?.member_code,
                loan.member?.name,
                loan.book?.title,
                loan.book?.isbn,
            ].some((value) => {
                return String(value ?? '')
                    .toLocaleLowerCase('id-ID')
                    .includes(search);
            });

            return matchesCondition && matchesSearch;
        });
    }

    function renderLoans(page = currentPage) {
        const now = Date.now();
        const filteredLoans = getFilteredLoans(now);

        const total = filteredLoans.length;
        const perPage = appliedFilters.per_page;

        lastPage = Math.max(1, Math.ceil(total / perPage));
        currentPage = Math.min(Math.max(1, page), lastPage);

        const start = (currentPage - 1) * perPage;
        const visibleLoans = filteredLoans.slice(
            start,
            start + perPage
        );

        if (total === 0) {
            loansBody.innerHTML = `
                <tr>
                    <td colspan="7"
                        class="text-center text-secondary py-4">
                        Peminjaman aktif tidak ditemukan.
                    </td>
                </tr>`;

            summary.textContent =
                'Tidak ada peminjaman aktif yang sesuai.';

            updateControls();
            return;
        }

        loansBody.innerHTML = visibleLoans.map((loan) => {
    const condition = getCondition(loan, now);

    let conditionBadge =
        '<span class="badge text-bg-secondary">Tanggal tidak tersedia</span>';

    if (condition === 'overdue') {
        conditionBadge = '<span class="badge text-bg-danger">Terlambat</span>';
    } else if (condition === 'on_time') {
        conditionBadge = '<span class="badge text-bg-success">Belum terlambat</span>';
    }

    return `
        <tr>
            <td class="text-nowrap">
                #${escapeHtml(loan.id)}
            </td>
            <td class="loan-member-cell">
                <div class="fw-semibold">
                    ${escapeHtml(loan.member?.name || '-')}
                </div>
                <div class="text-secondary small mt-1">
                    ${escapeHtml(loan.member?.member_code || '-')}
                </div>
            </td>
            <td class="loan-book-cell">
                <div class="fw-semibold">
                    ${escapeHtml(loan.book?.title || '-')}
                </div>
                <div class="text-secondary small mt-1">
                    ISBN: ${escapeHtml(loan.book?.isbn || '-')}
                </div>
            </td>
            <td class="loan-date-cell">
                ${escapeHtml(displayDate(loan.borrowed_at))}
            </td>
            <td class="loan-date-cell">
                ${escapeHtml(displayDate(loan.due_at))}
            </td>
            <td>
                ${conditionBadge}
            </td>
            
        </tr>`;
}).join('');

        const from = start + 1;
        const to = start + visibleLoans.length;

        summary.textContent =
            `Menampilkan ${from}–${to} dari ${total} peminjaman` +
            ` · Halaman ${currentPage}/${lastPage}`;

        updateControls();
    }

    async function loadLoans(
        page = currentPage,
        { clearAlert = true } = {}
    ) {
            if (
                    loadingLoans
                    || loadingOptions
                    || savingLoan
                    || returningLoan
                ) {
                    return false;
                }

        if (!Auth.getToken() || Auth.getRole() !== 'admin') {
            return false;
        }

        loadingLoans = true;
        updateControls();

        const alertBox = document.getElementById('alert');

        if (clearAlert && alertBox) {
            alertBox.replaceChildren();
        }

        try {
            const response = await apiFetch(
                '/loans?status=borrowed'
            );

            if (!Array.isArray(response.data)) {
                throw new Error(
                    'Format respons daftar peminjaman tidak sesuai.'
                );
            }

            allLoans = response.data;
            hasLoaded = true;

            renderLoans(page);

            return true;
        } catch (error) {
            showAlert(
                `Daftar peminjaman gagal dimuat: ${formatErrors(error)}`
            );

            if (!hasLoaded) {
                loansBody.innerHTML = `
                    <tr>
                        <td colspan="6"
                            class="text-center text-secondary py-4">
                            Daftar peminjaman gagal dimuat.
                            Klik Muat Ulang untuk mencoba kembali.
                        </td>
                    </tr>`;

                summary.textContent = '-';
            }

            return false;
        } finally {
            loadingLoans = false;
            updateControls();
        }
    }

    function applyFilters() {
        if (isBusy() || !hasLoaded) return;

        appliedFilters = {
            search: searchInput.value.trim(),
            condition: conditionSelect.value,
            per_page: Number(perPageSelect.value),
        };

        renderLoans(1);
    }

    function clearLoanFormError() {
        loanFormError.textContent = '';
        loanFormError.classList.add('d-none');
    }

    function displayLoanFormError(message) {
        loanFormError.textContent = message;
        loanFormError.classList.remove('d-none');
    }

    function toDateInputValue(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function resetLoanOptions() {
        memberSelect.replaceChildren(
            new Option('Pilih anggota aktif', '')
        );

        bookSelect.replaceChildren(
            new Option('Pilih buku tersedia', '')
        );
    }

    async function fetchBookOptions() {
        const books = [];

        let page = 1;
        let lastPage = 1;

        do {
            const response = await apiFetch(
                `/books?per_page=25&page=${page}`
            );

            if (!Array.isArray(response.data)) {
                throw new Error('Format daftar buku tidak sesuai.');
            }

            lastPage = Number(response.meta?.last_page);

            if (!Number.isInteger(lastPage) || lastPage < page) {
                throw new Error(
                    'Metadata pagination buku tidak sesuai.'
                );
            }

            books.push(...response.data);
            page++;
        } while (page <= lastPage);

        // Hindari pilihan ganda jika data bergeser selama pengambilan halaman.
        return Array.from(
            new Map(
                books.map((book) => [String(book.id), book])
            ).values()
        );
    }

    async function openLoanModal() {
        if (isBusy() || !hasLoaded) return;

        loanForm.reset();
        clearLoanFormError();
        resetLoanOptions();

        optionsReady = false;
        loadingOptions = true;

        const today = new Date();
        const defaultDueDate = new Date(today);

        defaultDueDate.setDate(defaultDueDate.getDate() + 7);

        dueInput.min = toDateInputValue(today);
        dueInput.value = toDateInputValue(defaultDueDate);

        optionsStatus.textContent =
            'Memuat anggota dan buku tersedia...';

        updateControls();
        loanModal.show();

        try {
            const [membersResponse, books] = await Promise.all([
                apiFetch('/members'),
                fetchBookOptions(),
            ]);

            if (!Array.isArray(membersResponse.data)) {
                throw new Error(
                    'Format daftar anggota tidak sesuai.'
                );
            }

            const activeMembers = membersResponse.data.filter(
                (member) => member.status === 'active'
            );

            const availableBooks = books.filter(
                (book) => Number(book.stock) > 0
            );

            activeMembers.forEach((member) => {
                memberSelect.add(new Option(
                    `${member.member_code} — ${member.name}`,
                    String(member.id)
                ));
            });

            availableBooks.forEach((book) => {
                bookSelect.add(new Option(
                    `${book.title} — Stok: ${book.stock}`,
                    String(book.id)
                ));
            });

            optionsReady =
                activeMembers.length > 0 &&
                availableBooks.length > 0;

            optionsStatus.textContent =
                `${activeMembers.length} anggota aktif · ` +
                `${availableBooks.length} judul buku tersedia`;

            if (!optionsReady) {
                displayLoanFormError(
                    'Peminjaman membutuhkan anggota aktif ' +
                    'dan buku tersedia.'
                );
            }
        } catch (error) {
            optionsStatus.textContent = 'Pilihan gagal dimuat.';

            displayLoanFormError(
                `${formatErrors(error)} ` +
                'Tutup modal dan coba kembali.'
            );
        } finally {
            loadingOptions = false;
            updateControls();
        }
    }

    filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
    });

    conditionSelect.addEventListener('change', applyFilters);
    perPageSelect.addEventListener('change', applyFilters);

    previousButton.addEventListener('click', () => {
        if (!isBusy() && hasLoaded) {
            renderLoans(currentPage - 1);
        }
    });

    nextButton.addEventListener('click', () => {
        if (!isBusy() && hasLoaded) {
            renderLoans(currentPage + 1);
        }
    });

    reloadButton.addEventListener('click', () => {
        if (!isBusy()) {
            loadLoans(currentPage);
        }
    });

    addLoanButton.addEventListener('click', openLoanModal);

    loanModalElement.addEventListener('hide.bs.modal', (event) => {
        if (loadingOptions || savingLoan) {
            event.preventDefault();
        }
    });

    loanModalElement.addEventListener('hidden.bs.modal', () => {
        loanForm.reset();
        clearLoanFormError();

        optionsReady = false;
        updateControls();
    });

    loanForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isBusy() || !optionsReady) return;
        if (!loanForm.reportValidity()) return;

        clearLoanFormError();

        const payload = {
            member_id: Number(memberSelect.value),
            book_id: Number(bookSelect.value),
            due_at: dueInput.value,
        };

        if (
            !Number.isInteger(payload.member_id) ||
            payload.member_id < 1 ||
            !Number.isInteger(payload.book_id) ||
            payload.book_id < 1
        ) {
            displayLoanFormError(
                'Pilih anggota dan buku yang valid.'
            );

            return;
        }

        savingLoan = true;
        updateControls();

        let response;

        try {
            response = await apiFetch('/loans', {
                method: 'POST',
                body: JSON.stringify(payload),
            });
        } catch (error) {
            displayLoanFormError(formatErrors(error));
            return;
        } finally {
            savingLoan = false;
            updateControls();
        }

        // Penyimpanan sudah berhasil; proses berikut hanya refresh tampilan.
        optionsReady = false;
        updateControls();

        loanModal.hide();

        const successMessage =
            response.message || 'Peminjaman berhasil disimpan.';

        showAlert(successMessage, 'success');

        const refreshed = await loadLoans(1, {
            clearAlert: false,
        });

        if (!refreshed) {
            showAlert(
                'Peminjaman berhasil disimpan, tetapi daftar belum ' +
                'berhasil diperbarui. Klik Muat Ulang; jangan membuat ' +
                'peminjaman yang sama lagi.',
                'warning'
            );
        }
    });

    loansBody.addEventListener('click', async (event) => {
    const button = event.target.closest(
        'button[data-action="return"]'
    );

    if (!button || button.disabled || isBusy()) return;

    const id = Number(button.dataset.id);

    if (!Number.isInteger(id) || id < 1) {
        showAlert('ID peminjaman tidak valid.');
        return;
    }

    const loan = allLoans.find(
        (item) => Number(item.id) === id
    );

    if (!loan || loan.status !== 'borrowed') {
        showAlert(
            'Peminjaman aktif tidak ditemukan. Klik Muat Ulang.'
        );

        return;
    }

    const memberName = loan.member?.name || 'anggota';
    const bookTitle = loan.book?.title || 'buku ini';

    const approved = window.confirm(
        `Catat pengembalian "${bookTitle}" dari ${memberName}?\n\n` +
        'Lanjutkan hanya jika buku sudah diterima. ' +
        'Stok buku akan bertambah satu.'
    );

    if (!approved) return;

    returningLoan = true;
    updateControls();

    button.textContent = 'Memproses...';

    let response;

    try {
        response = await apiFetch(`/loans/${id}/return`, {
    method: 'PUT',
    skipAuthRedirect: true
});
    } catch (error) {
        showAlert(formatErrors(error));
        return;
    } finally {
        returningLoan = false;

        button.innerHTML =
            '<i class="bi bi-check-circle me-1"></i> Kembalikan';

        updateControls();
    }

    // API berhasil; keluarkan loan dari daftar aktif lokal.
    allLoans = allLoans.filter(
        (item) => Number(item.id) !== id
    );

    renderLoans(currentPage);

    showAlert(
        response.message || 'Buku berhasil dikembalikan.',
        'success'
    );

    const refreshed = await loadLoans(currentPage, {
        clearAlert: false,
    });

    if (!refreshed) {
        showAlert(
            'Pengembalian berhasil, tetapi daftar belum berhasil ' +
            'dimuat ulang. Klik Muat Ulang; jangan mengulang ' +
            'pengembalian yang sudah berhasil.',
            'warning'
        );
    }
});

    updateControls();
    loadLoans(1);
})();
</script>
@endpush