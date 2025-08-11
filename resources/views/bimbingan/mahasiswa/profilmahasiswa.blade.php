@extends('layouts.app')

@section('title', $role === 'mahasiswa' ? 'Profil Mahasiswa' : 'Profil Dosen')

@push('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #059669, #2563eb);
            --secondary-gradient: linear-gradient(to right, #4ade80, #3b82f6);
            --text-dark: #2c3e50;
            --text-light: #34495e;
        }

        .student-profile-container {
            max-width: 800px;
            margin: 50px auto;
            perspective: 1000px;
        }

        .student-profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.4s ease-in-out;
            transform-style: preserve-3d;
        }

        .student-profile-card:hover {
            transform: rotateX(0) rotateY(0) scale(1);
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.15);
        }

        .profile-header {
            background: var(--primary-gradient);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .student-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .student-avatar:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-top: 15px;
            letter-spacing: -0.5px;
        }

        .profile-nim {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 1px;
        }

        .profile-details {
            padding: 30px;
            background: linear-gradient(to right, #f8f9fa, #f1f3f5);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .detail-value {
            color: var(--text-light);
            text-align: right;
        }

        .profile-actions {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background: #f8f9fa;
        }

        .btn-modern {
            flex-grow: 1;
            margin: 0 10px;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-modern a {
            color: white;
            text-decoration: none;
        }

        .btn-update {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-password {
            background: var(--secondary-gradient);
            color: white;
        }

        .btn-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
<div class="container my-5">
    @php
        $label = [
            'mahasiswa' => 'Profil Mahasiswa',
            'dosen' => 'Profil Dosen',
            'admin' => 'Profil Admin',
        ][$role] ?? 'Profil Pengguna';
    @endphp

    <h1 class="mb-3 gradient-text fw-bold">{{ $label }}</h1>
    <hr>
    
    <div class="student-profile-container">
        <div class="student-profile-card">
            <div class="profile-header">
                <div class="position-relative d-inline-block">
                    <img src="{{ $profile->foto_url }}" 
                         alt="Foto Profil" 
                         class="student-avatar mx-auto d-block">
                    @if(auth()->user()->nim === $profile->nim || auth()->user()->nip === $profile->nip)
                        <div class="position-absolute bottom-0 end-0">
                            <button type="button" class="btn btn-light rounded-circle p-2" data-bs-toggle="modal" data-bs-target="#updateFotoModal">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <h2 class="profile-name">{{ $profile->nama }}</h2>
                <p class="profile-nim">{{ $role === 'mahasiswa' ? 'NIM. ' . $profile->nim : 'NIP. ' . $profile->nip }}</p>
            </div>
            
            <div class="profile-details">
                <div class="detail-item">
                    <span class="detail-label">Program Studi</span>
                    <span class="detail-value">{{ $profile->prodi->nama_prodi ?? 'Teknik Informatika' }}</span>
                </div>
                @if($role === 'mahasiswa')
                    <div class="detail-item">
                        <span class="detail-label">Angkatan</span>
                        <span class="detail-value">{{ $profile->angkatan }}</span>
                    </div>
                    @if($profile->konsentrasi)
                    <div class="detail-item">
                        <span class="detail-label">Konsentrasi</span>
                        <span class="detail-value">{{ $profile->konsentrasi->nama_konsentrasi }}</span>
                    </div>
                    @endif
                @endif
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $profile->email }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Foto -->
<div class="modal fade" id="updateFotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="foto" class="form-label">Pilih Foto Baru</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG (Max: 2MB)</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload Foto</button>
                    </div>
                </form>
                @if($profile->foto)
                    <hr>
                    <form action="{{ route('profile.remove') }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">Hapus Foto Profil</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed bottom-0 end-0 m-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@endsection