<div class="bg-gradient-bar" style="height: 3px; background: linear-gradient(to right, #4ade80, #3b82f6, #8b5cf6);"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top" style="box-shadow: 0px 0px 10px 1px #afafaf">
    <div class="container">
        @if(Auth::guard('dosen')->check())
            <a class="navbar-brand me-4" href="{{ url('/persetujuan') }}" style="font-family: 'Viga', sans-serif; font-weight: 600; font-size: 25px;">
        @else
            <a class="navbar-brand me-4" href="{{ url('/usulanbimbingan') }}" style="font-family: 'Viga', sans-serif; font-weight: 600; font-size: 25px;">
        @endif
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2c/LOGO-UNRI.png" alt="SITEI Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
            SEPTI
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item d-none">
                    @if(Auth::guard('dosen')->check())
                        <a class="nav-link {{ Request::is('persetujuan') || Request::is('masukkanjadwal') || Request::is('riwayatdosen') || Request::is('editusulan') || Request::is('terimausulanbimbingan') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ url('/persetujuan') }}">RESERVASI</a>
                    @elseif(Auth::guard('mahasiswa')->check())
                        <a class="nav-link {{ Request::is('usulanbimbingan') || Request::is('pilihjadwal') || Request::is('detaildaftar') || Request::is('riwayatmahasiswa') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ url('/usulanbimbingan') }}">RESERVASI</a>
                    @elseif(Auth::guard('admin')->check())
                        {{-- tidak ada menu RESERVASI --}}
                    @endif
                </li>
                {{-- <li class="nav-item">
                    @if(Auth::guard('dosen')->check())
                        <a class="nav-link {{ Request::routeIs('pesan.*') ? 'active' : '' }}" 
                        style="font-weight: bold;" 
                        href="{{ url('/pesan/dashboardkonsultasi') }}">KONSULTASI</a>
                    @else
                        <a class="nav-link {{ Request::routeIs('pesan.*') ? 'active' : '' }}" 
                        style="font-weight: bold;" 
                        href="{{ route('pesan.dashboardkonsultasi') }}">KONSULTASI</a>
                    @endif
                </li> --}}
                <li class="nav-item">
                    @if(Auth::guard('dosen')->check())
                        <a class="nav-link {{ Request::is('dosen/alumni') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ url('/dosen/alumni?tab=statistik-alumni') }}">ALUMNI</a>
                    @elseif(Auth::guard('mahasiswa')->check())
                        <a class="nav-link {{ Request::is('alumni') ? 'active' : '' }}" 
                           style="font-weight: bold;" 
                           href="{{ url('/alumni?tab=form-alumni') }}">ALUMNI</a>
                    @elseif(Auth::guard('admin')->check())
                        <a class="nav-link {{ Request::is('admin/alumni') ? 'active' : '' }}" 
                            style="font-weight: bold;" 
                            href="{{ url('/admin/alumni?tab=statistik-alumni') }}">ALUMNI</a>
                    @endif
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn text-dark dropdown-toggle custom-dropdown-btn" style="font-weight: bold;" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        AKUN
                    </button>
                    <ul class="dropdown-menu custom-dropdown-menu fw-semibold" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item custom-dropdown-item" href="/profil">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="dropdown-item w-100 custom-dropdown-item text-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>