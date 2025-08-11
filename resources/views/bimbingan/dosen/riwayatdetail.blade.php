@extends('layouts.app')

@section('title', 'Riwayat Bimbingan Dosen')

@push('styles')
<style>
    form .form-label {
        font-weight: bold;
    }
    
    select.form-select option {
        color: black;
        font-weight: bold;
    }

    select.form-select option:disabled {
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Riwayat Bimbingan Dosen</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('dosen.persetujuan', ['tab' => 'pengelola']) }}">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </button>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white p-0">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link px-4 py-3">Riwayat Bimbingan {{ $dosen->nama }}</a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content">
                <div class="tab-pane fade show active" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2">Tampilkan</label>
                            <select class="form-select form-select-sm w-auto" id="show-entries">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            </select>
                            <label class="ms-2">entri</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="text-center table-secondary">
                                <tr>
                                    <th scope="col" class="text-center align-middle">No.</th>
                                    <th scope="col" class="text-center align-middle">NIM</th>
                                    <th scope="col" class="text-center align-middle">Nama</th>
                                    <th scope="col" class="text-center align-middle">Jenis Bimbingan</th>
                                    <th scope="col" class="text-center align-middle">Tanggal</th>
                                    <th scope="col" class="text-center align-middle">Waktu</th>
                                    <th scope="col" class="text-center align-middle">Lokasi</th>
                                    <th scope="col" class="text-center align-middle">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bimbingan as $index => $item)
                                <tr class="text-center">
                                    <td>{{ ($bimbingan->currentPage() - 1) * $bimbingan->perPage() + $loop->iteration }}</td>
                                    <td>{{ $item->nim }}</td>
                                    <td>{{ $item->mahasiswa_nama }}</td>
                                    <td>{{ $item->jenis_bimbingan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMMM Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</td>
                                    <td>{{ $item->lokasi ?? '-' }}</td>
                                    <td class="fw-bold {{ 
                                        $item->status === 'DISETUJUI' ? 'bg-success' : (
                                            $item->status === 'DITOLAK' ? 'bg-danger' : (
                                                $item->status === 'DIBATALKAN' ? 'bg-secondary' : (
                                                    $item->status === 'SELESAI' ? 'bg-primary' : 'bg-warning'
                                                )
                                            )
                                        ) 
                                    }} text-white">{{ $item->status }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data riwayat bimbingan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($bimbingan instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-3">
    <p class="mb-2">
        Menampilkan {{ $bimbingan->firstItem() ?? 0 }} sampai {{ $bimbingan->lastItem() ?? 0 }} 
        dari {{ $bimbingan->total() ?? 0 }} entri
    </p>
    <nav aria-label="Page navigation">
        <ul class="pagination mb-0 justify-content-center">
            {{-- Previous Page Link --}}
            @if ($bimbingan->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">« Sebelumnya</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $bimbingan->previousPageUrl() }}" rel="prev">« Sebelumnya</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
            $urlParams = request()->except('page');
            @endphp

            @foreach ($bimbingan->getUrlRange(1, $bimbingan->lastPage()) as $page => $url)
                @php
                $currentUrl = $url;
                foreach($urlParams as $key => $value) {
                    $currentUrl .= (parse_url($currentUrl, PHP_URL_QUERY) ? '&' : '?') . $key . '=' . $value;
                }
                @endphp
                
                @if ($page == $bimbingan->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $currentUrl }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($bimbingan->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $bimbingan->nextPageUrl() }}" rel="next">Selanjutnya »</a>
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
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const showEntries = document.getElementById('show-entries');
    if (showEntries) {
        showEntries.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Reset ke halaman 1
            window.location.href = url.toString();
        });
    }
});
</script>
@endpush