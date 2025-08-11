<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITEI - Data Usulan</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts (Open Sans dan Viga) -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Viga&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        .bg-gradient-bar {
            height: 3px;
            background: linear-gradient(to right, #4ade80, #3b82f6, #8b5cf6);
        }

        .blob-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(70px);
            mix-blend-mode: multiply;
            animation: blob 7s infinite;
            pointer-events: none;
        }

        .blob-1 {
            top: 0;
            left: 0;
            width: 300px;
            height: 300px;
            background-color: rgba(74, 222, 128, 0.1);
        }

        .blob-2 {
            top: 50%;
            right: 0;
            width: 350px;
            height: 350px;
            background-color: rgba(251, 191, 36, 0.1);
            animation-delay: 2s;
        }

        .blob-3 {
            bottom: 0;
            left: 50%;
            width: 350px;
            height: 350px;
            background-color: rgba(239, 68, 68, 0.1);
            animation-delay: 4s;
        }

        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(20px, -50px) scale(1.1);
            }

            50% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            75% {
                transform: translate(50px, 50px) scale(1.05);
            }
        }

        .navbar {
            box-shadow: 0px 0px 10px 1px #afafaf;
        }

        .navbar-brand {
            font-family: "Viga", sans-serif;
            font-weight: 600;
            font-size: 25px;
        }

        .nav-link {
            position: relative;
            color: #4b5563;
            transition: color 0.3s ease;
            font-weight: bold;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #059669;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #059669;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .gradient-text {
            background: linear-gradient(to right, #059669, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gradient {
            background: linear-gradient(to right, #4ade80, #3b82f6);
            border: none;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            z-index: 1;
            cursor: pointer;
        }

        .btn-gradient a {
            color: white;
            text-decoration: none;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-gradient:hover a {
            color: black;
        }

        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 12px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .green-text {
            color: #28a745;
        }

        .container {
            flex: 1;
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

        .approve-icon {
            background-color: #28a745;
            color: white !important;
        }

        .reject-icon {
            background-color: #dc3545;
            color: white !important;
        }

        .action-icon.edit-icon {
            background-color: #F3B806;
            color: #FFFFFF !important;
            padding: 6px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-icon.edit-icon:hover {
            background-color: #d69e05;
        }

        .action-icon.delete-icon {
            background-color: #6c757d;
            color: white !important;
        }

        .action-icon.delete-icon:hover {
            background-color: #5a6268;
        }

        .modal-header {
            background: linear-gradient(to right, #4ade80, #3b82f6);
            color: white;
        }

        .modal-title {
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-light">
    <div class="bg-gradient-bar"></div>
    <div class="blob-container">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand me-4" href="/">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2c/LOGO-UNRI.png" alt="SITEI Logo"
                    width="30" height="30" class="d-inline-block align-text-top me-2">
                SITEI
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" style="font-weight: bold;" href="/">BIMBINGAN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="font-weight: bold;" href="/dashboardpesan">PESAN</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn text-dark dropdown-toggle" style="font-weight: bold;" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            AKUN
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/login">Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Persetujuan Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
            <a href="/masukkanjadwal">
                <i class="bi bi-plus-lg me-2"></i> Masukkan Jadwal Bimbingan
            </a>
        </button>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-3" id="usulan-tab" data-bs-toggle="tab"
                            data-bs-target="#usulan" type="button" role="tab" aria-controls="usulan"
                            aria-selected="true">
                            Usulan Bimbingan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3" id="jadwal-tab" data-bs-toggle="tab" data-bs-target="#jadwal"
                            type="button" role="tab" aria-controls="jadwal" aria-selected="false">
                            Daftar Jadwal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-3" id="riwayat-tab" data-bs-toggle="tab"
                            data-bs-target="#riwayat" type="button" role="tab" aria-controls="riwayat"
                            aria-selected="false">
                            Riwayat
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="bimbinganTabContent">
                    <div class="tab-pane fade show active" id="usulan" role="tabpanel"
                        aria-labelledby="usulan-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                Tampilkan
                                <select class="form-select form-select-sm d-inline-block w-auto">
                                    <option>50</option>
                                </select>
                                entri
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-center">No.</th>
                                        <th class="text-center">NIM</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Jenis Bimbingan</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Waktu</th>
                                        <th class="text-center">Lokasi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelUsulan">
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">2107112735</td>
                                        <td class="text-center">Tri Murniati</td>
                                        <td class="text-center">Bimbingan Kerja Praktek</td>
                                        <td class="text-center">Senin, 4 Oktober 2024</td>
                                        <td class="text-center">13.30 - 16.00</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">USULAN</td>
                                        <td class="text-center">
                                            <div class="action-icons">
                                                <a href="/terimausulanbimbingan" class="action-icon info-icon">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                                <a href="#" class="action-icon approve-icon"
                                                    data-bs-toggle="modal" data-bs-target="#modalTerima">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="#" class="action-icon reject-icon"
                                                    data-bs-toggle="modal" data-bs-target="#modalTolak">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                <a href="#" class="action-icon delete-icon"
                                                    data-bs-toggle="modal" data-bs-target="#modalHapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td class="text-center">2107110255</td>
                                        <td class="text-center">Syahirah Tri Meilina</td>
                                        <td class="text-center">Bimbingan Skripsi</td>
                                        <td class="text-center">Senin, 30 September 2024</td>
                                        <td class="text-center">13.30 - 16.00</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">USULAN</td>
                                        <td class="text-center">
                                            <div class="action-icons">
                                                <a href="/terimausulanbimbingan" class="action-icon info-icon"
                                                    title="Lihat Detail">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                                <a href="#" class="action-icon approve-icon"
                                                    data-bs-toggle="modal" data-bs-target="#modalTerima"
                                                    title="Terima Usulan">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="#" class="action-icon reject-icon"
                                                    data-bs-toggle="modal" data-bs-target="#modalTolak"
                                                    title="Tolak Usulan">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>

                                            <!-- Modal Templates -->
                                            <div class="modal fade" id="modalTerima" tabindex="-1"
                                                aria-labelledby="modalTerimaLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalTerimaLabel">Terima
                                                                Usulan Bimbingan</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menerima usulan bimbingan ini?
                                                            </p>
                                                            <div class="usulan-detail mt-3">
                                                                <p><strong>NIM:</strong> <span
                                                                        class="nim-display"></span></p>
                                                                <p><strong>Nama:</strong> <span
                                                                        class="nama-display"></span></p>
                                                                <p><strong>Jenis Bimbingan:</strong> <span
                                                                        class="jenis-display"></span></p>
                                                            </div>
                                                            <div class="form-group mt-3">
                                                                <label for="lokasiBimbingan">Lokasi Bimbingan:</label>
                                                                <input type="text" class="form-control"
                                                                    id="lokasiBimbingan" required
                                                                    placeholder="Masukkan lokasi bimbingan">
                                                                <div class="invalid-feedback">Silakan isi lokasi
                                                                    bimbingan</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="button" class="btn btn-success"
                                                                id="confirmTerima">Ya, Terima</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="modalTolak" tabindex="-1"
                                                aria-labelledby="modalTolakLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalTolakLabel">Tolak Usulan
                                                                Bimbingan</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="usulan-detail mb-3">
                                                                <p><strong>NIM:</strong> <span
                                                                        class="nim-display"></span></p>
                                                                <p><strong>Nama:</strong> <span
                                                                        class="nama-display"></span></p>
                                                                <p><strong>Jenis Bimbingan:</strong> <span
                                                                        class="jenis-display"></span></p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="alasanPenolakan">Alasan Penolakan:</label>
                                                                <textarea class="form-control" id="alasanPenolakan" rows="3" required
                                                                    placeholder="Tuliskan alasan penolakan usulan bimbingan"></textarea>
                                                                <div class="invalid-feedback">Silakan isi alasan
                                                                    penolakan</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="button" class="btn btn-danger"
                                                                id="confirmTolak" data-row-id="">Ya, Tolak</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal Hapus -->
                                            <div class="modal fade" id="modalHapus" tabindex="-1"
                                                aria-labelledby="modalHapusLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalHapusLabel">Hapus Usulan
                                                                Bimbingan</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="usulan-detail mb-3">
                                                                <p><strong>NIM:</strong> <span
                                                                        class="nim-display"></span></p>
                                                                <p><strong>Nama:</strong> <span
                                                                        class="nama-display"></span></p>
                                                                <p><strong>Jenis Bimbingan:</strong> <span
                                                                        class="jenis-display"></span></p>
                                                            </div>
                                                            <p>Apakah Anda yakin ingin menghapus usulan bimbingan ini?
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="button" class="btn btn-danger"
                                                                id="confirmHapus">Ya, Hapus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--Daftar Jadwal-->
                    <div class="tab-pane fade" id="jadwal" role="tabpanel" aria-labelledby="jadwal-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                Tampilkan
                                <select class="form-select form-select-sm d-inline-block w-auto">
                                    <option>50</option>
                                </select>
                                entri
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Kode Dosen</th>
                                        <th class="text-center">Nama Dosen</th>
                                        <th class="text-center">Total Bimbingan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--isi contoh-->
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">ED</td>
                                        <td class="text-center">Edi Susilo, S.Pd., M.Kom., M.Eng.</td>
                                        <td class="text-center">2</td>
                                        <td class="text-center">
                                            <a href="/detaildaftar" class="badge btn btn-info p-1 mb-1">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!--Riwayat-->
                    <div class="tab-pane fade" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                Tampilkan
                                <select class="form-select form-select-sm d-inline-block w-auto">
                                    <option>50</option>
                                </select>
                                entri
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-center">No.</th>
                                        <th class="text-center">NIM</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Jenis Bimbingan</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Waktu</th>
                                        <th class="text-center">Lokasi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--isi contoh-->
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">2107110255</td>
                                        <td class="text-center">Syahirah Tri Meilina</td>
                                        <td class="text-center">Bimbingan Skripsi</td>
                                        <td class="text-center">Senin, 30 September 2024</td>
                                        <td class="text-center">13.30 - 16.00</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">SELESAI</td>
                                        <td class="text-center">
                                            <a href="/riwayatdosen" class="badge btn btn-info p-1 mb-1">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-3">
                        <p class="mb-2">Menampilkan 1 sampai 1 dari 1 entri</p>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item disabled"><a class="page-link" href="#">Selanjutnya</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container text-center">
            <p class="mb-0">
                Dikembangkan oleh Mahasiswa Prodi Teknik Informatika UNRI
                (<span class="green-text">Desi, Murni, dan Syahirah</span>)
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentRow = null;

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            function getRowData(row) {
                return {
                    nim: row.querySelector('td:nth-child(2)').textContent,
                    nama: row.querySelector('td:nth-child(3)').textContent,
                    jenisBimbingan: row.querySelector('td:nth-child(4)').textContent
                };
            }

            function updateModal(modal, rowData) {
                modal.querySelector('.nim-display').textContent = rowData.nim;
                modal.querySelector('.nama-display').textContent = rowData.nama;
                modal.querySelector('.jenis-display').textContent = rowData.jenisBimbingan;
            }

            // Improved modal cleanup function
            function cleanupModal() {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.dispose();
                    }
                });

                // Remove all modal backdrops
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());

                // Clean up body
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('.approve-icon, .reject-icon').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentRow = this.closest('tr');
                    const rowData = getRowData(currentRow);
                    const modalId = this.classList.contains('approve-icon') ? 'modalTerima' :
                        'modalTolak';
                    const modal = document.getElementById(modalId);
                    updateModal(modal, rowData);

                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                });
            });

            function updateRowStatus(row, status) {
                const statusCell = row.querySelector('td:nth-last-child(2)');
                const actionIcons = row.querySelector('.action-icons');

                statusCell.textContent = status;

                if (status === 'DISETUJUI' || status === 'DITOLAK') {
                    const approveIcon = actionIcons.querySelector('.approve-icon');
                    const rejectIcon = actionIcons.querySelector('.reject-icon');
                    if (approveIcon) approveIcon.remove();
                    if (rejectIcon) rejectIcon.remove();

                    if (status === 'DISETUJUI' && !actionIcons.querySelector('.edit-icon')) {
                        const editIcon = document.createElement('a');
                        editIcon.href = '/editusulan';
                        editIcon.className = 'action-icon edit-icon';
                        editIcon.setAttribute('data-bs-toggle', 'tooltip');
                        editIcon.setAttribute('title', 'Edit Usulan');
                        editIcon.innerHTML = '<i class="fas fa-pencil-alt"></i>';
                        actionIcons.appendChild(editIcon);

                        new bootstrap.Tooltip(editIcon);
                    }
                }
            }

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className =
                    `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
                notification.style.zIndex = '1050';
                notification.innerHTML =
                    `${message} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Improved modal confirmation handlers
            document.getElementById('confirmTerima').addEventListener('click', function() {
                const lokasiInput = document.getElementById('lokasiBimbingan');

                if (!lokasiInput.value.trim()) {
                    lokasiInput.classList.add('is-invalid');
                    return;
                }

                if (currentRow) {
                    // Update status
                    updateRowStatus(currentRow, 'DISETUJUI');

                    // Update lokasi in table
                    const lokasiCell = currentRow.querySelector(
                        'td:nth-child(7)'); // Adjust index if needed
                    lokasiCell.textContent = lokasiInput.value.trim();

                    // Close modal
                    const modal = document.getElementById('modalTerima');
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                        setTimeout(cleanupModal, 300);
                    }

                    // Optional: Show success notification
                    showNotification('Usulan bimbingan telah disetujui', 'success');
                }

                // Reset form
                lokasiInput.value = '';
                lokasiInput.classList.remove('is-invalid');
            });

            // Add modal hidden event handler to reset location input
            document.getElementById('modalTerima').addEventListener('hidden.bs.modal', function() {
                const lokasiInput = document.getElementById('lokasiBimbingan');
                lokasiInput.value = '';
                lokasiInput.classList.remove('is-invalid');
            });
            document.getElementById('confirmTolak').addEventListener('click', function() {
                const alasanInput = document.getElementById('alasanPenolakan');

                if (!alasanInput.value.trim()) {
                    alasanInput.classList.add('is-invalid');
                    return;
                }

                if (currentRow) {
                    updateRowStatus(currentRow, 'DITOLAK');
                    const modal = document.getElementById('modalTolak');
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                        setTimeout(cleanupModal, 300); // Wait for modal animation
                    }
                    // showNotification('Usulan bimbingan telah ditolak', 'danger');
                }

                alasanInput.value = '';
                alasanInput.classList.remove('is-invalid');
            });

            // Improved modal hidden event handlers
            ['modalTerima', 'modalTolak'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                modal.addEventListener('hidden.bs.modal', function() {
                    setTimeout(cleanupModal, 300);
                    if (modalId === 'modalTolak') {
                        const alasanInput = document.getElementById('alasanPenolakan');
                        alasanInput.value = '';
                        alasanInput.classList.remove('is-invalid');
                    }
                });
            });
            // Tambahkan di dalam event listener 'DOMContentLoaded'
document.querySelectorAll('.delete-icon').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        currentRow = this.closest('tr');
        const rowData = getRowData(currentRow);
        const modal = document.getElementById('modalHapus');
        updateModal(modal, rowData);

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    });
});

// Tambahkan handler untuk konfirmasi hapus
document.getElementById('confirmHapus').addEventListener('click', function() {
    if (currentRow) {
        // Animasi fade out sebelum menghapus
        currentRow.style.transition = 'opacity 0.3s ease';
        currentRow.style.opacity = '0';
        
        setTimeout(() => {
            currentRow.remove();
            
            // Update nomor urut pada tabel
            const tbody = document.querySelector('#tabelUsulan');
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });
            
            const modal = document.getElementById('modalHapus');
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
                setTimeout(cleanupModal, 300);
            }
            
            showNotification('Usulan bimbingan berhasil dihapus', 'success');
        }, 300);
    }
});

// Tambahkan handler untuk modal hapus hidden
document.getElementById('modalHapus').addEventListener('hidden.bs.modal', function() {
    setTimeout(cleanupModal, 300);
});
        });
    </script>
</body>

</html>
