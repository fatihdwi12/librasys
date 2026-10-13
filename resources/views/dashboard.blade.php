@extends('layouts.app')

@section('title', 'Dashboard - LibraSys')

@section('content')
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Total Books</div>
                    <div class="display-6" id="total-books">-</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Total Members</div>
                    <div class="display-6" id="total-members">-</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Active Loans</div>
                    <div class="display-6" id="active-loans">-</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Available Books</div>
                    <div class="display-6" id="available-books">-</div>
                </div>
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