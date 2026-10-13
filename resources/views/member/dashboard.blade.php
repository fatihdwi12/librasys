@extends('layouts.member')

@section('title', 'Dashboard - LibraSys')
@section('heading', 'Dashboard')
@section('subheading', 'Selamat Datang Pada Dashboard Librasys')

@section('content')
    <!-- Profile Card -->
    <div class="card-dark mb-4">
        <h2 class="h6 fw-bold mb-3 text-white">Profile Anggota</h2>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-medium mb-1">Nama</label>
                <div class="profile-box" id="profile-name">-</div>
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-medium mb-1">Email</label>
                <div class="profile-box" id="profile-email">-</div>
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-medium mb-1">Kode Anggota</label>
                <div class="profile-box" id="profile-code">-</div>
            </div>
        </div>

        <div class="text-start">
            <label class="form-label text-secondary small fw-medium d-block mb-1">Status</label>
            <div id="profile-status">-</div>
        </div>
    </div>

    <!-- Active Loan Stat Card -->
    <div class="stat-card">
        <div class="stat-header">
            <i class="bi bi-arrow-repeat fs-4"></i>
            <span>Active Loan</span>
        </div>
        <div class="stat-number" id="active-loans-count">-</div>
        <a href="/member/loans" class="d-inline-block mt-3 small text-secondary text-decoration-none">
            Lihat pinjaman saya <i class="bi bi-arrow-right"></i>
        </a>
    </div>
@endsection

@push('scripts')
<script>
    async function loadProfile() {
        const user = await window.currentUser;

        document.getElementById('profile-name').textContent = user.name;
        document.getElementById('profile-email').textContent = user.email;

        if (user.member) {
            document.getElementById('profile-code').textContent = user.member.member_code;

            const isActive = user.member.status === 'active';
            const tone = isActive ? 'success' : 'secondary';
            const label = isActive ? 'Active' : 'Inactive';
            document.getElementById('profile-status').innerHTML =
                `<span class="badge bg-${tone}-subtle text-${tone} border border-${tone}-subtle rounded-pill px-3 py-2">${label}</span>`;
        }
    }

    async function loadActiveLoans() {
        const response = await apiFetch('/my-loans');
        const active = response.data.filter((loan) => loan.status === 'borrowed').length;
        document.getElementById('active-loans-count').textContent = active;
    }

    (async () => {
        try {
            await Promise.all([loadProfile(), loadActiveLoans()]);
        } catch (error) {
            showAlert(error.message);
        }
    })();
</script>
@endpush