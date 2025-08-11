@extends('layouts.app')

@section('title', 'Detail Usulan Bimbingan')

@push('styles')
<style>
    .status {
        color: white;
        border-radius: 5px;
        font-size: 0.9em;
        display: inline-block;
        margin-top: 10px;
        padding: 5px 15px;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Detail Mahasiswa</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('dosen.persetujuan', ['tab' => 'usulan']) }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </button>

    <div class="container">
        <div class="row">
            <!-- Mahasiswa Info Card -->
            <div class="col-lg-6 col-md-12 bg-white rounded-start px-4 py-3 mb-2 shadow-sm">
                <h5 class="text-bold">Mahasiswa</h5>
                <hr>
                <p class="card-title text-muted text-sm">Nama</p>
                <p class="card-text text-start">{{ $usulan->mahasiswa_nama }}</p>
                <p class="card-title text-muted text-sm">NIM</p>
                <p class="card-text text-start">{{ $usulan->nim }}</p>
                <p class="card-title text-muted text-sm">Program Studi</p>
                <p class="card-text text-start">{{ $usulan->nama_prodi }}</p>
                <p class="card-title text-muted text-sm">Konsentrasi</p>
                <p class="card-text text-start">{{ $usulan->nama_konsentrasi }}</p>
            </div>

            <!-- Dosen Pembimbing Card -->
            <div class="col-lg-6 col-md-12 bg-white rounded-end px-4 py-3 mb-2 shadow-sm">
                <h5 class="text-bold">Dosen Pembimbing</h5>
                <hr>
                <p class="card-title text-secondary text-sm">Nama Pembimbing</p>
                <p class="card-text text-start">{{ $usulan->dosen_nama }}</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Usulan Jadwal Card -->
            <div class="col-lg-6 col-md-12 px-4 py-3 mb-2 bg-white rounded-start shadow-sm">
                <h5 class="text-bold">Data Usulan Jadwal Bimbingan</h5>
                <hr>
                <p class="card-title text-muted text-sm">Jenis Bimbingan</p>
                <p class="card-text text-start">{{ ucfirst($usulan->jenis_bimbingan) }}</p>
                <p class="card-title text-muted text-sm">Tanggal</p>
                <p class="card-text text-start">{{ $tanggal }}</p>
                <p class="card-title text-muted text-sm">Waktu</p>
                <p class="card-text text-start">{{ $waktu }}</p>
                <p class="card-title text-muted text-sm">Deskripsi</p>
                <p class="card-text text-start">{{ $usulan->deskripsi ?? '-' }}</p>
            </div>

            <!-- Keterangan Usulan Card -->
            <div class="col-lg-6 col-md-12 px-4 py-3 mb-2 bg-white rounded-end shadow-sm">
                <h5 class="text-bold">Keterangan Usulan</h5>
                <hr>
                <p class="card-title text-secondary text-sm">Status Usulan</p>
                <p class="card-text text-start">
                    <span id="status" class="status {{ $statusClass }}">{{ $usulan->status }}</span>
                </p>
                <p class="card-title text-secondary text-sm">Keterangan</p>
                <p id="keteranganText" class="card-text text-start">
                    @if($usulan->status == 'DISETUJUI')
                        Lokasi: {{ $usulan->lokasi }}
                    @elseif($usulan->status == 'DITOLAK')
                        {{ $usulan->keterangan }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    @if($usulan->status == 'USULAN')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 mb-2">
                <button id="btnTerima" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modalTerima">
                    Terima
                </button>
            </div>
            <div class="col-md-12 mb-2">
                <button id="btnTolak" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalTolak">
                    Tolak
                </button>
            </div>
        </div>
    </div>
    @endif
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
                <!-- Detail Usulan -->
                <div class="bg-light p-3 rounded mb-3">
                    <h6 class="fw-bold mb-3">Detail Usulan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>NIM:</strong><br>{{ $usulan->nim }}</p>
                            <p class="mb-2"><strong>Nama:</strong><br>{{ $usulan->mahasiswa_nama }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Tanggal:</strong><br>{{ $tanggal }}</p>
                            <p class="mb-2"><strong>Waktu:</strong><br>{{ $waktu }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Jenis Bimbingan:</strong><br>{{ ucfirst($usulan->jenis_bimbingan) }}</p>
                </div>

                <!-- Form Lokasi -->
                <div class="form-group">
                    <label for="lokasiBimbingan" class="form-label fw-bold">Lokasi Bimbingan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-location-dot"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="lokasiBimbingan" 
                               required 
                               value="{{ $usulan->lokasi_default ?? '' }}">
                    </div>
                    <div class="invalid-feedback">Lokasi bimbingan wajib diisi</div>
                    <small class="text-muted">Masukkan lokasi fisik atau link meeting untuk bimbingan online</small>
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
                <!-- Detail Usulan -->
                <div class="bg-light p-3 rounded mb-3">
                    <h6 class="fw-bold mb-3">Detail Usulan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>NIM:</strong><br>{{ $usulan->nim }}</p>
                            <p class="mb-2"><strong>Nama:</strong><br>{{ $usulan->mahasiswa_nama }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Tanggal:</strong><br>{{ $tanggal }}</p>
                            <p class="mb-2"><strong>Waktu:</strong><br>{{ $waktu }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Jenis Bimbingan:</strong><br>{{ ucfirst($usulan->jenis_bimbingan) }}</p>
                </div>

                <!-- Form Alasan -->
                <div class="form-group">
                    <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-comment-alt"></i>
                        </span>
                        <textarea class="form-control" 
                                  id="alasanPenolakan" 
                                  rows="3" 
                                  required></textarea>
                    </div>
                    <div class="invalid-feedback">Alasan penolakan wajib diisi</div>
                    <small class="text-muted">Berikan alasan yang jelas mengapa Anda menolak usulan ini</small>
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

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const confirmTerimaBtn = document.getElementById('confirmTerima');
    const confirmTolakBtn = document.getElementById('confirmTolak');
    const statusElement = document.getElementById('status');
    const keteranganText = document.getElementById('keteranganText');

    // Handler untuk menerima usulan
    confirmTerimaBtn?.addEventListener('click', async function() {
        const lokasiInput = document.getElementById('lokasiBimbingan');
        const lokasi = lokasiInput.value.trim();
        
        // Validasi input
        if (!lokasi) {
            lokasiInput.classList.add('is-invalid');
            return;
        }
        
        lokasiInput.classList.remove('is-invalid');
        
        try {
            // Tampilkan loading state
            confirmTerimaBtn.disabled = true;
            confirmTerimaBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            // Kirim request ke endpoint terima
            const response = await fetch(`/terimausulanbimbingan/terima/{{ $usulan->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ lokasi })
            });

            const data = await response.json();

            if (data.success) {
                // Update UI
                statusElement.className = 'status bg-success';
                statusElement.textContent = 'DISETUJUI';
                keteranganText.textContent = `Lokasi: ${lokasi}`;
                
                // Sembunyikan tombol
                const btnTerima = document.getElementById('btnTerima');
                const btnTolak = document.getElementById('btnTolak');
                if (btnTerima) btnTerima.remove();
                if (btnTolak) btnTolak.remove();
                
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTerima'));
                modal?.hide();

                // Tampilkan notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memproses usulan'
            });
        } finally {
            // Reset loading state
            confirmTerimaBtn.disabled = false;
            confirmTerimaBtn.innerHTML = '<i class="fas fa-check me-2"></i>Setujui Usulan';
        }
    });

    // Handler untuk menolak usulan
    confirmTolakBtn?.addEventListener('click', async function() {
        const alasanInput = document.getElementById('alasanPenolakan');
        const keterangan = alasanInput.value.trim();
        
        // Validasi input
        if (!keterangan) {
            alasanInput.classList.add('is-invalid');
            return;
        }
        
        alasanInput.classList.remove('is-invalid');
        
        try {
            // Tampilkan loading state
            confirmTolakBtn.disabled = true;
            confirmTolakBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            // Kirim request ke endpoint tolak
            const response = await fetch(`/terimausulanbimbingan/tolak/{{ $usulan->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ keterangan })
            });

            const data = await response.json();

            if (data.success) {
                // Update UI
                statusElement.className = 'status bg-danger';
                statusElement.textContent = 'DITOLAK';
                keteranganText.textContent = keterangan;
                
                // Sembunyikan tombol
                const btnTerima = document.getElementById('btnTerima');
                const btnTolak = document.getElementById('btnTolak');
                if (btnTerima) btnTerima.remove();
                if (btnTolak) btnTolak.remove();
                
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTolak'));
                modal?.hide();

                // Tampilkan notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memproses usulan'
            });
        } finally {
            // Reset loading state
            confirmTolakBtn.disabled = false;
            confirmTolakBtn.innerHTML = '<i class="fas fa-times me-2"></i>Tolak Usulan';
        }
    });

    // Reset validasi saat modal ditutup
    ['modalTerima', 'modalTolak'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal?.addEventListener('hidden.bs.modal', function() {
            const input = modalId === 'modalTerima' ? 
                document.getElementById('lokasiBimbingan') : 
                document.getElementById('alasanPenolakan');
            
            if (input) {
                input.classList.remove('is-invalid');
                input.value = modalId === 'modalTerima' ? 
                    '{{ $usulan->lokasi_default ?? "" }}' : '';
            }
        });
    });
});
</script>
@endpush