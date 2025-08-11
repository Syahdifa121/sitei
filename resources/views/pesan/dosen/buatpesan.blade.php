
@extends('layouts.app')

@section('title', 'Buat Pesan Dosen')
@push('styles')
    <style>
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
        .select2-container {
            width: 100% !important;
        }
        .batch-selection {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .batch-selection h5 {
            margin-bottom: 1rem;
            color: #495057;
        }
        .recipients-preview {
            margin-top: 1rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            max-height: 150px;
            overflow-y: auto;
        }
        .select2-container--default .select2-selection--multiple {
            border-color: #ced4da !important;
        }
    </style>
    @endpush

    
    @section('content')
    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Buat Pesan Baru</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2">
            <a href="/dashboardpesan" class="d-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </button>

        <form id="messageForm">
            <div class="mb-3">
                <label for="subject" class="form-label fw-bold">Subjek<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="subject" placeholder="Isi subjek" required>
            </div>

            <!-- Bagian Pemilihan Penerima -->
            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Penerima<span class="text-danger">*</span></label>
                
                <!-- Tab untuk memilih mode pengiriman -->
                <ul class="nav nav-tabs mb-3" id="recipientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">Mahasiswa Individual</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="batch-tab" data-bs-toggle="tab" data-bs-target="#batch" type="button" role="tab">Berdasarkan Angkatan</button>
                    </li>
                </ul>

                <!-- Konten Tab -->
                <div class="tab-content" id="recipientTabsContent">
                    <!-- Tab Mahasiswa Individual -->
                    <div class="tab-pane fade show active" id="individual" role="tabpanel">
                        <select class="form-control" id="individualStudents" multiple="multiple">
                            <!-- Options akan diisi melalui JavaScript -->
                        </select>
                    </div>

                    <!-- Tab Berdasarkan Angkatan -->
                    <div class="tab-pane fade" id="batch" role="tabpanel">
                        <div class="batch-selection">
                            <h5 class="mb-3">Pilih Angkatan:</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2021" id="batch2021">
                                        <label class="form-check-label" for="batch2021">
                                            Angkatan 2021
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2022" id="batch2022">
                                        <label class="form-check-label" for="batch2022">
                                            Angkatan 2022
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2023" id="batch2023">
                                        <label class="form-check-label" for="batch2023">
                                            Angkatan 2023
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2024" id="batch2024">
                                        <label class="form-check-label" for="batch2024">
                                            Angkatan 2024
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="recipients-preview" id="batchPreview">
                            <p class="text-muted mb-0">Mahasiswa yang dipilih akan muncul di sini...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label fw-bold">Prioritas<span class="text-danger">*</span></label>
                <select class="form-select" id="priority" required>
                    <option value="" selected disabled>Pilih Prioritas</option>
                    <option value="high">Mendesak</option>
                    <option value="medium">Umum</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="attachment" class="form-label fw-bold">Lampiran (Opsional)</label>
                <input type="text" class="form-control" id="attachment" placeholder="Isi lampiran berupa link Google Drive">
            </div>

            <div class="mb-3">
                <label for="message" class="form-label fw-bold">Pesan<span class="text-danger">*</span></label>
                <textarea class="form-control" id="message" rows="5" placeholder="Isi pesan" required></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-gradient">Kirim</button>
            </div>
        </form>
    </div>
    @endsection


   <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @push('scripts') 
    <script>
        $(document).ready(function() {
            // Data mahasiswa (contoh)
            const studentsData = {
                '2021': [
                    { id: '1', name: ' Syahirah Tri Meilina - 2021', nim: ' 2107110255' },
                    { id: '2', name: 'Cut Muthia Ramadhani  - 2021', nim: '2107110257' },
                    { id: '3', name: 'Syazliana Nuro - 2021', nim: '2107110256' },
                    { id: '4', name: 'Desi Maya Sari - 2021', nim: '2107110665' },
                    { id: '5', name: 'Tri Murniati - 2021', nim: '2107112735' },
                    { id: '6', name: 'Sherly Ratna Musva - 2021', nim: '2107110670' }
                ],
                '2022': [
                    { id: '7', name: 'Reza Ramadhani Putra - 2022', nim: '2207111389' },
                    { id: '8', name: 'Edi Putra Yuni - 2022', nim: '2207111395' },
                    { id: '9', name: 'Fatimah Azzahra - 2022', nim: '2207125072' },
                    { id: '10', name: 'Dinda Wulandari  - 2022', nim: ' 2207125080' }
                ],
                '2023': [
                    { id: '11', name: 'Indah Permata - 2023', nim: '23000111' },
                    { id: '12', name: 'Joko Widodo - 2023', nim: '23000112' },
                    { id: '13', name: 'Kartika Sari - 2023', nim: '23000113' },
                    { id: '14', name: 'Reza Puta - 2023', nim: '23000114' }
                ],
                '2024': [
                    { id: '15', name: 'Maya Angelina - 2024', nim: '24000111' },
                    { id: '16', name: 'Naufal Ahmad - 2024', nim: '24000112' },
                    { id: '17', name: 'Olivia Putri - 2024', nim: '24000113' },
                    { id: '18', name: 'Pandu Wijaya - 2024', nim: '24000114' }
                ]
            };

            // Inisialisasi Select2 untuk pemilihan mahasiswa individual
            $('#individualStudents').select2({
                placeholder: 'Ketik nama atau NIM mahasiswa',
                allowClear: true,
                data: Object.values(studentsData).flat().map(student => ({
                    id: student.id,
                    text: `${student.name} (${student.nim})`
                })),
                matcher: function(params, data) {
                    // Jika tidak ada pencarian, tampilkan semua opsi
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Jika ada pencarian, lakukan pencocokan
                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    // Ubah ke lowercase untuk pencarian yang tidak case sensitive
                    const searchTerm = params.term.toLowerCase();
                    const text = data.text.toLowerCase();

                    // Cek apakah teks mengandung kata pencarian
                    if (text.indexOf(searchTerm) > -1) {
                        return data;
                    }

                    return null;
                }
            });

            // Fungsi untuk memperbarui preview mahasiswa berdasarkan angkatan
            function updateBatchPreview() {
                const selectedBatches = [];
                $('.batch-checkbox:checked').each(function() {
                    selectedBatches.push($(this).val());
                });

                const previewElement = $('#batchPreview');
                if (selectedBatches.length === 0) {
                    previewElement.html('<p class="text-muted mb-0">Mahasiswa yang dipilih akan muncul di sini...</p>');
                    return;
                }

                let previewHtml = '<div class="selected-students">';
                let totalStudents = 0;

                selectedBatches.forEach(batch => {
                    const students = studentsData[batch];
                    totalStudents += students.length;
                    previewHtml += `<h6 class="mt-2">Angkatan ${batch}:</h6><ul class="list-unstyled ms-3">`;
                    students.forEach(student => {
                        previewHtml += `<li>${student.name} (${student.nim})</li>`;
                    });
                    previewHtml += '</ul>';
                });

                previewHtml += `<p class="mt-3 fw-bold">Total: ${totalStudents} mahasiswa</p></div>`;
                previewElement.html(previewHtml);
            }

            // Event listener untuk checkbox angkatan
            $('.batch-checkbox').change(function() {
                updateBatchPreview();
            });

            // Event listener untuk pengiriman form
            $('#messageForm').submit(function(e) {
                e.preventDefault();

                // Mengumpulkan data penerima
                let recipients = [];
                const activeTab = $('#recipientTabs .nav-link.active').attr('id');

                if (activeTab === 'individual-tab') {
                    recipients = $('#individualStudents').select2('data').map(item => ({
                        id: item.id,
                        name: item.text
                    }));
                } else {
                    $('.batch-checkbox:checked').each(function() {
                        const batch = $(this).val();
                        recipients = recipients.concat(studentsData[batch]);
                    });
                }

                // Validasi penerima
                if (recipients.length === 0) {
                    alert('Silakan pilih minimal satu penerima pesan');
                    return;
                }

                // Mengumpulkan data form
                const formData = {
                    subject: $('#subject').val(),
                    recipients: recipients,
                    priority: $('#priority').val(),
                    attachment: $('#attachment').val(),
                    message: $('#message').val()
                };

                // Log data form (untuk keperluan development)
                console.log('Form Data:', formData);

                // Di sini Anda bisa menambahkan kode untuk mengirim data ke server
                alert('Pesan berhasil dikirim ke ' + recipients.length + ' mahasiswa');
                
                // Reset form
                this.reset();
                $('#individualStudents').val(null).trigger('change');
                $('.batch-checkbox').prop('checked', false);
                updateBatchPreview();
            });

            // Mengatur warna teks untuk select priority
            const prioritySelect = document.getElementById('priority');
            prioritySelect.addEventListener('change', function() {
                this.style.color = this.value === "" ? "#6c757d" : "black";
            });

            // Mengatur warna awal
            if (prioritySelect.value === "") {
                prioritySelect.style.color = "#6c757d";
            }
        });
    </script>
    @endpush