@extends('layouts.app')

@section('title', 'Members - LibraSys')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Members</h1>
            <p class="text-secondary mb-0">
                Kelola data anggota perpustakaan.
            </p>
        </div>

        <button type="button"
                id="add-member-btn"
                class="btn btn-light"
                disabled>
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Anggota
        </button>
    </div>

    <div class="members-panel">
        <form id="members-filter-form" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="members-search" class="form-label small">
                    Pencarian
                </label>

                <input type="search"
                       id="members-search"
                       class="form-control"
                       placeholder="Kode, nama, email, atau telepon">
            </div>

            <div class="col-md-3">
                <label for="members-status" class="form-label small">
                    Status
                </label>

                <select id="members-status" class="form-select">
                    <option value="">Semua status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="members-per-page" class="form-label small">
                    Per halaman
                </label>

                <select id="members-per-page" class="form-select">
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
            <table class="table members-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode Anggota</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody id="members-body">
                    <tr>
                        <td colspan="6"
                            class="text-center text-secondary py-4">
                            Memuat anggota...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
            <span id="members-summary" class="text-secondary small">-</span>

            <div class="btn-group">
                <button type="button"
                        id="members-prev"
                        class="btn btn-outline-secondary btn-sm"
                        disabled>
                    Sebelumnya
                </button>

                <button type="button"
                        id="members-next"
                        class="btn btn-outline-secondary btn-sm"
                        disabled>
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    {{-- Modal tambah / ubah anggota --}}
    <div class="modal fade members-modal"
         id="member-modal"
         tabindex="-1"
         aria-labelledby="member-modal-title"
         aria-hidden="true"
         data-bs-theme="dark"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="member-form">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="member-modal-title">
                            Tambah Anggota
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div id="member-form-error"
                             class="alert alert-danger d-none"
                             role="alert"></div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="member-code" class="form-label">
                                    Kode Anggota
                                </label>

                                <input type="text"
                                       id="member-code"
                                       class="form-control"
                                       maxlength="50"
                                       required>
                            </div>

                            <div class="col-md-8">
                                <label for="member-name" class="form-label">
                                    Nama
                                </label>

                                <input type="text"
                                       id="member-name"
                                       class="form-control"
                                       maxlength="255"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="member-email" class="form-label">
                                    Email
                                </label>

                                <input type="email"
                                       id="member-email"
                                       class="form-control"
                                       maxlength="255"
                                       placeholder="Opsional">
                            </div>

                            <div class="col-md-6">
                                <label for="member-phone" class="form-label">
                                    Telepon
                                </label>

                                <input type="tel"
                                       id="member-phone"
                                       class="form-control"
                                       maxlength="20"
                                       placeholder="Opsional">
                            </div>

                            <div class="col-12">
                                <label for="member-address" class="form-label">
                                    Alamat
                                </label>

                                <textarea id="member-address"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Opsional"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="member-status" class="form-label">
                                    Status
                                </label>

                                <select id="member-status"
                                        class="form-select"
                                        required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
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
                                id="save-member-btn"
                                class="btn btn-light">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal ganti kata sandi akun anggota --}}
    <div class="modal fade members-modal"
         id="member-password-modal"
         tabindex="-1"
         aria-labelledby="member-password-modal-title"
         aria-hidden="true"
         data-bs-theme="dark"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="member-password-form">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5"
                            id="member-password-modal-title">
                            Ganti Kata Sandi Anggota
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-secondary mb-3">
                            Akun anggota:
                            <span id="password-member-name"
                                  class="text-light"></span>
                        </p>

                        <div id="member-password-error"
                             class="alert alert-danger d-none"
                             role="alert"></div>

                        <div class="mb-3">
                            <label for="reset-admin-password"
                                   class="form-label">
                                Kata Sandi Admin Saat Ini
                            </label>

                            <input type="password"
                                   id="reset-admin-password"
                                   class="form-control"
                                   autocomplete="current-password"
                                   required>

                            <div class="form-text">
                                Masukkan kata sandi akun admin Anda,
                                bukan kata sandi anggota.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reset-member-password"
                                   class="form-label">
                                Kata Sandi Baru Anggota
                            </label>

                            <input type="password"
                                   id="reset-member-password"
                                   class="form-control"
                                   autocomplete="new-password"
                                   minlength="15"
                                   maxlength="64"
                                   required>

                            <div class="form-text">
                                Gunakan 15–64 karakter.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reset-member-confirmation"
                                   class="form-label">
                                Konfirmasi Kata Sandi Baru
                            </label>

                            <input type="password"
                                   id="reset-member-confirmation"
                                   class="form-control"
                                   autocomplete="new-password"
                                   minlength="15"
                                   maxlength="64"
                                   required>
                        </div>

                        <p class="small text-secondary mb-0">
                            Perubahan ini tidak otomatis mengakhiri
                            sesi anggota pada perangkat lain.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                                id="save-member-password-btn"
                                class="btn btn-light">
                            Ganti Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .members-panel {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 24px;
    }

    .members-panel .form-control,
    .members-panel .form-select,
    .members-modal .form-control,
    .members-modal .form-select {
        background-color: #141414;
        border-color: #333333;
        color: #ffffff;
        color-scheme: dark;
    }

    .members-panel .form-control::placeholder,
    .members-modal .form-control::placeholder {
        color: #888888;
    }

    .members-panel .form-control:focus,
    .members-panel .form-select:focus,
    .members-modal .form-control:focus,
    .members-modal .form-select:focus {
        background-color: #141414;
        border-color: #777777;
        color: #ffffff;
        box-shadow: none;
    }

    .members-table {
        --bs-table-bg: #1f1f1f;
        --bs-table-color: #ffffff;
        --bs-table-border-color: #333333;
    }

    .members-table th {
        background-color: #181818;
        color: #a0a0a0;
        font-size: 0.875rem;
        padding: 14px 12px;
        white-space: nowrap;
    }

    .members-table td {
        padding: 14px 12px;
    }

    .members-table .member-address-cell {
        min-width: 180px;
        max-width: 280px;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .members-table .member-contact-cell {
        min-width: 180px;
        overflow-wrap: anywhere;
    }

    .members-modal .modal-content {
        background-color: #1f1f1f;
        color: #ffffff;
        border: 1px solid #333333;
        border-radius: 12px;
    }

    .members-modal .modal-header,
    .members-modal .modal-footer {
        border-color: #333333;
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const membersBody = document.getElementById('members-body');
    const summary = document.getElementById('members-summary');
    const previousButton = document.getElementById('members-prev');
    const nextButton = document.getElementById('members-next');
    const filterForm = document.getElementById('members-filter-form');
    const addButton = document.getElementById('add-member-btn');

    const memberForm = document.getElementById('member-form');
    const formError = document.getElementById('member-form-error');
    const saveButton = document.getElementById('save-member-btn');
    const modalElement = document.getElementById('member-modal');
    const memberModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    const passwordModalElement =
        document.getElementById('member-password-modal');

    const passwordModal = bootstrap.Modal.getOrCreateInstance(
        passwordModalElement
    );

    const passwordForm = document.getElementById('member-password-form');
    const passwordError = document.getElementById('member-password-error');
    const passwordSaveButton =
        document.getElementById('save-member-password-btn');

    let allMembers = [];
    let currentPage = 1;
    let lastPage = 1;

    let loadingMembers = false;
    let savingMember = false;
    let savingPassword = false;
    let actionBusy = false;

    let editingMemberId = null;
    let passwordMemberId = null;

    let appliedFilters = {
        search: '',
        status: '',
        per_page: 10,
    };

    function isBusy() {
        return loadingMembers || savingMember || savingPassword || actionBusy;
    }

    function updateControls() {
        const busy = isBusy();

        addButton.disabled = busy;
        previousButton.disabled = busy || currentPage <= 1;
        nextButton.disabled = busy || currentPage >= lastPage;

        filterForm.querySelectorAll('input, select, button').forEach((element) => {
            element.disabled = busy;
        });

        membersBody.querySelectorAll('button[data-action]').forEach((button) => {
            const unavailable =
                button.dataset.action === 'password'
                && button.dataset.hasAccount !== 'true';

            button.disabled = busy || unavailable;
        });

        saveButton.disabled = savingMember;

        modalElement.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
            button.disabled = savingMember;
        });

        passwordSaveButton.disabled = savingPassword;

        passwordModalElement
            .querySelectorAll('[data-bs-dismiss="modal"]')
            .forEach((button) => {
                button.disabled = savingPassword;
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

    function clearPasswordError() {
        passwordError.textContent = '';
        passwordError.classList.add('d-none');
    }

    function displayPasswordError(message) {
        passwordError.textContent = message;
        passwordError.classList.remove('d-none');
    }

    function getFilteredMembers() {
        const search = appliedFilters.search.toLocaleLowerCase('id-ID');

        return allMembers.filter((member) => {
            const matchesStatus = !appliedFilters.status
                || member.status === appliedFilters.status;

            const matchesSearch = !search || [
                member.member_code,
                member.name,
                member.email,
                member.phone,
            ].some((value) => {
                return String(value ?? '')
                    .toLocaleLowerCase('id-ID')
                    .includes(search);
            });

            return matchesStatus && matchesSearch;
        });
    }

    function renderMembers(page = currentPage) {
        const filteredMembers = getFilteredMembers();
        const total = filteredMembers.length;
        const perPage = appliedFilters.per_page;

        lastPage = Math.max(1, Math.ceil(total / perPage));
        currentPage = Math.min(Math.max(1, page), lastPage);

        const start = (currentPage - 1) * perPage;
        const visibleMembers = filteredMembers.slice(start, start + perPage);

        if (total === 0) {
            membersBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        Anggota tidak ditemukan.
                    </td>
                </tr>`;

            summary.textContent = 'Tidak ada anggota yang sesuai.';
            updateControls();
            return;
        }

        membersBody.innerHTML = visibleMembers.map((member) => {
            const statusBadge = member.status === 'active'
                ? '<span class="badge text-bg-success">Aktif</span>'
                : '<span class="badge text-bg-secondary">Nonaktif</span>';

            const hasAccount = member.has_account === true;

            return `
                <tr>
                    <td class="text-nowrap">
                        ${escapeHtml(member.member_code)}
                    </td>
                    <td>
                        <div class="fw-semibold member-name-text">
                            ${escapeHtml(member.name)}
                        </div>
                        <div class="text-secondary small mt-1">
                            ${hasAccount ? 'Memiliki akun login' : 'Belum memiliki akun login'}
                        </div>
                    </td>
                    <td class="member-contact-cell">
                        <div>${escapeHtml(member.email || '-')}</div>
                        <div class="text-secondary small mt-1">
                            ${escapeHtml(member.phone || '-')}
                        </div>
                    </td>
                    <td class="member-address-cell">${escapeHtml(member.address || '-')}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end text-nowrap">
                        <button type="button"
                                class="btn btn-outline-light btn-sm"
                                data-action="edit"
                                data-id="${Number(member.id)}">
                            <i class="bi bi-pencil me-1"></i>
                            Ubah
                        </button>

                        <button type="button"
                                class="btn btn-outline-danger btn-sm ms-1"
                                data-action="delete"
                                data-id="${Number(member.id)}">
                            <i class="bi bi-trash me-1"></i>
                            Hapus
                        </button>

                        <button type="button"
                                class="btn btn-outline-warning btn-sm ms-1"
                                data-action="password"
                                data-id="${Number(member.id)}"
                                data-has-account="${hasAccount}"
                                title="${hasAccount
                                    ? 'Ganti kata sandi akun anggota'
                                    : 'Anggota belum memiliki akun login'}"
                                ${hasAccount ? '' : 'disabled'}>
                            <i class="bi bi-key me-1"></i>
                            Kata Sandi
                        </button>
                    </td>
                </tr>`;
        }).join('');

        const from = start + 1;
        const to = start + visibleMembers.length;

        summary.textContent =
            `Menampilkan ${from}–${to} dari ${total} anggota · Halaman ${currentPage}/${lastPage}`;

        updateControls();
    }

    async function loadMembers(page = currentPage) {
        if (loadingMembers) return;

        loadingMembers = true;
        updateControls();

        try {
            const response = await apiFetch('/members');

            if (!Array.isArray(response.data)) {
                throw new Error('Format respons daftar anggota tidak sesuai.');
            }

            allMembers = response.data;
            renderMembers(page);
        } catch (error) {
            showAlert(`Daftar anggota gagal dimuat: ${formatErrors(error)}`);

            if (!membersBody.querySelector('button[data-action]')) {
                membersBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            Daftar anggota gagal dimuat. Muat ulang halaman.
                        </td>
                    </tr>`;

                summary.textContent = '-';
            }
        } finally {
            loadingMembers = false;
            updateControls();
        }
    }

    function applyFilters() {
        if (isBusy()) return;

        appliedFilters = {
            search: document.getElementById('members-search').value.trim(),
            status: document.getElementById('members-status').value,
            per_page: Number(document.getElementById('members-per-page').value),
        };

        renderMembers(1);
    }

    function openMemberModal(member = null) {
        memberForm.reset();
        clearFormError();

        editingMemberId = member ? member.id : null;

        document.getElementById('member-modal-title').textContent =
            member ? 'Ubah Anggota' : 'Tambah Anggota';

        document.getElementById('member-code').value = member?.member_code ?? '';
        document.getElementById('member-name').value = member?.name ?? '';
        document.getElementById('member-email').value = member?.email ?? '';
        document.getElementById('member-phone').value = member?.phone ?? '';
        document.getElementById('member-address').value = member?.address ?? '';
        document.getElementById('member-status').value = member?.status ?? 'active';

        saveButton.textContent = 'Simpan';
        updateControls();
        memberModal.show();
    }

    function openPasswordModal(member) {
        passwordForm.reset();
        clearPasswordError();

        passwordMemberId = Number(member.id);

        document.getElementById('password-member-name').textContent =
            `${member.member_code} — ${member.name}`;

        passwordSaveButton.textContent = 'Ganti Kata Sandi';
        updateControls();
        passwordModal.show();
    }

    filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
    });

    document.getElementById('members-status').addEventListener(
        'change',
        applyFilters
    );

    document.getElementById('members-per-page').addEventListener(
        'change',
        applyFilters
    );

    previousButton.addEventListener('click', () => {
        if (!isBusy()) renderMembers(currentPage - 1);
    });

    nextButton.addEventListener('click', () => {
        if (!isBusy()) renderMembers(currentPage + 1);
    });

    addButton.addEventListener('click', () => {
        if (!isBusy()) openMemberModal();
    });

    modalElement.addEventListener('shown.bs.modal', () => {
        document.getElementById('member-code').focus();
    });

    modalElement.addEventListener('hide.bs.modal', (event) => {
        if (savingMember) {
            event.preventDefault();
        }
    });

    memberForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isBusy()) return;
        if (!memberForm.reportValidity()) return;

        clearFormError();

        const email = document.getElementById('member-email').value.trim();
        const phone = document.getElementById('member-phone').value.trim();
        const address = document.getElementById('member-address').value.trim();

        const data = {
            member_code: document.getElementById('member-code').value.trim(),
            name: document.getElementById('member-name').value.trim(),
            email: email || null,
            phone: phone || null,
            address: address || null,
            status: document.getElementById('member-status').value,
        };

        if (!data.member_code || !data.name) {
            displayFormError('Kode anggota dan nama wajib diisi.');
            return;
        }

        const isEditing = editingMemberId !== null;
        const path = isEditing
            ? `/members/${editingMemberId}`
            : '/members';

        savingMember = true;
        saveButton.textContent = 'Menyimpan...';
        updateControls();

        try {
            const response = await apiFetch(path, {
                method: isEditing ? 'PUT' : 'POST',
                body: JSON.stringify(data),
            });

            savingMember = false;
            memberModal.hide();

            showAlert(
                response.message || (
                    isEditing
                        ? 'Anggota berhasil diperbarui.'
                        : 'Anggota berhasil ditambahkan.'
                ),
                'success'
            );

            await loadMembers(isEditing ? currentPage : 1);
        } catch (error) {
            displayFormError(formatErrors(error));
        } finally {
            savingMember = false;
            saveButton.textContent = 'Simpan';
            updateControls();
        }
    });

    passwordModalElement.addEventListener('shown.bs.modal', () => {
        document.getElementById('reset-admin-password').focus();
    });

    passwordModalElement.addEventListener('hide.bs.modal', (event) => {
        if (savingPassword) {
            event.preventDefault();
        }
    });

    passwordModalElement.addEventListener('hidden.bs.modal', () => {
        passwordForm.reset();
        passwordMemberId = null;
        clearPasswordError();
    });

    passwordForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (isBusy() || passwordMemberId === null) return;
        if (!passwordForm.reportValidity()) return;

        clearPasswordError();

        // Jangan trim password: spasi dapat menjadi bagian password.
        const adminPassword =
            document.getElementById('reset-admin-password').value;

        const password =
            document.getElementById('reset-member-password').value;

        const confirmation =
            document.getElementById('reset-member-confirmation').value;

        if (password !== confirmation) {
            displayPasswordError('Konfirmasi kata sandi baru tidak cocok.');
            return;
        }

        const targetMemberId = passwordMemberId;

        savingPassword = true;
        passwordSaveButton.textContent = 'Menyimpan...';
        updateControls();

        try {
            const response = await apiFetch(
                `/members/${targetMemberId}/password`,
                {
                    method: 'PUT',
                    body: JSON.stringify({
                        admin_password: adminPassword,
                        password: password,
                        password_confirmation: confirmation,
                    }),
                }
            );

            savingPassword = false;
            passwordForm.reset();
            passwordModal.hide();

            showAlert(
                response.message || 'Kata sandi anggota berhasil diubah.',
                'success'
            );
        } catch (error) {
            document.getElementById('reset-admin-password').value = '';
            displayPasswordError(formatErrors(error));
        } finally {
            savingPassword = false;
            passwordSaveButton.textContent = 'Ganti Kata Sandi';
            updateControls();
        }
    });

    membersBody.addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');

        if (!button || button.disabled || isBusy()) return;

        const id = Number(button.dataset.id);
        const action = button.dataset.action;

        if (action === 'password') {
            const member = allMembers.find((item) => Number(item.id) === id);

            if (!member || member.has_account !== true) {
                showAlert('Anggota ini belum memiliki akun login.');
                return;
            }

            openPasswordModal(member);
            return;
        }

        if (action === 'edit') {
            actionBusy = true;
            updateControls();

            try {
                const response = await apiFetch(`/members/${id}`);
                openMemberModal(response.data);
            } catch (error) {
                showAlert(formatErrors(error));
            } finally {
                actionBusy = false;
                updateControls();
            }

            return;
        }

        if (action !== 'delete') return;

        const name = button.closest('tr')
            .querySelector('.member-name-text').textContent.trim();

        if (!window.confirm(
            `Hapus anggota "${name}"? Tindakan ini tidak dapat dibatalkan.`
        )) {
            return;
        }

        actionBusy = true;
        updateControls();

        try {
            const response = await apiFetch(`/members/${id}`, {
                method: 'DELETE',
            });

            showAlert(
                response.message || 'Anggota berhasil dihapus.',
                'success'
            );

            await loadMembers(currentPage);
        } catch (error) {
            showAlert(formatErrors(error));
        } finally {
            actionBusy = false;
            updateControls();
        }
    });

    loadMembers(1);
})();
</script>
@endpush