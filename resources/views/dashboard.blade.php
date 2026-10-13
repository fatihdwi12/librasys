@extends('layouts.app')

@section('title', 'Dashboard - LibraSys')

@push('styles')
<style>
    /* Styling Kartu Dashboard */
    .dashboard-card {
        background-color: #1f1f1f;
        border: 1px solid #333333;
        border-radius: 12px;
        padding: 24px 28px;
        height: 100%;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .dashboard-card:hover {
        border-color: #444444;
    }

    .card-header-custom {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #cccccc;
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .card-header-custom i {
        font-size: 1.3rem;
    }

    .card-number {
        font-size: 3.2rem;
        font-weight: 500;
        color: #ffffff;
        line-height: 1;
        margin: 0;
    }
</style>
@endpush

@section('content')
    <!-- Header Dashboard -->
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; color: #ffffff;">Dashboard</h1>
        <p class="text-secondary" style="font-size: 0.95rem;">Selamat Datang Pada Dashboard Librasys</p>
    </div>

    <!-- Alert Container -->
    <div id="alert"></div>

    <!-- Grid Cards -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total Books (Judul Buku) -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <i class="bi bi-book"></i>
                    <span>Book</span>
                </div>
                <div class="card-number" id="total-books">-</div>
            </div>
        </div>

        <!-- Card 2: Total Members -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <i class="bi bi-people"></i>
                    <span>Member</span>
                </div>
                <div class="card-number" id="total-members">-</div>
            </div>
        </div>

        <!-- Card 3: Active Loans -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Active Loan</span>
                </div>
                <div class="card-number" id="active-loans">-</div>
            </div>
        </div>
    </div>

    <!-- Row 2 -->
    <div class="row g-4">
        <!-- Card 4: Available Books (Total Stok Eksemplar Tersedia) -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <i class="bi bi-calendar-check"></i>
                    <span>Available Books</span>
                </div>
                <div class="card-number" id="available-books">-</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Helper function untuk mengambil SELURUH data buku melintasi semua halaman paginasi
    async function fetchAllBooks() {
        let allBooks = [];
        let page = 1;
        let lastPage = 1;
        let totalMeta = 0;

        do {
            const response = await apiFetch(`/books?page=${page}&per_page=100`);
            const data = Array.isArray(response.data) ? response.data : [];
            allBooks.push(...data);

            lastPage = Number(response.meta?.last_page || 1);
            totalMeta = Number(response.meta?.total || allBooks.length);
            page++;
        } while (page <= lastPage);

        return {
            total: totalMeta,
            books: allBooks
        };
    }

    async function loadDashboard() {
        try {
            const [booksData, membersResponse, loansResponse] = await Promise.all([
                fetchAllBooks(),
                apiFetch('/members'),
                apiFetch('/loans?status=borrowed'),
            ]);

            const books = booksData.books;
            const members = Array.isArray(membersResponse.data) ? membersResponse.data : [];
            const loans = Array.isArray(loansResponse.data) ? loansResponse.data : [];

            // 1. Total Judul Buku
            document.getElementById('total-books').textContent = booksData.total;

            // 2. Total Anggota
            document.getElementById('total-members').textContent = members.length;

            // 3. Peminjaman Aktif
            document.getElementById('active-loans').textContent = loans.length;

            // 4. Available Books (Total Jumlah Fisik Eksemplar Stok yang Tersedia saat ini)
            const totalAvailableStock = books.reduce((sum, book) => {
                const stock = Number(book.stock);
                return sum + (isNaN(stock) ? 0 : stock);
            }, 0);

            document.getElementById('available-books').textContent = totalAvailableStock;

        } catch (error) {
            showAlert(error.message || 'Gagal memuat data dashboard.');
        }
    }

    loadDashboard();
</script>
@endpush