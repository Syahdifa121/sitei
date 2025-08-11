@extends('layouts.app')

@section('title', 'Buat Pesan')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    .btn-gradient:hover a {
        color: black;
    }
    .container {
        flex: 1; 
    }
    form .form-label {
        font-weight: bold;
    }
    
    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #212529;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb;
    }
    
    .select2-dropdown {
        border-color: #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .select2-container--default .select2-selection--single:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .dosen-result-item {
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dosen-info {
        display: flex;
        flex-direction: column;
    }
    
    .dosen-nama {
        font-weight: 500;
    }
    
    .dosen-nip {
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Error state untuk Select2 */
    .is-invalid + .select2-container .select2-selection {
        border-color: #dc3545;
    }
    
    .is-invalid + .select2-container.select2-container--focus .select2-selection {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

     /* Custom styling untuk icon di Select2 */
     .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 25px;
    }

    /* Menghilangkan clear button bawaan Select2 */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    /* Custom styling untuk icon di Select2 */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 25px;
        cursor: pointer;
    }

    /* Menghilangkan icon default dari Select2 */
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        display: none;
    }

    /* Custom icon container */
    .select2-selection__arrow::after {
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        color: #6c757d;
        position: absolute;
        top: 50%;
        right: 8px;
        transform: translateY(-50%);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    /* Default state - chevron down */
    .select2-selection__arrow:not(.has-value)::after {
        content: '\f0d7';
    }

    /* Selected state - times/clear icon */
    .select2-selection__arrow.has-value::after {
        content: '\f00d';
    }

    /* Hover state */
    .select2-selection__arrow:hover::after {
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Buat Pesan Baru</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ route('pesan.dashboardkonsultasi') }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </button>

    <form id="createMessageForm" method="POST" action="{{ route('pesan.store') }}">
    @csrf
        <div class="mb-3">
            <label for="subject" class="form-label">Subjek<span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                id="subject" name="subject" placeholder="Isi subjek" 
                value="{{ old('subject') }}" required>
            @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Bagian Pemilihan Penerima -->
        <div class="mb-3">
            <label for="recipient" class="form-label fw-bold">Penerima<span class="text-danger">*</span></label>
            
            @if(auth()->guard('dosen')->check())
                <!-- Tab untuk mode pengiriman khusus dosen -->
                <ul class="nav nav-tabs mb-3" id="recipientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">Mahasiswa Individual</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="batch-tab" data-bs-toggle="tab" data-bs-target="#batch" type="button" role="tab">Berdasarkan Angkatan</button>
                    </li>
                </ul>

                <!-- Konten Tab untuk dosen -->
                <div class="tab-content" id="recipientTabsContent">
                    <div class="tab-pane fade show active" id="individual" role="tabpanel">
                        <select class="form-control" id="individualStudents" name="penerima[]" multiple="multiple">
                            @foreach($mahasiswas as $mahasiswa)
                                <option value="{{ $mahasiswa->nim }}">
                                    {{ $mahasiswa->nama }} ({{ $mahasiswa->nim }}) - Angkatan {{ $mahasiswa->angkatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tab Berdasarkan Angkatan -->
                    <div class="tab-pane fade" id="batch" role="tabpanel">
                        <div class="batch-selection">
                            <h5 class="mb-3">Pilih Angkatan:</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2021" id="batch2021" name="angkatan[]">
                                        <label class="form-check-label" for="batch2021">
                                            Angkatan 2021
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2022" id="batch2022" name="angkatan[]">
                                        <label class="form-check-label" for="batch2022">
                                            Angkatan 2022
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2023" id="batch2023" name="angkatan[]">
                                        <label class="form-check-label" for="batch2023">
                                            Angkatan 2023
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input batch-checkbox" type="checkbox" value="2024" id="batch2024" name="angkatan[]">
                                        <label class="form-check-label" for="batch2024">
                                            Angkatan 2024
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="recipients-preview mt-3" id="batchPreview">
                            <p class="text-muted mb-0">Mahasiswa yang dipilih akan muncul di sini...</p>
                        </div>
                        <input type="hidden" name="selected_mahasiswa" id="selectedMahasiswaNim">
                    </div>
                </div>
            @else
                <!-- Pemilihan penerima untuk mahasiswa (select dosen) -->
                <select class="form-select @error('recipient') is-invalid @enderror" 
                        id="recipient" name="recipient" required>
                    <option value="">Pilih Dosen</option>
                    @foreach($dosen as $d)
                        <option value="{{ $d->nip }}" data-nama="{{ $d->nama }}"
                                {{ old('recipient') == $d->nip ? 'selected' : '' }}>
                            {{ $d->nama }}
                        </option>
                    @endforeach
                </select>
                @error('recipient')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endif
        </div>
        
        <div class="mb-3">
            <label for="priority" class="form-label">Prioritas<span class="text-danger">*</span></label>
            <select class="form-select @error('priority') is-invalid @enderror" 
                    id="priority" name="priority" required>
                <option value="" selected disabled>Pilih Prioritas</option>
                <option value="mendesak" {{ old('priority') == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                <option value="umum" {{ old('priority') == 'umum' ? 'selected' : '' }}>Umum</option>
            </select>
            @error('priority')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="message" class="form-label">Pesan<span class="text-danger">*</span></label>
            <textarea class="form-control @error('message') is-invalid @enderror" 
                    id="message" name="message" rows="5" 
                    placeholder="Isi pesan" required>{{ old('message') }}</textarea>
            @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="attachment" class="form-label">Lampiran (Opsional)</label>
            <input type="text" class="form-control @error('attachment') is-invalid @enderror" 
                id="attachment" name="attachment" 
                placeholder="Isi lampiran berupa link Google Drive"
                value="{{ old('attachment') }}">
            @error('attachment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="text-end">
            <button type="submit" class="btn btn-gradient">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
        </div>
    </form>           
</div>
@endsection
        
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#recipient').select2({
        placeholder: 'Ketik nama dosen...',
        allowClear: false, // Disable clear button bawaan
        width: '100%',
        language: {
            noResults: function() {
                return "Dosen tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        },
        templateResult: formatDosen,
        templateSelection: formatDosenSelection
    }).on('change', function() {
        const $arrow = $(this).next('.select2-container').find('.select2-selection__arrow');
        const hasValue = $(this).val() !== '' && $(this).val() !== null;
        
        if (hasValue) {
            $arrow.addClass('has-value');
        } else {
            $arrow.removeClass('has-value');
        }
    });

    $('#individualStudents').select2({
        placeholder: 'Pilih mahasiswa...',
        allowClear: true,
        width: '100%'
    });

    // Handle custom clear button
    $(document).on('click', '.select2-selection__arrow.has-value', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#recipient').val(null).trigger('change');
    });

    function formatDosen(dosen) {
        if (dosen.loading) return dosen.text;
        if (!dosen.id) return dosen.text;
        
        const $option = $(dosen.element);
        const nama = $option.data('nama');
        
        return $(`
            <div class="dosen-result-item">
                <span class="dosen-nama">${nama}</span>
            </div>
        `);
    }

    function formatDosenSelection(dosen) {
        if (!dosen.id) return dosen.text;
        return $(dosen.element).data('nama');
    }

    // Set initial state
    if ($('#recipient').val()) {
        $('#recipient').next('.select2-container')
            .find('.select2-selection__arrow')
            .addClass('has-value');
    }

    $('#createMessageForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    // Data umum yang selalu dikirim
    formData.append('subject', $('#subject').val());
    formData.append('priority', $('#priority').val());
    formData.append('message', $('#message').val());
    formData.append('attachment', $('#attachment').val());

    // Jika dosen (cek apakah elemen batch tab ada)
    if ($('#batch-tab').length) {
        const activeTab = $('.tab-pane.active').attr('id');
        
        if (activeTab === 'individual') {
            const selectedStudents = $('#individualStudents').val();
            if (!selectedStudents || selectedStudents.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Pilih minimal satu mahasiswa'
                });
                return false;
            }
            formData.append('selected_mahasiswa', selectedStudents.join(','));
        } else {
            const selectedNims = $('#selectedMahasiswaNim').val();
            if (!selectedNims) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Pilih minimal satu angkatan mahasiswa'
                });
                return false;
            }
            formData.append('selected_mahasiswa', selectedNims);
        }
    } else {
        // Untuk mahasiswa
        const selectedDosen = $('#recipient').val();
        if (!selectedDosen) {
            $('#recipient').next('.select2-container').addClass('is-invalid');
            return false;
        }
        formData.append('recipient', selectedDosen);
    }

    // Tampilkan loading
    Swal.fire({
        title: 'Mengirim pesan...',
        didOpen: () => {
            Swal.showLoading();
        },
        allowOutsideClick: false
    });

    // Kirim request
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message || 'Pesan berhasil dikirim',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/pesan/dashboardkonsultasi';
            });
        },
        error: function(xhr) {
            console.error('Error response:', xhr.responseText);
            
            let errorMessage = 'Terjadi kesalahan saat mengirim pesan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessage
            });

            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    const input = $(`#${key}`);
                    input.addClass('is-invalid');
                    if (key === 'recipient') {
                        input.next('.select2-container').addClass('is-invalid');
                    }
                    input.siblings('.invalid-feedback').remove();
                    input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                });
            }
        }
    });
});
});

document.querySelectorAll('.batch-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const selectedBatches = Array.from(document.querySelectorAll('.batch-checkbox:checked'))
            .map(input => input.value);

        if (selectedBatches.length === 0) {
            document.getElementById('batchPreview').innerHTML = 
                '<p class="text-muted mb-0">Mahasiswa yang dipilih akan muncul di sini...</p>';
            document.getElementById('selectedMahasiswaNim').value = '';
            return;
        }

        // Show loading state
        document.getElementById('batchPreview').innerHTML = 
            '<p class="text-muted mb-0">Mengambil data mahasiswa...</p>';

        fetch(`/pesan/getMahasiswaByAngkatan?angkatan=${encodeURIComponent(selectedBatches.join(','))}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                const mahasiswaList = data.data.map(mahasiswa => `
                    <div class="mb-2">
                        <i class="fas fa-user me-2"></i>
                        ${mahasiswa.name} (NIM: ${mahasiswa.nim})
                    </div>
                `).join('');

                document.getElementById('batchPreview').innerHTML = `
                    <div class="mt-3 bg-light p-3 rounded">
                        <h6 class="mb-3">Mahasiswa Terpilih (${data.data.length} orang):</h6>
                        ${mahasiswaList}
                    </div>`;

                // Update hidden input
                document.getElementById('selectedMahasiswaNim').value = 
                    data.data.map(mahasiswa => mahasiswa.nim).join(',');
            } else {
                document.getElementById('batchPreview').innerHTML = `
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Tidak ada data mahasiswa'}
                    </p>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('batchPreview').innerHTML = `
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Terjadi kesalahan saat mengambil data mahasiswa
                </p>`;
        });
    });
});

</script>
@endpush