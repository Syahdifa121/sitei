@extends('layouts.app')

@section('title', 'Dashboard Konsultasi')

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

    .btn-gradient a {
        color: white;
        text-decoration: none;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-gradient:hover a{
        color: black;
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
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="h6 text-muted">Total Konsultasi</h3>
                <h2 class="h4 mb-0">{{ $pesanAktif->count() + $pesanSelesai->count() }}</h2>
            </div>
        </div>
        
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="h6 text-muted">Konsultasi Selesai</h3>
                <h2 class="h4 mb-0">{{ $pesanSelesai->count() }}</h2>
            </div>
        </div>

        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="h6 text-muted">Konsultasi Aktif</h3>
                <h2 class="h4 mb-0">{{ $pesanAktif->count() }}</h2>
            </div>
        </div>
    </div>

    <!-- Main Section -->
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                @if(auth()->user()->role === 'mahasiswa')
                    <button class="create-ticket-btn w-100 mb-4" onclick="window.location.href='{{ route('pesan.create') }}'">
                        <i class="fas fa-plus-circle me-2"></i>Buat Konsultasi
                    </button>
                @else
                    <button class="create-ticket-btn w-100 mb-4" onclick="window.location.href='{{ route('pesan.create') }}'">
                        <i class="fas fa-plus-circle me-2"></i>Buat Konsultasi
                    </button>
                @endif
                    <div class="list-group list-group-flush">
                        <a href="{{ route('pesan.filterAktif') }}" 
                        class="list-group-item list-group-item-action {{ !Request::routeIs('pesan.filterSelesai') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-inbox me-2"></i>Aktif</span>
                            <span class="badge bg-white text-primary rounded-pill">{{ $pesanAktif->count() }}</span>
                        </a>
                        <a href="{{ route('pesan.filterSelesai') }}" 
                        class="list-group-item list-group-item-action {{ Request::routeIs('pesan.filterSelesai') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-history me-2"></i>Riwayat</span>
                            <span class="badge bg-light text-dark rounded-pill">{{ $pesanSelesai->count() }}</span>
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
            @forelse($pesanAktif as $pesan)
                <div class="ticket-card" data-priority="{{ $pesan->prioritas }}" onclick="window.location.href='{{ route('pesan.show', $pesan->id) }}'">
                    <div class="d-flex align-items-center mb-3">
                    <img src="{{ $pesan->mahasiswa->foto ? asset('storage/foto_profil/' . $pesan->mahasiswa->foto) : asset('images/default-avatar.png') }}" 
                     alt="Avatar" 
                     class="avatar me-3">
                        <div class="flex-grow-1">
                            @if(auth()->user()->role === 'mahasiswa')
                                <h6 class="mb-0">{{ $pesan->dosen->nama }}</h6>
                                <small class="text-muted">{{ $pesan->dosen->nip }}</small>
                            @else
                                <h6 class="mb-0">{{ $pesan->mahasiswa->nama }}</h6>
                                <small class="text-muted">{{ $pesan->mahasiswa->nim }}</small>
                            @endif
                        </div>
                        <span class="priority-badge {{ $pesan->prioritas == 'mendesak' ? 'priority-urgent' : 'priority-normal' }}">
                            <i class="fas fa-arrow-up me-1"></i>{{ ucfirst($pesan->prioritas) }}
                        </span>
                    </div>
                    <h5 class="mb-2">{{ $pesan->subjek }}</h5>
                    <div class="d-flex align-items-center text-muted">
                        <i class="far fa-clock me-2"></i>
                        <small>{{ $pesan->created_at->format('H:i') }} - {{ $pesan->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">Tidak Ada Konsultasi Aktif</h5>
                    <p class="text-muted mb-0">Konsultasi yang sedang berlangsung akan muncul di sini</p>
                </div>
            @endforelse
            </div>

            <!-- History Container -->
            <div class="history-container" id="history-tickets" style="display: none;">
                @forelse($pesanSelesai as $pesan)
                    <div class="ticket-card" data-priority="{{ $pesan->prioritas }}" onclick="window.location.href='{{ route('pesan.show', $pesan->id) }}'">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $pesan->mahasiswa->foto ? asset('storage/foto_profil/' . $pesan->mahasiswa->foto) : asset('images/default-avatar.png') }}" 
                                alt="Avatar" 
                                class="avatar me-3">
                            <div class="flex-grow-1">
                                @if(auth()->user()->role === 'mahasiswa')
                                    <h6 class="mb-0">{{ $pesan->dosen->nama }}</h6>
                                    <small class="text-muted">{{ $pesan->dosen->nip }}</small>
                                @else
                                    <h6 class="mb-0">{{ $pesan->mahasiswa->nama }}</h6>
                                    <small class="text-muted">{{ $pesan->mahasiswa->nim }}</small>
                                @endif
                            </div>
                            <span class="priority-badge {{ $pesan->prioritas == 'mendesak' ? 'priority-urgent' : 'priority-normal' }}">
                                <i class="fas fa-arrow-up me-1"></i>{{ ucfirst($pesan->prioritas) }}
                            </span>
                        </div>
                        <h5 class="mb-2">{{ $pesan->subjek }}</h5>
                        <div class="d-flex align-items-center text-muted">
                            <i class="far fa-clock me-2"></i>
                            <small>{{ $pesan->created_at->format('H:i') }} - {{ $pesan->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">Tidak Ada Riwayat Konsultasi</h5>
                        <p class="text-muted mb-0">Riwayat konsultasi yang telah selesai akan muncul di sini</p>
                    </div>
                @endforelse
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
    
    function getActiveContainer() {
        return activeTickets.style.display !== 'none' ? activeTickets : historyTickets;
    }

    function filterMessages(priority) {
        const activeContainer = getActiveContainer();
        const allTickets = activeContainer.querySelectorAll('.ticket-card');
        const searchTerm = document.querySelector('.search-box').value.toLowerCase();
        let visibleCount = 0;

        allTickets.forEach(ticket => {
            const ticketPriority = ticket.getAttribute('data-priority');
            const title = ticket.querySelector('h5').textContent.toLowerCase();
            const name = ticket.querySelector('h6').textContent.toLowerCase();
            const nim = ticket.querySelector('small').textContent.toLowerCase();
            
            const matchesSearch = title.includes(searchTerm) || 
                                name.includes(searchTerm) || 
                                nim.includes(searchTerm);
            const matchesPriority = priority === 'semua' || ticketPriority === priority;

            if (matchesSearch && matchesPriority) {
                ticket.style.display = 'block';
                visibleCount++;
            } else {
                ticket.style.display = 'none';
            }
        });

        // Tampilkan atau sembunyikan pesan "tidak ada konsultasi"
        const emptyStateDiv = activeContainer.querySelector('.text-center.py-5');
        if (emptyStateDiv) {
            emptyStateDiv.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    function setActiveFilterButton(button) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    }

    // Event listener untuk menu sidebar
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Reset filter dan search
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector('.filter-btn.btn-outline-primary').classList.add('active');
            document.querySelector('.search-box').value = '';
            
            // Update active menu
            document.querySelectorAll('.list-group-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Toggle containers
            const isActive = this.textContent.includes('Aktif');
            activeTickets.style.display = isActive ? 'block' : 'none';
            historyTickets.style.display = isActive ? 'none' : 'block';
            
            // Reset filter untuk menampilkan semua ticket
            filterMessages('semua');
        });
    });

    // Event listeners untuk tombol filter
    document.querySelector('.filter-btn.urgent').addEventListener('click', function() {
        filterMessages('mendesak');
        setActiveFilterButton(this);
    });

    document.querySelector('.filter-btn.normal').addEventListener('click', function() {
        filterMessages('umum');
        setActiveFilterButton(this);
    });

    document.querySelector('.filter-btn.btn-outline-primary').addEventListener('click', function() {
        filterMessages('semua');
        setActiveFilterButton(this);
    });

    // Event listener untuk pencarian
    document.querySelector('.search-box').addEventListener('input', function() {
        const activeFilter = document.querySelector('.filter-btn.active');
        const filterType = activeFilter.classList.contains('urgent') ? 'mendesak' : 
                          activeFilter.classList.contains('normal') ? 'umum' : 'semua';
        filterMessages(filterType);
    });
});
</script>
@endpush
