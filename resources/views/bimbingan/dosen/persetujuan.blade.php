@extends('layouts.app')

@section('title', 'Persetujuan Bimbingan')

@push('styles')
    <style>
        .action-icons {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .action-icon {
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
            text-decoration: none;
        }

        .action-icon:hover {
            opacity: 0.8;
        }

        .info-icon {
            background-color: #17a2b8;
            color: white !important;
        }

        .approve-icon {
            background-color: #28a745;
            color: white !important;
        }

        .reject-icon {
            background-color: #dc3545;
            color: white !important;
        }

        .edit-icon {
            background-color: #F3B806;
            color: white !important;
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: #2563eb;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
        }

        .page-link:hover {
            color: #1d4ed8;
            background-color: #f3f4f6;
        }
        .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    
    .card-header h5 {
        margin-bottom: 0;
        font-weight: 600;
        color: #333;
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* Styling untuk tabel */
    .table {
        margin-bottom: 0;
        border-color: #f0f0f0;
        border-collapse: collapse !important;
    }
    
    .table th {
        
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600;
        border-top: none;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6 !important;
        padding: 12px 10px;
    }
    
    .table td {
        vertical-align: middle;
        border: 1px solid #dee2e6 !important;
        padding: 12px 10px;
        border-color: #f0f0f0;
    }

    /* Compact modern styles for the modalBatal */
#modalBatal .modal-content {
    border: none;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

#modalBatal .modal-header {
    background: linear-gradient(135deg, #f53844 0%, #d2001a 100%);
    padding: 12px 16px;
    border-bottom: none;
}

#modalBatal .modal-title {
    color: white;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

#modalBatal .modal-title i {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 6px;
    margin-right: 8px;
    font-size: 16px;
}

#modalBatal .btn-close {
    position: relative;
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%; 
    opacity: 1;
    transition: all 0.2s ease;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

#modalBatal .btn-close:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

#modalBatal .btn-close:focus {
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.25);
    outline: none;
}

/* Optionally, if you want to replace the standard Bootstrap X with a custom one */
#modalBatal .btn-close::after {
    content: "×";
    font-size: 24px;
    font-weight: 300;
    line-height: 1;
    color: white;
    margin-top: -2px;
}

#modalBatal .btn-close:before {
    display: none;
}

/* Hide the default Bootstrap X */
#modalBatal .btn-close {
    font-size: 0;
    background-image: none;
}

#modalBatal .modal-body {
    padding: 16px;
}

#modalBatal .form-group {
    margin-bottom: 16px;
}

#modalBatal .form-label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 6px;
}

#modalBatal .input-group {
    box-shadow: none;
}

#modalBatal .input-group-text {
    background-color: #f1f1f1;
    color: #555;
    border: 1px solid #ced4da;
    border-right: none;
}

#modalBatal .form-control {
    border: 1px solid #ced4da;
    padding: 8px 12px;
    min-height: 80px;
}

#modalBatal .form-control:focus {
    border-color: #d2001a;
    box-shadow: 0 0 0 2px rgba(210, 0, 26, 0.1);
}

#modalBatal small.text-muted {
    font-size: 12px;
    color: #777 !important;
}

#modalBatal .related-schedules {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    margin-top: 16px;
    border: 1px solid #eaeaea;
}

#modalBatal .d-flex.align-items-center.mb-2 {
    margin-bottom: 10px !important;
}

#modalBatal h6.fw-bold.mb-0 {
    font-size: 14px;
    margin-bottom: 0;
}

#modalBatal .form-check {
    padding-left: 1.5rem;
}

#modalBatal .form-check-input {
    cursor: pointer;
}

#modalBatal .form-check-label {
    font-size: 13px;
    cursor: pointer;
}

#modalBatal .alert-info {
    background-color: #f0f7fb;
    border-left: 3px solid #5bc0de;
    color: #31708f;
    padding: 10px;
    margin-bottom: 12px;
    font-size: 13px;
}

#modalBatal .table-responsive {
    margin-top: 10px;
}

#modalBatal .table {
    border: 1px solid #dee2e6;
}

#modalBatal .table thead th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: 600;
    font-size: 13px;
    padding: 8px;
    text-align: center;
}

#modalBatal .table tbody td {
    padding: 8px;
    font-size: 13px;
    vertical-align: middle;
}

#modalBatal .spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}

#modalBatal .modal-footer {
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-top: 1px solid #eaeaea;
}

#modalBatal .btn {
    font-size: 14px;
    font-weight: 500;
    padding: 6px 16px;
    display: inline-flex;
    align-items: center;
}

#modalBatal .btn-secondary {
    background-color: #f2f2f2;
    border-color: #d4d4d4;
    color: #333;
}

#modalBatal .btn-secondary:hover {
    background-color: #e6e6e6;
}

#modalBatal .btn-danger {
    background: linear-gradient(135deg, #f53844 0%, #d2001a 100%);
    border: none;
}

#modalBatal .btn-danger:hover {
    background: linear-gradient(135deg, #d2001a 0%, #b50014 100%);
    box-shadow: 0 4px 8px rgba(210, 0, 26, 0.2);
}
    </style>
@endpush

@section('content')
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Persetujuan Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
            <a href="{{ route('dosen.jadwal.index') }}">
                <i class="bi bi-plus-lg me-2"></i> Jadwal Bimbingan
            </a>
        </button>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'usulan', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'usulan' ? 'active' : '' }}">
                            Usulan
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'jadwal', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'jadwal' ? 'active' : '' }}">
                            Jadwal
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'riwayat', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'riwayat' ? 'active' : '' }}">
                            Riwayat
                        </a>
                    </li>
                    @if(auth()->user()->isKoordinatorProdi())
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('dosen.persetujuan', ['tab' => 'pengelola', 'per_page' => request('per_page', 10)]) }}"
                            class="nav-link px-4 py-3 {{ $activeTab == 'pengelola' ? 'active' : '' }}">
                            Pengelola
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="card-body p-4">
                {{-- <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="me-2">Tampilkan</label>
                            <select class="form-select form-select-sm w-auto"
                                onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => $activeTab]) }}&per_page=' + this.value">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            </select>
                            <label class="ms-2">entries</label>
                        </div>
                    </div>
                </div> --}}

                <div class="tab-content" id="bimbinganTabContent">
                    @if ($activeTab == 'usulan')
                        <div class="tab-pane fade show active" id="usulan" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2">Tampilkan</label>
                                        <select class="form-select form-select-sm w-auto"
                                            onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'usulan']) }}&per_page=' + this.value">
                                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                        </select>
                                        <label class="ms-2">entries</label>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($usulan as $index => $item)
                                            <tr class="text-center" data-id="{{ $item->id }}">
                                                <td>{{ ($usulan->currentPage() - 1) * $usulan->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}
                                                </td>
                                                <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                <td
                                                    class="fw-bold bg-{{ $item->status === 'DISETUJUI' ? 'success' : ($item->status === 'DITOLAK' ? 'danger' : 'warning') }} text-white">
                                                    {{ $item->status }}</td>
                                                <td>
                                                    <div class="action-icons">
                                                        @if ($item->status == 'USULAN')
                                                            <a href="#" class="action-icon approve-icon"
                                                                data-bs-toggle="tooltip" title="Setujui">
                                                                <i class="bi bi-check-circle"></i>
                                                            </a>
                                                            <a href="#" class="action-icon reject-icon"
                                                                data-bs-toggle="tooltip" title="Tolak">
                                                                <i class="bi bi-x-circle"></i>
                                                            </a>
                                                        @endif
                                                        <div class="action-icons">
                                                            <a href="{{ route('dosen.detailbimbingan', $item->id) }}"
                                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                title="Info">
                                                                <i class="bi bi-info-circle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data usulan bimbingan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'jadwal')
                        <div class="tab-pane fade show active" id="jadwal" role="tabpanel">
                            <!-- Google Calendar Integration -->
                            <div class="mb-4">
                                @if (auth()->user()->hasGoogleCalendarConnected())
                                    <div class="card shadow border-0 rounded-4 mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                                            <h5 class="mb-0">Google Calendar</h5>
                                            <div>
                                                @if (auth()->user()->isGoogleTokenExpired())
                                                    <a href="{{ route('dosen.google.connect') }}"
                                                        class="btn btn-sm btn-warning me-2">
                                                        <i class="bi bi-arrow-clockwise me-1"></i> Hubungkan Ulang
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="ratio ratio-16x9">
                                                <iframe
                                                    src="https://calendar.google.com/calendar/embed?src={{ urlencode(auth()->user()->email) }}&mode=WEEK&showPrint=0&showCalendars=0&showTz=0&hl=id"
                                                    style="border: 0" frameborder="0" scrolling="no"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <div>
                                            Anda perlu menghubungkan Google Calendar jika ingin menggunakan fitur Kalender.
                                            <a href="{{ route('dosen.google.connect') }}" class="alert-link">
                                                Klik di sini untuk menghubungkan
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Tabel daftar mahasiswa yang disetujui -->
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-header bg-white p-3">
                                    <h5 class="mb-0 fw-bold">Daftar Mahasiswa Bimbingan</h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <label class="me-2">Tampilkan</label>
                                                <select class="form-select form-select-sm w-auto"
                                                    onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'jadwal']) }}&per_page=' + this.value">
                                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                    <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                                </select>
                                                <label class="ms-2">entries</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered align-middle">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>NIM</th>
                                                    <th>Nama</th>
                                                    <th>Jenis Bimbingan</th>
                                                    <th>Tanggal</th>
                                                    <th>Waktu</th>
                                                    <th>Lokasi</th>
                                                    <th>Antrian</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwal as $index => $item)
                                                    <tr class="text-center">
                                                        <td>{{ ($jadwal->currentPage() - 1) * $jadwal->perPage() + $loop->iteration }}</td>
                                                        <td>{{ $item->nim }}</td>
                                                        <td>{{ $item->mahasiswa_nama }}</td>
                                                        <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                        <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}</td>
                                                        <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                        <td class="fw-bold text-white bg-success">DISETUJUI</td>
                                                        <td>
                                                            <div class="d-flex gap-2 justify-content-center">
                                                                <button class="btn btn-sm btn-success selesai-btn"
                                                                    data-id="{{ $item->id }}" data-bs-toggle="modal"
                                                                    data-bs-target="#modalSelesai" title="Selesai">
                                                                    <i class="bi bi-check2-circle"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger batal-btn"
                                                                    data-id="{{ $item->id }}" title="Batalkan">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                                <div class="action-icons">
                                                                    <a href="{{ route('dosen.detailbimbingan', $item->id) }}"
                                                                        class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                        title="Info">
                                                                        <i class="bi bi-info-circle"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">Tidak ada jadwal bimbingan aktif</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                        @if($jadwal instanceof \Illuminate\Pagination\LengthAwarePaginator && $jadwal->total() > 0)
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <p class="mb-0">
                                                Menampilkan {{ $jadwal->firstItem() }} sampai {{ $jadwal->lastItem() }} dari
                                                {{ $jadwal->total() }} entri
                                            </p>
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination justify-content-end mb-0">
                                                    {{-- Tombol Sebelumnya --}}
                                                    @if ($jadwal->onFirstPage())
                                                        <li class="page-item disabled">
                                                            <span class="page-link">« Sebelumnya</span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $jadwal->previousPageUrl() }}&tab=jadwal">« Sebelumnya</a>
                                                        </li>
                                                    @endif

                                                    {{-- Tombol Nomor Halaman --}}
                                                    @foreach ($jadwal->getUrlRange(1, $jadwal->lastPage()) as $page => $url)
                                                        <li class="page-item {{ $page == $jadwal->currentPage() ? 'active' : '' }}">
                                                            <a class="page-link" href="{{ $url }}&tab=jadwal">{{ $page }}</a>
                                                        </li>
                                                    @endforeach

                                                    {{-- Tombol Selanjutnya --}}
                                                    @if ($jadwal->hasMorePages())
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $jadwal->nextPageUrl() }}&tab=jadwal">Selanjutnya »</a>
                                                        </li>
                                                    @else
                                                        <li class="page-item disabled">
                                                            <span class="page-link">Selanjutnya »</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </nav>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab == 'riwayat')
                        <div class="tab-pane fade show active" id="riwayat" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2">Tampilkan</label>
                                        <select class="form-select form-select-sm w-auto"
                                            onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'riwayat']) }}&per_page=' + this.value">
                                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                        </select>
                                        <label class="ms-2">entries</label>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Jenis Bimbingan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Antrian</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayat as $index => $item)
                                            <tr class="text-center">
                                                <td>{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $item->nim }}</td>
                                                <td>{{ $item->mahasiswa_nama }}</td>
                                                <td>{{ ucfirst($item->jenis_bimbingan) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                                <td>{{ $item->lokasi && trim($item->lokasi) !== '' ? $item->lokasi : '-' }}
                                                </td>
                                                <td>{{ $item->nomor_antrian && trim($item->nomor_antrian) !== '' ? $item->nomor_antrian : '-' }}</td>
                                                <td class="fw-bold {{ 
                                                    $item->status === 'DISETUJUI' ? 'bg-success' : (
                                                        $item->status === 'DITOLAK' ? 'bg-danger' : (
                                                            $item->status === 'DIBATALKAN' ? 'bg-secondary' : (
                                                                $item->status === 'SELESAI' ? 'bg-primary' : 'bg-warning'
                                                            )
                                                        )
                                                    ) 
                                                }} text-white">{{ $item->status }}</td>
                                                <td>
                                                    <div class="action-icons">
                                                        <a href="{{ route('dosen.detailbimbingan', $item->id) }}"
                                                            class="action-icon info-icon" data-bs-toggle="tooltip"
                                                            title="Info">
                                                            <i class="bi bi-info-circle"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data riwayat bimbingan
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Di dalam div tab-content -->
                    @if ($activeTab == 'pengelola' && auth()->user()->isKoordinatorProdi())
                    <div class="tab-pane fade show active" id="pengelola" role="tabpanel">
                        <!-- Daftar Jadwal Dosen -->
                        <div class="card shadow-lg border-0 rounded-4 mb-4">
                            <div class="card-header bg-white p-3">
                                <h5 class="mb-0 fw-bold">Daftar Jadwal Dosen</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <label class="me-2">Tampilkan</label>
                                            <select class="form-select form-select-sm w-auto"
                                                onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'riwayat']) }}&per_page=' + this.value">
                                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                            </select>
                                            <label class="ms-2">entries</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>NIP</th>
                                                <th>Nama Dosen</th>
                                                <th>Nama Singkat</th>
                                                <th>Total Bimbingan Hari Ini</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dosenList as $index => $dosen)
                                                <tr class="text-center">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $dosen->nip }}</td>
                                                    <td>{{ $dosen->nama }}</td>
                                                    <td>{{ $dosen->nama_singkat }}</td>
                                                    <td>{{ $dosen->total_bimbingan_hari_ini }}</td>
                                                    <td>
                                                        <div class="action-icons">
                                                            <a href="{{ route('dosen.detail', $dosen->nip) }}"
                                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                title="Info">
                                                                <i class="bi bi-info-circle"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data dosen</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($dosenList instanceof \Illuminate\Pagination\LengthAwarePaginator && $dosenList->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">
                                        Menampilkan {{ $dosenList->firstItem() }} sampai {{ $dosenList->lastItem() }} dari
                                        {{ $dosenList->total() }} entri
                                    </p>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end mb-0">
                                            {{-- Tombol Sebelumnya --}}
                                            @if ($dosenList->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">« Sebelumnya</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $dosenList->previousPageUrl() }}&tab=pengelola">« Sebelumnya</a>
                                                </li>
                                            @endif

                                            {{-- Tombol Nomor Halaman --}}
                                            @foreach ($dosenList->getUrlRange(1, $dosenList->lastPage()) as $page => $url)
                                                <li class="page-item {{ $page == $dosenList->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $url }}&tab=pengelola">{{ $page }}</a>
                                                </li>
                                            @endforeach

                                            {{-- Tombol Selanjutnya --}}
                                            @if ($dosenList->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $dosenList->nextPageUrl() }}&tab=pengelola">Selanjutnya »</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">Selanjutnya »</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Riwayat Jadwal Dosen -->
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-white p-3">
                                <h5 class="mb-0 fw-bold">Riwayat Jadwal Dosen</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <label class="me-2">Tampilkan</label>
                                            <select class="form-select form-select-sm w-auto"
                                                onchange="window.location.href='{{ route('dosen.persetujuan', ['tab' => 'riwayat']) }}&per_page=' + this.value">
                                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                                            </select>
                                            <label class="ms-2">entries</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>NIP</th>
                                                <th>Nama Dosen</th>
                                                <th>Nama Singkat</th>
                                                <th>Total Bimbingan Keseluruhan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($riwayatDosenList as $index => $dosen)
                                                <tr class="text-center">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $dosen->nip }}</td>
                                                    <td>{{ $dosen->nama }}</td>
                                                    <td>{{ $dosen->nama_singkat }}</td>
                                                    <td>{{ $dosen->total_bimbingan }}</td>
                                                    <td>
                                                        <div class="action-icons">
                                                            <a href="{{ route('dosen.riwayat.detail', $dosen->nip) }}"
                                                                class="action-icon info-icon" data-bs-toggle="tooltip"
                                                                title="Info">
                                                                <i class="bi bi-info-circle"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data riwayat dosen</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($riwayatDosenList instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayatDosenList->total() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">
                                        Menampilkan {{ $riwayatDosenList->firstItem() }} sampai {{ $riwayatDosenList->lastItem() }} dari
                                        {{ $riwayatDosenList->total() }} entri
                                    </p>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end mb-0">
                                            {{-- Tombol Sebelumnya --}}
                                            @if ($riwayatDosenList->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">« Sebelumnya</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $riwayatDosenList->previousPageUrl() }}&tab=pengelola">« Sebelumnya</a>
                                                </li>
                                            @endif

                                            {{-- Tombol Nomor Halaman --}}
                                            @foreach ($riwayatDosenList->getUrlRange(1, $riwayatDosenList->lastPage()) as $page => $url)
                                                <li class="page-item {{ $page == $riwayatDosenList->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $url }}&tab=pengelola">{{ $page }}</a>
                                                </li>
                                            @endforeach

                                            {{-- Tombol Selanjutnya --}}
                                            @if ($riwayatDosenList->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $riwayatDosenList->nextPageUrl() }}&tab=pengelola">Selanjutnya »</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">Selanjutnya »</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-3">
                    <p class="mb-2">
                        @if ($activeTab == 'usulan' && $usulan->total() > 0)
                            Menampilkan {{ $usulan->firstItem() }} sampai {{ $usulan->lastItem() }} dari
                            {{ $usulan->total() }} entri
                        @elseif($activeTab == 'riwayat' && $riwayat->total() > 0)
                            Menampilkan {{ $riwayat->firstItem() }} sampai {{ $riwayat->lastItem() }} dari
                            {{ $riwayat->total() }} entri
                        @endif
                    </p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            {{-- Previous Page --}}
                            @if ($activeTab == 'usulan')
                                @if ($usulan->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $usulan->previousPageUrl() }}&tab=usulan">« Sebelumnya</a>
                                    </li>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach ($usulan->getUrlRange(1, $usulan->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $usulan->currentPage() ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ $url }}&tab=usulan">{{ $page }}</a>
                                    </li>
                                @endforeach

                                {{-- Next Page --}}
                                @if ($usulan->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $usulan->nextPageUrl() }}&tab=usulan">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                            @elseif($activeTab == 'riwayat')
                                @if ($riwayat->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Sebelumnya</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $riwayat->previousPageUrl() }}&tab=riwayat">« Sebelumnya</a>
                                    </li>
                                @endif

                                @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
                                    <li class="page-item {{ $page == $riwayat->currentPage() ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ $url }}&tab=riwayat">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($riwayat->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $riwayat->nextPageUrl() }}&tab=riwayat">Selanjutnya »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Selanjutnya »</span>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Terima -->
    <div class="modal fade" id="modalTerima" tabindex="-1" aria-labelledby="modalTerimaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTerimaLabel">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Terima Usulan Bimbingan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Lokasi -->
                    <div class="form-group">
                        <label for="lokasiBimbingan" class="form-label fw-bold">Lokasi Bimbingan <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-location-dot"></i>
                            </span>
                            <input type="text" class="form-control" id="lokasiBimbingan" required
                                placeholder="Contoh: Ruang Dosen Lt.2, Meeting Room, atau Link Lokasi">
                        </div>
                        <div class="invalid-feedback">Lokasi bimbingan wajib diisi</div>
                        <small class="text-muted">Masukkan lokasi fisik atau link lokasi</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirmTerima">
                        <i class="fas fa-check me-2"></i>Setujui Usulan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTolakLabel">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Tolak Usulan Bimbingan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Alasan -->
                    <div class="form-group">
                        <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-comment-alt"></i>
                            </span>
                            <textarea class="form-control" id="alasanPenolakan" rows="3" required
                                placeholder="Contoh: Jadwal bertabrakan dengan kegiatan lain, Mohon ajukan di waktu lain"></textarea>
                        </div>
                        <div class="invalid-feedback">Alasan penolakan wajib diisi</div>
                        <small class="text-muted">Berikan alasan yang jelas agar mahasiswa dapat mengajukan ulang dengan
                            penyesuaian</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmTolak">
                        <i class="fas fa-times me-2"></i>Tolak Usulan
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Selesai -->
<div class="modal fade" id="modalSelesai" tabindex="-1" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow border-0">
            <div class="modal-header border-0 bg-success text-white">
                <h5 class="modal-title fw-bold" id="modalSelesaiLabel">
                    Konfirmasi Selesai Bimbingan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="d-flex justify-content-center mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10" style="width: 90px; height: 90px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 42px;"></i>
                    </div>
                </div>
                <p class="mb-1">Apakah Anda yakin sesi bimbingan ini telah selesai?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-success px-4" id="confirmSelesai">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Batal Persetujuan -->
<div class="modal fade" id="modalBatal" tabindex="-1" aria-labelledby="modalBatalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalBatalLabel">
                    <i class="bi bi-x-circle me-2"></i>
                    Batalkan Persetujuan Bimbingan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Alasan -->
                <div class="form-group mb-3">
                    <label for="alasanPembatalan" class="form-label fw-bold">Alasan Pembatalan <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-comment-alt"></i>
                        </span>
                        <textarea class="form-control" id="alasanPembatalan" rows="3" required
                            placeholder="Contoh: Ada jadwal rapat mendadak, mohon maaf atas ketidaknyamanannya"></textarea>
                    </div>
                    <div class="invalid-feedback">Alasan pembatalan wajib diisi</div>
                    <small class="text-muted">Berikan alasan yang jelas kepada mahasiswa mengapa jadwal bimbingan dibatalkan</small>
                </div>

                <!-- Daftar Mahasiswa dengan Jadwal yang Sama -->
                <div class="related-schedules" id="relatedSchedulesContainer">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="fw-bold mb-0">Mahasiswa Lain dengan Jadwal yang Sama:</h6>
                        <div class="form-check ms-3">
                            <input class="form-check-input" type="checkbox" id="selectAllRelatedSchedules">
                            <label class="form-check-label" for="selectAllRelatedSchedules">Pilih Semua</label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info d-none" id="noRelatedSchedules">
                        <i class="bi bi-info-circle me-2"></i>
                        Tidak ada mahasiswa lain yang memiliki jadwal di waktu yang sama.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="relatedSchedulesTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">Pilih</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Jenis Bimbingan</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="relatedSchedulesList">
                                <!-- Data akan diisi secara dinamis oleh JavaScript -->
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Memuat data...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmBatal">
                    <i class="bi bi-x-lg me-2"></i>Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>document.addEventListener('DOMContentLoaded', function() {
    let currentRow = null;
    let currentId = null;
    let currentSelesaiId = null;
    let currentBatalId = null;
    
    // Dapatkan referensi ke modal-modal
    const modalTerima = document.getElementById('modalTerima');
    const modalTolak = document.getElementById('modalTolak');
    const modalSelesai = document.getElementById('modalSelesai');
    const modalBatal = document.getElementById('modalBatal');
    
    // Inisialisasi instance bootstrap modal
    const bsModalTerima = modalTerima ? new bootstrap.Modal(modalTerima) : null;
    const bsModalTolak = modalTolak ? new bootstrap.Modal(modalTolak) : null;
    const bsModalSelesai = modalSelesai ? new bootstrap.Modal(modalSelesai) : null;
    const bsModalBatal = modalBatal ? new bootstrap.Modal(modalBatal) : null;

    function initializeTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                if (!bootstrap.Tooltip.getInstance(tooltip)) {
                    new bootstrap.Tooltip(tooltip);
                }
            });
        }
    }

    initializeTooltips();

    // Function to update row after approval/rejection
    function updateRowAfterAction(row, id, lokasi, status) {
    if (!row) return;

    const statusCell = row.querySelector('td:nth-child(9)'); // Adjusted to correct column
    if (statusCell) {
        statusCell.textContent = status;
        statusCell.className = 'fw-bold text-white';

        if (status === 'DISETUJUI') {
            statusCell.classList.add('bg-success');
        } else if (status === 'DITOLAK') {
            statusCell.classList.add('bg-danger');
        } else if (status === 'DIBATALKAN') { // Tambahkan kondisi untuk status DIBATALKAN
            statusCell.classList.add('bg-secondary');
        } else if (status === 'SELESAI') {
            statusCell.classList.add('bg-primary');
        } else {
            statusCell.classList.add('bg-warning');
        }
    }

    if (lokasi) {
        const lokasiCell = row.querySelector('td:nth-child(7)');
        if (lokasiCell) {
            lokasiCell.textContent = lokasi;
        }
    }

    const actionCell = row.querySelector('.action-icons');
    if (actionCell) {
        actionCell.innerHTML = `
            <a href="/dosen/detailbimbingan/${id}" 
               class="action-icon info-icon" 
               data-bs-toggle="tooltip" 
               title="Info">
                <i class="bi bi-info-circle"></i>
            </a>`;
        initializeTooltips();
    }
}

    // Setup modal handling for approve action
    document.querySelectorAll('.approve-icon').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentRow = this.closest('tr');
            currentId = currentRow.getAttribute('data-id');

            if (!currentRow || !currentId) return;

            if (bsModalTerima) bsModalTerima.show();
        });
    });

    // Setup modal handling for reject action
    document.querySelectorAll('.reject-icon').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentRow = this.closest('tr');
            currentId = currentRow.getAttribute('data-id');

            if (!currentRow || !currentId) return;

            if (bsModalTolak) bsModalTolak.show();
        });
    });

    // Setup modal handler untuk tombol selesai
    document.querySelectorAll('.selesai-btn').forEach(button => {
    button.addEventListener('click', async function(e) {
        e.preventDefault();
        currentSelesaiId = this.getAttribute('data-id');
        console.log('Button selesai diklik, ID:', currentSelesaiId);
        
        // Fetch additional data about the guidance session
        try {
            const row = this.closest('tr');
            if (row) {
                const mahasiswaNama = row.querySelector('td:nth-child(3)').textContent.trim();
                const jenisBimbingan = row.querySelector('td:nth-child(4)').textContent.trim();
                
                // Update modal with contextual information
                const mhsNameConfirm = document.getElementById('mhs-name-confirm');
                const jenisBimbinganConfirm = document.getElementById('jenis-bimbingan-confirm');
                
                if (mhsNameConfirm) mhsNameConfirm.textContent = mahasiswaNama;
                if (jenisBimbinganConfirm) jenisBimbinganConfirm.textContent = jenisBimbingan;
            }
        } catch (error) {
            console.error('Error getting row data:', error);
        }
        
        if (bsModalSelesai) bsModalSelesai.show();
    });
});

    // Handle approve confirmation
    document.getElementById('confirmTerima')?.addEventListener('click', async function() {
        const lokasiInput = document.getElementById('lokasiBimbingan');
        if (!lokasiInput || !currentId || !currentRow) return;

        const lokasi = lokasiInput.value.trim();
        if (!lokasi) {
            lokasiInput.classList.add('is-invalid');
            return;
        }

        try {
            this.disabled = true;

            const response = await fetch(`/persetujuan/terima/${currentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    lokasi: lokasi
                })
            });

            const data = await response.json();

            if (data.success) {
                if (bsModalTerima) bsModalTerima.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Usulan bimbingan berhasil disetujui',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memproses usulan'
            });
        } finally {
            this.disabled = false;
        }
    });

    // Handle reject confirmation
    document.getElementById('confirmTolak')?.addEventListener('click', async function() {
        const alasanInput = document.getElementById('alasanPenolakan');
        if (!alasanInput || !currentId || !currentRow) return;

        const alasan = alasanInput.value.trim();
        if (!alasan) {
            alasanInput.classList.add('is-invalid');
            return;
        }

        try {
            this.disabled = true;

            const response = await fetch(`/persetujuan/tolak/${currentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    keterangan: alasan
                })
            });

            const data = await response.json();

            if (data.success) {
                if (bsModalTolak) bsModalTolak.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Usulan bimbingan telah ditolak',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memproses usulan'
            });
        } finally {
            this.disabled = false;
        }
    });

    // Handle konfirmasi selesai
    document.getElementById('confirmSelesai')?.addEventListener('click', async function() {
        if (!currentSelesaiId) {
            console.log('Error: currentSelesaiId kosong');
            return;
        }

        try {
            console.log('Mengirim request ke /persetujuan/selesai/' + currentSelesaiId);
            
            // Close the confirmation modal first
            if (bsModalSelesai) bsModalSelesai.hide();

            // Show loading state
            Swal.fire({
                title: 'Memproses',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send the request
            const response = await fetch(`/persetujuan/selesai/${currentSelesaiId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', response.status);
            
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
                console.log('Parsed data:', data);
            } catch (parseError) {
                console.error('Error parsing JSON:', parseError);
                throw new Error('Invalid JSON response');
            }

            if (data.success) {
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Bimbingan telah diselesaikan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error lengkap:', error);
            Swal.fire({
                icon: 'error',
                title: 'Tidak dapat memproses permintaan',
                text: error.message || 'Silakan coba beberapa saat lagi',
                confirmButtonColor: '#1a73e8'
            });
        }
    });

    // Setup event listener untuk tombol batal
    document.querySelectorAll('.batal-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            currentBatalId = this.getAttribute('data-id');
            
            // Reset form state
            const alasanInput = document.getElementById('alasanPembatalan');
            if (alasanInput) {
                alasanInput.value = '';
                alasanInput.classList.remove('is-invalid');
            }
            
            // Reset daftar jadwal terkait
            const relatedSchedulesList = document.getElementById('relatedSchedulesList');
            if (relatedSchedulesList) {
                relatedSchedulesList.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">Memuat data jadwal terkait...</span>
                        </td>
                    </tr>
                `;
            }
            
            // Reset checkbox "Pilih Semua"
            const selectAllCheckbox = document.getElementById('selectAllRelatedSchedules');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
                
            // Ambil data jadwal yang berkaitan dengan waktu yang sama
            try {
                const response = await fetch(`/persetujuan/related-schedules/${currentBatalId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const responseText = await response.text();
                console.log('Raw API Response:', responseText);
                
                // Coba parse response sebagai JSON
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse response as JSON:', parseError);
                    throw new Error('Invalid JSON response');
                }
                console.log('Parsed API Response:', result);
                
                if (result.success) {
                    const relatedSchedules = result.schedules || [];
                    const noRelatedMsg = document.getElementById('noRelatedSchedules');
                    const tbody = document.getElementById('relatedSchedulesList');
                    
                    // Tampilkan pesan jika tidak ada jadwal terkait
                    if (relatedSchedules.length === 0) {
                        if (noRelatedMsg) noRelatedMsg.classList.remove('d-none');
                        if (tbody) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada jadwal terkait</td>
                                </tr>
                            `;
                        }
                    } else {
                        if (noRelatedMsg) noRelatedMsg.classList.add('d-none');
                        
                        // Render daftar jadwal terkait
                        if (tbody) {
                            tbody.innerHTML = '';
                            relatedSchedules.forEach(schedule => {
                                const scheduleData = schedule.stdClass || schedule;
                                
                                const nim = scheduleData.nim;
                                const nama = scheduleData.mahasiswa_nama;
                                
                                // Periksa apakah format waktu valid sebelum konversi
                                let waktuMulai = 'Tidak tersedia';
                                let waktuSelesai = 'Tidak tersedia';
                                
                                try {
                                    // Cek apakah waktu_mulai berisi tanggal lengkap atau hanya waktu
                                    if (schedule.waktu_mulai) {
                                        // Jika hanya berisi format waktu (09:54:00)
                                        if (schedule.waktu_mulai.includes(':') && !schedule.waktu_mulai.includes('-')) {
                                            const [hours, minutes] = schedule.waktu_mulai.split(':');
                                            waktuMulai = `${hours}:${minutes}`;
                                        } else {
                                            waktuMulai = new Date(schedule.waktu_mulai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                                        }
                                    }
                                    
                                    if (schedule.waktu_selesai) {
                                        if (schedule.waktu_selesai.includes(':') && !schedule.waktu_selesai.includes('-')) {
                                            const [hours, minutes] = schedule.waktu_selesai.split(':');
                                            waktuSelesai = `${hours}:${minutes}`;
                                        } else {
                                            waktuSelesai = new Date(schedule.waktu_selesai).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error saat memformat waktu:', error);
                                }
                                
                                tbody.innerHTML += `
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input related-schedule-check" 
                                                type="checkbox" 
                                                value="${schedule.id}" 
                                                id="schedule-${schedule.id}">
                                        </td>
                                        <td>${nim}</td>
                                        <td>${nama}</td>
                                        <td>${schedule.jenis_bimbingan ? schedule.jenis_bimbingan.charAt(0).toUpperCase() + schedule.jenis_bimbingan.slice(1) : ''}</td>
                                        <td>${waktuMulai} - ${waktuSelesai}</td>
                                    </tr>
                                `;
                            });
                        }       
                    }
                } else {
                    throw new Error(result.message || 'Gagal memuat jadwal terkait');
                }
            } catch (error) {
                console.error('Error fetching related schedules:', error);
                const relatedSchedulesList = document.getElementById('relatedSchedulesList');
                if (relatedSchedulesList) {
                    relatedSchedulesList.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Gagal memuat data jadwal terkait
                            </td>
                        </tr>
                    `;
                }
            }
            
            // Tampilkan modal
            if (bsModalBatal) bsModalBatal.show();
        });
    });

    // Event listener untuk checkbox "Pilih Semua"
    const selectAllCheckbox = document.getElementById('selectAllRelatedSchedules');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            console.log('Select all checkbox changed, checked:', this.checked);
            const isChecked = this.checked;
            document.querySelectorAll('.related-schedule-check').forEach(checkbox => {
                checkbox.checked = isChecked;
                console.log('Setting checkbox', checkbox.id, 'to', isChecked);
            });
        });
    } else {
        console.warn('Select all checkbox not found in the DOM');
    }

    // Handle konfirmasi pembatalan
    document.getElementById('confirmBatal')?.addEventListener('click', async function() {
        const alasanInput = document.getElementById('alasanPembatalan');
        if (!alasanInput || !currentBatalId) return;

        const alasan = alasanInput.value.trim();
        if (!alasan) {
            alasanInput.classList.add('is-invalid');
            return;
        }
        
        // Kumpulkan ID jadwal yang dipilih untuk dibatalkan
        const selectedSchedules = [];
        document.querySelectorAll('.related-schedule-check:checked').forEach(checkbox => {
            selectedSchedules.push(checkbox.value);
            console.log('Selected schedule:', checkbox.value);
        });

        try {
            // Tutup modal konfirmasi
            if (bsModalBatal) bsModalBatal.hide();

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('Sending cancellation request with data:', {
                alasan: alasan,
                related_schedules: selectedSchedules
            });

            // Kirim request
            const response = await fetch(`/persetujuan/batal/${currentBatalId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    alasan: alasan,
                    related_schedules: selectedSchedules
                })
            });
            
            const responseText = await response.text();
            console.log('Cancellation response:', responseText);
            
            // Parse the response
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('Parsed API Response:', result);
                console.log('Related Schedules:', result.schedules);
                if (result.success && result.schedules && result.schedules.length > 0) {
            console.log('Jumlah jadwal terkait:', result.schedules.length);
            console.log('Detail jadwal pertama:', result.schedules[0]);
        }
            } catch (parseError) {
                console.error('Error parsing cancellation response:', parseError);
                throw new Error('Invalid JSON response from server');
            }

            if (result.success) {
                // Tampilkan notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: selectedSchedules.length > 0 
                        ? `Berhasil membatalkan ${selectedSchedules.length + 1} jadwal bimbingan`
                        : 'Bimbingan telah dibatalkan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error during cancellation:', error);
            Swal.fire({
                icon: 'error',
                title: 'Tidak dapat memproses permintaan',
                text: error.message || 'Silakan coba beberapa saat lagi',
                confirmButtonColor: '#1a73e8'
            });
        }
    });

    // Handle modal cleanup
    ['modalTerima', 'modalTolak', 'modalSelesai', 'modalBatal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal?.addEventListener('hidden.bs.modal', function() {
            if (modalId === 'modalTerima') {
                const input = document.getElementById('lokasiBimbingan');
                if (input) {
                    input.classList.remove('is-invalid');
                    input.value = '';
                }
                currentRow = null;
                currentId = null;
            } else if (modalId === 'modalTolak') {
                const input = document.getElementById('alasanPenolakan');
                if (input) {
                    input.classList.remove('is-invalid');
                    input.value = '';
                }
                currentRow = null;
                currentId = null;
            } else if (modalId === 'modalSelesai') {
                currentSelesaiId = null;
            } else if (modalId === 'modalBatal') {
                const input = document.getElementById('alasanPembatalan');
                if (input) {
                    input.classList.remove('is-invalid');
                    input.value = '';
                }
                currentBatalId = null;
            }
        });
    });
});
</script>
@endpush
