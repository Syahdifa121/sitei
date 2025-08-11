<div>
    @if($currentTab === 'data-alumni')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span>Tampilkan</span>
                    <select class="form-select form-select-sm" style="width: auto;" id="per_page_select">
                        <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $currentPerPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $currentPerPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-2"></i>Download Data
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="downloadDropdown">
                            <li><a class="dropdown-item" href="#" onclick="downloadAllData('csv')">
                                <i class="fas fa-file-csv text-info me-2"></i>Download CSV (.csv)
                            </a></li>
                        </ul>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <span>Cari</span>
                        <input type="text" class="form-control form-control-sm" 
                        id="search_input" placeholder="Cari alumni..." 
                        value="{{ $currentSearch }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">No</th>
                            <th>
                                <a href="{{ url()->current() }}?tab=data-alumni&sort=nama&order={{ $currentSort == 'nama' && $currentOrder == 'asc' ? 'desc' : 'asc' }}&search={{ $currentSearch }}&per_page={{ $currentPerPage }}" 
                                   class="text-white text-decoration-none">
                                    Nama
                                    <span class="float-end">
                                        @if($currentSort == 'nama')
                                            <i class="fas fa-sort-{{ $currentOrder == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </span>
                                </a>
                            </th>
                            <th>
                                <a href="{{ url()->current() }}?tab=data-alumni&sort=email&order={{ $currentSort == 'email' && $currentOrder == 'asc' ? 'desc' : 'asc' }}&search={{ $currentSearch }}&per_page={{ $currentPerPage }}" 
                                   class="text-white text-decoration-none">
                                    Email
                                    <span class="float-end">
                                        @if($currentSort == 'email')
                                            <i class="fas fa-sort-{{ $currentOrder == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </span>
                                </a>
                            </th>
                            <th>
                                <a href="{{ url()->current() }}?tab=data-alumni&sort=tahun_lulus&order={{ $currentSort == 'tahun_lulus' && $currentOrder == 'asc' ? 'desc' : 'asc' }}&search={{ $currentSearch }}&per_page={{ $currentPerPage }}" 
                                   class="text-white text-decoration-none">
                                    Tahun Lulus
                                    <span class="float-end">
                                        @if($currentSort == 'tahun_lulus')
                                            <i class="fas fa-sort-{{ $currentOrder == 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </span>
                                </a>
                            </th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allAlumni as $key => $item)
                            <tr>
                                <td class="text-center">{{ ($allAlumni->currentPage() - 1) * $allAlumni->perPage() + $key + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->tahun_lulus ?: '-' }}</td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-info text-white border-0 btn-detail" 
                                            title="Detail" 
                                            style="background-color: #17a2b8"
                                            data-id="{{ $item->id ?? '' }}"
                                            data-nama="{{ $item->nama ?? '' }}"
                                            data-email="{{ $item->email ?? '' }}"
                                            data-user-nim="{{ $item->user_nim ?? '' }}"
                                            data-alamat="{{ $item->alamat ?? '' }}"
                                            data-no-telepon="{{ $item->no_telepon ?? '' }}"
                                            data-tahun-lulus="{{ $item->tahun_lulus ?? '' }}"
                                            data-lokasi-pekerjaan-provinsi="{{ $item->lokasi_pekerjaan_provinsi ?? '' }}"
                                            data-lokasi-pekerjaan-kabupaten="{{ $item->lokasi_pekerjaan_kabupaten ?? '' }}"
                                            data-nama-perusahaan="{{ $item->nama_perusahaan ?? '' }}"
                                            data-created-at="{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '' }}">
                                        <i class="fas fa-circle-info"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data alumni</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <p class="mb-0">
                Menampilkan {{ $allAlumni->firstItem() ?: '0' }} sampai 
                {{ $allAlumni->lastItem() ?: '0' }} dari 
                {{ $allAlumni->total() }} entri
            </p>
        </div>
        <div class="col-md-6">
            <div class="float-end">
                @if($allAlumni->hasPages())
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm">
                        {{-- Previous Page Link --}}
                        <li class="page-item {{ $allAlumni->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $allAlumni->previousPageUrl() }}&tab=data-alumni&sort={{ $currentSort }}&order={{ $currentOrder }}&search={{ $currentSearch }}&per_page={{ $currentPerPage }}" 
                               rel="prev">
                                Sebelumnya
                            </a>
                        </li>
                        
                        {{-- Pagination Elements --}}
                        @foreach ($allAlumni->getUrlRange(1, $allAlumni->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $allAlumni->currentPage() ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $url }}&tab=data-alumni&sort={{ $currentSort }}&order={{ $currentOrder }}&search={{ $currentSearch }}&per_page={{ $currentPerPage }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endforeach
                        
                        {{-- Next Page Link --}}
                        <li class="page-item {{ !$allAlumni->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $allAlumni->nextPageUrl() }}&tab=data-alumni&sort={{ $currentSort }}&order={{ $currentOrder }}&search={{ $currentSearch }}&per_page={{ $currentPerPage }}" 
                               rel="next">
                                Selanjutnya
                            </a>
                        </li>
                    </ul>
                </nav>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Detail Alumni -->
    <div class="modal fade" id="modalDetailAlumni" tabindex="-1" aria-labelledby="modalDetailAlumniLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-card-gradient text-white">
                    <h5 class="modal-title" id="modalDetailAlumniLabel">
                        <i class="fas fa-user-graduate me-2"></i>Detail Alumni
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nama Lengkap</label>
                                <p class="mb-0" id="modal-nama">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Email</label>
                                <p class="mb-0" id="modal-email">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">NIM</label>
                                <p class="mb-0" id="modal-user-nim">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Tahun Lulus</label>
                                <p class="mb-0" id="modal-tahun-lulus">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Alamat</label>
                                <p class="mb-0" id="modal-alamat">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nama Perusahaan</label>
                                <p class="mb-0" id="modal-nama-perusahaan">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Lokasi Pekerjaan (Provinsi)</label>
                                <p class="mb-0" id="modal-lokasi-pekerjaan-provinsi">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Lokasi Pekerjaan (Kabupaten)</label>
                                <p class="mb-0" id="modal-lokasi-pekerjaan-kabupaten">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Tanggal Registrasi</label>
                                <p class="mb-0" id="modal-created-at">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Download button styling */
.dropdown-toggle {
    transition: all 0.3s ease;
}

.dropdown-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.dropdown-menu {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    border-radius: 8px;
    overflow: hidden;
}

.dropdown-item {
    transition: all 0.2s ease;
    padding: 0.75rem 1rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

/* Loading overlay for download */
.download-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.download-spinner {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.spinner-border-custom {
    width: 3rem;
    height: 3rem;
    border: 0.25em solid #dee2e6;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spin 0.75s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

/* Hover effects for detail button */
.btn-detail {
    transition: all 0.3s ease;
}

.btn-detail:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    background-color: #138496 !important;
}

/* Modal backdrop with smooth transition */
.modal-backdrop {
    transition: opacity 0.15s linear;
}

/* Custom modal content styling */
.modal-content {
    border: none;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

/* Loading animation for button */
.btn-detail.loading {
    position: relative;
    color: transparent !important;
}

.btn-detail.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Form label styling */
.form-label.fw-bold {
    font-size: 0.875rem;
    color: #6c757d !important;
    margin-bottom: 0.25rem;
}

/* Modal body content styling */
.modal-body p {
    font-size: 1rem;
    color: #212529;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.modal-body .mb-3:last-child p {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle per page change
    document.getElementById('per_page_select').addEventListener('change', function() {
        const perPage = this.value;
        updateUrlParams({per_page: perPage, page: 1});
    });

    // Handle search input with debounce
    let searchTimer;
    document.getElementById('search_input').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            updateUrlParams({search: this.value, page: 1});
        }, 500);
    });

    // Handle detail button click
    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function() {
            // Add loading state
            this.classList.add('loading');
            
            // Get data from button attributes
            const data = {
                nama: this.dataset.nama || '-',
                email: this.dataset.email || '-',
                userNim: this.dataset.userNim || '-',
                tahunLulus: this.dataset.tahunLulus || '-',
                noTelepon: this.dataset.noTelepon || '-',
                alamat: this.dataset.alamat || '-',
                namaPerusahaan: this.dataset.namaPerusahaan || '-',
                lokasiPekerjaanProvinsi: this.dataset.lokasiPekerjaanProvinsi || '-',
                lokasiPekerjaanKabupaten: this.dataset.lokasiPekerjaanKabupaten || '-',
                createdAt: this.dataset.createdAt || '-'
            };

            // Simulate loading delay for smooth transition
            setTimeout(() => {
                // Remove loading state
                this.classList.remove('loading');
                
                // Populate modal with data
                populateModal(data);
                
                // Show modal with smooth transition
                const modal = new bootstrap.Modal(document.getElementById('modalDetailAlumni'), {
                    backdrop: 'static',
                    keyboard: true
                });
                modal.show();
            }, 300);
        });
    });

    // Function to populate modal with alumni data
    function populateModal(data) {
        document.getElementById('modal-nama').textContent = data.nama;
        document.getElementById('modal-email').textContent = data.email;
        document.getElementById('modal-user-nim').textContent = data.userNim;
        document.getElementById('modal-tahun-lulus').textContent = data.tahunLulus;
        document.getElementById('modal-alamat').textContent = data.alamat;
        document.getElementById('modal-nama-perusahaan').textContent = data.namaPerusahaan;
        document.getElementById('modal-lokasi-pekerjaan-provinsi').textContent = data.lokasiPekerjaanProvinsi;
        document.getElementById('modal-lokasi-pekerjaan-kabupaten').textContent = data.lokasiPekerjaanKabupaten;
        document.getElementById('modal-created-at').textContent = data.createdAt;
    }

    // Function to update URL parameters
    function updateUrlParams(params) {
        const currentUrl = new URL(window.location.href);
        const searchParams = currentUrl.searchParams;
        
        // Update parameters
        Object.keys(params).forEach(key => {
            searchParams.set(key, params[key]);
        });
        
        // Always maintain the tab parameter
        searchParams.set('tab', 'data-alumni');
        
        window.location.href = currentUrl.toString();
    }

    // Enhanced modal event listeners for smooth animations
    const modalElement = document.getElementById('modalDetailAlumni');
    
    modalElement.addEventListener('show.bs.modal', function() {
        document.body.style.overflow = 'hidden';
    });
    
    modalElement.addEventListener('hidden.bs.modal', function() {
        document.body.style.overflow = 'auto';
    });

    // Download functionality - Modified for Laravel integration
    window.downloadAllData = function(format) {
        // Show loading overlay
        showDownloadLoading();
        
        // Create form for POST request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.alumni.download") }}';
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add format parameter
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);
        
        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Hide loading after a short delay
        setTimeout(() => {
            hideDownloadLoading();
            showDownloadSuccess(format);
        }, 1500);
    };
    
    function showDownloadLoading() {
        const overlay = document.createElement('div');
        overlay.className = 'download-overlay';
        overlay.id = 'downloadOverlay';
        overlay.innerHTML = `
            <div class="download-spinner">
                <div class="spinner-border-custom mb-3 mx-auto"></div>
                <h5>Memproses Download...</h5>
                <p class="mb-0 text-muted">Mohon tunggu sebentar</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    
    function hideDownloadLoading() {
        const overlay = document.getElementById('downloadOverlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    function showDownloadSuccess(format) {
        // Create success toast
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000;';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    File ${format.toUpperCase()} berhasil didownload!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove toast after 3 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 3000);
    }
});
</script>