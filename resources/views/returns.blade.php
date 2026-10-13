    @extends('layouts.app')

@section('title', 'Pengembalian Buku - LibraSys')

@push('styles')
<style>
    .panel-dark {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 24px;
    }
    .form-control-dark, .form-select-dark {
        background-color: #141414;
        border: 1px solid #333333;
        color: #ffffff;
        border-radius: 8px;
    }
    .form-control-dark:focus, .form-select-dark:focus {
        background-color: #141414;
        border-color: #555555;
        color: #ffffff;
        box-shadow: none;
    }
.table-dark-custom {
    --bs-table-bg: #1f1f1f;
    --bs-table-color: #ffffff;
    --bs-table-border-color: #333333;
    --bs-table-striped-color: #ffffff;
    --bs-table-hover-color: #ffffff;
    --bs-table-active-color: #ffffff;
    color: #ffffff;
}

.table-dark-custom th {
    background-color: #181818;
    color: #a0a0a0;
    font-size: 0.875rem;
}

.table-dark-custom tbody td {
    color: #ffffff;
}

.table-dark-custom tbody .text-secondary {
    color: #ffffff !important;
}
</style>
@endpush

@section('content')
<div class="mb-4">
    <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">Pengembalian Buku</h1>
    <p class="text-secondary" style="font-size: 0.95rem;">Proses pengembalian buku dan pembaruan stok otomatis.</p>
</div>

<div id="alert"></div>

<!-- Form Cari Cepat Peminjaman -->
<div class="panel-dark mb-4">
    <h2 class="h6 fw-bold mb-3">Cari Transaksi Peminjaman</h2>
    <form id="search-loan-form" class="row g-3">
        <div class="col-md-9">
            <input type="text" id="loan-query" class="form-control form-control-dark" placeholder="Masukkan ID Loan, Kode Anggota, atau ISBN Buku..." required>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-light w-100 fw-semibold">
                <i class="bi bi-search me-1"></i> Cari Data
            </button>
        </div>
    </form>

    <!-- Detail Hasil Pencarian -->
    <div id="search-result" class="mt-4 d-none border-top border-secondary pt-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="h5 fw-bold text-white mb-1" id="res-book-title">-</h3>
                <p class="text-secondary mb-1">Peminjam: <span class="text-white" id="res-member-name">-</span> (<span id="res-member-code">-</span>)</p>
                <p class="text-secondary mb-0">Tgl Pinjam: <span id="res-borrow-date">-</span> | Jatuh Tempo: <span id="res-due-date">-</span></p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" id="confirm-return-btn" class="btn btn-success btn-lg fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Diproses Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Daftar Peminjaman Aktif -->
<div class="panel-dark">
    <h2 class="h6 fw-bold mb-3">Daftar Peminjaman Aktif</h2>
    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
            <thead>
                <tr>
                    <th>ID Loan</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody id="active-loans-body">
                <tr><td colspan="6" class="text-center text-secondary py-3">Memuat peminjaman aktif...</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeLoans = [];
    let selectedLoanId = null;

    async function loadActiveLoans() {
        try {
            const response = await apiFetch('/loans?status=borrowed');
            activeLoans = response.data || [];
            renderTable(activeLoans);
        } catch (error) {
            showAlert(`Gagal memuat peminjaman: ${formatErrors(error)}`);
        }
    }

    function renderTable(loans) {
        const body = document.getElementById('active-loans-body');
        if (loans.length === 0) {
            body.innerHTML = '<tr><td colspan="6" class="text-center text-secondary py-3">Tidak ada peminjaman aktif.</td></tr>';
            return;
        }

        body.innerHTML = loans.map(loan => `
            <tr>
                <td>#${loan.id}</td>
                <td>
                    <div class="fw-semibold">${escapeHtml(loan.member?.name || '-')}</div>
                    <div class="small text-secondary">${escapeHtml(loan.member?.member_code || '-')}</div>
                </td>
                <td>
                    <div class="fw-semibold">${escapeHtml(loan.book?.title || '-')}</div>
                    <div class="small text-secondary">ISBN: ${escapeHtml(loan.book?.isbn || '-')}</div>
                </td>
                <td>${formatDate(loan.borrowed_at)}</td>
                <td>${formatDate(loan.due_at)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="processReturn(${loan.id}, '${escapeHtml(loan.book?.title)}', '${escapeHtml(loan.member?.name)}')">
                        <i class="bi bi-check-circle me-1"></i> Kembalikan
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Cari cepat berdasarkan pencarian manual
    document.getElementById('search-loan-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const query = document.getElementById('loan-query').value.trim().toLowerCase();
        
        const found = activeLoans.find(l => 
            String(l.id) === query ||
            l.member?.member_code?.toLowerCase() === query ||
            l.book?.isbn?.toLowerCase() === query
        );

        const resultBox = document.getElementById('search-result');
        if (found) {
            selectedLoanId = found.id;
            document.getElementById('res-book-title').textContent = found.book?.title || '-';
            document.getElementById('res-member-name').textContent = found.member?.name || '-';
            document.getElementById('res-member-code').textContent = found.member?.member_code || '-';
            document.getElementById('res-borrow-date').textContent = formatDate(found.borrowed_at);
            document.getElementById('res-due-date').textContent = formatDate(found.due_at);
            resultBox.classList.remove('d-none');
        } else {
            resultBox.classList.add('d-none');
            showAlert('Data peminjaman aktif tidak ditemukan untuk kata kunci tersebut.', 'warning');
        }
    });

    document.getElementById('confirm-return-btn').addEventListener('click', () => {
        if (selectedLoanId) {
            const loan = activeLoans.find(l => l.id === selectedLoanId);
            processReturn(selectedLoanId, loan?.book?.title, loan?.member?.name);
        }
    });

    // Eksekusi API Return
    async function processReturn(id, bookTitle = 'Buku', memberName = 'Anggota') {
        if (!confirm(`Konfirmasi pengembalian "${bookTitle}" dari ${memberName}?`)) return;

        try {
            const response = await apiFetch(`/loans/${id}/return`, {
                method: 'PUT',
                skipAuthRedirect: true
            });

            showAlert(response.message || 'Buku berhasil dikembalikan!', 'success');
            document.getElementById('search-result').classList.add('d-none');
            document.getElementById('search-loan-form').reset();
            loadActiveLoans();
        } catch (error) {
            showAlert(`Pengembalian gagal: ${formatErrors(error)}`, 'danger');
        }
    }

    loadActiveLoans();
</script>
@endpush