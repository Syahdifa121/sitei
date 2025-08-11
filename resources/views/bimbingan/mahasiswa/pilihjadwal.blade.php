@extends('layouts.app')

@section('title', 'Pilih Jadwal Bimbingan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
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

        /* Info Box Styling */
        .info-box {
            background-color: #e8f0fe;
            border: 1px solid #1a73e8;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .info-box p {
            color: #1967d2;
            margin-bottom: 10px;
        }

        .info-box .btn-connect {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
        }

        .info-box .btn-connect:hover {
            background-color: #1557b0;
        }

    /* Modal Konfirmasi Modern */
        .swal2-popup.confirmation-modal {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            padding: 0;
            overflow: hidden;
        }

        .confirmation-modal .confirmation-modal-icon {
            background-color: #dce6f5;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            /* Mengurangi margin atas dan menyeimbangkan jarak */
            margin: 20px auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirmation-modal .confirmation-modal-icon i {
            font-size: 36px;
            color: #1a73e8;
        }

        .confirmation-modal .confirmation-modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #202124;
            margin-bottom: 12px;
            padding: 0 24px;
        }

        .confirmation-modal .confirmation-modal-body {
            padding: 0 24px;
        }

        .confirmation-modal .confirmation-modal-content {
            font-size: 16px;
            line-height: 1.5;
            color: #5f6368;
            /* Menambah margin bawah untuk memberi space sebelum tombol */
            margin-bottom: 20px;
        }

        .confirmation-modal .swal2-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 0;
            /* Meningkatkan padding secara signifikan di bagian bawah */
            padding: 0 24px 36px;
        }

        .confirmation-modal .btn-confirm {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: none;
        }

        .confirmation-modal .btn-confirm:hover {
            background-color: #1557b0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .confirmation-modal .btn-cancel {
            background-color: #cdd2d4;
            color: #46494d;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: none;
        }

        .confirmation-modal .btn-cancel:hover {
            background-color: #e8eaed;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .jadwal-tersedia {
        color: #16a34a;
    }
    
    .jadwal-penuh {
        color: #dc2626;
    }
    
    .jadwal-selesai {
        color: #6b7280;
    }
    
    select.form-select option:disabled {
        color: #dc2626 !important;
        font-style: italic;
    }
    </style>
@endpush

@section('content')
<div id="dosenData" data-jenis-bimbingan="{{ json_encode($jenisBimbinganPerDosen ?? []) }}"></div>
    <div class="container mt-5">
        <h1 class="mb-2 gradient-text fw-bold">Pilih Jadwal Bimbingan</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2">
            <a href="/usulanbimbingan" class="d-flex align-items-center justify-content-center">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </button>

        @if (!$isConnected)
            <div class="info-box">
                <p class="mb-2">Untuk menggunakan fitur ini, Anda perlu memberikan izin akses ke Google Calendar dengan
                    email: <strong>{{ Auth::guard('mahasiswa')->user()->email }}</strong></p>
                <a href="{{ route('mahasiswa.google.connect') }}" class="btn btn-connect">
                    <i class="fas fa-calendar-plus"></i>
                    Hubungkan dengan Google Calendar
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('pilihjadwal.store') }}" id="formBimbingan">
                @csrf
                <div class="mb-3">
                    <label for="pilihDosen" class="form-label">Pilih Dosen<span style="color: red;">*</span></label>
                    <select class="form-select" id="pilihDosen" name="nip" required>
                        <option value="" selected disabled>- Pilih Dosen -</option>
                        @foreach ($dosenList as $dosen)
                            <option value="{{ $dosen['nip'] }}">{{ $dosen['nama'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jenisBimbingan" class="form-label">Pilih Jenis Bimbingan<span
                            style="color: red;">*</span></label>
                    <select class="form-select" id="jenisBimbingan" name="jenis_bimbingan" required>
                        <option value="" selected disabled>- Pilih Jenis Bimbingan -</option>
                        <option value="skripsi">Bimbingan Skripsi</option>
                        <option value="kp">Bimbingan KP</option>
                        <option value="akademik">Bimbingan Akademik</option>
                        <option value="konsultasi">Konsultasi Pribadi</option>
                        <option value="mbkm">Bimbingan MBKM</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pilihJadwal" class="form-label">Pilih Jadwal<span style="color: red;">*</span></label>
                    <select class="form-select" id="pilihJadwal" name="jadwal_id" required>
                        <option value="" selected disabled>- Pilih Dosen Terlebih Dahulu -</option>
                    </select>
                    {{-- <small class="text-muted">Menampilkan jadwal yang masih tersedia</small> --}}
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi<small class="text-muted"> (Opsional)</small></label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"
                        placeholder="Tuliskan deskripsi atau topik bimbingan Anda"></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-gradient">
                        Usulkan
                    </button>
                </div>
            </form>
    </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen yang diperlukan
        const formBimbingan = document.getElementById('formBimbingan');
        const dosenSelect = document.getElementById('pilihDosen');
        const jadwalSelect = document.getElementById('pilihJadwal');
        const jenisBimbinganSelect = document.getElementById('jenisBimbingan');
        
        // Ambil data jenis bimbingan per dosen dari elemen tersembunyi jika ada
        let jenisBimbinganPerDosen = {};
        const dosenDataElement = document.getElementById('dosenData');
        if (dosenDataElement && dosenDataElement.dataset.jenisBimbingan) {
            try {
                jenisBimbinganPerDosen = JSON.parse(dosenDataElement.dataset.jenisBimbingan);
                console.log('Data jenis bimbingan per dosen dari server:', jenisBimbinganPerDosen);
            } catch (e) {
                console.error('Error parsing jenis bimbingan data:', e);
            }
        }
    
        // Fungsi untuk mengubah format tanggal ke bahasa Indonesia
        const formatTanggalIndonesia = (tanggal) => {
            const namaBulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const namaHari = [
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
            ];
    
            const date = new Date(tanggal);
            const hari = namaHari[date.getDay()];
            const tanggalNum = date.getDate();
            const bulan = namaBulan[date.getMonth()];
            const tahun = date.getFullYear();
    
            return `${hari}, ${tanggalNum} ${bulan} ${tahun}`;
        };
    
        // Fungsi untuk menampilkan pesan dengan SweetAlert2
        const tampilkanPesan = (icon, title, text) => {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonColor: '#1a73e8'
            });
        };
    
        // Mapping nama jenis bimbingan
        const jenisOptions = {
            'skripsi': 'Bimbingan Skripsi',
            'kp': 'Bimbingan KP',
            'akademik': 'Bimbingan Akademik',
            'konsultasi': 'Konsultasi Pribadi',
            'mbkm': 'Bimbingan MBKM',
            'lainnya': 'Lainnya'
        };
    
        // Handler untuk perubahan dosen
        if (dosenSelect) {
            dosenSelect.addEventListener('change', function() {
                const selectedDosen = this.value;
                console.log('Dosen yang dipilih:', selectedDosen);
                
                // Reset jenis bimbingan dropdown
                jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>- Pilih Jenis Bimbingan -</option>';
                
                // Tampilkan loading state
                jenisBimbinganSelect.innerHTML += '<option value="" disabled>Memuat data...</option>';
                
                // Ambil jenis bimbingan dari server dengan endpoint yang lebih spesifik
                fetch(`/pilihjadwal/dosen/${selectedDosen}/jenis-bimbingan`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Reset dropdown setelah data diambil
                        jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>- Pilih Jenis Bimbingan -</option>';
                        
                        console.log('Respons API jenis bimbingan:', data);
                        
                        if (data.success) {
                            // Jika berhasil mendapatkan data jenis bimbingan
                            if (data.jenisBimbingan && data.jenisBimbingan.length > 0) {
                                console.log('Menampilkan jenis bimbingan tersedia:', data.jenisBimbingan);
                                
                                // Tampilkan jenis bimbingan yang tersedia dari server
                                data.jenisBimbingan.forEach(jenis => {
                                    const option = document.createElement('option');
                                    option.value = jenis;
                                    option.textContent = jenisOptions[jenis] || jenis;
                                    jenisBimbinganSelect.appendChild(option);
                                });
                                
                                // Jika hanya ada satu jenis bimbingan, otomatis pilih dan load jadwal
                                if (data.jenisBimbingan.length === 1) {
                                    console.log('Hanya satu jenis bimbingan tersedia, otomatis memilih:', data.jenisBimbingan[0]);
                                    jenisBimbinganSelect.value = data.jenisBimbingan[0];
                                    // Trigger event change untuk memuat jadwal
                                    getAvailableJadwal();
                                }
                            } else {
                                console.log('Tidak ada jenis bimbingan tersedia');
                                jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>Tidak ada jadwal tersedia</option>';
                                tampilkanPesan('info', 'Informasi', 'Dosen belum menyediakan jadwal bimbingan');
                            }
                        } else {
                            console.log('API mengembalikan status sukses=false');
                            jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>Terjadi kesalahan</option>';
                            tampilkanPesan('error', 'Terjadi Kesalahan', data.message || 'Tidak dapat memuat jenis bimbingan');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching jenis bimbingan:', error);
                        // Reset dropdown jika terjadi error
                        jenisBimbinganSelect.innerHTML = '<option value="" selected disabled>Terjadi kesalahan</option>';
                        tampilkanPesan('error', 'Terjadi Kesalahan', 'Tidak dapat memuat jenis bimbingan. Silakan coba lagi.');
                    });
                
                // Reset jadwal dropdown
                jadwalSelect.innerHTML = '<option value="" selected disabled>- Pilih Jenis Bimbingan Terlebih Dahulu -</option>';
            });
        }
    
        // Function untuk mengambil jadwal
        async function getAvailableJadwal() {
            const nip = dosenSelect.value;
            const jenisBimbingan = jenisBimbinganSelect.value;
            
            console.log('Mengambil jadwal untuk:', {nip, jenisBimbingan});
    
            if (!nip || !jenisBimbingan) {
                jadwalSelect.innerHTML =
                    '<option value="" selected disabled>Pilih dosen dan jenis bimbingan terlebih dahulu</option>';
                return;
            }
    
            try {
                // Tampilkan loading state
                jadwalSelect.innerHTML = '<option value="" selected disabled>Memuat jadwal...</option>';
    
                const response = await fetch(
                    `/pilihjadwal/available?nip=${nip}&jenis_bimbingan=${jenisBimbingan}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });
    
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
    
                const result = await response.json();
                
                // LOG DEBUG
                console.log('Respons API jadwal:', result);
                
                // Log detail struktur data untuk debugging
                if (result.data && result.data.length > 0) {
                    console.log('Detail struktur jadwal pertama:', JSON.stringify(result.data[0], null, 2));
                }
    
                // Reset select
                jadwalSelect.innerHTML = '<option value="" selected disabled>- Pilih Jadwal -</option>';
    
                if (result.status === 'success' && result.data && Array.isArray(result.data)) {
                    if (result.data.length === 0) {
                        jadwalSelect.innerHTML =
                            '<option value="" disabled>Belum ada jadwal tersedia</option>';
                        tampilkanPesan('info', 'Informasi', 'Belum ada jadwal tersedia untuk dosen dan jenis bimbingan ini');
                        return;
                    }
    
                    // Render jadwal options
                    result.data.forEach(jadwal => {
                        const option = document.createElement('option');
                        option.value = jadwal.id;
                        
                        // Gunakan teks yang sudah diformat dari backend
                        if (jadwal.text) {
                            const isPenuh = jadwal.has_kuota_limit && jadwal.jumlah_pendaftar >= jadwal.kapasitas;
                        // PERUBAHAN #2: Tambahkan indikator status
                        if (isPenuh) {
                            option.textContent = jadwal.text + ' [PENUH]';
                            option.disabled = true;
                            option.style.color = '#dc2626'; // Merah
                            option.style.fontStyle = 'italic';
                        } else {
                            option.textContent = jadwal.text;
                        }
                        } else {
                            // Ekstrak tanggal dari waktu_mulai (format: YYYY-MM-DD HH:MM:SS)
                            const tanggalStr = jadwal.waktu_mulai ? jadwal.waktu_mulai.split(' ')[0] : (jadwal.tanggal || null);
                            const tanggalIndonesia = tanggalStr ? formatTanggalIndonesia(tanggalStr) : 'Tanggal tidak tersedia';
                            
                            // Format waktu dari waktu_mulai dan waktu_selesai
                            const waktuMulai = jadwal.waktu_mulai 
                                ? jadwal.waktu_mulai.split(' ')[1].substring(0, 5) 
                                : (jadwal.waktu || 'N/A');
                            
                            const waktuSelesai = jadwal.waktu_selesai 
                                ? jadwal.waktu_selesai.split(' ')[1].substring(0, 5) 
                                : 'N/A';
                            
                            // Tambahkan informasi dosen jika tersedia
                            let additionalInfo = '';
                            if (jadwal.dosen_nama) {
                                additionalInfo = ` - ${jadwal.dosen_nama}`;
                            }
                            
                            // Tambahkan informasi kuota jika tersedia
                            if (jadwal.kapasitas > 0) {
            // PERUBAHAN #4: Gunakan jumlah_pendaftar alih-alih pengajuanCount
            const pengajuanCount = jadwal.jumlah_pendaftar || 0;
            
            // PERUBAHAN #5: Tampilkan status berdasarkan kuota
            if (pengajuanCount >= jadwal.kapasitas) {
                additionalInfo += ` | Kuota: PENUH`;
                option.disabled = true;
                option.style.color = '#dc2626'; // Merah
            } else if (pengajuanCount > (jadwal.kapasitas * 0.7)) {
                // Hampir penuh (>70%)
                additionalInfo += ` | Kuota: ${pengajuanCount}/${jadwal.kapasitas} (Hampir Penuh)`;
                option.style.color = '#d97706'; // Kuning/Orange
            } else {
                additionalInfo += ` | Kuota: ${pengajuanCount}/${jadwal.kapasitas}`;
            }
        } else {
            additionalInfo += " | Kuota Tidak Terbatas";
        }
        
        option.textContent = `${tanggalIndonesia} | ${waktuMulai}-${waktuSelesai}${additionalInfo}`;
    }

    jadwalSelect.appendChild(option);
});
                    
                    // Jika hanya ada satu jadwal, otomatis pilih
                    if (result.data.length === 1) {
                        console.log('Hanya ada satu jadwal tersedia, otomatis memilih:', result.data[0].id);
                        jadwalSelect.value = result.data[0].id;
                    }
                } else {
                    throw new Error('Invalid response format');
                }
            } catch (error) {
                console.error('Error loading schedule:', error);
                jadwalSelect.innerHTML = '<option value="" disabled>Tidak dapat memuat jadwal</option>';
                tampilkanPesan('error', 'Tidak dapat memuat jadwal', 'Silakan muat ulang halaman dan coba kembali');
            }
        }
    
        // Tambahkan event listeners
        if (jenisBimbinganSelect) {
            jenisBimbinganSelect.addEventListener('change', getAvailableJadwal);
        }
    
        // Handler form submit
        if (formBimbingan) {
            formBimbingan.addEventListener('submit', async function(e) {
                e.preventDefault();
    
                try {
                    // Validasi form
                    const formData = new FormData(formBimbingan);
                    const jadwalId = formData.get('jadwal_id');
                    const jenisBimbingan = formData.get('jenis_bimbingan');
    
                    if (!jadwalId || !jenisBimbingan) {
                        tampilkanPesan('warning', 'Form tidak lengkap', 'Silakan pilih jadwal dan jenis bimbingan');
                        return;
                    }
    
                    // Cek ketersediaan jadwal
                    try {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Memeriksa jadwal',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
    
                        const checkResponse = await fetch(`/pilihjadwal/check?jadwal_id=${jadwalId}&jenis_bimbingan=${jenisBimbingan}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json'
                            }
                        });
    
                        if (!checkResponse.ok) {
                            throw new Error('Network response was not ok');
                        }
    
                        const checkResult = await checkResponse.json();
                        console.log('Respons cek ketersediaan:', checkResult);
    
                        if (!checkResult.available) {
                            Swal.close();
                            tampilkanPesan('warning', 'Tidak Dapat Mengajukan', 
                                checkResult.message || 'Jadwal tidak tersedia');
                            return;
                        }
    
                        // Konfirmasi pengajuan
                        const confirmResult = await Swal.fire({
    html: `
        <div class="confirmation-modal-icon">
            <i class="fas fa-question-circle"></i>
        </div>
        <h2 class="confirmation-modal-title">Konfirmasi Pengajuan</h2>
        <div class="confirmation-modal-body">
            <p class="confirmation-modal-content">
                Anda yakin ingin mengajukan bimbingan untuk jadwal ini?
            </p>
        </div>
    `,
    showCancelButton: true,
    buttonsStyling: false,
    confirmButtonText: 'Ya, ajukan!',
    cancelButtonText: 'Batal',
    customClass: {
        popup: 'confirmation-modal',
        confirmButton: 'btn-confirm',
        cancelButton: 'btn-cancel'
    },
    focusConfirm: false
});
    
                        if (!confirmResult.isConfirmed) {
                            return;
                        }
    
                        // Tampilkan loading saat mengirim data
                        Swal.fire({
                            title: 'Memproses',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
    
                        // Kirim data usulan bimbingan
                        const response = await fetch(formBimbingan.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(Object.fromEntries(formData))
                        });
    
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
    
                        const result = await response.json();
    
                        if (result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pengajuan bimbingan berhasil dikirim',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '/usulanbimbingan';
                            });
                        } else {
                            throw new Error(result.message || 'Server error');
                        }
                    } catch (error) {
                        console.error('Error checking availability:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak dapat memeriksa jadwal',
                            text: 'Silakan coba beberapa saat lagi',
                            confirmButtonColor: '#1a73e8'
                        });
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak dapat memproses permintaan',
                        text: 'Silakan coba beberapa saat lagi',
                        confirmButtonColor: '#1a73e8'
                    });
                }
            });
        }
    });</script>
@endpush
