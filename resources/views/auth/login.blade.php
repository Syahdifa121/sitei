<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Bimbingan dan Perpesanan Teknik Elektro UNRI">
    <meta name="author" content="SITEI JTE UNRI">
    <title>RESERVASI BIMBINGAN AKADEMIK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(-135deg, #A1E3F9 0%, #578FCA 50%, #3674B5 100%);
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%2333CCFF' fill-opacity='0.05' d='M0,96L48,122.7C96,149,192,203,288,208C384,213,480,171,576,138.7C672,107,768,85,864,96C960,107,1056,149,1152,154.7C1248,160,1344,128,1392,112L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E") no-repeat top;
            background-size: cover;
            z-index: 0;
        }

        /* Perbaikan untuk carousel */
        .carousel-section {
            background: rgba(248, 249, 250, 0.9);
            border-radius: 15px;
            margin: 1rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
            height: calc(100% - 2rem);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .carousel {
            width: 100%;
            height: 100%;
        }

        .carousel-inner {
            height: 100%;
        }

        .carousel-item {
            height: 100%;
            padding: 1rem;
        }

        .carousel-item img {
            border-radius: 15px;
            object-fit: contain;
            width: 100%;
            height: 100%;
            max-height: 100%;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 10%;
            opacity: 0.7;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
          
            border-radius: 50%;
            padding: 10px;
        }

        .form-section {
            position: relative;
            z-index: 1;
            padding: 2rem;
        }

        .logo-section {
            position: relative;
            padding-bottom: 2rem;
        }

        .logo-section::after {
            content: '';
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3674B5, transparent);
        }

        .form-floating > .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        /* Override Bootstrap validation icons */
        .form-control.is-valid,
        .was-validated .form-control:valid {
            background-image: none !important;
            padding-right: 0.75rem !important;
            border-color: #198754;
        }

        .form-control.is-invalid,
        .was-validated .form-control:invalid {
            background-image: none !important;
            padding-right: 0.75rem !important;
            border-color: #dc3545;
        }

        /* Custom password field */
        .password-field-wrapper {
            position: relative;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            z-index: 5;
            cursor: pointer;
            padding: 0.25rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #3674B5 0%, #3674B5 100%);
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(18, 1, 82, 0.2);
        }

        .alert {
            border-radius: 12px;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .alert-danger {
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            border-left: 4px solid #00923F;
        }

        .developer-link {
            color: #3674B5;
            transition: all 0.3s ease;
        }

        .developer-link:hover {
            color: #A1E3F9;
            text-decoration: none !important;
        }

        @media (max-width: 992px) {
            .carousel-section {
                margin-bottom: 2rem;
                height: 300px;
            }
        }

        /* Animated background */
        .animated-bg {
            position: fixed;
            width: 100vw;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: -1;
            background: linear-gradient(135deg, #1A3B6E 0%, #578FCA 50%, #A1E3F9 100%);
        }

        .animated-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,96L48,122.7C96,149,192,203,288,208C384,213,480,171,576,138.7C672,107,768,85,864,96C960,107,1056,149,1152,154.7C1248,160,1344,128,1392,112L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E") repeat-y;
            animation: wave 15s linear infinite;
            opacity: 0.1;
        }

        @keyframes wave {
            0% { background-position: 0 0; }
            100% { background-position: 1440px 0; }
        }
    </style>
</head>

<body>
    <div class="animated-bg"></div>
    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="login-container">
                        <div class="row g-0">
                            <div class="col-lg-8">
                                <div class="carousel-section">
                                    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="{{ asset('images/1.png') }}" alt="Slide 1">
                                            </div>
                                            {{-- <div class="carousel-item">
                                                <img src="{{ asset('images/2.png') }}" alt="Slide 2">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="{{ asset('images/3.png') }}" alt="Slide 3">
                                            </div> --}}
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-section">
                                    <div class="logo-section text-center">
                                        <img src="{{ asset('images/logounri.png') }}" alt="logo_unri" width="80" height="80" class="img-fluid mb-3">
                                        <h4 class="fw-bold mb-0">Sistem Elektronik</h4>
                                        <p class="mb-0">Program Studi Teknik Informatika</p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger py-2 mb-3">
                                            <small><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</small>
                                        </div>
                                    @endif

                                    @if (session('status'))
                                        <div class="alert alert-success py-2 mb-3">
                                            <small><i class="fas fa-check-circle me-2"></i>{{ session('status') }}</small>
                                        </div>
                                    @endif

                                    <form class="needs-validation" action="/login" method="POST" novalidate>
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" placeholder="NIP/NIM" value="{{ old('username') }}" required autocomplete="username" autofocus>
                                            <label for="username">NIP/NIM <span class="text-danger">*</span></label>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4 password-field-wrapper">
                                            <div class="form-floating">
                                                <input type="password" 
                                                    class="form-control @error('password') is-invalid @enderror" 
                                                    name="password" 
                                                    id="password" 
                                                    placeholder="Password" 
                                                    required>
                                                <label for="password">Password <span class="text-danger">*</span></label>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="button" id="togglePassword" class="password-toggle-btn">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>

                                        <button type="submit" class="btn btn-success w-100 mb-3">
                                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                        </button>

                                        <div class="text-center mb-4">
                                            <small class="text-muted">
                                                Belum Punya Akun atau Lupa Password?<br>
                                                <span class="fw-semibold">(Hubungi Admin Prodi)</span>
                                            </small>
                                        </div>

                                        <div class="text-center pt-3 border-top">
                                            <small class="text-muted d-block mb-1">Dikembangkan oleh:</small>
                                            <a href="/developer" class="developer-link text-decoration-none fw-semibold">
                                                SYAHIRAH
                                            </a>
                                            <div class="mt-2">
                                                <small class="text-muted">2025 © SEPTI TI UNRI</small>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password Toggle
        document.getElementById("togglePassword").addEventListener("click", function(e) {
            e.preventDefault();
            const passwordInput = document.getElementById("password");
            const icon = this.querySelector('i');
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        });

        // Form Validation
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>