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
        <!-- Card 1: Total Books -->
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
        <!-- Card 4: Available Books -->
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
    async function loadDashboard() {
        try {
            const [books, members, loans] = await Promise.all([
                apiFetch('/books?per_page=100'),
                apiFetch('/members'),
                apiFetch('/loans?status=borrowed'),
            ]);

            document.getElementById('total-books').textContent = books.meta.total;
            document.getElementById('available-books').textContent =
                books.data.filter((book) => book.stock > 0).length;
            document.getElementById('total-members').textContent = members.data.length;
            document.getElementById('active-loans').textContent = loans.data.length;
        } catch (error) {
            showAlert(error.message);
        }
    }

    loadDashboard();
</script>
@endpush