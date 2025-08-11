@extends('layouts.app')

@section('title', 'Edit Usulan')

@push('styles')
<style>
    .status-badge {
        display: inline-block;
        background-color: #17a2b8;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9em;
        margin-top: 10px;
        font-weight: 600;
    }

    .btn-ubah-data,
    .btn-selesai {
        width: 100%;
        margin-bottom: 10px;
    }

    .btn-ubah-data {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #ffffff;
        font-weight: 600;
    }

    .btn-ubah-data:hover {
        background-color: #e0a800;
        border-color: #e0a800;
    }

    .btn-simpan-perubahan {
        width: 100%;
        margin-bottom: 10px;
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .btn-simpan-perubahan:hover {
        background-color: #218838;
        border-color: #218838;
    }

    .btn-selesai {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
        font-weight: 600;
    }

    .btn-selesai:hover {
        background-color: #138496;
        border-color: #138496;
    }

    .edit-icon {
        cursor: pointer;
        margin-left: 10px;
        display: none;
    }

    .edit-input {
        display: none;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Detail Mahasiswa</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ url('/persetujuan') }}">
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
                <p class="card-text text-start">Syahirah Tri Meilina</p>
                <p class="card-title text-muted text-sm">NIM</p>
                <p class="card-text text-start">2107110255</p>
                <p class="card-title text-muted text-sm">Program Studi</p>
                <p class="card-text text-start">Teknik Informatika S1</p>
                <p class="card-title text-muted text-sm">Konsentrasi</p>
                <p class="card-text text-start">Rekayasa Perangkat Lunak</p>
            </div>

            <!-- Dosen Pembimbing Card -->
            <div class="col-lg-6 col-md-12 bg-white rounded-end px-4 py-3 mb-2 shadow-sm">
                <h5 class="text-bold">Dosen Pembimbing</h5>
                <hr>
                <p class="card-title text-secondary text-sm">Nama Pembimbing</p>
                <p class="card-text text-start">Edi Susilo, S.Pd., M.Kom., M.Eng.</p>
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
                <p class="card-text text-start">Bimbingan Skripsi</p>
                <p class="card-title text-muted text-sm">Tanggal</p>
                <p class="card-text text-start">
                    <span id="tanggal-text">Senin, 30 September 2024</span>
                    <i class="fas fa-edit edit-icon" onclick="toggleEdit('tanggal')"></i>
                    <input type="date" id="tanggal-input" class="edit-input form-control" value="2024-09-30">
                </p>
                <p class="card-title text-muted text-sm">Waktu</p>
                <p class="card-text text-start">
                    <span id="waktu-text">13.30 - 16.00</span>
                    <i class="fas fa-edit edit-icon" onclick="toggleEdit('waktu')"></i>
                    <select id="waktu-input-start" class="edit-input form-control mb-2">
                    </select>
                    <select id="waktu-input-end" class="edit-input form-control">
                    </select>
                </p>
                <p class="card-title text-muted text-sm">Deskripsi</p>
                <p class="card-text text-start">Izin bapak, saya ingin melakukan bimbingan bab 1 skripsi saya pak</p>
            </div>

            <!-- Keterangan Usulan Card -->
            <div class="col-lg-6 col-md-12 px-4 py-3 mb-2 bg-white rounded-end shadow-sm">
                <h5 class="text-bold">Keterangan Usulan</h5>
                <hr>
                <p class="card-title text-secondary text-sm">Status Usulan</p>
                <p class="card-text text-start">
                    <span id="status-badge" class="status-badge">USULAN DISETUJUI</span>
                </p>
                <p class="card-title text-secondary text-sm">Keterangan</p>
                <p id="keterangan-text" class="card-text text-start">Usulan Jadwal Bimbingan Disetujui</p>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 mb-2">
                <button id="btn-ubah-data" class="btn btn-ubah-data">Ubah Data</button>
            </div>
            <div class="col-md-12 mb-2">
                <button id="btn-selesai" class="btn btn-selesai">Selesai</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startTimeSelect = document.getElementById('waktu-input-start');
    const endTimeSelect = document.getElementById('waktu-input-end');
    populateTimeOptions(startTimeSelect);
    populateTimeOptions(endTimeSelect);

    // Set initial values based on the current text
    const waktuText = document.getElementById('waktu-text').textContent;
    const [startTime, endTime] = waktuText.split(' - ');
    startTimeSelect.value = startTime.trim();
    endTimeSelect.value = endTime.trim();

    document.getElementById('btn-ubah-data').addEventListener('click', function () {
        const editIcons = document.querySelectorAll('.edit-icon');
        if (this.textContent === 'Ubah Data') {
            editIcons.forEach(icon => icon.style.display = 'inline');
            this.textContent = 'Simpan Perubahan Data';
            this.classList.remove('btn-ubah-data');
            this.classList.add('btn-simpan-perubahan');
        } else {
            editIcons.forEach(icon => icon.style.display = 'none');
            this.textContent = 'Ubah Data';
            this.classList.remove('btn-simpan-perubahan');
            this.classList.add('btn-ubah-data');

            // Save changes
            const tanggalInput = document.getElementById('tanggal-input');
            const tanggalText = document.getElementById('tanggal-text');
            const waktuStartInput = document.getElementById('waktu-input-start');
            const waktuEndInput = document.getElementById('waktu-input-end');
            const waktuText = document.getElementById('waktu-text');

            if (tanggalInput.style.display !== 'none') {
                const date = new Date(tanggalInput.value);
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                tanggalText.textContent = date.toLocaleDateString('id-ID', options);
                tanggalInput.style.display = 'none';
                tanggalText.style.display = 'inline';
            }

            if (waktuStartInput.style.display !== 'none') {
                // Validate time
                const startTime = waktuStartInput.value;
                const endTime = waktuEndInput.value;
                if (startTime >= endTime) {
                    alert('Apakah anda yakin ingin merubah data?');
                } else {
                    waktuText.textContent = `${startTime} - ${endTime}`;
                    waktuStartInput.style.display = 'none';
                    waktuEndInput.style.display = 'none';
                    waktuText.style.display = 'inline';
                }
            }
        }
    });

    document.getElementById('btn-selesai').addEventListener('click', function () {
        const statusBadge = document.getElementById('status-badge');
        const keteranganText = document.getElementById('keterangan-text');

        statusBadge.textContent = 'BIMBINGAN SELESAI';
        statusBadge.style.backgroundColor = '#28a745';
        keteranganText.textContent = 'Bimbingan telah selesai dilaksanakan';

        this.disabled = true;
        document.getElementById('btn-ubah-data').disabled = true;
    });
});

function populateTimeOptions(selectElement, startHour = 7, endHour = 17) {
    selectElement.innerHTML = '';
    for (let hour = startHour; hour <= endHour; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            const option = document.createElement('option');
            option.value = time;
            option.textContent = time;
            selectElement.appendChild(option);
        }
    }
}

function toggleEdit(field) {
    const textElement = document.getElementById(`${field}-text`);
    const inputElement = document.getElementById(`${field}-input`);
    const inputElementEnd = document.getElementById(`${field}-input-end`);

    if (textElement.style.display !== 'none') {
        textElement.style.display = 'none';
        inputElement.style.display = 'block';
        if (inputElementEnd) {
            inputElementEnd.style.display = 'block';
        }
    } else {
        textElement.style.display = 'inline';
        inputElement.style.display = 'none';
        if (inputElementEnd) {
            inputElementEnd.style.display = 'none';
        }

        if (field === 'tanggal') {
            const date = new Date(inputElement.value);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            textElement.textContent = date.toLocaleDateString('id-ID', options);
        } else if (field === 'waktu') {
            textElement.textContent = `${inputElement.value} - ${inputElementEnd.value}`;
        }
    }
}
</script>
@endpush