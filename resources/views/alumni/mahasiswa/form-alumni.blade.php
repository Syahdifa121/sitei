@php
    $alumni = $user->alumni ?? null;
    $activeStep = request()->get('step', '1');
@endphp

<div class="alumni-form-container">

    <!-- Stepper Header -->
    <div class="d-flex justify-content-between mb-4 mx-md-5 mx-lg-6 mx-xl-7 px-4">
        <a href="{{ request()->url() }}?tab=form-alumni&step=1" 
            class="btn btn-step rounded-circle d-flex justify-content-center align-items-center 
            {{ $activeStep == '1' ? 'active' : ($isStep1Complete ? 'completed' : '') }}"
            style="width: 50px; height: 50px; padding: 0;"
            data-bs-toggle="tooltip" title="Biodata Alumni">
            <i class="bi bi-person-badge" style="font-size: 15px;"></i>
        </a>

        <div class="stepper-line flex-grow-1 mx-4 align-self-center 
            {{ $isStep1Complete ? 'line-completed' : '' }}"></div>

        @if ($isStep1Complete)
            <a href="{{ request()->url() }}?tab=form-alumni&step=2" 
                class="btn btn-step rounded-circle d-flex justify-content-center align-items-center 
                {{ $activeStep == '2' ? 'active' : ($isStep2Complete ? 'completed' : '') }}"
                style="width: 50px; height: 50px; padding: 0;"
                data-bs-toggle="tooltip" title="Kuisioner Wajib">
                <i class="bi bi-clipboard-check" style="font-size: 15px;"></i>
            </a>
        @else
            <span class="btn btn-step rounded-circle d-flex justify-content-center align-items-center"
                style="width: 50px; height: 50px; padding: 0; cursor: not-allowed;"
                data-bs-toggle="tooltip" title="Selesaikan Biodata terlebih dahulu">
                <i class="bi bi-clipboard-check" style="font-size: 15px;"></i>
            </span>  
        @endif

        <div class="stepper-line flex-grow-1 mx-4 align-self-center
            {{ $isStep2Complete ? 'line-completed' : '' }}"></div>

        @if ($isStep2Complete)
            <a href="{{ request()->url() }}?tab=form-alumni&step=3" 
                class="btn btn-step rounded-circle d-flex justify-content-center align-items-center 
                {{ $activeStep == '3' ? 'active' : ($isStep3Complete ? 'completed' : '') }}"
                style="width: 50px; height: 50px; padding: 0;"
                data-bs-toggle="tooltip" title="Kuisioner Lainnya">
                <i class="bi bi-pencil-square" style="font-size: 15px;"></i>
            </a>    
        @else
            <span class="btn btn-step rounded-circle d-flex justify-content-center align-items-center"
                style="width: 50px; height: 50px; padding: 0; cursor: not-allowed;"
                data-bs-toggle="tooltip" title="Selesaikan Kuisioner Wajib terlebih dahulu">
                <i class="bi bi-pencil-square" style="font-size: 15px;"></i>
            </span>     
        @endif
    </div>

    <div id="alumniForm">
        <div class="tab-content">
            {{-- Step 1 --}}
            @if ($activeStep == '1')
                <form action="{{ route('mahasiswa.alumni-step1') }}" method="POST" class="tab-pane fade show active">
                    @csrf
                    @include('alumni.mahasiswa.form-alumni.biodata-form')
                    
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn-gradient-success px-4 py-2">
                            Selanjutnya
                            <i class="bi bi-arrow-right me-2"></i>
                        </button>
                    </div>
                </form>
            @elseif ($activeStep == '2')
                @if ($isStep1Complete)
                    <form action="{{ route('mahasiswa.alumni-step2') }}" method="POST" class="tab-pane fade show active">
                        @csrf
                        @include('alumni.mahasiswa.form-alumni.kuisioner-wajib')
        
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ request()->url() }}?tab=form-alumni&step=1" class="btn-gradient-second px-4 py-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Sebelumnya
                            </a>
                            <button type="submit" class="btn-gradient-success px-4 py-2">
                                Selanjutnya
                                <i class="bi bi-arrow-right me-2"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-danger">
                        Silakan lengkapi data biodata di step 1 terlebih dahulu
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = "{{ request()->url() }}?tab=form-alumni&step=1";
                        }, 2000);
                    </script>
                @endif
            @elseif ($activeStep == '3')
                @if ($isStep2Complete)
                    <form id="alumniStep3Form" action="{{ route('mahasiswa.alumni-step3') }}" method="POST" class="tab-pane fade show active">
                        @csrf
                        @include('alumni.mahasiswa.form-alumni.kuisioner-lainnya')
                    
                        <div class="mt-4 d-flex justify-content-start">
                            <a href="{{ request()->url() }}?tab=form-alumni&step=2" class="btn-gradient-second px-4 py-2">
                                <i class="bi bi-arrow-left me-2"></i>       
                                Sebelumnya
                            </a>
                            <button type="button" id="submitBtn" class="btn-gradient-success px-4 py-2 ms-auto">
                                Submit
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-danger">
                        Silakan lengkapi data biodata di step 2 terlebih dahulu
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = "{{ request()->url() }}?tab=form-alumni&step=1";
                        }, 2000);
                    </script>
                @endif
            @endif
        </div>
    </div>
    
</div>

<style>
    /* Main colors */
    :root {
        --primary-color: #1e40af;
        --primary-light: #dbeafe;
        --secondary-color: #6c757d;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --light-color: #f8fafc;
        --dark-color: #334155;
    }

    /* Stepper styles */
    .btn-step {
        background-color: #e9ecef;
        color: #6c757d;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .btn-step.active {
        background-color: #0a2550; /* Dark navy blue for active */
        color: white;
        border-color: #0a2550;
    }

    .btn-step.completed {
        background-color: #1e3a6d; /* Slightly lighter navy blue for completed */
        color: white;
        border-color: #1e3a6d;
    }

    .stepper-line {
        height: 2px;
        background-color: #e2e8f0;
        transition: all 0.3s ease;
    }

    .stepper-line.line-completed {
        background-color: #1e3a6d;
        height: 3px;
    }

    .btn-gradient-success, .btn-gradient-second {
        background: linear-gradient(to right, #3875B6, #37C3F4);
        border: none;
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        z-index: 1;
        cursor: pointer;
        border-radius: 12px;
        padding: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-decoration: none;
    }

    .btn-gradient-second {
        background: linear-gradient(to right, #00b7ae, #37C3F4);
    }

    .btn-gradient-success:hover, .btn-gradient-second:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: "Apakah kamu yakin ingin submit?",
                text: "Data kuisioner akan disimpan dan tidak dapat diubah lagi.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, submit!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form secara normal (bukan AJAX)
                    document.getElementById('alumniStep3Form').submit();
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });

    function handleStatusChange(selected) {
        const kerjaSection = document.getElementById('kerja-details');
        const wiraswastaSection = document.getElementById('wiraswasta-details');
        const posisiSelect = document.getElementById('posisi_wirausaha');

        const showKerja = (selected === 1 || selected === 2);
        const showWiraswasta = (selected === 2);

        kerjaSection.style.display = showKerja ? 'block' : 'none';
        wiraswastaSection.style.display = showWiraswasta ? 'block' : 'none';

        if (!showKerja) {
            const inputs = kerjaSection.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }

                input.disabled = false;

                if (['bulan_mendapat_pekerjaan_ya', 'pendapatan_per_bulan', 'bulan_mendapat_pekerjaan_tidak'].includes(input.id)) {
                    input.disabled = true;
                }
            });
        }

        if (selected === 1 && posisiSelect) {
            posisiSelect.value = ''; 
        }
    }

    // Inisialisasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        const selectedStatus = document.querySelector('input[name="status_saat_ini"]:checked');
        if (selectedStatus) handleStatusChange(parseInt(selectedStatus.value));
    });

    function toggleKerjaCepat() {
        const isYa = document.getElementById('bekerja_6_bulan_setelah_lulus_1').checked;
        
        document.getElementById('bulan_mendapat_pekerjaan_ya').disabled = !isYa;
        document.getElementById('pendapatan_per_bulan').disabled = !isYa;
        
        document.getElementById('bulan_mendapat_pekerjaan_tidak').disabled = isYa;
        
        if (isYa) {
            document.getElementById('bulan_mendapat_pekerjaan_tidak').value = '';
        } else {
            document.getElementById('bulan_mendapat_pekerjaan_ya').value = '';
            document.getElementById('pendapatan_per_bulan').value = '';
        }
    }

    function toggleMulaiCariKerja(shouldFocus = false) {
        const selectedValue = document.querySelector('input[name="waktu_mulai_mencari_kerja"]:checked')?.value;
        
        const inputSebelum = document.querySelector('input[name="bulan_sebelum_lulus"]');
        const inputSesudah = document.querySelector('input[name="bulan_sesudah_lulus"]');
        
        inputSebelum.disabled = true;
        inputSesudah.disabled = true;
        
        inputSebelum.classList.remove('is-invalid');
        inputSesudah.classList.remove('is-invalid');
        
        if (selectedValue == 1) {
            inputSebelum.disabled = false;
            if (shouldFocus) inputSebelum.focus(); 
        } else if (selectedValue == 2) {
            inputSesudah.disabled = false;
            if (shouldFocus) inputSesudah.focus();
        }
    }

    // Panggil saat halaman dimuat tanpa focus
    document.addEventListener('DOMContentLoaded', function() {
        toggleMulaiCariKerja(false);
    });

    // Untuk event onchange tetap dengan focus
    function handleMulaiCariKerjaChange() {
        toggleMulaiCariKerja(true);
    }

    function toggleJenisLainnya(radio) {
        const lainnyaInput = document.getElementById('jenis_perusahaan_lainnya');
        if (radio.value == '7') {
            lainnyaInput.disabled = false;
        } else {
            lainnyaInput.disabled = true;
            lainnyaInput.value = '';
        }
    }

    function toggleSumberdanaLainnya(value) {
        const input = document.getElementById('sumber_pembiayaan_kuliah_lainnya');
        input.disabled = (value != 7);
        if (value != 7) input.value = '';
    }

    function toggleSituasiSaatIniLainnya(el) {
        const inputLainnya = document.getElementById('input_lainnya');
        if (el.value == '5') {
            inputLainnya.disabled = false;
        } else {
            inputLainnya.disabled = true;
            inputLainnya.value = ''; 
        }
    }

    function toggleAktifMencariLainnya(el) {
        const input = document.getElementById('input_aktif_mencari_pekerjaan_lainnya');
        if (el.value == '5') {
            input.disabled = false;
        } else {
            input.disabled = true;
            input.value = '';
        }
    }

    function toggleBeasiswaLainnya(el) {
        const inputLainnya = document.getElementById('beasiswa_lainnya');

        if (el.value == '22') {
            inputLainnya.disabled = false;
        } else {
            inputLainnya.disabled = true;
            inputLainnya.value = ''; 
        }
    }
</script>
