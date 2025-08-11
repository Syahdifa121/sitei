<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card-body p-4">
                    <div id="map">
                        <div id="filterButton">⚙️</div>
                        <div id="filterPopup">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Filter Alumni</h5>
                                <button type="button" id="closeFilterPopup" class="btn-close"></button>
                            </div>
                            <form id="filterForm">
                                <div class="mb-3">
                                    <label class="form-label">Nama Alumni</label>
                                    <select id="filterName" class="form-select">
                                        <option value="">Cari Nama Alumni</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Perusahaan</label>
                                    <select id="companySelect" class="form-select">
                                        <option value="">Pilih Perusahaan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Provinsi</label>
                                    <select id="provinceSelect" class="form-select">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                <label class="form-label">Kota</label>
                                    <select id="citySelect" class="form-select" disabled>
                                        <option value="">Pilih Kota</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tahun Lulus</label>
                                    <select id="yearSelect" class="form-select">
                                        <option value="">Semua</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status Pekerjaan</label>
                                    <select id="jobStatusSelect" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="Bekerja">Bekerja</option>
                                        <option value="Wirausaha">Wirausaha</option>
                                        <option value="Studi Lanjut">Studi Lanjut</option>
                                        <option value="Mencari Kerja">Mencari Kerja</option>
                                    </select>
                                </div>
                                  <div class="mb-3">
                                      <label class="form-label">Pekerjaan</label>
                                      <select id="jobSelect" class="form-select">
                                          <option value="">Pilih Pekerjaan</option>
                                      </select>
                                  </div>
                                  <div class="d-flex gap-2">
                                      <button type="button" class="btn btn-gradient flex-grow-1" id="filterButton2">
                                          Filter
                                      </button>
                                      <button type="button" class="btn btn-secondary flex-grow-1" id="resetButton">
                                        Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Pekerjaan</th>
                                    <th>Perusahaan</th>
                                    <th>Provinsi</th>
                                    <th>Kota</th>
                                    <th>Tahun Lulus</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="alumniTableBody">
                                <tr>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">tidak ada data</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-info btn-sm btn-detail-alumni" 
                                                data-nama="" data-email="" data-nim="" data-tahun-lulus="" data-no-telepon="" data-alamat="" data-nama-perusahaan="" data-provinsi="" data-kota="" data-created-at="">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>   
                                </tr>
                            </tbody>
                        </table>
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
                                                <p class="mb-0" id="modal-nim">-</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">Tahun Lulus</label>
                                                <p class="mb-0" id="modal-tahun-lulus">-</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">No. Telepon</label>
                                                <p class="mb-0" id="modal-no-telepon">-</p>
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
                                                <label class="form-label fw-bold text-muted">Provinsi</label>
                                                <p class="mb-0" id="modal-provinsi">-</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted">Kota</label>
                                                <p class="mb-0" id="modal-kota">-</p>
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
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-3">
                        <p class="mb-2" id="tableInfo">
                            Menampilkan 0 sampai 0 dari 0 entri
                        </p>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end mb-0" id="pagination">
                                <!-- Will be populated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.1/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css" />
    <style>
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
        #map {
            height: 800px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        #filterPopup {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 350px;
            display: none;
        }
        #filterButton { 
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        #filterButton:hover {
            transform: translateY(-2px);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 0 6px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px ;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 500;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
        }
        @media (max-width: 768px) {
            #filterPopup {
            width: 90%;
            left: 5%;
            top: 10%;
            bottom: auto;
            max-height: 70vh;
            overflow-y: auto;
            padding-bottom: 16px;
            font-size: 0px;
            }
        
            #filterPopup select,
            #filterPopup input,
            #filterPopup label {
                font-size: 10px !important:
                padding: 6px 8px;
            }
        
            #filterPopup .form-group {
                margin-bottom: 4px;
            }
            #map {
                width: 100%;
                margin-left: 0;
                margin-right: 0;
                height: 650px;
                border-radius: 0;
            }
            .card-body {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            .row, .col-md-12 {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endpush

@push('scripts')
    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <!-- Load Bootstrap after jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- Load Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.1/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    
    <!-- Load Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Load your custom map script -->
    <script src="{{ asset('js/map-init.js') }}"></script>
    
    <!-- Custom script untuk modal handling -->
    <script>
        // Pastikan DOM sudah ready dan semua library sudah dimuat
        $(document).ready(function() {
            // Check if Bootstrap is loaded
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is not loaded properly');
                return;
            }

            // Event delegation untuk tombol detail alumni
            $(document).on('click', '.btn-detail-alumni', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                try {
                    const $btn = $(this);
                    
                    // Ambil data dari atribut tombol
                    const data = {
                        nama: $btn.data('nama') || $btn.attr('data-nama') || '-',
                        email: $btn.data('email') || $btn.attr('data-email') || '-',
                        nim: $btn.data('nim') || $btn.attr('data-nim') || '-',
                        tahunLulus: $btn.data('tahun-lulus') || $btn.attr('data-tahun-lulus') || '-',
                        noTelepon: $btn.data('no-telepon') || $btn.attr('data-no-telepon') || '-',
                        alamat: $btn.data('alamat') || $btn.attr('data-alamat') || '-',
                        namaPerusahaan: $btn.data('nama-perusahaan') || $btn.attr('data-nama-perusahaan') || '-',
                        provinsi: $btn.data('provinsi') || $btn.attr('data-provinsi') || '-',
                        kota: $btn.data('kota') || $btn.attr('data-kota') || '-',
                        createdAt: $btn.data('created-at') || $btn.attr('data-created-at') || '-'
                    };

                    // Isi modal dengan data
                    $('#modal-nama').text(data.nama);
                    $('#modal-email').text(data.email);
                    $('#modal-nim').text(data.nim);
                    $('#modal-tahun-lulus').text(data.tahunLulus);
                    $('#modal-no-telepon').text(data.noTelepon);
                    $('#modal-alamat').text(data.alamat);
                    $('#modal-nama-perusahaan').text(data.namaPerusahaan);
                    $('#modal-provinsi').text(data.provinsi);
                    $('#modal-kota').text(data.kota);
                    $('#modal-created-at').text(data.createdAt);

                    // Tampilkan modal menggunakan Bootstrap 5 syntax
                    const modalElement = document.getElementById('modalDetailAlumni');
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: true,
                            keyboard: true,
                            focus: true
                        });
                        modal.show();
                    } else {
                        // Fallback menggunakan jQuery jika element tidak ditemukan
                        $('#modalDetailAlumni').modal('show');
                    }

                } catch (error) {
                    console.error('Error showing modal:', error);
                    alert('Terjadi kesalahan saat menampilkan detail alumni');
                }
            });

            // Handle modal events
            $('#modalDetailAlumni').on('hidden.bs.modal', function() {
                // Reset form jika ada
                $(this).find('form')[0]?.reset();
            });
        });

        // Fallback function jika masih ada referensi showDetails
        function showDetails(alumni) {
            console.warn('showDetails function is deprecated');
            
            // Cari tombol yang sesuai dan trigger click
            const $btn = $(`.btn-detail-alumni[data-nama="${alumni.name}"]`).first();
            if ($btn.length) {
                $btn.trigger('click');
            } else {
                console.error('Button not found for alumni:', alumni.name);
            }
        }

        // Helper function untuk render table (jika diperlukan)
        function renderTable(data) {
            const $tableBody = $('#alumniTableBody');
            if (!$tableBody.length) return;

            $tableBody.empty();
            
            if (!data || data.length === 0) {
                $tableBody.html('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            // Sort data by name
            data.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
            
            data.forEach((alumni, index) => {
                const row = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${alumni.name || '-'}</td>
                        <td class="text-center">${alumni.job || '-'}</td>
                        <td class="text-center">${alumni.company || '-'}</td>
                        <td class="text-center">${alumni.province || '-'}</td>
                        <td class="text-center">${alumni.city || '-'}</td>
                        <td class="text-center">${alumni.graduationYear || '-'}</td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-info btn-sm btn-detail-alumni"
                                    data-nama="${escapeHtml(alumni.name || '')}"
                                    data-email="${escapeHtml(alumni.email || '')}"
                                    data-nim="${escapeHtml(alumni.nim || '')}"
                                    data-tahun-lulus="${escapeHtml(alumni.graduationYear || '')}"
                                    data-no-telepon="${escapeHtml(alumni.phone || '')}"
                                    data-alamat="${escapeHtml(alumni.address || '')}"
                                    data-nama-perusahaan="${escapeHtml(alumni.company || '')}"
                                    data-provinsi="${escapeHtml(alumni.province || '')}"
                                    data-kota="${escapeHtml(alumni.city || '')}"
                                    data-created-at="${escapeHtml(alumni.createdAt || '')}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $tableBody.append(row);
            });
        }

        // Helper function untuk escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
@endpush