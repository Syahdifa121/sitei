<!-- File: resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Pesan Dosen')

@push('styles')
<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #4f46e5;
        --accent-color: #06b6d4;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem auto;
    }

    .stat-title {
        font-size: 1rem;
        color: #6b7280;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }

    .ticket-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .ticket-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .priority-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .priority-urgent {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .priority-normal {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .filter-btn {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .filter-btn.active {
        background: var(--primary-color);
        color: white;
    }

    .filter-btn.urgent {
        color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .filter-btn.urgent.active {
        background-color: var(--danger-color);
        color: white;
    }

    .filter-btn.normal {
        color: var(--success-color);
        border-color: var(--success-color);
    }

    .filter-btn.normal.active {
        background-color: var(--success-color);
        color: white;
    }

    .create-ticket-btn {
        background: linear-gradient(to right, #4ade80, #3b82f6);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .create-ticket-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .search-box {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        padding: 0.75rem 1rem;
    }

    .list-group-item {
        position: relative;
        transition: all 0.3s ease;
        border: none;
        border: none;
        margin-bottom: 30px;
        border-radius: 8px !important;
    }

    .list-group-item:hover {
        background-color: #10b981 !important;
        color: white !important;
    }
    .list-group-item:hover::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    border: 2px solid #10b981; /* Hover box color */
    border-radius: 12px; /* Rounded corners */
    opacity: 0.8;
    transition: all 0.3s ease;
}

.list-group-item.active::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    border: 2px solid #10b981; /* Hover box color for active */
    border-radius: 12px; /* Rounded corners */
    opacity: 0.8;
}

    .list-group-item:hover .badge {
        background-color: white !important;
        color: #10b981 !important;
    }

    .list-group-item.active {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<div class="container py-4">
    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="h6 text-muted">Total Konsultasi</h3>
                <h2 class="h4 mb-0">3</h2>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="h6 text-muted">Konsultasi Selesai</h3>
                <h2 class="h4 mb-0">0</h2>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="h6 text-muted">Konsultasi Aktif</h3>
                <h2 class="h4 mb-0">3</h2>
            </div>
        </div>
    </div>

    <!-- Main Section -->
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button class="create-ticket-btn w-100 mb-4" onclick="window.location.href='http://127.0.0.1:8000/buatpesan'">
                        <i class="fas fa-plus-circle me-2"></i>Buat Konsultasi
                    </button>                        
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action active d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-inbox me-2"></i>Aktif</span>
                            <span class="badge bg-white text-primary rounded-pill">3</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-history me-2"></i>Riwayat</span>
                            <span class="badge bg-light text-dark rounded-pill">0</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="search" class="form-control search-box" placeholder="Cari konsultasi...">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button class="filter-btn btn urgent">Mendesak</button>
                                <button class="filter-btn btn normal">Umum</button>
                                <button class="filter-btn btn btn-outline-primary active">Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="tickets-container" id="active-tickets">
                <!-- Urgent Priority Ticket -->
                <div class="ticket-card" data-priority="mendesak"  onclick="window.location.href='http://127.0.0.1:8000/isipesan'">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/fotodesi.jpeg') }}" alt="Avatar" class="avatar me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Desi Maya Sari</h6>
                            <small class="text-muted">2107110665</small>
                        </div>
                        <span class="priority-badge priority-urgent">
                            <i class="fas fa-arrow-up me-1"></i>Mendesak
                        </span>
                    </div>
                    <h5 class="mb-2">Bimbingan Skripsi</h5>
                    <div class="d-flex align-items-center text-muted">
                        <i class="far fa-clock me-2"></i>
                        <small>08:30 - Hari ini</small>
                    </div>
                </div>

                <!-- Urgent Priority Ticket -->
                <div class="ticket-card" data-priority="mendesak">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/fotosasa.jpg') }}" alt="Avatar" class="avatar me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Syahirah Tri Meilina</h6>
                            <small class="text-muted">2107110255</small>
                        </div>
                        <span class="priority-badge priority-urgent">
                            <i class="fas fa-arrow-up me-1"></i>Mendesak
                        </span>
                    </div>
                    <h5 class="mb-2">Bimbingan Skripsi</h5>
                    <div class="d-flex align-items-center text-muted">
                        <i class="far fa-clock me-2"></i>
                        <small>15:30 - Hari ini</small>
                    </div>
                </div>

                <!-- Normal Priority Ticket -->
                <div class="ticket-card" data-priority="umum" onclick="window.location.href='http://127.0.0.1:8000/isipesan'">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/fotodesi.jpeg') }}" alt="Avatar" class="avatar me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Desi Maya Sari</h6>
                            <small class="text-muted">2107110665</small>
                        </div>
                        <span class="priority-badge priority-normal">
                            <i class="fas fa-minus me-1"></i>Umum
                        </span>
                    </div>
                    <h5 class="mb-2">Bimbingan KRS</h5>
                    <div class="d-flex align-items-center text-muted">
                        <i class="far fa-clock me-2"></i>
                        <small>10:00 - Kemarin</small>
                    </div>
                </div>

                <!-- Normal Priority Ticket -->
                <div class="ticket-card" data-priority="umum">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/fotomurni.jpg') }}" alt="Avatar" class="avatar me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Tri Murniati</h6>
                            <small class="text-muted">2107112735</small>
                        </div>
                        <span class="priority-badge priority-normal">
                            <i class="fas fa-minus me-1"></i>Umum
                        </span>
                    </div>
                    <h5 class="mb-2">Konsultasi MBKM</h5>
                    <div class="d-flex align-items-center text-muted">
                        <i class="far fa-clock me-2"></i>
                        <small>2 hari lalu</small>
                    </div>
                </div>
            </div>
            <!-- Tambahkan container untuk riwayat -->
                <div class="history-container" id="history-tickets" style="display: none;">
                    <!-- Div untuk pesan riwayat kosong -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                                <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-muted">Tidak Ada Riwayat Konsultasi</h5>
                            <p class="text-muted mb-0">Riwayat konsultasi yang telah selesai akan muncul di sini</p>
                        </div>
                    </div>

                    <!-- Pesan pencarian tidak tersedia -->
                    <div id="no-results" class="text-center py-4" style="display: none;">
                        <p class="text-muted">Konsultasi tidak tersedia</p>
                    </div>

                <!-- Tambahkan div ini -->
                    <div id="no-results" class="text-center py-4" style="display: none;">
                        <p class="text-muted">Pencarian konsultasi tidak tersedia</p>
                    </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    const activeTickets = document.getElementById('active-tickets');
    const historyTickets = document.getElementById('history-tickets');
    const noResults = document.getElementById('no-results');

    // Fungsi untuk memeriksa dan menampilkan pesan "tidak tersedia"
    function checkNoResults() {
        const visibleTickets = document.querySelectorAll('.ticket-card[style="display: block;"]');
        const noResults = document.getElementById('no-results');
        const isHistory = document.querySelector('.list-group-item:last-child').classList.contains('active');
        
        // Tampilkan pesan sesuai dengan halaman yang aktif
        if (isHistory) {
            noResults.style.display = 'none';
        } else {
            noResults.style.display = visibleTickets.length === 0 ? 'block' : 'none';
        }
    }

    // Fungsi untuk mengaktifkan filter berdasarkan kategori prioritas
    function filterMessages(priority) {
        const allTickets = document.querySelectorAll('.ticket-card');
        const searchTerm = document.querySelector('.search-box').value.toLowerCase();

        allTickets.forEach(ticket => {
            const ticketPriority = ticket.getAttribute('data-priority');
            const title = ticket.querySelector('h5').textContent.toLowerCase();
            const name = ticket.querySelector('h6').textContent.toLowerCase();
            const nim = ticket.querySelector('small').textContent.toLowerCase();
            
            const matchesSearch = title.includes(searchTerm) || 
                                name.includes(searchTerm) || 
                                nim.includes(searchTerm);
            const matchesPriority = priority === 'semua' || ticketPriority === priority;

            // Tampilkan ticket jika memenuhi kriteria pencarian DAN filter
            ticket.style.display = (matchesSearch && matchesPriority) ? 'block' : 'none';
        });

        checkNoResults();
    }

    // Fungsi untuk menambahkan class 'active' pada tombol yang sedang diklik
    function setActiveFilterButton(button) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    }

    // Event listener untuk menu aktif dan riwayat
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Hapus class active dari semua menu
            document.querySelectorAll('.list-group-item').forEach(i => {
                i.classList.remove('active');
            });
            
            // Tambah class active ke menu yang diklik
            this.classList.add('active');
            
            // Cek apakah yang diklik adalah menu riwayat
            const isHistory = this.textContent.includes('Riwayat');
            
            // Tampilkan container yang sesuai
            activeTickets.style.display = isHistory ? 'none' : 'block';
            historyTickets.style.display = isHistory ? 'block' : 'none';
            
            // Reset pesan tidak tersedia
            noResults.style.display = 'none';
        });
    });

    // Event listener untuk tombol filter
    // Filter Mendesak
    document.querySelector('.filter-btn.urgent').addEventListener('click', function() {
        const isHistory = document.querySelector('.list-group-item:last-child').classList.contains('active');
        if (!isHistory) {
            filterMessages('mendesak');
        }
        setActiveFilterButton(this);
    });

    // Filter Umum
    document.querySelector('.filter-btn.normal').addEventListener('click', function() {
        const isHistory = document.querySelector('.list-group-item:last-child').classList.contains('active');
        if (!isHistory) {
            filterMessages('umum');
        }
        setActiveFilterButton(this);
    });

    // Filter Semua
    document.querySelector('.filter-btn.btn-outline-primary').addEventListener('click', function() {
        const isHistory = document.querySelector('.list-group-item:last-child').classList.contains('active');
        if (!isHistory) {
            filterMessages('semua');
        }
        setActiveFilterButton(this);
    });
    
    // Fungsi pencarian
    document.querySelector('.search-box').addEventListener('input', function(e) {
        const isHistory = document.querySelector('.list-group-item:last-child').classList.contains('active');
        if (!isHistory) {
            const activeFilter = document.querySelector('.filter-btn.active');
            const filterType = activeFilter.classList.contains('urgent') ? 'mendesak' : 
                             activeFilter.classList.contains('normal') ? 'umum' : 'semua';
            filterMessages(filterType);
        }
    });

    // Event listener untuk klik pada ticket card
    document.querySelectorAll('.ticket-card').forEach(ticket => {
        ticket.addEventListener('click', function() {
            const href = this.getAttribute('onclick');
            if (href) {
                const url = href.match(/'([^']+)'/)[1];
                window.location.href = url;
            }
        });
    });
});
</script>
@endpush
   
