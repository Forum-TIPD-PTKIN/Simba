@php
    $arrContextOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
@endphp

<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('pendaftar.dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/admin/images/logo_app_beasiswa.png') }}" class="img-fluid logo-lg"
                    alt="logo">
                <span class="badge bg-light-success rounded-pill ms-2 theme-version">v1.0.0</span>
            </a>
        </div>
        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0"
                            style="height: 45px;width: 45px;overflow: hidden;border-radius: 50%;">
                            <img style="width: 100%;" src="{{ session('profil')->avatar }}"
                                onerror="this.onerror=null;this.src='https://eu.ui-avatars.com/api/?name={{ urlencode(session('profil')->nama) }}&background=random&size=256'"
                                alt="user-img" class="user-avtar " />
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <small>{{ userAccessName() }}</small>
                        </div>
                        <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse"
                            href="#pc_sidebar_userlink">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sort-outline"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                        <div class="pt-3">
                            <a href="#!">
                                <i class="ti ti-user"></i>
                                <span>My Account</span>
                            </a>
                            <a href="#!">
                                <i class="ti ti-settings"></i>
                                <span>Settings</span>
                            </a>
                            <a href="#!">
                                <i class="ti ti-lock"></i>
                                <span>Lock Screen</span>
                            </a>
                            <a href="{{ route('logout') }}" class="logout">
                                <i class="ti ti-power"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Navigation</label>
                </li>

                <li class="pc-item">
                    <a href="{{ route('pendaftar.dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-status-up"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ route('pendaftar.jadwal-kegiatan') }}" class="pc-link"><span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-calendar-1"></use>
                            </svg> </span><span class="pc-mtext">Jadwal Kegiatan</span>
                    </a>
                </li>

                @if (session()->get('MENDAFTAR'))
                    <li class="pc-item pc-caption">
                        <label>ZONA PENDAFTARAN</label>
                        <svg class="pc-icon">
                            <use xlink:href="#custom-notification-status"></use>
                        </svg>
                    </li>


                    <li class="pc-item">
                        <a href="{{ route('pendaftar.riwayat') }}" class="pc-link">
                            <span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-flag"></use>
                                </svg>
                            </span>
                            <span class="pc-mtext">Riwayat Pendaftaran</span>
                        </a>
                    </li>

                    {{-- <li class="pc-item">
                        <a href="{{ route('pendaftar.pemberkasan') }}" class="pc-link">
                            <span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-upload"></use>
                                </svg>
                            </span>
                            <span class="pc-mtext">Pemberkasan</span>
                        </a>
                    </li> --}}

                    <li class="pc-item">
                        <a href="{{ route('pendaftar.seleksi-administrasi') }}" class="pc-link">
                            <span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-document-filter"></use>
                                </svg>
                            </span>
                            <span class="pc-mtext">Seleksi Administrasi</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
