@extends('layouts.app')

@section('title', 'Ganti Password')

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
            yy
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

        .password-form {
            max-width: 800px;
  
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .password-form h1 {
            text-align: center;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #059669, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
        }

        .password-form .form-group {
            margin-bottom: 20px;
        }

        .password-form label {
            font-weight: 600;
            color: #2c3e50;
        }

        .password-form input {
            display: block;
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            color: #34495e;
        }

        .password-form button {
            display: block;
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(to right, #4ade80, #3b82f6);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .password-form button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
        <h1 class="mb-3 gradient-text fw-bold">Ganti Password</h1>
        <hr>
        <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
            <a href="/profilmahasiswa">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </button>
        <div class="student-profile-container">
            <div class="student-profile-card">
                <div class="profile-header">
                    <img src="{{ asset('images/fotosasa.jpg') }}" alt="Foto Mahasiswa" class="student-avatar mx-auto d-block">
                    <h2 class="profile-name">SYAHIRAH TRI MEILINA</h2>
                    <p class="profile-nim">NIM. 2107110255</p>
                </div>
                <div class="password-form">
                    <h1>Ganti Password</h1>
                    <form>
                        <div class="form-group">
                            <label for="current-password">Password Lama</label>
                            <input type="password" id="current-password" name="current-password" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">Password Baru</label>
                            <input type="password" id="new-password" name="new-password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Ulangi Password Baru</label>
                            <input type="password" id="confirm-password" name="confirm-password" required>
                        </div>
                        <button type="submit">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
