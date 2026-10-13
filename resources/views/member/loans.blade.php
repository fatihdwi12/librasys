@extends('layouts.member')

@section('title', 'My Loan - LibraSys')
@section('heading', 'My Loan')
@section('subheading', 'Riwayat dan status pinjaman Anda')

@section('content')
    <div class="card-dark">
        <div class="btn-group mb-3" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="all">Semua</button>
            <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-filter="borrowed">Dipinjam</button>
            <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-filter="returned">Dikembalikan</button>
        </div>

        <div class="table-responsive">
            <table class="table table-dark-custom align-middle mb-0">
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
                    <tr><td colspan="5" class="text-secondary">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let allLoans = [];
    let activeFilter = 'all';

    function isOverdue(loan) {
        return loan.status === 'borrowed' && new Date(loan.due_at) < new Date();
    }

    function statusBadge(loan) {
        if (isOverdue(loan)) {
            return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Terlambat</span>';
        }
        if (loan.status === 'borrowed') {
            return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Dipinjam</span>';
        }
        return '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Dikembalikan</span>';
    }

    function renderLoans() {
        const body = document.getElementById('loans-body');
        const loans = activeFilter === 'all'
            ? allLoans
            : allLoans.filter((loan) => loan.status === activeFilter);

        if (loans.length === 0) {
            body.innerHTML = '<tr><td colspan="5" class="text-secondary">Tidak ada data pinjaman.</td></tr>';
            return;
        }

        body.innerHTML = loans.map((loan) => `
            <tr>
                <td>${escapeHtml(loan.book ? loan.book.title : '-')}</td>
                <td>${formatDate(loan.borrowed_at)}</td>
                <td>${formatDate(loan.due_at)}</td>
                <td>${formatDate(loan.returned_at)}</td>
                <td>${statusBadge(loan)}</td>
            </tr>`).join('');
    }

    document.querySelectorAll('.filter-btn').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            activeFilter = button.dataset.filter;
            renderLoans();
        });
    });

    (async () => {
        try {
            const response = await apiFetch('/my-loans');
            allLoans = response.data;
            renderLoans();
        } catch (error) {
            showAlert(error.message);
        }
    })();
</script>
@endpush